# Gemini Context: BlogV2 Project

## Project Overview

This directory contains the planning and definition documents for **BlogV2**, a personal blog platform. The goal is to create a simple, fast, and lightweight blogging system as an alternative to WordPress, designed to showcase professional expertise.

The project is architected as a decoupled system with a separate backend and frontend:

-   **Backend:** A RESTful API built with **Laravel (PHP 8.2+)**. It follows **Clean Architecture** principles, separating Domain, Application, Infrastructure, and Interface layers. It handles business logic, data persistence, and authentication.
-   **Frontend:** A **Nuxt 3 (Vue 3 + TypeScript)** application responsible for presentation and user interaction. It can be deployed as a Static Site Generation (SSG) or Server-Side Rendering (SSR) app on platforms like Vercel or Netlify.
-   **Database:** The primary database is **PostgreSQL** or **MySQL**, with **Redis** for caching and session management.
-   **Storage:** Media files are intended to be stored on **AWS S3**.

The project structure is planned as follows:

```
blog-platform/
├── backend/          # Laravel API
├── frontend/         # Nuxt 3 application
├── docker-compose.yml
└── README.md
```

## Building and Running

As the project is in the planning phase, no source code exists yet. However, the setup and execution commands can be inferred from the technical documentation.

### Backend (Laravel)

The backend can be run using Docker. A sample `docker-compose.yml` is provided in `docs/Definition.md`.

1.  **Navigate to the `backend` directory.**
2.  **Install dependencies:** `composer install`
3.  **Set up environment:** `cp .env.example .env` and fill in the variables (DB, AWS, etc.).
4.  **Generate app key:** `php artisan key:generate`
5.  **Run database migrations and seeders:** `php artisan migrate --seed`
6.  **Start the development server:** `php artisan serve` or `docker-compose up -d`

### Frontend (Nuxt 3)

1.  **Navigate to the `frontend` directory.**
2.  **Install dependencies:** `npm install`
3.  **Start the development server:** `npm run dev`
4.  **Build for production:** `npm run build`

## Development Conventions

The project's development is strictly guided by the **Clean Architecture** principles detailed in `docs/Implementación conceptual.md` and `docs/Detalles de implementación.md`.

### Key Architectural Concepts:

-   **Dependency Rule:** Dependencies flow inwards, from the Interface and Infrastructure layers towards the Application and Domain layers. The Domain layer has no external dependencies.
-   **Backend Layers:**
    -   `Domain`: Contains pure business logic, including **Entities**, **Value Objects**, **Domain Services**, and **Repository Interfaces**.
    -   `Application`: Orchestrates the business logic using **Use Cases** (following a CQRS-like pattern with Commands and Queries).
    -   `Infrastructure`: Provides concrete implementations for services, such as **Eloquent Repositories**, external API clients (AWS S3), and caching (Redis).
    -   `Interface`: The entry point for external requests, containing **HTTP Controllers**, **Console Commands**, and API Resources.
-   **Frontend Layers:** A similar Clean Architecture structure is applied to the Nuxt frontend, separating concerns into `domain`, `application`, `infrastructure`, and `interface` directories. This includes frontend entities, use cases, repositories that communicate with the backend API, and UI components.
-   **Dependency Injection:** A service container is used on the frontend to manage dependencies between layers, as detailed in `docs/Implementación conceptual.md`.

## Key Files

This directory is primarily for documentation. The core project definition is spread across several Markdown files in the `/docs` directory.

-   `docs/Definition.md`: The main technical brief. It outlines the project's objective, MVP features, technology stack, database schema, API endpoints, and future roadmap.
-   `docs/HUs.md`: Contains detailed User Stories (Historias de Usuario) for all planned features, from the MVP to future phases, along with their acceptance criteria.
-   `docs/Detalles de implementación.md`: Provides extremely detailed implementation plans, including Clean Architecture file structures for both backend and frontend, and examples of key classes like Entities, Value Objects, Use Cases, and Repositories.
-   `docs/Implementación conceptual.md`: Explains the high-level concepts of the Clean Architecture approach chosen for the project, detailing the responsibilities of each layer for both backend and frontend.
-   `docs/Plan de ejecución.md`: A step-by-step execution plan, including code templates for different class types (Entities, Use Cases, Repositories, etc.), a sprint-based timeline, and quality checklists.
-   `docs/Tasks.md`: A breakdown of development tasks derived from the User Stories, organized by feature.
