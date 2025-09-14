## TECNOLOGÍAS ESPECÍFICAS Y ARQUITECTURA TÉCNICA

### **STACK TECNOLÓGICO DEFINITIVO**

#### **Backend**

- **Framework**: Laravel 11.x (PHP 8.3+)
- **Base de Datos**: PostgreSQL 15+ (primary), Redis 7+ (cache/sessions)
- **Queue System**: Laravel Horizon + Redis
- **File Storage**: AWS S3 + CloudFront CDN
- **Authentication**: Laravel Sanctum + JWT
- **Testing**: PHPUnit + Pest
- **Documentation**: OpenAPI 3.0 (Swagger)

#### **Frontend**

- **Framework**: Nuxt 3.8+ (Vue 3 + TypeScript)
- **State Management**: Pinia
- **Styling**: Tailwind CSS 3.4+
- **Editor**: Quill.js + Highlight.js
- **HTTP Client**: Ofetch (nativo Nuxt)
- **Testing**: Vitest + Cypress
- **Build Tool**: Vite

#### **Infraestructura**

- **Containerization**: Docker + Docker Compose
- **Web Server**: Nginx (reverse proxy + static files)
- **Process Manager**: PM2 (para Nuxt en producción)
- **Monitoring**: Laravel Telescope (dev) + Custom metrics
- **Security**: Cloudflare (WAF + DDoS protection)

---

## ARQUITECTURA TÉCNICA DETALLADA

### **BACKEND - ESTRUCTURA DE ARCHIVOS CLEAN ARCHITECTURE**

```
blog-backend/
├── src/
│   ├── Domain/                 # Capa de Dominio
│   │   ├── Entities/
│   │   │   ├── User.php
│   │   │   ├── Post.php
│   │   │   ├── Comment.php
│   │   │   └── NewsletterSubscriber.php
│   │   ├── ValueObjects/
│   │   │   ├── Email.php
│   │   │   ├── Password.php
│   │   │   ├── PostSlug.php
│   │   │   ├── PostStatus.php
│   │   │   └── UserId.php
│   │   ├── Services/
│   │   │   ├── PostDomainService.php
│   │   │   ├── UserDomainService.php
│   │   │   └── CommentDomainService.php
│   │   ├── Repositories/
│   │   │   ├── UserRepositoryInterface.php
│   │   │   ├── PostRepositoryInterface.php
│   │   │   ├── CommentRepositoryInterface.php
│   │   │   └── NewsletterRepositoryInterface.php
│   │   ├── Events/
│   │   │   ├── PostPublished.php
│   │   │   ├── UserRegistered.php
│   │   │   └── CommentCreated.php
│   │   └── Exceptions/
│   │       ├── InvalidEmailException.php
│   │       ├── PostNotFoundException.php
│   │       └── InsufficientPermissionsException.php
│   ├── Application/            # Capa de Aplicación
│   │   ├── UseCases/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginUserUseCase.php
│   │   │   │   ├── RegisterUserUseCase.php
│   │   │   │   └── LogoutUserUseCase.php
│   │   │   ├── Post/
│   │   │   │   ├── CreatePostUseCase.php
│   │   │   │   ├── UpdatePostUseCase.php
│   │   │   │   ├── PublishPostUseCase.php
│   │   │   │   └── DeletePostUseCase.php
│   │   │   ├── Comment/
│   │   │   │   ├── CreateCommentUseCase.php
│   │   │   │   ├── ApproveCommentUseCase.php
│   │   │   │   └── RejectCommentUseCase.php
│   │   │   └── Newsletter/
│   │   │       ├── SubscribeNewsletterUseCase.php
│   │   │       └── SendNewsletterUseCase.php
│   │   ├── Commands/
│   │   │   ├── CreatePostCommand.php
│   │   │   ├── LoginCommand.php
│   │   │   └── CreateCommentCommand.php
│   │   ├── Queries/
│   │   │   ├── GetPostQuery.php
│   │   │   ├── GetPostsQuery.php
│   │   │   └── GetCommentsQuery.php
│   │   ├── Handlers/
│   │   │   ├── CreatePostHandler.php
│   │   │   ├── GetPostHandler.php
│   │   │   └── GetPostsHandler.php
│   │   ├── Services/
│   │   │   ├── AuthenticationService.php
│   │   │   ├── PostService.php
│   │   │   └── NotificationService.php
│   │   └── Interfaces/
│   │       ├── TokenServiceInterface.php
│   │       ├── EmailServiceInterface.php
│   │       ├── FileStorageInterface.php
│   │       └── CacheServiceInterface.php
│   ├── Infrastructure/         # Capa de Infraestructura
│   │   ├── Persistence/
│   │   │   ├── Eloquent/
│   │   │   │   ├── Models/
│   │   │   │   │   ├── UserEloquentModel.php
│   │   │   │   │   ├── PostEloquentModel.php
│   │   │   │   │   └── CommentEloquentModel.php
│   │   │   │   ├── Repositories/
│   │   │   │   │   ├── EloquentUserRepository.php
│   │   │   │   │   ├── EloquentPostRepository.php
│   │   │   │   │   └── EloquentCommentRepository.php
│   │   │   │   └── Mappers/
│   │   │   │       ├── UserMapper.php
│   │   │   │       ├── PostMapper.php
│   │   │   │       └── CommentMapper.php
│   │   ├── Services/
│   │   │   ├── JWTTokenService.php
│   │   │   ├── MailgunEmailService.php
│   │   │   ├── S3FileStorageService.php
│   │   │   ├── RedisCache Service.php
│   │   │   └── TurnstileService.php
│   │   ├── External/
│   │   │   ├── AWS/
│   │   │   │   ├── S3Client.php
│   │   │   │   └── S3StorageAdapter.php
│   │   │   ├── Cloudflare/
│   │   │   │   └── TurnstileClient.php
│   │   │   ├── Social/
│   │   │   │   ├── GoogleOAuthAdapter.php
│   │   │   │   ├── FacebookOAuthAdapter.php
│   │   │   │   └── TwitterOAuthAdapter.php
│   │   │   └── Email/
│   │   │       ├── MailgunAdapter.php
│   │   │       └── SendGridAdapter.php
│   │   ├── Queue/
│   │   │   ├── Jobs/
│   │   │   │   ├── SendWelcomeEmailJob.php
│   │   │   │   ├── ProcessImageJob.php
│   │   │   │   ├── SendNewsletterJob.php
│   │   │   │   └── PublishScheduledPostJob.php
│   │   │   └── Handlers/
│   │   │       ├── PostPublishedHandler.php
│   │   │       └── UserRegisteredHandler.php
│   │   └── Providers/
│   │       ├── RepositoryServiceProvider.php
│   │       ├── UseCaseServiceProvider.php
│   │       └── ExternalServiceProvider.php
│   └── Interface/              # Capa de Interfaz
│       ├── Http/
│       │   ├── Controllers/
│       │   │   ├── API/
│       │   │   │   ├── AuthController.php
│       │   │   │   ├── PostController.php
│       │   │   │   ├── CommentController.php
│       │   │   │   └── NewsletterController.php
│       │   │   └── Admin/
│       │   │       ├── AdminPostController.php
│       │   │       ├── AdminCommentController.php
│       │   │       └── AdminDashboardController.php
│       │   ├── Requests/
│       │   │   ├── CreatePostRequest.php
│       │   │   ├── UpdatePostRequest.php
│       │   │   ├── CreateCommentRequest.php
│       │   │   └── LoginRequest.php
│       │   ├── Resources/
│       │   │   ├── PostResource.php
│       │   │   ├── PostCollection.php
│       │   │   ├── CommentResource.php
│       │   │   └── UserResource.php
│       │   ├── Middleware/
│       │   │   ├── AuthenticateMiddleware.php
│       │   │   ├── RoleMiddleware.php
│       │   │   ├── RateLimitMiddleware.php
│       │   │   └── CorsMiddleware.php
│       │   └── Exceptions/
│       │       └── ApiExceptionHandler.php
│       ├── Console/
│       │   ├── Commands/
│       │   │   ├── CreateSuperAdminCommand.php
│       │   │   ├── PublishScheduledPostsCommand.php
│       │   │   └── CleanupUnusedMediaCommand.php
│       │   └── Kernel.php
│       └── GraphQL/            # Para futuras expansiones
│           ├── Mutations/
│           ├── Queries/
│           └── Types/
├── app/                        # Laravel framework files
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── public/
├── resources/
├── routes/
│   ├── api.php
│   ├── web.php
│   └── admin.php
├── storage/
├── tests/
│   ├── Unit/
│   │   ├── Domain/
│   │   ├── Application/
│   │   └── Infrastructure/
│   ├── Feature/
│   └── Integration/
├── docker-compose.yml
├── Dockerfile
├── .env.example
└── composer.json
```

