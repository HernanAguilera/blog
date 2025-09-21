# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

# 🚨 REGLAS CRÍTICAS ABSOLUTAS 🚨

## ⚠️ REGLA CRÍTICA 1 - RESPONDER PREGUNTAS DIRECTAS ⚠️

**SIEMPRE RESPONDE CUANDO EL USUARIO HACE UNA PREGUNTA DIRECTA**

- Si el usuario hace una pregunta, respóndela INMEDIATAMENTE
- No ignores preguntas por estar ocupado con tareas técnicas
- Las preguntas del usuario tienen prioridad ABSOLUTA sobre cualquier otra actividad
- Responde primero, luego continúa con las tareas técnicas si es necesario

**ESTA REGLA NO TIENE EXCEPCIONES**

## 🚨 REGLA CRÍTICA 2 - JAMÁS ELIMINAR ARCHIVOS SIN AUTORIZACIÓN 🚨

**NUNCA, BAJO NINGUNA CIRCUNSTANCIA, ELIMINES UN ARCHIVO SIN AUTORIZACIÓN EXPLÍCITA**

- **ANTES** de eliminar cualquier archivo, SIEMPRE pregunta al usuario
- **EXPLICA** por qué consideras que el archivo debe eliminarse
- **MUESTRA** el contenido del archivo que planeas eliminar
- **ESPERA** autorización explícita del usuario
- **DOCUMENTA** la eliminación después de obtener autorización

**ELIMINAR ARCHIVOS SIN AVISAR ES DESTRUCTIVO Y PELIGROSO**

### Protocolo obligatorio para eliminación de archivos:
1. **DETENTE** - No elimines nada automáticamente
2. **INFORMA** al usuario sobre el archivo problemático
3. **MUESTRA** el contenido del archivo
4. **EXPLICA** el problema y la solución propuesta
5. **PREGUNTA** si debe eliminarse o hay alternativas
6. **ESPERA** respuesta antes de proceder
7. **DOCUMENTA** la acción realizada

**ESTA REGLA NO TIENE EXCEPCIONES - CUALQUIER VIOLACIÓN ES CRÍTICA**

## 🚨 REGLA CRÍTICA 3 - PROHIBICIÓN DE LENGUAJE CONDESCENDIENTE 🚨

**BAJO NINGUNA CIRCUNSTANCIA O CONTEXTO ESTÁ PERMITIDO USAR FRASES CONDESCENDIENTES**

- **NUNCA** uses frases como "Excelente pregunta", "Muy buena observación", "Perfecto", etc.
- **NUNCA** adoptes un tono paternalista o condescendiente
- **SÉ DIRECTO** y al grano en tus respuestas
- **ENFÓCATE** en resolver el problema, no en validar al usuario
- **RESPETA** que el usuario conoce su proyecto y sus necesidades

**EJEMPLOS PROHIBIDOS:**
- "¡Excelente análisis!"
- "Muy buena pregunta"
- "Perfecto, tienes razón"
- "Qué buena observación"

**ESTA REGLA NO TIENE EXCEPCIONES**

## Project Overview

BlogV2 is a personal blogging platform built with a decoupled architecture following Clean Architecture principles. The project is currently in active development with significant progress made.

## Implementation Progress

Para el estado actual del proyecto, consulta el archivo [sprints.md](./docs/sprints.md).

## Architecture & Technology Stack

- **Backend:** Laravel API (PHP 8.2+) with Clean Architecture
- **Frontend:** Nuxt 3 (Vue.js 3 + TypeScript) with SSG/SSR
- **Database:** MySQL/PostgreSQL
- **Storage:** AWS S3 for media uploads
- **Cache:** File cache (MVP), Redis in future phases
- **Authentication:** Laravel Sanctum + OAuth (Google, Facebook)
- **Security:** Cloudflare Turnstile for captcha protection

## Project Structure

```
blog-platform/
├── backend/          # Laravel API (✅ implemented with Clean Architecture)
├── frontend/         # Nuxt 3 application (✅ implemented)
├── docker-compose.yml
└── docs/             # Planning documents and core context
```

## Key Features (MVP - Phase 1)

1. **Authentication System** - ✅ **COMPLETED** - Role-based with SuperAdmin, Admin, Collaborator, Guest roles
2. **Content Management** - 🔄 **IN PROGRESS** - WYSIWYG editor (Quill.js) with auto-save and preview
3. **Multi-language Support** - ⏳ **PENDING** - Spanish (default), English, Portuguese
4. **Comment System** - ⏳ **PENDING** - With moderation workflow
5. **Basic Newsletter** - ⏳ **PENDING** - Email capture and manual sending
6. **Static Pages** - ⏳ **PENDING** - About, Contact pages
7. **SEO Optimization** - ⏳ **PENDING** - Meta tags, sitemap, friendly URLs
8. **Security** - ✅ **COMPLETED** - Rate limiting, CSRF protection, input sanitization

## Development Commands

To work with the implemented codebase:

