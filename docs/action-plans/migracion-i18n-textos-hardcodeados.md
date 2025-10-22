# Plan de Acción: Migración de Textos Hardcodeados a i18n

**Estimación de tiempo:** 2-3 horas

**Estado:** 🟡 EN PROGRESO (Fase 1 completada)

**Objetivo:** Migrar todos los textos hardcodeados en español de los componentes y páginas a archivos de traducción i18n para soportar español, inglés y portugués.

---

## ✅ Fase 1 Completada: Estructura de Traducciones (es.json)

Se ha creado la estructura completa de traducciones en español con todas las claves necesarias en `frontend/locales/es.json`:

### Secciones agregadas:
- ✅ `common.*` - Textos comunes (anonymous, noTitle, characters, yes, no)
- ✅ `auth.*` - Autenticación completa (login, register, passwords, validaciones)
- ✅ `posts.*` - Posts completos (editor, lista, card, placeholders, confirmaciones)
- ✅ `comments.*` - Comentarios completos (form, tree, item, moderación)
- ✅ `pages.*` - Páginas estáticas (crear, eliminar, errores)
- ✅ `welcome.*` - Página de bienvenida
- ✅ `admin.*` - Secciones de administración
- ✅ `profile.*` - Perfil de usuario
- ✅ `validation.*` - Todas las validaciones necesarias

**Total de claves agregadas:** ~120 claves nuevas

---

## 📋 Fase 2: Traducir en.json y pt.json

### Paso 2.1: Actualizar frontend/locales/en.json

**Instrucciones:**
1. Abrir `frontend/locales/en.json`
2. Copiar la estructura de `frontend/locales/es.json`
3. Traducir TODOS los valores al inglés, manteniendo las claves idénticas
4. Mantener las interpolaciones como `{query}`, `{author}`, `{min}`, `{max}`, `{error}`, `{title}`, `{count}`, `{seconds}`

**Ejemplo de traducción:**

```json
"auth": {
  "login": "Log in",
  "register": "Sign up",
  "logout": "Log out",
  "loggingIn": "Logging in...",
  "creating": "Creating...",
  "creatingAccount": "Creating account...",
  "password": "Password",
  "passwordPlaceholder": "Your password",
  "passwordRepeat": "Repeat your password",
  "passwordRepeatPlaceholder": "Repeat your password",
  "passwordMinChars": "Minimum 8 characters",
  "rememberMe": "Remember me",
  "forgotPassword": "Forgot your password?",
  "orContinueWith": "Or continue with",
  "alreadyHaveAccount": "Already have an account?",
  "loginTitle": "Log In | BlogV2",
  "loginDescription": "Log in to your BlogV2 account...",
  "forgotPasswordTitle": "Recover Password | BlogV2",
  "forgotPasswordDescription": "Recover your BlogV2 password...",
  "passwordStrength": {
    "weak": "Weak",
    "fair": "Fair",
    "good": "Good",
    "excellent": "Excellent"
  }
}
```

### Paso 2.2: Actualizar frontend/locales/pt.json

**Instrucciones:**
1. Abrir `frontend/locales/pt.json`
2. Copiar la estructura de `frontend/locales/es.json`
3. Traducir TODOS los valores al portugués (brasileño), manteniendo las claves idénticas
4. Mantener las interpolaciones

**Ejemplo de traducción:**

```json
"auth": {
  "login": "Entrar",
  "register": "Criar conta",
  "logout": "Sair",
  "loggingIn": "Entrando...",
  "creating": "Criando...",
  "creatingAccount": "Criando conta...",
  "password": "Senha",
  "passwordPlaceholder": "Sua senha",
  "passwordRepeat": "Repita sua senha",
  "passwordRepeatPlaceholder": "Repita sua senha",
  "passwordMinChars": "Mínimo 8 caracteres",
  "rememberMe": "Lembrar-me",
  "forgotPassword": "Esqueceu sua senha?",
  "orContinueWith": "Ou continue com",
  "alreadyHaveAccount": "Já tem uma conta?",
  "loginTitle": "Entrar | BlogV2",
  "loginDescription": "Entre na sua conta BlogV2...",
  "forgotPasswordTitle": "Recuperar Senha | BlogV2",
  "forgotPasswordDescription": "Recupere sua senha do BlogV2...",
  "passwordStrength": {
    "weak": "Fraca",
    "fair": "Regular",
    "good": "Boa",
    "excellent": "Excelente"
  }
}
```

---

## 📋 Fase 3: Migrar Componentes

### Categoría 1: Componentes de Autenticación

#### 3.1: LoginForm.vue
**Ubicación:** `frontend/interface/components/auth/LoginForm.vue`

