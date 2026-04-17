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

-- Demo customers (password: demo123)
INSERT INTO users (id, first_name, last_name, email, password_hash, role)
VALUES
(2, 'Jamie',  'Chen',    'jamie.chen@example.com',    '$2y$10$woc3K0Qc8ElMEkQMSGIBvOvIhDwR8gdNIjy10SWiPEGHObHGANVc2', 'customer'),
(3, 'Taylor', 'Brooks',  'taylor.brooks@example.com', '$2y$10$O6shNkZlQ7aaySJG7MWbdOcIEZYotPm0HdZT.7IsnqHGx0dDU9NDS', 'customer'),
(4, 'Morgan', 'Davis',   'morgan.davis@example.com',  '$2y$10$KTj6Mn7Uh73NoEWG9bXGXu7xDyni8NYev2Sbh1L.iXNnCvRIw3k5m', 'customer'),
(5, 'Casey',  'Rivera',  'casey.rivera@example.com',  '$2y$10$GP4aSyOSsU.iwyLXHrkDE.yCElVjEO3Qn4PcZiYAt.pAFaYL/aSga', 'customer'),
(6, 'Jordan', 'Kim',     'jordan.kim@example.com',    '$2y$10$NV/I/LGfyX8lO0BZbx0hQOkWQw7sflRFDli9wi1NnN.f1XNm2iqfC', 'customer');

-- Demo orders  (subtotal, tax @ 8.25%, discount, total)
INSERT INTO orders (id, user_id, subtotal, tax, discount, total, status)
VALUES
(1, 2, 12.47, 1.03, 0.00, 13.50, 'fulfilled'),
(2, 3, 11.96, 0.99, 0.00, 12.95, 'paid'),
(3, 4,  6.37, 0.53, 0.00,  6.90, 'pending'),
(4, 5,  9.46, 0.78, 0.00, 10.24, 'cancelled'),
(5, 6,  9.56, 0.79, 0.00, 10.35, 'fulfilled');

-- Order items
INSERT INTO order_items (order_id, product_id, quantity, unit_price)
VALUES
-- Order 1: Jamie — 2x Pocky Strawberry, 1x Toblerone Milk
(1, 1, 2, 3.99),
(1, 6, 1, 4.49),
-- Order 2: Taylor — 1x Ferrero Rocher, 2x Haribo Goldbears, 1x Kinder Bueno
(2, 9, 1, 3.49),
(2, 4, 2, 2.99),
(2, 2, 1, 2.49),
-- Order 3: Morgan — 1x Sour Patch Kids, 2x Warheads Extreme Sour
(3, 5, 1, 2.79),
(3, 8, 2, 1.79),
-- Order 4: Casey — 3x Skittles Original, 1x Nerds Rope
(4, 7, 3, 2.49),
(4, 10, 1, 1.99),
-- Order 5: Jordan — 1x Albanese Gummy Bears, 2x Hi-Chew Mango, 1x Jolly Rancher
(5, 12, 1, 3.29),
(5, 3,  2, 1.99),
(5, 11, 1, 2.29);