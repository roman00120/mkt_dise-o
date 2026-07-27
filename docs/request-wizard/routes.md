# Rutas

- `GET /app/requests/create` — iniciar wizard.
- `POST /app/requests` — crear primer borrador.
- `GET/PATCH /app/requests/drafts/{creativeRequest}/edit` — retomar y guardar pasos.
- `POST /app/requests/drafts/{creativeRequest}/autosave` — autoguardado JSON.
- `POST/DELETE /app/requests/drafts/{creativeRequest}/files` — archivos.
- `POST /app/requests/drafts/{creativeRequest}/submit` — envío final.
- `GET /app/requests/{creativeRequest}/confirmation` — confirmación.
- `GET /app/requests/drafts` — listado mínimo de borradores.

Todas están protegidas por sesión, usuario activo, rol y policy.

