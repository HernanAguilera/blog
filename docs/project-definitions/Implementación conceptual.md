# IMPLEMENTACIÓN CONCEPTUAL - CLEAN ARCHITECTURE COMPLETA

## ARQUITECTURA GENERAL DEL SISTEMA

### **SEPARACIÓN DE RESPONSABILIDADES**

- **Backend (Laravel API)**: Lógica de negocio, persistencia, servicios externos
- **Frontend (Nuxt 3)**: Presentación, interacción usuario, estado cliente
- **Comunicación**: API REST con JSON, autenticación JWT

### **PRINCIPIOS CLEAN ARCHITECTURE**

1. **Independencia de frameworks**: Dominio no conoce Laravel/Nuxt
2. **Testabilidad**: Todas las capas son testables por separado
3. **Independencia de UI**: Cambiar frontend no afecta lógica de negocio
4. **Independencia de DB**: Cambiar base de datos no afecta dominio
5. **Regla de dependencia**: Solo hacia el centro (Dominio)

---

## BACKEND - CLEAN ARCHITECTURE

### **ESTRUCTURA DE DIRECTORIOS BACKEND**

```
src/
├── Domain/                 # Reglas de negocio puras
│   ├── Entities/          # Entidades de dominio
│   ├── ValueObjects/      # Objetos de valor
│   ├── Repositories/      # Interfaces de repositorio
│   ├── Services/          # Servicios de dominio
│   ├── Events/           # Eventos de dominio
│   └── Exceptions/       # Excepciones de dominio
├── Application/           # Casos de uso y orquestación
│   ├── UseCases/         # Casos de uso específicos
│   ├── Commands/         # Comandos (escritura)
│   ├── Queries/          # Consultas (lectura)
│   ├── Handlers/         # Manejadores CQRS
│   ├── Services/         # Servicios de aplicación
│   └── Interfaces/       # Contratos de servicios
├── Infrastructure/        # Implementaciones concretas
│   ├── Persistence/      # Repositorios concretos
│   ├── Services/         # Servicios de infraestructura
│   ├── External/         # APIs terceros
│   ├── Queue/           # Jobs y colas
│   └── Providers/       # Service providers
└── Interface/            # Capa de presentación
    ├── Http/            # Controllers HTTP
    ├── Console/         # Comandos CLI
    ├── Resources/       # Transformadores API
    └── Middleware/      # Middleware HTTP
```

### **CAPA DE DOMINIO (Domain)**

#### **Entidades de Dominio**

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

    public function __construct(
        UserId $id,
        UserName $name, 
        Email $email,
        Password $password,
        UserRole $role
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->status = UserStatus::active();
        $this->createdAt = new DateTimeImmutable();
        
        $this->recordEvent(new UserRegistered($this->id, $this->email));
    }

    public function authenticate(Password $providedPassword): bool
    {
        return $this->password->verify($providedPassword);
    }

    public function changePassword(Password $newPassword): void
    {
        $this->password = $newPassword;
        $this->recordEvent(new UserPasswordChanged($this->id));
    }

    public function deactivate(): void
    {
        $this->status = UserStatus::inactive();
        $this->recordEvent(new UserDeactivated($this->id));
    }

    public function linkSocialProvider(SocialProvider $provider): void
    {
        $this->socialProvider = $provider;
    }

    public function recordLogin(): void
    {
        $this->lastLoginAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): UserId { return $this->id; }
    public function getName(): UserName { return $this->name; }
    public function getEmail(): Email { return $this->email; }
    public function getRole(): UserRole { return $this->role; }
    public function isActive(): bool { return $this->status->isActive(); }
}
```

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
    private PostMetadata $metadata;
    private PostTranslations $translations;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $publishedAt;
    private ?DateTimeImmutable $scheduledAt;
    private ViewCount $viewCount;
    private ReadingTime $readingTime;

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
        $this->createdAt = new DateTimeImmutable();
        $this->viewCount = new ViewCount(0);
        $this->readingTime = ReadingTime::calculate($content);
        
        $this->recordEvent(new PostCreated($this->id, $this->authorId));
    }

    public function publish(): void
    {
        $this->guardCanBePublished();
        $this->status = PostStatus::published();
        $this->publishedAt = new DateTimeImmutable();
        $this->recordEvent(new PostPublished($this->id, $this->authorId));
    }

    public function schedule(DateTimeImmutable $scheduleDate): void
    {
        $this->guardCanBeScheduled($scheduleDate);
        $this->status = PostStatus::scheduled();
        $this->scheduledAt = $scheduleDate;
        $this->recordEvent(new PostScheduled($this->id, $scheduleDate));
    }

    public function archive(): void
    {
        $this->status = PostStatus::archived();
        $this->recordEvent(new PostArchived($this->id));
    }

    public function updateContent(PostContent $newContent): void
    {
        $this->content = $newContent;
        $this->readingTime = ReadingTime::calculate($newContent);
        $this->recordEvent(new PostContentUpdated($this->id));
    }

    public function addTranslation(Locale $locale, PostTranslation $translation): void
    {
        $this->translations->add($locale, $translation);
    }

    public function incrementViews(): void
    {
        $this->viewCount = $this->viewCount->increment();
    }

    public function setMetadata(PostMetadata $metadata): void
    {
        $this->metadata = $metadata;
    }

    private function guardCanBePublished(): void
    {
        if ($this->status->isPublished()) {
            throw new PostAlreadyPublishedException($this->id);
        }
    }

    private function guardCanBeScheduled(DateTimeImmutable $date): void
    {
        if ($date <= new DateTimeImmutable()) {
            throw new InvalidScheduleDateException($date);
        }
    }

    // Getters
    public function getId(): PostId { return $this->id; }
    public function getAuthorId(): UserId { return $this->authorId; }
    public function getTitle(): PostTitle { return $this->title; }
    public function getContent(): PostContent { return $this->content; }
    public function getSlug(): PostSlug { return $this->slug; }
    public function getStatus(): PostStatus { return $this->status; }
    public function isPublished(): bool { return $this->status->isPublished(); }
    public function getPublishedAt(): ?DateTimeImmutable { return $this->publishedAt; }
    public function getViewCount(): int { return $this->viewCount->getValue(); }
}
```