**Cambios a realizar:**

```vue
<!-- ANTES -->
<input placeholder="Tu contraseña" />
{{ isSubmitting ? 'Iniciando sesión...' : 'Iniciar sesión' }}
'Formato de email inválido'
'La contraseña es requerida'
'La contraseña debe tener al menos 6 caracteres'
'Recordarme'
'¿Olvidaste tu contraseña?'
'O continúa con'

<!-- DESPUÉS -->
<input :placeholder="$t('auth.passwordPlaceholder')" />
{{ isSubmitting ? $t('auth.loggingIn') : $t('auth.login') }}
$t('validation.emailInvalid')
$t('validation.passwordRequired')
$t('validation.passwordMinLength6')
$t('auth.rememberMe')
$t('auth.forgotPassword')
$t('auth.orContinueWith')
```

#### 3.2: RegisterForm.vue
**Ubicación:** `frontend/interface/components/auth/RegisterForm.vue`

**Cambios a realizar:**

```vue
<!-- ANTES -->
placeholder="Mínimo 8 caracteres"
placeholder="Repite tu contraseña"
'Débil', 'Regular', 'Buena', 'Excelente'
'Formato de email inválido'
'La contraseña es requerida'
'La contraseña debe tener al menos 8 caracteres'
{{ isSubmitting ? 'Creando cuenta...' : 'Crear cuenta' }}
'¿Ya tienes una cuenta?'

<!-- DESPUÉS -->
:placeholder="$t('auth.passwordMinChars')"
:placeholder="$t('auth.passwordRepeatPlaceholder')"
$t('auth.passwordStrength.weak'), $t('auth.passwordStrength.fair'),
$t('auth.passwordStrength.good'), $t('auth.passwordStrength.excellent')
$t('validation.emailInvalid')
$t('validation.passwordRequired')
$t('validation.passwordMinLength8')
{{ isSubmitting ? $t('auth.creatingAccount') : $t('auth.register') }}
$t('auth.alreadyHaveAccount')
```

---

### Categoría 2: Componentes de Posts

#### 3.3: PostEditor.vue
**Ubicación:** `frontend/interface/components/posts/PostEditor.vue`

**Cambios a realizar:**

```vue
<!-- ANTES -->
placeholder="Título del post..."
placeholder="Meta descripción para SEO (opcional, mínimo 50 caracteres)..."
{{ metaDescription.length }}/160 caracteres
'Mínimo 50 caracteres para SEO óptimo'
'Longitud óptima para SEO'
placeholder: 'Escribe tu contenido aquí...'

<!-- DESPUÉS -->
:placeholder="$t('posts.titlePlaceholder')"
:placeholder="$t('posts.metaDescriptionPlaceholder')"
{{ metaDescription.length }}/160 {{ $t('common.characters') }}
$t('posts.metaDescriptionMinLength')
$t('posts.metaDescriptionOptimal')
placeholder: $t('posts.contentPlaceholder')
```

#### 3.4: PostList.vue
**Ubicación:** `frontend/interface/components/posts/PostList.vue`

**Cambios a realizar:**

```vue
<!-- ANTES -->
placeholder="Buscar por título o contenido..."
'No se encontraron posts'
'No hay posts aún'
'Intenta cambiar los filtros de búsqueda.'
'Comienza creando tu primer post.'

<!-- DESPUÉS -->
:placeholder="$t('posts.searchByTitleOrContent')"
$t('posts.noPostsWithSearch')
$t('posts.noPosts')
$t('posts.tryChangingFilters')
$t('posts.startCreating')
```

#### 3.5: PostCard.vue
**Ubicación:** `frontend/interface/components/posts/PostCard.vue`

**Cambios a realizar:**

```vue
<!-- ANTES -->
'Sin título'
'¿Estás seguro de que quieres eliminar este post?'
'Sí, eliminar'

<!-- DESPUÉS -->
$t('common.noTitle')
$t('posts.confirmDelete')
$t('posts.yesDelete')
```

#### 3.6: PostPreview.vue
**Ubicación:** `frontend/interface/components/posts/PostPreview.vue`

```vue
<!-- ANTES -->
'Sin título'

<!-- DESPUÉS -->
$t('common.noTitle')
```

---

### Categoría 3: Componentes de Comentarios

#### 3.7: CommentForm.vue
**Ubicación:** `frontend/interface/components/comments/CommentForm.vue`

**Cambios a realizar:**

