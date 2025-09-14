# Docker Setup - BlogV2

Este proyecto incluye configuraciones Docker separadas para desarrollo y producción.

## 🚀 Desarrollo (con hot reload)

```bash
# Iniciar servicios de desarrollo
docker-compose -f docker-compose.dev.yml up

# Iniciar solo la base de datos y Redis (para desarrollo local)
docker-compose -f docker-compose.dev.yml up database redis
```

### Características del entorno de desarrollo:
- ✅ Hot reload para Laravel (`php artisan serve`)
- ✅ Hot reload para Nuxt 3 (`pnpm run dev`)
- ✅ Gestión de dependencias optimizada con **pnpm**
- ✅ Volúmenes montados para cambios en tiempo real
- ✅ Cache global de pnpm para instalaciones más rápidas
- ✅ Logs detallados y debugging habilitado
- ✅ Puertos expuestos: Frontend (3000), Backend (8000), DB (5432), Redis (6379)

### Requisitos previos para desarrollo:
1. Instalar Laravel en `./backend/`
2. Instalar Nuxt 3 en `./frontend/` (con pnpm)
3. Los contenedores esperarán hasta que existan `composer.json` y `package.json`
4. El frontend utilizará pnpm automáticamente para gestión de dependencias

## 🏭 Producción (optimizada)

```bash
# Copiar y configurar variables de entorno
cp .env.prod.example .env.prod
# Editar .env.prod con valores reales

# Construir e iniciar en producción
docker-compose -f docker-compose.prod.yml up -d --build

# Ver logs
docker-compose -f docker-compose.prod.yml logs -f

# Parar servicios
docker-compose -f docker-compose.prod.yml down
```

### Características del entorno de producción:
- ✅ Laravel optimizado con nginx + php-fpm
- ✅ Nuxt estático generado servido por nginx
- ✅ OPcache habilitado
- ✅ Compresión gzip
- ✅ Health checks
- ✅ Queue workers y scheduler automáticos
- ✅ Logs estructurados
- ✅ Multi-stage builds optimizados

## 📁 Estructura de archivos Docker

```
├── docker-compose.dev.yml          # Desarrollo con hot reload
├── docker-compose.prod.yml         # Producción optimizada
├── .env.prod.example               # Variables de entorno de producción
├── backend/
│   ├── Dockerfile.prod             # Dockerfile para Laravel en producción
│   └── docker/
│       ├── nginx.prod.conf         # Configuración nginx para Laravel
│       └── supervisord.prod.conf   # Supervisor para nginx + php-fpm
└── frontend/
    ├── Dockerfile.prod             # Dockerfile para Nuxt en producción
    └── docker/
        └── nginx.prod.conf         # Configuración nginx para Nuxt SSG
```

## 🔧 Comandos útiles

### Desarrollo
```bash
# Ver logs específicos
docker-compose -f docker-compose.dev.yml logs backend
docker-compose -f docker-compose.dev.yml logs frontend

# Reiniciar un servicio
docker-compose -f docker-compose.dev.yml restart backend

# Ejecutar comandos dentro de contenedores
docker-compose -f docker-compose.dev.yml exec backend php artisan migrate
docker-compose -f docker-compose.dev.yml exec frontend pnpm run build
```

### Producción
```bash
# Recrear servicios después de cambios
docker-compose -f docker-compose.prod.yml up -d --build --force-recreate

# Monitorear salud de servicios
docker-compose -f docker-compose.prod.yml ps

# Backup de base de datos
docker-compose -f docker-compose.prod.yml exec database pg_dump -U blogv2_user blogv2_prod > backup.sql
```

## 🚨 Notas importantes

1. **Desarrollo**: Los contenedores frontend y backend esperarán hasta que Laravel y Nuxt estén instalados
2. **Producción**: Configurar correctamente las variables en `.env.prod` antes del deploy
3. **Seguridad**: Cambiar todas las contraseñas por defecto en producción
4. **SSL**: En producción, usar un reverse proxy (Traefik, nginx) para manejar SSL
5. **Volúmenes**: Los logs y datos persisten en volumes de Docker

## 🔍 Health checks

Todos los servicios incluyen health checks accesibles:
- Backend: `http://localhost:8000/health`
- Frontend: `http://localhost:3000/health`
- Database: Verificación de conexión PostgreSQL
- Redis: Comando `redis-cli ping`