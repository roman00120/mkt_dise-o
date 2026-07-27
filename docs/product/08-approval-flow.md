# 8. Revisión y aprobación

```mermaid
sequenceDiagram
    participant C as Creativo
    participant S as Sistema
    participant M as Marketing
    C->>S: Publica versión y marca lista
    S->>M: Notifica entregable disponible
    M->>S: Abre versión y descarga
    alt Correcciones
        M->>S: Comentario obligatorio + solicita cambios
        S->>C: Notifica correcciones
        C->>S: Publica nueva versión
    else Aprobación
        M->>S: Confirma aprobación
        S->>S: Registra usuario, fecha y versión
        S->>C: Notifica aprobación
    end
```

## Revisión

Marketing ve archivo, versión, fecha, autor, comentarios y relación con la solicitud. Puede comentar sobre el entregable o marcar el archivo relacionado; la zona específica puede ser un dato de anotación futuro, no requisito inicial.

## Correcciones

Comentario obligatorio, estado `Correcciones solicitadas`, notificación al responsable y registro en historial. El creativo no sobrescribe una versión aprobada: crea una nueva versión vinculada.

## Aprobación

Requiere confirmación explícita y bloquea modificaciones accidentales sobre esa versión. La solicitud pasa a `Aprobada`; el cierre a `Finalizada` ocurre cuando existe entregable aprobado y el supervisor/sistema confirma condiciones.

## Reversión

Recomendación: Marketing no revierte una aprobación. Supervisor o administrador puede reabrirla excepcionalmente con motivo obligatorio, permiso especial y auditoría completa.
