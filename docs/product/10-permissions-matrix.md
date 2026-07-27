# 10. Matriz de roles y permisos

Leyenda: **✓** permitido, **—** no permitido, **E** permitido solo dentro de su equipo/servicio, **S** supervisor o permiso explícito.

| Permiso | Marketing | Diseño | Video | Render | Supervisor | Admin |
|---|---:|---:|---:|---:|---:|---:|
| `request.view.own` | ✓ | E | E | E | ✓ | ✓ |
| `request.view.team` | — | E | E | E | ✓ | ✓ |
| `request.view.all` | — | — | — | — | ✓ | ✓ |
| `request.create` | ✓ | — | — | — | ✓ | ✓ |
| `request.update.own` | ✓* | — | — | — | S | ✓ |
| `request.assign` | — | — | — | — | ✓ | ✓ |
| `request.reassign` | — | — | — | — | ✓ | ✓ |
| `request.change-status` | — | E | E | E | ✓ | ✓ |
| `request.cancel` | ✓* | — | — | — | ✓ | ✓ |
| `request.reject` | — | — | — | — | ✓ | ✓ |
| `request.approve` | ✓ | — | — | — | S | ✓ |
| `request.close` | — | — | — | — | ✓ | ✓ |
| `comment.create` | ✓ | E | E | E | ✓ | ✓ |
| `comment.edit.own` | ✓ | E | E | E | ✓ | ✓ |
| `comment.internal.create/view` | — | E | E | E | ✓ | ✓ |
| `comment.moderate` | — | — | — | — | S | ✓ |
| `file.upload/download` | ✓ | E | E | E | ✓ | ✓ |
| `file.delete.own` | ✓* | E | E | E | S | ✓ |
| `file.delete.any` | — | — | — | — | S | ✓ |
| `deliverable.upload` | — | E | E | E | ✓ | ✓ |
| `deliverable.approve` | ✓ | — | — | — | S | ✓ |
| `report.view` | — | — | — | — | ✓ | ✓ |
| `audit.view` | — | — | — | — | S | ✓ |
| `user/role/permission.manage` | — | — | — | — | — | ✓ |
| `catalog.manage/settings.manage` | — | — | — | — | S | ✓ |

`*` sujeto a estado; por ejemplo, Marketing edita antes de validación y cancela según política aprobada. Todo permiso debe verificarse en backend, no solo ocultando controles.

## Mínimo privilegio

Un usuario tiene un rol base y permisos adicionales explícitos. El alcance por servicio y departamento se evalúa antes del rol; un permiso especial se registra en auditoría y puede caducar.
