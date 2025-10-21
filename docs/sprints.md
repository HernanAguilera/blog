# Planificación de Sprints

## Sprint 0: Configuración del Proyecto

- [x] **Tarea 0.1: Configuración del Repositorio y Entorno**
  - [x] Inicializar el repositorio Git con un archivo `.gitignore` adecuado.
  - [x] Crear la estructura de directorios principal (`backend`, `frontend`).
  - [x] Configurar `docker-compose.dev.yml` para el entorno de desarrollo local (Laravel, Nuxt, PostgreSQL, Redis).
  - [x] Configurar `docker-compose.prod.yml` para el entorno de producción optimizado.
  - [x] Crear los `Dockerfiles` base para los servicios de desarrollo y producción.

- [x] **Tarea 0.2: Andamiaje del Backend (Laravel)**
  - [x] Instalar Laravel y sus dependencias.
  - [x] Crear la estructura de directorios para la Arquitectura Limpia (`src/Domain`, `src/Application`, etc.).
  - [x] Configurar los Service Providers para la inyección de dependencias.
  - [x] Configurar herramientas de calidad de código (PHPStan, PHP-CS-Fixer, PHPUnit mejorado).

- [x] **Tarea 0.3: Andamiaje del Frontend (Nuxt)**
  - [x] Instalar Nuxt 3 y sus dependencias (Pinia, Tailwind CSS) usando pnpm.
  - [x] Crear la estructura de directorios para la Arquitectura Limpia (`domain`, `application`, etc.).
  - [x] Configurar el contenedor de inyección de dependencias.
  - [x] Configurar herramientas de calidad de código (ESLint, Prettier).

## Sprint 1 (Días 1-3): Base de Autenticación

- [x] **Historia de Usuario:** [Historia 1: Autenticación y Gestión de Usuarios](./HU/historia-1-autenticacion-y-gestion-de-usuarios.md)
  - [x] [Tarea 1.1: Configuración Base de Autenticación](./task/tarea-1-1-configuracion-base-de-autenticacion.md)
  - [x] [Tarea 1.2: Autenticación Tradicional (Backend)](./task/tarea-1-2-autenticacion-tradicional-backend.md)
  - [x] [Tarea 1.4: Sistema de Roles (Backend)](./task/tarea-1-4-sistema-de-roles-backend.md)

## Sprint 2 (Días 4-6): OAuth y Seguridad

- [x] **Historia de Usuario:** [Historia 1: Autenticación y Gestión de Usuarios](./HU/historia-1-autenticacion-y-gestion-de-usuarios.md)
  - [x] [Tarea 1.3: OAuth Social (Backend)](./task/tarea-1-3-oauth-social-backend.md)
  - [x] [Tarea 1.5: Seguridad y Rate Limiting](./task/tarea-1-5-seguridad-y-rate-limiting.md)

## Sprint 3 (Días 7-9): Frontend Autenticación

- [x] **Historia de Usuario:** [Historia 1: Autenticación y Gestión de Usuarios](./HU/historia-1-autenticacion-y-gestion-de-usuarios.md)
  - [x] [Tarea 1.6: Frontend - Sistema de Autenticación](./task/tarea-1-6-frontend-sistema-de-autenticacion.md)

## Sprint 4 (Días 10-12): Backend Posts

- [x] **Historia de Usuario:** [Historia 2: Gestión de Posts y Contenido](./HU/historia-2-gestion-de-posts-y-contenido.md)
  - [x] [Tarea 2.1: Dominio de Posts (Backend)](./task/tarea-2-1-dominio-de-posts-backend.md)
  - [x] [Tarea 2.2: CRUD de Posts (Backend)](./task/tarea-2-2-crud-de-posts-backend.md)

## Sprint 5 (Días 13-15): Editor Avanzado

- [x] **Historia de Usuario:** [Historia 2: Gestión de Posts y Contenido](./HU/historia-2-gestion-de-posts-y-contenido.md)
  - [x] [Tarea 2.3: Editor Avanzado (Backend)](./task/tarea-2-3-editor-avanzado-backend.md)
  - [x] [Tarea 2.4: Gestión de Estados](./task/tarea-2-4-gestion-de-estados.md)

## Sprint 6 (Días 16-18): Frontend Posts

