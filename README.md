# TG Creative Hub

Base inicial de la plataforma **TG Creative Hub — TOTAL GROUND**, preparada durante la Fase 0 con Laravel 12 y Blade.

## Requisitos

- PHP 8.2 o superior (entorno actual: PHP 8.3 portable en `C:\tmp\php83`)
- Composer 2
- Node.js LTS y npm
- Git
- SQLite (incluido mediante la extensión PDO de PHP)

MySQL no es necesario en esta fase; se configurará posteriormente.

## Instalación

```powershell
composer install
npm.cmd install
copy .env.example .env
php artisan key:generate
```

Configura `.env` con `APP_NAME="TG Creative Hub"`, locale `es` y `APP_URL=http://localhost`. No incluyas credenciales reales.

## SQLite y migraciones

La aplicación usa SQLite durante la Fase 0:

```env
DB_CONNECTION=sqlite
```

Ejecuta las migraciones con:

```powershell
php artisan migrate
php artisan migrate:status
```

## Desarrollo

En terminales separadas:

```powershell
npm.cmd run dev
php artisan serve
```

La ruta inicial es `GET /` y muestra el estado de instalación de TG Creative Hub.

## Tests

```powershell
php artisan test
```

## Build frontend

```powershell
npm.cmd run build
```

El frontend base utiliza Vite y Tailwind CSS. No se han instalado React, Vue, Bootstrap, jQuery, Breeze ni autenticación.

## Próximas fases

Login, dashboard, solicitudes, paneles, reportes y el Design System quedan fuera de esta Fase 0.

## Documentación de producto

La documentación funcional y de UX de la Fase 1 está disponible en [docs/product/README.md](docs/product/README.md). Incluye arquitectura de información, mapa de pantallas, flujos, estados, permisos, reglas de negocio, notificaciones, auditoría, roadmap y decisiones abiertas.
