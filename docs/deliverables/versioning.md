# Versionado

El siguiente número se calcula dentro de transacción con lock sobre el entregable y unique `(deliverable_id, version_number)`. Las versiones enviadas o aprobadas no se editan; las correcciones crean una versión nueva sin copiar archivos automáticamente.
