# Design Decisions

Este directorio contiene documentación de decisiones de diseño y arquitectura del proyecto BlogV2.

## Propósito

Documentar decisiones técnicas importantes con su contexto, alternativas consideradas, y justificación. Esto ayuda a:
- Entender por qué se tomaron ciertas decisiones
- Evitar reabrir debates ya resueltos
- Facilitar onboarding de nuevos desarrolladores
- Mantener coherencia arquitectónica a lo largo del tiempo

## Decisiones Documentadas

### Sistema de Comentarios

#### [Comment System - Anonymous Users](./comment-system-anonymous-users.md)
**Sprint:** 7 | **Estado:** Implementado

Decisión sobre cómo manejar comentarios de usuarios no registrados:
- Por qué NO se verifica ownership del email
- Por qué NO se crean cuentas automáticamente
- Limitaciones conocidas y aceptadas para MVP
- Roadmap futuro para escalabilidad

**Keywords:** comentarios, anónimos, verificación email, moderación, UX

---

## Formato Recomendado

Al crear nuevas decisiones de diseño, usar esta estructura:

```markdown
# Decisión de Diseño: [Título]

**Fecha:** YYYY-MM-DD
**Sprint:** Sprint X - Tarea Y.Z
**Estado:** [Propuesto/Implementado/Rechazado/Deprecado]

## Contexto
Explicar el problema o situación que requiere una decisión.

## Decisión
Describir la decisión tomada con detalles técnicos.

## Alternativas Consideradas
Listar y explicar opciones que se evaluaron pero no se eligieron.

## Consecuencias
- Positivas: Beneficios de la decisión
- Negativas: Trade-offs y limitaciones aceptadas

## Referencias
Links a HU, tareas, documentación relevante.
```

## Cuándo Documentar

Documenta decisiones cuando:
- La decisión afecta múltiples partes del sistema
- Hay trade-offs significativos involucrados
- La decisión podría ser cuestionada en el futuro
- Existen alternativas válidas que fueron rechazadas
- La decisión tiene implicaciones de escalabilidad/seguridad/UX

**No es necesario** documentar decisiones triviales o que siguen convenciones estándar del framework.
