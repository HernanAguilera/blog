**Historia de Usuario:** HISTORIA 1: AUTENTICACIÓN Y GESTIÓN DE USUARIOS

#### **Tarea 1.6: Frontend - Sistema de Autenticación**

- 1.6.1 Crear entidades de dominio User (frontend)
- 1.6.2 Implementar HttpUserRepository
- 1.6.3 Crear casos de uso LoginUseCase y RegisterUserUseCase (frontend)
- 1.6.4 Implementar AuthStore con Pinia
- 1.6.5 Crear composable useAuth()
- 1.6.6 Desarrollar componentes LoginForm y RegisterForm
- 1.6.7 Implementar middleware de autenticación para rutas
- 1.6.8 Crear páginas de login y registro

**Flujo de Autenticación:**

```
Usuario ingresa credenciales → LoginForm → useAuth.login() → 
LoginUseCase → HttpUserRepository → API /auth/login → 
AuthController → LoginUserUseCase → UserRepository → 
JWT Token generado → Respuesta al frontend → 
Token guardado en TokenStorage → Usuario autenticado
```
