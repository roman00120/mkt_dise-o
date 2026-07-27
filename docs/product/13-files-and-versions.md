# 13. Archivos y versiones

## Categorías

1. Brief y referencias: aportados por Marketing.
2. Avances: trabajo en progreso, visibles según alcance.
3. Entregables: versión enviada a revisión.
4. Archivos finales: entregable aprobado, protegido contra reemplazo accidental.

## Reglas de acceso

| Acción | Marketing | Creativo | Supervisor | Admin |
|---|---|---|---|---|
| Subir referencia | Propia solicitud | Según permiso | ✓ | ✓ |
| Subir avance | — | Su servicio | ✓ | ✓ |
| Subir entregable | — | Su servicio | ✓ | ✓ |
| Descargar visible | ✓ | E | ✓ | ✓ |
| Eliminar propio | Antes de cierre, según tipo | Antes de revisión | S | ✓ |
| Eliminar cualquier | — | — | S | ✓ |

## Versionado

El sistema asigna `v1`, `v2`, etc. Una nueva ronda de correcciones crea una nueva versión; no se sobrescribe una versión enviada o aprobada. Cada versión registra autor, fecha, archivos, comentario de entrega y estado. El archivo final se identifica por aprobación, no por el nombre del archivo.

## Validaciones conceptuales

Extensiones permitidas por catálogo, MIME real, tamaño máximo configurable, nombre normalizado, antivirus/escaneo cuando exista infraestructura y bloqueo de ejecutables no permitidos. Los enlaces no deben exponer rutas físicas ni permitir acceso sin autorización.

## Decisiones pendientes

Tamaño máximo, retención, formatos por servicio, previews, anotaciones sobre imagen/video y almacenamiento técnico definitivo se mantienen en [decisiones abiertas](18-open-decisions.md).
