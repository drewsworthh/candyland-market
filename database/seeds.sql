-- ============================================================
-- Candyland Market — seed data
-- Safe to re-run: truncates all tables first
-- ============================================================
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE order_items;
TRUNCATE TABLE orders;
TRUNCATE TABLE cart_items;
TRUNCATE TABLE carts;
TRUNCATE TABLE inventory;
TRUNCATE TABLE discount_codes;
TRUNCATE TABLE products;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- ── Products ──────────────────────────────────────────────
-- Colours by category:
--   Japanese/Asian  fde8ef / c4275a  (soft pink)
--   Chocolate       efebe9 / 5d3a1a  (warm brown)
--   Gummy / Fruity  fce4ec / c62828  (berry red)
--   Sour            f1f8e9 / 33691e  (lime green)
--   Chewy / Taffy   fff8e1 / bf360c  (amber orange)
--   Hard / Lolly    f3e5f5 / 6a1b9a  (purple)
--   Colourful       e8eaf6 / 283593  (indigo)
--   Novelty         fff9c4 / e65100  (yellow)
-- ─────────────────────────────────────────────────────────

INSERT INTO products (id, name, description, price, image_url) VALUES

-- ── Japanese & Asian candy (1–14) ────────────────────────
( 1, 'Pocky Strawberry',       'Japanese biscuit sticks dipped in creamy strawberry coating.',        3.99, 'https://placehold.co/400x300/fde8ef/c4275a?text=Pocky+Strawberry'),
( 2, 'Pocky Chocolate',        'Classic biscuit sticks fully coated in rich milk chocolate.',          3.99, 'https://placehold.co/400x300/fde8ef/c4275a?text=Pocky+Chocolate'),
( 3, 'Pocky Matcha',           'Delicate green tea flavoured coating on crispy biscuit sticks.',       4.29, 'https://placehold.co/400x300/fde8ef/c4275a?text=Pocky+Matcha'),
( 4, 'Hi-Chew Mango',          'Intensely chewy candy bursting with tropical mango flavour.',          1.99, 'https://placehold.co/400x300/fde8ef/c4275a?text=Hi-Chew+Mango'),
( 5, 'Hi-Chew Strawberry',     'Soft and chewy candy packed with fresh strawberry flavour.',           1.99, 'https://placehold.co/400x300/fde8ef/c4275a?text=Hi-Chew+Strawberry'),
( 6, 'Hi-Chew Green Apple',    'Refreshingly tart green apple in a perfectly chewy candy.',            1.99, 'https://placehold.co/400x300/fde8ef/c4275a?text=Hi-Chew+Apple'),
( 7, 'Meiji Hello Panda Choc', 'Panda-shaped biscuits filled with creamy milk chocolate.',             3.49, 'https://placehold.co/400x300/fde8ef/c4275a?text=Hello+Panda'),
( 8, 'Meiji Hello Panda Straw','Panda-shaped biscuits filled with sweet strawberry cream.',            3.49, 'https://placehold.co/400x300/fde8ef/c4275a?text=Hello+Panda+Straw'),
( 9, 'Chupa Chups Strawberry', 'Iconic round lollipop with sweet, creamy strawberry flavour.',         0.99, 'https://placehold.co/400x300/fde8ef/c4275a?text=Chupa+Chups+Straw'),
(10, 'Chupa Chups Cola',       'Iconic round lollipop with a bold, fizzy cola flavour.',               0.99, 'https://placehold.co/400x300/fde8ef/c4275a?text=Chupa+Chups+Cola'),
(11, 'Kinder Bueno',           'Light crispy wafer filled with hazelnut cream, coated in chocolate.',  2.49, 'https://placehold.co/400x300/fde8ef/c4275a?text=Kinder+Bueno'),
(12, 'Kinder Joy',             'Two delicious halves — creamy filling and a chocolate surprise toy.',   2.99, 'https://placehold.co/400x300/fde8ef/c4275a?text=Kinder+Joy'),
(13, 'Botan Rice Candy',       'Soft, chewy rice paper wrapped lychee-flavoured candy from Japan.',    2.79, 'https://placehold.co/400x300/fde8ef/c4275a?text=Botan+Rice+Candy'),
(14, 'Ramune Original',        'Japanese marble soda candy with a lemon-lime fizz flavour.',           1.99, 'https://placehold.co/400x300/fde8ef/c4275a?text=Ramune+Candy'),

