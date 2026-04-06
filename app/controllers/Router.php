<?php

declare(strict_types=1);

class Router {
    public static function dispatch(): void {
        $page = $_GET['page'] ?? 'shop';
        $action = $_POST['action'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action !== '') {
            self::handlePost($action);
        }
        switch ($page) {
            case 'login':
                AuthController::renderLogin();
                break;
            case 'register':
                AuthController::renderRegister();
                break;
            case 'logout':
                AuthController::doLogout();
                break;
            case 'profile':
                AuthController::renderProfile();
                break;
            case 'cart':
                CartController::renderCart();
                break;
            case 'checkout':
                CartController::renderCheckout();
                break;
            case 'orders':
                OrderController::renderOrders();
                break;
            case 'order':
                OrderController::renderOrderDetail();
                break;
            case 'admin':
                AdminController::renderAdmin();
                break;
            default:
                ShopController::renderShop();
                break;
        }
    }

    private static function handlePost(string $action): void {
        verifyCsrf();
        switch ($action) {
            case 'login':
                AuthController::processLogin();
                break;
            case 'register':
                AuthController::processRegister();
                break;
            case 'profile_update':
                AuthController::processProfileUpdate();
                break;
            case 'add_to_cart':
                CartController::processAddToCart();
                break;
            case 'update_cart':
                CartController::processUpdateCart();
                break;
            case 'apply_coupon':
                CartController::processApplyCoupon();
                break;
            case 'place_order':
                CartController::processPlaceOrder();
                break;
            case 'admin_save_product':
                self::processAdminSaveProduct();
                break;
            case 'admin_save_user':
                self::processAdminSaveUser();
                break;
            case 'admin_save_discount':
                self::processAdminSaveDiscount();
                break;
        }
    }

    private static function processAdminSaveProduct(): void {
        requireAdmin();
        $productId = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $data = [
            'id' => $productId,
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'image_url' => trim($_POST['image_url'] ?? ''),
            'quantity' => max(0, (int)($_POST['quantity'] ?? 0)),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        if ($data['name'] === '' || $data['price'] <= 0) {
            flash('error', 'Name and price are required.');
            redirect('index.php?page=admin&tab=products');
        }
        if (ProductService::saveProduct($data)) {
            flash('success', 'Product saved successfully.');
        } else {
            flash('error', 'Unable to save the product.');
        }
        redirect('index.php?page=admin&tab=products');
    }

    private static function processAdminSaveUser(): void {
        requireAdmin();
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            flash('error', 'Invalid user.');
            redirect('index.php?page=admin&tab=users');
        }
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'role' => $_POST['role'] === 'admin' ? 'admin' : 'customer',
        ];
        if ($data['first_name'] === '' || $data['last_name'] === '' || $data['email'] === '') {
            flash('error', 'Name and email are required.');
            redirect('index.php?page=admin&tab=users');
        }
        if (trim($_POST['password'] ?? '') !== '') {
            $data['password_hash'] = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        }
        if (UserService::updateUserRecord($userId, $data)) {
            flash('success', 'User updated successfully.');
        } else {
            flash('error', 'Unable to update the user.');
        }
        redirect('index.php?page=admin&tab=users');
    }

    private static function processAdminSaveDiscount(): void {
        requireAdmin();
        $data = [
            'code' => trim($_POST['code'] ?? ''),
            'discount_type' => $_POST['discount_type'] === 'fixed' ? 'fixed' : 'percent',
            'discount_value' => max(0, (float)($_POST['discount_value'] ?? 0)),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'expires_at' => trim($_POST['expires_at'] ?? ''),
        ];
        if ($data['code'] === '' || $data['discount_value'] <= 0) {
            flash('error', 'Discount code and value are required.');
            redirect('index.php?page=admin&tab=discounts');
        }
        if (DiscountService::addDiscountCode($data)) {
            flash('success', 'Discount code created.');
        } else {
            flash('error', 'Unable to create discount code.');
        }
        redirect('index.php?page=admin&tab=discounts');
    }
}
