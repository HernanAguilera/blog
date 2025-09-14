**Historia de Usuario:** HISTORIA 4: MULTIIDIoma

#### **Tarea 4.5: Gestión de Contenido Multiidioma**

- 4.5.1 Modificar PostEditor para soporte de traducciones
- 4.5.2 Crear interface para gestionar traducciones de posts
- 4.5.3 Implementar fallback al idioma por defecto
- 4.5.4 Crear indicadores visuales de traducciones disponibles
- 4.5.5 Optimizar queries para evitar cargar traducciones innecesarias

**Flujo Multiidioma:**

```
Usuario visita /en/2024/01/mi-post → Nuxt i18n detecta locale → 
API request con ?locale=en → PostRepository busca traducción en inglés → 
Si existe: devuelve traducción → Si no: devuelve original + flag → 
Frontend renderiza con textos en inglés → hreflang tags generados
```
