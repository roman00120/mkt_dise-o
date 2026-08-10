# Rutas

Panel: `/creative`, `/creative/design`, `/creative/video`, `/creative/render`.
Bandeja: `/creative/requests`; Kanban: `/creative/requests/kanban`; detalle: `/creative/requests/{creativeRequest}`; workload: `/creative/workload`.
Acciones POST: validate, assign, reassign, priority, internal-date, transition, request-information y reject. Marketing responde información mediante `POST /app/requests/{creativeRequest}/provide-information`.
