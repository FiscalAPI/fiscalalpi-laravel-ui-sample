# Configuración de FiscalAPI para Laravel

Este documento describe cómo configurar e integrar FiscalAPI con tu aplicación Laravel.

## 📋 Requisitos Previos

- Laravel 12.x
- PHP 8.2+
- Composer
- Paquete `fiscalapi/fiscalapi` ya instalado

## ⚙️ Configuración

### 1. Variables de Entorno

Crea o actualiza tu archivo `.env` con las siguientes variables:

```env
# FiscalAPI Configuration
FISCALAPI_URL=https://test.fiscalapi.com
FISCALAPI_KEY=tu_api_key_aqui
FISCALAPI_TENANT=tu_tenant_id_aqui
FISCALAPI_DEBUG=false
FISCALAPI_VERIFY_SSL=true
FISCALAPI_API_VERSION=v4
FISCALAPI_TIMEZONE=America/Mexico_City
```

**Nota:** Para producción, cambia `FISCALAPI_URL` a `https://live.fiscalapi.com`

### 2. Archivos de Configuración

Los siguientes archivos ya han sido creados automáticamente:

- `config/fiscalapi.php` - Configuración de la API
- `app/Providers/FiscalApiServiceProvider.php` - Service Provider
- `app/Services/FiscalApiProductService.php` - Servicio de sincronización
- `app/Console/Commands/SyncProductsWithFiscalApi.php` - Comando Artisan

### 3. Service Provider

El `FiscalApiServiceProvider` ya está registrado en `bootstrap/providers.php`.

## 🔄 Uso del Sistema

### Operaciones CRUD Automáticas

El `ProductController` ahora sincroniza automáticamente con FiscalAPI:

- **Crear**: Se crea localmente y en FiscalAPI
- **Actualizar**: Se actualiza localmente y en FiscalAPI
- **Eliminar**: Se elimina de ambos sistemas

### Sincronización Manual

#### Comando Artisan

```bash
# Sincronizar un producto específico
php artisan products:sync-fiscalapi --product-id=1

# Sincronizar todos los productos
php artisan products:sync-fiscalapi --all
```

#### Métodos del Controlador

```php
// Sincronizar manualmente
$controller->syncWithFiscalApi($product);

// Obtener productos de FiscalAPI
$controller->getFiscalApiProducts();

// Obtener producto específico de FiscalAPI
$controller->getFiscalApiProduct($fiscalapiId);
```

## 🗄️ Mapeo de Campos

### Campos Locales → FiscalAPI

| Campo Local | Campo FiscalAPI | Descripción |
|-------------|-----------------|-------------|
| `description` | `description` | Descripción del producto |
| `unitPrice` | `unitPrice` | Precio unitario |
| `sat_unit_measurement_id` | `satUnitMeasurementId` | Código SAT unidad de medida |
| `sat_tax_object_id` | `satTaxObjectId` | Código SAT objeto de impuesto |
| `sat_product_code_id` | `satProductCodeId` | Código SAT producto |
| `fiscalapiId` | `data.id` | ID del producto en FiscalAPI (extraído de `data.id`) |

**Nota:** El campo `fiscalapiId` se extrae de la respuesta `data.id` de FiscalAPI, no directamente del campo `id` de nivel raíz.

### Impuestos por Defecto

Si no se especifican impuestos, se aplica automáticamente:
- **IVA**: 16% (Tasa, Traslado)
- **Tax ID**: 002 (IVA)

## 🚀 Funcionalidades

### 1. Sincronización Automática
- Creación, actualización y eliminación automática
- Manejo de errores con logging
- Respuestas informativas al usuario

### 2. Sincronización Manual
- Comando Artisan para sincronización masiva
- Métodos del controlador para sincronización individual
- Logging detallado de operaciones

### 3. Manejo de Errores
- Logging automático de errores
- Respuestas JSON para APIs
- Mensajes informativos para el usuario

### 4. Validación y Seguridad
- Validación de datos antes de envío
- Manejo seguro de excepciones
- Verificación SSL configurable

## 📝 Logs

El sistema registra automáticamente:

