# FarmaGest — Contrato técnico compartido

Este documento define la base común del proyecto. Cada integrante debe implementar su propio módulo en su rama y presentar pruebas reales.

## Tablas ya definidas en el repositorio

| Módulo | Modelo | Tabla |
| --- | --- | --- |
| Proveedores | Supplier | suppliers |
| Compras | Purchase / PurchaseItem | purchases / purchase_items |
| Ventas | Sale / SaleItem | sales / sale_items |
| Inventario | StockMovement | stock_movements |
| Productos | Product / Category | products / categories |
| Usuarios | User | users |

Las operaciones se crean inicialmente con estado draft. Solo se confirman cuando se guardan todos los detalles y movimientos. Los totales deben calcularse en el servidor, no confiar en cifras recibidas desde el formulario.

## Contrato obligatorio del inventario

Servicio: app/Services/InventoryService.php

Entrada por compra: increase(productId, quantity, 'purchase_item', purchaseItemId)

Salida por venta: decrease(productId, quantity, 'sale_item', saleItemId)

El cuarto parámetro identifica la línea de detalle ya creada, NO la cabecera. Cada producto aparece una sola vez en cada compra o venta. No registrar la misma línea dos veces.

El servicio valida cantidades positivas, impide stock negativo, protege ante reintentos y registra el movimiento. Usa una transacción con bloqueo de la fila del producto.

### Responsabilidad de Compras y Ventas

El controlador de Compras o Ventas debe envolver en una sola transacción de base de datos: crear cabecera, crear detalles, invocar InventoryService por cada detalle y actualizar el estado a confirmed. La transacción interna del servicio no reemplaza la transacción del flujo completo.

No actualizar products.stock directamente desde los controladores. En el formulario de edición de Productos el stock ahora es de solo lectura; la creación inicial sigue permitiendo definir existencias de apertura.

## Trabajo independiente por rama

| Rama | Responsable | Implementar / probar |
| --- | --- | --- |
| feature/proveedores | Viviana | SupplierController, vistas suppliers, validaciones, rutas suppliers.* |
| feature/compras | Viviana | PurchaseController, vistas purchases, confirmación de compra con InventoryService |
| feature/ventas | Brandon | SaleController, vistas sales, confirmación de venta con InventoryService |
| feature/inventario | Daniel | InventoryController, historial, consultas y alertas de stock; pruebas adicionales |
| feature/autenticacion | Jhoselyn | Inicio y cierre de sesión, roles administrador/cajero, middleware y permisos |
| feature/dashboard | Jhoselyn | Métricas reales de ventas y existencias; sin datos inventados |

Los archivos compartidos de modelos y migraciones ya están preparados. No deben recrearlos. Si un cambio resulta necesario, explicarlo en el PR y coordinarlo con Mariano.

## Actualizar ramas antiguas

Las ramas se crearon antes de estas modificaciones. Cuando los PRs compartidos estén fusionados, cada integrante debe ejecutar sin cambios locales pendientes:

1. git fetch origin
2. git switch feature/ventas (cambiar por su rama; si solo existe remoto, git switch --track origin/feature/ventas)
3. git merge origin/main
4. git push origin feature/ventas

Si Git marca conflictos, no forzar; pedir revisión.

## Pruebas sin tocar la base local

El archivo phpunit.xml selecciona SQLite en memoria al ejecutar la suite. Se necesita tener habilitado pdo_sqlite en PHP.

Comando: php artisan test --filter=InventoryServiceTest

Los casos de prueba comprueban entradas y salidas, falta de stock y prevención de duplicados. También se deben verificar manualmente las operaciones completas después de integrar cada módulo.

## Normas

- Laravel 12, Blade, Bootstrap 5, MySQL para uso normal.
- Mensajes al usuario y comentarios técnicos necesarios en español.
- No subir .env, archivos privados, contraseñas ni bases SQL con datos reales.
- PR con descripción, evidencia de pruebas y cambios auténticos del integrante. Un cambio cosmético aislado no sustituye desarrollar y entender una funcionalidad.
- Las rutas del módulo deben añadirse sin borrar las existentes.
