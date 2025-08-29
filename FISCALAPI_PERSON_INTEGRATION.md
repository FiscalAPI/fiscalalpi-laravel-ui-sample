# FiscalAPI Integration - Sistema de Sincronización de Personas

## Descripción

Este sistema permite mantener sincronizadas las personas entre la base de datos local de Laravel y el sistema remoto de FiscalAPI. Cada operación CRUD se ejecuta en ambos sistemas para garantizar consistencia.

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
- `app/Http/Controllers/PersonController.php` - Controlador sincronizado

## Funcionalidades

### Operaciones CRUD Sincronizadas

#### Crear Persona
- Se crea primero en FiscalAPI
- Se almacena el ID remoto (`fiscalapiId`)
- Se crea en la base de datos local
- Ambos sistemas quedan sincronizados

#### Actualizar Persona
- Se actualiza primero en FiscalAPI (si existe `fiscalapiId`)
- Se actualiza en la base de datos local
- Se mantiene la consistencia entre ambos sistemas

#### Eliminar Persona
- Se elimina primero de FiscalAPI (si existe `fiscalapiId`)
- Se elimina de la base de datos local
- Se mantiene la consistencia entre ambos sistemas

## Estructura de Datos

### Mapeo de Campos

| Campo Local | Campo FiscalAPI | Descripción |
|-------------|-----------------|-------------|
| `legalName` | `legalName` | Nombre legal de la persona |
| `email` | `email` | Correo electrónico |
| `password` | `password` | Contraseña (sin hashear para la API) |
| `capitalRegime` | `capitalRegime` | Régimen de capital |
| `satTaxRegimeId` | `satTaxRegimeId` | Código SAT régimen fiscal |
| `satCfdiUseId` | `satCfdiUseId` | Código SAT uso de CFDI |
| `tin` | `tin` | RFC/TIN |
| `zipCode` | `zipCode` | Código postal |
| `taxPassword` | `taxPassword` | Contraseña fiscal (sin hashear para la API) |
| `fiscalapiId` | `id` | ID de la persona en FiscalAPI |

### Campos Especiales

- **Contraseñas**: Se envían sin hashear a FiscalAPI, pero se almacenan hasheadas localmente
- **ID de FiscalAPI**: Se extrae de `response.data.id` y se almacena en `fiscalapiId`
- **Relaciones SAT**: Se mantienen las relaciones con los códigos SAT locales

## Uso del Sistema

### 1. Crear Persona
```php
// Las personas se crean automáticamente en ambos sistemas
$person = Person::create([
    'legalName' => 'Nueva Empresa S.A. de C.V.',
    'email' => 'contacto@nuevaempresa.com',
    'password' => 'contraseña123',
    'tin' => 'NEM123456789',
    'zipCode' => '12345',
    'satTaxRegimeId' => '601',
    'satCfdiUseId' => 'G01'
]);
```

### 2. Actualizar Persona
```php
// Las actualizaciones se sincronizan automáticamente
$person->update([
    'legalName' => 'Empresa Actualizada S.A. de C.V.',
    'email' => 'nuevo@empresaactualizada.com'
]);
```