- Operaciones exitosas de sincronización
- Errores de comunicación con FiscalAPI
- Fallos en operaciones CRUD
- Operaciones de sincronización manual

Los logs se encuentran en `storage/logs/laravel.log`.

## 🔧 Troubleshooting

### Problemas Comunes

1. **Error de autenticación**
   - Verifica `FISCALAPI_KEY` y `FISCALAPI_TENANT`
   - Asegúrate de que la API key sea válida

2. **Error de conexión**
   - Verifica `FISCALAPI_URL`
   - Comprueba la conectividad de red
   - Verifica `FISCALAPI_VERIFY_SSL`

3. **Productos no sincronizados**
   - Ejecuta `php artisan products:sync-fiscalapi --all`
   - Revisa los logs para errores específicos

4. **Error "Cannot use object of type Fiscalapi\Http\FiscalApiHttpResponse as array"**
   - Este error ya está resuelto con el método `extractResponseData`
   - El sistema ahora maneja automáticamente tanto respuestas de array como objetos
   - Si persiste, ejecuta `php artisan fiscalapi:test` para diagnosticar

### Manejo de Respuestas

El sistema ahora maneja correctamente la estructura de respuesta estándar de FiscalAPI:

**Estructura de Respuesta:**
```json
{
    "data": { ... },
    "succeeded": true,
    "message": "",
    "details": "",
    "httpStatusCode": 200
}
```

**Campos Clave:**
- `data`: Contiene los datos del producto (ID, descripción, precio, etc.)
- `succeeded`: Indica si la operación fue exitosa
- `message`: Mensaje de la operación
- `details`: Detalles adicionales
- `httpStatusCode`: Código HTTP de la respuesta

**Manejo Automático:**
- Verifica `succeeded` para determinar el éxito de la operación
- Extrae el ID del producto de `data.id` para sincronización
- Maneja tanto respuestas de array como objetos de respuesta HTTP
- Fallback robusto si la estructura cambia

### Comandos de Diagnóstico

```bash
# Probar conexión básica
php artisan fiscalapi:test

# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Verificar configuración
php artisan config:show fiscalapi
```

### Verificación de Configuración

```bash
# Verificar configuración
php artisan config:show fiscalapi

# Probar conexión básica
php artisan fiscalapi:test

# Probar sincronización de productos
php artisan products:sync-fiscalapi --all

# Probar en Tinker
php artisan tinker
>>> app(App\Services\FiscalApiProductService::class)->getProductsFromFiscalApi()
```

## 📚 Recursos Adicionales

- [Documentación oficial de FiscalAPI](https://docs.fiscalapi.com)
- [SDK de FiscalAPI para PHP](https://github.com/fiscalapi/fiscalapi-php)
- [Laravel Service Providers](https://laravel.com/docs/providers)

## 📋 Ejemplos de Respuestas de FiscalAPI

### Respuesta de Creación Exitosa
```json
{
    "data": {
        "description": "Producto de prueba",
        "unitPrice": 100.00,
        "id": "b2e8dba2-5986-426b-8c1b-6657c0cecbf7"
    },
    "succeeded": true,
    "message": "",
    "details": "",
    "httpStatusCode": 200
}
```

### Respuesta de Lista de Productos
```json
{
    "data": {
        "items": [
            {
                "description": "Producto 1",
                "unitPrice": 150.75,
                "id": "2c6aafcf-8cd2-4fb1-94a8-687adc671380"
            }
        ],
        "pageNumber": 1,
        "totalPages": 2,
        "totalCount": 4
    },
    "succeeded": true,
    "message": "",
    "httpStatusCode": 200
}
```

### Respuesta de Error
```json
{
    "data": null,
    "succeeded": false,
    "message": "Error de validación",
    "details": "El campo description es requerido",
    "httpStatusCode": 400
}
```

## 🤝 Soporte

Para problemas específicos de la integración:
1. Revisa los logs de Laravel
2. Verifica la configuración de variables de entorno
3. Consulta la documentación oficial de FiscalAPI
4. Revisa el estado de la API de FiscalAPI
