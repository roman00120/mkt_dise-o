# 11. Reglas de negocio

1. Solo usuarios activos acceden.
2. Solo Marketing, supervisor o administrador crea solicitudes.
3. Cada solicitud pertenece a uno y solo uno de los tres servicios creativos.
4. Un creativo trabaja únicamente solicitudes de su servicio salvo permiso especial.
5. No se puede enviar una solicitud sin sus campos mínimos.
6. La prioridad solicitada por Marketing no equivale a prioridad operativa; el supervisor puede ajustarla.
7. No se puede asignar a un usuario inactivo o de otro servicio sin permiso.
8. Los cambios de estado obedecen la máquina definida.
9. Rechazo, cancelación y solicitud de correcciones requieren motivo/comentario.
10. Una solicitud no finaliza sin entregable aprobado.
11. Todo entregable pertenece a una versión; una versión no se reemplaza en sitio.
12. La aprobación registra usuario, fecha, versión y contexto.
13. Las notas internas nunca son visibles para Marketing.
14. Cambios relevantes quedan en historial y los cambios administrativos en auditoría.
15. Las solicitudes cerradas no se eliminan físicamente; se conservan según política.
16. Los archivos se validan por extensión, MIME, tamaño, nombre seguro y autorización.
17. Los folios son únicos y no se reutilizan.
18. Las notificaciones no conceden acceso: el destino vuelve a validar permisos.
19. Una fecha requerida menor al tiempo mínimo debe advertir riesgo y requerir decisión del supervisor.
20. Toda acción sensible debe ser idempotente para evitar duplicados por reintento.

## Validación y seguridad

Autorización en backend, protección contra acceso directo a archivos, sanitización de nombres, límites configurables, prevención de exposición de notas internas y registro de eventos sensibles.