- [x] **Historia de Usuario:** [Historia 2: Gestión de Posts y Contenido](./HU/historia-2-gestion-de-posts-y-contenido.md)
  - [x] [Tarea 2.6: Frontend - Gestión de Posts](./task/tarea-2-6-frontend-gestion-de-posts.md)
    - [x] Implementado store de posts con Pinia
    - [x] Componentes PostList y PostEditor
    - [x] Páginas admin para gestión de posts
    - [x] Sistema de toasts para notificaciones
    - [x] Integración completa frontend-backend
  - [x] [Tarea 2.7: Editor Quill.js y Highlight.js](./task/tarea-2-7-editor-quill-js-y-highlight-js.md)
    - [x] Editor Quill.js completamente funcional
    - [x] Auto-save y preview capabilities
    - [x] Manejo de estados de guardado
    - [x] Validación y sanitización de contenido

## ✅ Sprint 6.5 (COMPLETADO): Frontend Público - MVP Completado

- [x] **COMPLETADO:** [Plan de Acción Sprint 6.5](./action-plans/sprint-6-5-frontend-publico.md)
  - [x] **Tarea 6.5.1: Layout Público Base** - Header, Footer, Navigation responsivos
  - [x] **Tarea 6.5.2: Página de Inicio** - Lista de posts publicados con paginación
  - [x] **Tarea 6.5.3: Página Post Individual** - Routing dinámico `/{year}/{month}/{slug}`
  - [x] **Tarea 6.5.4: SEO y Meta Tags** - Open Graph, meta description, structured data
  - [x] **Tarea 6.5.5: Error Pages** - 404 y páginas de error amigables
  - [x] **Tarea 6.5.6: Testing y Optimización** - Performance, responsive, SEO

**🎯 RESULTADO:** Blog público completamente funcional. MVP del blog finalizado.

**✅ LOGROS:** Visitantes pueden ahora leer posts publicados, navegar el sitio, y disfrutar de una experiencia optimizada con SEO completo.

## ✅ Sprint 7: Comentarios y Moderación (COMPLETADO)

