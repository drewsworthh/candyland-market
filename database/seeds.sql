INSERT INTO products (name, description, price, image_url)
VALUES
('Pocky Strawberry', 'Japanese strawberry biscuit sticks', 3.99, 'assets/images/pocky.jpg'),
('Kinder Bueno', 'Chocolate wafer bar', 2.49, 'assets/images/bueno.jpg'),
('Hi-Chew Mango', 'Chewy mango candy', 1.99, 'assets/images/hichew.jpg');

INSERT INTO inventory (product_id, quantity)
VALUES
(1, 25),
(2, 40),
(3, 60);

INSERT INTO users (first_name, last_name, email, password_hash, role)
VALUES
('Admin', 'User', 'admin@candyland.com', '$2y$10$abcdefghijklmnopqrstuv', 'admin');