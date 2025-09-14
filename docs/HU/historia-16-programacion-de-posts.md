**Depende de:** Historia 2

### **Historia 16: Programación de Posts**

**Como** administrador  
**Quiero** programar posts para publicación futura  
**Para** mantener consistencia en publicaciones sin estar presente

**Descripción detallada:** Sistema completo de programación de contenido con múltiples opciones de scheduling y automatizaciones relacionadas.

**Criterios de aceptación:**

- 16.1 Campo datetime picker para fecha de publicación
- 16.2 Estado "scheduled" para posts programados
- 16.3 Job scheduler para publicar automáticamente
- 16.4 Vista calendario de posts programados
- 16.5 Notificación email cuando se publica automáticamente
- 16.6 Validación: no permitir fechas pasadas
- 16.7 Timezone management correcto
- 16.8 Re-programación de posts
- 16.9 Draft automático si scheduling falla
- 16.10 Social media auto-post al publicar (Twitter/X)
