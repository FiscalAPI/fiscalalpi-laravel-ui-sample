# Comandos de Sincronización de Personas con FiscalAPI

Este documento describe los comandos Artisan disponibles para sincronizar personas entre Laravel y FiscalAPI.

## 📋 Comandos Disponibles

### 1. `people:sync-fiscalapi` - Sincronización General

Comando principal para sincronización bidireccional de personas.

#### **Uso Básico:**
```bash
# Mostrar ayuda y opciones disponibles
php artisan people:sync-fiscalapi

# Sincronizar una persona específica por ID local
php artisan people:sync-fiscalapi --id=1

# Sincronizar una persona específica por ID de FiscalAPI
php artisan people:sync-fiscalapi --fiscalapi-id=uuid-here

# Sincronizar todas las personas
php artisan people:sync-fiscalapi --all
```

#### **Opciones de Dirección:**
```bash
# Sincronizar solo desde FiscalAPI hacia Laravel
php artisan people:sync-fiscalapi --all --from-fiscalapi

# Sincronizar solo desde Laravel hacia FiscalAPI
php artisan people:sync-fiscalapi --all --to-fiscalapi

# Sincronización bidireccional (por defecto)
php artisan people:sync-fiscalapi --all
```

#### **Opciones de Control:**
```bash
# Forzar sincronización incluso con errores
php artisan people:sync-fiscalapi --all --force

# Combinar opciones
php artisan people:sync-fiscalapi --all --from-fiscalapi --force
```

### 2. `people:sync-all-from-fiscalapi` - Sincronización Masiva

Comando especializado para sincronización masiva desde FiscalAPI hacia Laravel.

#### **Uso Básico:**
```bash
# Sincronizar todas las personas desde FiscalAPI
php artisan people:sync-all-from-fiscalapi

# Con opciones personalizadas
php artisan people:sync-all-from-fiscalapi --page-size=100 --update-existing --force
```

#### **Opciones Disponibles:**
- `--page-size=50`: Número de personas por página (por defecto: 50)
- `--force`: Forzar sincronización incluso si hay errores
- `--update-existing`: Actualizar personas existentes en lugar de saltarlas

## 🔄 Tipos de Sincronización

### **Sincronización Bidireccional (Por Defecto)**
```bash
php artisan people:sync-fiscalapi --all
```
- **Hacia FiscalAPI**: Crea/actualiza personas en FiscalAPI
- **Desde FiscalAPI**: Actualiza datos locales desde FiscalAPI
- **Resultado**: Ambos sistemas quedan completamente sincronizados

### **Sincronización Unidireccional (Hacia FiscalAPI)**
```bash
php artisan people:sync-fiscalapi --all --to-fiscalapi
```
- Solo envía datos de Laravel hacia FiscalAPI
- Útil para: Migración inicial, actualización masiva
- **No modifica** datos locales

### **Sincronización Unidireccional (Desde FiscalAPI)**
```bash
php artisan people:sync-fiscalapi --all --from-fiscalapi
```
- Solo trae datos de FiscalAPI hacia Laravel
- Útil para: Respaldo, restauración de datos
- **No modifica** datos en FiscalAPI

### **Sincronización Masiva (Desde FiscalAPI)**
```bash
php artisan people:sync-all-from-fiscalapi --update-existing
```
- Trae **todas** las personas de FiscalAPI
- Procesa por páginas para eficiencia
- Opción de actualizar existentes o solo crear nuevas

## 📊 Ejemplos de Uso

### **Ejemplo 1: Sincronización Inicial**
```bash
# Primera vez: traer todas las personas de FiscalAPI
php artisan people:sync-all-from-fiscalapi --update-existing

# Luego: sincronización bidireccional regular
php artisan people:sync-fiscalapi --all
```

### **Ejemplo 2: Actualización Masiva**
```bash
# Actualizar todas las personas locales en FiscalAPI
php artisan people:sync-fiscalapi --all --to-fiscalapi

# O sincronización bidireccional completa
php artisan people:sync-fiscalapi --all
```

### **Ejemplo 3: Respaldo desde FiscalAPI**
```bash
# Traer todos los datos actualizados de FiscalAPI
php artisan people:sync-all-from-fiscalapi --update-existing --force
```

