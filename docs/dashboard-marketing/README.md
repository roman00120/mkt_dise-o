# Dashboard de Marketing

## Objetivo

Ofrecer a Marketing una primera pantalla funcional después del login: estado de solicitudes, atención requerida, entregables, actividad y accesos rápidos.

## Ruta y acceso

`GET /app` (`app.dashboard`) requiere `auth`, `active` y uno de los roles `marketing`, `supervisor` o `admin`. El dashboard redirige al rol Marketing desde el login; los otros dos roles pueden consultarlo explícitamente.

## Estados disponibles

- Normal: métricas y registros consultados desde la base de datos y limitados al usuario autenticado.
- Vacío: se muestran estados vacíos cuando no existen registros.
- Filtros visuales: `?filter=all|pending|in-progress|review|completed`.

Las solicitudes, entregables, notificaciones y catálogos se consultan desde sus tablas reales. Los seeders de desarrollo y demo están separados y nunca se ejecutan desde el seeder general.

Consulta también [architecture.md](architecture.md), [components.md](components.md), [responsive.md](responsive.md), [accessibility.md](accessibility.md), [demo-data.md](demo-data.md) y [testing.md](testing.md).
