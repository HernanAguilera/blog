<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\Events;

use Blog\Domain\Events\DomainEvent;
use Blog\Domain\Comment\Entities\Comment;

final class CommentCreated extends DomainEvent
{
    public function __construct(
        private readonly Comment $comment
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'comment.created';
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function toPrimitives(): array
    {
        $data = [
            'comment_id' => $this->comment->id()?->value(),
            'post_id' => $this->comment->postId()->value(),
            'author_type' => $this->comment->authorType()->value(),
            'status' => $this->comment->status()->value(),
            'parent_id' => $this->comment->parentId()?->value(),
            'is_reply' => $this->comment->isReply(),
            'created_at' => $this->comment->createdAt()->format('Y-m-d H:i:s'),
        ];

        // Add author info based on type
        if ($this->comment->authorType()->isRegistered()) {
            $data['user_id'] = $this->comment->userId()?->value();
        } else {
            $data['anonymous_author'] = $this->comment->anonymousAuthor()?->toArray();
        }

        return $data;
    }
}
