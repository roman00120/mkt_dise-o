# Fase 6 · Solicitudes de Marketing

La experiencia de Marketing consulta solicitudes reales en `/app/requests`, con búsqueda, filtros, paginación, detalle, archivos privados, historial, duplicación y cancelación controlada. Esta fase no incluye panel creativo, Kanban, asignación, comentarios funcionales, entregables ni aprobación.

## Alcance

Marketing consulta únicamente sus solicitudes. Admin y Supervisor conservan consulta según la matriz actual; los roles creativos reciben 403. Las acciones que cambian estado usan POST, CSRF, policy y transacción.

## Documentos

- [Arquitectura](architecture.md) · [Rutas](routes.md) · [Autorización](authorization.md)
- [Filtros y búsqueda](filters-and-search.md) · [Detalle](request-detail.md) · [Archivos](files.md)
- [Duplicación](duplication.md) · [Cancelación](cancellation.md) · [Responsive](responsive.md)
- [Accesibilidad](accessibility.md) · [Seguridad](security.md) · [Testing](testing.md)

## Comandos

`php artisan migrate`, `php artisan db:seed`, `php artisan test`, `npm.cmd run build`, `vendor/bin/pint --test`.
