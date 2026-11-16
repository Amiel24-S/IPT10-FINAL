-- Book Corner database
-- Import this once in phpMyAdmin (Import tab), or run:  mysql -u root < database/schema.sql

DROP DATABASE IF EXISTS book_corner;
CREATE DATABASE book_corner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE book_corner;

-- People who can log in. role is 'customer' or 'admin'.
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,            -- bcrypt hash, never plain text
    role       ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Book genres
CREATE TABLE categories (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Books for sale. Details can be filled in from the Open Library API.
CREATE TABLE books (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NULL,
    isbn        VARCHAR(13) NOT NULL,
    title       VARCHAR(200) NOT NULL,
    author      VARCHAR(200) NOT NULL,
    publisher   VARCHAR(150) NULL,
    year        SMALLINT NULL,
    pages       INT NULL,
    price       DECIMAL(10,2) NOT NULL,
    stock       INT NOT NULL DEFAULT 0,
    cover_url   VARCHAR(255) NULL,
    description TEXT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- One row per checkout
CREATE TABLE orders (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    total      DECIMAL(10,2) NOT NULL,
    status     ENUM('pending','shipped','completed') NOT NULL DEFAULT 'pending',
    address    VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- The books inside each order. price is copied so old orders keep their price.
CREATE TABLE order_items (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id  INT NOT NULL,
    quantity INT NOT NULL,
    price    DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id)  REFERENCES books(id)
) ENGINE=InnoDB;

-- ---------- Sample data ----------

-- Admin login: admin@bookcorner.test / admin123
INSERT INTO users (name, email, password, role) VALUES
('Store Admin', 'admin@bookcorner.test', '$2y$10$/dX2oL9Aayl.bMZZ0rG0vOo/qRGHtUQgKaL0TTVF.H6YpkJ2VVgIi', 'admin');

INSERT INTO categories (name) VALUES
('Classics'), ('Fantasy'), ('Fiction'), ('Dystopian'), ('Self-Discovery');

INSERT INTO books (category_id, isbn, title, author, publisher, year, pages, price, stock, description) VALUES
(2, '9780547928227', 'The Hobbit', 'J.R.R. Tolkien', 'Houghton Mifflin Harcourt', 2012, 300, 545.00, 8,
 'Bilbo Baggins leaves his quiet hobbit-hole for a long journey with a band of dwarves and a wizard.'),
(2, '9780439708180', 'Harry Potter and the Sorcerer''s Stone', 'J.K. Rowling', 'Scholastic', 1998, 309, 495.00, 12,
 'An orphan learns on his eleventh birthday that he is a wizard and starts school at Hogwarts.'),
(1, '9780061120084', 'To Kill a Mockingbird', 'Harper Lee', 'Harper Perennial', 2006, 336, 450.00, 6,
 'A young girl in a small Alabama town watches her father defend a man nobody else will.'),
(4, '9780451524935', '1984', 'George Orwell', 'Signet Classics', 1961, 328, 395.00, 10,
 'Winston Smith works for a government that watches everything and rewrites the past.'),
(1, '9780743273565', 'The Great Gatsby', 'F. Scott Fitzgerald', 'Scribner', 2004, 180, 350.00, 7,
 'A mysterious millionaire throws lavish parties on Long Island, hoping to win back a lost love.'),
(1, '9780141439518', 'Pride and Prejudice', 'Jane Austen', 'Penguin Classics', 2002, 480, 380.00, 9,
 'Elizabeth Bennet and Mr. Darcy misjudge each other badly before they finally understand each other.'),
(3, '9780316769488', 'The Catcher in the Rye', 'J.D. Salinger', 'Little, Brown', 1991, 277, 420.00, 5,
 'Holden Caulfield spends a few restless days wandering New York after leaving his school.'),
(4, '9780385490818', 'The Handmaid''s Tale', 'Margaret Atwood', 'Anchor', 1998, 311, 475.00, 4,
 'In a strict new regime, Offred is allowed only one purpose and remembers the life she lost.'),
(5, '9780062315007', 'The Alchemist', 'Paulo Coelho', 'HarperOne', 2014, 208, 399.00, 15,
 'A shepherd boy follows a recurring dream across the desert in search of treasure.');
