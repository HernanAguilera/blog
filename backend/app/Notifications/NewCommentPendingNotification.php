<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentPendingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly string $commentId,
        private readonly string $postTitle,
        private readonly string $authorName,
        private readonly string $authorEmail,
        private readonly string $contentPreview
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $moderationUrl = config('app.url') . '/admin/comments/pending';

        return (new MailMessage)
            ->subject('Nuevo comentario pendiente de moderación')
            ->greeting('Hola!')
            ->line("Hay un nuevo comentario pendiente de moderación en el post:")
            ->line("**Post:** {$this->postTitle}")
            ->line("**Autor:** {$this->authorName} ({$this->authorEmail})")
            ->line("**Comentario:**")
            ->line($this->contentPreview)
            ->action('Moderar Comentario', $moderationUrl)
            ->line('Por favor, revisa y modera este comentario lo antes posible.')
            ->salutation('Saludos, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->commentId,
            'post_title' => $this->postTitle,
            'author_name' => $this->authorName,
            'author_email' => $this->authorEmail,
            'content_preview' => $this->contentPreview,
        ];
    }
}
