# Database Schema

## Tablas Principales

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
