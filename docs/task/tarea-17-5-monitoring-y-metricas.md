**Historia de Usuario:** HISTORIA 17: CACHE CON REDIS Y LARAVEL HORIZON

#### **Tarea 17.5: Monitoring y Métricas**

- 17.5.1 Crear dashboard de métricas de cache (hit ratio, miss ratio)
- 17.5.2 Implementar alertas para cache performance degradado
- 17.5.3 Crear métricas de queue throughput y latencia
- 17.5.4 Implementar logging detallado de cache operations
- 17.5.5 Configurar métricas exportables para monitoring externo

**Flujo de Cache:**

```
Request → Check Redis cache → Cache hit: return cached → 
Cache miss: execute query → Store in Redis with tags → 
Post updated → InvalidateCache job queued → 
Horizon processes job → Cache tags invalidated → 
Next request: cache miss → Fresh data cached → Metrics updated
```
