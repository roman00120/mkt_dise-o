# Pruebas

Las pruebas Feature validan acceso por rol, estados de usuario, nombre, CTA, métricas, servicios, atención, entregables, actividad, estados visuales y rutas placeholder. Las pruebas Unit validan la estructura del servicio, salud de fechas y orden de atención por vencimiento.

```powershell
php artisan test
vendor/bin/pint --test
npm.cmd run build
```

No se prueba todavía persistencia de solicitudes, porque esa entidad pertenece a una fase posterior.

