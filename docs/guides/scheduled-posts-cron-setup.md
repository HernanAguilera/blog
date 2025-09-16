# Guía: Configuración de Cron para Posts Programados

## ¿Qué es y por qué necesitamos configurar Cron?

### **¿Qué es Cron?**
Cron es un servicio del sistema operativo Linux/Unix que permite ejecutar comandos o scripts automáticamente en horarios específicos. Es como un "despertador" para tu servidor que ejecuta tareas sin intervención humana.

### **¿Por qué lo necesitamos?**
El sistema de posts programados permite a los usuarios escribir contenido y programar su publicación para una fecha futura. Para que esto funcione automáticamente, necesitamos que el servidor verifique cada cierto tiempo si hay posts listos para publicar.

**Sin cron**: Los posts programados se quedarían "esperando" para siempre.
**Con cron**: Se publican automáticamente en la fecha/hora programada.

## ¿Cómo funciona nuestro sistema?

```
┌─────────────────┐    ┌───────────────────┐    ┌─────────────────────┐
│ Usuario programa│    │ Cron del sistema  │    │ Posts se publican   │
│ post para       │ -> │ ejecuta cada      │ -> │ automáticamente     │
│ mañana 9:00 AM  │    │ minuto el comando │    │ mañana a las 9:00   │
└─────────────────┘    └───────────────────┘    └─────────────────────┘
```

### **Flujo técnico:**
1. **Cron del sistema** ejecuta `php artisan schedule:run` cada minuto
2. **Laravel Scheduler** verifica qué tareas deben ejecutarse
3. **Si es momento**, ejecuta `posts:publish-scheduled`
4. **El comando** busca posts con `scheduled_at <= now()` y los publica

## Configuración Paso a Paso

### **Desarrollo (Docker)**

#### **Opción 1: Configurar dentro del contenedor**
```bash
# 1. Acceder al contenedor backend
docker exec -it blogv2_dev_backend bash

# 2. Configurar cron
echo '* * * * * cd /var/www/html && php artisan schedule:run >> /var/log/cron.log 2>&1' | crontab -

# 3. Verificar que se configuró
crontab -l

# 4. Iniciar el servicio cron
service cron start
```

#### **Opción 2: Script automático**
```bash
# Ejecutar desde el host (fuera del contenedor)
docker exec blogv2_dev_backend bash -c "
    echo '* * * * * cd /var/www/html && php artisan schedule:run' | crontab - &&
    service cron start &&
    echo 'Cron configurado exitosamente'
"
```

### **Producción (Servidor Linux)**

#### **En servidor con acceso root:**
```bash
# 1. Editar crontab del usuario web (www-data o similar)
sudo crontab -u www-data -e

# 2. Agregar esta línea al final del archivo:
* * * * * cd /var/www/html/backend && /usr/bin/php artisan schedule:run >> /var/log/laravel-cron.log 2>&1

# 3. Guardar y salir (Ctrl+X, Y, Enter en nano)
```

#### **En hosting compartido:**
```bash
# Acceder al panel de control del hosting
# Buscar "Cron Jobs" o "Tareas Programadas"
# Configurar:
# Comando: /usr/bin/php /home/usuario/public_html/backend/artisan schedule:run
# Frecuencia: Cada minuto (* * * * *)
```

### **Explicación del comando cron:**
```bash
* * * * * cd /var/www/html && php artisan schedule:run
│ │ │ │ │
│ │ │ │ └─── Día de la semana (0-7, 0 y 7 = domingo)
│ │ │ └───── Mes (1-12)
│ │ └─────── Día del mes (1-31)
│ └───────── Hora (0-23)
└─────────── Minuto (0-59)

* = "cada" (cada minuto, cada hora, etc.)
```

## Verificación y Testing

### **Verificar que cron está funcionando:**
```bash
# 1. Ver logs de cron
tail -f /var/log/cron.log

# 2. Ver logs de Laravel
tail -f storage/logs/laravel.log

# 3. Ver logs específicos de posts programados
tail -f storage/logs/scheduled-posts.log
```

### **Probar manualmente:**
```bash
# Ejecutar el comando directamente
php artisan posts:publish-scheduled

# Ver qué comandos están programados
php artisan schedule:list

# Simular ejecución del scheduler
php artisan schedule:run
```

