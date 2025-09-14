# KEY DOMAIN ENTITIES

## USER ENTITY (Backend)
```php
final class User extends AggregateRoot
{
    private UserId $id;
    private UserName $name;
    private Email $email;
    private Password $password;
    private UserRole $role;
    private UserStatus $status;
    private ?SocialProvider $socialProvider;

    // Business Methods
    public function authenticate(Password $providedPassword): bool
    public function changePassword(Password $newPassword): void
    public function linkSocialProvider(SocialProvider $provider): void
    public function recordLogin(): void
    public function deactivate(): void

    // Business Logic Queries
    public function isActive(): bool
    public function canCreatePosts(): bool
    public function canModeratePosts(): bool
    public function hasPermission(string $permission): bool
}
```

## POST ENTITY (Backend)
```php
final class Post extends AggregateRoot
{
    private PostId $id;
    private UserId $authorId;
    private PostTitle $title;
    private PostContent $content;
    private PostSlug $slug;
    private PostStatus $status;
    private PostTranslations $translations;
    private ViewCount $viewCount;
    private ReadingTime $readingTime;

    // Business Methods
    public function publish(): void
    public function schedule(DateTimeImmutable $scheduleDate): void
    public function archive(): void
    public function updateContent(PostContent $newContent): void
    public function addTranslation(Locale $locale, PostTranslation $translation): void
    public function incrementViews(): void

    // Business Logic Queries
    public function isPublished(): bool
    public function isDraft(): bool
    public function isScheduled(): bool
    public function canEdit(User $user): bool
}
```

## COMMENT ENTITY (Backend)
```php
final class Comment extends AggregateRoot
{
    private CommentId $id;
    private PostId $postId;
    private ?UserId $userId;
    private CommentContent $content;
    private CommentStatus $status;
    private ?CommentId $parentId;

    // Business Methods
    public function approve(): void
    public function reject(string $reason): void
    public function markAsSpam(): void
    public function reply(Comment $reply): void

    // Business Logic Queries
    public function isApproved(): bool
    public function isPending(): bool
    public function isSpam(): bool
    public function hasReplies(): bool
}
```

## USER ENTITY (Frontend)
```typescript
export class User {
    private constructor(
        private readonly _id: UserId,
        private readonly _name: string,
        private readonly _email: string,
        private readonly _role: UserRole,
        private readonly _avatar?: string
    ) {}

    // Business Logic
    canCreatePosts(): boolean
    canModeratePosts(): boolean
    canPublishPosts(): boolean
    isAdmin(): boolean
    isSuperAdmin(): boolean
    hasPermission(permission: string): boolean

    // Computed Properties
    getInitials(): string
    getDisplayName(): string
    getAvatarUrl(): string
}
```

## POST ENTITY (Frontend)
```typescript
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
        private readonly _publishedAt?: Date
    ) {}

    // Business Logic
    isPublished(): boolean
    isDraft(): boolean
    isScheduled(): boolean
    canEdit(user?: User): boolean
    canDelete(user?: User): boolean
    canPublish(user?: User): boolean

    // Computed Properties
    getUrl(locale: string): string
    getEditUrl(): string
    getPreviewUrl(): string
    getReadingTimeText(t: Function): string
    getStatusColor(): string
}
```

## KEY VALUE OBJECTS

### Email Value Object
```php
final class Email
{
    private string $value;

    public function __construct(string $email)
    public function getValue(): string
    public function getDomain(): string
    public function getLocalPart(): string
    public function equals(Email $other): bool
    public function isTemporary(): bool
}
```

### PostSlug Value Object
```php
final class PostSlug
{
    private string $value;

    public function __construct(string $slug)
    public static function fromTitle(string $title): self
    public function getValue(): string
    public function equals(PostSlug $other): bool
}
```

### Password Value Object
```php
final class Password
{
    private string $hashedValue;

    public static function fromPlainText(string $plainText): self
    public static function fromHash(string $hash): self
    public function verify(Password $plainPassword): bool
    public function getHash(): string
}
```

## BUSINESS RULES EXAMPLES

### User Business Rules
- SuperAdmin se crea desactivado por defecto
- Solo admins pueden moderar posts
- Usuarios guest solo pueden comentar
- Login fallido incrementa contador de intentos
- OAuth linking preserva datos existentes

### Post Business Rules
- Solo se puede publicar si está completo (título + contenido)
- Scheduling solo para fechas futuras
- Slug debe ser único por idioma
- Auto-save cada 30 segundos en draft
- View count no cambia updatedAt

### Comment Business Rules
- Comentarios de no-admins van a pending por defecto
- Solo admins pueden aprobar/rechazar
- Spam detection automática (futura)
- Respuestas anidadas máximo 3 niveles
- Rate limiting: 3 comentarios por minuto