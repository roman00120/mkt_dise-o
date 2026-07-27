# Roles y estados

Roles soportados: `admin`, `marketing`, `design`, `video`, `render` y `supervisor`.

Estados soportados:

- `active`: puede iniciar sesión y acceder a sus rutas.
- `inactive`: no puede iniciar sesión.
- `suspended`: no puede iniciar sesión.

El middleware `active` valida el estado en cada ruta protegida. El middleware `role` limita el acceso por rol y devuelve 403 cuando corresponde.

