# Componentes

## Primitivas

`x-ui.button`, `x-ui.icon-button`, `x-ui.input`, `x-ui.textarea`, `x-ui.select`, `x-ui.badge`, `x-ui.card`, `x-ui.alert`, `x-ui.avatar`, `x-ui.tabs`, `x-ui.modal`, `x-ui.drawer`, `x-ui.dropdown`, `x-ui.progress`, `x-ui.empty-state`, `x-ui.skeleton`, `x-ui.spinner`.

## Especializados

`x-request.status-badge`, `x-request.priority-badge`, `x-request.service-badge`, `x-data.stat`, `x-data.table`, `x-file.upload-zone`, `x-file.item`, `x-comment.item`, `x-navigation.sidebar`, `x-navigation.topbar`, `x-wizard.stepper`.

Ejemplo:

```blade
<x-ui.button variant="primary" size="md">
    Nueva solicitud
</x-ui.button>
```

Cada componente acepta `class` mediante merge de atributos. No usar componentes para ocultar reglas de negocio; esta fase solo demuestra estados visuales.
