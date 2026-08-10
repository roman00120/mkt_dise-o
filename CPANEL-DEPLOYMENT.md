# Despliegue de TG Creative Hub en cPanel

Esta guía deja la aplicación lista para subir a un hosting cPanel. No subas el
archivo `.env` local ni contraseñas reales al repositorio.

## 1. Requisitos del hosting

En cPanel confirma:

- PHP 8.2 o superior; se recomienda PHP 8.3.
- Extensiones PHP: `openssl`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`,
  `ctype`, `json`, `fileinfo`, `bcmath` y `zip`.
- Composer 2, mediante Terminal de cPanel o el selector de aplicaciones.
- MySQL/MariaDB.
- HTTPS activo para el dominio.
- Acceso a Terminal/SSH recomendado.

## 2. Estructura recomendada

No uses la raíz completa del proyecto como document root. La estructura segura
es:

```text
/home/USUARIO/tg-creative-hub/       proyecto completo
/home/USUARIO/public_html/           document root del dominio
```

Configura el dominio o subdominio para que su document root sea:

```text
/home/USUARIO/tg-creative-hub/public
```

Así `app`, `config`, `database`, `resources`, `storage` y `.env` quedan fuera
del acceso público.

## 3. Subir los archivos

Sube al servidor el proyecto completo, excepto estos elementos de desarrollo:

- `.env` local.
- `node_modules`.
- `vendor` si Composer está disponible en el hosting.
- `public/hot`.
- `.git`.
- Datos demo o respaldos locales.

Con Terminal, desde la carpeta del proyecto:

```bash
composer install --no-dev --optimize-autoloader
```

El build frontend ya se genera localmente. Si Node.js está disponible en cPanel,
puedes regenerarlo con:

```bash
npm ci
npm run build
```

## 4. Crear la base de datos MySQL

En cPanel abre **MySQL Databases** y crea:

1. Una base de datos.
2. Un usuario exclusivo para la aplicación.
3. Una contraseña fuerte.
4. Asigna el usuario a la base con todos los privilegios.

cPanel puede anteponer el nombre de la cuenta. Usa el nombre completo que
muestre cPanel en el `.env`.

## 5. Crear el archivo .env

Copia `.env.cpanel.example` como `.env`:

```bash
cp .env.cpanel.example .env
```

Edita obligatoriamente:

```env
APP_URL=https://TU-DOMINIO.COM
DB_DATABASE=nombre_real_de_cpanel
DB_USERNAME=usuario_real_de_cpanel
DB_PASSWORD=contrasena_real
MAIL_HOST=servidor_smtp_real
MAIL_USERNAME=correo_real
MAIL_PASSWORD=contrasena_real
MAIL_FROM_ADDRESS=no-reply@tu-dominio.com
```

Genera la clave una sola vez:

```bash
php artisan key:generate --force
```

No uses `APP_DEBUG=true` en producción.

## 6. Ejecutar migraciones

Con el `.env` ya configurado:

```bash
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder --force
```

`ProductionSeeder` carga catálogos, departamentos y datos operativos base. No
carga usuarios con contraseñas conocidas.

## 7. Crear los usuarios reales

Define temporalmente el nombre y correo del administrador en `.env`:

```env
ADMIN_NAME=Nombre del administrador
ADMIN_EMAIL=admin@tu-dominio.com
```

Ejecuta:

```bash
php artisan production:users
```

El comando solicita las contraseñas de forma interactiva. Usa contraseñas
únicas de mínimo 12 caracteres. No las guardes en este archivo.

Después elimina o protege los valores temporales de `ADMIN_NAME` y
`ADMIN_EMAIL` si ya no son necesarios.

## 8. Permisos

Laravel necesita escritura en:

```text
storage/
bootstrap/cache/
```

Con Terminal Linux:

```bash
chmod -R 775 storage bootstrap/cache
```

Si el hosting usa otro usuario/grupo, aplica el propietario que indique cPanel.
No uses permisos `777` salvo una prueba temporal y controlada.

## 9. Optimizar la aplicación

Después de configurar `.env`, migraciones y usuarios:

```bash
php artisan storage:link
php artisan optimize
php artisan app:optimize-production
```

Si `storage:link` no es necesario para los archivos privados, el comando puede
omitirse; los archivos privados deben seguir descargándose mediante las rutas
autorizadas de Laravel.

## 10. Correo de solicitudes

El flujo de correo funciona así:

1. Marketing envía una solicitud.
2. Hugo recibe notificación interna y correo con el resumen.
3. Hugo valida y asigna.
4. Ana, Gerardo o Jesús recibe correo según la asignación.

El SMTP debe ser el del correo corporativo. En cPanel revisa los datos en
**Email Accounts > Connect Devices**. Normalmente se usa puerto `587` con TLS
o `465` con SSL, según el proveedor.

Valida la configuración de correo desde Terminal (este comando no envía un
mensaje real):

```bash
php artisan mail:test correo-de-prueba@tu-dominio.com
```

Revisa los logs si falla:

```bash
tail -f storage/logs/laravel.log
```

## 11. Cron de Laravel

En **Cron Jobs** de cPanel agrega cada minuto:

```text
* * * * * cd /home/USUARIO/tg-creative-hub && php artisan schedule:run >> /dev/null 2>&1
```

Si más adelante se habilitan trabajos en cola, agrega un worker supervisado por
cPanel o por el proveedor:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

## 12. Verificación después de publicar

Comprueba:

```bash
php artisan about
php artisan migrate:status
php artisan route:list
php artisan optimize:clear
php artisan optimize
```

En el navegador verifica:

1. `https://TU-DOMINIO.COM/login`
2. Inicio de sesión.
3. Creación de solicitud por Marketing.
4. Notificación de Hugo.
5. Validación y asignación.
6. Aviso a Ana.
7. Entregable y envío final a Marketing.
8. Aprobación de Marketing.
9. Cierre por Hugo.

## 13. Errores frecuentes

### Error 500

Revisa `storage/logs/laravel.log`, confirma `.env`, permisos, PHP y extensiones.
Después ejecuta:

```bash
php artisan optimize:clear
php artisan optimize
```

### La página descarga PHP o muestra el listado de archivos

El document root no apunta a `public`. Corrígelo en el dominio/subdominio.

### No conecta MySQL

Confirma que `DB_HOST`, nombre completo de base, usuario completo y contraseña
sean exactamente los de cPanel. En muchos hostings `DB_HOST` es `localhost`.

### No llegan correos

Confirma SMTP, puerto, cifrado, usuario y remitente. El correo remitente debe
existir en el hosting o estar autorizado por el proveedor.

### Los estilos no aparecen

Confirma que exista `public/build/` y ejecuta localmente:

```bash
npm run build
```

Vuelve a subir `public/build/`.

## 14. Checklist final

- [ ] PHP 8.2+ activo.
- [ ] Dominio con HTTPS.
- [ ] Document root apuntando a `public`.
- [ ] `.env` creado y fuera de `public`.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_URL` real configurada.
- [ ] MySQL creado y conectado.
- [ ] Migraciones ejecutadas.
- [ ] Usuarios reales creados.
- [ ] Permisos de `storage` y `bootstrap/cache` correctos.
- [ ] SMTP configurado y probado.
- [ ] Build frontend presente.
- [ ] Cron configurado.
- [ ] Flujo Marketing -> Hugo -> Ana -> Marketing verificado.
