# Concurrencia

Creación/edición de comentarios y menciones usa transacciones e índices únicos. Las notificaciones esperan commit. Los adjuntos se guardan dentro de una operación transaccional y se descargan por stream.
