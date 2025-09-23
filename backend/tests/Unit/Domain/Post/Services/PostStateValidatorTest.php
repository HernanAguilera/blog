<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Post\Services;

use Blog\Domain\Post\Entities\Post;
use Blog\Domain\Post\Services\PostStateValidator;
use Blog\Domain\Post\ValueObjects\PostTitle;
use Blog\Domain\Post\ValueObjects\PostContent;
use Blog\Domain\Post\ValueObjects\PostStatus;
use Blog\Domain\Post\Exceptions\InvalidPostStatusException;
use Blog\Domain\User\ValueObjects\UserId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PostStateValidatorTest extends TestCase
{
    private PostStateValidator $validator;
    private Post $post;

    protected function setUp(): void
    {
        $this->validator = new PostStateValidator();
        $this->post = Post::create(
            new PostTitle('Test Post'),
            new PostContent('Test content'),
            new UserId(1)
        );
    }

    /** @test */
    public function it_allows_draft_to_any_status(): void
    {
        // Draft post should be able to transition to any status
        $this->assertTrue($this->validator->canTransitionTo($this->post, PostStatus::scheduled()));
        $this->assertTrue($this->validator->canTransitionTo($this->post, PostStatus::published()));
        $this->assertTrue($this->validator->canTransitionTo($this->post, PostStatus::archived()));
    }

    /** @test */
    public function it_allows_scheduled_to_draft_and_published(): void
    {
        $this->post->schedule(new DateTimeImmutable('+1 hour'));

        $this->assertTrue($this->validator->canTransitionTo($this->post, PostStatus::draft()));
        $this->assertTrue($this->validator->canTransitionTo($this->post, PostStatus::published()));
        $this->assertTrue($this->validator->canTransitionTo($this->post, PostStatus::archived()));
    }

    /** @test */
    public function it_allows_published_to_draft_and_archived(): void
    {
        $this->post->publish();

        $this->assertTrue($this->validator->canTransitionTo($this->post, PostStatus::draft()));
        $this->assertTrue($this->validator->canTransitionTo($this->post, PostStatus::archived()));
        $this->assertFalse($this->validator->canTransitionTo($this->post, PostStatus::scheduled()));
    }

    /** @test */
    public function it_allows_archived_to_any_status_except_archived(): void
    {
        $this->post->archive();

        $this->assertTrue($this->validator->canTransitionTo($this->post, PostStatus::draft()));
        $this->assertTrue($this->validator->canTransitionTo($this->post, PostStatus::published()));
        $this->assertFalse($this->validator->canTransitionTo($this->post, PostStatus::scheduled()));
    }

    /** @test */
    public function it_validates_scheduling_constraints(): void
    {
        // Cannot schedule for past date
        $this->expectException(InvalidPostStatusException::class);
        $this->expectExceptionMessage('Cannot schedule a post for a past date');

        $this->validator->validateScheduling($this->post, new DateTimeImmutable('-1 hour'));
    }

    /** @test */
    public function it_prevents_scheduling_published_posts(): void
    {
        $this->post->publish();

        $this->expectException(InvalidPostStatusException::class);
        $this->expectExceptionMessage('Cannot schedule an already published post');

        $this->validator->validateScheduling($this->post, new DateTimeImmutable('+1 hour'));
    }

    /** @test */
    public function it_prevents_scheduling_too_far_in_future(): void
    {
        $this->expectException(InvalidPostStatusException::class);
        $this->expectExceptionMessage('Cannot schedule a post more than 1 year in the future');

        $this->validator->validateScheduling($this->post, new DateTimeImmutable('+2 years'));
    }

    /** @test */
    public function it_throws_exception_for_invalid_transitions(): void
    {
        $this->post->publish();

        $this->expectException(InvalidPostStatusException::class);
        $this->expectExceptionMessage("Cannot transition post from 'published' to 'scheduled'");

        $this->validator->validateTransition($this->post, PostStatus::scheduled());
    }

    /** @test */
    public function it_returns_available_transitions(): void
    {
        // Draft post should have multiple options
        $transitions = $this->validator->getAvailableTransitions($this->post);
        $this->assertContains(PostStatus::SCHEDULED, $transitions);
        $this->assertContains(PostStatus::PUBLISHED, $transitions);
        $this->assertContains(PostStatus::ARCHIVED, $transitions);
        $this->assertNotContains(PostStatus::DRAFT, $transitions); // Already draft

        // Published post should have limited options
        $this->post->publish();
        $transitions = $this->validator->getAvailableTransitions($this->post);
        $this->assertContains(PostStatus::DRAFT, $transitions);
        $this->assertContains(PostStatus::ARCHIVED, $transitions);
        $this->assertNotContains(PostStatus::SCHEDULED, $transitions);
        $this->assertNotContains(PostStatus::PUBLISHED, $transitions); // Already published
    }
}