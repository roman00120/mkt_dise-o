# Pruebas

Las pruebas cubren acceso por rol, preselección de servicio, folio, creación de borrador, validación de brief/prioridad, ownership, envío, transición a `pending` y prevención de doble envío. Las pruebas de concurrencia real dependen del driver; la secuencia tiene índice único y bloqueo transaccional.

```powershell
php artisan migrate:fresh --seed
php artisan test
vendor/bin/pint --test
npm.cmd run build
```

