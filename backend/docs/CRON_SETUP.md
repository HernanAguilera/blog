# Configuración de Cron Jobs para Posts Programados

## Configuración Automática (Laravel Scheduler)

Laravel incluye un scheduler que maneja todos los trabajos programados desde un solo cron job en el sistema.

### 1. Configurar Cron en el Sistema

Agregar esta línea al crontab del sistema (ejecutar `crontab -e`):

```bash
* * * * * cd /path/to/project/backend && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Para Desarrollo con Docker

```bash
# Ejecutar dentro del contenedor backend:
docker exec blogv2_dev_backend crontab -e

# Agregar la línea:
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Configuración en Producción

```bash
# En el servidor de producción:
sudo crontab -e

# Agregar:
* * * * * cd /var/www/html/backend && /usr/bin/php artisan schedule:run >> /var/log/laravel-schedule.log 2>&1
```

## Comandos Manuales

### Publicar Posts Programados

```bash
# Ejecutar sincrónicamente
php artisan posts:publish-scheduled

# Enviar a cola de trabajos
php artisan posts:publish-scheduled --queue

# Forzar ejecución
php artisan posts:publish-scheduled --force
```

### Verificar Scheduler

```bash
# Listar trabajos programados
php artisan schedule:list

# Ejecutar scheduler manualmente
php artisan schedule:run

# Probar un comando específico
php artisan schedule:test
```

## Configuración del Scheduler

El scheduler está configurado en `app/Console/Kernel.php`:

- **Frecuencia**: Cada minuto
- **Modo**: En cola (background)
- **Overlap**: Previene solapamiento con timeout de 5 minutos
- **Logs**: Se guardan en `storage/logs/scheduled-posts.log`
- **Notificaciones**: Email en caso de fallo

## Monitoreo y Logs

### Logs de Publicación

```bash
# Ver logs en tiempo real
tail -f storage/logs/scheduled-posts.log

# Ver logs de Laravel
tail -f storage/logs/laravel.log
```

### Métricas Importantes

- Número de posts programados procesados
- Tiempo de ejecución
- Errores y fallos
- Posts publicados exitosamente

## Solución de Problemas

### El Cron No Se Ejecuta

1. Verificar que el cron esté configurado: `crontab -l`
2. Verificar permisos de archivo
3. Verificar logs del sistema: `/var/log/cron` o `/var/log/syslog`

### Posts No Se Publican

1. Verificar logs: `storage/logs/scheduled-posts.log`
2. Ejecutar manualmente: `php artisan posts:publish-scheduled`
3. Verificar configuración de base de datos
4. Verificar permisos de archivos

### Debugging

```bash
# Ejecutar con output detallado
php artisan posts:publish-scheduled -v

# Verificar configuración
php artisan config:show

# Limpiar cache
php artisan cache:clear
php artisan config:clear
```

## Configuración de Producción

### Variables de Entorno

```env
# En .env
QUEUE_CONNECTION=redis
LOG_CHANNEL=daily
LOG_LEVEL=info

# Para notificaciones
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

### Optimizaciones

1. **Usar cola Redis** para mejor performance
2. **Configurar supervisor** para workers de cola
3. **Monitoreo con Laravel Horizon** (opcional)
4. **Alertas por email** en fallos críticos