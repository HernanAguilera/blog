**Depende de:** Ninguna

### **Historia 1: Autenticación y Gestión de Usuarios**

**Como** administrador del blog  
**Quiero** tener un sistema de autenticación flexible con múltiples opciones  
**Para** poder acceder de manera segura y permitir que otros usuarios comenten

**Descripción detallada:** Sistema completo de autenticación que soporte tanto registro tradicional como OAuth social. Incluye gestión de roles con permisos específicos y la capacidad de activar/desactivar métodos de autenticación según necesidades.

**Criterios de aceptación:**

- 1.1 Registro tradicional con email/password funcional
- 1.2 Login tradicional con email/password funcional
- 1.3 OAuth Google implementado y funcional
- 1.4 OAuth Facebook implementado y funcional
- 1.5 OAuth Twitter/X implementado y funcional
- 1.6 Panel admin para activar/desactivar cada provider OAuth
- 1.7 Sistema de roles: SuperAdmin, Admin, Colaborador, Invitado
- 1.8 SuperAdmin creado automáticamente via seeder, desactivado por defecto
- 1.9 Rate limiting: máximo 5 intentos de login por minuto
- 1.10 Cloudflare Turnstile integrado en formularios de registro/login
- 1.11 Logout funcional con invalidación de tokens
- 1.12 Recuperación de contraseña vía email