#### **Value Objects**

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

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    private function guardValidEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($email);
        }
    }
}

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
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        return new self($slug);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function guardValidSlug(string $slug): void
    {
        if (!preg_match('/^[a-z0-9-]+$/', $slug) || strlen($slug) === 0) {
            throw new InvalidSlugException($slug);
        }
    }
}

// src/Domain/ValueObjects/Password.php
final class Password
{
    private string $hashedValue;

    private function __construct(string $hashedValue)
    {
        $this->hashedValue = $hashedValue;
    }

    public static function fromPlainText(string $plainText): self
    {
        self::guardValidPassword($plainText);
        return new self(password_hash($plainText, PASSWORD_BCRYPT));
    }

    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    public function verify(Password $plainPassword): bool
    {
        return password_verify($plainPassword->hashedValue, $this->hashedValue);
    }

    public function getHash(): string
    {
        return $this->hashedValue;
    }

    private static function guardValidPassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new WeakPasswordException();
        }
    }
}
```

#### **Servicios de Dominio**

```php
// src/Domain/Services/PostDomainService.php
final class PostDomainService
{
    public function generateUniqueSlug(
        PostTitle $title,
        Locale $locale,
        PostRepositoryInterface $repository
    ): PostSlug {
        $baseSlug = PostSlug::fromTitle($title->getValue());
        $slug = $baseSlug;
        $counter = 1;

        while ($repository->existsBySlug($slug, $locale)) {
            $slug = new PostSlug($baseSlug->getValue() . '-' . $counter);
            $counter++;
        }

        return $slug;
    }

    public function canUserEditPost(User $user, Post $post): bool
    {
        return $user->getId()->equals($post->getAuthorId()) || 
               $user->getRole()->isAdmin();
    }
}

// src/Domain/Services/UserDomainService.php
final class UserDomainService
{
    public function isEmailUnique(
        Email $email,
        UserRepositoryInterface $repository
    ): bool {
        return !$repository->existsByEmail($email);
    }
}
```

#### **Interfaces de Repositorio**

```php
// src/Domain/Repositories/UserRepositoryInterface.php
interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(UserId $id): ?User;
    public function findByEmail(Email $email): ?User;
    public function findBySocialProvider(SocialProvider $provider): ?User;
    public function existsByEmail(Email $email): bool;
    public function delete(UserId $id): void;
}

// src/Domain/Repositories/PostRepositoryInterface.php
interface PostRepositoryInterface
{
    public function save(Post $post): void;
    public function findById(PostId $id): ?Post;
    public function findBySlug(PostSlug $slug, Locale $locale): ?Post;
    public function existsBySlug(PostSlug $slug, Locale $locale): bool;
    public function findPublished(int $page, int $limit): PostCollection;
    public function findByAuthor(UserId $authorId): PostCollection;
    public function findScheduledPosts(): PostCollection;
    public function findByCategory(CategoryId $categoryId): PostCollection;
    public function search(string $query): PostCollection;
    public function delete(PostId $id): void;
}
```

### **CAPA DE APLICACIÓN (Application)**

#### **Casos de Uso**

```php
// src/Application/UseCases/Auth/LoginUserUseCase.php
final class LoginUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenServiceInterface $tokenService,
        private RateLimitServiceInterface $rateLimitService
    ) {}

    public function execute(LoginCommand $command): LoginResult
    {
        // Rate limiting
        $this->rateLimitService->checkLimit(
            'login:' . $command->ipAddress,
            5,
            60
        );

        // Buscar usuario
        $user = $this->userRepository->findByEmail($command->email);
        if (!$user) {
            $this->rateLimitService->recordFailure('login:' . $command->ipAddress);
            throw new InvalidCredentialsException();
        }

        // Verificar credenciales
        if (!$user->authenticate($command->password)) {
            $this->rateLimitService->recordFailure('login:' . $command->ipAddress);
            throw new InvalidCredentialsException();
        }

        // Verificar que esté activo
        if (!$user->isActive()) {
            throw new UserInactiveException();
        }

        // Registrar login
        $user->recordLogin();
        $this->userRepository->save($user);

        // Generar token
        $token = $this->tokenService->generateToken($user->getId());

        return new LoginResult($token, $user->getId());
    }
}

// src/Application/UseCases/Post/CreatePostUseCase.php
final class CreatePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private UserRepositoryInterface $userRepository,
        private PostDomainService $postDomainService
    ) {}

    public function execute(CreatePostCommand $command): PostId
    {
        // Verificar autorización
        $author = $this->userRepository->findById($command->authorId);
        if (!$author) {
            throw new AuthorNotFoundException($command->authorId);
        }

        if (!$author->getRole()->canCreatePosts()) {
            throw new InsufficientPermissionsException();
        }

        // Generar slug único
        $slug = $this->postDomainService->generateUniqueSlug(
            $command->title,
            $command->locale,
            $this->postRepository
        );

        // Crear post
        $post = new Post(
            PostId::generate(),
            $command->authorId,
            $command->title,
            $command->content,
            $slug
        );

        // Agregar metadatos si existen
        if ($command->metadata) {
            $post->setMetadata($command->metadata);
        }

        // Guardar
        $this->postRepository->save($post);

        return $post->getId();
    }
}
```

#### **Commands y Queries**

```php
// src/Application/Commands/LoginCommand.php
final class LoginCommand
{
    public function __construct(
        public readonly Email $email,
        public readonly Password $password,
        public readonly string $ipAddress
    ) {}
}

