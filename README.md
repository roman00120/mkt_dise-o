# 🎨 TG Creative Hub & Mesa de Control de Requerimientos

¡Hola! 👋 Te doy la bienvenida a este repositorio. Creé y desarrollé esta plataforma con el objetivo de centralizar, ordenar y dar seguimiento en tiempo real a todo el flujo de requerimientos creativos (Diseño Gráfico, Producción de Video y Renders 3D), integrando además una mesa de control ágil para tickets y solicitudes operativas en **Total Ground**.

---

## 🚀 ¿Por qué desarrollé este proyecto?

Antes de esta herramienta, la recepción de solicitudes se gestionaba por correos dispersos y mensajes informales, lo que provocaba pérdida de referencias, briefs incompletos y falta de trazabilidad en las fechas de entrega.

Diseñé este sistema para resolver esos puntos clave:
- **Flujo de trabajo estructurado:** Desde que Marketing levanta un brief detallado hasta la validación, asignación al creativo y entrega final aprobada.
- **Asistencia con Inteligencia Artificial (Gemini):** Integré un revisor automático con IA que analiza la redacción del brief antes del envío para asegurar que cuente con toda la información necesaria.
- **Control de SLA y Tiempos:** Monitoreo claro de prioridades (Baja, Media, Alta, Urgente), fechas límite y estado de cumplimiento.
- **Mesa de Soporte y Tickets:** Un módulo visual pensado para reportar y atender incidencias de soporte, sistemas, equipo y operaciones.

---

## 🛠️ Stack Tecnológico que utilicé

Elegí una combinación de tecnologías robustas y modernas que ofrecen gran rendimiento y una experiencia de usuario rápida:

### ⚙️ Backend
- **Laravel 12 (PHP 8.2+ / 8.3):** Arquitectura MVC limpia, Eloquent ORM, políticas de acceso (Policies), requests validados y migraciones estructuradas.
- **Autenticación:** Base con Laravel Breeze personalizada para múltiples perfiles y departamentos.
- **Base de Datos:** MySQL / MariaDB (con compatibilidad lista para SQLite en desarrollo).
- **Integración con IA:** Conexión a la API de Google Gemini (`gemini-flash-latest`) para enriquecimiento y análisis de solicitudes.

### 🎨 Frontend & Diseño
- **Laravel Blade:** Vistas nativas renderizadas en el servidor para velocidad y sencillez.
- **Tailwind CSS v4:** Diseño moderno con la paleta de identidad corporativa de Total Ground (Rojo corporativo, grises oscuros y modo oscuro), sombras suaves y micro-animaciones.
- **Alpine.js:** Reactividad ligera para componentes interactivos (dropdowns, modales, paneles colapsables) sin la sobrecarga de un framework pesado.
- **Lucide Icons:** Iconografía vectorial estilizada y consistente en toda la plataforma.
- **Vite:** Compilador de assets ultrarrápido con Hot Module Replacement.

---

## 👥 Flujo de Trabajo y Roles

El sistema está organizado en torno a los roles reales de la operación:

1. **Marketing (Solicitante):**
   - Asistente interactivo paso a paso (*Wizard*) para especificar servicio, formato, objetivos, público meta y adjuntar archivos o referencias.
   - Botón inteligente de revisión con IA antes de formalizar la entrega.
   - Seguimiento del estado en vivo (Pendiente, En Validación, En Proceso, Entregado, Aprobado).
2. **Supervisor / Aprobador (Hugo / Admin):**
   - Revisa la viabilidad de la solicitud, solicita información adicional si hace falta o asigna directamente al creativo especialista.
   - Ajusta fechas de entrega y prioridades operativas.
3. **Equipo Creativo (Ana Román, Gerardo, Jesús):**
   - Espacio de trabajo dedicado donde visualizan sus asignaciones, descargan briefs y suben entregables con versiones.
4. **Mesa de Tickets y Soporte:**
   - Panel ágil con métricas KPI (Abiertos, En Proceso, Cerrados, Vencidos), filtros de búsqueda instantáneos y centro de notificaciones dropdown.

---

## 💻 Instalación y Puesta en Marcha en Local

Para clonar y levantar el proyecto en tu entorno local, sigue estos pasos:

### 1. Clonar el repositorio
```bash
git clone https://github.com/roman00120/mkt_dise-o.git
cd mkt_dise-o
```

### 2. Instalar dependencias de PHP y JavaScript
```bash
# Dependencias de backend
composer install

# Dependencias de frontend
npm install
```

### 3. Configurar el archivo de entorno
Copia la plantilla `.env` y genera la clave de la aplicación:
```bash
cp .env.example .env
php artisan key:generate
```

Configura tus credenciales de base de datos en el archivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=solicitudes_mkt
DB_USERNAME=root
DB_PASSWORD=
```

*(Opcional)* Si cuentas con una API Key de Google Gemini para la revisión de briefs:
```env
GEMINI_API_KEY=tu_api_key_aqui
GEMINI_MODEL=gemini-flash-latest
```

### 4. Ejecutar migraciones y vincular almacenamiento
```bash
php artisan migrate --seed
php artisan storage:link
```

### 5. Compilar assets y levantar el servidor
```bash
# Compilar estilos y scripts
npm run build

# Iniciar servidor local
php artisan serve --port=8080
```

Abre tu navegador en: **`http://localhost:8080`**

---

## 👥 Cuentas de Acceso Preconfiguradas

Para agilizar pruebas en local, puedes iniciar sesión con las siguientes cuentas ya existentes:

| Perfil | Nombre | Correo Electrónico |
| :--- | :--- | :--- |
| **Administrador / Supervisor** | Hugo | `daniels@totalground.com` |
| **Administrador** | Esther | `esther@totalground.com` |
| **Marketing (Solicitante)** | Ángel Castañeda | `angel.castaneda@totalground.com` |
| **Marketing (Solicitante)** | Arlem Cruz | `arlem.cruz@totalground.com` |
| **Diseño Gráfico** | Ana Carolina Román | `ana.roman@totalground.com` |
| **Video y Multimedia** | Gerardo | `gerardo.lopez@totalground.com` |
| **3D / Renders** | Jesús | `jesus.cabrera@totalground.com` |

---

## 🎫 Módulo de Tickets Integrado

Para acceder directamente a la mesa de control de tickets operativos:
- **Ruta:** `http://localhost:8080/tickets/index.php`
- Incluye métricas en vivo, centro de notificaciones flotante con vista previa de folios, buscador multifiltro y soporte completo para modo claro y modo oscuro.

---

## 📂 Estructura del Código

```text
├── app/
│   ├── Http/Controllers/       # Controladores de solicitudes, entregables y auth
│   ├── Models/                 # Modelos Eloquent (CreativeRequest, User, Comment...)
│   └── Services/AI/            # Integración del servicio de IA (Gemini)
├── database/
│   ├── migrations/             # Migraciones de tablas y esquemas
│   └── seeders/                # Poblado inicial de datos y catálogos
├── public/
│   ├── tickets/                # Módulo de tickets y soporte operativo
│   └── build/                  # Assets compilados por Vite (CSS/JS)
├── resources/
│   ├── css/                    # Estilos Tailwind CSS v4
│   ├── js/                     # Lógica frontend con Alpine.js y Lucide
│   └── views/                  # Plantillas Blade divididas por módulos
└── routes/
    └── web.php                 # Rutas de la plataforma
```

---

Desarrollé esta solución buscando que el trabajo entre áreas sea más ágil, claro y profesional. Si tienes sugerencias o comentarios, con gusto los leo.
