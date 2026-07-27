# Accesibilidad

- Focus visible con ring rojo semántico.
- Labels visibles; placeholder nunca sustituye a label.
- Errores junto al campo y `aria-invalid` cuando aplica.
- Botones solo icono tienen `aria-label` y `title`.
- Roles `dialog`, `tab`, `tablist`, `progressbar` y `status` en patrones correspondientes.
- Estados comunican texto e icono, no solo color.
- Áreas táctiles principales de al menos 44px.
- Escape cierra modal/drawer; el foco y bloqueo de scroll requieren validación adicional al integrar producción.
- `prefers-reduced-motion` desactiva transiciones prolongadas.

Antes de producción: ejecutar auditoría automatizada y revisión manual con teclado/lector de pantalla en 320, 768 y 1440px.