### **Crear un post de prueba:**
```bash
# Crear un post programado para dentro de 2 minutos
php artisan tinker

# En tinker:
$post = App\src\Domain\Post\Entities\Post::create(
    new App\src\Domain\Post\ValueObjects\PostTitle('Test Post'),
    new App\src\Domain\Post\ValueObjects\PostContent('Contenido de prueba'),
    new App\src\Domain\User\ValueObjects\UserId(1)
);

$post->schedule(new DateTimeImmutable('+2 minutes'));
app(App\src\Domain\Post\Repositories\PostRepositoryInterface::class)->save($post);
```

## Solución de Problemas Comunes

### **❌ Cron no se ejecuta**

**Problema**: Los posts no se publican automáticamente.

**Soluciones**:
```bash
# 1. Verificar que cron está instalado y ejecutándose
systemctl status cron  # Ubuntu/Debian
systemctl status crond  # CentOS/RHEL

# 2. Verificar logs de cron
tail -f /var/log/syslog | grep CRON

# 3. Verificar permisos
ls -la /var/www/html/backend/artisan
# Debe ser ejecutable: -rwxr-xr-x

# 4. Probar comando manualmente
cd /var/www/html/backend && php artisan schedule:run
```

### **❌ Errores de permisos**

**Problema**: Permission denied al ejecutar artisan.

**Soluciones**:
```bash
# 1. Corregir propietario
sudo chown -R www-data:www-data /var/www/html/backend

# 2. Corregir permisos
sudo chmod +x /var/www/html/backend/artisan
sudo chmod -R 755 /var/www/html/backend/storage
sudo chmod -R 755 /var/www/html/backend/bootstrap/cache
```

### **❌ Posts no se publican**

**Problema**: Cron funciona pero posts siguen programados.

**Soluciones**:
```bash
# 1. Verificar base de datos
# Confirmar que posts tienen scheduled_at <= now()

# 2. Verificar logs de aplicación
tail -f storage/logs/laravel.log

# 3. Ejecutar manualmente con debug
php artisan posts:publish-scheduled -vvv
```

## Monitoreo y Mantenimiento

### **Logs importantes:**
- `/var/log/cron.log` - Logs del sistema cron
- `storage/logs/laravel.log` - Logs de Laravel
- `storage/logs/scheduled-posts.log` - Logs específicos del comando

### **Métricas a monitorear:**
- Número de posts procesados por día
- Tiempo de ejecución del comando
- Errores y fallos
- Uso de memoria y CPU

### **Alertas recomendadas:**
```bash
# Script para alertar si no se han procesado posts en X tiempo
# Agregar al cron cada hora:
0 * * * * /path/to/check-scheduled-posts.sh
```

## Alternativas Sin Cron

### **Para desarrollo:**
```bash
# Ejecutar manualmente cuando sea necesario
php artisan posts:publish-scheduled

# O usar queue workers con supervisor
php artisan queue:work --queue=scheduled-posts
```

### **Para hosting que no permite cron:**
- Usar servicios externos como **cron-job.org**
- Configurar webhook que llame a endpoint específico
- Usar GitHub Actions o similar para ejecutar tareas

## Comandos Útiles de Referencia

```bash
# Gestión de cron
crontab -l                    # Listar cron jobs
crontab -e                    # Editar cron jobs
crontab -r                    # Eliminar todos los cron jobs

# Laravel scheduler
php artisan schedule:list     # Ver comandos programados
php artisan schedule:run      # Ejecutar scheduler
php artisan schedule:work     # Ejecutar scheduler continuamente

# Posts programados
php artisan posts:publish-scheduled           # Ejecutar sincrónicamente
php artisan posts:publish-scheduled --queue   # Enviar a cola
php artisan posts:publish-scheduled --force   # Forzar ejecución

# Debugging
tail -f storage/logs/scheduled-posts.log      # Ver logs en tiempo real
php artisan queue:work --verbose              # Ver cola en tiempo real
```

---

**💡 Tip**: Siempre prueba la configuración en desarrollo antes de implementar en producción. Un cron mal configurado puede sobrecargar el servidor.