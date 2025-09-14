# Project Overview

## INFORMACIÓN GENERAL

- **Tipo de Proyecto:** Blog personal para mostrar expertise profesional
- **Objetivo:** Plataforma simple de blogging, más rápida y liviana que WordPress
- **Cliente:** Propietario individual, sin fines de monetización inicial
- **Timeline MVP:** 12-15 días laborales

## ARQUITECTURA TÉCNICA

### Stack Tecnológico

- **Backend:** Laravel API (PHP 8.2+)
- **Frontend:** Nuxt 3 (SSG/SSR)
- **Base de Datos:** MySQL/PostgreSQL
- **Storage:** AWS S3 para medios
- **Cache:** File cache (MVP), Redis en fases futuras
- **Hosting:** VPS propio (backend) + Vercel/Netlify (frontend)

### Estructura del Proyecto

```
blog-platform/
├── backend/          # Laravel API
├── frontend/         # Nuxt 3 application
├── docker-compose.yml
└── README.md
```

## FUNCIONALIDADES MVP - FASE 1

### 1. Sistema de Usuarios y Autenticación
- **Roles:** SuperAdmin, Admin, Colaborador, Invitado
- **Autenticación:** Laravel Sanctum, Login tradicional, OAuth (Google, Facebook), Cloudflare Turnstile.

### 2. Gestión de Contenido (Posts)
- **Editor:** WYSIWYG con Quill.js, auto-save, preview.
- **Estados:** draft, published, archived.
- **URLs:** `/{locale}/{year}/{month}/{slug}`
- **SEO:** Meta description, Canonical URLs, Open Graph.

### 3. Sistema de Comentarios
- **Estados:** pending_approval, approved, rejected.
- **Funcionalidades:** Comentarios de registrados y anónimos, panel de moderación.

### 4. Multiidioma
- **Idiomas:** Español (default), Inglés, Portugués.
- **Implementación:** Nuxt i18n, URLs localizadas, hreflang tags.

### 5. Newsletter Básica
- Captura de emails, envío manual.

### 6. Páginas Estáticas
- "Sobre mí" y "Contacto".

### 7. Gestión de Medios
- Subida a AWS S3, validación, optimización básica.

### 8. SEO y Performance
- Meta tags dinámicos, sitemap, URLs amigables, Critical CSS.

### 9. Seguridad (Crítico para MVP)
- Rate limiting, validación de uploads, sanitización HTML, HTTPS, Turnstile.