- [x] **Historia de Usuario:** [Historia 3: Sistema de Comentarios con Moderación](./HU/historia-3-sistema-de-comentarios-con-moderacion.md)
  - [x] [Tarea 3.1: Dominio de Comentarios (Backend)](./task/tarea-3-1-dominio-de-comentarios-backend.md)
    - [x] Plan de acción: [plan-tarea-3-1-dominio-comentarios-backend.md](./action-plans/plan-tarea-3-1-dominio-comentarios-backend.md)
    - [x] Value Objects implementados: CommentId, CommentContent, CommentStatus, AnonymousAuthor, CommentAuthorType
    - [x] Entidad Comment con métodos approve(), reject(), markAsSpam()
    - [x] CommentRepositoryInterface con getCommentTree() para árbol anidado con Recursive CTE
    - [x] CommentDomainService con sanitización, detección de spam, auto-aprobación
    - [x] Domain Events: CommentCreated, CommentApproved, CommentRejected, CommentMarkedAsSpam, CommentReplyCreated
    - [x] Excepciones: CommentNotFoundException, InvalidCommentStatusException, InvalidCommentContentException, InvalidAuthorException, SpamCommentException
    - [x] Soporte completo para usuarios registrados y anónimos
    - [x] Anidación ilimitada a nivel lógico (control visual en frontend)
  - [x] [Tarea 3.2: CRUD de Comentarios](./task/tarea-3-2-crud-de-comentarios.md)
    - [x] **Migración**: Tabla comments con UUID, foreign keys a posts/users, self-referencing parent_id, índices optimizados
    - [x] **Modelos Eloquent**: CommentModel con HasUuids, relaciones (post, user, moderator, parent, replies), PostModel actualizado
    - [x] **Mapper**: CommentMapper para conversión bidireccional Domain ↔ Eloquent
    - [x] **Repositorio**: EloquentCommentRepository con implementación de Recursive CTE en getCommentTree()
    - [x] **DTOs**: CreateCommentDTO, CreateAnonymousCommentDTO (con turnstileToken)
    - [x] **Use Cases** (8 implementados):
      - CreateCommentFromUserUseCase (auto-aprobado)
      - CreateAnonymousCommentUseCase (pending approval)
      - ApproveCommentUseCase
      - RejectCommentUseCase
      - MarkCommentAsSpamUseCase
      - GetCommentTreeUseCase
      - GetPendingCommentsUseCase
      - DeleteCommentUseCase
    - [x] **Requests**: CreateCommentRequest, CreateAnonymousCommentRequest (con Turnstile), ModerateCommentRequest
    - [x] **Controllers**: CommentController (público), AdminCommentController (admin)
    - [x] **Rutas API**: GET/POST /api/posts/{slug}/comments, POST /api/posts/{slug}/comments/anonymous, endpoints admin
    - [x] **Rate Limiting**: 3/min (producción), 15/min (desarrollo)
    - [x] **Service Providers**: Bindings en BlogServiceProvider, rate limiter en AppServiceProvider
    - [x] **Total**: 46 archivos implementados desde Domain hasta Presentation layer
  - [x] [Tarea 3.3: Sistema de Moderación](./task/tarea-3-3-sistema-de-moderacion.md)
    - [x] Plan de acción: [plan-tarea-3-3-sistema-de-moderacion.md](./action-plans/plan-tarea-3-3-sistema-de-moderacion.md)
    - [x] **ModerationService**: Servicio de aplicación con reglas configurables
    - [x] **Notificaciones Email**: NewCommentPendingNotification + SendNewCommentNotification listener
    - [x] **Use Cases Bulk** (3 implementados):
      - BulkApproveCommentsUseCase
      - BulkRejectCommentsUseCase
      - BulkDeleteCommentsUseCase
    - [x] **Endpoints Bulk**: POST /api/admin/comments/bulk-approve, bulk-reject, bulk-delete
    - [x] **Configuración**: config/moderation.php con reglas personalizables
    - [x] **Event Listener**: Registrado en AppServiceProvider
    - [x] **Service Bindings**: ModerationService en BlogServiceProvider
    - [x] **Total**: 11 archivos (7 nuevos + 4 modificados)
  - [x] [Tarea 3.4: Comentarios Anidados](./task/tarea-3-4-comentarios-anidados.md)
    - [x] Soporte completo implementado con Recursive CTE en backend
    - [x] Frontend soporta comentarios anidados (parent_id)
  - [x] [Tarea 3.5: Seguridad y Anti-spam](./task/tarea-3-5-seguridad-y-anti-spam.md)
    - [x] Plan de acción: [plan-tarea-3-5-seguridad-y-anti-spam.md](./action-plans/plan-tarea-3-5-seguridad-y-anti-spam.md)
    - [x] **CommentSecurityLogger**: Servicio para logging de eventos de seguridad
    - [x] **Logging integrado en Use Cases**:
      - CreateAnonymousCommentUseCase (spam y contenido sospechoso)
      - CreateCommentFromUserUseCase (spam de usuarios registrados)
    - [x] **Rate Limiter Logging**: Log cuando se excede límite de 3/min
    - [x] **Eventos de seguridad loggeados**:
      - comment_spam_attempt (spam detectado)
      - comment_suspicious_content (contenido de baja calidad)
      - comment_rate_limit_exceeded (límite excedido)
      - comment_turnstile_failure (validación Turnstile fallida)
      - comment_multiple_rejections (múltiples rechazos de misma IP)
      - comment_deleted (auditoría de eliminaciones)
      - comment_bulk_* (acciones masivas)
    - [x] **Service Bindings**: CommentSecurityLogger en BlogServiceProvider
    - [x] **Total**: 5 archivos (1 nuevo + 4 modificados)
    - [x] **Nota**: Turnstile (3.5.1), Rate limiting (3.5.2), Filtros anti-spam (3.5.3) y Sanitización HTML (3.5.4) ya estaban implementados en Tareas 3.1-3.2
  - [x] [Tarea 3.6: Frontend - Sistema de Comentarios](./task/tarea-3-6-frontend-sistema-de-comentarios.md)
    - [x] **Domain Layer**: Comment entity, Value Objects (CommentId, CommentContent, CommentStatus, CommentAuthorType, AnonymousAuthor), Repository interface, Types
    - [x] **Application Layer**: 12 Use Cases (create, approve, reject, spam, delete, bulk operations)
    - [x] **Infrastructure Layer**: CommentAPI client, HttpCommentRepository
    - [x] **Interface - Components**: 8 componentes (CommentForm, CommentItem, CommentTree, CommentsList, CommentReplyForm, CommentModerationPanel, CommentModerationItem, CommentBulkActions)
    - [x] **Interface - Composables**: useComments, useCommentModeration (completamente conectados al service container)
    - [x] **Interface - Store**: comment.store.ts (Pinia)
    - [x] **Service Container**: Bindings configurados en bindings.ts con todos los Use Cases
    - [x] **Integration**: CommentsList integrado en página pública de post
    - [x] **Admin Page**: /admin/comments/index.vue creada y funcional
    - [x] **Turnstile**: Configuración verificada en nuxt.config.ts
    - [x] **TypeScript**: Todos los errores de tipo resueltos, typecheck pasa exitosamente
    - [x] **Dependencies**: dompurify instalado para sanitización HTML
    - [x] **Total**: ~35 archivos implementados
    - [x] **Estado**: ✅ Sistema completamente implementado y listo para testing funcional con backend.

