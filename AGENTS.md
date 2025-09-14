# AGENTS.md

## Build, Lint, and Test Commands

**Backend (Laravel):**
- Install: `cd backend && composer install`
- Env: `cp .env.example .env && php artisan key:generate`
- Migrate: `php artisan migrate --seed`
- Run: `php artisan serve`
- Test all: `php artisan test`
- Test single: `php artisan test --filter=TestClassName`
- Lint: `composer run-script lint` (if defined)

**Frontend (Nuxt 3):**
- Install: `cd frontend && npm install`
- Dev: `npm run dev`
- Build: `npm run build`
- Lint: `npm run lint`
- Test all: `npm run test` (if defined)
- Test single: `npm run test -- -t 'test name'`

## Code Style Guidelines

- **Imports:** Use absolute imports for domain/application layers; group by vendor, then project, then relative.
- **Formatting:** 2 spaces for JS/TS, PSR-12 for PHP. Use Prettier (frontend) and PHP CS Fixer (backend) if available.
- **Types:** Always use strict types in PHP (`declare(strict_types=1);`). Prefer explicit types in TypeScript.
- **Naming:**
  - Classes: `PascalCase` (e.g., `PostController`)
  - Variables/functions: `camelCase`
  - Constants: `UPPER_SNAKE_CASE`
- **Error Handling:**
  - Throw domain-specific exceptions for business logic errors.
  - Use try/catch in application/infrastructure layers only.
- **Other:**
  - Follow Clean Architecture: no framework code in domain layer.
  - Use DTOs for input/output in use cases.
  - Validate all user input and sanitize HTML.
  - All forms must use Cloudflare Turnstile.

Refer to `/project-definitions/Plan de ejecución.md` for detailed templates and `/project-definitions/Definition.md` for architecture rules.