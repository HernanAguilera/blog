<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases\Auth;

use App\src\Application\DTOs\Auth\AuthenticationResultDTO;
use App\src\Application\DTOs\Auth\SocialLoginDTO;
use App\src\Application\Services\Auth\JwtServiceInterface;
use App\src\Application\Services\Auth\SocialAuthServiceInterface;
use App\src\Application\UseCases\Auth\SocialLoginUseCase;
use App\src\Domain\User\Entities\User;
use App\src\Domain\User\Exceptions\AuthenticationException;
use App\src\Domain\User\Repositories\UserRepositoryInterface;
use App\src\Domain\User\ValueObjects\Email;
use App\src\Domain\User\ValueObjects\SocialProvider;
use App\src\Domain\User\ValueObjects\SocialUserData;
use App\src\Domain\User\ValueObjects\UserRole;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SocialLoginUseCaseTest extends TestCase
{
    private SocialLoginUseCase $useCase;
    private SocialAuthServiceInterface|MockObject $socialAuthService;
    private UserRepositoryInterface|MockObject $userRepository;
    private JwtServiceInterface|MockObject $jwtService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->socialAuthService = $this->createMock(SocialAuthServiceInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->jwtService = $this->createMock(JwtServiceInterface::class);

        $this->useCase = new SocialLoginUseCase(
            $this->socialAuthService,
            $this->userRepository,
            $this->jwtService
        );
    }

    public function test_creates_new_user_when_none_exists(): void
    {
        // Arrange
        $socialUserData = new SocialUserData(
            socialId: '12345',
            provider: SocialProvider::GOOGLE,
            email: 'test@example.com',
            name: 'Test User'
        );

        $dto = SocialLoginDTO::fromProvider('google');

        $this->socialAuthService
            ->expects($this->once())
            ->method('handleProviderCallback')
            ->with($dto->provider)
            ->willReturn($socialUserData);

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo(new Email('test@example.com')))
            ->willReturn(null);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $this->jwtService
            ->expects($this->once())
            ->method('generateToken')
            ->willReturn('jwt_token');

        $this->jwtService
            ->expects($this->once())
            ->method('getTokenExpiration')
            ->willReturn(new DateTimeImmutable('+1 hour'));

        // Act
        $result = $this->useCase->execute($dto);

        // Assert
        $this->assertInstanceOf(AuthenticationResultDTO::class, $result);
        $this->assertEquals('jwt_token', $result->token);
    }

    public function test_links_social_account_to_existing_user(): void
    {
        // Arrange
        $socialUserData = new SocialUserData(
            socialId: '12345',
            provider: SocialProvider::GOOGLE,
            email: 'existing@example.com',
            name: 'Existing User'
        );

        $existingUser = User::create(
            name: 'Existing User',
            email: new Email('existing@example.com'),
            password: \App\src\Domain\User\ValueObjects\Password::fromPlainText('password'),
            role: UserRole::COLLABORATOR
        );

        $dto = SocialLoginDTO::fromProvider('google');

        $this->socialAuthService
            ->expects($this->once())
            ->method('handleProviderCallback')
            ->willReturn($socialUserData);

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($existingUser);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $this->jwtService
            ->expects($this->once())
            ->method('generateToken')
            ->willReturn('jwt_token');

        $this->jwtService
            ->expects($this->once())
            ->method('getTokenExpiration')
            ->willReturn(new DateTimeImmutable('+1 hour'));

        // Act
        $result = $this->useCase->execute($dto);

        // Assert
        $this->assertInstanceOf(AuthenticationResultDTO::class, $result);
        $this->assertEquals('jwt_token', $result->token);
    }

    public function test_throws_exception_when_user_is_inactive(): void
    {
        // Arrange
        $socialUserData = new SocialUserData(
            socialId: '12345',
            provider: SocialProvider::GOOGLE,
            email: 'inactive@example.com',
            name: 'Inactive User'
        );

        $inactiveUser = User::create(
            name: 'Inactive User',
            email: new Email('inactive@example.com'),
            password: \App\src\Domain\User\ValueObjects\Password::fromPlainText('password')
        );
        $inactiveUser->deactivate();

        $dto = SocialLoginDTO::fromProvider('google');

        $this->socialAuthService
            ->expects($this->once())
            ->method('handleProviderCallback')
            ->willReturn($socialUserData);

        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($inactiveUser);

        // Expect exception
        $this->expectException(AuthenticationException::class);

        // Act
        $this->useCase->execute($dto);
    }

    public function test_get_redirect_url_delegates_to_service(): void
    {
        // Arrange
        $dto = SocialLoginDTO::fromProvider('google');
        $expectedUrl = 'https://accounts.google.com/oauth/authorize?...';

        $this->socialAuthService
            ->expects($this->once())
            ->method('redirectToProvider')
            ->with($dto->provider)
            ->willReturn($expectedUrl);

        // Act
        $result = $this->useCase->getRedirectUrl($dto);

        // Assert
        $this->assertEquals($expectedUrl, $result);
    }
}