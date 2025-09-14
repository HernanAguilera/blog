# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

BlogV2 is a personal blogging platform built with a decoupled architecture following Clean Architecture principles. The project is currently in the planning phase - **NO CODE HAS BEEN IMPLEMENTED YET**. This repository contains comprehensive planning documents and architectural definitions.

**Current Status:** Planning phase complete, implementation not started

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
├── backend/          # Laravel API (not yet created)
├── frontend/         # Nuxt 3 application (not yet created)
├── docker-compose.yml
└── project-definitions/  # Planning documents (current content)
```

## Key Features (MVP - Phase 1)

1. **Authentication System** - Role-based with SuperAdmin, Admin, Collaborator, Guest roles
2. **Content Management** - WYSIWYG editor (Quill.js) with auto-save and preview
3. **Multi-language Support** - Spanish (default), English, Portuguese
4. **Comment System** - With moderation workflow
5. **Basic Newsletter** - Email capture and manual sending
6. **Static Pages** - About, Contact pages
7. **SEO Optimization** - Meta tags, sitemap, friendly URLs
8. **Security** - Rate limiting, CSRF protection, input sanitization

## Future Development Commands

Since no code exists yet, these are the planned commands once implementation begins:

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
npm install
npm run dev
npm run build
npm run generate
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
Key tables planned:
- Users & Roles (Spatie Permission)
- Posts with translations (multi-language)
- Comments with moderation states
- Newsletter subscribers
- Static pages with translations
- Media management

## API Endpoints (Planned)

### Authentication
- POST /api/auth/login
- POST /api/auth/register
- POST /api/auth/social/{provider}

### Content Management
- GET /api/posts (public listing)
- GET /api/posts/{slug} (individual post)
- POST /api/admin/posts (create)
- PUT /api/admin/posts/{id} (update)

### Comments
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

- Use Spatie Permission package for roles/permissions
- Implement auto-save every 30 seconds in editor
- Posts URL structure: /{locale}/{year}/{month}/{slug}
- All forms require Cloudflare Turnstile validation
- Default comment state: pending_approval for non-admins
- SuperAdmin role created via seeder, disabled by default
- Follow Docker setup for consistent development environment

## Documentation Structure

All project documentation is organized within the `/docs/` directory:

### `/docs/project-definitions/` - High-level Planning & Architecture
- `Definition.md` - Complete technical specifications and architecture
- `Plan de ejecución.md` - Implementation templates and guidelines
- `Implementación conceptual.md` - Conceptual implementation details
- `Detalles de implementación.md` - Technical implementation details

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