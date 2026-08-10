# Fase 11 — Reportes y analítica operativa

Módulo autenticado para consultar operación real por periodo, servicio y alcance. No incluye predicción, BI externo, costos, rankings ni datos públicos.

## Rutas

Administración: `/admin/reports`, `/admin/reports/executive`, `/admin/reports/operations`, `/admin/reports/requests`, `/admin/reports/export/csv` y `/admin/reports/export/pdf`. Supervisión: `/creative/reports`. Métricas personales: `/creative/my-metrics` y `/app/reports` para Marketing.

## Fuente y limitaciones

La demanda usa `submitted_at`; los borradores no cuentan como enviados. Los ciclos válidos usan `completed_at - submitted_at`. Los periodos de estado se reconstruyen desde eventos y nunca modifican solicitudes ni eventos. Las métricas respetan usuario, rol y servicio.
