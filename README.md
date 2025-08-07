# Sandy Beauty Nails - Sistema de Gestión de Citas

Este es un sistema completo de gestión de citas para salón de belleza especializado en cuidado de uñas desarrollado en PHP puro (sin framework) que sigue una arquitectura MVC organizada y accesible.

## ✅ Características Implementadas

### 🔐 Sistema de Autenticación
- Login seguro con hash de contraseñas
- Gestión de sesiones segura
- Protección contra ataques CSRF
- Logout automático por inactividad

### 👥 Gestión de Usuarios (2 Roles)
- **SuperAdmin**: Control total del sistema, reportes financieros
- **Manicurista**: Gestión de sus propias citas y clientes

### 📊 Dashboards Diferenciados
- **SuperAdmin Dashboard**: Métricas globales, gestión completa de citas, reportes financieros
- **Manicurista Dashboard**: Citas asignadas, gestión de horarios personales

### 📝 Sistema de Reservaciones
- Formulario público de reservación accesible
- Validación automática de clientes existentes por teléfono
- Selección de servicios (manicure, pedicure, uñas acrílicas)
- Selección opcional de manicurista preferida
- Sistema de horarios disponibles con bloqueo automático
- Confirmación inmediata de reservas

### 📅 Gestión de Citas
- Estados configurables (Pendiente, Confirmada, Completada, Cancelada, No se presentó)
- Filtros avanzados por fecha, estado, manicurista
- Historial completo de citas por cliente
- Cambio manual de estados

### 👤 Gestión de Clientes
- Registro automático en primera reserva
- Historial completo de servicios
- Información de contacto actualizable
- Estadísticas por cliente

### 💰 Módulo de Finanzas (Solo SuperAdmin)
- Reportes de ingresos diarios, semanales, mensuales
- Gráficas de rendimiento por servicio
- Estadísticas de manicuristas
- Métricas de ocupación

### 🛡️ Seguridad Implementada
- Validación y sanitización de datos
- Hash seguro de contraseñas (password_hash)
- Validación de permisos por roles
- Logging de actividades del sistema
- Headers de seguridad configurados

## 🗂️ Estructura del Proyecto

```
sandy/
├── config/
│   ├── app.php               # Configuración de aplicación
│   └── database.php          # Configuración de base de datos
├── controllers/
│   ├── BaseController.php    # Controlador base
│   ├── AdminController.php   # Lógica de autenticación admin
│   ├── AppointmentController.php # Lógica de citas
│   ├── BookingController.php # Lógica de reservaciones públicas
│   ├── ClientController.php  # Lógica de clientes
│   └── ReportController.php  # Lógica de reportes
├── models/
│   ├── BaseModel.php        # Modelo base
│   ├── Appointment.php      # Modelo de citas
│   ├── Client.php           # Modelo de clientes
│   ├── Service.php          # Modelo de servicios
│   └── Manicurist.php       # Modelo de manicuristas
├── views/
│   ├── dashboards/          # Vistas de dashboards por rol
│   │   ├── admin.php        # Dashboard de SuperAdmin
│   │   └── manicurist.php   # Dashboard de Manicurista
│   ├── admin/               # Vistas administrativas
│   ├── booking/             # Vistas de reservación pública
│   └── layout/              # Plantillas base
├── public/
│   ├── index.php            # Punto de entrada principal
│   ├── login.php            # Formulario de login
│   ├── register.php         # Página de registro (no activa)
│   └── assets/              # Recursos estáticos (CSS, JS, imágenes)
├── includes/
│   ├── auth.php             # Sistema de autenticación
│   └── functions.php        # Funciones auxiliares
├── routes/
│   └── web.php              # Definición de rutas
├── database.sql             # Schema de base de datos
├── .htaccess               # Redirección a /public
└── README.md               # Este archivo
```

## 🚀 Instalación y Configuración

### 1. Requisitos del Sistema
- PHP 8.0 o superior
- MySQL 5.7 o 8.0
- Servidor web (Apache/Nginx)
- Extensiones PHP: PDO, PDO_MySQL, mbstring

### 2. Configuración de Base de Datos
1. Crear base de datos MySQL
2. Ejecutar el script `database.sql`
3. Configurar credenciales en `config/database.php`

### 3. Configuración del Servidor
- Configurar DocumentRoot hacia la carpeta `public/`
- Habilitar mod_rewrite si usa Apache
- Configurar permisos de escritura en `logs/` si es necesario

### 4. Usuarios por Defecto
- **SuperAdmin**: `admin` / `admin123`
- **Manicuristas**: 
  - `sandy` / `admin123`
  - `maria` / `admin123`
  - `ana` / `admin123`

> ⚠️ **Importante**: Cambiar estas contraseñas en producción

## 🎯 Funcionalidades por Rol

### SuperAdmin
- ✅ Vista global de métricas del sistema
- ✅ Gestión completa de todas las citas
- ✅ Gestión de todos los clientes
- ✅ Acceso a reportes financieros
- ✅ Gráficas de rendimiento
- ✅ Configuración del sistema

### Manicurista
- ✅ Gestión de sus propias citas
- ✅ Vista de agenda personal
- ✅ Actualización de estados de cita
- ✅ Información de clientes asignados

## 🔧 Tecnologías Utilizadas

- **Backend**: PHP 8.x (puro, sin framework)
- **Base de Datos**: MySQL con claves foráneas y relaciones optimizadas
- **Frontend**: HTML5, CSS3, JavaScript Vanilla
- **UI Framework**: Bootstrap 5
- **Iconos**: Bootstrap Icons
- **Seguridad**: password_hash(), sanitización de entrada, headers de seguridad

## 📈 Características del Sistema

### Sistema de Reservaciones Públicas
- Formulario accesible en horario de negocio (Lunes-Sábado 8:00-19:00)
- Detección automática de clientes existentes
- Validación de disponibilidad en tiempo real
- Confirmación inmediata

### Gestión de Horarios
- Configuración flexible de horarios de negocio
- Bloqueo automático de slots ocupados
- Duración personalizable por tipo de servicio
- Validación de solapamientos

### Sistema de Reportes
- Métricas de ingresos por período
- Estadísticas de ocupación
- Rendimiento por manicurista
- Servicios más populares

## 🛠️ Próximas Mejoras

- [ ] Integración completa con Mercado Pago
- [ ] Sistema de notificaciones por email/SMS
- [ ] Recordatorios automáticos de citas
- [ ] Sistema de descuentos y promociones
- [ ] Galería de trabajos realizados
- [ ] Sistema de reseñas y calificaciones
- [ ] Aplicación móvil (PWA)
- [ ] Integración con WhatsApp Business API
- [ ] Calendario visual interactivo
- [ ] Gestión de inventario de productos

## 🔐 Notas de Seguridad

El sistema implementa múltiples capas de seguridad:
- Autenticación robusta con sesiones seguras
- Validación exhaustiva de entrada de datos
- Protección contra CSRF, XSS y SQL Injection
- Permisos granulares por rol
- Logging completo de actividades
- Headers de seguridad HTTP

## 📞 Soporte

Sistema desarrollado como solución completa para Sandy Beauty Nails. Incluye todas las funcionalidades necesarias para la gestión diaria de un salón de belleza especializado en cuidado de uñas.

Para soporte técnico o consultas sobre el sistema:
- Sistema listo para producción con las configuraciones adecuadas
- Documentación completa incluida
- Estructura escalable y mantenible
