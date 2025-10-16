<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Comment;

use Blog\Application\DTOs\Comment\CreateAnonymousCommentDTO;
use Blog\Domain\Comment\Entities\Comment;
use Blog\Domain\Comment\Repositories\CommentRepositoryInterface;
use Blog\Domain\Comment\Services\CommentDomainService;
use Blog\Domain\Comment\ValueObjects\CommentContent;
use Blog\Domain\Comment\ValueObjects\CommentId;
use Blog\Domain\Comment\ValueObjects\AnonymousAuthor;
use Blog\Domain\Comment\Events\CommentCreated;
use Blog\Domain\Comment\Events\CommentReplyCreated;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\Shared\Events\EventDispatcherInterface;

final readonly class CreateAnonymousCommentUseCase
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private CommentDomainService $domainService,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function execute(CreateAnonymousCommentDTO $dto): Comment
    {
        // TODO: Validate Cloudflare Turnstile token
        // This will be implemented in the controller layer

        // Sanitize content
        $sanitizedContent = $this->domainService->sanitizeContent($dto->content);
        $content = new CommentContent($sanitizedContent);

        // Validate content quality
        $qualityErrors = $this->domainService->validateContentQuality($content);
        if (!empty($qualityErrors)) {
            throw new \DomainException(implode(', ', $qualityErrors));
        }

        // Check for spam
        if ($this->domainService->isSpamContent($sanitizedContent)) {
            throw new \DomainException('Comment detected as spam');
        }

        // Create anonymous author
        $anonymousAuthor = new AnonymousAuthor(
            name: $dto->authorName,
            email: $dto->authorEmail,
            website: $dto->authorWebsite
        );

        // Create comment (pending approval for anonymous users)
        $comment = Comment::createAnonymous(
            postId: new PostId($dto->postId),
            anonymousAuthor: $anonymousAuthor,
            content: $content,
            parentId: $dto->parentId ? CommentId::fromString($dto->parentId) : null,
            ipAddress: $dto->ipAddress,
            userAgent: $dto->userAgent
        );

        // Save comment
        $this->commentRepository->save($comment);

        // Dispatch events
        $this->eventDispatcher->dispatch(new CommentCreated($comment));

        if ($comment->isReply()) {
            $this->eventDispatcher->dispatch(new CommentReplyCreated($comment));
        }

        return $comment;
    }
}