### **Ejemplo 4: Sincronización de Persona Específica**
```bash
# Sincronizar persona con ID local 5
php artisan people:sync-fiscalapi --id=5

# Sincronizar persona con ID de FiscalAPI específico
php artisan people:sync-fiscalapi --fiscalapi-id=d7992a07-4161-48ba-b3bf-558790c9bcdb
```

## ⚙️ Configuración y Opciones

### **Tamaño de Página**
```bash
# Procesar 100 personas por página (más rápido, más memoria)
php artisan people:sync-all-from-fiscalapi --page-size=100

# Procesar 25 personas por página (más lento, menos memoria)
php artisan people:sync-all-from-fiscalapi --page-size=25
```

### **Manejo de Errores**
```bash
# Continuar incluso con errores (útil para sincronización masiva)
php artisan people:sync-fiscalapi --all --force

# Detener en el primer error (por defecto)
php artisan people:sync-fiscalapi --all
```

### **Actualización de Existentes**
```bash
# Actualizar personas que ya existen localmente
php artisan people:sync-all-from-fiscalapi --update-existing

# Solo crear nuevas personas (saltar existentes)
php artisan people:sync-all-from-fiscalapi
```

## 📈 Monitoreo y Logs

### **Salida en Consola**
Los comandos muestran:
- Progreso con barras de progreso
- Estadísticas por página
- Resumen final completo
- Errores detallados

### **Logs de Laravel**
Se registran automáticamente:
- Operaciones exitosas
- Errores de sincronización
- Estadísticas de sincronización
- Detalles de fallos

### **Verificar Logs**
```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Buscar logs de sincronización
grep "sync" storage/logs/laravel.log
```

## 🚀 Casos de Uso Comunes

### **1. Configuración Inicial**
```bash
# Paso 1: Traer todas las personas de FiscalAPI
php artisan people:sync-all-from-fiscalapi --update-existing

# Paso 2: Verificar sincronización
php artisan people:sync-fiscalapi --all
```

### **2. Mantenimiento Regular**
```bash
# Sincronización diaria/semanal
php artisan people:sync-fiscalapi --all

# O programar en cron
0 2 * * * cd /path/to/app && php artisan people:sync-fiscalapi --all
```

### **3. Recuperación de Datos**
```bash
# Restaurar desde FiscalAPI después de problema
php artisan people:sync-all-from-fiscalapi --update-existing --force
```

### **4. Migración de Datos**
```bash
# Enviar todas las personas locales a FiscalAPI
php artisan people:sync-fiscalapi --all --to-fiscalapi
```

## 🔧 Troubleshooting

### **Problemas Comunes**

1. **Error de conexión con FiscalAPI**
   ```bash
   # Verificar configuración
   php artisan config:show fiscalapi
   
   # Probar conexión básica
   php artisan tinker
   >>> app(Fiscalapi\Services\FiscalApiClient::class)->getPersonService()->list(1, 1)
   ```

2. **Personas sin fiscalapiId**
   ```bash
   # Crear fiscalapiId para personas existentes
   php artisan people:sync-fiscalapi --all --to-fiscalapi
   ```

3. **Errores de validación**
   ```bash
   # Usar --force para continuar con errores
   php artisan people:sync-fiscalapi --all --force
   ```

4. **Memoria insuficiente**
   ```bash
   # Reducir tamaño de página
   php artisan people:sync-all-from-fiscalapi --page-size=25
   ```

### **Comandos de Diagnóstico**
```bash
# Verificar estado de sincronización
php artisan tinker
>>> App\Models\Person::whereNull('fiscalapiId')->count()

# Ver personas no sincronizadas
>>> App\Models\Person::whereNull('fiscalapiId')->get(['id', 'legalName', 'email'])
```

## 📚 Comandos Relacionados

- **`products:sync-fiscalapi`**: Sincronización de productos
- **`fiscalapi:test`**: Probar conexión con FiscalAPI
- **`config:show fiscalapi`**: Ver configuración de FiscalAPI

## 🔗 Enlaces Útiles

- [FISCALAPI_PERSON_INTEGRATION.md](./FISCALAPI_PERSON_INTEGRATION.md) - Integración general
- [FISCALAPI_SETUP.md](./FISCALAPI_SETUP.md) - Configuración del sistema
- [Documentación oficial de FiscalAPI](https://docs.fiscalapi.com)