// src/Application/Commands/CreatePostCommand.php
final class CreatePostCommand
{
    public function __construct(
        public readonly UserId $authorId,
        public readonly PostTitle $title,
        public readonly PostContent $content,
        public readonly Locale $locale,
        public readonly ?PostMetadata $metadata = null
    ) {}
}

// src/Application/Queries/GetPostQuery.php
final class GetPostQuery
{
    public function __construct(
        public readonly PostSlug $slug,
        public readonly Locale $locale
    ) {}
}
```

### **CAPA DE INFRAESTRUCTURA (Infrastructure)**

#### **Repositorios Concretos**

```php
// src/Infrastructure/Persistence/Eloquent/EloquentUserRepository.php
final class EloquentUserRepository implements UserRepositoryInterface
{
    public function save(User $user): void
    {
        $eloquentModel = $this->findEloquentModel($user->getId()) ?? new UserEloquentModel();
        
        $eloquentModel->fill([
            'id' => $user->getId()->getValue(),
            'name' => $user->getName()->getValue(),
            'email' => $user->getEmail()->getValue(),
            'password' => $user->getPassword()->getHash(),
            'role' => $user->getRole()->getValue(),
            'status' => $user->getStatus()->getValue(),
            'last_login_at' => $user->getLastLoginAt()?->format('Y-m-d H:i:s')
        ]);

        $eloquentModel->save();
    }

    public function findById(UserId $id): ?User
    {
        $eloquentModel = UserEloquentModel::find($id->getValue());
        return $eloquentModel ? $this->toDomainEntity($eloquentModel) : null;
    }

    public function findByEmail(Email $email): ?User
    {
        $eloquentModel = UserEloquentModel::where('email', $email->getValue())->first();
        return $eloquentModel ? $this->toDomainEntity($eloquentModel) : null;
    }

    public function existsByEmail(Email $email): bool
    {
        return UserEloquentModel::where('email', $email->getValue())->exists();
    }

    private function toDomainEntity(UserEloquentModel $model): User
    {
        $user = new User(
            new UserId($model->id),
            new UserName($model->name),
            new Email($model->email),
            Password::fromHash($model->password),
            UserRole::fromString($model->role)
        );

        if ($model->last_login_at) {
            $user->setLastLoginAt(new DateTimeImmutable($model->last_login_at));
        }

        return $user;
    }

    private function findEloquentModel(UserId $id): ?UserEloquentModel
    {
        return UserEloquentModel::find($id->getValue());
    }
}
```

#### **Servicios de Infraestructura**

```php
// src/Infrastructure/Services/JWTTokenService.php
final class JWTTokenService implements TokenServiceInterface
{
    public function __construct(
        private string $secretKey,
        private int $expirationTime = 3600
    ) {}

    public function generateToken(UserId $userId): AccessToken
    {
        $payload = [
            'user_id' => $userId->getValue(),
            'exp' => time() + $this->expirationTime,
            'iat' => time()
        ];

        $token = JWT::encode($payload, $this->secretKey, 'HS256');
        return new AccessToken($token);
    }

    public function validateToken(AccessToken $token): UserId
    {
        try {
            $decoded = JWT::decode($token->getValue(), new Key($this->secretKey, 'HS256'));
            return new UserId($decoded->user_id);
        } catch (Exception $e) {
            throw new InvalidTokenException();
        }
    }
}
```

### **CAPA DE INTERFAZ (Interface)**

#### **Controllers HTTP**

```php
// src/Interface/Http/Controllers/AuthController.php
final class AuthController extends Controller
{
    public function __construct(
        private LoginUserUseCase $loginUseCase,
        private RegisterUserUseCase $registerUseCase
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $command = new LoginCommand(
                new Email($request->input('email')),
                Password::fromPlainText($request->input('password')),
                $request->ip()
            );

            $result = $this->loginUseCase->execute($command);

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $result->token->getValue(),
                    'user_id' => $result->userId->getValue()
                ]
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $command = new RegisterUserCommand(
                new UserName($request->input('name')),
                new Email($request->input('email')),
                Password::fromPlainText($request->input('password'))
            );

            $userId = $this->registerUseCase->execute($command);

            return response()->json([
                'success' => true,
                'data' => ['user_id' => $userId->getValue()]
            ], 201);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
```

---

## FRONTEND - CLEAN ARCHITECTURE

### **ESTRUCTURA DE DIRECTORIOS FRONTEND**

```
frontend/
├── domain/                 # Lógica de negocio del cliente
│   ├── entities/          # Entidades del dominio cliente
│   ├── repositories/      # Interfaces de repositorio
│   ├── services/          # Servicios de dominio
│   └── value-objects/     # Objetos de valor
├── application/           # Casos de uso del cliente
│   ├── use-cases/         # Casos de uso específicos
│   ├── commands/          # Comandos del cliente
│   ├── queries/           # Consultas del cliente
│   └── services/          # Servicios de aplicación
├── infrastructure/        # Implementaciones concretas
│   ├── api/              # Clients HTTP
│   ├── storage/          # LocalStorage, SessionStorage
│   ├── external/         # APIs terceros (Analytics, etc)
│   └── repositories/     # Repositorios concretos
├── interface/             # Capa de presentación
│   ├── components/       # Componentes Vue
│   ├── pages/           # Páginas de Nuxt
│   ├── layouts/         # Layouts
│   ├── stores/          # Pinia stores
│   ├── composables/     # Lógica reutilizable
│   └── middleware/      # Middleware de Nuxt
└── shared/               # Utilidades compartidas
    ├── constants/        # Constantes
    ├── types/           # Tipos TypeScript
    └── utils/           # Funciones utilitarias
```

### **CAPA DE DOMINIO FRONTEND**

#### **Entidades de Dominio Cliente**

```typescript
// domain/entities/User.ts
export class User {
    private constructor(
        private readonly _id: UserId,
        private readonly _name: string,
        private readonly _email: string,
        private readonly _role: UserRole,
        private readonly _avatar?: string
    ) {}