**🎯 RESULTADO:** Sistema de comentarios completo con moderación, soporte para usuarios registrados y anónimos, protección anti-spam, y panel de administración.

**✅ LOGROS:**
- Comentarios anidados con árbol recursivo
- Moderación masiva (bulk approve/reject/delete)
- Rate limiting y Turnstile para prevenir spam
- Sanitización HTML con DOMPurify
- Arquitectura limpia end-to-end (Domain → Application → Infrastructure → Presentation)
- TypeScript type-safe en todo el frontend

## ✅ Sprint 7.5: Mejoras UX y Clean Architecture - Sistema de Modales y Notificaciones (COMPLETADO)

- [x] **Tarea Técnica:** Sistema de Modales y Abstracción de Notificaciones (Frontend)
  - [x] Plan de acción: [sprint-7-5-sistema-modales.md](./action-plans/sprint-7-5-sistema-modales.md)

  **Parte 1: Sistema de Modales** ✅
  - [x] **Objetivo**: Reemplazar `alert()` y `confirm()` nativos por modales personalizados
  - [x] **Componentes creados**:
    - BaseModal.vue (componente base con Teleport, animaciones, accesibilidad)
    - ConfirmModal.vue (confirmaciones con 4 variantes)
    - AlertModal.vue (información/alertas)
    - ModalContainer.vue (contenedor global integrado en app.vue)
  - [x] **Composable**: useModal.ts con API Promise-based
  - [x] **Características implementadas**:
    - Diseño coherente con Tailwind CSS y dark mode
    - Accesibilidad (ARIA labels, keyboard navigation, ESC key)
    - Animaciones suaves (Vue Transition API)
    - Variantes: success, error, warning, info, danger
    - Backdrop clickable para cerrar
    - TypeScript completamente tipado
  - [x] **Refactorización**: 4 archivos actualizados (PostCard.vue, CommentModerationItem.vue, CommentBulkActions.vue)

  **Parte 2: Abstracción NotificationService (Clean Architecture)** ✅
  - [x] **Problema resuelto**: Eliminado acoplamiento directo a `vue-toastification`
  - [x] **Arquitectura implementada**: Clean Architecture respetada
  - [x] **Capas implementadas**:
    - Application Layer: NotificationServiceInterface (contrato)
    - Infrastructure Layer: ToastNotificationService (implementación)
    - Interface Layer: useNotification() composable
  - [x] **Service Container**: Registrado en plugins/container.client.ts
  - [x] **Refactorización completada**: pages/admin/posts/create.vue actualizado
  - [x] **Beneficio logrado**: Librería completamente intercambiable sin cambiar componentes

  - [x] **Tiempo real**: ~4 horas

**🎯 RESULTADO ALCANZADO:**
- ✅ Sistema de modales profesional funcionando
- ✅ 0 referencias a `alert()` o `confirm()` nativos
- ✅ 0 referencias directas a `$toast`
- ✅ Arquitectura desacoplada siguiendo Clean Architecture
- ✅ TypeScript typecheck pasando sin errores
- ✅ Código mantenible y fácilmente testeable

**✅ LOGROS:**
- 4 componentes Vue creados (BaseModal, ConfirmModal, AlertModal, ModalContainer)
- 2 composables creados (useModal, useNotification)
- 1 interfaz de servicio (NotificationServiceInterface)
- 1 implementación de servicio (ToastNotificationService)
- 5 archivos refactorizados para usar las nuevas abstracciones
- Base sólida para futuras implementaciones de UI

