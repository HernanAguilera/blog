**Historia de Usuario:** HISTORIA 3: SISTEMA DE COMENTARIOS CON MODERACIÓN

#### **Tarea 3.6: Frontend - Sistema de Comentarios**

- 3.6.1 Crear entidades Comment (frontend)
- 3.6.2 Implementar HttpCommentRepository
- 3.6.3 Crear casos de uso para comentarios (frontend)
- 3.6.4 Desarrollar CommentForm para nuevos comentarios
- 3.6.5 Crear CommentsList con threading visual
- 3.6.6 Implementar panel de moderación para admins
- 3.6.7 Crear composable useComments()

**Flujo de Comentario:**

```
Usuario escribe comentario → CommentForm → Turnstile validation → 
CreateCommentUseCase → Rate limiting check → Comment entity creado → 
Status: pending_approval → Email notification al admin → 
Admin modera → ApproveCommentUseCase → Comment visible públicamente
```