### 3. Eliminar Persona
```php
// La eliminación se sincroniza automáticamente
$person->delete();
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

### Tipos de Errores Manejados

1. **Errores de API**: Fallos en la comunicación con FiscalAPI
2. **Errores de Validación**: Datos inválidos enviados a la API
3. **Errores de Sincronización**: Personas sin `fiscalapiId`
4. **Excepciones del Sistema**: Errores internos de Laravel

## Estructura de Respuestas de FiscalAPI

### Respuesta de Creación Exitosa
```json
{
    "data": {
        "id": "d7992a07-4161-48ba-b3bf-558790c9bcdb",
        "legalName": "NUEVA EMPRESA"
    },
    "succeeded": true,
    "httpStatusCode": 200
}
```

### Respuesta de Actualización Exitosa
```json
{
    "data": {
        "id": "d7992a07-4161-48ba-b3bf-558790c9bcdb",
        "legalName": "EMPRESA ACTUALIZADA"
    },
    "succeeded": true,
    "httpStatusCode": 200
}
```

### Respuesta de Eliminación Exitosa
```json
{
    "data": true,
    "succeeded": true,
    "httpStatusCode": 200
}
```

### Respuesta de Error
```json
{
    "data": null,
    "succeeded": false,
    "message": "Error de validación",
    "details": "El campo legalName es requerido",
    "httpStatusCode": 400
}
```

## Flujo de Sincronización

### Crear Persona
1. Validar datos de entrada
2. Preparar datos para FiscalAPI (sin hashear contraseñas)
3. Llamar a `$this->fiscalApi->getPersonService()->create()`
4. Verificar `response.succeeded`
5. Extraer `response.data.id` como `fiscalapiId`
6. Preparar datos locales (con contraseñas hasheadas y `fiscalapiId`)
7. Crear en base de datos local
8. Retornar éxito

### Actualizar Persona
1. Verificar que existe `fiscalapiId`
2. Validar datos de entrada
3. Preparar datos para FiscalAPI (sin hashear contraseñas)
4. Agregar `id` con el `fiscalapiId`
5. Llamar a `$this->fiscalApi->getPersonService()->update()`
6. Verificar `response.succeeded`
7. Preparar datos locales (con contraseñas hasheadas si se proporcionan)
8. Actualizar en base de datos local
9. Retornar éxito

### Eliminar Persona
1. Verificar que existe `fiscalapiId`
2. Llamar a `$this->fiscalApi->getPersonService()->delete()`
3. Verificar `response.succeeded`
4. Eliminar de base de datos local
5. Retornar éxito

## Validaciones y Seguridad

### Validaciones de Entrada
- `StorePersonRequest` y `UpdatePersonRequest` validan los datos
- Se verifica que los campos requeridos estén presentes
- Se validan formatos de email, RFC, etc.

### Seguridad de Contraseñas
- Las contraseñas se envían sin hashear a FiscalAPI
- Se almacenan hasheadas localmente usando `Hash::make()`
- Se manejan de forma segura en el modelo

### Verificación de Sincronización
- Solo se permiten operaciones en personas con `fiscalapiId`
- Se previenen inconsistencias entre sistemas
- Se valida el estado de sincronización antes de operaciones

## Troubleshooting

### Problemas Comunes

1. **Error "Esta persona no está sincronizada"**
   - La persona no tiene `fiscalapiId`
   - Verificar que se creó correctamente en FiscalAPI
   - Revisar logs para errores de creación

2. **Error de autenticación con FiscalAPI**
   - Verificar `FISCALAPI_KEY` y `FISCALAPI_TENANT`
   - Comprobar que la API key sea válida
   - Verificar la URL de la API

3. **Error de conexión**
   - Verificar `FISCALAPI_URL`
   - Comprobar conectividad de red
   - Verificar `FISCALAPI_VERIFY_SSL`

4. **Personas no sincronizadas**
   - Revisar logs para errores específicos
   - Verificar que el service provider esté registrado
   - Comprobar la configuración de FiscalAPI

### Comandos de Diagnóstico

```bash
# Verificar configuración
php artisan config:show fiscalapi

# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Probar en Tinker
php artisan tinker
>>> app(Fiscalapi\Services\FiscalApiClient::class)->getPersonService()->list(1, 1)
```

## Recursos Adicionales

- [Documentación oficial de FiscalAPI](https://docs.fiscalapi.com)
- [SDK de FiscalAPI para PHP](https://github.com/fiscalapi/fiscalapi-php)
- [Laravel Service Providers](https://laravel.com/docs/providers)
- [FISCALAPI_SETUP.md](./FISCALAPI_SETUP.md) - Configuración general
- [FISCALAPI_INTEGRATION.md](./FISCALAPI_INTEGRATION.md) - Integración de productos

## Notas de Implementación

### Cambios Realizados en PersonController

1. **Inyección de Dependencias**: Se agregó `FiscalApiClient` en el constructor
2. **Método `prepareApiData()`**: Prepara datos para FiscalAPI sin hashear contraseñas
3. **Sincronización en CRUD**: Todas las operaciones se sincronizan con FiscalAPI
4. **Manejo de Errores**: Logging detallado y respuestas informativas
5. **Validación de Sincronización**: Verificación de `fiscalapiId` antes de operaciones

### Patrón de Respuesta

El controlador sigue el patrón estándar de FiscalAPI:
- Usa `$apiResponse->getJson()` para obtener la respuesta
- Verifica `$responseData['succeeded']` para determinar el éxito
- Extrae `$responseData['data']['id']` como `fiscalapiId`
- Maneja errores con logging y respuestas al usuario

### Consistencia de Datos

- Las operaciones locales solo se ejecutan si la API es exitosa
- Se mantiene la integridad entre ambos sistemas
- Se previenen inconsistencias con validaciones apropiadas