**📋 CONTEXTO:** Deuda técnica UX + Arquitectura completamente saldada. El proyecto ahora tiene un sistema de UI modular y desacoplado.

## Sprint 8: Multi-idioma

- [ ] **Historia de Usuario:** [Historia 4: Multiidioma](./HU/historia-4-multiidioma.md)
  - [ ] [Tarea 4.1: Configuración Base Multiidioma](./task/tarea-4-1-configuracion-base-multiidioma.md)
  - [ ] [Tarea 4.2: Traducción de Contenido (Backend)](./task/tarea-4-2-traduccion-de-contenido-backend.md)
  - [ ] [Tarea 4.3: SEO Multiidioma](./task/tarea-4-3-seo-multiidioma.md)
  - [ ] [Tarea 4.4: Traducción de Interface](./task/tarea-4-4-traduccion-de-interface.md)
  - [ ] [Tarea 4.5: Gestión de Contenido Multiidioma](./task/tarea-4-5-gestion-de-contenido-multiidioma.md)

## Sprint 9: Newsletter y Páginas Estáticas

- [ ] **Historia de Usuario:** [Historia 5: Newsletter Básica](./HU/historia-5-newsletter-basica.md)
  - [ ] [Tarea 5.1: Dominio Newsletter (Backend)](./task/tarea-5-1-dominio-newsletter-backend.md)
  - [ ] [Tarea 5.2: Gestión de Suscriptores](./task/tarea-5-2-gestion-de-suscriptores.md)
  - [ ] [Tarea 5.3: Envío Manual de Newsletters](./task/tarea-5-3-envio-manual-de-newsletters.md)
  - [ ] [Tarea 5.4: Seguridad y Validaciones](./task/tarea-5-4-seguridad-y-validaciones.md)
  - [ ] [Tarea 5.5: Frontend Newsletter](./task/tarea-5-5-frontend-newsletter.md)
- [ ] **Historia de Usuario:** [Historia 6: Páginas Estáticas](./HU/historia-6-paginas-estaticas.md)
  - [ ] [Tarea 6.1: Dominio de Páginas (Backend)](./task/tarea-6-1-dominio-de-paginas-backend.md)
  - [ ] [Tarea 6.2: Editor de Páginas](./task/tarea-6-2-editor-de-paginas.md)
  - [ ] [Tarea 6.3: Páginas Por Defecto](./task/tarea-6-3-paginas-por-defecto.md)
  - [ ] [Tarea 6.4: Frontend Páginas Estáticas](./task/tarea-6-4-frontend-paginas-estaticas.md)

## Sprint 10: Media, SEO y Performance

- [ ] **Historia de Usuario:** [Historia 7: Gestión de Medios](./HU/historia-7-gestion-de-medios.md)
  - [ ] [Tarea 7.1: Infraestructura AWS S3](./task/tarea-7-1-infraestructura-aws-s3.md)
  - [ ] [Tarea 7.2: Dominio de Medios (Backend)](./task/tarea-7-2-dominio-de-medios-backend.md)
  - [ ] [Tarea 7.3: Procesamiento de Imágenes](./task/tarea-7-3-procesamiento-de-imagenes.md)
  - [ ] [Tarea 7.4: API de Medios](./task/tarea-7-4-api-de-medios.md)
  - [ ] [Tarea 7.5: Frontend Gestión de Medios](./task/tarea-7-5-frontend-gestion-de-medios.md)
- [ ] **Historia de Usuario:** [Historia 8: SEO y Performance](./HU/historia-8-seo-y-performance.md)
  - [ ] [Tarea 8.1: SEO On-Page](./task/tarea-8-1-seo-on-page.md)
  - [ ] [Tarea 8.2: Structured Data](./task/tarea-8-2-structured-data.md)
  - [ ] [Tarea 8.3: Performance Optimization](./task/tarea-8-3-performance-optimization.md)
  - [ ] [Tarea 8.4: Caching Strategy](./task/tarea-8-4-caching-strategy.md)

## Sprint 11: Seguridad y Operaciones

