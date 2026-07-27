# 4. Mapa de pantallas

Formato: **pantalla — ruta conceptual — acceso — objetivo — acción principal — dependencias / riesgos**.

## Públicas

| Pantalla | Ruta | Acceso | Objetivo y acción |
|---|---|---|---|
| Login | `/login` | Público | Acceder; iniciar sesión. |
| Recuperar contraseña | `/password/forgot` | Público | Solicitar enlace; enviar correo. |
| Restablecer contraseña | `/password/reset` | Token | Definir nueva contraseña; confirmar. |

## Marketing

| Pantalla | Ruta conceptual | Objetivo / acción principal | Vacío, error y riesgo |
|---|---|---|---|
| Dashboard | `/marketing` | Ver pendientes y crear solicitud | Sin solicitudes ofrece CTA; riesgo de saturación |
| Selección de servicio | `/marketing/requests/new/service` | Elegir uno de tres servicios | Explica alcance; no mezclar servicios |
| Plantilla | `.../template` | Elegir tipo | Si no hay plantilla, brief base |
| Brief | `.../brief` | Completar información | Validación por campo; riesgo de formulario largo |
| Archivos | `.../files` | Adjuntar referencias | Límites y formatos visibles |
| Fecha/prioridad | `.../schedule` | Indicar fecha y prioridad solicitada | Advertir fecha no garantizada |
| Revisión | `.../review` | Confirmar resumen | Permite volver sin perder datos |
| Confirmación | `.../submitted` | Mostrar folio y próximo paso | Error de envío permite reintentar |
| Mis solicitudes | `/marketing/requests` | Consultar, filtrar y abrir | Vacío ofrece crear |
| Por estado | `.../requests?status=` | Enfocar seguimiento | Filtros sin resultados explicados |
| Detalle | `/marketing/requests/{folio}` | Revisar avance y actuar | Oculta notas internas |
| Entregables | `.../deliverables` | Descargar y revisar versión | Archivo no disponible permite reintentar |
| Comentarios | `.../comments` | Conversar sobre entrega | Comentario de corrección obligatorio |
| Historial | `.../history` | Consultar trazabilidad | Solo lectura |
| Notificaciones | `/notifications` | Resolver alertas | Leídas/no leídas claras |
| Perfil | `/profile` | Editar datos propios | No administra permisos |

## Equipo creativo

Dashboard, Kanban, Asignaciones, Sin asignar, Detalle, Brief, Archivos, Comentarios, Versiones, Entregables, Historial, Carga, Notificaciones y Perfil. Sus rutas viven bajo `/creative`; Sin asignar solo aparece con permiso `request.assign`.

## Administración

Dashboard, Usuarios, Crear/Editar usuario, Roles, Permisos, Departamentos, Equipos, Categorías, Plantillas, Estados, Prioridades, Reportes, Bitácora, Configuración y Auditoría. Sus rutas viven bajo `/admin` y requieren permisos por módulo.

## Dependencias transversales

Toda pantalla protegida depende de usuario activo y autorización backend. Los detalles dependen de alcance de la solicitud; los catálogos dependen de configuración vigente; archivos dependen de permisos y validación de seguridad.