### **FRONTEND - ESTRUCTURA CLEAN ARCHITECTURE**

```
blog-frontend/
├── domain/                     # Capa de Dominio
│   ├── entities/
│   │   ├── User.ts
│   │   ├── Post.ts
│   │   ├── Comment.ts
│   │   └── Newsletter.ts
│   ├── value-objects/
│   │   ├── UserId.ts
│   │   ├── Email.ts
│   │   ├── PostSlug.ts
│   │   └── PostStatus.ts
│   ├── repositories/
│   │   ├── UserRepositoryInterface.ts
│   │   ├── PostRepositoryInterface.ts
│   │   ├── CommentRepositoryInterface.ts
│   │   └── NewsletterRepositoryInterface.ts
│   ├── services/
│   │   ├── PostDomainService.ts
│   │   └── ValidationService.ts
│   └── exceptions/
│       ├── DomainException.ts
│       ├── ValidationException.ts
│       └── NotFoundException.ts
├── application/                # Capa de Aplicación
│   ├── use-cases/
│   │   ├── auth/
│   │   │   ├── LoginUseCase.ts
│   │   │   ├── LogoutUseCase.ts
│   │   │   └── RegisterUseCase.ts
│   │   ├── posts/
│   │   │   ├── GetPostUseCase.ts
│   │   │   ├── GetPostsUseCase.ts
│   │   │   ├── CreatePostUseCase.ts
│   │   │   └── UpdatePostUseCase.ts
│   │   ├── comments/
│   │   │   ├── GetCommentsUseCase.ts
│   │   │   └── CreateCommentUseCase.ts
│   │   └── newsletter/
│   │       └── SubscribeNewsletterUseCase.ts
│   ├── commands/
│   │   ├── CreatePostCommand.ts
│   │   ├── LoginCommand.ts
│   │   └── CreateCommentCommand.ts
│   ├── queries/
│   │   ├── GetPostQuery.ts
│   │   ├── GetPostsQuery.ts
│   │   └── GetCommentsQuery.ts
│   └── services/
│       ├── AuthService.ts
│       ├── PostService.ts
│       └── NotificationService.ts
├── infrastructure/             # Capa de Infraestructura
│   ├── api/
│   │   ├── HttpClient.ts
│   │   ├── ApiResponse.ts
│   │   └── ApiError.ts
│   ├── repositories/
│   │   ├── HttpUserRepository.ts
│   │   ├── HttpPostRepository.ts
│   │   ├── HttpCommentRepository.ts
│   │   └── HttpNewsletterRepository.ts
│   ├── storage/
│   │   ├── LocalTokenStorage.ts
│   │   ├── SessionStorage.ts
│   │   └── CacheStorage.ts
│   ├── external/
│   │   ├── GoogleAnalytics.ts
│   │   ├── FacebookPixel.ts
│   │   └── TwitterWidget.ts
│   └── services/
│       ├── HttpClientService.ts
│       ├── StorageService.ts
│       └── CacheService.ts
├── interface/                  # Capa de Interfaz
│   ├── components/
│   │   ├── common/
│   │   │   ├── AppHeader.vue
│   │   │   ├── AppFooter.vue
│   │   │   ├── LoadingSpinner.vue
│   │   │   └── ErrorMessage.vue
│   │   ├── auth/
│   │   │   ├── LoginForm.vue
│   │   │   ├── RegisterForm.vue
│   │   │   └── UserProfile.vue
│   │   ├── posts/
│   │   │   ├── PostCard.vue
│   │   │   ├── PostDetail.vue
│   │   │   ├── PostEditor.vue
│   │   │   └── PostsList.vue
│   │   ├── comments/
│   │   │   ├── CommentForm.vue
│   │   │   ├── CommentItem.vue
│   │   │   └── CommentsList.vue
│   │   ├── newsletter/
│   │   │   ├── NewsletterForm.vue
│   │   │   └── NewsletterBanner.vue
│   │   └── admin/
│   │       ├── AdminDashboard.vue
│   │       ├── AdminPostsList.vue
│   │       ├── AdminCommentsList.vue
│   │       └── AdminSettings.vue
│   ├── pages/
│   │   ├── index.vue
│   │   ├── about.vue
│   │   ├── contact.vue
│   │   ├── login.vue
│   │   ├── register.vue
│   │   ├── admin/
│   │   │   ├── index.vue
│   │   │   ├── posts/
│   │   │   │   ├── index.vue
│   │   │   │   ├── create.vue
│   │   │   │   └── [id]/edit.vue
│   │   │   └── comments/
│   │   │       └── index.vue
│   │   └── [...slug].vue          # Dynamic post pages
│   ├── layouts/
│   │   ├── default.vue
│   │   ├── admin.vue
│   │   └── blog.vue
│   ├── stores/
│   │   ├── auth.ts
│   │   ├── posts.ts
│   │   ├── comments.ts
│   │   ├── newsletter.ts
│   │   └── ui.ts
│   ├── composables/
│   │   ├── useAuth.ts
│   │   ├── usePosts.ts
│   │   ├── useComments.ts
│   │   ├── useNewsletter.ts
│   │   ├── useApi.ts
│   │   └── useTheme.ts
│   ├── middleware/
│   │   ├── auth.ts
│   │   ├── admin.ts
│   │   ├── guest.ts
│   │   └── redirect.ts
│   └── plugins/
│       ├── container.client.ts
│       ├── quill.client.ts
│       ├── highlight.client.ts
│       └── analytics.client.ts
├── shared/                     # Utilidades Compartidas
│   ├── constants/
│   │   ├── api.ts
│   │   ├── routes.ts
│   │   └── config.ts
│   ├── types/
│   │   ├── api.ts
│   │   ├── entities.ts
│   │   └── common.ts
│   ├── utils/
│   │   ├── date.ts
│   │   ├── string.ts
│   │   ├── validation.ts
│   │   └── formatting.ts
│   ├── container/
│   │   ├── ContainerInterface.ts
│   │   ├── SimpleContainer.ts
│   │   ├── container.ts
│   │   └── bindings.ts
│   └── guards/
│       ├── AuthGuard.ts
│       ├── AdminGuard.ts
│       └── GuestGuard.ts
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   └── components.css
│   ├── images/
│   └── icons/
├── public/
├── tests/
│   ├── unit/
│   ├── integration/
│   └── e2e/
├── locales/
│   ├── es.json
│   ├── en.json
│   └── pt.json
├── nuxt.config.ts
├── package.json
├── tailwind.config.js
├── tsconfig.json
└── vitest.config.ts
```

---

## CLASES, INTERFACES Y COMPONENTES ESPECÍFICOS

### **1. BACKEND - ENTIDADES DE DOMINIO**

#### **User Entity**

