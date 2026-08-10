# TG Creative Hub

Plataforma interna de TOTAL GROUND construida con Laravel 12, Blade, Vite y Tailwind CSS.

## Requisitos

- PHP 8.2 o superior (entorno validado con PHP 8.3 en `C:\tmp\php83`).
- Composer 2, Node.js LTS, npm y Git.
- SQLite para desarrollo y Fase 0. Producción debe usar MySQL.

## Instalación

```powershell
composer install
npm.cmd install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm.cmd run build
```

No se incluyen credenciales reales ni contraseñas conocidas en el repositorio.

## Usuarios iniciales de producción

`ProductionSeeder` carga únicamente catálogos y departamentos. Para configurar los cuatro usuarios iniciales en staging o producción, define `ADMIN_NAME` y `ADMIN_EMAIL` y ejecuta el flujo interactivo:

```powershell
php artisan production:users
```

El comando solicita una contraseña fuerte oculta para cada usuario, exige mínimo 12 caracteres con mayúsculas, minúsculas, números y símbolos, y fuerza el cambio en el primer acceso. Para actualizar usuarios existentes se requiere `--update` y confirmación explícita.

Para crear solo un administrador:

```powershell
php artisan admin:create
```

Estos comandos están bloqueados en `local` y solo aceptan `production` o `staging`. No se imprimen ni registran contraseñas.

## Uso de SQLite

Configura en `.env`:

```env
DB_CONNECTION=sqlite
```

En Windows, crea `database/database.sqlite` si no existe. No necesitas MySQL para desarrollo local.

## Comandos de desarrollo

```powershell
npm.cmd run dev
php artisan serve
```

La aplicación queda disponible normalmente en `http://127.0.0.1:8000`.

## Migraciones

```powershell
php artisan migrate
php artisan migrate:status
```

Los datos demo solo se cargan si `ENABLE_DEMO_DATA=true` y nunca en staging o producción. `php artisan db:seed` carga primero los catálogos operativos.

## Tests y compilación frontend

```powershell
php artisan test
npm.cmd run build
vendor/bin/pint --test
composer validate
composer audit
```
