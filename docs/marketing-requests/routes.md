# Rutas

- `GET /app/requests` · `app.requests.index`
- `GET /app/requests/{creativeRequest}` · `app.requests.show`
- `GET /app/requests/{creativeRequest}/files/{file}/download` · descarga autorizada
- `POST /app/requests/{creativeRequest}/duplicate` · nuevo borrador
- `POST /app/requests/{creativeRequest}/cancel` · cancelación

Todas requieren `auth`, `active` y rol de portal Marketing. Las rutas del Wizard de Fase 5 se mantienen.
