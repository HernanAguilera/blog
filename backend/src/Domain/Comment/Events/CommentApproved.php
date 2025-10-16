<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\Events;

use Blog\Domain\Events\DomainEvent;
use Blog\Domain\Comment\Entities\Comment;

final class CommentApproved extends DomainEvent
{
    public function __construct(
        private readonly Comment $comment
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'comment.approved';
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function toPrimitives(): array
    {
        return [
            'comment_id' => $this->comment->id()?->value(),
            'post_id' => $this->comment->postId()->value(),
            'approved_by' => $this->comment->approvedBy()?->value(),
            'approved_at' => $this->comment->approvedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
