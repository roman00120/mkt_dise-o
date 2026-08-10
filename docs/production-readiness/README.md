# Fase 12 — Producción

Este documento reúne la preparación operativa de TG Creative Hub. La aplicación está preparada para staging; producción requiere valores reales de infraestructura, HTTPS, MySQL, almacenamiento privado, worker de cola, scheduler y restauración validada en un entorno aislado.

## Comandos principales

```powershell
php artisan app:validate-environment --strict
php artisan app:verify-database --strict
php artisan app:smoke-test
php artisan storage:audit
php artisan app:backup --all --verify
php artisan app:backup-verify --latest
php artisan app:backup-prune --dry-run
php artisan app:restore <backup> --dry-run
```

Nunca ejecutar `migrate:fresh`, `db:seed` demo o regenerar `APP_KEY` en producción.
