# Arquitectura

El controlador `App\Http\Controllers\App\DashboardController` valida el filtro y delega la composición de datos a `App\Services\Dashboard\MarketingDashboardService`. La vista `resources/views/app/dashboard.blade.php` compone secciones y componentes Blade; no contiene consultas ni arrays de negocio.

El servicio devuelve `metrics`, `attentionItems`, `recentRequests`, `pendingDeliverables`, `recentActivity`, `serviceCards` y `filter`. Los datos son fixtures en memoria para no introducir una entidad temporal de solicitudes.

El layout `resources/views/layouts/app.blade.php` es reutilizable: navegación, sidebar responsive, topbar, flash messages, Vite, Alpine, skip link y contenido.

