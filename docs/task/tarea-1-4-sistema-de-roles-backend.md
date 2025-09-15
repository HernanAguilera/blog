**Historia de Usuario:** HISTORIA 1: AUTENTICACIÓN Y GESTIÓN DE USUARIOS

#### **Tarea 1.4: Sistema de Roles (Backend)**

- 1.4.1 ✅ Implementar Permission enum con constantes escalables
- 1.4.2 ✅ Usar migración existente (campo role en users table)
- 1.4.3 ✅ Implementar UserRole value object con método can(Permission) genérico
- 1.4.4 ✅ Crear seeders para roles: SuperAdmin, Admin, Colaborador, Invitado
- 1.4.5 ✅ Crear PermissionMiddleware para autorización granular

**Decisión Arquitectónica:** Sistema custom en lugar de Spatie Permission para mayor simplicidad, performance y escalabilidad controlada. La solución actual permite agregar/modificar permisos sin cambios en BD o middleware.
