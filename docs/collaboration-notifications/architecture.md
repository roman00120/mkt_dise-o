# Arquitectura

Controllers delgados delegan en `Services\Comments` y `Services\Notifications`. Las policies validan alcance y visibilidad antes de consultar o mutar. Las vistas reciben únicamente comentarios filtrados por el controlador.
