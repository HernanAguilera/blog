# Validación y Manejo de Errores - Clean Architecture

## Índice
1. [Principios Fundamentales](#principios-fundamentales)
2. [Responsabilidades por Capa](#responsabilidades-por-capa)
3. [Sistema de Errores](#sistema-de-errores)
4. [Dónde Validar Según el Tipo](#dónde-validar-según-el-tipo)
5. [Ejemplos Prácticos](#ejemplos-prácticos)
6. [Anti-patrones a Evitar](#anti-patrones-a-evitar)

---

## Principios Fundamentales

### 1. Defensa en Profundidad
- **Frontend valida:** Mejor experiencia de usuario (feedback inmediato)
- **Backend valida:** Seguridad y consistencia de datos (NUNCA confiar en el frontend)
- **Ambos son necesarios:** No son redundantes, son complementarios

### 2. Fail Fast
- Validar lo antes posible en cada capa
- Lanzar errores específicos para cada tipo de problema
- No permitir que datos inválidos se propaguen

### 3. Single Responsibility
- Cada capa tiene un tipo específico de validación
- No duplicar validaciones de negocio en múltiples capas
- Value Objects encapsulan las reglas de negocio

---

## Responsabilidades por Capa

### Domain Layer (Capa de Dominio)

**Responsabilidad:** Reglas de negocio puras

**Qué valida:**
- Formato y longitud de datos (reglas de negocio)
- Invariantes del dominio
- Consistencia de entidades

**Qué NO valida:**
- Datos técnicos (tokens, slugs, IDs externos)
- Estado de UI
- Permisos de usuario

**Errores que lanza:**
- `InvalidCommentContentError`
- `InvalidCommentIdError`
- `InvalidAnonymousAuthorError`
- Cualquier error de dominio específico

**Dónde se implementa:**
- Value Objects
- Entidades
- Domain Services

**Ejemplo:**
```typescript
// domain/value-objects/comment-content.vo.ts
export class CommentContent {
  private static readonly MIN_LENGTH = 3;
  private static readonly MAX_LENGTH = 2000;

  public static create(value: string): CommentContent {
    const sanitized = value.trim();

    if (!sanitized || sanitized.length === 0) {
      throw InvalidCommentContentError.empty();
    }

    if (sanitized.length < CommentContent.MIN_LENGTH) {
      throw InvalidCommentContentError.tooShort(
        CommentContent.MIN_LENGTH,
        sanitized.length
      );
    }

    if (sanitized.length > CommentContent.MAX_LENGTH) {
      throw InvalidCommentContentError.tooLong(
        CommentContent.MAX_LENGTH,
        sanitized.length
      );
    }

    return new CommentContent(sanitized);
  }
}
```

---

### Application Layer (Capa de Aplicación)

**Responsabilidad:** Orquestación de casos de uso

**Qué hace:**
- Coordina el flujo entre Repository y Domain
- **NO valida reglas de negocio**
- **NO valida datos del usuario**

**Qué NO hace:**
- Validaciones de formato o longitud
- Validaciones que deberían estar en Value Objects
- Validaciones que deberían estar en la UI

**Errores que lanza:**
- `InvalidInputError` (solo para validaciones técnicas muy específicas)
- En general, **NO debería lanzar errores de validación**

**Cuándo usar `InvalidInputError`:**
- Solo para validaciones técnicas/estructurales que no pertenecen al dominio
- Ejemplo: Arrays vacíos en operaciones bulk (validación técnica, no de negocio)

**Use Cases - Responsabilidad:**
Los Use Cases son **orquestadores puros**. Su único trabajo es:
1. Recibir datos
2. Llamar al Repository
3. Retornar el resultado

**Ejemplo CORRECTO:**
```typescript
// application/use-cases/comment/create-anonymous-comment.use-case.ts
export class CreateAnonymousCommentUseCase {
  constructor(private readonly commentRepository: CommentRepositoryInterface) {}

  async execute(data: CreateAnonymousCommentData): Promise<Comment> {
    // Solo orquestación, sin validaciones
    // La UI validó con VOs antes de llamar aquí
    // El backend validará de todas formas
    return await this.commentRepository.createAnonymousComment(data);
  }
}
```

**Ejemplo INCORRECTO (anti-patrón):**
```typescript
// ❌ MAL - El Use Case NO debe validar reglas de negocio
async execute(data: CreateAnonymousCommentData): Promise<Comment> {
  // ❌ No hacer esto - pertenece a la UI
  if (!data.content || data.content.length < 3) {
    throw new Error('Content too short');
  }

  // ❌ No hacer esto - se crea y se desecha (anti-patrón)
  CommentContent.create(data.content);

  return await this.commentRepository.createAnonymousComment(data);
}
```

**Excepción - Validaciones técnicas:**
```typescript
// application/use-cases/comment/bulk-approve-comments.use-case.ts
export class BulkApproveCommentsUseCase {
  async execute(commentIds: string[]): Promise<void> {
    // ✅ OK - Validación técnica/estructural
    if (commentIds.length === 0) {
      throw InvalidInputError.emptyBulkOperation();
    }

    const ids = commentIds.map((id) => CommentId.create(id));
    await this.commentRepository.bulkApprove(ids);
  }
}
```

---

### Infrastructure Layer (Capa de Infraestructura)

**Responsabilidad:** I/O (Input/Output) - Comunicación con sistemas externos

**Qué hace:**
- Llamadas HTTP al backend
- Mapeo de datos backend ↔ frontend
- **NO valida datos**

**Qué NO hace:**
- Validaciones de negocio
- Validaciones de formato
- Transformaciones de datos (más allá del mapeo)

**Errores que lanza:**
- `ApiError` (errores HTTP del servidor)
- `NetworkError` (errores de conectividad)

**Ejemplo:**
```typescript
// infrastructure/repositories/http-comment-repository.ts
export class HttpCommentRepository implements CommentRepositoryInterface {
  async createAnonymousComment(data: CreateAnonymousCommentData): Promise<Comment> {
    // Solo I/O, sin validaciones
    // La UI ya validó con VOs
    // El backend validará de todas formas
    const commentData = await this.commentAPI.createAnonymousComment(data);
    return CommentEntity.fromData(this.mapToCommentData(commentData));
  }
}
```

**Manejo de errores HTTP:**
```typescript
// infrastructure/services/http-client.ts (ejemplo)
async post<T>(url: string, data: any): Promise<T> {
  try {
    const response = await fetch(url, {
      method: 'POST',
      body: JSON.stringify(data),
      headers: { 'Content-Type': 'application/json' }
    });

    if (!response.ok) {
      if (response.status === 429) {
        throw ApiError.rateLimitExceeded();
      } else if (response.status === 401) {
        throw ApiError.unauthorized();
      } else if (response.status >= 500) {
        throw ApiError.serverError();
      } else {
        const error = await response.json();
        throw ApiError.fromResponse(response.status, error.message);
      }
    }

    return await response.json();
  } catch (error) {
    if (error instanceof ApiError) {
      throw error;
    }
    throw NetworkError.connectionFailed();
  }
}
```

---

### Presentation Layer (Capa de Presentación / UI)

**Responsabilidad:** Validación de formularios y experiencia de usuario

**Qué valida:**
- **TODOS los datos del usuario** usando Value Objects
- Formato de campos
- Datos requeridos
- Obtención de tokens (ej: Turnstile)

**Cuándo validar:**
- **ANTES** de llamar al Use Case
- En el submit del formulario
- Opcionalmente: validación en tiempo real mientras el usuario escribe

**Errores que maneja:**
- Todos los errores de Domain (`InvalidCommentContentError`, etc.)
- Errores de Application (`InvalidInputError`)
- Errores de Infrastructure (`ApiError`, `NetworkError`)

**Ejemplo:**
```typescript
// interface/composables/useCommentForm.ts
export const useCommentForm = (postSlug: string) => {
  const formData = reactive({
    content: '',
    anonymousName: '',
    anonymousEmail: '',
  });

  const formErrors = reactive({
    content: '',
    author: '',
    general: '',
  });

  const submitAnonymousComment = async () => {
    // Limpiar errores previos
    formErrors.content = '';
    formErrors.author = '';
    formErrors.general = '';

    try {
      // 1. VALIDAR contenido usando Value Object (Domain)
      const content = CommentContent.create(formData.content);

      // 2. VALIDAR autor anónimo usando Value Object (Domain)
      const author = AnonymousAuthor.create(
        formData.anonymousName,
        formData.anonymousEmail
      );

      // 3. Obtener token de Turnstile (responsabilidad de la UI)
      const turnstileToken = await getTurnstileToken();

      if (!turnstileToken) {
        formErrors.general = 'Error de verificación. Intenta de nuevo.';
        return;
      }

      // 4. Llamar Use Case con datos YA VALIDADOS
      await createAnonymousCommentUseCase.execute({
        postSlug,
        content: content.getValue(),
        anonymousName: author.getName(),
        anonymousEmail: author.getEmail(),
        turnstileToken,
        parentId: props.parentId,
      });

      // 5. Éxito
      toast.success('Comentario enviado correctamente');
      resetForm();

    } catch (error) {
      // 6. Manejo específico por tipo de error

      // Errores de Domain
      if (error instanceof InvalidCommentContentError) {
        formErrors.content = error.message;
      } else if (error instanceof InvalidAnonymousAuthorError) {
        formErrors.author = error.message;
      }

      // Errores de Application
      else if (error instanceof InvalidInputError) {
        formErrors.general = error.message;
      }

      // Errores de Infrastructure
      else if (error instanceof ApiError) {
        if (error.statusCode === 429) {
          formErrors.general = 'Has excedido el límite. Espera 20 segundos.';
        } else if (error.statusCode === 422) {
          formErrors.general = 'Datos inválidos. Revisa los campos.';
        } else {
          formErrors.general = 'Error del servidor. Intenta más tarde.';
        }
      } else if (error instanceof NetworkError) {
        formErrors.general = 'Error de conexión. Verifica tu internet.';
      }

      // Error desconocido
      else {
        formErrors.general = 'Error inesperado. Intenta de nuevo.';
        console.error('Unexpected error:', error);
      }
    }
  };

  return {
    formData,
    formErrors,
    submitAnonymousComment,
  };
};
```

---

## Sistema de Errores

### Jerarquía de Errores por Capa

```
Error (base)
│
├── Domain Errors (domain/exceptions/)
│   ├── InvalidCommentContentError
│   │   ├── .empty()
│   │   ├── .tooShort(min, actual)
│   │   └── .tooLong(max, actual)
│   │
│   ├── InvalidCommentIdError
│   │   ├── .empty()
│   │   └── .invalidFormat(value)
│   │
│   └── InvalidAnonymousAuthorError
│       ├── .nameRequired()
│       ├── .nameEmpty()
│       ├── .nameTooShort(min, actual)
│       ├── .nameTooLong(max, actual)
│       ├── .emailRequired()
│       └── .invalidEmail(email)
│
├── Application Errors (application/exceptions/)
│   └── InvalidInputError
│       ├── .postSlugRequired()
│       ├── .turnstileTokenRequired()
│       └── .emptyBulkOperation()
│
└── Infrastructure Errors (infrastructure/exceptions/)
    ├── ApiError
    │   ├── .badRequest(message?)
    │   ├── .unauthorized()
    │   ├── .forbidden()
    │   ├── .notFound()
    │   ├── .rateLimitExceeded()
    │   └── .serverError()
    │
    └── NetworkError
        ├── .connectionFailed()
        ├── .timeout()
        └── .offline()
```

### Características de los Errores

**Todos los errores tienen:**
```typescript
class CustomError extends Error {
  public readonly name: string;  // Nombre de la clase
  public readonly message: string;  // Mensaje descriptivo

  constructor(message: string) {
    super(message);
    this.name = 'CustomError';
    Object.setPrototypeOf(this, CustomError.prototype);  // Fix para instanceof
  }
}
```

**Errores de Infrastructure tienen información adicional:**
```typescript
class ApiError extends Error {
  public readonly statusCode: number;  // HTTP status code
  public readonly response?: unknown;  // Response completa del servidor
}
```

---

## Dónde Validar Según el Tipo

| Tipo de Validación | Capa | Dónde | Error que Lanza |
|-------------------|------|-------|-----------------|
| **Reglas de Negocio** | Domain | Value Objects | `Invalid*Error` (Domain) |
| Longitud de contenido | Domain | CommentContent | `InvalidCommentContentError` |
| Formato de email | Domain | AnonymousAuthor | `InvalidAnonymousAuthorError` |
| Formato de UUID | Domain | CommentId | `InvalidCommentIdError` |
| **Validaciones Técnicas** | Application | Use Cases (mínimo) | `InvalidInputError` |
| Array vacío en bulk | Application | BulkUseCase | `InvalidInputError` |
| **Formularios** | Presentation | UI Components | Captura todos los errores |
| Validar antes de submit | Presentation | useCommentForm | Captura Domain errors |
| Obtener token Turnstile | Presentation | CommentForm | Maneja fallas |
| **I/O y Comunicación** | Infrastructure | Repository/API | `ApiError`, `NetworkError` |
| Errores HTTP | Infrastructure | HttpClient | `ApiError` |
| Errores de red | Infrastructure | HttpClient | `NetworkError` |

---

## Ejemplos Prácticos

### Ejemplo 1: Crear Comentario Anónimo (Flujo Completo)

**1. UI valida y prepara datos:**
```typescript
// Presentation Layer
const submitComment = async () => {
  try {
    // Validar con VOs (lanza errores de Domain)
    const content = CommentContent.create(formData.content);
    const author = AnonymousAuthor.create(formData.name, formData.email);
    const turnstileToken = await getTurnstileToken();

    // Llamar Use Case
    await createAnonymousCommentUseCase.execute({
      content: content.getValue(),
      anonymousName: author.getName(),
      anonymousEmail: author.getEmail(),
      turnstileToken,
      postSlug,
    });
  } catch (error) {
    // Manejar errores
  }
};
```

**2. Use Case orquesta:**
```typescript
// Application Layer
async execute(data: CreateAnonymousCommentData): Promise<Comment> {
  // Solo orquestación, sin validaciones
  return await this.commentRepository.createAnonymousComment(data);
}
```

**3. Repository hace I/O:**
```typescript
// Infrastructure Layer
async createAnonymousComment(data: CreateAnonymousCommentData): Promise<Comment> {
  // Solo llamada HTTP (puede lanzar ApiError o NetworkError)
  const commentData = await this.commentAPI.createAnonymousComment(data);
  return CommentEntity.fromData(this.mapToCommentData(commentData));
}
```

**4. Backend valida de nuevo:**
```php
// Backend (Laravel) - Defensa en profundidad
public function execute(CreateAnonymousCommentDTO $dto): Comment
{
    $content = new CommentContent($dto->content);  // Valida de nuevo
    $author = new AnonymousAuthor($dto->authorName, $dto->authorEmail);  // Valida de nuevo

    // Validar token Turnstile con Cloudflare
    // Detectar spam, etc.

    return $comment;
}
```

### Ejemplo 2: Validación en Tiempo Real (Opcional)

```typescript
// Presentation Layer - Validación mientras el usuario escribe
const validateContentRealtime = (value: string) => {
  try {
    CommentContent.create(value);
    formErrors.content = '';  // Sin errores
  } catch (error) {
    if (error instanceof InvalidCommentContentError) {
      formErrors.content = error.message;  // Mostrar error en tiempo real
    }
  }
};

// En el template
<textarea
  v-model="formData.content"
  @input="validateContentRealtime(formData.content)"
/>
<span class="error">{{ formErrors.content }}</span>
```

---

## Anti-patrones a Evitar

### ❌ Anti-patrón 1: Crear VOs solo para validar y descartarlos

```typescript
// ❌ MAL - En Use Case
async execute(data: CreateAnonymousCommentData): Promise<Comment> {
  CommentContent.create(data.content);  // Se crea y se desecha
  AnonymousAuthor.create(data.anonymousName, data.anonymousEmail);  // Se crea y se desecha

  return await this.commentRepository.createAnonymousComment(data);  // Usa datos RAW
}
```

**Por qué está mal:**
- Los VOs se crean pero nunca se usan
- Es validación redundante (la UI ya debió validar)
- El backend va a validar de todas formas
- Desperdicio de recursos

**Correcto:**
```typescript
// ✅ BIEN - En UI
const content = CommentContent.create(formData.content);  // Se crea y SE USA
const author = AnonymousAuthor.create(formData.name, formData.email);  // Se crea y SE USA

await useCase.execute({
  content: content.getValue(),  // Usar el VO
  anonymousName: author.getName(),  // Usar el VO
  anonymousEmail: author.getEmail(),  // Usar el VO
});
```

### ❌ Anti-patrón 2: Validaciones de negocio en Use Cases

```typescript
// ❌ MAL
async execute(data: CreateAnonymousCommentData): Promise<Comment> {
  if (!data.content || data.content.length < 3) {
    throw new Error('Content too short');
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(data.anonymousEmail)) {
    throw new Error('Invalid email');
  }

  return await this.commentRepository.createAnonymousComment(data);
}
```

**Por qué está mal:**
- Las reglas de negocio deben estar en Value Objects (Domain)
- Duplica lógica que ya existe en VOs
- Dificulta el testing y mantenimiento

**Correcto:**
- Reglas de negocio en VOs
- UI valida con VOs antes de llamar Use Case
- Use Case solo orquesta

### ❌ Anti-patrón 3: Confiar solo en validación de backend

```typescript
// ❌ MAL - No validar en frontend
const submitComment = async () => {
  // Enviar directamente sin validar
  await createAnonymousCommentUseCase.execute(formData);
  // Esperar que el backend lance errores
};
```

**Por qué está mal:**
- Mala experiencia de usuario (round-trip al servidor para saber que hay error)
- Desperdicia recursos del servidor
- No aprovecha las capacidades del frontend

**Correcto:**
- Validar en frontend CON VOs (feedback inmediato)
- Validar en backend TAMBIÉN (seguridad)
- Defensa en profundidad

### ❌ Anti-patrón 4: Validaciones en Repository

```typescript
// ❌ MAL
async createAnonymousComment(data: CreateAnonymousCommentData): Promise<Comment> {
  // Validar en Repository
  if (data.content.length < 3) {
    throw new Error('Content too short');
  }

  const commentData = await this.commentAPI.createAnonymousComment(data);
  return CommentEntity.fromData(commentData);
}
```

**Por qué está mal:**
- Repository es Infrastructure, no debe validar reglas de negocio
- Responsabilidad incorrecta (debe ser solo I/O)
- Mezcla de concerns

**Correcto:**
- Repository solo hace I/O
- Validaciones en UI (con VOs) y Backend

---

## Resumen - Checklist

### ✅ Domain Layer
- [ ] Value Objects validan reglas de negocio
- [ ] Lanzan errores específicos de dominio (`Invalid*Error`)
- [ ] No tienen dependencias externas
- [ ] Son inmutables y reutilizables

### ✅ Application Layer (Use Cases)
- [ ] Solo orquestan (coordinan Repository)
- [ ] NO validan reglas de negocio
- [ ] NO crean VOs solo para validar
- [ ] Pueden lanzar `InvalidInputError` solo para validaciones técnicas muy específicas

### ✅ Infrastructure Layer
- [ ] Solo I/O (HTTP, Base de datos, etc.)
- [ ] NO validan datos
- [ ] Lanzan `ApiError` o `NetworkError` según el tipo de fallo
- [ ] Mapean datos backend ↔ frontend

### ✅ Presentation Layer (UI)
- [ ] Valida TODOS los datos del usuario con VOs
- [ ] Valida ANTES de llamar Use Cases
- [ ] Obtiene tokens externos (ej: Turnstile)
- [ ] Maneja y muestra TODOS los tipos de errores al usuario

### ✅ Backend
- [ ] Valida SIEMPRE (defensa en profundidad)
- [ ] Nunca confía en el frontend
- [ ] Es la fuente de verdad

---

## Conclusión

**Principio Rector:**
> Cada capa tiene su responsabilidad específica de validación. El frontend valida para mejor UX, el backend valida para seguridad. Los Use Cases solo orquestan, los Value Objects encapsulan reglas de negocio, y la UI es la primera línea de defensa.

**Regla de Oro:**
> Si estás creando un Value Object y no lo usas después, estás en el lugar equivocado. Las validaciones con VOs deben estar en la UI.
