# Autenticación — TG Creative Hub

La Fase 3 implementa autenticación Blade con Laravel 12, recuperación de contraseña, confirmación de contraseña, roles y estados de usuario. No existe registro público ni autenticación social.

## Requisitos

- PHP 8.2 o superior (entorno validado con PHP 8.3).
- Composer, Node.js y npm.
- SQLite para desarrollo local de esta fase.

## Instalación local

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm.cmd install
npm.cmd run build
```

El seeder general no crea usuarios. Crea el primer administrador con `php artisan admin:create`; los usuarios posteriores deben gestionarse desde el panel administrativo o el proceso aprobado de alta.

## Uso de SQLite

`.env` debe contener `DB_CONNECTION=sqlite` y `database/database.sqlite` debe existir. Las variables de host, puerto, base, usuario y contraseña de MySQL permanecen comentadas durante la Fase 3.

## Desarrollo y comandos

```powershell
php artisan serve
npm.cmd run dev
php artisan migrate
php artisan migrate:status
php artisan test
npm.cmd run build
```

Para producción se debe sustituir SQLite por la base aprobada, configurar correo real mediante secretos y usar HTTPS.