```php
// src/Domain/Entities/User.php
final class User extends AggregateRoot
{
    private UserId $id;
    private UserName $name;
    private Email $email;
    private Password $password;
    private UserRole $role;
    private UserStatus $status;
    private ?SocialProvider $socialProvider;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $lastLoginAt;
    private ?Avatar $avatar;

    public function __construct(
        UserId $id,
        UserName $name,
        Email $email,
        Password $password,
        UserRole $role = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role ?? UserRole::guest();
        $this->status = UserStatus::active();
        $this->createdAt = new DateTimeImmutable();
        
        $this->recordEvent(new UserRegistered($this->id, $this->email));
    }

    public function authenticate(Password $providedPassword): bool
    {
        if (!$this->status->isActive()) {
            throw new UserInactiveException($this->id);
        }
        
        return $this->password->verify($providedPassword);
    }

    public function changePassword(Password $newPassword): void
    {
        $oldPassword = $this->password;
        $this->password = $newPassword;
        
        $this->recordEvent(new UserPasswordChanged($this->id, $oldPassword, $newPassword));
    }

    public function linkSocialProvider(SocialProvider $provider): void
    {
        $this->socialProvider = $provider;
        $this->recordEvent(new SocialProviderLinked($this->id, $provider));
    }

    public function recordLogin(): void
    {
        $this->lastLoginAt = new DateTimeImmutable();
        $this->recordEvent(new UserLoggedIn($this->id));
    }

    public function deactivate(string $reason = null): void
    {
        $this->status = UserStatus::inactive();
        $this->recordEvent(new UserDeactivated($this->id, $reason));
    }

    public function assignRole(UserRole $role): void
    {
        $oldRole = $this->role;
        $this->role = $role;
        
        $this->recordEvent(new UserRoleChanged($this->id, $oldRole, $role));
    }

    public function updateProfile(UserName $name, ?Avatar $avatar = null): void
    {
        $this->name = $name;
        if ($avatar) {
            $this->avatar = $avatar;
        }
        
        $this->recordEvent(new UserProfileUpdated($this->id));
    }

    // Getters
    public function getId(): UserId { return $this->id; }
    public function getName(): UserName { return $this->name; }
    public function getEmail(): Email { return $this->email; }
    public function getRole(): UserRole { return $this->role; }
    public function getStatus(): UserStatus { return $this->status; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getLastLoginAt(): ?DateTimeImmutable { return $this->lastLoginAt; }
    public function getAvatar(): ?Avatar { return $this->avatar; }
    public function getSocialProvider(): ?SocialProvider { return $this->socialProvider; }
    
    // Business Logic
    public function isActive(): bool { return $this->status->isActive(); }
    public function isAdmin(): bool { return $this->role->isAdmin(); }
    public function canCreatePosts(): bool { return $this->role->canCreatePosts(); }
    public function canModeratePosts(): bool { return $this->role->canModeratePosts(); }
    public function canPublishPosts(): bool { return $this->role->canPublishPosts(); }
    public function hasPermission(string $permission): bool { return $this->role->hasPermission($permission); }
}
```

#### **Post Entity**

```php
// src/Domain/Entities/Post.php
final class Post extends AggregateRoot
{
    private PostId $id;
    private UserId $authorId;
    private PostTitle $title;
    private PostContent $content;
    private PostSlug $slug;
    private PostStatus $status;
    private ?PostExcerpt $excerpt;
    private ?PostMetadata $metadata;
    private PostTranslations $translations;
    private CategoryCollection $categories;
    private TagCollection $tags;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private ?DateTimeImmutable $publishedAt;
    private ?DateTimeImmutable $scheduledAt;
    private ViewCount $viewCount;
    private ReadingTime $readingTime;
    private ?FeaturedImage $featuredImage;

    public function __construct(
        PostId $id,
        UserId $authorId,
        PostTitle $title,
        PostContent $content,
        PostSlug $slug
    ) {
        $this->id = $id;
        $this->authorId = $authorId;
        $this->title = $title;
        $this->content = $content;
        $this->slug = $slug;
        $this->status = PostStatus::draft();
        $this->translations = new PostTranslations();
        $this->categories = new CategoryCollection();
        $this->tags = new TagCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->viewCount = new ViewCount(0);
        $this->readingTime = ReadingTime::calculate($content);
        
        $this->recordEvent(new PostCreated($this->id, $this->authorId, $this->title));
    }

    public function updateContent(PostTitle $title, PostContent $content): void
    {
        $this->title = $title;
        $this->content = $content;
        $this->readingTime = ReadingTime::calculate($content);
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new PostContentUpdated($this->id));
    }

    public function publish(): void
    {
        $this->guardCanBePublished();
        
        $this->status = PostStatus::published();
        $this->publishedAt = new DateTimeImmutable();
        $this->scheduledAt = null; // Clear any scheduled date
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new PostPublished($this->id, $this->authorId, $this->title));
    }

    public function schedule(DateTimeImmutable $scheduleDate): void
    {
        $this->guardCanBeScheduled($scheduleDate);
        
        $this->status = PostStatus::scheduled();
        $this->scheduledAt = $scheduleDate;
        $this->publishedAt = null; // Clear any published date
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new PostScheduled($this->id, $scheduleDate));
    }

    public function archive(): void
    {
        $this->status = PostStatus::archived();
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new PostArchived($this->id));
    }

    public function addTranslation(Locale $locale, PostTranslation $translation): void
    {
        $this->translations->add($locale, $translation);
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new PostTranslationAdded($this->id, $locale));
    }

    public function assignToCategories(CategoryCollection $categories): void
    {
        $this->categories = $categories;
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new PostCategoriesChanged($this->id, $categories));
    }

    public function assignTags(TagCollection $tags): void
    {
        $this->tags = $tags;
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new PostTagsChanged($this->id, $tags));
    }

    public function setMetadata(PostMetadata $metadata): void
    {
        $this->metadata = $metadata;
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new PostMetadataUpdated($this->id));
    }

    public function setFeaturedImage(FeaturedImage $image): void
    {
        $this->featuredImage = $image;
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new PostFeaturedImageSet($this->id, $image));
    }

    public function incrementViews(): void
    {
        $this->viewCount = $this->viewCount->increment();
        // Note: No updatedAt change for view increments
        
        $this->recordEvent(new PostViewed($this->id));
    }

    public function generateExcerpt(int $length = 155): void
    {
        $this->excerpt = PostExcerpt::fromContent($this->content, $length);
        $this->updatedAt = new DateTimeImmutable();
    }

    private function guardCanBePublished(): void
    {
        if ($this->status->isPublished()) {
            throw new PostAlreadyPublishedException($this->id);
        }
        
        if ($this->title->isEmpty() || $this->content->isEmpty()) {
            throw new IncompletePostException($this->id);
        }
    }

    private function guardCanBeScheduled(DateTimeImmutable $date): void
    {
        if ($date <= new DateTimeImmutable()) {
            throw new InvalidScheduleDateException($date);
        }
        
        if ($this->status->isPublished()) {
            throw new PostAlreadyPublishedException($this->id);
        }
    }

    // Getters
    public function getId(): PostId { return $this->id; }
    public function getAuthorId(): UserId { return $this->authorId; }
    public function getTitle(): PostTitle { return $this->title; }
    public function getContent(): PostContent { return $this->content; }
    public function getSlug(): PostSlug { return $this->slug; }
    public function getStatus(): PostStatus { return $this->status; }
    public function getExcerpt(): ?PostExcerpt { return $this->excerpt; }
    public function getMetadata(): ?PostMetadata { return $this->metadata; }
    public function getTranslations(): PostTranslations { return $this->translations; }
    public function getCategories(): CategoryCollection { return $this->categories; }
    public function getTags(): TagCollection { return $this->tags; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }
    public function getPublishedAt(): ?DateTimeImmutable { return $this->publishedAt; }
    public function getScheduledAt(): ?DateTimeImmutable { return $this->scheduledAt; }
    public function getViewCount(): int { return $this->viewCount->getValue(); }
    public function getReadingTime(): int { return $this->readingTime->getMinutes(); }
    public function getFeaturedImage(): ?FeaturedImage { return $this->featuredImage; }
    
    // Business Logic
    public function isPublished(): bool { return $this->status->isPublished(); }
    public function isDraft(): bool { return $this->status->isDraft(); }
    public function isScheduled(): bool { return $this->status->isScheduled(); }
    public function isArchived(): bool { return $this->status->isArchived(); }
    public function isReadyToPublish(): bool { return $this->scheduledAt !== null && $this->scheduledAt <= new DateTimeImmutable(); }
    public function hasTranslation(Locale $locale): bool { return $this->translations->has($locale); }
    public function isInCategory(CategoryId $categoryId): bool { return $this->categories->contains($categoryId); }
    public function hasTag(TagId $tagId): bool { return $this->tags->contains($tagId); }
}
```