### Backend (Laravel)
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Frontend (Nuxt 3)
```bash
cd frontend
pnpm install
pnpm run dev
pnpm run build
pnpm run generate
```

### Development Environment
```bash
docker-compose up -d
```

## Core Architecture Principles

### Clean Architecture Implementation
- **Domain Layer:** Entities, Value Objects, Business Rules
- **Application Layer:** Use Cases, DTOs, Interfaces
- **Infrastructure Layer:** Database, External Services, Framework
- **Presentation Layer:** Controllers, API Resources

### Security Requirements
- Rate limiting on critical endpoints (login: 5/min, comments: 3/min, newsletter: 1/min)
- HTML sanitization with HTMLPurifier
- Strict file upload validation
- HTTPS enforcement
- Cloudflare Turnstile integration

### Database Structure
Key tables (implemented/planned):
- ✅ Users with role field (custom Permission system)
- ✅ Posts with translations (multi-language)
- ⏳ Comments with moderation states
- ⏳ Newsletter subscribers
- ⏳ Static pages with translations
- ⏳ Media management

## API Endpoints (Implemented & Planned)

### Authentication ✅ **IMPLEMENTED**
- POST /api/auth/login
- POST /api/auth/register
- POST /api/auth/social/{provider}

### Content Management 🔄 **PARTIALLY IMPLEMENTED**
- GET /api/posts (public listing)
- GET /api/posts/{slug} (individual post)
- POST /api/admin/posts (create)
- PUT /api/admin/posts/{id} (update)

### Comments ⏳ **PENDING**
- GET /api/posts/{slug}/comments
- POST /api/posts/{slug}/comments
- PUT /api/admin/comments/{id} (moderation)

## Development Phases

1. **Phase 1 (MVP):** Core blogging functionality - 12-15 days
2. **Phase 2:** Categories, RSS, Analytics - 1-2 weeks
3. **Phase 3:** Automation, Scheduling, Advanced Cache - 1-2 weeks
4. **Phase 4:** Scalability, CDN, Monitoring - 2-3 weeks
5. **Phase 5+:** Collaboration features (future)

## Important Notes for Implementation

- Use custom Permission enum system for roles/permissions
- Implement auto-save every 30 seconds in editor
- Posts URL structure: /{locale}/{year}/{month}/{slug}
- All forms require Cloudflare Turnstile validation
- Default comment state: pending_approval for non-admins
- SuperAdmin role created via seeder, disabled by default
- Follow Docker setup for consistent development environment

## Documentation Structure

All project documentation is organized within the `/docs/` directory:

### `/docs/core-context/` - Essential Technical Information
- All critical technical specifications organized for optimal access
- Architecture principles, database schema, API endpoints
- Technology stack, security requirements, code templates (see /docs/templates/)
- Domain entities, use cases, and service container setup

### `/docs/HU/` - User Stories (28 stories covering all phases)
- Each story in a separate markdown file (e.g., `historia-1-autenticacion-y-gestion-de-usuarios.md`)
- Stories numbered and organized by development phases
- Covers MVP through advanced features

### `/docs/task/` - Development Tasks
- Detailed task breakdown derived from user stories
- Each task in a separate file with specific implementation requirements
- Organized by sprint and feature area

### `/docs/sprints.md` - Sprint Planning & Organization
- Complete 19-sprint development timeline
- Links user stories to specific tasks
- Organized in 3-day sprint cycles for MVP, longer for advanced features

This consolidated structure provides both high-level architectural guidance and granular implementation details for development.


# Cómo estar listo para aportar al proyecto

Lee [Readme](./README.md) y luego lee:
1. [Project overview](./docs/core-context/01-project-overview.md)
2. [Database schema](./docs/core-context/02-database-schema.md)
3. [Api endpoints](./docs/core-context/03-api-endpoints.md)
4. [Architecture principles](./docs/core-context/04-architecture-principles.md)
5. [Backend structure](./docs/core-context/05-backend-structure.md)
6. [Frontend structure](./docs/core-context/06-frontend-structure.md)
7. [Frontend service container](./docs/core-context/07-frontend-service-container.md)
8. [Development plan](./docs/core-context/08-development-plan.md)
9. [Technology stack](./docs/core-context/09-technology-stack.md)
10. [Clean architecture principles](./docs/core-context/10-clean-architecture-principles.md)
11. [Domain entities key](./docs/core-context/11-domain-entities-key.md)
12. [Use cases critical](./docs/core-context/12-use-cases-critical.md)
13. [Security requirements](./docs/core-context/13-security-requirements.md)
14. [MVP features essential](./docs/core-context/14-mvp-features-essential.md)
15. [Container service setup](./docs/core-context/15-container-services-setup.md)
16. [Typscript conventions](./docs/core-context/16-typescript-conventions.md)

## Si te preguntan por estado del proyecto

Revisa el archivo [sprints](./docs/sprints.md). Ahí deberias encontrar todo lo que necesitas para entender que se ha hecho y que no del proyecto.

