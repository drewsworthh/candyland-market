<?php

declare(strict_types=1);

class AdminController {
    public static function renderAdmin(): void {
        requireAdmin();
        $tab = self::ensureAdminTab($_GET['tab'] ?? 'dashboard');
        renderHeader('Admin Dashboard');
        ?>
        <section class="admin-shell">
            <aside class="admin-nav">
                <h2>Admin</h2>
                <a href="index.php?page=admin&tab=dashboard" class="<?php echo $tab === 'dashboard' ? 'active' : ''; ?>">Overview</a>
                <a href="index.php?page=admin&tab=products" class="<?php echo $tab === 'products' ? 'active' : ''; ?>">Products</a>
                <a href="index.php?page=admin&tab=users" class="<?php echo $tab === 'users' ? 'active' : ''; ?>">Users</a>
                <a href="index.php?page=admin&tab=orders" class="<?php echo $tab === 'orders' ? 'active' : ''; ?>">Orders</a>
                <a href="index.php?page=admin&tab=discounts" class="<?php echo $tab === 'discounts' ? 'active' : ''; ?>">Discount Codes</a>
            </aside>
            <div class="admin-content">
                <?php if ($tab === 'dashboard'): ?>
                    <h2>Admin Overview</h2>
                    <div class="admin-summary">
                        <div class="summary-card">
                            <h3>Products</h3>
                            <p><?php echo count(ProductService::getProducts('', '', true)); ?> total items</p>
                        </div>
                        <div class="summary-card">
                            <h3>Customers</h3>
                            <p><?php echo count(array_filter(UserService::getUsers(), fn($u) => $u['role'] === 'customer')); ?> customers</p>
                        </div>
                        <div class="summary-card">
                            <h3>Orders</h3>
                            <p><?php echo count(OrderService::getAllOrders()); ?> placed orders</p>
                        </div>
                    </div>
                <?php elseif ($tab === 'products'): ?>
                    <h2>Manage Products</h2>
                    <div class="admin-panel">
                        <div class="admin-table">
                            <div class="table-row table-header">
                                <div>Name</div>
                                <div>Price</div>
                                <div>Stock</div>
                                <div>Status</div>
                            </div>
                            <?php foreach (ProductService::getProducts('', '', true) as $product): ?>
                                <div class="table-row">
                                    <div><?php echo h($product['name']); ?></div>
                                    <div>$<?php echo fmt($product['price']); ?></div>
                                    <div><?php echo (int)$product['quantity']; ?></div>
                                    <div><?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="admin-form card-form">
                            <h3>Add / Update Product</h3>
                            <form method="post">
                                <input type="hidden" name="action" value="admin_save_product">
                                <?php csrfField(); ?>
                                <label>Name<input type="text" name="name" required></label>
                                <label>Description<textarea name="description" rows="4"></textarea></label>
                                <label>Price<input type="number" name="price" step="0.01" required></label>
                                <label>Image URL<input type="text" name="image_url" placeholder="assets/images/placeholder.jpg"></label>
                                <label>Quantity<input type="number" name="quantity" min="0" value="0" required></label>
                                <label class="checkbox"><input type="checkbox" name="is_active" checked> Active</label>
                                <button type="submit">Save Product</button>
                            </form>
                        </div>
                    </div>
                <?php elseif ($tab === 'users'): ?>
                    <h2>Manage Users</h2>
                    <div class="admin-panel">
                        <div class="admin-table">
                            <div class="table-row table-header">
                                <div>Name</div>
                                <div>Email</div>
                                <div>Role</div>
                            </div>
                            <?php foreach (UserService::getUsers() as $user): ?>
                                <div class="table-row">
                                    <div><?php echo h($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                    <div><?php echo h($user['email']); ?></div>
                                    <div><?php echo h($user['role']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="admin-form card-form">
                            <h3>Edit User</h3>
                            <form method="post">
                                <input type="hidden" name="action" value="admin_save_user">
                                <?php csrfField(); ?>
                                <label>User ID<input type="number" name="user_id" required></label>
                                <label>First Name<input type="text" name="first_name"></label>
                                <label>Last Name<input type="text" name="last_name"></label>
                                <label>Email<input type="email" name="email"></label>
                                <label>New Password<input type="password" name="password"></label>
                                <label>Role<select name="role">
                                    <option value="customer">Customer</option>
                                    <option value="admin">Admin</option>
                                </select></label>
                                <button type="submit">Save User</button>
                            </form>
                        </div>
                    </div>
                <?php elseif ($tab === 'orders'): ?>
                    <h2>View Orders</h2>
                    <div class="admin-actions">
                        <label>Sort by
                            <select onchange="window.location.href='index.php?page=admin&tab=orders&sort=' + this.value">
                                <option value=""<?php echo empty($_GET['sort']) ? ' selected' : ''; ?>>Order Date</option>
                                <option value="customer_asc"<?php echo ($_GET['sort'] ?? '') === 'customer_asc' ? ' selected' : ''; ?>>Customer A–Z</option>
                                <option value="customer_desc"<?php echo ($_GET['sort'] ?? '') === 'customer_desc' ? ' selected' : ''; ?>>Customer Z–A</option>
                                <option value="total_desc"<?php echo ($_GET['sort'] ?? '') === 'total_desc' ? ' selected' : ''; ?>>Order Size High→Low</option>
                                <option value="total_asc"<?php echo ($_GET['sort'] ?? '') === 'total_asc' ? ' selected' : ''; ?>>Order Size Low→High</option>
                            </select>
                        </label>
                    </div>
                    <div class="admin-table">
                        <div class="table-row table-header">
                            <div>ID</div>
                            <div>Customer</div>
                            <div>Date</div>
                            <div>Total</div>
                            <div>Status</div>
                        </div>
                        <?php foreach (OrderService::getAllOrders(trim($_GET['sort'] ?? '')) as $order): ?>
                            <div class="table-row">
                                <div><?php echo (int)$order['id']; ?></div>
                                <div><?php echo h($order['first_name'] . ' ' . $order['last_name']); ?></div>
                                <div><?php echo h(date('Y-m-d', strtotime($order['created_at']))); ?></div>
                                <div>$<?php echo fmt($order['total']); ?></div>
                                <div><?php echo h($order['status']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <h2>Discount Codes</h2>
                    <div class="admin-panel">
                        <div class="admin-table">
                            <div class="table-row table-header">
                                <div>Code</div>
                                <div>Type</div>
                                <div>Value</div>
                                <div>Expires</div>
                                <div>Status</div>
                            </div>
                            <?php foreach (DiscountService::getDiscountCodes() as $discount): ?>
                                <div class="table-row">
                                    <div><?php echo h($discount['code']); ?></div>
                                    <div><?php echo h($discount['discount_type']); ?></div>
                                    <div><?php echo h($discount['discount_value']); ?></div>
                                    <div><?php echo h($discount['expires_at'] ?? 'Never'); ?></div>
                                    <div><?php echo $discount['is_active'] ? 'Active' : 'Inactive'; ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="admin-form card-form">
                            <h3>Create Discount Code</h3>
                            <form method="post">
                                <input type="hidden" name="action" value="admin_save_discount">
                                <?php csrfField(); ?>
                                <label>Code<input type="text" name="code" required></label>
                                <label>Type<select name="discount_type">
                                    <option value="percent">Percent</option>
                                    <option value="fixed">Fixed</option>
                                </select></label>
                                <label>Value<input type="number" name="discount_value" step="0.01" required></label>
                                <label>Expires At<input type="datetime-local" name="expires_at"></label>
                                <label class="checkbox"><input type="checkbox" name="is_active" checked> Active</label>
                                <button type="submit">Create Code</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
        renderFooter();
    }

    private static function ensureAdminTab(string $tab): string {
        $allowed = ['dashboard', 'products', 'users', 'orders', 'discounts'];
        return in_array($tab, $allowed, true) ? $tab : 'dashboard';
    }
}
