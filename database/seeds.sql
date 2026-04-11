INSERT INTO products (name, description, price, image_url)
VALUES
('Pocky Strawberry', 'Japanese strawberry biscuit sticks', 3.99, 'assets/images/placeholder.jpg'),
('Kinder Bueno', 'Chocolate wafer bar', 2.49, 'assets/images/placeholder.jpg'),
('Hi-Chew Mango', 'Chewy mango candy', 1.99, 'assets/images/placeholder.jpg'),
('Haribo Goldbears',      'The original German gummy bears in five classic fruit flavors.',      2.99, 'https://images.unsplash.com/photo-1582058778036-05b7c6e84965?w=600&q=80'),
('Sour Patch Kids',       'Soft and chewy candies — sour first, then sweet.',                   2.79, 'https://images.unsplash.com/photo-1625183836696-65be4afb7808?w=600&q=80'),
('Toblerone Milk',        'Swiss milk chocolate with honey-almond nougat in an iconic shape.',  4.49, 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=600&q=80'),
('Skittles Original',     'Taste the rainbow — chewy candies in five fruity flavors.',           2.49, 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=600&q=80'),
('Warheads Extreme Sour', 'Intensely sour hard candy — watermelon, apple, black cherry & more.',1.79, 'https://images.unsplash.com/photo-1499195333224-3ce974eecb47?w=600&q=80'),
('Ferrero Rocher 3-pack', 'Premium hazelnut chocolates wrapped in gold foil.',                  3.49, 'https://images.unsplash.com/photo-1548741487-18d363dc4469?w=600&q=80'),
('Nerds Rope',            'Tangy crunchy Nerds clusters on a chewy candy rope.',                 1.99, 'https://images.unsplash.com/photo-1536592524720-65fb27ec4e1e?w=600&q=80'),
('Jolly Rancher Assorted','Hard candy in watermelon, green apple, grape, cherry, and blue raspberry.', 2.29, 'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?w=600&q=80'),
('Albanese Gummy Bears',  'Softer-than-soft 12-flavor gummy bears made in the USA.',            3.29, 'https://images.unsplash.com/photo-1581600140682-d4e68c8cde32?w=600&q=80');
 

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