# FiscalAPI Integration - Sistema de Sincronización de Productos

## Descripción

Este sistema permite mantener sincronizados los productos entre la base de datos local de Laravel y el sistema remoto de FiscalAPI. Cada operación CRUD se ejecuta en ambos sistemas para garantizar consistencia.

## Configuración

### 1. Variables de Entorno

Asegúrate de tener configuradas las siguientes variables en tu archivo `.env`:

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

### 2. Configuración de Laravel

El sistema ya está configurado con:
- `config/fiscalapi.php` - Archivo de configuración
- `app/Providers/FiscalApiServiceProvider.php` - Service Provider
- `app/Services/FiscalApiProductService.php` - Servicio de sincronización

## Funcionalidades

### Operaciones CRUD Sincronizadas

#### Crear Producto
- Se crea primero en FiscalAPI
- Se almacena el ID remoto (`fiscalapiId`)
- Se crea en la base de datos local
- Ambos sistemas quedan sincronizados

#### Actualizar Producto
- Se actualiza primero en FiscalAPI (si existe `fiscalapiId`)
- Se actualiza en la base de datos local
- Se mantiene la consistencia entre ambos sistemas

#### Eliminar Producto
- Se elimina primero de FiscalAPI (si existe `fiscalapiId`)
- Se elimina de la base de datos local
- Se mantiene la consistencia entre ambos sistemas

### Sincronización desde FiscalAPI

#### Sincronizar Producto Específico
```php
// En el controlador
$product = $this->fiscalApiService->syncFromFiscalApi($fiscalApiId);

// O mediante comando Artisan
php artisan fiscalapi:sync-products --id=FISCALAPI_ID
```

#### Sincronizar Todos los Productos
```php
// En el controlador
$syncedProducts = $this->fiscalApiService->syncAllFromFiscalApi();

// O mediante comando Artisan
php artisan fiscalapi:sync-products --all
```

## Estructura de Datos

### Mapeo de Campos

| Campo Local | Campo FiscalAPI | Descripción |
|-------------|-----------------|-------------|
| `description` | `description` | Descripción del producto |
| `unitPrice` | `unitPrice` | Precio unitario |
| `sat_unit_measurement_id` | `satUnitMeasurementId` | Código SAT unidad de medida |
| `sat_tax_object_id` | `satTaxObjectId` | Código SAT objeto de impuesto |
| `sat_product_code_id` | `satProductCodeId` | Código SAT producto |
| `fiscalapiId` | `id` | ID del producto en FiscalAPI |

### Impuestos por Defecto

Si no se especifican impuestos, el sistema aplica automáticamente:
- **IVA**: 16% (Tasa)
- **Tipo**: Traslado
- **Código**: 002

## Uso del Sistema

### 1. Crear Producto
```php
// Los productos se crean automáticamente en ambos sistemas
$product = Product::create([
    'description' => 'Nuevo Producto',
    'unitPrice' => 100.00,
    'sat_unit_measurement_id' => 'H87',
    'sat_tax_object_id' => '02',
    'sat_product_code_id' => '81111602'
]);
```

### 2. Actualizar Producto
```php
// Las actualizaciones se sincronizan automáticamente
$product->update([
    'description' => 'Producto Actualizado',
    'unitPrice' => 150.00
]);
```

### 3. Eliminar Producto
```php
// La eliminación se sincroniza automáticamente
$product->delete();
```

## Manejo de Errores

### Logs
El sistema registra todas las operaciones en los logs de Laravel:
- Operaciones exitosas con información de sincronización
- Errores con detalles para debugging
- Fallos de sincronización con contexto

### Respuestas de Error
- Si falla la operación en FiscalAPI, se revierte la operación local
- Se muestran mensajes de error descriptivos al usuario
- Se mantiene la integridad de los datos

## Comandos Artisan Disponibles

### Sincronizar Producto Específico
```bash
php artisan fiscalapi:sync-products --id=FISCALAPI_ID
```

### Sincronizar Todos los Productos
```bash
php artisan fiscalapi:sync-products --all
```

## Rutas de Sincronización

### Sincronizar Producto Específico
```
GET /products/sync/{fiscalApiId}
```

### Sincronizar Todos los Productos
```
POST /products/sync-all
```

## Consideraciones Importantes

### 1. Orden de Operaciones
- **Crear**: FiscalAPI → Local
- **Actualizar**: FiscalAPI → Local
- **Eliminar**: FiscalAPI → Local

### 2. Manejo de Fallos
- Si falla FiscalAPI, no se modifica la base de datos local
- Se registran todos los errores para auditoría
- Se notifica al usuario sobre el estado de la operación

### 3. Consistencia de Datos
- El campo `fiscalapiId` vincula ambos sistemas
- Las operaciones son atómicas (todo o nada)
- Se mantiene la integridad referencial

## Troubleshooting

### Problemas Comunes

#### 1. Error de Autenticación
- Verificar `FISCALAPI_KEY` y `FISCALAPI_TENANT`
- Confirmar que la API key tenga permisos suficientes

#### 2. Error de Conexión
- Verificar `FISCALAPI_URL`
- Confirmar conectividad de red
- Verificar configuración SSL si es necesario

#### 3. Error de Sincronización
- Revisar logs de Laravel
- Verificar formato de datos enviados
- Confirmar que el producto existe en FiscalAPI

### Logs de Debug
Para activar logs detallados, establecer en `.env`:
```env
FISCALAPI_DEBUG=true
```

## Desarrollo y Mantenimiento

### Agregar Nuevos Campos
1. Actualizar el modelo `Product`
2. Modificar `FiscalApiProductService::prepareFiscalApiData()`
3. Actualizar métodos de sincronización
4. Agregar validaciones en requests

### Extender Funcionalidad
1. Crear nuevos métodos en `FiscalApiProductService`
2. Agregar rutas en `web.php`
3. Implementar en el controlador
4. Agregar comandos Artisan si es necesario

## Soporte

Para problemas o preguntas sobre la integración:
1. Revisar logs de Laravel
2. Verificar configuración de variables de entorno
3. Confirmar conectividad con FiscalAPI
4. Revisar documentación de la API de FiscalAPI
