# Baseball SaaS Platform

Una plataforma SaaS moderna para la gestión de ligas, equipos, jugadores y competencias de béisbol, construida con Laravel 12 y Filament 5.

## 📋 Requisitos del Sistema

*   **PHP**: 8.2 o superior
*   **Composer**: 2.0 o superior
*   **Node.js**: 18.x o superior & NPM
*   **Base de Datos**: SQLite (por defecto), MySQL 8.0+, o PostgreSQL 15+
*   **Extensiones PHP**: `dom`, `curl`, `libxml`, `mbstring`, `zip`, `pcntl`, `pcre`, `sqlite3`, `gd`, `bcmath`, `intl`.

## 🚀 Instalación en un Nuevo Servidor

Sigue estos pasos para poner en marcha el proyecto:

### Opción Rápida (Recomendada)
Si tienes el entorno base configurado, puedes usar el script de setup incluido:
```bash
composer setup
```
Este comando ejecutará `composer install`, copiará el `.env`, generará la clave, correrá las migraciones y compilará los assets con `npm`.

### Paso a paso (Manual)
#### 1. Clonar el repositorio
```bash
git clone <url-del-repositorio> baseball-saas
cd baseball-saas
```

### 2. Instalar dependencias
```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

### 3. Configurar el entorno
Copia el archivo de ejemplo y genera la clave de la aplicación:
```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

### 4. Configurar la Base de Datos
Edita las variables `DB_*` en tu archivo `.env` para conectar con tu base de datos (PostgreSQL recomendado para producción).

### 5. Configurar Stripe (Pagos)
Asegúrate de configurar las siguientes variables en tu `.env`:
```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
CASHIER_CURRENCY=usd
```

### 6. Ejecutar Migraciones y Setup
El proyecto utiliza **Spatie Laravel Permission**, **Filament Shield** y **Laravel Cashier**.

```bash
php artisan migrate --force
php artisan shield:generate --all --panel=admin
php artisan filament:assets
php artisan filament:optimize
```

### 7. Popular la Base de Datos (Opcional)
Para crear roles base y un super usuario inicial:
```bash
php artisan db:seed --class=DatabaseSeeder
```

---

## ☁️ Notas de Despliegue (Producción)

### 1. Colas de Trabajo (Queues)
El sistema utiliza colas para el envío de correos y procesamiento de eventos de Stripe. Debes configurar un proceso **Supervisor** para mantener corriendo el worker:

```ini
[program:baseball-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/project/storage/logs/worker.log
```

### 2. Tareas Programadas (Cron)
Agrega la siguiente entrada al crontab del usuario del servidor:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Webhooks de Stripe
Configura un punto final en el Dashboard de Stripe apuntando a:
`https://tu-dominio.com/stripe/webhook`
Eventos requeridos: `customer.subscription.updated`, `customer.subscription.deleted`, `customer.subscription.created`.

### 4. Permisos de Carpetas
Asegúrate de que `storage` y `bootstrap/cache` tengan permisos de escritura:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
```

### 5. Optimización
Una vez configurado todo, ejecuta:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Configuración de Nginx
Asegúrate de apuntar el `root` a la carpeta `/public` del proyecto en tu configuración del servidor web.

### 7. Variables de Producción
En tu archivo `.env`, asegúrate de establecer:
```env
APP_ENV=production
APP_DEBUG=false
```

---

## 🏗️ Arquitectura
*   **Backend**: Laravel 12
*   **Admin Panel**: Filament 5 (Multitenancy basado en Ligas)
*   **Permisos**: Spatie Laravel-Permission con Filament Shield
*   **Frontend**: Tailwind CSS 4 & Vite
