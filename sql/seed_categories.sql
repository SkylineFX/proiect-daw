-- Seed Data for Categories and Subcategories
-- Run this in PHPMyAdmin

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM subcategories;
ALTER TABLE subcategories AUTO_INCREMENT = 1;

DELETE FROM categories;
ALTER TABLE categories AUTO_INCREMENT = 1;

-- 1. Writing Instruments
INSERT INTO categories (id, name, description) VALUES (1, 'Instrumente de Scris', 'Pixuri, stilouri, creioane, etc.');
INSERT INTO subcategories (category_id, name) VALUES 
(1, 'Pixuri'),
(1, 'Stilouri'),
(1, 'Creioane Mecanice'),
(1, 'Markere si Evidentiatoare');

-- 2. Paper Products
INSERT INTO categories (id, name, description) VALUES (2, 'Hartie si Caiete', 'Caiete studentesti, hartie copiator, agende.');
INSERT INTO subcategories (category_id, name) VALUES 
(2, 'Caiete A4'),
(2, 'Caiete A5'),
(2, 'Hartie Copiator'),
(2, 'Agende si Blocnotes');

-- 3. Office Organizers
INSERT INTO categories (id, name, description) VALUES (3, 'Organizare Birou', 'Dosare, bibliorafturi, suporturi.');
INSERT INTO subcategories (category_id, name) VALUES 
(3, 'Dosare Plastic'),
(3, 'Bibliorafturi'),
(3, 'Suporturi Birou');

-- 4. Art Supplies
INSERT INTO categories (id, name, description) VALUES (4, 'Desen si Arta', 'Produse pentru desen si pictura.');
INSERT INTO subcategories (category_id, name) VALUES 
(4, 'Blocuri de Desen'),
(4, 'Acuarele si Pensule'),
(4, 'Creioane Colorate');

SET FOREIGN_KEY_CHECKS = 1;
