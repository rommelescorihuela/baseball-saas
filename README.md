# Baseball SaaS Platform

Una plataforma SaaS moderna para la gestión de ligas, equipos, jugadores y competencias de béisbol, construida con Laravel 12 y Filament 5.

## 📋 Requisitos del Sistema

*   **PHP**: 8.2 o superior
*   **Composer**: 2.0 o superior
*   **Node.js**: 18.x o superior & NPM
*   **Base de Datos**: SQLite (por defecto), MySQL 8.0+, o PostgreSQL 15+
*   **Extensiones PHP**: `dom`, `curl`, `libxml`, `mbstring`, `zip`, `pcntl`, `pcre`, `sqlite3`, `gd`.

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

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Configurar el entorno
Copia el archivo de ejemplo y genera la clave de la aplicación:
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar la Base de Datos
Por defecto, el proyecto usa SQLite. Si deseas usarlo, crea el archivo:
```bash
touch database/database.sqlite
```
*Si prefieres MySQL/PostgreSQL, edita las variables `DB_*` en tu archivo `.env`.*

### 5. Ejecutar Migraciones y Setup Inicial
```bash
php artisan migrate
php artisan shield:install --panel=admin # Instala roles y permisos base
```

### 6. Instalar dependencias de Frontend y Compilar
```bash
npm install
npm run build
```

### 7. Enlace de Almacenamiento
```bash
php artisan storage:link
```

---

## 🎮 Ejecución del Demo (Datos Funcionales)

Para que todos los módulos sean funcionales y tengan datos de prueba (Ligas, Equipos, Jugadores, Temporadas, etc.), utiliza el `SaaSSeeder`:

```bash
php artisan db:seed --class=SaaSSeeder
```

Este comando generará:
*   5 Ligas de ejemplo.
*   Usuarios propietarios para cada liga.
*   12 Equipos por cada liga.
*   15 Jugadores por cada equipo.
*   Temporadas y Competencias activas con categorías configuradas.

### Credenciales por Defecto
*   **Super Admin**: Crea uno rápidamente con `php artisan make:filament-user`.
*   **Owners de Ligas (Demo)**:
    *   **Email**: `owner@liga-demo.com` (si corres `DemoSeeder`) o `owner@<slug-de-liga>.com` (si corres `SaaSSeeder`).
    *   **Password**: `password`

---

## 🛠️ Comandos de Desarrollo

*   **Servidor local**: `php artisan serve`
*   **Vite (HMR)**: `npm run dev`
*   **Ejecutar Pruebas**: `php artisan test`
*   **Limpiar Cache**: `php artisan optimize:clear`

## 🌐 Notas de Despliegue (Producción)

1.  **Permisos de Carpetas**: Asegúrate de que `storage` y `bootstrap/cache` tengan permisos de escritura para el usuario del servidor web (ej. `www-data`).
    ```bash
    sudo chown -R www-data:www-data storage bootstrap/cache
    ```
2.  **Configuración de Nginx**: Asegúrate de apuntar el `root` a la carpeta `/public` del proyecto.
3.  **Variables de Entorno**: Cambia `APP_ENV=production` y `APP_DEBUG=false` en el `.env`.
4.  **Optimización**:
    ```bash
    php artisan optimize
    npm run build
    ```

---

## 🏗️ Arquitectura
*   **Backend**: Laravel 12
*   **Admin Panel**: Filament 5 (Multitenancy basado en Ligas)
*   **Permisos**: Spatie Laravel-Permission con Filament Shield
*   **Frontend**: Tailwind CSS 4 & Vite
