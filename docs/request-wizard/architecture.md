# Arquitectura

`RequestWizardController` coordina pasos y delega persistencia a `RequestDraftService`. `RequestSubmissionService` realiza el envío transaccional a `pending`. `RequestFileService` valida y almacena archivos privados. `FolioGenerator` administra una secuencia anual bloqueada en base de datos. `RecommendedDateService` calcula días hábiles de lunes a viernes.

La entidad principal es `CreativeRequest`; los datos específicos viven en `creative_request_details.data`. Los archivos y eventos mínimos tienen tablas separadas.

