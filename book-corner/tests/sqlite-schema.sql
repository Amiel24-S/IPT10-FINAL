-- Same tables as database/schema.sql, written for SQLite so tests run in memory.
CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT UNIQUE, password TEXT,
                    role TEXT DEFAULT 'customer', created_at TEXT DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE categories (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT UNIQUE);
CREATE TABLE books (id INTEGER PRIMARY KEY AUTOINCREMENT, category_id INTEGER, isbn TEXT, title TEXT, author TEXT,
                    publisher TEXT, year INTEGER, pages INTEGER, price REAL, stock INTEGER DEFAULT 0,
                    cover_url TEXT, description TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, total REAL, status TEXT DEFAULT 'pending',
                     address TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE order_items (id INTEGER PRIMARY KEY AUTOINCREMENT, order_id INTEGER, book_id INTEGER, quantity INTEGER, price REAL);
