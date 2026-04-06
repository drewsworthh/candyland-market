<?php

// Load .env file into $_ENV if not already set by the environment
$_envFile = __DIR__ . '/../../.env';
if (file_exists($_envFile)) {
    foreach (file($_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_line) {
        if (str_starts_with(trim($_line), '#') || !str_contains($_line, '=')) {
            continue;
        }
        [$_key, $_val] = explode('=', $_line, 2);
        $_key = trim($_key);
        $_val = trim($_val);
        if (!isset($_ENV[$_key])) {
            $_ENV[$_key] = $_val;
            putenv("$_key=$_val");
        }
    }
}
unset($_envFile, $_line, $_key, $_val);

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
define('DEFAULT_IMAGE', 'https://placehold.co/400x300/fde8ef/c4275a?text=No+Image');

if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
