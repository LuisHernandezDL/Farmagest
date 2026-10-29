# Diagrama entidad-relación de FarmaGest (borrador técnico)

Este diagrama refleja las entidades compartidas previstas para Compras, Ventas e Inventario. Actualizar al integrar Autenticación y cualquier atributo final. La versión entregable debe coincidir con las migraciones aprobadas.

~~~mermaid
erDiagram
    CATEGORIES ||--o{ PRODUCTS : clasifica
    SUPPLIERS ||--o{ PURCHASES : suministra
    PURCHASES ||--o{ PURCHASE_ITEMS : incluye
    PRODUCTS ||--o{ PURCHASE_ITEMS : comprado
    USERS ||--o{ SALES : registra
    SALES ||--o{ SALE_ITEMS : incluye
    PRODUCTS ||--o{ SALE_ITEMS : vendido
    PRODUCTS ||--o{ STOCK_MOVEMENTS : modifica

    CATEGORIES { bigint id PK string name }
    PRODUCTS { bigint id PK bigint category_id FK string code string name decimal purchase_price decimal sale_price integer stock integer min_stock }
    SUPPLIERS { bigint id PK string name string phone string email string address }
    PURCHASES { bigint id PK bigint supplier_id FK string status decimal total datetime confirmed_at }
    PURCHASE_ITEMS { bigint id PK bigint purchase_id FK bigint product_id FK integer quantity decimal unit_price decimal line_total }
    SALES { bigint id PK bigint user_id FK string status decimal total datetime confirmed_at }
    SALE_ITEMS { bigint id PK bigint sale_id FK bigint product_id FK integer quantity decimal unit_price decimal line_total }
    STOCK_MOVEMENTS { bigint id PK bigint product_id FK string direction integer quantity integer balance_after string source_type bigint source_id }
    USERS { bigint id PK string name string email }
~~~

**Nota:** stock_movements.source_type y source_id son referencias lógicas al detalle de compra o venta, no claves foráneas físicas porque pueden apuntar a dos tablas distintas. Las reglas de integridad para estas referencias se cumplen en InventoryService y controladores.