    static create(data: {
        id: string;
        name: string;
        email: string;
        role: string;
        avatar?: string;
    }): User {
        return new User(
            new UserId(data.id),
            data.name,
            data.email,
            UserRole.fromString(data.role),
            data.avatar
        );
    }

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

    canCreatePosts(): boolean {
        return this._role.canCreatePosts();
    }

    canModeratePosts(): boolean {
        return this._role.canModeratePosts();
    }

    isAdmin(): boolean {
        return this._role.isAdmin();
    }
}

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
        private readonly _publishedAt?: Date,
        private readonly _viewCount: number = 0
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
            data.publishedAt ? new Date(data.publishedAt) : undefined,
            data.viewCount
        );
    }

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

    get publishedAt(): Date | undefined {
        return this._publishedAt;
    }

    get viewCount(): number {
        return this._viewCount;
    }

    isPublished(): boolean {
        return this._status.isPublished();
    }

    isDraft(): boolean {
        return this._status.isDraft();
    }

    getUrl(locale: string = 'es'): string {
        if (!this.publishedAt) return '';
        
        const year = this.publishedAt.getFullYear();
        const month = String(this.publishedAt.getMonth() + 1).padStart(2, '0');
        
        return `/${locale}/${year}/${month}/${this._slug}`;
    }
}
```

#### **Value Objects Frontend**

```typescript
// domain/value-objects/UserId.ts
export class UserId {
    constructor(public readonly value: string) {
        if (!value || value.trim() === '') {
            throw new Error('UserId cannot be empty');
        }
    }

    equals(other: UserId): boolean {
        return this.value === other.value;
    }
}

// domain/value-objects/PostStatus.ts
export class PostStatus {
    private static readonly DRAFT = 'draft';
    private static readonly PUBLISHED = 'published';
    private static readonly ARCHIVED = 'archived';
    private static readonly SCHEDULED = 'scheduled';

    private constructor(public readonly value: string) {}

    static draft(): PostStatus {
        return new PostStatus(PostStatus.DRAFT);
    }

    static published(): PostStatus {
        return new PostStatus(PostStatus.PUBLISHED);
    }

    static archived(): PostStatus {
        return new PostStatus(PostStatus.ARCHIVED);
    }

    static scheduled(): PostStatus {
        return new PostStatus(PostStatus.SCHEDULED);
    }

    static fromString(status: string): PostStatus {
        return new PostStatus(status);
    }

    isDraft(): boolean {
        return this.value === PostStatus.DRAFT;
    }

    isPublished(): boolean {
        return this.value === PostStatus.PUBLISHED;
    }

    isArchived(): boolean {
        return this.value === PostStatus.ARCHIVED;
    }

    isScheduled(): boolean {
        return this.value === PostStatus.SCHEDULED;
    }
}
```

#### **Interfaces de Repositorio Frontend**

```typescript
// domain/repositories/UserRepositoryInterface.ts
export interface UserRepositoryInterface {
    login(email: string, password: string): Promise<LoginResult>;
    register(userData: RegisterData): Promise<User>;
    getCurrentUser(): Promise<User | null>;
    logout(): Promise<void>;
    updateProfile(userData: Partial<UserData>): Promise<User>;
}

// domain/repositories/PostRepositoryInterface.ts
export interface PostRepositoryInterface {
    findAll(params?: PostQueryParams): Promise<PostCollection>;
    findBySlug(slug: string, locale: string): Promise<Post | null>;
    create(postData: CreatePostData): Promise<Post>;
    update(id: string, postData: UpdatePostData): Promise<Post>;
    delete(id: string): Promise<void>;
    publish(id: string): Promise<Post>;
    findByCategory(categorySlug: string): Promise<PostCollection>;
    search(query: string): Promise<PostCollection>;
}
```

### **CAPA DE APLICACIÓN FRONTEND**

#### **Casos de Uso Frontend**

```typescript
// application/use-cases/auth/LoginUseCase.ts
export class LoginUseCase {
    constructor(
        private userRepository: UserRepositoryInterface,
        private tokenStorage: TokenStorageInterface
    ) {}

    async execute(email: string, password: string): Promise<User> {
        try {
            const result = await this.userRepository.login(email, password);
            
            // Guardar token
            await this.tokenStorage.save(result.token);
            
            return result.user;
        } catch (error) {
            if (error instanceof InvalidCredentialsError) {
                throw new Error('Credenciales inválidas');
            }
            throw new Error('Error al iniciar sesión');
        }
    }
}

// application/use-cases/posts/GetPostUseCase.ts
export class GetPostUseCase {
    constructor(
        private postRepository: PostRepositoryInterface
    ) {}

    async execute(slug: string, locale: string): Promise<Post> {
        const post = await this.postRepository.findBySlug(slug, locale);
        
        if (!post) {
            throw new PostNotFoundError(slug);
        }

        return post;
    }
}

// application/use-cases/posts/CreatePostUseCase.ts
export class CreatePostUseCase {
    constructor(
        private postRepository: PostRepositoryInterface,
        private userRepository: UserRepositoryInterface
    ) {}

    async execute(postData: CreatePostData): Promise<Post> {
        // Verificar permisos
        const currentUser = await this.userRepository.getCurrentUser();
        if (!currentUser || !currentUser.canCreatePosts()) {
            throw new InsufficientPermissionsError();
        }

        // Crear el post
        return await this.postRepository.create(postData);
    }
}
```
### **CAPA DE INFRAESTRUCTURA FRONTEND**

#### **Repositorios Concretos Frontend**

```typescript
// infrastructure/repositories/HttpUserRepository.ts
export class HttpUserRepository implements UserRepositoryInterface {
    constructor(
        private httpClient: HttpClientInterface,
        private tokenStorage: TokenStorageInterface
    ) {}

    async login(email: string, password: string): Promise<LoginResult> {
        const response = await this.httpClient.post<LoginResponse>('/auth/login', {
            email,
            password
        });

        return {
            token: response.data.token,
            user: User.create(response.data.user)
        };
    }

