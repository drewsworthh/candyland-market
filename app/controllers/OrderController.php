<?php

declare(strict_types=1);

class OrderController {
    public static function renderOrders(): void {
        requireLogin();
        $user = currentUser();
        $orders = OrderService::getOrdersForUser($user['id']);
        renderHeader('My Orders');
        ?>
        <section class="orders-panel">
            <h2>Order History</h2>
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <h3>No orders yet.</h3>
                    <p>Your completed orders will appear here.</p>
                </div>
            <?php else: ?>
                <div class="order-list">
                    <?php foreach ($orders as $order): ?>
                        <article class="order-card">
                            <div>
                                <strong>Order #<?php echo (int)$order['id']; ?></strong>
                                <p><?php echo h(date('F j, Y', strtotime($order['created_at']))); ?></p>
                            </div>
                            <div>
                                <p>Status: <?php echo h($order['status']); ?></p>
                                <p>Total: $<?php echo fmt($order['total']); ?></p>
                            </div>
                            <div><a href="index.php?page=order&order_id=<?php echo (int)$order['id']; ?>" class="button button-secondary">View Details</a></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php
        renderFooter();
    }

    public static function renderOrderDetail(): void {
        requireLogin();
        $user = currentUser();
        $orderId = (int)($_GET['order_id'] ?? 0);
        if ($orderId <= 0) {
            redirect('index.php?page=orders');
        }
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
        $stmt->execute([$orderId, $user['id']]);
        $order = $stmt->fetch();
        if (!$order) {
            flash('error', 'Order not found.');
            redirect('index.php?page=orders');
        }
        $items = OrderService::getOrderItems($orderId);
        renderHeader('Order Details');
        ?>
        <section class="order-detail">
            <h2>Order #<?php echo (int)$order['id']; ?></h2>
            <p>Date: <?php echo h(date('F j, Y', strtotime($order['created_at']))); ?></p>
            <p>Status: <?php echo h($order['status']); ?></p>
            <div class="order-items">
                <?php foreach ($items as $item): ?>
                    <div class="order-item">
                        <img src="<?php echo h($item['image_url'] ?: DEFAULT_IMAGE); ?>" alt="<?php echo h($item['name']); ?>">
                        <div>
                            <strong><?php echo h($item['name']); ?></strong>
                            <p>Qty: <?php echo (int)$item['quantity']; ?></p>
                            <p>Unit: $<?php echo fmt($item['unit_price']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="order-summary">
                <p>Subtotal: $<?php echo fmt($order['subtotal']); ?></p>
                <p>Discount: $<?php echo fmt($order['discount']); ?></p>
                <p>Tax: $<?php echo fmt($order['tax']); ?></p>
                <p class="summary-total">Total: $<?php echo fmt($order['total']); ?></p>
            </div>
        </section>
        <?php
        renderFooter();
    }
}
