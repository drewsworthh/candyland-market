INSERT INTO products (name, description, price, image_url)
VALUES
('Pocky Strawberry', 'Japanese strawberry biscuit sticks', 3.99, 'assets/images/pocky-strawberry.jpg'),
('Kinder Bueno', 'Chocolate wafer bar', 2.49, 'assets/images/kinder-bueno.jpg'),
('Hi-Chew Mango', 'Chewy mango candy', 1.99, 'assets/images/hichew-mango.jpg'),
('Haribo Goldbears', 'Classic gummy bears', 2.99, 'assets/images/haribo.jpg'),
('Sour Patch Kids', 'Sour then sweet candy', 2.79, 'assets/images/sourpatch.webp'),
('Toblerone Milk', 'Swiss milk chocolate', 4.49, 'assets/images/tolberone.webp'),
('Skittles Original', 'Fruity chewy candy', 2.49, 'assets/images/skittles.jpeg'),
('Warheads Extreme Sour', 'Extreme sour candy', 1.79, 'assets/images/warheads.webp'),
('Ferrero Rocher 3-pack', 'Hazelnut chocolates', 3.49, 'assets/images/ferrero.avif'),
('Nerds Rope', 'Crunchy candy rope', 1.99, 'assets/images/nerdrope.webp'),
('Jolly Rancher Assorted', 'Assorted hard candy', 2.29, 'assets/images/jollyrancher.webp'),
('Albanese Gummy Bears', 'Soft gummy bears', 3.29, 'assets/images/albanese.jpg');

INSERT INTO inventory (product_id, quantity)
VALUES
(1, 25),
(2, 40),
(3, 60),
(4, 80),
(5, 55),
(6, 30),
(7, 70),
(8, 90),
(9, 45),
(10, 65),
(11, 50),
(12, 35);

INSERT INTO users (first_name, last_name, email, password_hash, role)
VALUES
('Admin', 'User', 'admin@candyland.com', '$2y$10$T8ZtPNz/3RKsKX3QQr4WpOnebQ7B3b4zPmfSERXOmXyCt60H4016G', 'admin');

INSERT INTO discount_codes (code, discount_type, discount_value, is_active, expires_at)
VALUES
('SWEET10', 'percent', 10.00, 1, DATE_ADD(NOW(), INTERVAL 30 DAY)),
('CANDY5', 'fixed', 5.00, 1, DATE_ADD(NOW(), INTERVAL 30 DAY));