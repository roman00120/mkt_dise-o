# 3. Arquitectura de información

## Jerarquía

```mermaid
flowchart TD
    H[TG Creative Hub]
    H --> M[Portal Marketing]
    H --> C[Portal Creativo]
    H --> A[Administración]
    M --> M1[Inicio]
    M --> M2[Solicitudes]
    M2 --> M21[Nueva solicitud]
    M2 --> M22[Mis solicitudes]
    M2 --> M23[Detalle]
    C --> C1[Panel]
    C --> C2[Trabajo]
    C2 --> C21[Kanban]
    C2 --> C22[Asignaciones]
    C --> C3[Carga]
    A --> A1[Acceso y permisos]
    A --> A2[Catálogos]
    A --> A3[Operación y reportes]
```

## Entidades conceptuales

- Usuario: identidad, rol, departamento, estado activo.
- Solicitud: folio, servicio, brief, estado, prioridad, fechas y relaciones.
- Comentario: compartido o nota interna, con autor y contexto.
- Archivo: referencia, brief, avance, entregable o final.
- Versión: conjunto de entregables asociado a una revisión.
- Catálogo: servicio, tipo, categoría, prioridad, estado, plantilla.
- Evento: cambio relevante para historial y auditoría.

## Jerarquía de contenido del detalle

1. Encabezado: folio, título, estado, prioridad y fecha requerida.
2. Próxima acción y responsable.
3. Brief resumido y campos específicos del servicio.
4. Entregables/versiones y revisión.
5. Archivos y referencias.
6. Comentarios compartidos; notas internas separadas.
7. Historial y metadatos.

## Relación entre módulos

```mermaid
flowchart LR
    Solicitud --> Brief
    Solicitud --> Estado
    Solicitud --> Asignacion
    Solicitud --> Comentarios
    Solicitud --> Archivos
    Archivos --> Versiones
    Versiones --> Aprobacion
    Solicitud --> Notificaciones
    Solicitud --> Historial
    Catalogos --> Solicitud
    Permisos --> Solicitud
```

## Estados de interfaz

Cada módulo debe especificar carga, vacío, error, éxito y sin permisos. Un vacío debe explicar qué significa y ofrecer una acción; un error debe permitir reintentar; la carga debe preservar el contexto y evitar saltos de layout.
