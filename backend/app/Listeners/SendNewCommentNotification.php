<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewCommentPendingNotification;
use Blog\Application\Services\Moderation\ModerationService;
use Blog\Domain\Comment\Events\CommentCreated;
use Blog\Domain\Post\Repositories\PostRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class SendNewCommentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ModerationService $moderationService,
        private readonly PostRepositoryInterface $postRepository
    ) {}

    /**
     * Handle the event.
     */
    public function handle(CommentCreated $event): void
    {
        $comment = $event->comment;

        // Only notify if the service says we should
        if (!$this->moderationService->shouldNotify($comment)) {
            return;
        }

        // Get post title
        $post = $this->postRepository->findById($comment->postId());
        $postTitle = $post ? $post->getTitle() : 'Post #' . $comment->postId()->value();

        // Get author info
        $authorName = $this->getAuthorName($comment);
        $authorEmail = $this->getAuthorEmail($comment);

        // Content preview
        $contentPreview = Str::limit($comment->content()->value(), 200);

        // Get notification recipients
        $recipients = $this->getNotificationRecipients();

        // Send notification to each recipient
        foreach ($recipients as $recipient) {
            $recipient->notify(new NewCommentPendingNotification(
                commentId: $comment->id()->value(),
                postTitle: $postTitle,
                authorName: $authorName,
                authorEmail: $authorEmail,
                contentPreview: $contentPreview
            ));
        }
    }

    /**
     * Get the author name from comment.
     */
    private function getAuthorName($comment): string
    {
        if ($comment->authorType()->isRegistered()) {
            // Get user name from userId
            $user = User::find($comment->userId()?->value());
            return $user?->name ?? 'Usuario Registrado';
        }

        return $comment->anonymousAuthor()->name();
    }

    /**
     * Get the author email from comment.
     */
    private function getAuthorEmail($comment): string
    {
        if ($comment->authorType()->isRegistered()) {
            // Get user email from userId
            $user = User::find($comment->userId()?->value());
            return $user?->email ?? 'N/A';
        }

        return $comment->anonymousAuthor()->email();
    }

    /**
     * Get users who should receive notifications.
     *
     * @return \Illuminate\Support\Collection<User>
     */
    private function getNotificationRecipients()
    {
        // Check if specific emails are configured
        $configuredEmails = $this->moderationService->getNotificationEmails();

        if (!empty($configuredEmails)) {
            return User::whereIn('email', $configuredEmails)->get();
        }

        // Default: notify all admins
        return User::whereIn('role', ['admin', 'super_admin'])->get();
    }
}
