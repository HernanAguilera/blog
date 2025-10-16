**Depende de:** Historia 2

### **Historia 3: Sistema de Comentarios con Moderación**

**Como** visitante del blog  
**Quiero** poder comentar en los posts  
**Para** interactuar y generar discusión sobre el contenido

**Como** administrador  
**Quiero** moderar comentarios antes de su publicación  
**Para** mantener la calidad y evitar spam

**Descripción detallada:** Sistema de comentarios que permite participación tanto de usuarios registrados como anónimos, con sistema de moderación manual y medidas anti-spam.

**📋 Decisión de Diseño:** Ver [comment-system-anonymous-users.md](../design-decisions/comment-system-anonymous-users.md) para entender por qué NO se verifica email de anónimos y las limitaciones aceptadas para MVP.

**Criterios de aceptación:**

- 3.1 Comentarios de usuarios registrados (auto-aprobados)
- 3.2 Comentarios de usuarios anónimos (requieren nombre y email)
- 3.3 Estados: pending_approval, approved, rejected
- 3.4 Panel de moderación ordenado por fecha (más antiguos primero)
- 3.5 Cloudflare Turnstile en formulario de comentarios
- 3.6 Rate limiting: máximo 3 comentarios por minuto
- 3.7 Validación de email válido para comentarios anónimos
- 3.8 Sanitización de HTML en comentarios
- 3.9 Notificación email al admin cuando hay comentarios pendientes
- 3.10 Respuestas simples a comentarios (1 nivel de anidación)
