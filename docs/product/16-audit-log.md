# 16. Bitácora y auditoría

## Eventos mínimos

Inicio/cierre de sesión, creación y edición de solicitud, cambio de estado, asignación/reasignación, carga/eliminación de archivo, comentario eliminado, aprobación, cancelación, rechazo, cambio de roles/permisos, suspensión de usuario y cambio de configuración.

## Registro por evento

| Dato | Descripción |
|---|---|
| Usuario | Identidad que ejecutó la acción; sistema si es automática |
| Fecha | Timestamp con zona de almacenamiento definida |
| Acción | Verbo normalizado |
| Entidad | Tipo e identificador/folio |
| Valores | Anterior y nuevo, sin secretos |
| Contexto | Motivo, origen y correlación |
| IP | Solo cuando corresponda y según política |

Los eventos deben ser append-only para usuarios operativos. Administradores pueden consultar y filtrar, pero no alterar evidencia. Cambios de permisos, acceso a notas internas, aprobación y eliminación requieren mayor detalle.

## Retención

La duración, exportación y anonimización requieren decisión de privacidad/Legal. No almacenar contraseñas, tokens ni contenido sensible innecesario en auditoría.
