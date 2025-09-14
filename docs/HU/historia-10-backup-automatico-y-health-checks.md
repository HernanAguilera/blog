**Depende de:** Ninguna

### **Historia 10: Backup Automático y Health Checks**

**Como** administrador  
**Quiero** tener backups automáticos y monitoreo de salud  
**Para** garantizar la disponibilidad y recuperación del blog

**Descripción detallada:** Sistema de backup automático de base de datos y archivos, con health checks para monitorear el estado de la aplicación y alertas automáticas.

**Criterios de aceptación:**

- 10.1 Backup automático diario de base de datos
- 10.2 Backup semanal completo (DB + archivos)
- 10.3 Retención de backups por 30 días
- 10.4 Health check endpoint (/health)
- 10.5 Monitoreo de conectividad a base de datos
- 10.6 Monitoreo de conectividad a AWS S3
- 10.7 Alertas por email si algún servicio falla
- 10.8 Dashboard de estado de servicios en admin
- 10.9 Logs estructurados de backups
- 10.10 Restauración manual desde backup
