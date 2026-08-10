# Arquitectura

`MarketingRequestQuery` concentra alcance, búsqueda, filtros, orden y eager loading. `RequestController` prepara listado y detalle; `RequestDuplicationService` y `RequestCancellationService` encapsulan transacciones. Blade presenta datos escapados y no ejecuta consultas.

La migración añade `duplicated_from_id`, `cancelled_at` y `cancellation_reason`. Los archivos no se duplican automáticamente.