```vue
<!-- ANTES -->
errors.value.content = 'El comentario no puede estar vacío'
errors.value.email = 'El email no es válido'
errors.value.content = 'Por favor completa la verificación de seguridad'
'Tu comentario será visible una vez aprobado por un moderador.'
'ℹ️ Verificación de seguridad pendiente de configurar'
placeholder="Tu nombre"
placeholder="Escribe tu comentario..."
{{ content.length }} / 2000
'Cancelar'
'Por favor espera {{ rateLimitRemaining }} segundos antes de comentar nuevamente.'

<!-- DESPUÉS -->
errors.value.content = $t('validation.commentEmpty')
errors.value.email = $t('validation.invalidEmail')
errors.value.content = $t('validation.securityRequired')
$t('comments.moderationNotice')
$t('comments.securityNotice')
:placeholder="$t('comments.namePlaceholder')"
:placeholder="$t('comments.commentPlaceholder')"
$t('comments.characterCount', { count: content.length })
$t('actions.cancel')
$t('comments.waitBeforeCommenting', { seconds: rateLimitRemaining })
```

#### 3.8: CommentTree.vue
**Ubicación:** `frontend/interface/components/comments/CommentTree.vue`

```vue
<!-- ANTES -->
'Anónimo'

<!-- DESPUÉS -->
$t('common.anonymous')
```

#### 3.9: CommentItem.vue
**Ubicación:** `frontend/interface/components/comments/CommentItem.vue`

```vue
<!-- ANTES -->
'Anónimo'

<!-- DESPUÉS -->
$t('common.anonymous')
```

---

### Categoría 4: Componentes de Admin

#### 3.10: CommentModerationItem.vue
**Ubicación:** `frontend/interface/components/admin/CommentModerationItem.vue`

**Cambios a realizar:**

```vue
<!-- ANTES -->
'Usuario registrado'
'Anónimo'
'¿Eliminar permanentemente este comentario? Esta acción no se puede deshacer.'
'Sí, eliminar'

<!-- DESPUÉS -->
$t('comments.registeredUser')
$t('common.anonymous')
$t('comments.confirmDeleteComment')
$t('posts.yesDelete')
```

#### 3.11: CommentBulkActions.vue
**Ubicación:** `frontend/interface/components/admin/CommentBulkActions.vue`

```vue
<!-- ANTES -->
'Sí, eliminar'

<!-- DESPUÉS -->
$t('posts.yesDelete')
```

---

### Categoría 5: Componentes Públicos

#### 3.12: PublicPostGrid.vue
**Ubicación:** `frontend/interface/components/public/PublicPostGrid.vue`

**Cambios a realizar:**

```vue
<!-- ANTES -->
'No se encontraron posts con tu búsqueda.'
'Aún no se han publicado posts en este blog.'

<!-- DESPUÉS -->
$t('posts.noPostsWithSearch')
$t('posts.noPosts')
```

---

## 📋 Fase 4: Migrar Páginas

### Categoría 6: Páginas de Autenticación

#### 4.1: pages/auth/login.vue
**Ubicación:** `frontend/pages/auth/login.vue`

```vue
<!-- ANTES -->
title: 'Iniciar Sesión | BlogV2'
content: 'Inicia sesión en tu cuenta de BlogV2...'

<!-- DESPUÉS -->
title: $t('auth.loginTitle')
content: $t('auth.loginDescription')
```

#### 4.2: pages/auth/forgot-password.vue
**Ubicación:** `frontend/pages/auth/forgot-password.vue`

```vue
<!-- ANTES -->
title: 'Recuperar Contraseña | BlogV2'
content: 'Recupera tu contraseña de BlogV2...'

<!-- DESPUÉS -->
title: $t('auth.forgotPasswordTitle')
content: $t('auth.forgotPasswordDescription')
```

---

### Categoría 7: Páginas de Bienvenida

#### 4.3: pages/welcome.vue
**Ubicación:** `frontend/pages/welcome.vue`

**Cambios extensos - Ver archivo para detalles:**

```vue
<!-- ANTES -->
'¡Bienvenido a BlogV2!'
'¡Bienvenido de vuelta!'
'Tu cuenta ha sido creada exitosamente...'
'Nos alegra verte de nuevo...'
'Verificado', 'Pendiente de verificación'
'Enviando...', 'Reenviar email de verificación'
'Email de verificación enviado...'
'Error al enviar el email de verificación...'

<!-- DESPUÉS -->
$t('welcome.title')
$t('welcome.titleBack')
$t('welcome.newAccountMessage')
$t('welcome.backMessage')
$t('welcome.verified'), $t('welcome.pendingVerification')
$t('welcome.sending'), $t('welcome.resendVerification')
$t('welcome.verificationSent')
$t('welcome.verificationError')
```

---

### Categoría 8: Páginas de Admin - Pages

