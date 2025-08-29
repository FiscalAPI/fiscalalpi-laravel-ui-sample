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
# Sistema POS - Laravel

Sistema de Punto de Venta completamente funcional implementado en Laravel con interfaz moderna y responsive.

## 🚀 **Características Implementadas**

### **Sistema de Órdenes**
- ✅ Creación automática de órdenes al abrir POS
- ✅ Gestión de emisor (empresa) y receptor (cliente)
- ✅ Agregar/remover productos con cantidades y descuentos
- ✅ Cálculo automático de totales y subtotales
- ✅ Múltiples métodos de pago (efectivo, tarjetas, cheque, tarjeta de regalo)
- ✅ Finalización y cancelación de ventas

### **Interfaz de Usuario**
- ✅ Panel izquierdo: Búsqueda de productos, selección empresa/cliente, lista de productos
- ✅ Panel derecho: Resumen de venta, métodos de pago, botón de finalizar
- ✅ Diseño responsive y moderno con Tailwind CSS
- ✅ Componentes Blade modulares y reutilizables

### **Base de Datos**
- ✅ Modelos `Order` y `OrderItem` con relaciones
- ✅ Migraciones para tablas `orders` y `order_items`
- ✅ Datos de prueba con 26 personas reales (empresas y clientes)
- ✅ Códigos SAT para productos, impuestos y unidades de medida

### **Funcionalidades Avanzadas**
- ✅ Sincronización automática entre UI y base de datos
- ✅ Limpieza automática de órdenes no finalizadas
- ✅ Manejo de navegación con confirmación de salida
- ✅ Validaciones en frontend y backend
- ✅ Manejo de errores robusto

## 🛠 **Instalación y Configuración**

### **Requisitos**
- PHP 8.1+
- Laravel 11+
- SQLite (configurado por defecto)
- Node.js y NPM

### **Pasos de Instalación**

1. **Clonar el repositorio**
   ```bash
   git clone <repository-url>
   cd example-app
   ```

2. **Instalar dependencias PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias Node.js**
   ```bash
   npm install
   ```

4. **Configurar base de datos**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Compilar assets**
   ```bash
   npm run build
   ```

6. **Iniciar servidor**
   ```bash
   php artisan serve
   ```

## 📱 **Uso del Sistema POS**

### **Acceso**
- Navegar a `/pos` en el navegador
- Se crea automáticamente una nueva orden

### **Flujo de Trabajo**
1. **Seleccionar empresa y cliente** desde los dropdowns
2. **Buscar productos** usando el campo de búsqueda
3. **Agregar productos** a la venta con cantidades y descuentos
4. **Configurar pagos** en el panel derecho
5. **Finalizar venta** cuando el total esté cubierto

### **Funciones Disponibles**
- **Cancelar Venta**: Elimina la orden actual
- **Pausar Venta**: Función en desarrollo
- **Finalizar Venta**: Completa la venta y crea nueva orden

## 🔧 **Comandos Artisan**

### **Limpieza de Órdenes**
```bash
# Limpiar órdenes no finalizadas (con confirmación)
php artisan pos:cleanup-orders

# Limpiar sin confirmación
php artisan pos:cleanup-orders --force
```

## 📊 **Estructura de Base de Datos**

### **Tabla `orders`**
- `id` - Identificador único
- `issuer_id` - ID de la empresa emisora
- `recipient_id` - ID del cliente receptor
- `status` - Estado de la orden (draft, completed, cancelled)
- `subtotal` - Subtotal sin descuentos
- `discounts` - Total de descuentos aplicados
- `total` - Total final de la venta
- `paid` - Monto pagado
- `due` - Monto pendiente

### **Tabla `order_items`**
- `id` - Identificador único
- `order_id` - ID de la orden
- `product_id` - ID del producto
- `quantity` - Cantidad vendida
- `unit_price` - Precio unitario
- `discount_percentage` - Porcentaje de descuento
- `subtotal` - Subtotal del item

## 🔒 **Seguridad y Validaciones**

### **Frontend**
- Validación de campos requeridos
- Confirmación antes de acciones críticas
- Manejo de errores de red

### **Backend**
- Validación de datos en controladores
- Protección CSRF en todas las rutas
- Verificación de existencia de registros

## 🚀 **Próximos Pasos Implementados**

### **1. Sincronización UI-DB ✅**
- Se agregó función `loadOrderItems()` para cargar items desde la base de datos
- Se corrigió la sincronización entre `currentOrderItems` y la respuesta del servidor
- Se actualiza la UI automáticamente al agregar/remover productos

### **2. Botón Finalizar Funcional ✅**
- Se corrigió la validación de `currentOrderItems.length > 0`
- Se agregó mensaje cuando no hay productos en la venta
- Se implementó función `createNewOrder()` para nueva venta

### **3. Manejo de Navegación ✅**
- Se agregaron eventos `beforeunload` y `pagehide`
- Se implementa cancelación automática al navegar sin finalizar
- Se creó middleware `CleanupUnfinishedOrders` para limpieza automática
- Se agregó comando artisan `pos:cleanup-orders`

### **4. Correcciones Adicionales ✅**
- Se corrigieron nombres de campos en modelos (`unit_price`, `discount_percentage`)
- Se agregó método `updateDiscount()` para actualizar descuentos
- Se implementó limpieza automática de órdenes antiguas (>1 hora)
- Se mejoró el manejo de errores y logging

## 📝 **Archivos Principales**

- **Controlador**: `app/Http/Controllers/PosController.php`
- **Modelos**: `app/Models/Order.php`, `app/Models/OrderItem.php`
- **Vistas**: `resources/views/components/pos/index.blade.php`
- **Rutas**: `routes/web.php`
- **Middleware**: `app/Http/Middleware/CleanupUnfinishedOrders.php`
- **Comando**: `app/Console/Commands/CleanupUnfinishedOrders.php`

## 🎯 **Estado Actual**

El sistema POS está **completamente funcional** con todas las características implementadas:

- ✅ **Sincronización completa** entre UI y base de datos
- ✅ **Finalización de ventas** funcionando correctamente
- ✅ **Manejo de navegación** con limpieza automática
- ✅ **Interfaz responsive** y moderna
- ✅ **Validaciones robustas** en frontend y backend
- ✅ **Manejo de errores** implementado
- ✅ **Limpieza automática** de órdenes no finalizadas

## 🚀 **Próximas Mejoras Sugeridas**

1. **Reportes de Ventas**: Dashboard con estadísticas y gráficos
2. **Gestión de Inventario**: Control de stock en tiempo real
3. **Múltiples Sucursales**: Soporte para diferentes ubicaciones
4. **Integración con Impresoras**: Impresión de tickets y facturas
5. **Sincronización Offline**: Funcionamiento sin conexión
6. **Auditoría Completa**: Logs detallados de todas las operaciones

---

**Desarrollado con Laravel 11 y Tailwind CSS**
php artisan people:sync-all-from-fiscalapi --page-size=1 --force
