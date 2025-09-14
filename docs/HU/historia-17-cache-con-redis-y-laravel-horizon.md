**Depende de:** Ninguna

### **Historia 17: Cache con Redis y Laravel Horizon**

**Como** visitante  
**Quiero** una experiencia ultra-rápida  
**Para** acceder al contenido sin demoras

**Como** administrador  
**Quiero** un sistema de cache robusto  
**Para** optimizar performance y reducir carga del servidor

**Descripción detallada:** Implementación completa de cache distribuido con Redis y gestión de colas con Laravel Horizon.

**Criterios de aceptación:**

- 17.1 Redis configurado para cache de sesiones
- 17.2 Cache de queries frecuentes
- 17.3 Cache de páginas completas para visitantes
- 17.4 Laravel Horizon para gestión de queues
- 17.5 Cache invalidation automático al actualizar contenido
- 17.6 Cache warming para contenido crítico
- 17.7 Métricas de hit ratio en admin
- 17.8 Cache tags para invalidación granular
- 17.9 Queue monitoring dashboard
- 17.10 Cache de contadores (vistas, comentarios)