- [ ] **Historia de Usuario:** [Historia 9: Seguridad](./HU/historia-9-seguridad.md)
  - [ ] [Tarea 9.1: Seguridad Backend](./task/tarea-9-1-seguridad-backend.md)
  - [ ] [Tarea 9.2: Validación y Sanitización](./task/tarea-9-2-validacion-y-sanitizacion.md)
  - [ ] [Tarea 9.3: Monitoring y Logging](./task/tarea-9-3-monitoring-y-logging.md)
- [ ] **Historia de Usuario:** [Historia 10: Backup Automático y Health Checks](./HU/historia-10-backup-automatico-y-health-checks.md)
  - [ ] [Tarea 10.1: Sistema de Backup (Backend)](./task/tarea-10-1-sistema-de-backup-backend.md)
  - [ ] [Tarea 10.2: Health Monitoring](./task/tarea-10-2-health-monitoring.md)
  - [ ] [Tarea 10.3: Logging y Monitoring](./task/tarea-10-3-logging-y-monitoring.md)
  - [ ] [Tarea 10.4: Restauración Manual](./task/tarea-10-4-restauracion-manual.md)

## Sprint 12: Taxonomía

- [ ] **Historia de Usuario:** [Historia 11: Categorías y Tags](./HU/historia-11-categorias-y-tags.md)
  - [ ] [Tarea 11.1: Dominio de Taxonomía (Backend)](./task/tarea-11-1-dominio-de-taxonomia-backend.md)
  - [ ] [Tarea 11.2: Gestión de Categorías](./task/tarea-11-2-gestion-de-categorias.md)
  - [ ] [Tarea 11.3: Sistema de Tags](./task/tarea-11-3-sistema-de-tags.md)
  - [ ] [Tarea 11.4: Relaciones Post-Taxonomía](./task/tarea-11-4-relaciones-post-taxonomia.md)
  - [ ] [Tarea 11.5: Páginas de Archivo](./task/tarea-11-5-paginas-de-archivo.md)
  - [ ] [Tarea 11.6: Frontend Taxonomía](./task/tarea-11-6-frontend-taxonomia.md)

## Sprint 13: Sindicación y SEO Avanzado

- [ ] **Historia de Usuario:** [Historia 12: RSS Feeds](./HU/historia-12-rss-feeds.md)
  - [ ] [Tarea 12.1: Generación de RSS (Backend)](./task/tarea-12-1-generacion-de-rss-backend.md)
  - [ ] [Tarea 12.2: Endpoints RSS](./task/tarea-12-2-endpoints-rss.md)
  - [ ] [Tarea 12.3: Optimización de Feeds](./task/tarea-12-3-optimizacion-de-feeds.md)
  - [ ] [Tarea 12.4: Auto-discovery y Metadatos](./task/tarea-12-4-auto-discovery-y-metadatos.md)
  - [ ] [Tarea 12.5: Frontend RSS Integration](./task/tarea-12-5-frontend-rss-integration.md)
- [ ] **Historia de Usuario:** [Historia 13: SEO Avanzado y Redirects](./HU/historia-13-seo-avanzado-y-redirects.md)
  - [ ] [Tarea 13.1: Gestión de Redirects](./task/tarea-13-1-gestion-de-redirects.md)
  - [ ] [Tarea 13.2: Análisis SEO Automático](./task/tarea-13-2-analisis-seo-automatico.md)
  - [ ] [Tarea 13.3: Structured Data Avanzado](./task/tarea-13-3-structured-data-avanzado.md)
  - [ ] [Tarea 13.4: Enlaces y Navegación](./task/tarea-13-4-enlaces-y-navegacion.md)
  - [ ] [Tarea 13.5: Reportes SEO](./task/tarea-13-5-reportes-seo.md)

## Sprint 14: Theming y Automatización de Newsletter

- [ ] **Historia de Usuario:** [Historia 14: Modo Oscuro](./HU/historia-14-modo-oscuro.md)
  - [ ] [Tarea 14.1: Sistema de Theming (Frontend)](./task/tarea-14-1-sistema-de-theming-frontend.md)
  - [ ] [Tarea 14.2: Componentes UI Adaptativos](./task/tarea-14-2-componentes-ui-adaptativos.md)
  - [ ] [Tarea 14.3: Editor y Contenido](./task/tarea-14-3-editor-y-contenido.md)
  - [ ] [Tarea 14.4: Admin Panel](./task/tarea-14-4-admin-panel.md)
  - [ ] [Tarea 14.5: Optimización y Performance](./task/tarea-14-5-optimizacion-y-performance.md)