### **2. VALUE OBJECTS CRÍTICOS**

#### **Email Value Object**

```php
// src/Domain/ValueObjects/Email.php
final class Email
{
    private string $value;

    public function __construct(string $email)
    {
        $this->guardValidEmail($email);
        $this->value = strtolower(trim($email));
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getDomain(): string
    {
        return substr($this->value, strpos($this->value, '@') + 1);
    }

    public function getLocalPart(): string
    {
        return substr($this->value, 0, strpos($this->value, '@'));
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function isTemporary(): bool
    {
        $temporaryDomains = [
            '10minutemail.com', 'tempmail.org', 'guerrillamail.com',
            'mailinator.com', 'throwaway.email'
        ];
        
        return in_array($this->getDomain(), $temporaryDomains);
    }

    private function guardValidEmail(string $email): void
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }
        
        if (strlen($email) > 320) { // RFC 5321 limit
            throw new InvalidEmailException("Email too long: {$email}");
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
```

#### **PostSlug Value Object**

```php
// src/Domain/ValueObjects/PostSlug.php
final class PostSlug
{
    private string $value;

    public function __construct(string $slug)
    {
        $this->guardValidSlug($slug);
        $this->value = $slug;
    }

    public static function fromTitle(string $title): self
    {
        $slug = self::generateSlugFromTitle($title);
        return new self($slug);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(PostSlug $other): bool
    {
        return $this->value === $other->value;
    }

    private function guardValidSlug(string $slug): void
    {
        if (empty($slug)) {
            throw new InvalidSlugException('Slug cannot be empty');
        }

        if (strlen($slug) > 255) {
            throw new InvalidSlugException('Slug too long');
        }

        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            throw new InvalidSlugException("Invalid slug format: {$slug}");
        }

        if (str_starts_with($slug, '-') || str_ends_with($slug, '-')) {
            throw new InvalidSlugException("Slug cannot start or end with dash: {$slug}");
        }
    }

    private static function generateSlugFromTitle(string $title): string
    {
        // Remove HTML tags
        $slug = strip_tags($title);
        
        // Convert to lowercase
        $slug = strtolower($slug);
        
        // Replace special characters
        $slug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $slug);
        
        // Replace spaces with dashes
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        
        // Trim dashes
        $slug = trim($slug, '-');
        
        // Limit length
        if (strlen($slug) > 100) {
            $slug = substr($slug, 0, 100);
            $slug = rtrim($slug, '-');
        }

        return $slug;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
```

### **3. USE CASES PRINCIPALES**

#### **CreatePostUseCase**

```php
// src/Application/UseCases/Post/CreatePostUseCase.php
final class CreatePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository,
        private PostDomainService $postDomainService,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function execute(CreatePostCommand $command): PostId
    {
        // Verificar autorización
        $author = $this->userRepository->findById($command->authorId);
        if (!$author) {
            throw new AuthorNotFoundException($command->authorId);
        }

        if (!$author->canCreatePosts()) {
            throw new InsufficientPermissionsException(
                "User {$author->getId()} cannot create posts"
            );
        }

        // Generar slug único
        $slug = $this->postDomainService->generateUniqueSlug(
            $command->title,
            $command->locale,
            $this->postRepository
        );

        // Crear la entidad Post
        $post = new Post(
            PostId::generate(),
            $command->authorId,
            $command->title,
            $command->content,
            $slug
        );

        // Configurar metadatos si existen
        if ($command->metadata) {
            $post->setMetadata($command->metadata);
        }

        // Asignar categorías si existen
        if (!$command->categories->isEmpty()) {
            $post->assignToCategories($command->categories);
        }

        // Asignar tags si existen
        if (!$command->tags->isEmpty()) {
            $post->assignTags($command->tags);
        }

        // Configurar imagen destacada si existe
        if ($command->featuredImage) {
            $post->setFeaturedImage($command->featuredImage);
        }

        // Generar excerpt automáticamente si no se proporcionó
        if (!$command->excerpt) {
            $post->generateExcerpt();
        }

        // Guardar el post
        $this->postRepository->save($post);

        // Agregar traducciones si existen
        foreach ($command->translations as $locale => $translation) {
            $post->addTranslation($locale, $translation);
        }

        // Guardar nuevamente con traducciones
        if (!empty($command->translations)) {
            $this->postRepository->save($post);
        }

        // Disparar eventos
        foreach ($post->getEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return $post->getId();
    }
}
```

#### **LoginUserUseCase**

```php
// src/Application/UseCases/Auth/LoginUserUseCase.php
final class LoginUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenServiceInterface $tokenService,
        private RateLimitServiceInterface $rateLimitService,
        private EventDispatcherInterface $eventDispatcher,
        private SecurityLogger $securityLogger
    ) {}

    public function execute(LoginCommand $command): LoginResult
    {
        $rateLimitKey = "login:{$command->ipAddress}";
        
        // Verificar rate limiting
        if (!$this->rateLimitService->canAttempt($rateLimitKey, 5, 300)) { // 5 attempts per 5 minutes
            $this->securityLogger->logRateLimitExceeded($command->email, $command->ipAddress);
            throw new TooManyAttemptsException('Too many login attempts');
        }

        try {
            // Buscar usuario por email
            $user = $this->userRepository->findByEmail($command->email);
            if (!$user) {
                $this->handleFailedLogin($command, 'User not found');
                throw new InvalidCredentialsException();
            }

            // Verificar si el usuario está activo
            if (!$user->isActive()) {
                $this->securityLogger->logInactiveUserLogin($user->getId(), $command->ipAddress);
                throw new UserInactiveException();
            }

            // Verificar credenciales
            if (!$user->authenticate($command->password)) {
                $this->handleFailedLogin($command, 'Invalid password', $user);
                throw new InvalidCredentialsException();
            }

            // Login exitoso
            $user->recordLogin();
            $this->userRepository->save($user);

            // Generar tokens
            $accessToken = $this->tokenService->generateAccessToken($user->getId());
            $refreshToken = $this->tokenService->generateRefreshToken($user->getId());

            // Limpiar rate limiting
            $this->rateLimitService->clear($rateLimitKey);

            // Log successful login
            $this->securityLogger->logSuccessfulLogin($user->getId(), $command->ipAddress);

            // Disparar evento
            foreach ($user->getEvents() as $event) {
                $this->eventDispatcher->dispatch($event);
            }

            return new LoginResult(
                $accessToken,
                $refreshToken,
                $user->getId(),
                $user->getRole()
            );

        } catch (InvalidCredentialsException | UserInactiveException $e) {
            $this->rateLimitService->recordFailure($rateLimitKey);
            throw $e;
        }
    }

    private function handleFailedLogin(LoginCommand $command, string $reason, ?User $user = null): void
    {
        $this->securityLogger->logFailedLogin(
            $command->email,
            $command->ipAddress,
            $reason,
            $user?->getId()
        );
    }
}
```