-- ── Chocolate (15–29) ─────────────────────────────────────
(15, 'Twix Caramel',           'Crunchy biscuit layered with caramel, coated in milk chocolate.',      2.79, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Twix+Caramel'),
(16, 'Snickers Original',      'Nougat, caramel and peanuts wrapped in milk chocolate.',               2.49, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Snickers'),
(17, 'Kit Kat Milk Chocolate', 'Four crispy wafer fingers coated in smooth milk chocolate.',           2.29, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Kit+Kat'),
(18, 'Milky Way Original',     'Light fluffy nougat and caramel wrapped in milk chocolate.',           2.49, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Milky+Way'),
(19, '3 Musketeers',           'Whipped chocolate nougat in a smooth chocolate coating.',              2.29, 'https://placehold.co/400x300/efebe9/5d3a1a?text=3+Musketeers'),
(20, 'Butterfinger',           'Crispy peanut butter flavoured candy in a chocolate shell.',           2.49, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Butterfinger'),
(21, 'Baby Ruth',              'Peanuts, caramel and nougat covered in compound chocolate.',           2.49, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Baby+Ruth'),
(22, 'Almond Joy',             'Coconut and whole almonds topped with milk chocolate.',                2.79, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Almond+Joy'),
(23, 'Hershey''s Milk Choc',   'America''s original milk chocolate bar since 1900.',                  2.99, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Hershey%27s'),
(24, 'Reese''s PB Cups',       'Smooth peanut butter wrapped in rich Hershey milk chocolate.',        3.49, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Reese%27s+PB+Cups'),
(25, 'Ferrero Rocher 3-Pack',  'Three premium hazelnut chocolates wrapped in golden foil.',            4.49, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Ferrero+Rocher'),
(26, 'Toblerone Honey Almond', 'Swiss milk chocolate with honey and almond nougat triangles.',         5.49, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Toblerone'),
(27, 'Cadbury Dairy Milk',     'Rich creamy milk chocolate — a British institution.',                  3.49, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Cadbury+Dairy+Milk'),
(28, 'Lindt Milk Chocolate',   'Smooth Swiss milk chocolate crafted with premium Alpine milk.',        4.99, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Lindt+Milk+Choc'),
(29, 'Rolo Caramel Chocolates','Smooth caramel centres enrobed in milk chocolate.',                    2.99, 'https://placehold.co/400x300/efebe9/5d3a1a?text=Rolo'),

-- ── Gummy & Fruity (30–42) ────────────────────────────────
(30, 'Haribo Gold-Bears',      'The original HARIBO gummy bear — a timeless classic since 1922.',     2.79, 'https://placehold.co/400x300/fce4ec/c62828?text=Haribo+Gold-Bears'),
(31, 'Rainbow Gummy Bears',    'Soft gummy bears in six fruit flavours.',                              2.99, 'https://placehold.co/400x300/fce4ec/c62828?text=Rainbow+Gummies'),
(32, 'Swedish Fish',           'Soft, chewy red candy with a uniquely sweet fish shape.',              2.49, 'https://placehold.co/400x300/fce4ec/c62828?text=Swedish+Fish'),
(33, 'Gummy Worms',            'Colourful squiggly worm gummies in dual fruit flavours.',              2.49, 'https://placehold.co/400x300/fce4ec/c62828?text=Gummy+Worms'),
(34, 'Gummy Cola Bottles',     'Fizzy cola-flavoured gummy bottles — retro and irresistible.',         2.49, 'https://placehold.co/400x300/fce4ec/c62828?text=Cola+Bottles'),
(35, 'Peach Rings',            'Soft sugar-coated gummy rings with a sweet peach flavour.',            2.79, 'https://placehold.co/400x300/fce4ec/c62828?text=Peach+Rings'),
(36, 'Watermelon Gummy Slices','Sugar-dusted watermelon slices — sweet with a sour edge.',             2.79, 'https://placehold.co/400x300/fce4ec/c62828?text=Watermelon+Slices'),
(37, 'Apple Rings Gummy',      'Chewy sour apple rings generously coated in sugar.',                   2.49, 'https://placehold.co/400x300/fce4ec/c62828?text=Apple+Rings'),
(38, 'Strawberry Laces',       'Long, chewy strawberry-flavoured candy laces.',                        1.99, 'https://placehold.co/400x300/fce4ec/c62828?text=Strawberry+Laces'),
(39, 'Cherry Sour Gummies',    'Tangy cherry-shaped gummies with a sugar and sour coating.',           2.29, 'https://placehold.co/400x300/fce4ec/c62828?text=Cherry+Sours'),
(40, 'Raisinets Milk Choc',    'Plump raisins enrobed in smooth milk chocolate.',                      2.99, 'https://placehold.co/400x300/fce4ec/c62828?text=Raisinets'),
(41, 'Whoppers Malted Milk',   'Crunchy malted milk balls dipped in rich chocolate.',                  2.49, 'https://placehold.co/400x300/fce4ec/c62828?text=Whoppers'),
(42, 'Milk Duds Theater Box',  'Soft caramel balls coated in chocolate — a movie night classic.',      2.99, 'https://placehold.co/400x300/fce4ec/c62828?text=Milk+Duds'),

-- ── Sour Candy (43–48) ────────────────────────────────────
(43, 'Sour Patch Original',    'Kids gone wild — sour then sweet in grape, lime, lemon, and orange.', 2.29, 'https://placehold.co/400x300/f1f8e9/33691e?text=Sour+Patch+Original'),
(44, 'Sour Patch Watermelon',  'Sweet-then-sour watermelon-shaped candy that really bites back.',      1.99, 'https://placehold.co/400x300/f1f8e9/33691e?text=Sour+Patch+Wmln'),
(45, 'Warheads Extreme Sour',  'Insanely intense sour hard candy — dare you to hold on.',              1.99, 'https://placehold.co/400x300/f1f8e9/33691e?text=Warheads+Sour'),
(46, 'Sour Keys',              'Chewy sugar-coated key-shaped gummy candy with a tangy kick.',         2.29, 'https://placehold.co/400x300/f1f8e9/33691e?text=Sour+Keys'),
(47, 'Sour Rainbow Belts',     'Flat sour candy belts in a rainbow of bold fruity flavours.',          2.49, 'https://placehold.co/400x300/f1f8e9/33691e?text=Sour+Belts'),
(48, 'Toxic Waste Sour Candy', 'Extreme sour candy sold in a novelty toxic waste drum.',               3.99, 'https://placehold.co/400x300/f1f8e9/33691e?text=Toxic+Waste'),

-- ── Chewy & Taffy (49–56) ─────────────────────────────────
(49, 'Starburst Original',     'Chewy fruit squares — strawberry, cherry, lemon, and orange.',        2.29, 'https://placehold.co/400x300/fff8e1/bf360c?text=Starburst+Original'),
(50, 'Starburst Tropical',     'Chewy tropical squares — mango, strawberry banana, kiwi, and more.', 2.29, 'https://placehold.co/400x300/fff8e1/bf360c?text=Starburst+Tropical'),
(51, 'Laffy Taffy Banana',     'Stretchy banana-flavoured taffy with a silly joke on every wrapper.', 1.79, 'https://placehold.co/400x300/fff8e1/bf360c?text=Laffy+Taffy+Banana'),
(52, 'Laffy Taffy Strawberry', 'Sweet stretchy strawberry taffy with that perfect soft chew.',        1.79, 'https://placehold.co/400x300/fff8e1/bf360c?text=Laffy+Taffy+Straw'),
(53, 'Airheads Blue Raspberry','Tart, chewy blue raspberry taffy — an Airheads fan favourite.',       1.99, 'https://placehold.co/400x300/fff8e1/bf360c?text=Airheads+Blue+Razz'),
(54, 'Airheads Cherry',        'Soft, chewy cherry taffy with a bright tangy kick.',                   1.99, 'https://placehold.co/400x300/fff8e1/bf360c?text=Airheads+Cherry'),
(55, 'Tootsie Rolls',          'Classic chocolatey chewy candy — an American icon since 1896.',       1.99, 'https://placehold.co/400x300/fff8e1/bf360c?text=Tootsie+Rolls'),
(56, 'Twizzlers Strawberry',   'Chewy strawberry-flavoured twists — endlessly snackable.',            3.49, 'https://placehold.co/400x300/fff8e1/bf360c?text=Twizzlers'),

-- ── Hard Candy & Lollipops (57–62) ───────────────────────
(57, 'Dum Dums Assorted',      'Mini lollipops in over 16 classic flavours per bag.',                  3.49, 'https://placehold.co/400x300/f3e5f5/6a1b9a?text=Dum+Dums'),
(58, 'Blow Pops Assorted',     'Lollipop with a chewing gum centre — cherry, strawberry, and more.',  2.49, 'https://placehold.co/400x300/f3e5f5/6a1b9a?text=Blow+Pops'),
(59, 'Tootsie Pops Assorted',  'Hard candy pop with a chewy Tootsie Roll centre.',                    2.49, 'https://placehold.co/400x300/f3e5f5/6a1b9a?text=Tootsie+Pops'),
(60, 'Ring Pops Assorted',     'Wearable candy rings in strawberry, cherry, and blue raspberry.',      1.49, 'https://placehold.co/400x300/f3e5f5/6a1b9a?text=Ring+Pops'),
(61, 'Gobstoppers Everlasting','Jawbreakers that change colour and flavour as you slowly enjoy them.', 2.29, 'https://placehold.co/400x300/f3e5f5/6a1b9a?text=Gobstoppers'),
(62, 'York Peppermint Patties','Cool peppermint filling enrobed in dark chocolate.',                   2.99, 'https://placehold.co/400x300/f3e5f5/6a1b9a?text=York+Peppermint'),

-- ── Colourful Candy (63–66) ───────────────────────────────
(63, 'Skittles Original',      'Bite-size fruity candies — taste the rainbow.',                        2.49, 'https://placehold.co/400x300/e8eaf6/283593?text=Skittles+Original'),
(64, 'Skittles Tropical',      'Bite-size candies with bold tropical fruit shells.',                   2.49, 'https://placehold.co/400x300/e8eaf6/283593?text=Skittles+Tropical'),
(65, 'M&Ms Milk Chocolate',    'Colourful candy-coated milk chocolate pieces — the classic.',          3.49, 'https://placehold.co/400x300/e8eaf6/283593?text=M%26Ms+Milk+Choc'),
(66, 'M&Ms Peanut',            'Crunchy peanuts in chocolate wrapped in a colourful candy shell.',     3.49, 'https://placehold.co/400x300/e8eaf6/283593?text=M%26Ms+Peanut'),

-- ── Novelty & Classic (67–71) ─────────────────────────────
(67, 'Pop Rocks Strawberry',   'Popping candy that crackles and fizzes right on your tongue.',         1.99, 'https://placehold.co/400x300/fff9c4/e65100?text=Pop+Rocks'),
(68, 'Fun Dip Assorted',       'Flavoured sugar powder with a dipping candy stick.',                   1.49, 'https://placehold.co/400x300/fff9c4/e65100?text=Fun+Dip'),
(69, 'Nerds Rainbow',          'Tiny, tangy, crunchy candy in a rainbow of fruit flavours.',           1.99, 'https://placehold.co/400x300/fff9c4/e65100?text=Nerds+Rainbow'),
(70, 'Sweet Tarts Original',   'Tangy compressed sugar discs in classic fruit flavours.',              1.79, 'https://placehold.co/400x300/fff9c4/e65100?text=Sweet+Tarts'),
(71, 'Candy Corn Classic',     'Tri-coloured wax candy — the iconic Halloween treat.',                 2.29, 'https://placehold.co/400x300/fff9c4/e65100?text=Candy+Corn');

-- ── Inventory ─────────────────────────────────────────────
INSERT INTO inventory (product_id, quantity) VALUES
( 1, 40), ( 2, 38), ( 3, 25), ( 4, 55), ( 5, 60),
( 6, 70), ( 7, 30), ( 8, 28), ( 9, 90), (10, 85),
(11, 45), (12, 32), (13, 20), (14, 18), (15, 50),
(16, 55), (17, 62), (18, 48), (19, 36), (20, 33),
(21, 29), (22, 35), (23, 44), (24, 38), (25, 20),
(26, 22), (27, 42), (28, 15), (29, 40), (30, 75),
(31, 50), (32, 58), (33, 65), (34, 47), (35, 53),
(36, 39), (37, 44), (38, 72), (39, 56), (40, 30),
(41, 38), (42, 43), (43, 60), (44, 78), (45, 25),
(46, 52), (47, 41), (48, 18), (49, 46), (50, 42),
(51, 68), (52, 62), (53, 74), (54, 70), (55, 45),
(56, 33), (57, 48), (58, 40), (59, 44), (60, 65),
(61, 28), (62, 36), (63, 72), (64, 58), (65, 55),
(66, 52), (67, 88), (68, 76), (69, 64), (70, 80),
(71, 95);

-- ── Users ─────────────────────────────────────────────────
INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES
('Admin', 'User', 'admin@candyland.com', '$2b$12$WVfEbe13uwdtWnUoRqEiG.i/QfClJanRDdZsw4ePmGqos3o9UyiYm', 'admin');

-- ── Discount codes ────────────────────────────────────────
INSERT INTO discount_codes (code, discount_type, discount_value, is_active, expires_at) VALUES
('SWEET10', 'percent', 10.00, 1, DATE_ADD(NOW(), INTERVAL 30 DAY)),
('CANDY5',  'fixed',    5.00, 1, DATE_ADD(NOW(), INTERVAL 30 DAY));
