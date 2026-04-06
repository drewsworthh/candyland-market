<?php

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../helpers/helpers.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/ProductService.php';
require_once __DIR__ . '/../services/CartService.php';
require_once __DIR__ . '/../services/DiscountService.php';
require_once __DIR__ . '/../services/OrderService.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ShopController.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/Router.php';

define('TAX_RATE', 0.0825);
define('DEFAULT_IMAGE', 'assets/images/placeholder.jpg');

ini_set('display_errors', '1');
error_reporting(E_ALL);
