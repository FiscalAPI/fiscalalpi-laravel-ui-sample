<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# 🚀 Sistema de Gestión de Ventas y Facturación

## 📋 **Descripción General**
Sistema completo de gestión de ventas desarrollado en Laravel 10 con integración real a FiscalAPI para la generación de facturas electrónicas. El sistema permite gestionar órdenes, generar facturas, descargar documentos (PDF/XML) y enviar facturas por correo electrónico.

---

## ✨ **Características Principales**

### **🛒 Gestión de Ventas**
- ✅ Sistema completo de órdenes y pedidos
- ✅ Gestión de clientes y productos
- ✅ Cálculo automático de totales y descuentos
- ✅ Estados de orden configurables

### **📄 Facturación Electrónica**
- ✅ Integración real con FiscalAPI
- ✅ Generación automática de facturas CFDI 4.0
- ✅ Descarga de PDFs y XMLs
- ✅ Envío automático por correo electrónico

### **🎨 Interfaz de Usuario**
- ✅ Diseño responsivo con Tailwind CSS
- ✅ Tabla de ventas con filtros avanzados
- ✅ Botones de acción contextuales
- ✅ Notificaciones en tiempo real
- ✅ Modal para envío de correos

---

## 🏗️ **Arquitectura del Sistema**

### **Componentes Principales**
```
app/
├── Http/Controllers/
│   └── SalesController.php              # Controlador principal de ventas
├── Services/
│   └── FiscalApiInvoiceService.php      # Servicio de facturación FiscalAPI
└── Models/
    ├── Order.php                        # Modelo de órdenes
    ├── OrderItem.php                    # Modelo de items de orden
    ├── Person.php                       # Modelo de personas (clientes)
    └── Product.php                      # Modelo de productos

resources/views/
├── components/
│   └── sales.blade.php                  # Componente de tabla de ventas
└── sales/
    └── index.blade.php                  # Vista principal de ventas
```

---

## 🚀 **Instalación y Configuración**

### **1. Requisitos del Sistema**
- PHP 8.1+
- Laravel 10+
- Composer
- Base de datos SQLite/MySQL/PostgreSQL

### **2. Instalación**
```bash
# Clonar el repositorio
git clone <repository-url>
cd example-app

# Instalar dependencias
composer install
npm install

# Configurar variables de entorno
cp .env.example .env
```

### **3. Configuración de FiscalAPI**
```env
# FiscalAPI Configuration
FISCALAPI_URL=https://test.fiscalapi.com
FISCALAPI_KEY=your_api_key_here
FISCALAPI_TENANT=your_tenant_id_here
FISCALAPI_DEBUG=false
FISCALAPI_VERIFY_SSL=false
FISCALAPI_API_VERSION=v4
FISCALAPI_TIMEZONE=America/Mexico_City
```

### **4. Base de Datos**
```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Generar clave de aplicación
php artisan key:generate
```

---

## 📱 **Uso del Sistema**

### **Acceso a Ventas**
```
URL: /sales
Ruta: sales.index
```

### **Funcionalidades Disponibles**

#### **Para Órdenes sin Factura:**
- 🔵 **Facturar**: Generar factura electrónica

#### **Para Órdenes con Factura:**
- 👁️ **Ver PDF**: Visualizar factura en nueva pestaña
- 📥 **Descargar PDF**: Descarga directa del archivo
- 📄 **Descargar XML**: Descarga del archivo XML
- 📧 **Enviar por Correo**: Modal para envío de email

### **Filtros Disponibles**
- **Búsqueda**: Por ID, cliente o RFC
- **Estado**: Completada, Borrador, Pendiente, Cancelada
- **Rango de fechas**: Hoy, Semana, Mes, Trimestre, Año

---

## 🔌 **API Endpoints**

### **Rutas Web**
```php
Route::prefix('sales')->group(function () {
    Route::get('/', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/{order}/generate-invoice', [SalesController::class, 'generateInvoice'])->name('sales.generate-invoice');
    Route::get('/invoice/{invoiceId}/pdf', [SalesController::class, 'getInvoicePdf'])->name('sales.invoice-pdf');
    Route::get('/invoice/{invoiceId}/xml', [SalesController::class, 'getInvoiceXml'])->name('sales.invoice-xml');
    Route::post('/invoice/{invoiceId}/send-email', [SalesController::class, 'sendInvoiceByEmail'])->name('sales.invoice-send-email');
    Route::get('/invoice/{invoiceId}/download-pdf', [SalesController::class, 'downloadInvoicePdf'])->name('sales.invoice-download-pdf');
});
```

