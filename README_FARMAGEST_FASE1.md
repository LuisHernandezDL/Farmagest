# FarmaGest – Fase 1: Productos

Este paquete fue preparado a partir del repositorio inicial `LuisHernandezDL/Farmagest` (Laravel 12). Incluye exclusivamente el primer módulo (categorías + CRUD de productos). No contiene `.env` ni credenciales.

## Cómo incorporarlo a tu proyecto local (Windows 11 / PowerShell)

**Antes de copiar:** dentro de `C:\Proyectos\farmagest`, ejecuta `git status`. Si ya tienes cambios propios en `routes/web.php` o archivos con los mismos nombres, haz copia y combina cambios; **no los sobrescribas**.

1. Desde PowerShell: `cd C:\Proyectos\farmagest`.
2. Crea una rama: `git switch -c feature/productos`.
3. Extrae los archivos de este ZIP en la raíz `C:\Proyectos\farmagest`, conservando las subcarpetas `app`, `database`, `resources`, `routes`. Acepta reemplazar `routes/web.php` solo si sigue teniendo únicamente la ruta `/` a `welcome`.
4. Asegúrate de que XAMPP MySQL está funcionando y que `.env` apunta a `farmagest_db` y al puerto correcto (3307 o 3306).
5. Ejecuta `php artisan config:clear`, `php artisan migrate`, `php artisan db:seed --class=CategorySeeder` y `php artisan route:list --path=products`.
6. Ejecuta `php artisan serve` y abre `http://127.0.0.1:8000/products`.
7. Prueba crear un producto con datos de muestra, editarlo, buscar si se refleja el stock y eliminarlo. Intenta crear dos productos con el mismo código: la aplicación debe bloquear el segundo.
8. Sube tu rama: `git add app database resources routes`, `git commit -m "feat: implementar CRUD de productos y categorias base"`, `git push -u origin feature/productos` y abre un Pull Request a `main`.

**No ejecutes `php artisan migrate:fresh`**: borraría las tablas y datos actuales. Tampoco hagas commits de `.env`.

## Archivos incluidos

- `database/migrations/*create_categories_table.php`, `*create_products_table.php`: tablas.
- `app/Models/Category.php`, `Product.php`: modelos y relación.
- `database/seeders/CategorySeeder.php`: carga categorías iniciales de ejemplo; es repetible.
- `app/Http/Controllers/ProductController.php`: CRUD y validaciones de servidor.
- `resources/views/layouts/app.blade.php`: plantilla Bootstrap.
- `resources/views/products/index.blade.php`: listado, edición, eliminación, stock bajo.
- `resources/views/products/form.blade.php`: formulario de creación y edición.
- `routes/web.php`: sustituye la pantalla inicial por el CRUD.

## Avisos importantes

- Este es un **módulo local de práctica**. Las rutas **todavía no requieren autenticación**, así que **NO publiques esta versión en Internet** hasta integrar login y permisos.
- La edición permite cambiar existencias como valor inicial. Cuando se incorporen compras y ventas, los movimientos deben actualizar el inventario mediante transacciones; dejaremos de permitir ajustes manuales directos por el mismo formulario.
- La eliminación física se usa solo para la demostración inicial. Antes de conectar `sale_items` y `purchase_items`, convertiremos esto en **desactivación** para preservar el historial.
- Categorías iniciales son datos de demostración. Faltará CRUD de categorías según el alcance final.

## Diagnóstico rápido

- `SQLSTATE[HY000] [2002]`: revisar MySQL en XAMPP y `DB_PORT`.
- `Unknown database farmagest_db`: crear DB con ese nombre en phpMyAdmin.
- `Base table or view not found`: ejecutar migraciones y revisar `migrate:status`.
- `Table 'products' already exists`: probablemente hay migración previa duplicada; no volver a copiar ni ejecutar `migrate:fresh` sin respaldo.
- `Route [products.index] not defined`: revisar `routes/web.php` y `php artisan route:list --path=products`.
- `View [products.form] not found`: revisar ruta exacta de `resources/views/products/form.blade.php`.
- `419 Page Expired`: revisar cookies/sesión de Laravel, `APP_KEY` y las directivas `@csrf`.
- `SQLSTATE[23000] duplicate`: el código SKU debe ser único; cambiar código.
- Bootstrap se carga desde CDN: con Internet bloqueado la página funciona pero puede perder estilo.
