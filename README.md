# BlogV2 - Modern Clean Architecture Blogging Platform

![Project Status: In Planning](https://img.shields.io/badge/status-in%20planning-blue.svg)
![Architecture: Clean Architecture](https://img.shields.io/badge/architecture-clean-green.svg)
![Backend: Laravel](https://img.shields.io/badge/backend-laravel-red.svg)
![Frontend: Nuxt 3](https://img.shields.io/badge/frontend-nuxt%203-brightgreen.svg)

## About This Project

BlogV2 is a professional-grade, high-performance blogging platform built from the ground up using **Clean Architecture** principles. Designed as a modern alternative to traditional CMS platforms like WordPress, it prioritizes speed, maintainability, security, and developer experience.

**Current Status:** Complete planning and architecture documentation. Implementation phase ready to begin.

## Why This Project?

### The Problem with Traditional CMS
- **Performance bottlenecks** from monolithic architecture
- **Security vulnerabilities** from extensive plugin ecosystems
- **Maintenance overhead** from coupled components
- **Limited scalability** options

### Our Solution: Clean Architecture + Modern Stack
- **Blazing fast** with SSG/SSR and optimized caching
- **Highly secure** with minimal attack surface
- **Developer friendly** with hot reload and dependency injection
- **Future-proof** with framework-independent business logic
- **Scalable** with decoupled frontend/backend architecture

## Key Features (MVP)

### 🔐 **Flexible Authentication System**
- Traditional email/password with JWT tokens
- OAuth integration (Google, Facebook, Twitter)
- Role-based access control (SuperAdmin, Admin, Collaborator, Guest)
- Advanced rate limiting and security measures

### ✍️ **Advanced Content Management**
- Modern WYSIWYG editor (Quill.js) with syntax highlighting
- Auto-save every 30 seconds with conflict resolution
- Preview mode with shareable links
- Multiple post states (draft, published, scheduled, archived)
- SEO optimization with meta tags and canonical URLs

### 🌍 **Multi-Language Support**
- Complete i18n for UI and content
- Support for Spanish (default), English, and Portuguese
- Localized URLs with hreflang tags
- Automatic browser language detection

### 💬 **Interactive Comment System**
- User and anonymous commenting
- Advanced moderation workflow
- Spam protection with Cloudflare Turnstile
- Rate limiting per user/IP

### 📧 **Newsletter Management**
- Email collection with GDPR compliance
- Manual sending with template support
- Unsubscribe management
- Foundation for future automation

### 🚀 **SEO & Performance**
- Dynamic meta tags and Open Graph support
- Automatic sitemap generation
- Critical CSS inlining
- Image optimization and lazy loading
- Core Web Vitals optimized (>90 Lighthouse score)

### 🔒 **Enterprise-Grade Security**
- Rate limiting on all critical endpoints
- HTML sanitization with HTMLPurifier
- CSRF protection and secure headers
- File upload validation and virus scanning
- Security event logging and monitoring

## Technology Stack

### Architecture Pattern
**Clean Architecture** with strict separation of concerns:
- **Domain Layer**: Business rules and entities
- **Application Layer**: Use cases and orchestration
- **Infrastructure Layer**: External services and persistence
- **Interface Layer**: Controllers and presentation

### Backend Stack
- **Framework**: Laravel 11.x (PHP 8.3+)
- **Database**: PostgreSQL 15+ with Redis 7+ for caching
- **Authentication**: Laravel Sanctum + JWT
- **Queue System**: Laravel Horizon + Redis
- **File Storage**: AWS S3 + CloudFront CDN
- **Testing**: PHPUnit + Pest
- **Documentation**: OpenAPI 3.0 (Swagger)

### Frontend Stack
- **Framework**: Nuxt 3.8+ (Vue 3 + TypeScript)
- **State Management**: Pinia with persistent storage
- **Styling**: Tailwind CSS 3.4+
- **Editor**: Quill.js + Highlight.js
- **HTTP Client**: Ofetch (native Nuxt)
- **Testing**: Vitest + Cypress
- **Build Tool**: Vite with optimizations

### Infrastructure
- **Containerization**: Docker + Docker Compose
- **Web Server**: Nginx (reverse proxy + static files)
- **Process Manager**: PM2 for Nuxt in production
- **Monitoring**: Laravel Telescope + custom metrics
- **Security**: Cloudflare (WAF + DDoS protection)

## Architecture Decisions

### Why Clean Architecture for a Blog?

1. **Scalability**: Easy to add features without impacting existing code
2. **Testability**: Fast, reliable tests with clear boundaries
3. **Flexibility**: Switch frameworks without rewriting business logic
4. **Maintainability**: Clear code organization and separation of concerns
5. **Team Development**: Parallel work on different layers without conflicts

### Why Decoupled Frontend/Backend?

1. **Performance**: SSG/SSR for public content, SPA for admin interface
2. **Deployment Flexibility**: Independent scaling and deployment
3. **Technology Choice**: Best tool for each specific problem
4. **Security**: API can be more restrictive than UI
5. **Mobile Ready**: API prepared for future mobile applications

### Why Not WordPress?

1. **Performance**: Nuxt SSG is significantly faster
2. **Security**: Reduced attack surface with fewer dependencies
3. **Customization**: Complete control over functionality
4. **Modern Stack**: Better developer experience and tooling
5. **Scalability**: Architecture designed for growth

## Development Methodology

### Approach
- **Domain-Driven Development**: Start with business logic, then infrastructure
- **Test-First**: Write tests before implementation
- **Vertical Slicing**: Complete features end-to-end
- **Continuous Integration**: Every commit maintains system functionality

### Quality Standards
- **Code Coverage**: Minimum 80% for domain and application layers
- **Performance**: Core Web Vitals in green, <3s initial load
- **Security**: All measures implemented and tested
- **UX**: Intuitive, responsive interface
- **Documentation**: Self-documenting code with comprehensive README

### Development Tools

#### Backend
- **Laravel Sail** for consistent environment
- **Pest** for expressive testing
- **Larastan** for static analysis
- **Laravel IDE Helper** for better DX

#### Frontend
- **Nuxt DevTools** for debugging
- **Vitest** for unit testing
- **Cypress** for E2E testing
- **ESLint + Prettier** for code consistency

## Getting Started

### Prerequisites
- Docker and Docker Compose
- Node.js 18+ and npm
- PHP 8.3+ and Composer (for local development)

### Quick Start with Docker

These commands allow you to build and run the application using the Docker setup.

#### Development Environment

For local development with hot-reloading.

```bash
# Build and start the development containers in the background
docker compose -f docker-compose.dev.yml up -d --build
```

#### Production Environment

To simulate and run the production-ready version of the application.

```bash
# Build and start the production containers in the background
docker compose -f docker-compose.prod.yml up -d --build
```

### Environment Configuration

Create `.env` files based on the examples:

```env
# Backend (.env)
APP_NAME="Blog Platform"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=blog_db
DB_USERNAME=blog_user
DB_PASSWORD=secure_password

# AWS S3 (for file uploads)
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name

# OAuth (optional)
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_secret
FACEBOOK_CLIENT_ID=your_facebook_client_id
FACEBOOK_CLIENT_SECRET=your_facebook_secret

# Cloudflare Turnstile (for spam protection)
TURNSTILE_SITE_KEY=your_site_key
TURNSTILE_SECRET_KEY=your_secret_key
```

## Project Documentation

This project maintains comprehensive documentation organized for optimal developer experience:

### Core Context (`/docs/core-context/`)
Essential information for understanding the project:
- **Project Overview** (`01-project-overview.md`)
- **Database Schema** (`02-database-schema.md`)
- **API Endpoints** (`03-api-endpoints.md`)
- **Architecture Principles** (`04-architecture-principles.md`)
- **Backend/Frontend Structure** (`05-backend-structure.md`, `06-frontend-structure.md`)
- **Technology Stack** (`09-technology-stack.md`)
- **Security Requirements** (`13-security-requirements.md`)
- **Implementation Templates** (see `/docs/templates/` directory)

### Development Planning (`/docs/`)
- **User Stories** (`HU/`): Complete feature specifications
- **Development Tasks** (`task/`): Granular implementation tasks
- **Sprint Planning** (`sprints.md`): Organized development timeline
- **Design Decisions** (`design-decisions/`): Architecture and design choices with rationale

## MVP Acceptance Criteria

### Functional Requirements
- [x] Admin can create/edit/delete posts with WYSIWYG editor
- [x] Public post display with clean, responsive design
- [x] Visitor commenting system with moderation
- [x] Multi-language system (Spanish, English, Portuguese)
- [x] Newsletter subscription with email capture
- [x] Editable static pages (About, Contact)
- [x] Auto-save and preview functionality

### Technical Requirements
- [x] Documented Laravel API with OpenAPI spec
- [x] Optimized Nuxt frontend with SSG
- [x] Complete SEO implementation (meta tags, sitemap, hreflang)
- [x] All security measures implemented and tested
- [x] Docker development environment
- [x] Production deployment capability
- [x] Performance: <3s load time, >90 Lighthouse score

### Operational Requirements
- [x] Admin user manual
- [x] Complete technical documentation
- [x] Environment configuration guide
- [x] Backup and recovery procedures

## Roadmap

### Phase 2 - Operational (1-2 weeks)
- Automated backups and health monitoring
- Categories, tags, and RSS feeds
- Advanced SEO features and 301 redirects
- Dark mode theme
- Nested comments and automatic moderation
- Basic analytics dashboard

### Phase 3 - Automation (1-2 weeks)
- Newsletter automation with templates
- Post scheduling system
- Redis caching with Laravel Horizon
- ML-powered spam filtering
- Advanced search functionality

### Phase 4 - Scalability (2-3 weeks)
- Complete CDN integration
- Structured logging and monitoring
- Advanced performance tuning
- Mobile app API preparation
- PWA capabilities
- Premium content architecture

### Phase 5+ - Collaboration (Future)
- Real-time collaborative editing
- Version control for content
- Multi-tenant architecture
- Advanced workflow management

## Contributing

This project welcomes contributions! Please see our contributing guidelines for:
- Code style and conventions
- Testing requirements
- Pull request process
- Issue reporting

## Success Metrics

### Technical KPIs
- Build time < 5 minutes
- Test suite execution < 30 seconds
- Lighthouse performance score > 90
- Zero critical security vulnerabilities

### Functional KPIs
- Admin can manage all content efficiently
- Public site loads in < 3 seconds
- Comment moderation workflow < 2 minutes
- Newsletter signup conversion > 5%

### Business KPIs
- 99.5%+ uptime
- < 200ms API response time
- Zero data loss incidents
- Full GDPR compliance

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contact

For technical questions, feature requests, or collaboration opportunities:
- Create an issue in this repository
- Contact the development team
- Check the documentation in `/docs/core-context/`

---

**Built with ❤️ using Clean Architecture principles**