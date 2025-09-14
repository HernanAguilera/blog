# BlogV2 - A Modern, Lightweight Blogging Platform

![Project Status: In Planning](https://img.shields.io/badge/status-in%20planning-blue.svg)

## About This Project

BlogV2 is a personal project to build a high-performance, lightweight, and modern blogging platform from the ground up. The goal is to create a faster, more streamlined alternative to traditional CMS platforms like WordPress, with a focus on professional expertise, SEO, and an excellent user experience.

This repository currently holds the complete planning, architecture, and definition documentation for the project. The implementation phase has not yet begun.

## Key Features (MVP)

The initial version (MVP) is planned to include:

-   **Flexible Authentication:** Secure login system supporting traditional email/password, as well as social OAuth providers (Google, Facebook).
-   **Advanced Content Management:** A modern WYSIWYG editor (Quill.js) with auto-saving, preview mode, and support for formatted text, images, and embedded videos.
-   **Multi-Language Support:** Full internationalization (i18n) for UI and content, with support for English, Spanish, and Portuguese.
-   **Comment System:** An interactive comment section with moderation tools to prevent spam and maintain high-quality discussions.
-   **Newsletter Subscription:** A basic system to capture emails and send out newsletters manually.
-   **SEO & Performance:** Built-in SEO best practices, including dynamic meta tags, automatic sitemaps, friendly URLs, and performance optimizations like critical CSS and image optimization.
-   **Robust Security:** Measures including rate limiting, CSRF protection, and strict data sanitization.

## Technology Stack

The project is designed as a decoupled application:

-   **Backend:** **Laravel (PHP 8.2+)** serving a RESTful API.
-   **Frontend:** **Nuxt 3 (Vue.js 3 + TypeScript)** for a fast, server-rendered (or statically generated) user interface.
-   **Database:** **PostgreSQL** or **MySQL**.
-   **Caching:** **Redis** for sessions and application cache.
-   **File Storage:** **AWS S3** for media uploads.
-   **Deployment:** The backend is designed for a VPS, while the frontend can be deployed on services like Vercel or Netlify.

## Architecture

The project follows **Clean Architecture** principles for both the backend and frontend. This ensures a clear separation of concerns, high testability, and long-term maintainability. The core business logic is kept independent of frameworks and external services.

## Getting Started

While the code is not yet available, the intended setup process is outlined below.

### Backend (Laravel)

```bash
# 1. Navigate to the backend directory
cd backend

# 2. Install dependencies
composer install

# 3. Set up your .env file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Run database migrations and seeders
php artisan migrate --seed

# 6. Start the server
php artisan serve
```

### Frontend (Nuxt 3)

```bash
# 1. Navigate to the frontend directory
cd frontend

# 2. Install dependencies
npm install

# 3. Start the development server
npm run dev
```

## Documentation

This project is extensively documented in the `/docs` directory. The documentation is organized as follows:

- `/docs/project-definitions`: Contains all technical definitions, implementation details, and execution plans.
- `/docs/HU`: Contains all User Stories, with each story in a separate Markdown file.
- `/docs/task`: Contains all development tasks, derived from the user stories.
- `/docs/sprints.md`: Outlines the sprint planning for the project.

These documents serve as the single source of truth for the development process.
