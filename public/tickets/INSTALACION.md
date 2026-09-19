# Instalaci�n del Sistema de Tickets

## M�todo 1: cPanel (Recomendado para Producci�n)

### Paso 1: Descargar/Preparar archivos
- Descarga todos los archivos del proyecto
- No necesitas npm, composer ni dependencias

### Paso 2: Subir a cPanel
1. Accede a cPanel en tu hosting
2. Ve a **Administrador de archivos**
3. Sube todos los archivos a la carpeta **public_html**
4. Aseg�rate que la estructura sea:
   ```
   public_html/
   +-- index.php
   +-- config.php
   +-- .htaccess
   +-- api/
   +-- views/
   +-- public/
   +-- data/
   ```

### Paso 3: Permisos
1. Click derecho en carpeta **data** ? Cambiar permisos
2. Establece permisos a **755**
3. Aplica cambios recursivamente

### Paso 4: Acceder
- Abre en tu navegador: `https://tu-dominio.com`

---

## M�todo 2: Servidor Local (Desarrollo)

### Requisitos
- PHP 8.0+
- Ninguna otra dependencia

### Pasos

1. **Descarga el proyecto** en una carpeta

2. **Ejecuta PHP**:
   ```bash
   cd ruta/del/proyecto
   php -S localhost:8000
   ```

3. **Abre en navegador**:
   ```
   http://localhost:8000
   ```

---

## M�todo 3: XAMPP / WAMP / LAMP

1. Copia la carpeta del proyecto en:
   - **XAMPP**: `C:\xampp\htdocs\tickets`
   - **WAMP**: `C:\wamp\www\tickets`
   - **LAMP**: `/var/www/html/tickets`

2. Accede:
   - **Windows**: `http://localhost/tickets`
   - **Linux**: `http://localhost/tickets`

---

## Acceso de Prueba

### Colaborador
- Selecciona cualquier departamento
- No necesita usuario ni contrase�a

### Administrador
- **Usuario**: admin
- **Contrase�a**: admin123

---

## Verificaci�n

Para verificar que funciona:

1. **Login page** debe aparecer sin errores
2. **Inicia como colaborador** seleccionando un departamento
3. **Crea un ticket** de prueba
4. **Verifica que aparece** en el dashboard

---

## Troubleshooting

### Blanco o error al cargar
- Verifica que PHP est� habilitado
- Comprueba permisos 755 en carpeta `/data`
- Revisa los logs del servidor

### Error "No se puede escribir en data/"
```bash
chmod 755 data/
```

### Cambiar email de notificaciones
Edita `config.php`:
```php
define('ADMIN_EMAIL', 'tu-email@empresa.com');
```

### Cambiar credencial admin
Edita `config.php`:
```php
define('ADMIN_PASSWORD', 'nueva-contrase�a');
```

---

## Seguridad

?? **IMPORTANTE**: Para uso en producci�n

1. Cambia la contrase�a del admin en `config.php`
2. Usa HTTPS (SSL)
3. Configura firewall para limitar acceso
4. Haz backups regulares de `data/data.json`

---

�Listo! Tu sistema de tickets est� funcionando.

