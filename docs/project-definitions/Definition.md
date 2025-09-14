# BRIEF TÉCNICO - BLOG PLATFORM PERSONAL

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

**Roles definidos:**

- **SuperAdmin:** Creado automáticamente, desactivado por defecto, activable solo por BD
- **Admin:** Usuario principal (cliente)
- **Colaborador:** Para futuros colaboradores
- **Invitado:** Rol por defecto, solo permite comentar

**Autenticación:**

- Laravel Sanctum para API
- Login tradicional (email/password)
- OAuth social: Google, Facebook (Laravel Socialite)
- Cloudflare Turnstile para captcha

**Implementación técnica:**

- Spatie Permission package para roles y permisos
- Seeders automáticos para crear SuperAdmin
- Middleware de autenticación y autorización

### 2. Gestión de Contenido (Posts)

**Editor:**

- WYSIWYG con Quill.js (moderno y liviano)
- Soporte para: texto formateado, imágenes, videos YouTube embebidos
- Auto-save cada 30 segundos
- Modo preview (/preview/{token})

**Estados de Posts:**

- `draft` - Borrador
- `published` - Publicado
- `archived` - Archivado

**Estructura URLs:**

- Posts: `/{locale}/{year}/{month}/{slug}`
- Páginas estáticas: `/{locale}/{slug}`

**Metadatos SEO:**

- Meta description personalizable
- Canonical URLs automáticas
- Open Graph tags

### 3. Sistema de Comentarios

**Estados de comentarios:**

- `pending_approval` - Pendiente (default para no-admins)
- `approved` - Aprobado
- `rejected` - Rechazado

**Funcionalidades:**

- Comentarios de usuarios registrados y anónimos
- Panel de moderación manual (ordenados por fecha, más antiguos primero)
- Filtrado manual extensible (base para automatización futura)
- Cloudflare Turnstile en formulario de comentarios

### 4. Multiidioma

**Idiomas soportados:**

- Español (idioma por defecto)
- Inglés
- Portugués

**Implementación técnica:**

- Nuxt i18n module
- URLs localizadas con hreflang tags
- Detección automática de idioma del navegador
- SEO correcto para cada idioma

### 5. Newsletter Básica

**Funcionalidades MVP:**

- Formulario de captura de emails
- Almacenamiento en base de datos
- Envío manual de newsletters
- Cloudflare Turnstile en formulario de suscripción

**Base para automatización futura:**

- Estructura preparada para templates
- Queue jobs para envío masivo

### 6. Páginas Estáticas

- Página "Sobre mí"
- Página "Contacto"
- Editor simple para modificar contenido

### 7. Gestión de Medios

- Subida a AWS S3
- Validación de archivos (tipos permitidos, tamaño máximo)
- Optimización básica de imágenes
- Alt text para accesibilidad

### 8. SEO y Performance

- Meta tags dinámicos
- Sitemap automático (Nuxt)
- URLs amigables
- Optimización de imágenes automática
- Critical CSS inline (Nuxt automático)
- File cache para queries frecuentes

### 9. Seguridad (Crítico para MVP)

- Rate limiting en endpoints críticos:
    - Login: 5 intentos por minuto
    - Comentarios: 3 por minuto
    - Newsletter: 1 por minuto
- Validación estricta de uploads
- Sanitización HTML (HTMLPurifier)
- HTTPS forzado
- Cloudflare Turnstile en formularios críticos

## ESTRUCTURA DE BASE DE DATOS

### Tablas Principales

```sql
-- Usuarios y roles
users (id, name, email, email_verified_at, password, avatar, created_at, updated_at)
roles (id, name, guard_name, created_at, updated_at)
permissions (id, name, guard_name, created_at, updated_at)
model_has_roles (role_id, model_type, model_id)
model_has_permissions (permission_id, model_type, model_id)
role_has_permissions (permission_id, role_id)

-- Contenido
posts (id, user_id, title, slug, content, meta_description, status, published_at, created_at, updated_at)
post_translations (id, post_id, locale, title, slug, content, meta_description)

-- Comentarios
comments (id, post_id, user_id, author_name, author_email, content, status, created_at, updated_at)

-- Newsletter
newsletter_subscribers (id, email, active, subscribed_at, unsubscribed_at)

-- Páginas estáticas
pages (id, title, slug, content, created_at, updated_at)
page_translations (id, page_id, locale, title, slug, content)

-- Medios
media (id, filename, original_filename, path, mime_type, size, alt_text, created_at, updated_at)
```