#### 4.4: pages/admin/pages/index.vue
**Ubicación:** `frontend/pages/admin/pages/index.vue`

```vue
<!-- ANTES -->
'Error al cargar las páginas: ' + error.message
'Sin título'
'¿Eliminar página?'
'Esta acción no se puede deshacer.'
'Página eliminada correctamente'
'Error al eliminar la página: ' + error.message

<!-- DESPUÉS -->
$t('pages.errorLoadingPages', { error: error.message })
$t('common.noTitle')
$t('pages.confirmDeletePage')
$t('pages.deletePageWarning')
$t('pages.pageDeleted')
$t('pages.errorDeletingPage', { error: error.message })
```

#### 4.5: pages/admin/pages/create.vue
**Ubicación:** `frontend/pages/admin/pages/create.vue`

```vue
<!-- ANTES -->
'Creando...', 'Crear Página'
'Página creada correctamente'
'Error al crear la página: ' + error.message

<!-- DESPUÉS -->
$t('pages.creating'), $t('pages.createPage')
$t('pages.pageCreated')
$t('pages.errorCreatingPage', { error: error.message })
```

---

### Categoría 9: Páginas Principales

#### 4.6: pages/index.vue
**Ubicación:** `frontend/pages/index.vue`

```vue
<!-- ANTES -->
placeholder="Buscar artículos..."
`Resultados para "${searchQuery}"`
'Últimos Artículos'
'Mantente al día con nuestras últimas publicaciones'

<!-- DESPUÉS -->
:placeholder="$t('posts.searchPlaceholder')"
$t('posts.resultsFor', { query: searchQuery })
$t('posts.latestPosts')
$t('posts.keepUpToDate')
```

#### 4.7: pages/404.vue
**Ubicación:** `frontend/pages/404.vue`

```vue
<!-- ANTES -->
placeholder="Buscar artículos..."

<!-- DESPUÉS -->
:placeholder="$t('posts.searchPlaceholder')"
```

#### 4.8: pages/[year]/[month]/[slug].vue
**Ubicación:** `frontend/pages/[year]/[month]/[slug].vue`

```vue
<!-- ANTES -->
'Página no encontrada'
'Artículo no disponible'
'Este artículo aún no ha sido publicado'

<!-- DESPUÉS -->
$t('pages.pageNotFound')
$t('posts.postNotAvailable')
$t('posts.postNotAvailableDescription')
```

---

### Categoría 10: Páginas de Admin - Posts

#### 4.9: pages/admin/posts/[id]/edit.vue
**Ubicación:** `frontend/pages/admin/posts/[id]/edit.vue`

```vue
<!-- ANTES -->
'Editar: ${title} - Panel de Administración'
'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?'
'Sí, salir'

<!-- DESPUÉS -->
$t('posts.editPost', { title: title })
$t('posts.unsavedChanges')
$t('posts.yesExit')
```

#### 4.10: pages/admin/posts/create.vue
**Ubicación:** `frontend/pages/admin/posts/create.vue`

```vue
<!-- ANTES -->
'El título es requerido para crear un post.'
title: 'Crear Nuevo Post - Panel de Administración'
'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?'
'Sí, salir'

<!-- DESPUÉS -->
$t('posts.titleRequired')
title: $t('posts.createNewPost')
$t('posts.unsavedChanges')
$t('posts.yesExit')
```

#### 4.11: pages/admin/posts/index.vue
**Ubicación:** `frontend/pages/admin/posts/index.vue`

```vue
<!-- ANTES -->
'Gestión de Posts'
'Administra todos los posts del blog'
title: 'Gestión de Posts - Panel de Administración'
'Administra todos los posts del blog desde el panel de administración'

<!-- DESPUÉS -->
$t('posts.managePosts')
$t('posts.manageAllPosts')
title: $t('posts.managePosts')
$t('posts.managePostsFromAdmin')
```

#### 4.12: pages/admin/posts/[id]/index.vue
**Ubicación:** `frontend/pages/admin/posts/[id]/index.vue`

```vue
<!-- ANTES -->
'Ver Post - Panel de Administración'

<!-- DESPUÉS -->
$t('posts.viewPost')
```

#### 4.13: pages/admin/comments/index.vue
**Ubicación:** `frontend/pages/admin/comments/index.vue`

```vue
<!-- ANTES -->
title: 'Moderación de Comentarios - Admin'

<!-- DESPUÉS -->
title: $t('comments.moderation')
```

#### 4.14: pages/admin/index.vue
**Ubicación:** `frontend/pages/admin/index.vue`

