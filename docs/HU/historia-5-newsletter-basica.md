**Depende de:** Historia 1

### **Historia 5: Newsletter Básica**

**Como** visitante interesado  
**Quiero** suscribirme a un newsletter  
**Para** recibir actualizaciones del blog

**Como** administrador  
**Quiero** gestionar suscriptores y enviar newsletters  
**Para** mantener engagement con mi audiencia

**Descripción detallada:** Sistema básico de newsletter con captura de emails, gestión de suscriptores y envío manual de comunicaciones.

**Criterios de aceptación:**

- 5.1 Formulario de suscripción con email y nombre opcional
- 5.2 Cloudflare Turnstile en formulario de suscripción
- 5.3 Rate limiting: 1 suscripción por minuto por IP
- 5.4 Double opt-in vía email de confirmación
- 5.5 Panel admin para ver lista de suscriptores
- 5.6 Funcionalidad de unsuscribe con link único
- 5.7 Template básico fijo para newsletters
- 5.8 Envío manual de newsletters desde panel admin
- 5.9 Validación de email duplicado
- 5.10 Estado activo/inactivo para suscriptores
