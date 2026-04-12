<?php

declare(strict_types=1);

class ShopController {
    public static function renderShop(): void {
        $query = trim($_GET['q'] ?? '');
        $sort = trim($_GET['sort'] ?? '');
        $products = ProductService::getProducts($query, $sort);
        renderHeader('Candyland Market');
        ?>

        <section class="hero">
            <div>
                <h1>Welcome to Candyland Market</h1>
                <p>Discover sweet candies, shop effortlessly, and place orders with fast checkout.</p>
            </div>
            <?php if (!currentUser()): ?>
            <div>
                <a href="index.php?page=register" class="button">Create an Account</a>
            </div>
            <?php endif; ?>
        </section>

        <section class="filter-bar">
            <form method="get" class="search-form">
                <input type="hidden" name="page" value="shop">
                <input type="text" name="q" placeholder="Search item or description" value="<?php echo h($query); ?>">
                <button type="submit">Search</button>
            </form>
            <form method="get" class="sort-form">
                <input type="hidden" name="page" value="shop">
                <label>Sort by</label>
                <select name="sort" onchange="this.form.submit()">
                    <option value="" <?php echo $sort === '' ? 'selected' : ''; ?>>Newest</option>
                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price low to high</option>
                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price high to low</option>
                    <option value="avail_desc" <?php echo $sort === 'avail_desc' ? 'selected' : ''; ?>>Availability</option>
                </select>
            </form>
        </section>

        <section class="product-grid">
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <h2>No products match your criteria.</h2>
                    <p>Try a new keyword or browse our full selection.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <?php renderProductCard($product); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
        <?php
        renderFooter();
    }
}
