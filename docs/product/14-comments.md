# 14. Comentarios y notas

## Tipos

| Tipo | Visible para | Uso |
|---|---|---|
| Comentario compartido | Marketing, equipo relacionado, supervisor y admin autorizado | Decisiones, preguntas y feedback de trabajo |
| Nota interna | Equipo creativo, supervisor y admin autorizado | Coordinación interna; nunca Marketing |

## Reglas

- Crear comentario requiere acceso a la solicitud.
- Editar solo el propio comentario dentro de una ventana configurable; mostrar “editado”.
- Eliminar es lógico, requiere permiso y deja auditoría; no borrar evidencia.
- Respuestas pueden agrupar una conversación sin duplicar contexto.
- Menciones notifican al usuario mencionado, pero no amplían visibilidad.
- Adjuntos heredan permisos del comentario y se validan como cualquier archivo.
- Un comentario de corrección debe estar asociado a la versión o entregable cuando sea posible.
- Moderación de supervisor/admin puede ocultar contenido, conservando auditoría.

## Guardrail de privacidad

El tipo de comentario se fija al crear y se muestra de forma muy diferenciada. Las consultas, APIs, notificaciones, búsquedas y exportaciones filtran notas internas por permiso; nunca se confía solo en el frontend.
