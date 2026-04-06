INSERT INTO products (name, description, price, image_url)
VALUES
('Pocky Strawberry', 'Japanese strawberry biscuit sticks', 3.99, 'assets/images/placeholder.jpg'),
('Kinder Bueno', 'Chocolate wafer bar', 2.49, 'assets/images/placeholder.jpg'),
('Hi-Chew Mango', 'Chewy mango candy', 1.99, 'assets/images/placeholder.jpg');

INSERT INTO inventory (product_id, quantity)
VALUES
(1, 25),
(2, 40),
(3, 60);

INSERT INTO users (first_name, last_name, email, password_hash, role)
VALUES
('Admin', 'User', 'admin@candyland.com', '$2b$12$WVfEbe13uwdtWnUoRqEiG.i/QfClJanRDdZsw4ePmGqos3o9UyiYm', 'admin');

INSERT INTO discount_codes (code, discount_type, discount_value, is_active, expires_at)
VALUES
('SWEET10', 'percent', 10.00, 1, DATE_ADD(NOW(), INTERVAL 30 DAY)),
('CANDY5', 'fixed', 5.00, 1, DATE_ADD(NOW(), INTERVAL 30 DAY));