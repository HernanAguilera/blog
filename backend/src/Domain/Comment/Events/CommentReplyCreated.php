<?php

declare(strict_types=1);

namespace Blog\Domain\Comment\Events;

use Blog\Domain\Events\DomainEvent;
use Blog\Domain\Comment\Entities\Comment;

final class CommentReplyCreated extends DomainEvent
{
    public function __construct(
        private readonly Comment $reply
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'comment.reply_created';
    }

    public function getReply(): Comment
    {
        return $this->reply;
    }

    public function toPrimitives(): array
    {
        $data = [
            'comment_id' => $this->reply->id()?->value(),
            'post_id' => $this->reply->postId()->value(),
            'parent_id' => $this->reply->parentId()?->value(),
            'author_type' => $this->reply->authorType()->value(),
            'status' => $this->reply->status()->value(),
            'created_at' => $this->reply->createdAt()->format('Y-m-d H:i:s'),
        ];

        // Add author info based on type
        if ($this->reply->authorType()->isRegistered()) {
            $data['user_id'] = $this->reply->userId()?->value();
        } else {
            $data['anonymous_author'] = $this->reply->anonymousAuthor()?->toArray();
        }

        return $data;
    }
}