- [ ] **Historia de Usuario:** [Historia 15: Newsletter Automation](./HU/historia-15-newsletter-automation.md)
  - [ ] [Tarea 15.1: Sistema de Templates](./task/tarea-15-1-sistema-de-templates.md)
  - [ ] [Tarea 15.2: Automatización de Envíos](./task/tarea-15-2-automatizacion-de-envios.md)
  - [ ] [Tarea 15.3: Segmentación de Audiencia](./task/tarea-15-3-segmentacion-de-audiencia.md)
  - [ ] [Tarea 15.4: Analytics y Testing](./task/tarea-15-4-analytics-y-testing.md)
  - [ ] [Tarea 15.5: Bounce y Cleanup Automático](./task/tarea-15-5-bounce-y-cleanup-automatico.md)

## Sprint 15: Contenido Avanzado

- [ ] **Historia de Usuario:** [Historia 16: Programación de Posts](./HU/historia-16-programacion-de-posts.md)
  - [ ] [Tarea 16.1: Scheduling Core (Backend)](./task/tarea-16-1-scheduling-core-backend.md)
  - [ ] [Tarea 16.2: Interface de Programación](./task/tarea-16-2-interface-de-programacion.md)
  - [ ] [Tarea 16.3: Notificaciones y Alerts](./task/tarea-16-3-notificaciones-y-alerts.md)
  - [ ] [Tarea 16.4: Social Media Integration](./task/tarea-16-4-social-media-integration.md)
  - [ ] [Tarea 16.5: Gestión de Errores](./task/tarea-16-5-gestion-de-errores.md)
- [ ] **Historia de Usuario:** [Historia 18: Filtros Automáticos de Comentarios](./HU/historia-18-filtros-automaticos-de-comentarios.md)
  - [ ] [Tarea 18.1: Integración Akismet](./task/tarea-18-1-integracion-akismet.md)
  - [ ] [Tarea 18.2: Filtros de Contenido](./task/tarea-18-2-filtros-de-contenido.md)
  - [ ] [Tarea 18.3: Machine Learning Básico](./task/tarea-18-3-machine-learning-basico.md)
  - [ ] [Tarea 18.4: Sistema de Quarantine](./task/tarea-18-4-sistema-de-quarantine.md)
  - [ ] [Tarea 18.5: Reportes y Tuning](./task/tarea-18-5-reportes-y-tuning.md)

## Sprint 16: Performance y Búsqueda

- [ ] **Historia de Usuario:** [Historia 17: Cache con Redis y Laravel Horizon](./HU/historia-17-cache-con-redis-y-laravel-horizon.md)
  - [ ] [Tarea 17.1: Configuración Redis](./task/tarea-17-1-configuracion-redis.md)
  - [ ] [Tarea 17.2: Cache Strategy Avanzado](./task/tarea-17-2-cache-strategy-avanzado.md)
  - [ ] [Tarea 17.3: Laravel Horizon Setup](./task/tarea-17-3-laravel-horizon-setup.md)
  - [ ] [Tarea 17.4: Queue Optimization](./task/tarea-17-4-queue-optimization.md)
  - [ ] [Tarea 17.5: Monitoring y Métricas](./task/tarea-17-5-monitoring-y-metricas.md)
- [ ] **Historia de Usuario:** [Historia 19: Búsqueda Avanzada](./HU/historia-19-busqueda-avanzada.md)
  - [ ] [Tarea 19.1: Full-Text Search Backend](./task/tarea-19-1-full-text-search-backend.md)
  - [ ] [Tarea 19.2: Filtros y Operadores](./task/tarea-19-2-filtros-y-operadores.md)
  - [ ] [Tarea 19.3: Indexing y Performance](./task/tarea-19-3-indexing-y-performance.md)
  - [ ] [Tarea 19.4: Frontend de Búsqueda](./task/tarea-19-4-frontend-de-busqueda.md)
  - [ ] [Tarea 19.5: Analytics de Búsqueda](./task/tarea-19-5-analytics-de-busqueda.md)

## Sprint 17: Escalabilidad y Monitoreo