### **4. REPOSITORIES CONCRETOS**

#### **EloquentPostRepository**

```php
// src/Infrastructure/Persistence/Eloquent/Repositories/EloquentPostRepository.php
final class EloquentPostRepository implements PostRepositoryInterface
{
    public function __construct(
        private PostMapper $mapper,
        private CacheServiceInterface $cache
    ) {}

    public function save(Post $post): void
    {
        $eloquentModel = $this->findEloquentModel($post->getId()) ?? new PostEloquentModel();
        
        $eloquentModel->fill($this->mapper->toEloquentArray($post));
        $eloquentModel->save();

        // Save translations
        $this->saveTranslations($post, $eloquentModel);

        // Save categories and tags
        $this->saveTaxonomy($post, $eloquentModel);

        // Invalidate cache
        $this->invalidatePostCache($post->getId(), $post->getSlug());
    }

    public function findById(PostId $id): ?Post
    {
        $cacheKey = "post:id:{$id->getValue()}";
        
        return $this->cache->remember($cacheKey, 3600, function () use ($id) {
            $eloquentModel = PostEloquentModel::with([
                'translations', 
                'categories', 
                'tags', 
                'author'
            ])->find($id->getValue());

            return $eloquentModel ? $this->mapper->toDomainEntity($eloquentModel) : null;
        });
    }

    public function findBySlug(PostSlug $slug, Locale $locale): ?Post
    {
        $cacheKey = "post:slug:{$slug->getValue()}:{$locale->getValue()}";
        
        return $this->cache->remember($cacheKey, 3600, function () use ($slug, $locale) {
            $eloquentModel = PostEloquentModel::whereHas('translations', function ($query) use ($slug, $locale) {
                $query->where('slug', $slug->getValue())
                      ->where('locale', $locale->getValue());
            })
            ->with(['translations', 'categories', 'tags', 'author'])
            ->first();

            return $eloquentModel ? $this->mapper->toDomainEntity($eloquentModel) : null;
        });
    }

    public function existsBySlug(PostSlug $slug, Locale $locale): bool
    {
        return PostTranslationEloquentModel::where('slug', $slug->getValue())
            ->where('locale', $locale->getValue())
            ->exists();
    }

    public function findPublished(int $page = 1, int $limit = 12): PostCollection
    {
        $cacheKey = "posts:published:page:{$page}:limit:{$limit}";
        
        $eloquentModels = $this->cache->remember($cacheKey, 1800, function () use ($page, $limit) {
            return PostEloquentModel::where('status', PostStatus::PUBLISHED)
                ->with(['translations', 'categories', 'tags', 'author'])
                ->orderBy('published_at', 'desc')
                ->skip(($page - 1) * $limit)
                ->take($limit)
                ->get();
        });

        return new PostCollection(
            $eloquentModels->map(fn($model) => $this->mapper->toDomainEntity($model))->toArray()
        );
    }

    public function findByAuthor(UserId $authorId): PostCollection
    {
        $eloquentModels = PostEloquentModel::where('user_id', $authorId->getValue())
            ->with(['translations', 'categories', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get();

        return new PostCollection(
            $eloquentModels->map(fn($model) => $this->mapper->toDomainEntity($model))->toArray()
        );
    }

    public function findScheduledPosts(): PostCollection
    {
        $eloquentModels = PostEloquentModel::where('status', PostStatus::SCHEDULED)
            ->where('scheduled_at', '<=', now())
            ->with(['translations', 'categories', 'tags', 'author'])
            ->get();

        return new PostCollection(
            $eloquentModels->map(fn($model) => $this->mapper->toDomainEntity($model))->toArray()
        );
    }

    public function search(string $query): PostCollection
    {
        $eloquentModels = PostEloquentModel::where('status', PostStatus::PUBLISHED)
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder->whereFullText(['title', 'content'], $query)
                    ->orWhereHas('translations', function ($translationQuery) use ($query) {
                        $translationQuery->whereFullText(['title', 'content'], $query);
                    });
            })
            ->with(['translations', 'categories', 'tags', 'author'])
            ->orderByRaw("MATCH(title, content) AGAINST(? IN NATURAL LANGUAGE MODE) DESC", [$query])
            ->get();

        return new PostCollection(
            $eloquentModels->map(fn($model) => $this->mapper->toDomainEntity($model))->toArray()
        );
    }

    public function delete(PostId $id): void
    {
        $eloquentModel = PostEloquentModel::find($id->getValue());
        if ($eloquentModel) {
            $eloquentModel->delete();
            $this->invalidatePostCache($id);
        }
    }

    private function saveTranslations(Post $post, PostEloquentModel $eloquentModel): void
    {
        // Delete existing translations
        $eloquentModel->translations()->delete();

        // Save new translations
        foreach ($post->getTranslations() as $locale => $translation) {
            $eloquentModel->translations()->create([
                'locale' => $locale->getValue(),
                'title' => $translation->getTitle()->getValue(),
                'content' => $translation->getContent()->getValue(),
                'slug' => $translation->getSlug()->getValue(),
                'excerpt' => $translation->getExcerpt()?->getValue(),
                'meta_description' => $translation->getMetaDescription()?->getValue(),
            ]);
        }
    }

    private function saveTaxonomy(Post $post, PostEloquentModel $eloquentModel): void
    {
        // Sync categories
        $categoryIds = $post->getCategories()->getIds();
        $eloquentModel->categories()->sync($categoryIds);

        // Sync tags
        $tagIds = $post->getTags()->getIds();
        $eloquentModel->tags()->sync($tagIds);
    }

    private function findEloquentModel(PostId $id): ?PostEloquentModel
    {
        return PostEloquentModel::find($id->getValue());
    }

    private function invalidatePostCache(PostId $id, PostSlug $slug = null): void
    {
        $this->cache->forget("post:id:{$id->getValue()}");
        
        if ($slug) {
            $locales = ['es', 'en', 'pt'];
            foreach ($locales as $locale) {
                $this->cache->forget("post:slug:{$slug->getValue()}:{$locale}");
            }
        }

        // Invalidate listing caches
        $this->cache->flush('posts:published:*');
    }
}
```

### **5. FRONTEND - ENTIDADES DE DOMINIO**

#### **User Entity (Frontend)**

```typescript
// domain/entities/User.ts
export class User {
    private constructor(
        private readonly _id: UserId,
        private readonly _name: string,
        private readonly _email: string,
        private readonly _role: UserRole,
        private readonly _avatar?: string,
        private readonly _createdAt?: Date,
        private readonly _lastLoginAt?: Date
    ) {}

    static create(data: UserData): User {
        return new User(
            new UserId(data.id),
            data.name,
            data.email,
            UserRole.fromString(data.role),
            data.avatar,
            data.createdAt ? new Date(data.createdAt) : undefined,
            data.lastLoginAt ? new Date(data.lastLoginAt) : undefined
        );
    }

    // Getters
    get id(): string {
        return this._id.value;
    }

    get name(): string {
        return this._name;
    }

    get email(): string {
        return this._email;
    }

    get role(): UserRole {
        return this._role;
    }

    get avatar(): string | undefined {
        return this._avatar;
    }

    get createdAt(): Date | undefined {
        return this._createdAt;
    }

    get lastLoginAt(): Date | undefined {
        return this._lastLoginAt;
    }

    // Business Logic
    canCreatePosts(): boolean {
        return this._role.canCreatePosts();
    }

    canModeratePosts(): boolean {
        return this._role.canModeratePosts();
    }

    canPublishPosts(): boolean {
        return this._role.canPublishPosts();
    }

    isAdmin(): boolean {
        return this._role.isAdmin();
    }

    isSuperAdmin(): boolean {
        return this._role.isSuperAdmin();
    }

    hasPermission(permission: string): boolean {
        return this._role.hasPermission(permission);
    }

    getInitials(): string {
        return this._name.split(' ')
            .map(word => word.charAt(0))
            .join('')
            .toUpperCase()
            .substring(0, 2);
    }

    getDisplayName(): string {
        return this._name;
    }

    getAvatarUrl(): string {
        return this._avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(this._name)}&background=random`;
    }

    toJSON(): Record<string, any> {
        return {
            id: this.id,
            name: this.name,
            email: this.email,
            role: this._role.value,
            avatar: this.avatar,
            createdAt: this.createdAt?.toISOString(),
            lastLoginAt: this.lastLoginAt?.toISOString()
        };
    }
}
```

#### **Post Entity (Frontend)**

```typescript
// domain/entities/Post.ts
export class Post {
    private constructor(
        private readonly _id: PostId,
        private readonly _title: string,
        private readonly _content: string,
        private readonly _slug: string,
        private readonly _status: PostStatus,
        private readonly _excerpt: string,
        private readonly _readingTime: number,
        private readonly _viewCount: number,
        private readonly _createdAt: Date,
        private readonly _updatedAt: Date,
        private readonly _publishedAt?: Date,
        private readonly _scheduledAt?: Date,
        private readonly _featuredImage?: string,
        private readonly _author?: User,
        private readonly _categories: Category[] = [],
        private readonly _tags: Tag[] = []
    ) {}