## CONFIGURACIÓN DE DESARROLLO

### Docker Setup

```yaml
# docker-compose.yml para backend
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8000:8000"
    volumes:
      - ./:/var/www
    environment:
      - DB_HOST=mysql
    depends_on:
      - mysql

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: blog_db
    ports:
      - "3306:3306"
```

### Variables de Entorno Críticas

```env
# Laravel
APP_NAME="Blog Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_db
DB_USERNAME=root
DB_PASSWORD=

# AWS S3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

# OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=

# Cloudflare Turnstile
TURNSTILE_SITE_KEY=
TURNSTILE_SECRET_KEY=
```

## ENDPOINTS API PRINCIPALES

### Autenticación

```
POST /api/auth/login
POST /api/auth/register
POST /api/auth/logout
POST /api/auth/social/{provider}
```

### Posts

```
GET /api/posts                 # Listado público
GET /api/posts/{slug}          # Post individual
POST /api/admin/posts          # Crear post
PUT /api/admin/posts/{id}      # Actualizar post
DELETE /api/admin/posts/{id}   # Eliminar post
```

### Comentarios

```
GET /api/posts/{slug}/comments     # Comentarios de un post
POST /api/posts/{slug}/comments    # Crear comentario
GET /api/admin/comments            # Panel moderación
PUT /api/admin/comments/{id}       # Aprobar/rechazar
```

### Newsletter

```
POST /api/newsletter/subscribe     # Suscribirse
POST /api/admin/newsletter/send    # Envío manual
```

## ROADMAP FASES FUTURAS

### FASE 2 - OPERACIONAL CRÍTICO (1-2 semanas)

- Backup automático + Health checks
- Categorías/Tags + RSS feeds
- SEO avanzado + Redirects 301
- Dark mode
- Comentarios anidados + moderación automática
- Analytics básicos

### FASE 3 - AUTOMATIZACIÓN (1-2 semanas)

- Newsletter automation + templates
- Programación de posts
- Redis cache + Laravel Horizon
- Filtros automáticos comentarios (Akismet, ML)
- Búsqueda avanzada

### FASE 4 - ESCALABILIDAD (2-3 semanas)

- CDN completo + Critical CSS
- Monitoreo + Logs estructurados
- Performance tuning avanzado
- Mobile app API preparation
- Advanced analytics
- PWA capabilities (si hay tiempo)
- Premium content structure (si hay tiempo)

### FASE 5+ - COLABORACIÓN (FUTURO LEJANO)

- Editor colaborativo real-time
- Drafts compartidos
- Control de versiones
- Multi-tenant (si se requiere)

## CRITERIOS DE ACEPTACIÓN MVP

### Funcionales

- ✅ Usuario admin puede crear/editar/eliminar posts
- ✅ Posts se muestran públicamente con diseño limpio
- ✅ Visitantes pueden comentar (con moderación)
- ✅ Sistema multiidioma funcional
- ✅ Formulario newsletter captura emails
- ✅ Páginas "Sobre mí" y "Contacto" editables
- ✅ Auto-save y preview funcionando

### Técnicos

- ✅ API Laravel documentada y funcional
- ✅ Frontend Nuxt con SSG optimizado
- ✅ SEO correcto (meta tags, hreflang, sitemap)
- ✅ Todas las medidas de seguridad implementadas
- ✅ Docker setup funcional
- ✅ Deploy en VPS exitoso
- ✅ Performance: < 3s carga inicial, > 90 Lighthouse

### Operacionales

- ✅ Manual de usuario para admin
- ✅ Documentación técnica completa
- ✅ Variables de entorno documentadas
- ✅ Proceso de backup manual documentado

## ENTREGABLES

1. **Código fuente completo**
    
    - Repositorio Git con ambas aplicaciones
    - Docker setup para desarrollo
    - Scripts de deployment
2. **Base de datos**
    
    - Migraciones Laravel
    - Seeders con datos de prueba
    - Diagrama ER
3. **Documentación**
    
    - README técnico detallado
    - Documentación de API (Postman/Swagger)
    - Manual de usuario admin
    - Guía de deployment
4. **Configuración**
    
    - Archivos .env.example
    - Configuración nginx/apache
    - SSL certificates setup

## CONTACTO Y CLARIFICACIONES

Para cualquier duda técnica durante el desarrollo, contactar al Product Owner para clarificaciones sobre:

- Casos de uso específicos
- Priorizaciones de funcionalidades
- Decisiones de UX/UI
- Cambios de alcance