- [ ] **Historia de Usuario:** [Historia 20: CDN Completo y Critical CSS](./HU/historia-20-cdn-completo-y-critical-css.md)
  - [ ] [Tarea 20.1: CloudFlare CDN Setup](./task/tarea-20-1-cloudflare-cdn-setup.md)
  - [ ] [Tarea 20.2: Critical CSS Automation](./task/tarea-20-2-critical-css-automation.md)
  - [ ] [Tarea 20.3: Advanced Image Optimization](./task/tarea-20-3-advanced-image-optimization.md)
  - [ ] [Tarea 20.4: Resource Optimization](./task/tarea-20-4-resource-optimization.md)
  - [ ] [Tarea 20.5: Service Worker Implementation](./task/tarea-20-5-service-worker-implementation.md)
- [ ] **Historia de Usuario:** [Historia 21: Monitoreo y Logs Estructurados](./HU/historia-21-monitoreo-y-logs-estructurados.md)
  - [ ] [Tarea 21.1: Structured Logging](./task/tarea-21-1-structured-logging.md)
  - [ ] [Tarea 21.2: Application Metrics](./task/tarea-21-2-application-metrics.md)
  - [ ] [Tarea 21.3: User Activity Logging](./task/tarea-21-3-user-activity-logging.md)
  - [ ] [Tarea 21.4: Real-time Monitoring Dashboard](./task/tarea-21-4-real-time-monitoring-dashboard.md)
  - [ ] [Tarea 21.5: Log Analysis y Trending](./task/tarea-21-5-log-analysis-y-trending.md)

## Sprint 18: Performance y Futuro

- [ ] **Historia de Usuario:** [Historia 22: Performance Tuning Avanzado](./HU/historia-22-performance-tuning-avanzado.md)
  - [ ] [Tarea 22.1: Database Optimization](./task/tarea-22-1-database-optimization.md)
  - [ ] [Tarea 22.2: Application Performance](./task/tarea-22-2-application-performance.md)
  - [ ] [Tarea 22.3: Frontend Performance](./task/tarea-22-3-frontend-performance.md)
  - [ ] [Tarea 22.4: Load Testing y Benchmarking](./task/tarea-22-4-load-testing-y-benchmarking.md)
  - [ ] [Tarea 22.5: Scaling Preparation](./task/tarea-22-5-scaling-preparation.md)
- [ ] **Historia de Usuario:** [Historia 23: Mobile App API Preparation](./HU/historia-23-mobile-app-api-preparation.md)
  - [ ] [Tarea 23.1: API Versioning](./task/tarea-23-1-api-versioning.md)
  - [ ] [Tarea 23.2: Mobile-Optimized Authentication](./task/tarea-23-2-mobile-optimized-authentication.md)
  - [ ] [Tarea 23.3: Offline-First API Design](./task/tarea-23-3-offline-first-api-design.md)
  - [ ] [Tarea 23.4: Push Notifications Infrastructure](./task/tarea-23-4-push-notifications-infrastructure.md)
  - [ ] [Tarea 23.5: Mobile-Specific Optimizations](./task/tarea-23-5-mobile-specific-optimizations.md)

## Sprint 19: Analytics y Futuro Lejano

- [ ] **Historia de Usuario:** [Historia 24: Advanced Analytics](./HU/historia-24-advanced-analytics.md)
  - [ ] [Tarea 24.1: Google Analytics 4 Integration](./task/tarea-24-1-google-analytics-4-integration.md)
  - [ ] [Tarea 24.2: Custom Analytics Backend](./task/tarea-24-2-custom-analytics-backend.md)
  - [ ] [Tarea 24.3: Content Performance Analytics](./task/tarea-24-3-content-performance-analytics.md)
  - [ ] [Tarea 24.4: User Behavior Analytics](./task/tarea-24-4-user-behavior-analytics.md)
  - [ ] [Tarea 24.5: Reporting y Dashboards](./task/tarea-24-5-reporting-y-dashboards.md)
- [ ] **Historia de Usuario:** [Historia 25: PWA Capabilities](./HU/historia-25-pwa-capabilities.md)
- [ ] **Historia de Usuario:** [Historia 26: Premium Content Structure](./HU/historia-26-premium-content-structure.md)
- [ ] **Historia de Usuario:** [Historia 27: Editor Colaborativo Real-time](./HU/historia-27-editor-colaborativo-real-time.md)
- [ ] **Historia de Usuario:** [Historia 28: Multi-tenant Architecture](./HU/historia-28-multi-tenant-architecture.md)