**Historia de Usuario:** HISTORIA 5: NEWSLETTER BÁSICA

#### **Tarea 5.5: Frontend Newsletter**

- 5.5.1 Crear entidades Newsletter (frontend)
- 5.5.2 Desarrollar NewsletterForm con validaciones
- 5.5.3 Implementar HttpNewsletterRepository
- 5.5.4 Crear composable useNewsletter()
- 5.5.5 Desarrollar página de confirmación y unsuscribe
- 5.5.6 Crear panel admin para gestión de newsletters

**Flujo de Newsletter:**

```
Usuario ingresa email → NewsletterForm → Turnstile validation → 
SubscribeToNewsletterUseCase → Email verification enviado → 
Usuario confirma → Status: active → Admin envía newsletter manual → 
SendManualNewsletterUseCase → Queue job → Email enviado a todos los activos
```
