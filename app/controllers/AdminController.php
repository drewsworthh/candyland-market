<?php

declare(strict_types=1);

class AdminController {
    public static function renderAdmin(): void {
        requireAdmin();
        $tab = self::ensureAdminTab($_GET['tab'] ?? 'dashboard');

        // Pre-load edit targets for products and users tabs
        $editProduct = ($tab === 'products' && isset($_GET['edit']))
            ? ProductService::getProductById((int)$_GET['edit'])
            : null;

        $editUser = ($tab === 'users' && isset($_GET['edit']))
            ? UserService::getUserById((int)$_GET['edit'])
            : null;

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
                            <p><?php echo count(ProductService::getProducts('', '', true)); ?></p>
                        </div>
                        <div class="summary-card">
                            <h3>Customers</h3>
                            <p><?php echo count(array_filter(UserService::getUsers(), fn($u) => $u['role'] === 'customer')); ?></p>
                        </div>
                        <div class="summary-card">
                            <h3>Orders</h3>
                            <p><?php echo count(OrderService::getAllOrders()); ?></p>
                        </div>
                        <div class="summary-card">
                            <h3>Revenue</h3>
                            <p>$<?php echo fmt(OrderService::getTotalRevenue()); ?></p>
                        </div>
                    </div>
                    <h3>Recent Orders</h3>
                    <?php $recentOrders = OrderService::getRecentOrders(5); ?>
                    <?php if (empty($recentOrders)): ?>
                        <p style="color:var(--muted);">No orders yet.</p>
                    <?php else: ?>
                        <div class="admin-table">
                            <div class="table-row table-header">
                                <div>ID</div>
                                <div>Customer</div>
                                <div>Date</div>
                                <div>Total</div>
                                <div>Status</div>
                            </div>
                            <?php foreach ($recentOrders as $order): ?>
                                <div class="table-row">
                                    <div>#<?php echo (int)$order['id']; ?></div>
                                    <div><?php echo h($order['first_name'] . ' ' . $order['last_name']); ?></div>
                                    <div><?php echo h(date('Y-m-d', strtotime($order['created_at']))); ?></div>
                                    <div>$<?php echo fmt($order['total']); ?></div>
                                    <div><?php echo h($order['status']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="index.php?page=admin&tab=orders" class="button button-secondary" style="margin-top:0.5rem;">View all orders &rarr;</a>
                    <?php endif; ?>

                <?php elseif ($tab === 'products'): ?>
                    <h2>Manage Products</h2>
                    <div class="admin-panel">
                        <div class="admin-table products-table">
                            <div class="table-row table-header">
                                <div>Name</div>
                                <div>Price</div>
                                <div>Stock</div>
                                <div>Status</div>
                                <div>Actions</div>
                            </div>
                            <?php foreach (ProductService::getProducts('', '', true) as $product): ?>
                                <div class="table-row <?php echo $editProduct && (int)$editProduct['id'] === (int)$product['id'] ? 'row-editing' : ''; ?>">
                                    <div><?php echo h($product['name']); ?></div>
                                    <div>$<?php echo fmt($product['price']); ?></div>
                                    <div><?php echo (int)$product['quantity']; ?></div>
                                    <div>
                                        <span class="status-badge <?php echo $product['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                            <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </div>
                                    <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                                        <a href="index.php?page=admin&tab=products&edit=<?php echo (int)$product['id']; ?>#product-edit-form" class="button button-secondary">Edit</a>
                                        <form method="post" onsubmit="return confirm('Permanently delete \'<?php echo h($product['name']); ?>\'? This cannot be undone.');">
                                            <input type="hidden" name="action" value="admin_delete_product">
                                            <?php csrfField(); ?>
                                            <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                                            <button type="submit" class="button button-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="admin-form card-form" id="product-edit-form">
                            <h3><?php echo $editProduct ? 'Editing: ' . h($editProduct['name']) : 'Add New Product'; ?></h3>
                            <form method="post">
                                <input type="hidden" name="action" value="admin_save_product">
                                <?php csrfField(); ?>
                                <?php if ($editProduct): ?>
                                    <input type="hidden" name="id" value="<?php echo (int)$editProduct['id']; ?>">
                                <?php endif; ?>
                                <label>Name
                                    <input type="text" name="name" value="<?php echo h($editProduct['name'] ?? ''); ?>" required>
                                </label>
                                <label>Description
                                    <textarea name="description" rows="4"><?php echo h($editProduct['description'] ?? ''); ?></textarea>
                                </label>
                                <label>Price
                                    <input type="number" name="price" step="0.01" value="<?php echo h((string)($editProduct['price'] ?? '')); ?>" required>
                                </label>
                                <label>Image URL
                                    <input type="text" name="image_url" value="<?php echo h($editProduct['image_url'] ?? ''); ?>" placeholder="https://...">
                                </label>
                                <label>Quantity
                                    <input type="number" name="quantity" min="0" value="<?php echo (int)($editProduct['quantity'] ?? 0); ?>" required>
                                </label>
                                <label class="checkbox">
                                    <input type="checkbox" name="is_active" <?php echo ($editProduct['is_active'] ?? 1) ? 'checked' : ''; ?>> Active <span style="color:var(--muted);font-size:0.85rem;">(uncheck to make inactive / hide from shop)</span>
                                </label>
                                <button type="submit"><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?></button>
                                <?php if ($editProduct): ?>
                                    <a href="index.php?page=admin&tab=products" class="button button-secondary">Cancel</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>

                <?php elseif ($tab === 'users'): ?>
                    <h2>Manage Users</h2>
                    <?php $userSearch = trim($_GET['q'] ?? ''); ?>
                    <form method="get" class="admin-search-form">
                        <input type="hidden" name="page" value="admin">
                        <input type="hidden" name="tab" value="users">
                        <input type="text" name="q" placeholder="Search by name, email or role…" value="<?php echo h($userSearch); ?>">
                        <button type="submit">Search</button>
                        <?php if ($userSearch !== ''): ?>
                            <a href="index.php?page=admin&tab=users" class="button button-secondary">Clear</a>
                        <?php endif; ?>
                    </form>
                    <div class="admin-panel">
                        <div class="admin-table">
                            <div class="table-row table-header">
                                <div>ID</div>
                                <div>Name</div>
                                <div>Email</div>
                                <div>Role</div>
                                <div>Actions</div>
                            </div>
                            <?php $userList = UserService::getUsers($userSearch); ?>
                            <?php if (empty($userList)): ?>
                                <p style="padding:1rem;color:var(--muted);">No users match "<?php echo h($userSearch); ?>".</p>
                            <?php endif; ?>
                            <?php foreach ($userList as $user): ?>
                                <div class="table-row <?php echo $editUser && (int)$editUser['id'] === (int)$user['id'] ? 'row-editing' : ''; ?>">
                                    <div><?php echo (int)$user['id']; ?></div>
                                    <div><?php echo h($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                    <div><?php echo h($user['email']); ?></div>
                                    <div><?php echo h($user['role']); ?></div>
                                    <div>
                                        <a href="index.php?page=admin&tab=users&edit=<?php echo (int)$user['id']; ?>#user-edit-form" class="button button-secondary">Edit</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="admin-form card-form" id="user-edit-form">
                            <h3><?php echo $editUser ? 'Editing: ' . h($editUser['first_name'] . ' ' . $editUser['last_name']) : 'Edit User'; ?></h3>
                            <?php if (!$editUser): ?>
                                <p style="color:var(--muted);font-size:0.9rem;margin:0;">Click <strong>Edit</strong> on a row above to load a user.</p>
                            <?php else: ?>
                                <form method="post">
                                    <input type="hidden" name="action" value="admin_save_user">
                                    <?php csrfField(); ?>
                                    <input type="hidden" name="user_id" value="<?php echo (int)$editUser['id']; ?>">
                                    <label>First Name
                                        <input type="text" name="first_name" value="<?php echo h($editUser['first_name']); ?>" required>
                                    </label>
                                    <label>Last Name
                                        <input type="text" name="last_name" value="<?php echo h($editUser['last_name']); ?>" required>
                                    </label>
                                    <label>Email
                                        <input type="email" name="email" value="<?php echo h($editUser['email']); ?>" required>
                                    </label>
                                    <label>New Password
                                        <input type="password" name="password" placeholder="Leave blank to keep current">
                                    </label>
                                    <label>Role
                                        <select name="role">
                                            <option value="customer" <?php echo $editUser['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                                            <option value="admin" <?php echo $editUser['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </label>
                                    <button type="submit">Update User</button>
                                    <a href="index.php?page=admin&tab=users" class="button button-secondary">Cancel</a>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($tab === 'orders'): ?>
                    <h2>Manage Orders</h2>
                    <?php $orderSearch = trim($_GET['q'] ?? ''); $orderSort = trim($_GET['sort'] ?? ''); ?>
                    <form method="get" id="order-filter-form" class="admin-actions">
                        <input type="hidden" name="page" value="admin">
                        <input type="hidden" name="tab" value="orders">
                        <input type="text" name="q"
                            placeholder="Search by name, email, order ID or status…"
                            value="<?php echo h($orderSearch); ?>">
                        <label style="display:flex;align-items:center;gap:0.4rem;white-space:nowrap;">Sort by
                            <select name="sort" onchange="document.getElementById('order-filter-form').submit()">
                                <option value=""<?php echo $orderSort === '' ? ' selected' : ''; ?>>Order Date</option>
                                <option value="customer_asc"<?php echo $orderSort === 'customer_asc' ? ' selected' : ''; ?>>Customer A–Z</option>
                                <option value="customer_desc"<?php echo $orderSort === 'customer_desc' ? ' selected' : ''; ?>>Customer Z–A</option>
                                <option value="total_desc"<?php echo $orderSort === 'total_desc' ? ' selected' : ''; ?>>Order Size High→Low</option>
                                <option value="total_asc"<?php echo $orderSort === 'total_asc' ? ' selected' : ''; ?>>Order Size Low→High</option>
                            </select>
                        </label>
                        <?php if ($orderSearch !== ''): ?>
                            <a href="index.php?page=admin&tab=orders&sort=<?php echo h($orderSort); ?>" class="button button-secondary">Clear</a>
                        <?php endif; ?>
                    </form>
                    <div class="admin-table">
                        <div class="table-row table-header">
                            <div>ID</div>
                            <div>Customer</div>
                            <div>Date</div>
                            <div>Total</div>
                            <div>Status</div>
                        </div>
                        <?php $orderList = OrderService::getAllOrders($orderSort, $orderSearch); ?>
                        <?php if (empty($orderList)): ?>
                            <p style="padding:1rem;color:var(--muted);">No orders match "<?php echo h($orderSearch); ?>".</p>
                        <?php endif; ?>
                        <?php foreach ($orderList as $order): ?>
                            <div class="table-row">
                                <div><?php echo (int)$order['id']; ?></div>
                                <div><?php echo h($order['first_name'] . ' ' . $order['last_name']); ?></div>
                                <div><?php echo h(date('Y-m-d', strtotime($order['created_at']))); ?></div>
                                <div>$<?php echo fmt($order['total']); ?></div>
                                <div>
                                    <form method="post" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
                                        <input type="hidden" name="action" value="admin_update_order_status">
                                        <?php csrfField(); ?>
                                        <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">
                                        <select name="status">
                                            <?php foreach (['pending', 'paid', 'fulfilled', 'cancelled'] as $s): ?>
                                                <option value="<?php echo $s; ?>"<?php echo $order['status'] === $s ? ' selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit">Save</button>
                                    </form>
                                </div>
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
                                <div>Actions</div>
                            </div>
                            <?php foreach (DiscountService::getDiscountCodes() as $discount): ?>
                                <div class="table-row">
                                    <div><?php echo h($discount['code']); ?></div>
                                    <div><?php echo h($discount['discount_type']); ?></div>
                                    <div><?php echo h($discount['discount_value']); ?></div>
                                    <div><?php echo h($discount['expires_at'] ?? 'Never'); ?></div>
                                    <div><?php echo $discount['is_active'] ? 'Active' : 'Inactive'; ?></div>
                                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                                        <form method="post">
                                            <input type="hidden" name="action" value="admin_toggle_discount">
                                            <?php csrfField(); ?>
                                            <input type="hidden" name="discount_id" value="<?php echo (int)$discount['id']; ?>">
                                            <button type="submit"><?php echo $discount['is_active'] ? 'Disable' : 'Enable'; ?></button>
                                        </form>
                                        <form method="post" onsubmit="return confirm('Delete discount code <?php echo h($discount['code']); ?>?');">
                                            <input type="hidden" name="action" value="admin_delete_discount">
                                            <?php csrfField(); ?>
                                            <input type="hidden" name="discount_id" value="<?php echo (int)$discount['id']; ?>">
                                            <button type="submit">Delete</button>
                                        </form>
                                    </div>
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