    async register(userData: RegisterData): Promise<User> {
        const response = await this.httpClient.post<UserResponse>('/auth/register', userData);
        return User.create(response.data);
    }

    async getCurrentUser(): Promise<User | null> {
        try {
            const token = await this.tokenStorage.get();
            if (!token) return null;

            const response = await this.httpClient.get<UserResponse>('/auth/me');
            return User.create(response.data);
        } catch (error) {
            return null;
        }
    }

    async logout(): Promise<void> {
        await this.httpClient.post('/auth/logout');
        await this.tokenStorage.clear();
    }

    async updateProfile(userData: Partial<UserData>): Promise<User> {
        const response = await this.httpClient.put<UserResponse>('/auth/profile', userData);
        return User.create(response.data);
    }
}

// infrastructure/repositories/HttpPostRepository.ts
export class HttpPostRepository implements PostRepositoryInterface {
    constructor(private httpClient: HttpClientInterface) {}

    async findAll(params?: PostQueryParams): Promise<PostCollection> {
        const response = await this.httpClient.get<PostCollectionResponse>('/posts', { params });
        
        return {
            posts: response.data.map(postData => Post.create(postData)),
            totalCount: response.meta.total,
            currentPage: response.meta.current_page,
            totalPages: response.meta.last_page
        };
    }

    async findBySlug(slug: string, locale: string): Promise<Post | null> {
        try {
            const response = await this.httpClient.get<PostResponse>(`/posts/${slug}`, {
                params: { locale }
            });
            return Post.create(response.data);
        } catch (error) {
            if (error.status === 404) return null;
            throw error;
        }
    }

    async create(postData: CreatePostData): Promise<Post> {
        const response = await this.httpClient.post<PostResponse>('/admin/posts', postData);
        return Post.create(response.data);
    }

    async update(id: string, postData: UpdatePostData): Promise<Post> {
        const response = await this.httpClient.put<PostResponse>(`/admin/posts/${id}`, postData);
        return Post.create(response.data);
    }

    async delete(id: string): Promise<void> {
        await this.httpClient.delete(`/admin/posts/${id}`);
    }

    async publish(id: string): Promise<Post> {
        const response = await this.httpClient.post<PostResponse>(`/admin/posts/${id}/publish`);
        return Post.create(response.data);
    }

    async search(query: string): Promise<PostCollection> {
        const response = await this.httpClient.get<PostCollectionResponse>('/posts/search', {
            params: { q: query }
        });
        
        return {
            posts: response.data.map(postData => Post.create(postData)),
            totalCount: response.meta.total,
            currentPage: response.meta.current_page,
            totalPages: response.meta.last_page
        };
    }
}
```

#### **Servicios de Infraestructura Frontend**

```typescript
// infrastructure/services/HttpClient.ts
export class HttpClient implements HttpClientInterface {
    constructor(
        private baseURL: string,
        private tokenStorage: TokenStorageInterface
    ) {}

    async get<T>(url: string, config?: RequestConfig): Promise<ApiResponse<T>> {
        return this.request<T>('GET', url, undefined, config);
    }

    async post<T>(url: string, data?: any, config?: RequestConfig): Promise<ApiResponse<T>> {
        return this.request<T>('POST', url, data, config);
    }

    async put<T>(url: string, data?: any, config?: RequestConfig): Promise<ApiResponse<T>> {
        return this.request<T>('PUT', url, data, config);
    }

    async delete<T>(url: string, config?: RequestConfig): Promise<ApiResponse<T>> {
        return this.request<T>('DELETE', url, undefined, config);
    }

    private async request<T>(
        method: string,
        url: string,
        data?: any,
        config?: RequestConfig
    ): Promise<ApiResponse<T>> {
        const token = await this.tokenStorage.get();
        
        const headers: Record<string, string> = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...config?.headers
        };

        if (token) {
            headers.Authorization = `Bearer ${token}`;
        }

        const response = await fetch(`${this.baseURL}${url}`, {
            method,
            headers,
            body: data ? JSON.stringify(data) : undefined,
            ...config
        });

        if (!response.ok) {
            throw new HttpError(response.status, await response.text());
        }

        return await response.json();
    }
}

// infrastructure/storage/LocalTokenStorage.ts
export class LocalTokenStorage implements TokenStorageInterface {
    private readonly TOKEN_KEY = 'auth_token';

    async save(token: string): Promise<void> {
        localStorage.setItem(this.TOKEN_KEY, token);
    }

    async get(): Promise<string | null> {
        return localStorage.getItem(this.TOKEN_KEY);
    }

    async clear(): Promise<void> {
        localStorage.removeItem(this.TOKEN_KEY);
    }

    async exists(): Promise<boolean> {
        return localStorage.getItem(this.TOKEN_KEY) !== null;
    }
}
```

### **CAPA DE INTERFAZ FRONTEND**

#### **Stores (Pinia)**

```typescript
// interface/stores/auth.ts
export const useAuthStore = defineStore('auth', () => {
    // State
    const user = ref<User | null>(null);
    const isAuthenticated = computed(() => user.value !== null);
    const isLoading = ref(false);

    // Use cases
    const loginUseCase = new LoginUseCase(
        container.get<UserRepositoryInterface>('UserRepository'),
        container.get<TokenStorageInterface>('TokenStorage')
    );

    const getCurrentUserUseCase = new GetCurrentUserUseCase(
        container.get<UserRepositoryInterface>('UserRepository')
    );

    const logoutUseCase = new LogoutUseCase(
        container.get<UserRepositoryInterface>('UserRepository')
    );

    // Actions
    const login = async (email: string, password: string) => {
        isLoading.value = true;
        try {
            user.value = await loginUseCase.execute(email, password);
        } finally {
            isLoading.value = false;
        }
    };

    const logout = async () => {
        await logoutUseCase.execute();
        user.value = null;
    };

    const loadCurrentUser = async () => {
        try {
            user.value = await getCurrentUserUseCase.execute();
        } catch (error) {
            user.value = null;
        }
    };

    const canCreatePosts = computed(() => {
        return user.value?.canCreatePosts() ?? false;
    });

    const isAdmin = computed(() => {
        return user.value?.isAdmin() ?? false;
    });

    return {
        user: readonly(user),
        isAuthenticated,
        isLoading: readonly(isLoading),
        canCreatePosts,
        isAdmin,
        login,
        logout,
        loadCurrentUser
    };
});