    static create(data: PostData): Post {
        return new Post(
            new PostId(data.id),
            data.title,
            data.content,
            data.slug,
            PostStatus.fromString(data.status),
            data.excerpt,
            data.readingTime,
            data.viewCount || 0,
            new Date(data.createdAt),
            new Date(data.updatedAt),
            data.publishedAt ? new Date(data.publishedAt) : undefined,
            data.scheduledAt ? new Date(data.scheduledAt) : undefined,
            data.featuredImage,
            data.author ? User.create(data.author) : undefined,
            data.categories?.map(cat => Category.create(cat)) || [],
            data.tags?.map(tag => Tag.create(tag)) || []
        );
    }

    // Getters
    get id(): string {
        return this._id.value;
    }

    get title(): string {
        return this._title;
    }

    get content(): string {
        return this._content;
    }

    get slug(): string {
        return this._slug;
    }

    get status(): PostStatus {
        return this._status;
    }

    get excerpt(): string {
        return this._excerpt;
    }

    get readingTime(): number {
        return this._readingTime;
    }

    get viewCount(): number {
        return this._viewCount;
    }

    get createdAt(): Date {
        return this._createdAt;
    }

    get updatedAt(): Date {
        return this._updatedAt;
    }

    get publishedAt(): Date | undefined {
        return this._publishedAt;
    }

    get scheduledAt(): Date | undefined {
        return this._scheduledAt;
    }

    get featuredImage(): string | undefined {
        return this._featuredImage;
    }

    get author(): User | undefined {
        return this._author;
    }

    get categories(): Category[] {
        return this._categories;
    }

    get tags(): Tag[] {
        return this._tags;
    }

    // Business Logic
    isPublished(): boolean {
        return this._status.isPublished();
    }

    isDraft(): boolean {
        return this._status.isDraft();
    }

    isScheduled(): boolean {
        return this._status.isScheduled();
    }

    isArchived(): boolean {
        return this._status.isArchived();
    }

    canEdit(user?: User): boolean {
        if (!user) return false;
        
        return user.isAdmin() || 
               (this._author && user.id === this._author.id);
    }

    canDelete(user?: User): boolean {
        if (!user) return false;
        
        return user.isAdmin() || 
               (user.canModeratePosts() && this.isDraft());
    }

    canPublish(user?: User): boolean {
        if (!user) return false;
        
        return user.canPublishPosts() && 
               (this.isDraft() || this.isScheduled());
    }

    getUrl(locale: string = 'es'): string {
        if (!this.publishedAt) return '';
        
        const year = this.publishedAt.getFullYear();
        const month = String(this.publishedAt.getMonth() + 1).padStart(2, '0');
        
        return `/${locale}/${year}/${month}/${this._slug}`;
    }

    getEditUrl(): string {
        return `/admin/posts/${this.id}/edit`;
    }

    getPreviewUrl(): string {
        return `/admin/posts/${this.id}/preview`;
    }

    getReadingTimeText(t: (key: string, params?: any) => string): string {
        return t('posts.readingTime', { minutes: this.readingTime });
    }

    getStatusText(t: (key: string) => string): string {
        return t(`posts.status.${this.status.value}`);
    }

    getStatusColor(): string {
        switch (this.status.value) {
            case 'published': return 'green';
            case 'draft': return 'gray';
            case 'scheduled': return 'blue';
            case 'archived': return 'orange';
            default: return 'gray';
        }
    }

    getCategoryNames(): string[] {
        return this.categories.map(cat => cat.name);
    }

    getTagNames(): string[] {
        return this.tags.map(tag => tag.name);
    }

    hasCategory(categoryId: string): boolean {
        return this.categories.some(cat => cat.id === categoryId);
    }

    hasTag(tagId: string): boolean {
        return this.tags.some(tag => tag.id === tagId);
    }

    getFeaturedImageUrl(): string | undefined {
        return this._featuredImage;
    }

    getPlaceholderImage(): string {
        return `https://picsum.photos/800/400?random=${this.id}`;
    }

