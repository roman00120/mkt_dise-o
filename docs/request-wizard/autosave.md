# Guardado y autoguardado

Cada avance de paso persiste el borrador y actualiza `current_step` y `last_autosaved_at`. Existe endpoint JSON `POST /autosave` para conectar debounce de Alpine de 800–1200 ms sin crear nuevos registros.

El folio se asigna al primer borrador persistente. Un borrador conserva su folio al editarlo o enviarlo.