```vue
<!-- ANTES -->
title: 'Administración | BlogV2'
content: 'Panel de administración de BlogV2...'

<!-- DESPUÉS -->
title: $t('admin.title')
content: $t('admin.description')
```

#### 4.15: pages/profile/index.vue
**Ubicación:** `frontend/pages/profile/index.vue`

```vue
<!-- ANTES -->
content: 'Gestiona tu información personal en BlogV2'

<!-- DESPUÉS -->
content: $t('profile.description')
```

---

## 📋 Fase 5: Verificación y Testing

### 5.1: Verificar compilación
```bash
cd frontend
pnpm run typecheck
```

### 5.2: Verificar que no queden textos hardcodeados

Ejecutar búsqueda en todos los archivos migrados:

```bash
# Buscar textos en español que NO estén dentro de $t()
grep -r "placeholder=\"[^$]" frontend/interface/components/
grep -r "'[A-ZÁÉÍÓÚ]" frontend/interface/components/
grep -r "\"[A-ZÁÉÍÓÚ]" frontend/interface/components/
```

### 5.3: Probar cambio de idioma

1. Iniciar servidor de desarrollo
2. Navegar por todas las páginas
3. Cambiar entre español, inglés y portugués
4. Verificar que todos los textos se traduzcan correctamente

---

## 📝 Checklist Final

### Archivos de Traducción
- [x] ✅ `frontend/locales/es.json` - Completado con ~120 claves
- [ ] ⏳ `frontend/locales/en.json` - Traducir al inglés
- [ ] ⏳ `frontend/locales/pt.json` - Traducir al portugués

### Componentes de Autenticación (2 archivos)
- [ ] ⏳ `LoginForm.vue`
- [ ] ⏳ `RegisterForm.vue`

### Componentes de Posts (4 archivos)
- [ ] ⏳ `PostEditor.vue`
- [ ] ⏳ `PostList.vue`
- [ ] ⏳ `PostCard.vue`
- [ ] ⏳ `PostPreview.vue`

### Componentes de Comentarios (3 archivos)
- [ ] ⏳ `CommentForm.vue`
- [ ] ⏳ `CommentTree.vue`
- [ ] ⏳ `CommentItem.vue`

### Componentes de Admin (3 archivos)
- [ ] ⏳ `CommentModerationItem.vue`
- [ ] ⏳ `CommentBulkActions.vue`
- [ ] ⏳ `PublicPostGrid.vue`

### Páginas de Autenticación (2 archivos)
- [ ] ⏳ `pages/auth/login.vue`
- [ ] ⏳ `pages/auth/forgot-password.vue`

### Páginas de Welcome (1 archivo)
- [ ] ⏳ `pages/welcome.vue`

### Páginas de Admin - Pages (2 archivos)
- [ ] ⏳ `pages/admin/pages/index.vue`
- [ ] ⏳ `pages/admin/pages/create.vue`

### Páginas Principales (3 archivos)
- [ ] ⏳ `pages/index.vue`
- [ ] ⏳ `pages/404.vue`
- [ ] ⏳ `pages/[year]/[month]/[slug].vue`

### Páginas de Admin - Posts (5 archivos)
- [ ] ⏳ `pages/admin/posts/[id]/edit.vue`
- [ ] ⏳ `pages/admin/posts/create.vue`
- [ ] ⏳ `pages/admin/posts/index.vue`
- [ ] ⏳ `pages/admin/posts/[id]/index.vue`

### Páginas de Admin - Otros (3 archivos)
- [ ] ⏳ `pages/admin/comments/index.vue`
- [ ] ⏳ `pages/admin/index.vue`
- [ ] ⏳ `pages/profile/index.vue`

### Verificación Final
- [ ] ⏳ Ejecutar typecheck sin errores
- [ ] ⏳ Buscar textos hardcodeados restantes
- [ ] ⏳ Probar cambio de idioma en todas las páginas
- [ ] ⏳ Commit de cambios

---

## 🎯 Total de Archivos a Modificar

- **Archivos de traducción:** 2 archivos (en.json, pt.json)
- **Componentes:** 12 archivos
- **Páginas:** 15 archivos
- **TOTAL:** 29 archivos

---

## 💡 Notas Importantes

1. **Interpolaciones:** Mantener las variables como `{query}`, `{author}`, etc.
2. **Pluralización:** Para casos complejos, usar vue-i18n pluralization
3. **Contexto:** Algunos textos pueden necesitar contextos diferentes según dónde se usen
4. **Testing:** Probar cambio de idioma en cada página modificada
5. **Commit:** Hacer commit después de completar cada categoría de archivos

---

**Última actualización:** 2025-10-22 (Fase 1 completada)
