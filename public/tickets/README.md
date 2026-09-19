# Sistema de Tickets - Total Ground

Sistema de gesti�n de tickets en PHP puro, sin dependencias externas, ideal para hosting compartido con cPanel.

## Tecnolog�as

- **Backend**: PHP 8.x puro (sin frameworks)
- **Frontend**: HTML + CSS + JavaScript vanilla
- **Base de datos**: JSON (archivo local)
- **Servidor**: Compatible con Apache (cPanel)

## Estructura del Proyecto

```
/
+-- config.php                # Configuraci�n y funciones helper
+-- index.php                 # Router principal
+-- api/
�   +-- auth.php              # Autenticaci�n
�   +-- tickets.php           # CRUD de tickets
+-- views/
�   +-- login.html
�   +-- dashboard.html
�   +-- create-ticket.html
�   +-- ticket-detail.html
+-- public/
�   +-- style.css
�   +-- auth.js
�   +-- dashboard.js
+-- data/
    +-- data.json             # Base de datos (se crea autom�ticamente)
```

## Instalaci�n en cPanel

1. **Subir archivos**: Copia todos los archivos a tu directorio `public_html` usando SFTP o el Administrador de archivos de cPanel

2. **Permisos de carpeta**: Aseg�rate que la carpeta `/data` tiene permisos 755 (lectura y escritura)

3. **Acceder**: Abre en tu navegador
```
https://tu-dominio.com
```

## Instalaci�n en Servidor Local (Desarrollo)

1. **Requisitos**: PHP 8.0+ con soporte para sesiones

2. **Ejecutar servidor local**:
```bash
cd /ruta/del/proyecto
php -S localhost:8000
```

3. **Acceder**:
```
http://localhost:8000
```

## Usuarios

### Colaboradores
- Inician sesi�n seleccionando su departamento (sin usuario ni contrase�a)
- Solo ven tickets de su departamento
- Pueden crear nuevos tickets
- No pueden editar tickets

### Administradores
- **Usuario**: admin
- **Contrase�a**: admin123
- Ven todos los tickets de todos los departamentos
- Pueden actualizar estado y cerrar tickets
- Reciben notificaciones por email

## Departamentos

- Soporte T�cnico
- Ventas
- Recursos Humanos
- Facturaci�n
- Operaciones

(Editable f�cilmente en `config.php`)

## Funcionalidades

### Login
- **Colaboradores**: Seleccionar departamento (sin contrase�a)
- **Admin**: Usuario y contrase�a hardcodeados

### Dashboard
- Listado de tickets con filtros por estado y prioridad
- Estad�sticas r�pidas (Total, Abiertos, En Proceso, Cerrados)
- B�squeda de tickets por ID o t�tulo

### Crear Ticket
- T�tulo y descripci�n requeridos
- Seleccionar departamento y prioridad
- Env�o de email de notificaci�n al admin

### Ver/Editar Ticket
- Detalles completos del ticket
- Admin: Cambiar estado (Abierto ? En Proceso ? Cerrado)
- Solo admin puede hacer cambios

## C�mo Personalizar

### Cambiar credenciales admin
Edita en `config.php`:
```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASSWORD', 'admin123');
```

### Agregar/Editar departamentos
En `config.php`:
```php
define('DEPARTMENTS', [
    'soporte' => 'Soporte T�cnico',
    'ventas' => 'Ventas',
    // Agrega m�s aqu�
]);
```

### Cambiar email del admin
En `config.php`:
```php
define('ADMIN_EMAIL', 'tu-email@totalground.com');
```

### Colores personalizados
Edita `public/style.css` variables CSS:
```css
:root {
  --primary: #E31E24;      /* Color principal */
  --secondary: #1A1A1A;    /* Color secundario */
}
```

## Base de Datos

El archivo `data/data.json` se crea autom�ticamente. Estructura:

```json
{
  "tickets": [
    {
      "id": 1,
      "title": "Problema con sistema",
      "description": "El sistema no carga",
      "department": "soporte",
      "priority": "alta",
      "creator": "Juan P�rez",
      "status": "abierto",
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00",
      "assigned_to": null
    }
  ],
  "last_id": 1
}
```

## Email Notifications

Los tickets se notifican por email al admin usando `mail()` de PHP. Para que funcione:

1. El servidor debe tener mail() configurado
2. Aseg�rate que el SMTP est� disponible en cPanel

## Notas Importantes

- Sin dependencias externas (npm, composer)
- PHP puro y seguro (entrada sanitizada)
- Sessions en servidor (no localStorage)
- Compatible con cualquier hosting compartido
- Archivo JSON con autobackup

## Troubleshooting

### "Carpeta data no tiene permisos"
```bash
chmod 755 data/
```

### "No se crean tickets"
- Verifica permisos de escritura en `/data`
- Revisa los logs del servidor

### "No llegan emails"
- Verifica que mail() est� habilitado en PHP
- Comprueba la configuraci�n de SMTP del servidor

## Soporte

Para soporte interno, contacta al equipo de TI

