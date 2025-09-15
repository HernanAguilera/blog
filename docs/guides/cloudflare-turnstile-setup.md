# Configuración de Cloudflare Turnstile

Esta guía te ayudará a configurar Cloudflare Turnstile para proteger los formularios del blog con CAPTCHA.

## ¿Qué es Turnstile?

Cloudflare Turnstile es un servicio de CAPTCHA invisible que protege tu sitio web contra bots y spam sin afectar la experiencia del usuario. Es una alternativa moderna a reCAPTCHA.

## Paso 1: Crear cuenta en Cloudflare

1. Ve a [Cloudflare](https://www.cloudflare.com/)
2. Crea una cuenta gratuita si no tienes una
3. Inicia sesión en tu dashboard

## Paso 2: Acceder a Turnstile

1. En el dashboard de Cloudflare, busca **"Turnstile"** en el menú lateral
2. O ve directamente a: https://dash.cloudflare.com/profile/api-tokens
3. Selecciona la pestaña **"Turnstile"**

## Paso 3: Crear un sitio en Turnstile

1. Haz clic en **"Add site"** o **"Agregar sitio"**
2. Completa los campos:
   - **Site name**: `BlogV2` (o el nombre que prefieras)
   - **Domain**: Para desarrollo usa `localhost` y `127.0.0.1`
   - **Mode**: Selecciona **"Managed"** (recomendado)

### Configuración recomendada para desarrollo:

```
Site name: BlogV2 - Development
Domain: localhost, 127.0.0.1, tu-dominio-local.test
Mode: Managed (Challenge)
```

### Para producción:

```
Site name: BlogV2 - Production
Domain: tu-dominio.com, www.tu-dominio.com
Mode: Managed (Challenge)
```

## Paso 4: Obtener las claves

Después de crear el sitio, verás dos claves importantes:

1. **Site Key** (Clave del sitio)
   - Es pública, va en el frontend
   - Ejemplo: `0x4AAAAAAABkMYinukE_pNgb`

2. **Secret Key** (Clave secreta)
   - Es privada, va en el backend
   - Ejemplo: `0x4AAAAAAABkMYinyArH8fF3wXJ4NJZD2-I`

## Paso 5: Configurar las variables de entorno

1. Abre el archivo `.env` en el directorio `backend/`
2. Agrega las siguientes variables:

```env
# Cloudflare Turnstile Configuration
TURNSTILE_SITE_KEY=tu-site-key-aqui
TURNSTILE_SECRET_KEY=tu-secret-key-aqui
```

### Ejemplo completo:

```env
# Cloudflare Turnstile Configuration
TURNSTILE_SITE_KEY=0x4AAAAAAABkMYinukE_pNgb
TURNSTILE_SECRET_KEY=0x4AAAAAAABkMYinyArH8fF3wXJ4NJZD2-I
```

## Paso 6: Verificar la configuración

1. Reinicia el servidor del backend si está corriendo
2. El sistema automáticamente detectará las claves y habilitará Turnstile
3. Los formularios de login y registro ahora requerirán verificación CAPTCHA

## Paso 7: Configuración del frontend (Futuro)

Cuando implementes el frontend, necesitarás:

1. Incluir el script de Turnstile en tu HTML:
```html
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
```

2. Agregar el widget de Turnstile a tus formularios:
```html
<div class="cf-turnstile"
     data-sitekey="tu-site-key-aqui"
     data-callback="onTurnstileSuccess">
</div>
```

## Configuraciones avanzadas

### Modo de desarrollo vs producción

- **Desarrollo**: Usa dominios locales (`localhost`, `127.0.0.1`)
- **Producción**: Usa tu dominio real

### Tipos de desafío

- **Managed**: Cloudflare decide automáticamente el nivel de desafío
- **Non-interactive**: Sin interacción del usuario (invisible)
- **Invisible**: Solo se muestra si es necesario

## Solución de problemas

### Error: "Invalid site key"
- Verifica que la `TURNSTILE_SITE_KEY` sea correcta
- Asegúrate que el dominio esté configurado en Cloudflare

### Error: "Invalid secret key"
- Verifica que la `TURNSTILE_SECRET_KEY` sea correcta
- No confundas la site key con la secret key

### CAPTCHA no aparece
- Verifica que las claves estén en el archivo `.env`
- Reinicia el servidor después de cambiar las variables
- Revisa los logs del navegador para errores JavaScript

### Turnstile deshabilitado
- Si no configuras las claves, Turnstile se deshabilita automáticamente
- Los formularios funcionarán sin CAPTCHA (solo para desarrollo)

## Seguridad

⚠️ **Importante:**
- **NUNCA** publiques tu `TURNSTILE_SECRET_KEY` en repositorios públicos
- Usa diferentes claves para desarrollo y producción
- Mantén el archivo `.env` en tu `.gitignore`

## Recursos adicionales

- [Documentación oficial de Turnstile](https://developers.cloudflare.com/turnstile/)
- [API Reference](https://developers.cloudflare.com/turnstile/get-started/)
- [Configuración avanzada](https://developers.cloudflare.com/turnstile/reference/configuration/)

## Estado actual del proyecto

✅ Backend configurado para usar Turnstile
✅ Middleware implementado y funcional
✅ Validación automática en formularios
⏳ Frontend pendiente (Sprint 3)