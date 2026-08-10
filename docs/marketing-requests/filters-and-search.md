# Filtros y búsqueda

`q` busca únicamente `folio` y `title`. Estado, servicio, prioridad, fechas, atención y borradores se validan con enums y reglas de fecha. `sort`, `direction` y `per_page` usan whitelist estricta: actualización, creación, fecha requerida, prioridad y 10/25/50 registros. Laravel conserva el query string en paginación.
