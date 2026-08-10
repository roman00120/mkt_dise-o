# Fase 9 · Colaboración y notificaciones

Fase 9 implementa colaboración persistente y notificaciones in-app para TG Creative Hub. No incluye chat en tiempo real, WebSockets, mensajería privada, push, correo productivo ni Fase 10.

## Arquitectura

`Comment` es polimórfico, pero el morph map admite únicamente `creative_request`, `deliverable` y `deliverable_version`. La visibilidad se filtra en backend: `public` para Marketing y equipo autorizado; `internal` solo para Creative/Supervisor. Las notificaciones usan el canal database de Laravel y preferencias in-app.

Los comentarios y sus menciones se guardan dentro de una transacción. Las notificaciones se envían con `DB::afterCommit`, por lo que no se avisa una operación que haya sido revertida. No se guardan modelos serializados, cuerpos de notas internas ni rutas de almacenamiento en la notificación.

## Funcionalidad

- Comentarios en solicitudes y entregables.
- Notas internas separadas visual y técnicamente.
- Respuestas de un nivel.
- Menciones por IDs validados, máximo 10.
- Adjuntos privados, máximo 5 por comentario y 15 MB por archivo.
- Edición del autor dentro de 15 minutos con revisiones.
- Eliminación lógica y placeholder de comentario eliminado.
- Centro de notificaciones, filtros, no leídas y marcado masivo.
- Preferencias básicas; asignaciones, información solicitada y correcciones son críticas.

Consulta los documentos específicos de esta carpeta para rutas, seguridad, accesibilidad y pruebas.