// interface/stores/posts.ts
export const usePostsStore = defineStore('posts', () => {
    // State
    const posts = ref<Post[]>([]);
    const currentPost = ref<Post | null>(null);
    const isLoading = ref(false);
    const totalCount = ref(0);
    const currentPage = ref(1);

    // Use cases
    const getPostsUseCase = new GetPostsUseCase(
        container.get<PostRepositoryInterface>('PostRepository')
    );

    const getPostUseCase = new GetPostUseCase(
        container.get<PostRepositoryInterface>('PostRepository')
    );

    const createPostUseCase = new CreatePostUseCase(
        container.get<PostRepositoryInterface>('PostRepository'),
        container.get<UserRepositoryInterface>('UserRepository')
    );

    // Actions
    const loadPosts = async (params?: PostQueryParams) => {
        isLoading.value = true;
        try {
            const result = await getPostsUseCase.execute(params);
            posts.value = result.posts;
            totalCount.value = result.totalCount;
            currentPage.value = result.currentPage;
        } finally {
            isLoading.value = false;
        }
    };

    const loadPost = async (slug: string, locale: string) => {
        isLoading.value = true;
        try {
            currentPost.value = await getPostUseCase.execute(slug, locale);
        } finally {
            isLoading.value = false;
        }
    };

    const createPost = async (postData: CreatePostData) => {
        const newPost = await createPostUseCase.execute(postData);
        posts.value.unshift(newPost);
        return newPost;
    };

    const clearCurrentPost = () => {
        currentPost.value = null;
    };

    return {
        posts: readonly(posts),
        currentPost: readonly(currentPost),
        isLoading: readonly(isLoading),
        totalCount: readonly(totalCount),
        currentPage: readonly(currentPage),
        loadPosts,
        loadPost,
        createPost,
        clearCurrentPost
    };
});
```

#### **Composables**

```typescript
// interface/composables/useAuth.ts
export const useAuth = () => {
    const authStore = useAuthStore();

    const login = async (credentials: LoginCredentials) => {
        try {
            await authStore.login(credentials.email, credentials.password);
            await navigateTo('/admin');
        } catch (error) {
            throw error;
        }
    };

    const logout = async () => {
        await authStore.logout();
        await navigateTo('/');
    };

    const requireAuth = () => {
        if (!authStore.isAuthenticated) {
            throw createError({
                statusCode: 401,
                statusMessage: 'Unauthorized'
            });
        }
    };

    const requirePermission = (permission: string) => {
        requireAuth();
        
        const user = authStore.user;
        if (!user?.hasPermission(permission)) {
            throw createError({
                statusCode: 403,
                statusMessage: 'Forbidden'
            });
        }
    };

    return {
        user: authStore.user,
        isAuthenticated: authStore.isAuthenticated,
        isLoading: authStore.isLoading,
        canCreatePosts: authStore.canCreatePosts,
        isAdmin: authStore.isAdmin,
        login,
        logout,
        requireAuth,
        requirePermission
    };
};

// interface/composables/usePosts.ts
export const usePosts = () => {
    const postsStore = usePostsStore();

    const loadPosts = async (filters?: PostFilters) => {
        const params = {
            page: filters?.page || 1,
            limit: filters?.limit || 12,
            category: filters?.category,
            tag: filters?.tag,
            status: filters?.status
        };

        await postsStore.loadPosts(params);
    };

    const loadPost = async (slug: string) => {
        const { $i18n } = useNuxtApp();
        await postsStore.loadPost(slug, $i18n.locale.value);
    };

    const createPost = async (postData: CreatePostFormData) => {
        const { $i18n } = useNuxtApp();
        
        const createData: CreatePostData = {
            title: postData.title,
            content: postData.content,
            locale: $i18n.locale.value,
            metadata: {
                metaDescription: postData.metaDescription,
                excerpt: postData.excerpt
            }
        };

        return await postsStore.createPost(createData);
    };

    return {
        posts: postsStore.posts,
        currentPost: postsStore.currentPost,
        isLoading: postsStore.isLoading,
        totalCount: postsStore.totalCount,
        currentPage: postsStore.currentPage,
        loadPosts,
        loadPost,
        createPost,
        clearCurrentPost: postsStore.clearCurrentPost
    };
};
```

#### **Componentes Vue**

```vue
<!-- interface/components/Post/PostCard.vue -->
<template>
  <article class="post-card">
    <div class="post-card__image" v-if="post.featuredImage">
      <NuxtImg
        :src="post.featuredImage"
        :alt="post.title"
        class="post-card__img"
      />
    </div>
    
    <div class="post-card__content">
      <header class="post-card__header">
        <h2 class="post-card__title">
          <NuxtLink :to="post.getUrl(locale)" class="post-card__link">
            {{ post.title }}
          </NuxtLink>
        </h2>
        
        <div class="post-card__meta">
          <time class="post-card__date" :datetime="post.publishedAt?.toISOString()">
            {{ formatDate(post.publishedAt) }}
          </time>
          <span class="post-card__reading-time">
            {{ $t('posts.readingTime', { minutes: post.readingTime }) }}
          </span>
        </div>
      </header>
      
      <div class="post-card__excerpt">
        {{ post.excerpt }}
      </div>
      
      <footer class="post-card__footer">
        <div class="post-card__stats">
          <span class="post-card__views">
            {{ $t('posts.views', { count: post.viewCount }) }}
          </span>
        </div>
        
        <NuxtLink :to="post.getUrl(locale)" class="post-card__read-more">
          {{ $t('posts.readMore') }}
        </NuxtLink>
      </footer>
    </div>
  </article>
