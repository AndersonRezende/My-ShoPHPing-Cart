CREATE TABLE IF NOT EXISTS cart (
    id INTEGER PRIMARY KEY,
    updated_at TEXT NOT NULL
);

INSERT OR IGNORE INTO cart (id, updated_at)
VALUES (1, datetime('now'));
