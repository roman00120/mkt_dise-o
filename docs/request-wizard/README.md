# Wizard de solicitudes creativas

Fase 5 implementa la creación persistente de solicitudes para Marketing. El flujo tiene seis pasos editables y un séptimo estado de confirmación: servicio, tipo, brief, archivos, fecha/prioridad, revisión y confirmación.

Ruta inicial: `GET /app/requests/create`. Sólo usuarios activos con rol Marketing pueden crear. No se implementan todavía detalle completo, panel creativo, asignación, comentarios, entregables ni notificaciones completas.

## Uso

```powershell
php artisan migrate
php artisan db:seed
php artisan serve
```

El servicio puede preseleccionarse con `?service=design`, `?service=video` o `?service=render`. Los borradores se consultan en `/app/requests/drafts`.

