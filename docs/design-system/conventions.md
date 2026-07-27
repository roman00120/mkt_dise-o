# Convenciones

- Tokens semánticos en `tokens.css`; no crear nombres físicos como `red-1` o `dark-two`.
- Estilos base en `base.css`; patrones compartidos en `components.css`.
- Alpine solo para interacción local ligera; no persistencia ni reglas de negocio.
- Lucide es la única familia de iconos. Usar `data-lucide` y `aria-hidden` si es decorativo.
- Textos visibles en español y datos demo realistas, no lorem ipsum.
- Componentes Blade anónimos cuando no se necesita clase PHP.
- La ruta `/design-system` queda bloqueada si `config('app.env')` no es `local`.
- No introducir CSS inline, librerías de UI ni variantes duplicadas sin documentar.
