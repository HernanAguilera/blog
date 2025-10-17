<?php

declare(strict_types=1);

namespace Blog\Application\UseCases\Comment;

use Blog\Application\DTOs\Comment\CreateCommentDTO;
use Blog\Application\Services\Security\CommentSecurityLogger;
use Blog\Domain\Comment\Entities\Comment;
use Blog\Domain\Comment\Repositories\CommentRepositoryInterface;
use Blog\Domain\Comment\Services\CommentDomainService;
use Blog\Domain\Comment\ValueObjects\CommentContent;
use Blog\Domain\Comment\ValueObjects\CommentId;
use Blog\Domain\Comment\Events\CommentCreated;
use Blog\Domain\Comment\Events\CommentReplyCreated;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\User\ValueObjects\UserId;
use Blog\Domain\Shared\Events\EventDispatcherInterface;

final readonly class CreateCommentFromUserUseCase
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private CommentDomainService $domainService,
        private EventDispatcherInterface $eventDispatcher,
        private CommentSecurityLogger $securityLogger
    ) {}

    public function execute(CreateCommentDTO $dto): Comment
    {
        // Sanitize content
        $sanitizedContent = $this->domainService->sanitizeContent($dto->content);
        $content = new CommentContent($sanitizedContent);

        // Validate content quality
        $qualityErrors = $this->domainService->validateContentQuality($content);
        if (!empty($qualityErrors)) {
            // Log suspicious content from registered user
            $this->securityLogger->logSuspiciousContent(
                ipAddress: $dto->ipAddress,
                reason: implode(', ', $qualityErrors),
                content: $sanitizedContent,
                userId: $dto->userId,
                email: null
            );
            throw new \DomainException(implode(', ', $qualityErrors));
        }

        // Check for spam (unusual for registered users, high severity)
        if ($this->domainService->isSpamContent($sanitizedContent)) {
            // Log spam attempt from registered user (more suspicious)
            $this->securityLogger->logSpamAttempt(
                ipAddress: $dto->ipAddress,
                content: $sanitizedContent,
                userId: $dto->userId,
                email: null
            );
            throw new \DomainException('Comment detected as spam');
        }

        // Create comment (auto-approved for registered users)
        $comment = Comment::createFromUser(
            postId: new PostId($dto->postId),
            userId: new UserId($dto->userId),
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
