**Historia de Usuario:** HISTORIA 12: RSS FEEDS

#### **Tarea 12.5: Frontend RSS Integration**

- 12.5.1 Crear componentes de enlace a feeds RSS
- 12.5.2 Implementar iconos RSS en lugares apropiados
- 12.5.3 Crear página explicativa sobre RSS feeds
- 12.5.4 Implementar preview de feeds en admin
- 12.5.5 Crear validador de feeds en panel admin

**Flujo RSS:**

```
Cron job hourly → Cache invalidation → RSSService.generateMainFeed() → 
Query posts publicados → Transform a XML → CDATA para HTML → 
Cache por 1 hora → /rss.xml servido → Auto-discovery tags → 
Agregadores detectan feed → Lectores suscritos reciben updates
```
