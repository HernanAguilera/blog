# Decisión de Diseño: Sistema de Comentarios Anónimos

**Fecha:** 2025-10-16
**Sprint:** Sprint 7 - Tarea 3.2
**Estado:** Implementado

## Contexto

El sistema de comentarios permite dos tipos de usuarios:
1. **Usuarios Registrados**: Autenticados con JWT, comentarios auto-aprobados
2. **Usuarios Anónimos**: No autenticados, requieren nombre + email, comentarios pending approval

## Decisión

### Sistema Implementado (Opción A: Simple con Moderación Manual)

**Características:**
- Usuarios anónimos proporcionan: nombre (requerido), email (requerido), website (opcional)
- **NO se verifica la propiedad del email**
- **NO se crea cuenta automáticamente**
- Comentarios van a estado `pending_approval`
- Admin modera manualmente (aprobar/rechazar/spam)
- Cloudflare Turnstile previene spam automatizado
- Rate limiting: 3 comentarios/minuto (producción), 15/minuto (desarrollo)

**Validaciones:**
- Formato de email válido (FILTER_VALIDATE_EMAIL)
- Nombre: 2-100 caracteres
- Contenido: 3-5000 caracteres
- Sanitización HTML con CommentDomainService
- Detección básica de spam (keywords, URLs excesivas, mayúsculas)

## Limitaciones Conocidas

### 1. Email Duplicado
- **Problema**: Múltiples personas pueden usar el mismo email
- **Ejemplo**:
  - Juan comenta como "Juan" con `juan@test.com`
  - Pedro comenta como "Pedro" con `juan@test.com`
- **Impacto**: Bajo - La moderación manual detecta inconsistencias
- **Mitigación**: Admin puede rechazar comentarios sospechosos

### 2. Suplantación de Identidad
- **Problema**: Alguien puede comentar usando el email de otra persona
- **Impacto**: Medio - Posible abuso
- **Mitigación**:
  - Moderación manual obligatoria para anónimos
  - Rate limiting previene spam masivo
  - IP tracking para detectar patrones

### 3. Experiencia de Usuario Repetitiva
- **Problema**: Usuario debe ingresar nombre/email en cada comentario
- **Impacto**: Medio - Fricción en UX
- **Mitigación Planificada**: Frontend guardará datos en localStorage/cookies

## Alternativas Consideradas

### Opción B: Verificación de Email Opcional
- Email de verificación enviado al usuario anónimo
- Si verifica, marca email como "trusted" (sin crear cuenta)
- Futuros comentarios desde ese email obtienen ventajas (ej: auto-aprobación)
- **Rechazada para MVP**: Complejidad adicional innecesaria

### Opción C: Forzar Registro
- Eliminar comentarios anónimos completamente
- Solo usuarios registrados pueden comentar
- **Rechazada**: Alta barrera de entrada, baja conversión

### Opción D: Sistema Híbrido (Disqus-style)
- Primer comentario anónimo → email con link de verificación
- Opción de crear cuenta o solo verificar email
- **Rechazada para MVP**: Muy complejo para fase inicial

## Roadmap Futuro

### Sprint Futuro (Post-MVP)
Si el volumen de comentarios crece y la moderación manual se vuelve inmanejable:

1. **Fase 1**: Implementar "recordar datos" en frontend (localStorage)
2. **Fase 2**: Email verification opcional (Opción B)
   - Nueva tabla: `verified_guest_emails`
   - Columnas: email, verification_token, verified_at
   - Comentarios de emails verificados → auto-aprobados
3. **Fase 3**: Sugerir "crear cuenta" después de N comentarios aprobados

## Justificación

Para el MVP de un blog personal:
- **Volumen esperado**: Bajo/Medio (~10-50 comentarios/día)
- **Moderación**: Factible manualmente
- **Prioridad**: Simplicidad y velocidad de implementación
- **Trade-off aceptable**: Limitaciones conocidas vs complejidad de verificación

La moderación manual + Turnstile + Rate limiting son suficientes para prevenir abuso en escala MVP.

## Referencias

- Historia de Usuario: [historia-3-sistema-de-comentarios-con-moderacion.md](../HU/historia-3-sistema-de-comentarios-con-moderacion.md)
- Criterio 3.7: "Validación de email válido" → Validación de **formato**, NO verificación de propiedad
- Implementación: Tarea 3.2 (Sprint 7)

## Mejora UX Pendiente

**TODO (Frontend - Sprint 7 o posterior):**
- Guardar nombre/email/website en localStorage del navegador
- Auto-rellenar formulario de comentario anónimo si datos existen
- Mostrar checkbox "Recordar mis datos"
- Expiración: 90 días o al borrar localStorage

Esto mejora significativamente UX sin complejidad backend.
