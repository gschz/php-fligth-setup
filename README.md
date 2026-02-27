# PHP Flight Setup — Pure REST API Skeleton

Proyecto base para FlightPHP v3 como **API REST pura** con Eloquent ORM, Phinx migrations, Runway (CLI), Tracy, PHPStan, PHPUnit, Rector y PHP-CS-Fixer.

## Requisitos

- PHP 8.4
- Composer
- SQLite (para desarrollo local, sin servidor necesario)
- PostgreSQL (opcional, para staging/producción)

## Instalación e inicio rápido

```bash
composer install

# Copiar la plantilla de entorno para desarrollo local (SQLite por defecto)
cp .envs/.env.example .envs/.env.local

# Editar .envs/.env.local con tus valores (APP_KEY, etc.)

# Crear el directorio de base de datos (si no existe)
mkdir -p database

# Arrancar el servidor de desarrollo (carga .envs/.env.local automáticamente)
composer dev
```

Abrir http://localhost:8000/health

## Multi-entorno: SQLite vs PostgreSQL

Este proyecto usa `getenv()` para leer variables de entorno — sin loaders `.env` embebidos en la app.
Los env files se cargan mediante `composer dev` / `composer dev:pg` antes de arrancar el servidor.

| Variable | Descripción | Default |
|---|---|---|
| `APP_ENV` | `development`, `production`, `testing` | `development` |
| `DB_CONNECTION` | `sqlite` o `pgsql` | `sqlite` |
| `DB_DATABASE` | Ruta SQLite o nombre BD PostgreSQL | `database/database.sqlite` |
| `DB_HOST` | Host PostgreSQL | `127.0.0.1` |
| `DB_PORT` | Puerto PostgreSQL | `5432` |
| `DB_USERNAME` | Usuario PostgreSQL | `postgres` |
| `DB_PASSWORD` | Contraseña PostgreSQL | _(vacío)_ |
| `DATABASE_URL` | DSN completo (Heroku/Railway) — sobreescribe DB_* | _(vacío)_ |
| `CORS_ALLOWED_ORIGINS` | Orígenes CORS permitidos | `*` |

### Desarrollo local (SQLite)

```bash
cp .envs/.env.example .envs/.env.local
composer dev
```

### Desarrollo con PostgreSQL

```bash
cp .envs/.env.pg.example .envs/.env.pg.local
# Editar .envs/.env.pg.local con tus credenciales
composer dev:pg
```

### Estructura de `.envs/`

| Archivo | Descripción | Trackeado en git |
|---|---|---|
| `.env.example` | Plantilla SQLite (desarrollo local, default) | ✅ Sí |
| `.env.pg.example` | Plantilla PostgreSQL (desarrollo local) | ✅ Sí |
| `.env.production.example` | Referencia de producción (sin valores reales) | ✅ Sí |
| `.env.local` | Valores reales SQLite locales | ❌ No (git-ignorado) |
| `.env.pg.local` | Valores reales PostgreSQL locales | ❌ No (git-ignorado) |

> **Nota:** `app/config/config.php` está trackeado en git — no contiene credenciales. No es necesario crearlo manualmente.

## Migraciones (Phinx)

```bash
# Ejecutar migraciones (SQLite por defecto)
composer db:migrate

# Ejecutar migraciones en entorno de tests
composer db:migrate:test

# Rollback
composer db:rollback

# Seeds
composer db:seed
```

## Estructura del proyecto

```
project-root/
├── app/
│   ├── commands/        # Comandos CLI (Runway)
│   ├── config/
│   │   ├── bootstrap.php
│   │   ├── config.php         ← trackeado en git, lee variables de entorno
│   │   ├── routes.php         ← rutas API REST
│   │   └── services.php       ← Eloquent Capsule + Tracy + Flight::db()
│   ├── controllers/
│   │   └── ApiExampleController.php
│   ├── log/             # Logs (git-ignorados)
│   ├── middlewares/
│   │   ├── CorsMiddleware.php
│   │   └── SecurityHeadersMiddleware.php
│   ├── models/
│   │   └── User.php     ← modelo Eloquent de ejemplo
│   ├── utils/
│   │   └── ApiResponse.php
│   └── views/           # Mantenido pero no expuesto como endpoints HTTP
├── bin/
│   └── dev.php          # Launcher: carga env file y arranca el servidor
├── database/
│   ├── migrations/      # Migraciones Phinx
│   └── seeders/         # Seeds Phinx
├── public/              # Web root (index.php)
├── tests/               # PHPUnit tests
├── .envs/
│   ├── .env.example            ← plantilla SQLite (trackeada en git)
│   ├── .env.pg.example         ← plantilla PostgreSQL (trackeada en git)
│   └── .env.production.example ← referencia producción (trackeada en git)
├── phinx.php            ← configuración de migraciones
├── phpstan.neon
├── .php-cs-fixer.php
└── composer.json
```

## Endpoints API

| Método | Ruta | Descripción |
|---|---|---|
| `GET` | `/health` | Health check |
| `GET` | `/api/v1/users` | Listar usuarios |
| `GET` | `/api/v1/users/:id` | Obtener usuario |
| `POST` | `/api/v1/users` | Crear usuario |
| `PUT` | `/api/v1/users/:id` | Actualizar usuario |
| `DELETE` | `/api/v1/users/:id` | Eliminar usuario |

## Scripts y calidad (Composer)

```bash
composer dev            # Carga .envs/.env.local y arranca en :8000 (SQLite)
composer dev:pg         # Carga .envs/.env.pg.local y arranca en :8000 (PostgreSQL)
composer start          # Arranca sin cargar env file (usa variables ya seteadas)
composer db:migrate     # Ejecuta migraciones (SQLite por defecto)
composer db:migrate:pg  # Ejecuta migraciones con PostgreSQL
composer db:migrate:test # Ejecuta migraciones en entorno de tests
composer db:rollback    # Rollback de la última migración
composer db:seed        # Ejecuta seeds
composer test:unit      # PHPUnit
composer test:stan      # PHPStan análisis estático
composer rector:dry     # Rector (dry-run)
composer rector:fix     # Rector (fix)
composer lint           # PHP-CS-Fixer (dry-run)
composer lint:fix       # PHP-CS-Fixer (fix)
```

## Configuración

- `app/config/config.php` — trackeado en git, sin credenciales, lee variables de entorno con `getenv()`
- `app/config/services.php` — inicializa Eloquent Capsule, Tracy y `Flight::db()`
- `app/config/routes.php` — define todas las rutas API REST

## Seguridad

- Todos los valores sensibles se leen desde variables de entorno (nunca hardcodeados)
- `config.php` está trackeado en git (no contiene credenciales)
- Los env files con valores reales (`.envs/.env.local`, `.envs/.env.pg.local`) están git-ignorados
- CORS configurable via `CORS_ALLOWED_ORIGINS`
- Security headers en todas las rutas API
