<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/CartService.php';

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $message): void {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function getFlashes(): array {
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function redirect(string $url): void {
    // Block absolute URLs to prevent open redirect attacks
    if (preg_match('#^https?://#i', $url)) {
        $url = 'index.php';
    }
    header('Location: ' . $url);
    exit;
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): void {
    echo '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        die('Request validation failed.');
    }
}

function currentUser(): ?array {
    static $current = null;
    if ($current !== null) {
        return $current;
    }
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $current = UserService::getUserById((int)$_SESSION['user_id']);
    return $current ?: null;
}

function requireLogin(): void {
    if (!currentUser()) {
        flash('warning', 'Please sign in to continue.');
        redirect('index.php?page=login');
    }
}

function requireAdmin(): void {
    $user = currentUser();
    if (!$user || $user['role'] !== 'admin') {
        flash('warning', 'Administrator access required.');
        redirect('index.php');
    }
}

function renderHeader(string $title = 'Candyland Market'): void {
    $user = currentUser();
    $cartCount = $user ? CartService::getCartItemCount($user['id']) : 0;
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="brand">
        <a href="index.php">🍬 Candyland Market</a>
    </div>
    <nav class="site-nav">
        <a href="index.php">Shop</a>
        <?php if ($user): ?>
            <a href="index.php?page=cart">Cart (<?php echo $cartCount; ?>)</a>
            <a href="index.php?page=orders">My Orders</a>
            <a href="index.php?page=profile">Profile</a>
            <?php if ($user['role'] === 'admin'): ?>
                <a href="index.php?page=admin">Admin</a>
            <?php endif; ?>
            <a href="index.php?page=logout">Logout</a>
        <?php else: ?>
            <a href="index.php?page=login">Login</a>
            <a href="index.php?page=register">Register</a>
        <?php endif; ?>
    </nav>
</header>
<main class="page-content">
    <?php foreach (getFlashes() as $flash): ?>
        <div class="alert alert-<?php echo h($flash['type']); ?>"><?php echo h($flash['message']); ?></div>
    <?php endforeach; ?>
    <?php
}

function renderFooter(): void {
    ?>
</main>
<footer class="site-footer">
    <p>Built for Candyland Market with PHP and MySQL.</p>
</footer>
</body>
</html>
    <?php
}

function fmt(float|int|string $value, int $decimals = 2): string {
    return number_format((float)$value, $decimals);
}

function renderProductCard(array $product): void {
    ?>
    <article class="product-card">
        <img src="/<?php echo h($product['image_url'] ?: DEFAULT_IMAGE); ?>" 
     alt="<?php echo h($product['name']); ?>">
        <div class="product-card-body">
            <h3><?php echo h($product['name']); ?></h3>
            <p><?php echo h($product['description']); ?></p>
            <div class="product-meta">
                <span class="price">$<?php echo fmt($product['price']); ?></span>
                <span class="stock"><?php echo (int)$product['quantity']; ?> in stock</span>
            </div>
            <form method="post" class="product-action">
                <input type="hidden" name="action" value="add_to_cart">
                <?php csrfField(); ?>
                <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                <label>
                    Qty
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo max(1, (int)$product['quantity']); ?>">
                </label>
                <button type="submit" <?php echo (int)$product['quantity'] <= 0 ? 'disabled' : ''; ?>>Add to cart</button>
            </form>
        </div>
    </article>
    <?php
}
