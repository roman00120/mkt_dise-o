# 12. Notificaciones

| Evento | Destinatario | Prioridad | Canal |
|---|---|---|---|
| Solicitud creada | Supervisor/equipo | Obligatoria, inmediata | Sistema; correo opcional |
| Asignada/reasignada | Responsable | Inmediata | Sistema; correo configurable |
| Rechazada/cancelada | Marketing y afectados | Inmediata | Sistema |
| Información solicitada | Marketing | Inmediata | Sistema; correo opcional |
| Comentario/mención | Participantes/mencionado | Agrupable, inmediata para mención | Sistema |
| Archivo cargado | Participantes relevantes | Agrupable | Sistema |
| Entregable disponible | Marketing | Inmediata | Sistema; correo opcional |
| Correcciones solicitadas | Responsable | Inmediata y obligatoria | Sistema |
| Nueva versión | Marketing | Inmediata | Sistema |
| Aprobada/finalizada | Participantes | Inmediata | Sistema |
| Fecha próxima/vencida | Responsable y supervisor | Agrupable diaria | Sistema; configurable |

## Reglas

- La bandeja interna es el canal base y no se desactiva para eventos críticos.
- El correo es configurable por usuario/administrador, excepto alertas definidas como obligatorias.
- Varias cargas o comentarios del mismo contexto pueden agruparse para evitar ruido.
- Una notificación silenciosa actualiza contador o actividad sin interrumpir.
- Cada notificación enlaza al contexto y se valida con permisos actuales.
- Se conserva estado leído/no leído y fecha de lectura.