</template>

<script setup lang="ts">
import type { Post } from '~/domain/entities/Post';

interface Props {
  post: Post;
}

defineProps<Props>();

const { locale } = useI18n();

const formatDate = (date: Date | undefined) => {
  if (!date) return '';
  
  return new Intl.DateTimeFormat(locale.value, {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }).format(date);
};
</script>
```

#### **Páginas**

```vue
<!-- interface/pages/[...slug].vue -->
<template>
  <div class="post-page">
    <LoadingSpinner v-if="isLoading" />
    
    <article v-else-if="currentPost" class="post">
      <PostHeader :post="currentPost" />
      <PostContent :post="currentPost" />
      <PostFooter :post="currentPost" />
      <PostComments :post="currentPost" />
    </article>
    
    <ErrorMessage v-else :message="$t('posts.notFound')" />
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'blog'
});

const route = useRoute();
const { currentPost, isLoading, loadPost, clearCurrentPost } = usePosts();

const slug = computed(() => {
  const segments = route.params.slug as string[];
  return segments[segments.length - 1];
});

// Load post on mount
await loadPost(slug.value);

// SEO
useSeoMeta({
  title: () => currentPost.value?.title,
  description: () => currentPost.value?.excerpt,
  ogTitle: () => currentPost.value?.title,
  ogDescription: () => currentPost.value?.excerpt,
  ogImage: () => currentPost.value?.featuredImage,
  twitterCard: 'summary_large_image'
});

// Cleanup on unmount
onUnmounted(() => {
  clearCurrentPost();
});
</script>
```

### **DEPENDENCY INJECTION**

#### **Interface del Container (abstracción)**

```typescript
// shared/container/ContainerInterface.ts
export interface ContainerInterface {
    bind<T>(key: string, factory: () => T): void;
    singleton<T>(key: string, factory: () => T): void;
    get<T>(key: string): T;
    has(key: string): boolean;
}
```

#### **Container de Servicios**

##### **1. Interface del Container (abstracción)**

```typescript
// shared/container/ContainerInterface.ts
export interface ContainerInterface {
    bind<T>(key: string, factory: () => T): void;
    singleton<T>(key: string, factory: () => T): void;
    get<T>(key: string): T;
    has(key: string): boolean;
}
```

##### **2. Implementación Simple**

```typescript
// shared/container/SimpleContainer.ts
export class SimpleContainer implements ContainerInterface {
    private services = new Map<string, () => any>();
    private singletons = new Map<string, any>();
    private isSingleton = new Set<string>();

    bind<T>(key: string, factory: () => T): void {
        this.services.set(key, factory);
    }

    singleton<T>(key: string, factory: () => T): void {
        this.services.set(key, factory);
        this.isSingleton.add(key);
        this.singletons.set(key, null); // placeholder
    }

    get<T>(key: string): T {
        if (!this.services.has(key)) {
            throw new Error(`Service '${key}' not found in container`);
        }

        // Si es singleton y ya fue creado, devolverlo
        if (this.isSingleton.has(key)) {
            let instance = this.singletons.get(key);
            if (instance === null) {
                // Primera vez, crear la instancia
                instance = this.services.get(key)!();
                this.singletons.set(key, instance);
            }
            return instance;
        }

        // No es singleton, crear nueva instancia siempre
        return this.services.get(key)!();
    }

    has(key: string): boolean {
        return this.services.has(key);
    }
}
```

##### **3. Configuración del Container**

```typescript
// shared/container/container.ts
import type { ContainerInterface } from './ContainerInterface';
import { SimpleContainer } from './SimpleContainer';

// Instance global
let containerInstance: ContainerInterface | null = null;

export const createContainer = (): ContainerInterface => {
    return new SimpleContainer();
};

export const getContainer = (): ContainerInterface => {
    if (!containerInstance) {
        throw new Error('Container not initialized. Call setupContainer first.');
    }
    return containerInstance;
};

export const setupContainer = (container: ContainerInterface): void => {
    containerInstance = container;
};

// Helper para no escribir tanto
export const container = () => getContainer();
```

##### **4. Registro de Servicios**

```typescript
// shared/container/bindings.ts
import type { ContainerInterface } from './ContainerInterface';
import { HttpClient } from '~/infrastructure/api/HttpClient';
import { LocalTokenStorage } from '~/infrastructure/storage/LocalTokenStorage';
import { HttpUserRepository } from '~/infrastructure/repositories/HttpUserRepository';
import { HttpPostRepository } from '~/infrastructure/repositories/HttpPostRepository';

export const registerServices = (container: ContainerInterface, config: any) => {
    // Storage (singleton)
    container.singleton('TokenStorage', () => new LocalTokenStorage());

    // HTTP Client (singleton)
    container.singleton('HttpClient', () => 
        new HttpClient(
            config.public.apiBaseUrl,
            container.get('TokenStorage')
        )
    );

    // Repositories (singleton)
    container.singleton('UserRepository', () =>
        new HttpUserRepository(
            container.get('HttpClient'),
            container.get('TokenStorage')
        )
    );

    container.singleton('PostRepository', () =>
        new HttpPostRepository(container.get('HttpClient'))
    );

    container.singleton('CommentRepository', () =>
        new HttpCommentRepository(container.get('HttpClient'))
    );

    container.singleton('NewsletterRepository', () =>
        new HttpNewsletterRepository(container.get('HttpClient'))
    );

    // Use Cases (no necesariamente singleton, depende del caso)
    container.bind('LoginUseCase', () =>
        new LoginUseCase(
            container.get('UserRepository'),
            container.get('TokenStorage')
        )
    );

    container.bind('CreatePostUseCase', () =>
        new CreatePostUseCase(
            container.get('PostRepository'),
            container.get('UserRepository')
        )
    );

    container.bind('GetPostUseCase', () =>
        new GetPostUseCase(container.get('PostRepository'))
    );
};
```

##### **5. Plugin de Nuxt**

```typescript
// plugins/container.client.ts
import { createContainer, setupContainer } from '~/shared/container/container';
import { registerServices } from '~/shared/container/bindings';

