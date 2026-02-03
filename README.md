# Baseball SaaS Platform

Sistema de gestión para ligas y equipos de béisbol con arquitectura multi-tenancy.

## Requisitos Previos

- **PHP**: 8.2 o superior
- **Composer**: Para administrar dependencias de PHP
- **Node.js & NPM**: Para compilar assets del frontend
- **Base de Datos**: SQLite (por defecto) o MySQL/PostgreSQL

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <URL_DEL_REPOSITORIO>
   cd baseball-saas
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Configurar variables de entorno**
   Copia el archivo de ejemplo y genera la clave de la aplicación:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   *Nota: Por defecto, la configuración está lista para usar SQLite.*

4. **Preparar la base de datos**
   Ejecuta las migraciones y los seeders para poblar la base de datos con datos de prueba:
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed --seeder=BaseballTestSeeder
   ```

5. **Instalar dependencias de Frontend**
   ```bash
   npm install
   npm run build
   ```

## Ejecución

Para iniciar el servidor de desarrollo local:

```bash
php artisan serve
```

El sistema estará disponible en `http://localhost:8000`.

### Configuración de Hosts (Multi-tenancy)

Dado que el sistema utiliza subdominios para identificar a los equipos (tenants), necesitas configurar tu archivo `/etc/hosts` (en Linux/Mac) o `C:\Windows\System32\drivers\etc\hosts` (en Windows) para mapear los subdominios locales:

```text
127.0.0.1   localhost
127.0.0.1   leones.localhost
127.0.0.1   tigres.localhost
127.0.0.1   aguilas.localhost
127.0.0.1   bufalos.localhost
```

Ahora podrás acceder a los tenants usando URLs como `http://leones.localhost:8000`.

## Credenciales de Acceso (Datos de Prueba)

El `BaseballTestSeeder` crea los siguientes usuarios por defecto:

### Super Admin (Panel Central)
- **URL**: `http://localhost:8000/admin`
- **Email**: `admin@test.com`
- **Password**: `admin123`

### Team Owners (Panel de Equipo)
Cada equipo tiene su propio subdominio.

| Equipo | Subdominio | URL | Email de Dueño | Password |
|--------|------------|-----|----------------|----------|
| **Leones** | `leones` | `http://leones.localhost:8000/admin` | `leones@test.com` | `password123` |
| **Tigres** | `tigres` | `http://tigres.localhost:8000/admin` | `tigres@test.com` | `password123` |
| **Águilas** | `aguilas` | `http://aguilas.localhost:8000/admin` | `aguilas@test.com` | `password123` |
| **Búfalos** | `bufalos` | `http://bufalos.localhost:8000/admin` | `bufalos@test.com` | `password123` |

## Estructura del Proyecto

- **App/Filament**: Recursos y páginas del panel administrativo.
- **App/Models**: Modelos Eloquent (`League`, `Team`, `Player`, `Season`, etc.).
- **Database/Seeders**: Seeders para datos iniciales y de prueba.

## Nuevas Funcionalidades (v2.0)

Se ha implementado un portal público completo y un sistema de automatización de contenidos:

### 1. Portal Público (Public Frontend)
El sistema ahora cuenta con un sitio web público para la liga y los equipos, totalmente responsive y con diseño "Sports Premium" (Midnight Slate & Vibrant Orange).

*   **Landing Page de Liga (`/`)**: 
    *   Noticias destacadas y cuadrícula de artículos recientes.
    *   Tabla de Posiciones (Standings) calculada dinámicamente.
    *   Hero section con contenido multimedia.
*   **Sitio de Equipo (Team Dashboard)** (`{team}.app...`):
    *   Subdominio único para cada equipo.
    *   Noticias exclusivas del equipo.
    *   Roster activo con enlaces a perfiles.
    *   Líderes de estadísticas (Team Leaders).
*   **Perfil de Jugador**: Ficha técnica detallada con desglose de estadísticas por temporada.

### 2. Automatización de Noticias (Game Recaps)
El sistema genera noticias automáticamente al finalizar los juegos.
*   **Servicio**: `GameRecapService` analiza el resultado (ganador, perdedor, score) y las estadísticas.
*   **MVP**: Identifica automáticamente al jugador más valioso del partido (Hits, RBIs, HRs) para mencionarlo en la crónica.
*   **Comando**: Ejecuta `php artisan baseball:generate-recaps` para generar noticias de juegos recientes.

### 3. Sistema de Contenidos (CMS)
*   **Modelo `Article`**: Gestión de noticias con soporte para imágenes, categorías (Recap, Highlight, Analysis) y Slugs SEO-friendly.
*   **Admin Panel**: Recurso de Filament para crear y editar noticias desde el backend.

### 4. Rebranding & UI
*   Refactorización completa de componentes (`x-sports.*`) eliminando dependencias de marcas externas.
*   Diseño limpio y agnóstico ("White Label") listo para producción.
*   Iconos SVG nativos y optimización de assets.
