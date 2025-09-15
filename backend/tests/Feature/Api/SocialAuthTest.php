<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User as LaravelUser;
use App\src\Application\Services\Auth\SocialAuthServiceInterface;
use App\src\Domain\User\ValueObjects\SocialProvider;
use App\src\Domain\User\ValueObjects\SocialUserData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_providers_endpoint_returns_enabled_providers(): void
    {
        // Act
        $response = $this->getJson('/api/auth/social/providers');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'providers' => [
                    '*' => [
                        'name',
                        'display_name',
                        'enabled'
                    ]
                ],
                'total'
            ]
        ]);
    }

    public function test_redirect_endpoint_returns_redirect_url(): void
    {
        // Mock the social auth service
        $mockService = $this->createMock(SocialAuthServiceInterface::class);
        $mockService->expects($this->once())
            ->method('redirectToProvider')
            ->willReturn('https://accounts.google.com/oauth/authorize?...');

        $this->app->instance(SocialAuthServiceInterface::class, $mockService);

        // Act
        $response = $this->getJson('/api/auth/social/google');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'redirect_url',
                'provider'
            ]
        ]);
        $response->assertJson([
            'success' => true,
            'data' => [
                'provider' => 'google'
            ]
        ]);
    }

    public function test_redirect_endpoint_fails_with_invalid_provider(): void
    {
        // Act
        $response = $this->getJson('/api/auth/social/invalid');

        // Assert
        $response->assertStatus(404); // Laravel returns 404 for invalid route parameter
    }

    public function test_callback_endpoint_creates_new_user(): void
    {
        // Mock social user data
        $socialUserData = new SocialUserData(
            socialId: '12345',
            provider: SocialProvider::GOOGLE,
            email: 'newuser@example.com',
            name: 'New User'
        );

        // Mock the social auth service
        $mockService = $this->createMock(SocialAuthServiceInterface::class);
        $mockService->expects($this->once())
            ->method('handleProviderCallback')
            ->willReturn($socialUserData);

        $this->app->instance(SocialAuthServiceInterface::class, $mockService);

        // Act
        $response = $this->getJson('/api/auth/social/google/callback');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
                'expires_at',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'is_active',
                    'social_provider',
                    'email_verified_at'
                ]
            ]
        ]);

        $response->assertJson([
            'success' => true,
            'data' => [
                'user' => [
                    'name' => 'New User',
                    'email' => 'newuser@example.com',
                    'role' => 'guest',
                    'social_provider' => 'google'
                ]
            ]
        ]);

        // Verify user was created in database
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'social_id' => '12345',
            'social_provider' => 'google'
        ]);
    }

    public function test_callback_endpoint_links_to_existing_user(): void
    {
        // Create existing user
        $existingUser = LaravelUser::factory()->create([
            'email' => 'existing@example.com',
            'role' => 'collaborator'
        ]);

        // Mock social user data with same email
        $socialUserData = new SocialUserData(
            socialId: '67890',
            provider: SocialProvider::GOOGLE,
            email: 'existing@example.com',
            name: 'Existing User'
        );

        // Mock the social auth service
        $mockService = $this->createMock(SocialAuthServiceInterface::class);
        $mockService->expects($this->once())
            ->method('handleProviderCallback')
            ->willReturn($socialUserData);

        $this->app->instance(SocialAuthServiceInterface::class, $mockService);

        // Act
        $response = $this->getJson('/api/auth/social/google/callback');

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'user' => [
                    'email' => 'existing@example.com',
                    'role' => 'collaborator', // Keeps existing role
                    'social_provider' => 'google'
                ]
            ]
        ]);

        // Verify social fields were added to existing user
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'email' => 'existing@example.com',
            'social_id' => '67890',
            'social_provider' => 'google',
            'role' => 'collaborator'
        ]);
    }

    public function test_callback_endpoint_handles_oauth_error(): void
    {
        // Act - Simulate OAuth error
        $response = $this->getJson('/api/auth/social/google/callback?error=access_denied&error_description=User+denied+access');

        // Assert
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Social authentication failed',
            'errors' => [
                'oauth' => ['User denied access']
            ]
        ]);
    }

    public function test_endpoints_are_rate_limited(): void
    {
        // Mock the social auth service to prevent actual OAuth calls
        $mockService = $this->createMock(SocialAuthServiceInterface::class);
        $mockService->method('redirectToProvider')
            ->willReturn('https://example.com/oauth');

        $this->app->instance(SocialAuthServiceInterface::class, $mockService);

        // Make multiple requests to test rate limiting
        for ($i = 0; $i < 35; $i++) { // More than the limit of 30 for development
            $response = $this->getJson('/api/auth/social/google');
        }

        // Last request should be rate limited
        $response->assertStatus(429);
        $response->assertJson([
            'success' => false,
            'message' => 'Too many social authentication attempts. Please try again later.'
        ]);
    }
}