export default defineNuxtPlugin(() => {
    const config = useRuntimeConfig();
    
    // Crear container
    const container = createContainer();
    
    // Registrar servicios
    registerServices(container, config);
    
    // Configurar como instancia global
    setupContainer(container);
    
    return {
        provide: {
            container: () => container
        }
    };
});
```

##### **6. CÓMO RECUPERAR LOS SERVICIOS**

###### **Opción A: En Composables**

```typescript
// interface/composables/useAuth.ts
import { container } from '~/shared/container/container';
import type { LoginUseCase } from '~/application/use-cases/auth/LoginUseCase';
import type { UserRepositoryInterface } from '~/domain/repositories/UserRepositoryInterface';

export const useAuth = () => {
    const authStore = useAuthStore();

    const login = async (credentials: LoginCredentials) => {
        const loginUseCase = container().get<LoginUseCase>('LoginUseCase');
        
        try {
            await loginUseCase.execute(credentials.email, credentials.password);
            await navigateTo('/admin');
        } catch (error) {
            throw error;
        }
    };

    const getCurrentUser = async () => {
        const userRepository = container().get<UserRepositoryInterface>('UserRepository');
        return await userRepository.getCurrentUser();
    };

    return {
        user: authStore.user,
        isAuthenticated: authStore.isAuthenticated,
        login,
        getCurrentUser
    };
};
```

###### **Opción B: En Stores**

```typescript
// interface/stores/auth.ts
import { container } from '~/shared/container/container';
import type { LoginUseCase } from '~/application/use-cases/auth/LoginUseCase';

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null);
    const isLoading = ref(false);

    const login = async (email: string, password: string) => {
        isLoading.value = true;
        try {
            const loginUseCase = container().get<LoginUseCase>('LoginUseCase');
            user.value = await loginUseCase.execute(email, password);
        } finally {
            isLoading.value = false;
        }
    };

    return {
        user: readonly(user),
        isLoading: readonly(isLoading),
        login
    };
});
```

###### **Opción C: En Componentes**

```vue
<!-- interface/components/LoginForm.vue -->
<script setup lang="ts">
import { container } from '~/shared/container/container';
import type { LoginUseCase } from '~/application/use-cases/auth/LoginUseCase';

const login = async (formData: LoginFormData) => {
    const loginUseCase = container().get<LoginUseCase>('LoginUseCase');
    
    try {
        await loginUseCase.execute(formData.email, formData.password);
        await navigateTo('/dashboard');
    } catch (error) {
        // manejar error
    }
};
</script>
```

##### **7. Type Safety**

```typescript
// shared/container/ServiceTypes.ts
export interface ServiceMap {
    'TokenStorage': TokenStorageInterface;
    'HttpClient': HttpClientInterface;
    'UserRepository': UserRepositoryInterface;
    'PostRepository': PostRepositoryInterface;
    'LoginUseCase': LoginUseCase;
    'CreatePostUseCase': CreatePostUseCase;
    'GetPostUseCase': GetPostUseCase;
}

// Mejorar el container con tipos
export interface ContainerInterface {
    bind<K extends keyof ServiceMap>(key: K, factory: () => ServiceMap[K]): void;
    singleton<K extends keyof ServiceMap>(key: K, factory: () => ServiceMap[K]): void;
    get<K extends keyof ServiceMap>(key: K): ServiceMap[K];
    has(key: keyof ServiceMap): boolean;
}
```

##### VENTAJAS DE ESTA IMPLEMENTACIÓN

1. **Funciona realmente** - resuelve dependencias correctamente
2. **Abstracción clara** - interface permite cambiar implementación
3. **Type safe** - con el ServiceMap tenemos autocompletado
4. **Fácil de usar** - `container().get('ServiceName')`
5. **Centralizado** - todos los servicios en un lugar
6. **Testeable** - fácil mockear para tests
7. **Migrable** - cambiar a TSyringe u otro sin cambiar código cliente

### **MIDDLEWARE DE AUTENTICACIÓN**

```typescript
// interface/middleware/auth.ts
export default defineNuxtRouteMiddleware((to) => {
    const { isAuthenticated } = useAuth();
    
    if (!isAuthenticated) {
        throw createError({
            statusCode: 401,
            statusMessage: 'Unauthorized'
        });
    }
});

// interface/middleware/admin.ts
export default defineNuxtRouteMiddleware(() => {
    const { isAdmin, requireAuth } = useAuth();
    
    requireAuth();
    
    if (!isAdmin) {
        throw createError({
            statusCode: 403,
            statusMessage: 'Forbidden'
        });
    }
});
```

## RESUMEN DE ARQUITECTURA CLEAN COMPLETA

### **BENEFICIOS CONSEGUIDOS**

1. **Separación clara de responsabilidades**
2. **Independencia de frameworks** (Laravel/Nuxt)
3. **Testabilidad completa** en todas las capas
4. **Mantenibilidad alta** con código desacoplado
5. **Escalabilidad** para futuras funcionalidades
6. **Flexibilidad** para cambiar tecnologías

### **FLUJO DE DATOS**

```
Frontend: User Interaction → Components → Composables → Stores → Use Cases → Repositories → HTTP Client → Backend API

Backend: HTTP Request → Controllers → Use Cases → Domain Services → Entities → Repositories → Database
```

### **TESTING STRATEGY**

- **Domain**: Unit tests puros
- **Application**: Unit tests con mocks
- **Infrastructure**: Integration tests
- **Interface**: Component tests + E2E