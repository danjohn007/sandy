# Sandy Beauty Nails - Sistema de Citas

Sistema completo de reservas de citas para salón de belleza especializado en cuidado de uñas, desarrollado con PHP puro siguiendo arquitectura MVC.

## 🚀 Características

### Funcionalidades Públicas
- ✅ Formulario de reservación accesible de lunes a sábado (8:00 AM - 7:00 PM)
- ✅ Validación automática de clientes existentes por teléfono
- ✅ Selección de servicios (manicure, pedicure, uñas acrílicas)
- ✅ Selección opcional de manicurista
- ✅ Sistema de horarios disponibles con bloqueo automático
- ✅ Validaciones en frontend (JavaScript) y backend (PHP)
- ✅ Confirmación por pantalla
- 🔄 Integración con Mercado Pago (pendiente)

### Dashboard Administrativo
- ✅ Sistema de login para SuperAdmin y Manicuristas
- ✅ Módulo de reservaciones con filtros avanzados
- ✅ Gestión de clientes con historial
- ✅ Módulo de finanzas (solo SuperAdmin)
- ✅ Reportes y gráficas (solo SuperAdmin)
- ✅ Cambio manual de estado de citas

### Tecnologías
- **Backend:** PHP 8.x (MVC puro, sin framework)
- **Base de Datos:** MySQL 5.7+
- **Frontend:** Bootstrap 5.x + JavaScript Vanilla
- **Pagos:** Mercado Pago (configuración pendiente)

## 📁 Estructura del Proyecto

```
/sandy/
├── /app/
│   ├── /controllers/    # Controladores MVC
│   ├── /models/         # Modelos y consultas DB
│   └── /views/          # Vistas HTML + Bootstrap
├── /config/
│   └── database.php     # Configuración de BD
├── /public/
│   ├── /assets/         # CSS, JS, imágenes
│   ├── .htaccess        # Configuración Apache
│   └── index.php        # Punto de entrada
├── /routes/
│   └── web.php          # Rutas de la aplicación
├── .env                 # Variables de entorno
├── .htaccess            # Redirección a /public
├── database_schema.sql  # Schema de la base de datos
└── README.md           # Este archivo
```

## 🔧 Instalación

### Prerrequisitos
- Apache 2.4+ con mod_rewrite habilitado
- PHP 8.0+ con extensiones: PDO, PDO_MySQL, mbstring
- MySQL 5.7+ o MariaDB 10.2+
- Acceso SSH o panel de control del hosting

### Pasos de Instalación

#### 1. Configurar Base de Datos
```sql
-- Crear la base de datos
CREATE DATABASE fix360_sandy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario y otorgar permisos
CREATE USER 'fix360_sandy'@'localhost' IDENTIFIED BY 'Danjohn07';
GRANT ALL PRIVILEGES ON fix360_sandy.* TO 'fix360_sandy'@'localhost';
FLUSH PRIVILEGES;
```

#### 2. Ejecutar Schema de Base de Datos
```bash
# Importar el schema
mysql -u fix360_sandy -p fix360_sandy < database_schema.sql
```

#### 3. Configurar Variables de Entorno
Editar el archivo `.env` con tus credenciales:

```env
# Configuración de Base de Datos
DB_HOST=localhost
DB_NAME=fix360_sandy
DB_USER=fix360_sandy
DB_PASS=Danjohn07
DB_CHARSET=utf8mb4

# Configuración de Aplicación
APP_URL=https://fix360.app/sandy/
APP_ENV=production
APP_DEBUG=false

# Mercado Pago (obtener en https://www.mercadopago.com/developers)
MP_ACCESS_TOKEN=tu_access_token_aqui
MP_PUBLIC_KEY=tu_public_key_aqui

# Configuración de Email (opcional)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_FROM_NAME=Sandy Beauty Nails
```

#### 4. Configurar Apache Virtual Host

**Opción A: Subdirectorio (Recomendado para hosting compartido)**

Subir archivos al directorio `/public_html/sandy/` y la aplicación estará disponible en `https://tudominio.com/sandy/`

**Opción B: Dominio/Subdomain completo**

```apache
<VirtualHost *:80>
    ServerName sandy.tudominio.com
    DocumentRoot /path/to/sandy/public
    
    <Directory /path/to/sandy/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sandy_error.log
    CustomLog ${APACHE_LOG_DIR}/sandy_access.log combined
</VirtualHost>
```

#### 5. Configurar Permisos
```bash
# Dar permisos de escritura (si es necesario)
chmod 755 /path/to/sandy/
chmod 644 /path/to/sandy/.env
```

### 🔐 Credenciales por Defecto

**SuperAdmin:**
- Usuario: `admin`
- Contraseña: `admin123`

**Manicuristas:**
- Usuario: `sandy` | Contraseña: `admin123`
- Usuario: `maria` | Contraseña: `admin123`
- Usuario: `ana` | Contraseña: `admin123`

