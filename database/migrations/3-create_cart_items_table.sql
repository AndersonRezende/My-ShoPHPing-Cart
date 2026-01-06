CREATE TABLE IF NOT EXISTS cart_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cart_id INTEGER NOT NULL,
    product_id TEXT,
    description TEXT,
    quantity INTEGER NOT NULL,
    unit_price INTEGER NOT NULL
);