### **Rutas API**
```php
Route::prefix('orders')->group(function () {
    Route::post('/{order}/generate-invoice', [SalesController::class, 'generateInvoice']);
});

Route::prefix('invoices')->group(function () {
    Route::get('/{invoiceId}/pdf', [SalesController::class, 'getInvoicePdf']);
    Route::get('/{invoiceId}/xml', [SalesController::class, 'getInvoiceXml']);
    Route::post('/{invoiceId}/send-email', [SalesController::class, 'sendInvoiceByEmail']);
    Route::get('/{invoiceId}/download-pdf', [SalesController::class, 'downloadInvoicePdf']);
});
```

---

## 🧪 **Testing**

### **Ejecutar Tests**
```bash
# Tests unitarios
php artisan test

# Tests con Pest
./vendor/bin/pest

# Tests específicos
php artisan test --filter=SalesController
```

### **Casos de Prueba Cubiertos**
- ✅ Generación de facturas
- ✅ Descarga de documentos
- ✅ Envío por correo
- ✅ Validaciones de datos
- ✅ Manejo de errores

---

## 📊 **Monitoreo y Logs**

### **Logs de Aplicación**
```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Logs específicos de facturación
grep "Invoice" storage/logs/laravel.log
```

### **Métricas a Monitorear**
- Tiempo de generación de facturas
- Tasa de éxito de llamadas a FiscalAPI
- Uso de funcionalidades por usuarios
- Errores y excepciones

---

## 🛡️ **Seguridad**

### **Medidas Implementadas**
- ✅ Validación de entrada en todos los endpoints
- ✅ Sanitización de datos antes de enviar a API
- ✅ Logging seguro sin información sensible
- ✅ Manejo de errores sin exposición de datos internos
- ✅ CSRF protection en formularios

---

## 🔧 **Mantenimiento**

### **Tareas Recomendadas**
- **Diario**: Revisar logs de error
- **Semanal**: Monitorear métricas de API
- **Mensual**: Actualizar dependencias
- **Trimestral**: Revisar configuración de FiscalAPI

### **Backup**
```bash
# Backup de base de datos
php artisan db:backup

# Backup de archivos
php artisan backup:run
```

---

## 📚 **Documentación Adicional**

- [**FISCALAPI_INTEGRATION.md**](FISCALAPI_INTEGRATION.md) - Documentación completa de la integración
- [**FISCALAPI_SETUP.md**](FISCALAPI_SETUP.md) - Guía de configuración
- [**LAYOUT_REFACTORING.md**](LAYOUT_REFACTORING.md) - Refactorización del layout

---

## 🆘 **Soporte**

### **Problemas Comunes**

#### **1. Error de Conexión con FiscalAPI**
- Verificar variables de entorno
- Confirmar conectividad de red
- Revisar logs de aplicación

#### **2. Error al Generar Factura**
- Verificar que la orden tenga todos los datos requeridos
- Confirmar que productos y clientes tengan IDs de FiscalAPI
- Revisar logs para detalles del error

#### **3. Problemas con Descargas**
- Verificar permisos de directorio storage/app/temp
- Confirmar que la factura existe en FiscalAPI
- Revisar logs de descarga

### **Contacto**
Para soporte técnico o preguntas:
1. Revisar documentación en este repositorio
2. Consultar logs de aplicación
3. Verificar configuración de variables de entorno

---

## 📈 **Roadmap**

### **Versión 2.1 (Próxima)**
- [ ] Cache de respuestas para mejorar performance
- [ ] Validación en tiempo real de datos
- [ ] Sistema de reintentos para fallos de API

### **Versión 2.2**
- [ ] Notificaciones push
- [ ] Dashboard de métricas
- [ ] Exportación masiva de facturas

### **Versión 3.0**
- [ ] Integración con sistemas de contabilidad
- [ ] Plantillas personalizables
- [ ] Sistema de aprobaciones

---

## 📄 **Licencia**

Este proyecto está bajo la licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

---

## 🤝 **Contribuciones**

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

---

*Última actualización: Diciembre 2024*
*Versión: 2.0 - Integración Completa con FiscalAPI*