> ⚠️ **Importante:** Cambiar estas contraseñas en producción

### 📧 Configuración de Email (Opcional)

Para habilitar notificaciones por email:

1. Configurar SMTP en `.env`
2. Para Gmail, generar una App Password en la configuración de cuenta
3. Actualizar las variables MAIL_* en `.env`

### 💳 Configuración de Mercado Pago

1. Crear cuenta en [Mercado Pago Developers](https://www.mercadopago.com/developers)
2. Obtener credenciales de prueba/producción
3. Actualizar `MP_ACCESS_TOKEN` y `MP_PUBLIC_KEY` en `.env`
4. Implementar webhook para confirmación de pagos (pendiente)

## 🎯 Uso del Sistema

### Para Clientes
1. Visitar `/book` o hacer clic en "Reservar Cita"
2. Ingresar número de teléfono (detecta clientes existentes)
3. Completar datos personales
4. Seleccionar servicio y manicurista (opcional)
5. Elegir fecha y hora disponible
6. Confirmar reserva

### Para Administradores
1. Acceder a `/admin`
2. Iniciar sesión con credenciales
3. **Dashboard:** Ver resumen del día y estadísticas
4. **Citas:** Gestionar reservas y cambiar estados
5. **Clientes:** Ver historial y datos de clientes
6. **Reportes:** (Solo SuperAdmin) Ver estadísticas financieras

## 🔧 Solución de Problemas

### Error 500 - Internal Server Error
- Verificar que mod_rewrite esté habilitado
- Revisar permisos de archivos y directorios
- Comprobar configuración de base de datos en `.env`
- Verificar logs de Apache: `/var/log/apache2/error.log`

### Error de Conexión a Base de Datos
- Verificar credenciales en `.env`
- Comprobar que MySQL esté ejecutándose
- Verificar que el usuario tenga permisos en la base de datos

### Problemas con Rutas
- Verificar que `.htaccess` esté en `/public/`
- Comprobar que AllowOverride esté habilitado en Apache
- Verificar configuración de DocumentRoot

### Problemas de Permisos
```bash
# Corregir permisos básicos
find /path/to/sandy -type f -exec chmod 644 {} \;
find /path/to/sandy -type d -exec chmod 755 {} \;
```

### Variables Indefinidas (Corregido)
**Problema:** Warnings de PHP sobre variables no definidas en dashboard y formularios.

**Solución implementada:**
- **AdminController:** Agregado método `initializeStats()` para validar estadísticas con valores predeterminados seguros
- **Dashboard:** Validaciones `isset()` antes de acceder a índices de arrays
- **BookingController:** Validación de arrays `$services` y `$manicurists` con valores predeterminados vacíos
- **Formulario de reservas:** Validación `is_array()` antes de usar `foreach` en servicios y manicuristas

**Archivos modificados:**
- `app/controllers/AdminController.php` - Método `initializeStats()` agregado
- `app/views/admin/dashboard.php` - Validaciones de acceso a arrays
- `app/controllers/BookingController.php` - Inicialización segura de arrays  
- `app/views/booking/form.php` - Validación de arrays antes de iteración

## 🚀 Características Pendientes

- [ ] Integración completa con Mercado Pago
- [ ] Sistema de notificaciones por email/SMS
- [ ] Recordatorios automáticos de citas
- [ ] Sistema de descuentos y promociones
- [ ] Galería de trabajos realizados
- [ ] Sistema de reseñas y calificaciones
- [ ] Aplicación móvil (PWA)
- [ ] Integración con WhatsApp Business API

## 🔒 Mejoras de Seguridad y Estabilidad

### Validaciones Implementadas (v1.1)
- ✅ **Prevención de variables indefinidas:** Todos los controladores y vistas ahora validan la existencia de variables antes de usarlas
- ✅ **Inicialización segura de arrays:** Los arrays de servicios y manicuristas se inicializan como arrays vacíos si no hay datos
- ✅ **Validación de índices de array:** Las vistas verifican que los índices existan antes de accederlos
- ✅ **Manejo robusto de estadísticas:** El dashboard inicializa estadísticas con valores por defecto (0) si no hay datos
- ✅ **Protección contra foreach en null:** Validación `is_array()` antes de iterar sobre datos
- ✅ **Código defensivo:** Todas las operaciones críticas incluyen validaciones de tipo y existencia

## 🤝 Contribución

Este proyecto fue desarrollado específicamente para Sandy Beauty Nails. Para modificaciones o mejoras:

1. Realizar backup de la base de datos
2. Probar cambios en ambiente de desarrollo
3. Documentar nuevas funcionalidades
4. Actualizar este README si es necesario

## 📞 Soporte

Para soporte técnico o consultas sobre el sistema:
- Email: soporte@fix360.app
- URL: https://fix360.app/sandy/

## 📄 Licencia

Sistema propietario desarrollado para Sandy Beauty Nails.
Todos los derechos reservados © 2024.