    toJSON(): Record<string, any> {
        return {
            id: this.id,
            title: this.title,
            slug: this.slug,
            excerpt: this.excerpt,
            status: this.status.value,
            readingTime: this.readingTime,
            viewCount: this.viewCount,
            createdAt: this.createdAt.toISOString(),
            updatedAt: this.updatedAt.toISOString(),
            publishedAt: this.publishedAt?.toISOString(),
            scheduledAt: this.scheduledAt?.toISOString(),
            featuredImage: this.featuredImage,
            author: this.author?.toJSON(),
            categories: this.categories.map(cat => cat.toJSON()),
            tags: this.tags.map(tag => tag.toJSON())
        };
    }
}
```

### **6. CONTAINER DE SERVICIOS COMPLETO**

#### **Service Container y Bindings**

```typescript
// shared/container/bindings.ts
export const configureContainer = (container: ContainerInterface) => {
    const config = useRuntimeConfig();

    // ========== STORAGE SERVICES ==========
    container.singleton('TokenStorage', () => new LocalTokenStorage());
    container.singleton('CacheStorage', () => new BrowserCacheStorage());
    container.singleton('SessionStorage', () => new BrowserSessionStorage());

    // ========== HTTP CLIENT ==========
    container.singleton('HttpClient', () => 
        new HttpClient(
            config.public.apiBaseUrl,
            container.get('TokenStorage'),
            {
                timeout: 30000,
                retries: 3,
                retryDelay: 1000
            }
        )
    );

    // ========== REPOSITORIES ==========
    container.singleton('UserRepository', () =>
        new HttpUserRepository(
            container.get('HttpClient'),
            container.get('TokenStorage')
        )
    );

    container.singleton('PostRepository', () =>
        new HttpPostRepository(
            container.get('HttpClient'),
            container.get('CacheStorage')
        )
    );

    container.singleton('CommentRepository', () =>
        new HttpCommentRepository(
            container.get('HttpClient')
        )
    );

    container.singleton('NewsletterRepository', () =>
        new HttpNewsletterRepository(
            container.get('HttpClient')
        )
    );

    container.singleton('MediaRepository', () =>
        new HttpMediaRepository(
            container.get('HttpClient')
        )
    );

    container.singleton('CategoryRepository', () =>
        new HttpCategoryRepository(
            container.get('HttpClient'),
            container.get('CacheStorage')
        )
    );

    container.singleton('TagRepository', () =>
        new HttpTagRepository(
            container.get('HttpClient'),
            container.get('CacheStorage')
        )
    );

    // ========== SERVICES ==========
    container.singleton('ValidationService', () => new ValidationService());
    container.singleton('NotificationService', () => new NotificationService());
    container.singleton('AnalyticsService', () => new AnalyticsService());
    container.singleton('SEOService', () => new SEOService());
    container.singleton('ThemeService', () => new ThemeService(container.get('SessionStorage')));

    // ========== USE CASES - AUTH ==========
    container.bind('LoginUseCase', () =>
        new LoginUseCase(
            container.get('UserRepository'),
            container.get('TokenStorage'),
            container.get('NotificationService')
        )
    );

    container.bind('LogoutUseCase', () =>
        new LogoutUseCase(
            container.get('UserRepository'),
            container.get('TokenStorage')
        )
    );

    container.bind('RegisterUseCase', () =>
        new RegisterUseCase(
            container.get('UserRepository'),
            container.get('ValidationService')
        )
    );

    container.bind('GetCurrentUserUseCase', () =>
        new GetCurrentUserUseCase(
            container.get('UserRepository'),
            container.get('TokenStorage')
        )
    );

    // ========== USE CASES - POSTS ==========
    container.bind('GetPostsUseCase', () =>
        new GetPostsUseCase(
            container.get('PostRepository'),
            container.get('CacheStorage')
        )
    );

    container.bind('GetPostUseCase', () =>
        new GetPostUseCase(
            container.get('PostRepository'),
            container.get('CacheStorage')
        )
    );

    container.bind('CreatePostUseCase', () =>
        new CreatePostUseCase(
            container.get('PostRepository'),
            container.get('UserRepository'),
            container.get('ValidationService')
        )
    );

    container.bind('UpdatePostUseCase', () =>
        new UpdatePostUseCase(
            container.get('PostRepository'),
            container.get('UserRepository'),
            container.get('ValidationService')
        )
    );

    container.bind('DeletePostUseCase', () =>
        new DeletePostUseCase(
            container.get('PostRepository'),
            container.get('UserRepository')
        )
    );

    container.bind('PublishPostUseCase', () =>
        new PublishPostUseCase(
            container.get('PostRepository'),
            container.get('UserRepository')
        )
    );

    // ========== USE CASES - COMMENTS ==========
    container.bind('GetCommentsUseCase', () =>
        new GetCommentsUseCase(
            container.get('CommentRepository')
        )
    );

    container.bind('CreateCommentUseCase', () =>
        new CreateCommentUseCase(
            container.get('CommentRepository'),
            container.get('ValidationService'),
            container.get('NotificationService')
        )
    );

    // ========== USE CASES - NEWSLETTER ==========
    container.bind('SubscribeNewsletterUseCase', () =>
        new SubscribeNewsletterUseCase(
            container.get('NewsletterRepository'),
            container.get('ValidationService'),
            container.get('NotificationService')
        )
    );

    container.bind('UnsubscribeNewsletterUseCase', () =>
        new UnsubscribeNewsletterUseCase(
            container.get('NewsletterRepository')
        )
    );

    // ========== USE CASES - MEDIA ==========
    container.bind('UploadMediaUseCase', () =>
        new UploadMediaUseCase(
            container.get('MediaRepository'),
            container.get('ValidationService')
        )
    );

    container.bind('DeleteMediaUseCase', () =>
        new DeleteMediaUseCase(
            container.get('MediaRepository'),
            container.get('UserRepository')
        )
    );

    // ========== USE CASES - TAXONOMY ==========
    container.bind('GetCategoriesUseCase', () =>
        new GetCategoriesUseCase(
            container.get('CategoryRepository'),
            container.get('CacheStorage')
        )
    );

    container.bind('GetTagsUseCase', () =>
        new GetTagsUseCase(
            container.get('TagRepository'),
            container.get('CacheStorage')
        )
    );

    container.bind('CreateCategoryUseCase', () =>
        new CreateCategoryUseCase(
            container.get('CategoryRepository'),
            container.get('ValidationService')
        )
    );

    container.bind('CreateTagUseCase', () =>
        new CreateTagUseCase(
            container.get('TagRepository'),
            container.get('ValidationService')
        )
    );
};
```

### **7. COMPONENTES VUE PRINCIPALES**

#### **PostEditor Component**

```vue
<!-- interface/components/admin/PostEditor.vue -->
<template>
  <div class="post-editor">
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Header Controls -->
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <select 
            v-model="formData.status" 
            class="border rounded-md px-3 py-2"
            :disabled="isLoading"
          >
            <option value="draft">{{ $t('posts.status.draft') }}</option>
            <option value="published">{{ $t('posts.status.published') }}</option>
            <option value="scheduled">{{ $t('posts.status.scheduled') }}</option>
          </select>
          
          <div v-if="formData.status === 'scheduled'" class="flex items-center space-x-2">
            <label class="text-sm font-medium">{{ $t('posts.scheduleDate') }}:</label>
            <input 
              v-model="formData.scheduledAt" 
              type="datetime-local"
              class="border rounded-md px-3 py-2"
              :min="minScheduleDate"
              required
            />
          </div>
        </div>

        <div class="flex items-center space-x-3">
          <span v-if="autoSaveStatus" class="text-sm text-gray-500">
            {{ autoSaveStatus }}
          </span>
          
          <button 
            type="button" 
            @click="showPreview = !showPreview"
            class="px-4 py-2 text-blue-600 border border-blue-600 rounded-md hover:bg-blue-50"
            :disabled="isLoading"
          >
            {{ showPreview ? $t('common.hidePreview') : $t('common.showPreview') }}
          </button>
          
          <button 
            type="submit" 
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
            :disabled="isLoading || !isFormValid"
          >
            <LoadingSpinner v-if="isLoading" class="w-4 h-4 mr-2" />
            {{ post ? $t('common.update') : $t('common.create') }}
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Editor Panel -->
        <div class="space-y-4">
          <!-- Title -->
          <div>
            <label class="block text-sm font-medium mb-1">
              {{ $t('posts.title') }} *
            </label>
            <input 
              v-model="formData.title"
              type="text"
              class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500"
              :placeholder="$t('posts.titlePlaceholder')"
              required
              @input="handleTitleChange"
            />
            <div v-if="formData.slug" class="text-xs text-gray-500 mt-1">
              URL: {{ getPostUrl(formData.slug) }}
            </div>
          </div>

          <!-- Content Editor -->
          <div>
            <label class="block text-sm font-medium mb-1">
              {{ $t('posts.content') }} *
            </label>
            <div 
              ref="editorContainer"
              class="border rounded-md"
              style="min-height: 400px;"
            ></div>
            <div class="text-xs text-gray-500 mt-1">
              {{ $t('posts.readingTime') }}: {{ readingTime }} {{ $t('common.minutes') }}
            </div>
          </div>

          <!-- Excerpt -->
          <div>
            <label class="block text-sm font-medium mb-1">
              {{ $t('posts.excerpt') }}
            </label>
            <textarea 
              v-model="formData.excerpt"
              rows="3"
              class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500"
              :placeholder="$t('posts.excerptPlaceholder')"
              maxlength="300"
            ></textarea>
            <div class="text-xs text-gray-500 mt-1">
              {{ formData.excerpt?.length || 0 }}/300
            </div>
          </div>

          <!-- Meta Description -->
          <div>
            <label class="block text-sm font-medium mb-1">
              {{ $t('seo.metaDescription') }}
            </label>
            <textarea 
              v-model="formData.metaDescription"
              rows="2"
              class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500"
              :placeholder="$t('seo.metaDescriptionPlaceholder')"
              maxlength="160"
            ></textarea>
            <div class="text-xs text-gray-500 mt-1">
              {{ formData.metaDescription?.length || 0 }}/160
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
          <!-- Featured Image -->
          <div>
            <label class="block text-sm font-medium mb-2">
              {{ $t('posts.featuredImage') }}
            </label>
            <MediaUploader 
              v-model="formData.featuredImage"
              accept="image/*"
              :max-size="5 * 1024 * 1024"
              class="w-full"
            />
          </div>

          <!-- Categories -->
          <div>
            <label class="block text-sm font-medium mb-2">
              {{ $t('taxonomy.categories') }}
            </label>
            <CategorySelector 
              v-model="formData.categories"
              :multiple="true"
              class="w-full"
            />
          </div>

          <!-- Tags -->
          <div>
            <label class="block text-sm font-medium mb-2">
              {{ $t('taxonomy.tags') }}
            </label>
            <TagInput 
              v-model="formData.tags"
              :suggestions="availableTags"
              class="w-full"
            />
          </div>

          <!-- Language Selector -->
          <div>
            <label class="block text-sm font-medium mb-2">
              {{ $t('i18n.language') }}
            </label>
            <select 
              v-model="formData.locale"
              class="w-full border rounded-md px-3 py-2"
            >
              <option value="es">{{ $t('i18n.spanish') }}</option>
              <option value="en">{{ $t('i18n.english') }}</option>
              <option value="pt">{{ $t('i18n.portuguese') }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Preview Panel -->
      <div v-if="showPreview" class="border-t pt-6">
        <h3 class="text-lg font-semibold mb-4">{{ $t('common.preview') }}</h3>
        <PostPreview 
          :title="formData.title"
          :content="formData.content"
          :excerpt="formData.excerpt"
          :featured-image="formData.featuredImage"
          :reading-time="readingTime"
        />
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { container } from '~/shared/container/container';
import type { CreatePostUseCase } from '~/application/use-cases/posts/CreatePostUseCase';
import type { UpdatePostUseCase } from '~/application/use-cases/posts/UpdatePostUseCase';
import type { Post } from '~/domain/entities/Post';

// Props
interface Props {
  post?: Post;
}

const props = defineProps<Props>();

// Emits
const emit = defineEmits<{
  saved: [post: Post];
  error: [error: Error];
}>();

// Container services
const createPostUseCase = container().get<CreatePostUseCase>('CreatePostUseCase');
const updatePostUseCase = container().get<UpdatePostUseCase>('UpdatePostUseCase');

// Refs
const editorContainer = ref<HTMLElement>();
const quillEditor = ref<any>();
const isLoading = ref(false);
const showPreview = ref(false);
const autoSaveTimeout = ref<NodeJS.Timeout>();
const autoSaveStatus = ref('');
const availableTags = ref<string[]>([]);

// Form data
const formData = reactive({
  title: props.post?.title || '',
  content: props.post?.content || '',
  excerpt: props.post?.excerpt || '',
  metaDescription: props.post?.metadata?.metaDescription || '',
  featuredImage: props.post?.featuredImage || '',
  categories: props.post?.categories.map(c => c.id) || [],
  tags: props.post?.tags.map(t => t.name) || [],
  locale: 'es',
  status: props.post?.status.value || 'draft',
  scheduledAt: props.post?.scheduledAt?.toISOString().slice(0, 16) || ''
});

// Computed
const isFormValid = computed(() => {
  return formData.title.trim() && 
         formData.content.trim() &&
         (formData.status !== 'scheduled' || formData.scheduledAt);
});

const readingTime = computed(() => {
  const words = formData.content.replace(/<[^>]*>/g, '').split(/\s+/).length;
  return Math.max(1, Math.ceil(words / 200));
});

const minScheduleDate = computed(() => {
  const now = new Date();
  now.setMinutes(now.getMinutes() + 5); // Minimum 5 minutes from now
  return now.toISOString().slice(0, 16);
});

// Methods
const initializeEditor = async () => {
  const { default: Quill } = await import('quill');
  const { default: hljs } = await import('highlight.js');

  // Configure Quill with syntax highlighting
  quillEditor.value = new Quill(editorContainer.value!, {
    theme: 'snow',
    modules: {
      syntax: {
        highlight: (text: string) => hljs.highlightAuto(text).value
      },
      toolbar: [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link', 'image'],
        ['clean']
      ]
    }
  });

  // Set initial content
  if (formData.content) {
    quillEditor.value.root.innerHTML = formData.content;
  }

  // Listen for changes
  quillEditor.value.on('text-change', () => {
    formData.content = quillEditor.value.root.innerHTML;
    scheduleAutoSave();
  });
};

const handleTitleChange = () => {
  if (!props.post) {
    // Generate slug for new posts
    formData.slug = generateSlug(formData.title);
  }
  scheduleAutoSave();
};

const generateSlug = (title: string): string => {
  return title
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim('-');
};

const getPostUrl = (slug: string): string => {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  return `/${formData.locale}/${year}/${month}/${slug}`;
};

const scheduleAutoSave = () => {
  if (autoSaveTimeout.value) {
    clearTimeout(autoSaveTimeout.value);
  }

  autoSaveTimeout.value = setTimeout(() => {
    if (props.post && isFormValid.value) {
      performAutoSave();
    }
  }, 30000); // Auto-save every 30 seconds
};

const performAutoSave = async () => {
  if (!props.post || !isFormValid.value) return;

  try {
    autoSaveStatus.value = 'Guardando...';
    
    await updatePostUseCase.execute({
      id: props.post.id,
      title: formData.title,
      content: formData.content,
      excerpt: formData.excerpt,
      metaDescription: formData.metaDescription,
      featuredImage: formData.featuredImage,
      categories: formData.categories,
      tags: formData.tags,
      locale: formData.locale,
      status: 'draft' // Always save as draft during auto-save
    });

    autoSaveStatus.value = 'Guardado automáticamente';
    
    setTimeout(() => {
      autoSaveStatus.value = '';
    }, 3000);
    
  } catch (error) {
    autoSaveStatus.value = 'Error al guardar';
    console.error('Auto-save failed:', error);
  }
};

const handleSubmit = async () => {
  if (!isFormValid.value) return;

  isLoading.value = true;

  try {
    const postData = {
      title: formData.title,
      content: formData.content,
      excerpt: formData.excerpt,
      metaDescription: formData.metaDescription,
      featuredImage: formData.featuredImage,
      categories: formData.categories,
      tags: formData.tags,
      locale: formData.locale,
      status: formData.status,
      scheduledAt: formData.status === 'scheduled' ? new Date(formData.scheduledAt) : undefined
    };

    let savedPost: Post;

    if (props.post) {
      // Update existing post
      savedPost = await updatePostUseCase.execute({
        id: props.post.id,
        ...postData
      });
    } else {
      // Create new post
      savedPost = await createPostUseCase.execute(postData);
    }

    emit('saved', savedPost);
    
    // Clear auto-save timer
    if (autoSaveTimeout.value) {
      clearTimeout(autoSaveTimeout.value);
    }
    
  } catch (error) {
    emit('error', error instanceof Error ? error : new Error('Unknown error'));
  } finally {
    isLoading.value = false;
  }
};

// Lifecycle
onMounted(() => {
  initializeEditor();
});

onUnmounted(() => {
  if (autoSaveTimeout.value) {
    clearTimeout(autoSaveTimeout.value);
  }
});

// Watch for external changes
watch(() => props.post, (newPost) => {
  if (newPost && quillEditor.value) {
    formData.title = newPost.title;
    formData.content = newPost.content;
    formData.excerpt = newPost.excerpt;
    
    quillEditor.value.root.innerHTML = newPost.content;
  }
}, { immediate: true });
</script>

<style scoped>
:deep(.ql-editor) {
  min-height: 300px;
  font-size: 16px;
  line-height: 1.6;
}

:deep(.ql-editor.ql-blank::before) {
  font-style: normal;
  color: #9ca3af;
}

:deep(.ql-syntax) {
  background-color: #f8f9fa;
  border-radius: 4px;
  padding: 1rem;
  margin: 1rem 0;
  overflow-x: auto;
}
</style>
```