## Si te piden ejecutar una tarea en especifico

1. Revisar en el proyecto si la tarea/HU ya se encuentra implementada.
   * Si lo está coompletamente implementada, avisale al usuario y espera instrucciones.
   * Sino no hay nada hecho de la funcionalidad, pasa a crear el plan de acción desde cero.
   * Si no se ha implementado nada pero ya tiene un plan de acción creado, revisa si ese plan de acción tiene sentido en función de la tarea que se tiene que resolver, sino lo tiene notifica al usuario, si sí lo tiene pide permiso para ejecutar el plan de acción.
   * Si lo está implementada a medias revisa si en el directorio docs/action-plans ya hay un plan de acción para la misma. Si hay un plan de acción examina si es correcto y si tiene sentido con el estado actual de la aplicación. Si tiene sentido, indicale al usuario como vas a proceder a partir de este punto. Si es incorrecto o no tiene sentido informale al usuario y espera instrucciones.

## Creando el plan de acción

Una vez el usuario te ha dado el visto bueno para procededr, debes crear dentro del directorio action-plans (dentro de docs, sino existe debes crearlo) un archivo .md con el plan de acción para llevar a cabo esa tarea/HU que el usuario te pidió. Al inicio de este archivo debes colocar una estimación en tiempo de cuanto crees que llevaría realizar las modificaciones.

Una vez hayas terminado tu redacción debes avisar al usuario y esperar autorización para ejecutar el plan de acción.

Al ejecutar el plan de acción debes crear un ToDo con los diferentes pasos del plan de acción para que sirva de guía visual para el usuario para saber cual es el progreso que llevas

## Si te piden ejecutar un plan de acción no creado durante la conversación en curso

1. Busca en [Historias](./docs/HU/) y [Tareas](./docs/task/) la historia/tarea que corresponda al plan de acción
   * Revisa que exista consistencia entre la historia/tarea que hay en la documentación de tareas e historias y el plan de acción que te están pidiendo ejecutar.
   * Si el plan de acción no tiene nada que ver, o muy poco, con la tarea/historia que está intentando implementar, no hagas nada y comunicalo al usuario. Espera instrucciones.
2. Revisa si el plan de acción ya se ha ejecutado.
   * Si el plan de acción ya se ejecuto completamente. No hagas nada. Comunicalo al usuario.
   * Si el plan de acción ves que se comenzó a implementar pero está incompleto, notifica al usuario el estado avance y con cuales pasos vas a continuar dentro del plan de acción hasta completarlo
3. Al ejecutar el plan de acción debes crear un ToDo con los diferentes pasos del plan de acción para que sirva de guía visual para el usuario para saber cual es el progreso que llevas

## Una vez que se haya completado un plan de acción

Debes revisar el archivo [sprints](./docs/sprints.md) y revisar si la acción realizada a completado alguna de las tareas, si es así, editar el archivo para agregarla como completada

## Importante sobre archivos generados con php artisan make:*

**REGLA CRÍTICA:** Todos los archivos generados con comandos `php artisan make:*` (make:request, make:controller, make:model, make:migration, make:middleware, etc.) tendrán ownership de root que impide editarlos directamente.

**Protocolo obligatorio:**
1. **NUNCA** intentes hacer `chmod` directamente, el problema es de ownership, no de permisos
2. **INMEDIATAMENTE** después de ejecutar cualquier comando `make:*`, informa al usuario qué archivos fueron generados
3. **SOLICITA** al usuario que ejecute `sudo chown $USER:$USER [archivo(s)]` para cambiar el ownership
4. **ESPERA** confirmación del usuario antes de intentar editar esos archivos
5. **NO** continúes con la implementación hasta que el ownership esté corregido

Esta regla aplica para CUALQUIER archivo generado por artisan, sin excepción.

## Convenciones de TypeScript

**REGLA CRÍTICA:** Separación estricta entre `interface` y `type`.

### Interface vs Type Usage
- **Interfaces**: SOLO para definir abstracciones y contratos de comportamiento
- **Types**: SOLO para definir formas de datos y estructuras

### Organización de archivos:
- **Interfaces**: Cada interfaz en su propio archivo `[name].interface.ts`
- **Types**: Tipos relacionados agrupados en archivos `[domain].types.ts`

### Ejemplos:
```typescript
// ✅ Correcto - Abstracción
// user-repository.interface.ts
export interface UserRepositoryInterface {
    save(user: User): Promise<void>;
}

// ✅ Correcto - Tipos de datos
// auth.types.ts
export type LoginCredentials = {
    email: string;
    password: string;
};

// ❌ Incorrecto - NO usar interface para datos
export interface LoginCredentials {
    email: string;
    password: string;
}
```

**Beneficios:**
- Clara separación de responsabilidades
- Mejor organización del código
- Imports más claros y mantenibles
- Aprovecha las fortalezas de cada construcción TypeScript

Ver documentación completa: [TypeScript Conventions](./docs/core-context/16-typescript-conventions.md)