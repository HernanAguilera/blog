<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\Entities;

use Blog\Domain\Comment\ValueObjects\CommentId;
use Blog\Domain\Comment\ValueObjects\CommentContent;
use Blog\Domain\Comment\ValueObjects\CommentStatus;
use Blog\Domain\Comment\ValueObjects\CommentAuthorType;
use Blog\Domain\Comment\ValueObjects\AnonymousAuthor;
use Blog\Domain\Post\ValueObjects\PostId;
use Blog\Domain\User\ValueObjects\UserId;
use DateTimeImmutable;

final class Comment
{
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt = null;
    private ?DateTimeImmutable $approvedAt = null;
    private ?UserId $approvedBy = null;

    public function __construct(
        private ?CommentId $id,
        private PostId $postId,
        private CommentAuthorType $authorType,
        private CommentContent $content,
        private CommentStatus $status,
        private ?UserId $userId,
        private ?AnonymousAuthor $anonymousAuthor,
        private ?CommentId $parentId,
        private string $ipAddress,
        private string $userAgent,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->validateAuthor();
    }

    private function validateAuthor(): void
    {
        // Usuario registrado debe tener userId y NO anonymousAuthor
        if ($this->authorType->isRegistered()) {
            if ($this->userId === null) {
                throw new \InvalidArgumentException('Registered comment must have a userId');
            }
            if ($this->anonymousAuthor !== null) {
                throw new \InvalidArgumentException('Registered comment cannot have anonymousAuthor');
            }
        }

        // Usuario anónimo debe tener anonymousAuthor y NO userId
        if ($this->authorType->isAnonymous()) {
            if ($this->anonymousAuthor === null) {
                throw new \InvalidArgumentException('Anonymous comment must have anonymousAuthor data');
            }
            if ($this->userId !== null) {
                throw new \InvalidArgumentException('Anonymous comment cannot have userId');
            }
        }
    }

    public static function createFromUser(
        PostId $postId,
        UserId $userId,
        CommentContent $content,
        ?CommentId $parentId,
        string $ipAddress,
        string $userAgent
    ): self {
        return new self(
            id: CommentId::generate(),
            postId: $postId,
            authorType: CommentAuthorType::registered(),
            content: $content,
            status: CommentStatus::approved(), // Auto-aprobar usuarios registrados
            userId: $userId,
            anonymousAuthor: null,
            parentId: $parentId,
            ipAddress: $ipAddress,
            userAgent: $userAgent
        );
    }

    public static function createAnonymous(
        PostId $postId,
        AnonymousAuthor $anonymousAuthor,
        CommentContent $content,
        ?CommentId $parentId,
        string $ipAddress,
        string $userAgent
    ): self {
        return new self(
            id: CommentId::generate(),
            postId: $postId,
            authorType: CommentAuthorType::anonymous(),
            content: $content,
            status: CommentStatus::pendingApproval(), // Pendiente para anónimos
            userId: null,
            anonymousAuthor: $anonymousAuthor,
            parentId: $parentId,
            ipAddress: $ipAddress,
            userAgent: $userAgent
        );
    }

    public function approve(UserId $moderatorId): void
    {
        if ($this->status->isApproved()) {
            throw new \DomainException('Comment is already approved');
        }

        if ($this->status->isRejected() || $this->status->isSpam()) {
            throw new \DomainException('Cannot approve a rejected or spam comment');
        }

        $this->status = CommentStatus::approved();
        $this->approvedBy = $moderatorId;
        $this->approvedAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function reject(UserId $moderatorId): void
    {
        if ($this->status->isRejected()) {
            throw new \DomainException('Comment is already rejected');
        }

        $this->status = CommentStatus::rejected();
        $this->approvedBy = $moderatorId; // Guardamos quién tomó la decisión
        $this->updatedAt = new DateTimeImmutable();
    }

    public function markAsSpam(): void
    {
        if ($this->status->isSpam()) {
            throw new \DomainException('Comment is already marked as spam');
        }

        $this->status = CommentStatus::spam();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function isReply(): bool
    {
        return $this->parentId !== null;
    }

    // Getters
    public function id(): ?CommentId
    {
        return $this->id;
    }

    public function postId(): PostId
    {
        return $this->postId;
    }

    public function authorType(): CommentAuthorType
    {
        return $this->authorType;
    }

    public function content(): CommentContent
    {
        return $this->content;
    }

    public function status(): CommentStatus
    {
        return $this->status;
    }

    public function userId(): ?UserId
    {
        return $this->userId;
    }

    public function anonymousAuthor(): ?AnonymousAuthor
    {
        return $this->anonymousAuthor;
    }

    public function parentId(): ?CommentId
    {
        return $this->parentId;
    }

    public function ipAddress(): string
    {
        return $this->ipAddress;
    }

    public function userAgent(): string
    {
        return $this->userAgent;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function approvedAt(): ?DateTimeImmutable
    {
        return $this->approvedAt;
    }

    public function approvedBy(): ?UserId
    {
        return $this->approvedBy;
    }
}
