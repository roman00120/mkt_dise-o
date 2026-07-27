# Dashboard de Marketing

## Objetivo

Ofrecer a Marketing una primera pantalla funcional después del login: estado de solicitudes, atención requerida, entregables, actividad y accesos rápidos.

## Ruta y acceso

`GET /app` (`app.dashboard`) requiere `auth`, `active` y uno de los roles `marketing`, `supervisor` o `admin`. El dashboard redirige al rol Marketing desde el login; los otros dos roles pueden consultarlo explícitamente.

## Estados disponibles

- Normal: datos de demostración realistas.
- Vacío: `/app?demo=empty`.
- Carga: `/app?demo=loading`.
- Error seguro: `/app?demo=error`.
- Filtros visuales: `?filter=all|pending|in-progress|review|completed`.

Las rutas de solicitudes, perfil y notificaciones son placeholders protegidos. No existe persistencia de solicitudes en esta fase.

Consulta también [architecture.md](architecture.md), [components.md](components.md), [responsive.md](responsive.md), [accessibility.md](accessibility.md), [demo-data.md](demo-data.md) y [testing.md](testing.md).

