**Historia de Usuario:** HISTORIA 14: MODO OSCURO

#### **Tarea 14.5: Optimización y Performance**

- 14.5.1 Minimizar layout shift durante cambio de tema
- 14.5.2 Implementar lazy loading de estilos de tema
- 14.5.3 Optimizar CSS para evitar fouc (flash of unstyled content)
- 14.5.4 Crear system de fallbacks para browsers antiguos
- 14.5.5 Implementar metrics de uso de modo oscuro

**Flujo de Modo Oscuro:**

```
Usuario click toggle → ThemeService.toggleTheme() → 
CSS variables actualizadas → Transición CSS activada → 
Preferencia guardada en localStorage → Próxima visita → 
Auto-aplicación de tema guardado → Respeto a system preference si no hay guardado
```
