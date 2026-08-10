# Fase 10 — Administración

El panel administrativo está disponible en `/admin` únicamente para usuarios autenticados, activos y con el rol `admin`. Supervisores y equipos operativos no reciben acceso implícito.

## Módulos

- Dashboard con métricas operativas, actividad reciente y alertas.
- Usuarios: alta, edición controlada, roles existentes, departamentos, estados y restablecimiento de acceso.
- Departamentos y tipos de solicitud con activación controlada.
- Configuración permitida: tiempos recomendados, organización y notificaciones de base de datos.
- Consulta global de solicitudes, auditoría administrativa y exportaciones CSV.

## Seguridad

Las acciones sensibles usan policies, confirmación reciente de contraseña y rate limiting. Las operaciones de estado requieren motivo y confirmación. No se permiten contraseñas administradas manualmente, roles arbitrarios, eliminación masiva, importación ni cambios directos a datos de acceso. La auditoría excluye contraseñas, tokens, secretos e IPs sensibles del metadata.

## Desarrollo y validación

```powershell
php artisan migrate:fresh --seed
php artisan test
php artisan view:cache
vendor/bin/pint --test
npm.cmd run build
```

El seeder general no crea usuarios ni datos de negocio. Crea el administrador mediante `php artisan admin:create`; el comando solicita la contraseña de forma interactiva, valida que el correo no exista y nunca la imprime ni la registra.
