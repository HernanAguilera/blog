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

The backend can be run using Docker. Complete setup instructions and configuration examples are provided in `README.md`.

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

The project's development is strictly guided by the **Clean Architecture** principles detailed in `docs/core-context/10-clean-architecture-principles.md` and the complete architecture overview in `README.md`.

### Key Architectural Concepts:

-   **Dependency Rule:** Dependencies flow inwards, from the Interface and Infrastructure layers towards the Application and Domain layers. The Domain layer has no external dependencies.
-   **Backend Layers:**
    -   `Domain`: Contains pure business logic, including **Entities**, **Value Objects**, **Domain Services**, and **Repository Interfaces**.
    -   `Application`: Orchestrates the business logic using **Use Cases** (following a CQRS-like pattern with Commands and Queries).
    -   `Infrastructure`: Provides concrete implementations for services, such as **Eloquent Repositories**, external API clients (AWS S3), and caching (Redis).
    -   `Interface`: The entry point for external requests, containing **HTTP Controllers**, **Console Commands**, and API Resources.
-   **Frontend Layers:** A similar Clean Architecture structure is applied to the Nuxt frontend, separating concerns into `domain`, `application`, `infrastructure`, and `interface` directories. This includes frontend entities, use cases, repositories that communicate with the backend API, and UI components.
-   **Dependency Injection:** A service container is used on the frontend to manage dependencies between layers, as detailed in `docs/core-context/16-container-services-setup.md`.

## Key Files

This directory is primarily for documentation. The core project definition is spread across several Markdown files in the `/docs` directory.

-   `README.md`: The main project overview with complete technical specifications, architecture decisions, development methodology, and setup instructions.
-   `docs/core-context/`: Essential technical information organized for optimal access, including architecture principles, database schema, API endpoints, technology stack, security requirements, and domain entities.
-   `docs/templates/`: Code templates for different class types (Entities, Use Cases, Repositories, etc.) organized in individual files for easy access during development.
-   `docs/HU/`: Contains detailed User Stories (Historias de Usuario) for all planned features, from the MVP to future phases, along with their acceptance criteria.
-   `docs/task/`: A breakdown of development tasks derived from the User Stories, organized by feature and sprint.
-   `docs/sprints.md`: Sprint planning and organization with complete development timeline.


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
