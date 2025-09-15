# Configuración de Proveedores OAuth

Esta guía explica cómo configurar los proveedores de autenticación social (Google, Facebook, Twitter) para el sistema OAuth de BlogV2.

## URLs de Callback

Las URLs de callback para cada proveedor son:
```
Google:   {APP_URL}/auth/social/google/callback
Facebook: {APP_URL}/auth/social/facebook/callback
Twitter:  {APP_URL}/auth/social/twitter/callback
```

**Ejemplo para desarrollo local:**
```
http://localhost:8000/auth/social/google/callback
http://localhost:8000/auth/social/facebook/callback
http://localhost:8000/auth/social/twitter/callback
```

## Google OAuth Setup

1. **Acceder a Google Cloud Console**
   - Ir a: https://console.cloud.google.com/
   - Seleccionar o crear un proyecto

2. **Habilitar APIs necesarias**
   - Ir a "APIs y servicios" > "Biblioteca"
   - Buscar "Google+ API" y habilitarla

3. **Crear credenciales OAuth 2.0**
   - Ir a "APIs y servicios" > "Credenciales"
   - Clic en "Crear credenciales" > "ID de cliente de OAuth 2.0"
   - Tipo de aplicación: "Aplicación web"

4. **Configurar dominios autorizados**
   - Orígenes de JavaScript autorizados: `http://localhost:8000`
   - URIs de redirección autorizadas: `http://localhost:8000/auth/social/google/callback`

5. **Copiar credenciales al .env**
   ```env
   GOOGLE_CLIENT_ID=your_google_client_id
   GOOGLE_CLIENT_SECRET=your_google_client_secret
   ```

## Facebook OAuth Setup

1. **Acceder a Facebook Developers**
   - Ir a: https://developers.facebook.com/
   - Crear una nueva aplicación

2. **Configurar Facebook Login**
   - Agregar producto "Facebook Login"
   - Ir a "Facebook Login" > "Configuración"

3. **Configurar URLs de redirección**
   - Agregar: `http://localhost:8000/auth/social/facebook/callback`

4. **Obtener credenciales**
   - Ir a "Configuración" > "Básica"
   - Copiar "Identificador de la aplicación" y "Clave secreta"

5. **Copiar credenciales al .env**
   ```env
   FACEBOOK_CLIENT_ID=your_facebook_app_id
   FACEBOOK_CLIENT_SECRET=your_facebook_app_secret
   ```

## Twitter OAuth Setup

1. **Acceder a Twitter Developer Portal**
   - Ir a: https://developer.twitter.com/
   - Crear una nueva aplicación

2. **Configurar OAuth 1.0a**
   - Habilitar "Enable 3-legged OAuth"
   - Configurar callback URL: `http://localhost:8000/auth/social/twitter/callback`

3. **Obtener credenciales**
   - Ir a "Keys and tokens"
   - Copiar "API Key" y "API Secret Key"

4. **Copiar credenciales al .env**
   ```env
   TWITTER_CLIENT_ID=your_twitter_api_key
   TWITTER_CLIENT_SECRET=your_twitter_api_secret
   ```

## Verificación de Configuración

### Verificar variables de entorno
```bash
docker compose -f docker-compose.dev.yml exec backend php artisan tinker
```

En Tinker ejecutar:
```php
config('services.google.client_id')
config('services.facebook.client_id')
config('services.twitter.client_id')
```

### Probar endpoints

1. **Listar proveedores habilitados:**
   ```bash
   curl http://localhost:8000/api/auth/social/providers
   ```

2. **Probar redirección:**
   ```bash
   curl http://localhost:8000/api/auth/social/google
   ```

## Configuración para Producción

### Variables de entorno
```env
APP_URL=https://tudominio.com

GOOGLE_CLIENT_ID=prod_google_client_id
GOOGLE_CLIENT_SECRET=prod_google_client_secret

FACEBOOK_CLIENT_ID=prod_facebook_client_id
FACEBOOK_CLIENT_SECRET=prod_facebook_client_secret

TWITTER_CLIENT_ID=prod_twitter_client_id
TWITTER_CLIENT_SECRET=prod_twitter_client_secret
```

### URLs de callback
```
https://tudominio.com/auth/social/google/callback
https://tudominio.com/auth/social/facebook/callback
https://tudominio.com/auth/social/twitter/callback
```

## Solución de Problemas

### "Provider not enabled"
- **Causa:** Variables de entorno no configuradas
- **Solución:** Verificar `.env` y reiniciar contenedor

### "Invalid redirect URI"
- **Causa:** URL de callback no coincide
- **Solución:** Verificar `APP_URL` y URLs en consolas de desarrolladores

### "Client ID not found"
- **Causa:** Credenciales incorrectas
- **Solución:** Verificar credenciales y estado de la aplicación

## Estados del Sistema

### Sin credenciales configuradas
- Los proveedores aparecen como "no habilitados"
- Los endpoints devuelven errores de configuración
- Los tests funcionan con mocks

### Con credenciales de desarrollo
- Los proveedores aparecen como "habilitados"
- Se puede probar el flujo completo de OAuth
- Se pueden crear y vincular usuarios reales

### Producción
- Usar credenciales de producción
- URLs HTTPS obligatorias
- Verificar políticas de privacidad requeridas