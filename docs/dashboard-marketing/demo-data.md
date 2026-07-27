# Datos demostrativos

`MarketingDashboardService` usa seis solicitudes, tres servicios, dos entregables y cuatro actividades con nombres y folios coherentes con la documentación de producto. Los datos se generan en memoria por request y no se insertan en SQLite.

La salud de fechas se calcula usando `America/Mexico_City`:

- `on_time`: más de 3 días restantes o solicitud completada.
- `due_soon`: entre 0 y 3 días restantes.
- `overdue`: fecha anterior a hoy y no completada.
- `without_date`: no existe fecha requerida.

No hay credenciales ni datos sensibles en estos fixtures.

