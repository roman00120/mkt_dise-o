# TG Creative Hub

Plataforma interna de TG Creative Hub — TOTAL GROUND, construida con Laravel 12, Blade, Vite, Tailwind CSS 4, Alpine.js y el Design System de Total Ground.

## Requisitos

- PHP 8.2 o superior (entorno validado con PHP 8.3 en `C:\tmp\php83`).
- Composer 2, Node.js LTS, npm y Git.
- SQLite para las fases iniciales.

## Instalación

```powershell
composer install
npm.cmd install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

No incluyas credenciales reales en el repositorio.

## Desarrollo

```powershell
npm.cmd run dev
php artisan serve
```

La ruta principal de Marketing es `GET /app`. Requiere autenticación, usuario activo y rol `marketing`, `supervisor` o `admin`.

## SQLite y migraciones

```env
DB_CONNECTION=sqlite
```

```powershell
php artisan migrate
php artisan migrate:status
```

## Tests y build

```powershell
php artisan test
npm.cmd run build
vendor/bin/pint --test
composer validate
composer audit
```

## Documentación

- [Fase 1 — Producto y UX](docs/product/README.md)
- [Fase 2 — Design System](docs/design-system/README.md)
- [Fase 3 — Autenticación](docs/authentication/README.md)
- [Fase 4 — Dashboard de Marketing](docs/dashboard-marketing/README.md)
- [Fase 5 — Wizard de solicitudes](docs/request-wizard/README.md)

## Alcance actual

La Fase 4 incluye el dashboard de Marketing con datos demostrativos, estados de carga/vacío/error, layout autenticado y rutas placeholder. El wizard real, solicitudes persistentes, panel creativo y panel administrativo quedan para fases posteriores.
