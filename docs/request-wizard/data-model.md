# Modelo de datos

Tablas: `creative_requests`, `creative_request_details`, `creative_request_files`, `creative_request_events` y `creative_request_sequences`.

`CreativeRequest` usa UUID, folio único, requester, servicio, tipo, campos generales, fecha, prioridad solicitada, estado, paso actual, timestamps de autosave/envío y soft deletes. Los enums restringen servicio, prioridad y estado.

Estados usados activamente: `draft` y `pending`. Los demás están preparados para fases posteriores.

