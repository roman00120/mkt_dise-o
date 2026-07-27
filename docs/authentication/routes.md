# Rutas de autenticación

| Método | Ruta | Acceso |
|---|---|---|
| GET | `/login` | Invitado |
| POST | `/login` | Invitado, límite 5/minuto |
| POST | `/logout` | Usuario autenticado |
| GET/POST | `/forgot-password` | Invitado |
| GET/POST | `/reset-password/{token}` | Invitado |
| GET/POST | `/confirm-password` | Usuario autenticado y activo |

No se exponen rutas de registro ni verificación de correo en esta fase.

## Destinos por rol

- `admin` → `/admin`
- `marketing` → `/app`
- `design` → `/creative/design`
- `video` → `/creative/video`
- `render` → `/creative/render`
- `supervisor` → `/creative`

Los destinos son placeholders protegidos; no contienen todavía módulos de negocio.

