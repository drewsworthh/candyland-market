<?php

declare(strict_types=1);

class CartController {
    public static function renderCart(): void {
        requireLogin();
        $user = currentUser();
        $cart = CartService::getActiveCart($user['id']);
        $items = CartService::getCartItems($cart['id']);
        $coupon = CartService::getCartCoupon();
        $totals = CartService::calculateCartTotals($items, $coupon);
        renderHeader('Shopping Cart');
        ?>
        <section class="cart-panel">
            <h2>Your Cart</h2>
            <?php if (empty($items)): ?>
                <div class="empty-state">
                    <h3>Your cart is empty.</h3>
                    <p><a href="index.php">Start shopping now.</a></p>
                </div>
            <?php else: ?>
                <form method="post" class="cart-form">
                    <input type="hidden" name="action" value="update_cart">
                    <div class="cart-table">
                        <div class="cart-row cart-header">
                            <div>Item</div>
                            <div>Price</div>
                            <div>Qty</div>
                            <div>Subtotal</div>
                        </div>
                        <?php foreach ($items as $item): ?>
                            <div class="cart-row">
                                <div class="cart-product">
                                    <img src="<?php echo h($item['image_url'] ?: DEFAULT_IMAGE); ?>" alt="<?php echo h($item['name']); ?>">
                                    <div>
                                        <strong><?php echo h($item['name']); ?></strong>
                                        <p><?php echo h($item['description']); ?></p>
                                    </div>
                                </div>
                                <div>$<?php echo fmt($item['price']); ?></div>
                                <div><input type="number" name="quantity[<?php echo (int)$item['id']; ?>]" value="<?php echo (int)$item['quantity']; ?>" min="0" max="<?php echo max(1, (int)$item['stock']); ?>"></div>
                                <div>$<?php echo fmt($item['price'] * $item['quantity']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-actions">
                        <button type="submit">Update Cart</button>
                        <a href="index.php?page=checkout" class="button">Proceed to Checkout</a>
                    </div>
                </form>
                <div class="cart-summary">
                    <div class="summary-card">
                        <h3>Order Summary</h3>
                        <p>Subtotal: $<?php echo fmt($totals['subtotal']); ?></p>
                        <p>Discount: $<?php echo fmt($totals['discount']); ?></p>
                        <p>Tax (8.25%): $<?php echo fmt($totals['tax']); ?></p>
                        <p class="summary-total">Total: $<?php echo fmt($totals['total']); ?></p>
                    </div>
                    <div class="summary-card">
                        <h3>Discount Code</h3>
                        <form method="post" class="coupon-form">
                            <input type="hidden" name="action" value="apply_coupon">
                            <label>Code<input type="text" name="coupon_code" value="<?php echo h($coupon['code'] ?? ''); ?>"></label>
                            <button type="submit">Apply</button>
                        </form>
                        <?php if ($coupon): ?>
                            <p>Current code: <strong><?php echo h(strtoupper($coupon['code'])); ?></strong></p>
                        <?php else: ?>
                            <p>No discount code applied yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>
        <?php
        renderFooter();
    }

    public static function renderCheckout(): void {
        requireLogin();
        $user = currentUser();
        $cart = CartService::getActiveCart($user['id']);
        $items = CartService::getCartItems($cart['id']);
        if (empty($items)) {
            flash('warning', 'Your cart is empty. Add items before checking out.');
            redirect('index.php?page=cart');
        }
        $coupon = CartService::getCartCoupon();
        $totals = CartService::calculateCartTotals($items, $coupon);
        renderHeader('Checkout');
        ?>
        <section class="checkout-panel">
            <div class="checkout-summary">
                <h2>Review Your Order</h2>
                <ul>
                    <?php foreach ($items as $item): ?>
                        <li><?php echo h($item['name']); ?> × <?php echo (int)$item['quantity']; ?> — $<?php echo fmt($item['price'] * $item['quantity']); ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="order-totals">
                    <p>Subtotal: $<?php echo fmt($totals['subtotal']); ?></p>
                    <p>Discount: $<?php echo fmt($totals['discount']); ?></p>
                    <p>Tax (8.25%): $<?php echo fmt($totals['tax']); ?></p>
                    <p class="summary-total">Total: $<?php echo fmt($totals['total']); ?></p>
                </div>
            </div>
            <div class="checkout-form card-form">
                <h2>Place Order</h2>
                <p>Customer: <?php echo h($user['first_name'] . ' ' . $user['last_name']); ?></p>
                <p>Email: <?php echo h($user['email']); ?></p>
                <form method="post">
                    <input type="hidden" name="action" value="place_order">
                    <button type="submit" class="button">Place Order</button>
                </form>
            </div>
        </section>
        <?php
        renderFooter();
    }

    public static function processAddToCart(): void {
        requireLogin();
        $user = currentUser();
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        if ($productId <= 0) {
            flash('error', 'Invalid product selection.');
            redirect('index.php');
        }
        if (CartService::addToCart($user['id'], $productId, $quantity)) {
            flash('success', 'Added product to your cart.');
        } else {
            flash('error', 'Unable to add that item to the cart.');
        }
        redirect('index.php');
    }

    public static function processUpdateCart(): void {
        requireLogin();
        $user = currentUser();
        $cart = CartService::getActiveCart($user['id']);
        foreach ($_POST['quantity'] ?? [] as $itemId => $quantity) {
            CartService::updateCartItem($cart['id'], (int)$itemId, max(0, (int)$quantity));
        }
        flash('success', 'Your cart has been updated.');
        redirect('index.php?page=cart');
    }

    public static function processApplyCoupon(): void {
        requireLogin();
        $code = trim($_POST['coupon_code'] ?? '');
        if ($code === '') {
            flash('error', 'Enter a discount code to apply.');
            redirect('index.php?page=cart');
        }
        $coupon = DiscountService::getDiscountByCode($code);
        if (!$coupon) {
            flash('error', 'That discount code is not valid.');
            redirect('index.php?page=cart');
        }
        CartService::setCartCoupon($coupon);
        flash('success', 'Discount code applied: ' . strtoupper($coupon['code']));
        redirect('index.php?page=cart');
    }

    public static function processPlaceOrder(): void {
        requireLogin();
        $user = currentUser();
        try {
            $orderId = OrderService::placeOrder($user['id']);
            if ($orderId) {
                flash('success', 'Order placed successfully!');
                redirect('index.php?page=order&order_id=' . $orderId);
            }
            flash('error', 'Unable to place order.');
        } catch (Throwable $exception) {
            flash('error', 'Unable to place order: ' . $exception->getMessage());
        }
        redirect('index.php?page=checkout');
    }
}
