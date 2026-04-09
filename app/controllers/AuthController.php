<?php

declare(strict_types=1);

class AuthController {
    public static function renderLogin(): void {
        renderHeader('Sign In');
        ?>
        <section class="form-panel">
            <h2>Sign In</h2>
            <form method="post" class="card-form">
                <input type="hidden" name="action" value="login">
                <?php csrfField(); ?>
                <label>Email<input type="email" name="email" required></label>
                <label>Password<input type="password" name="password" required></label>
                <button type="submit">Login</button>
            </form>
            <p>New to Candyland? <a href="index.php?page=register">Create an account</a>.</p>
        </section>
        <?php
        renderFooter();
    }

    public static function renderRegister(): void {
        renderHeader('Register');
        ?>
        <section class="form-panel">
            <h2>Create an Account</h2>
            <form method="post" class="card-form">
                <input type="hidden" name="action" value="register">
                <?php csrfField(); ?>
                <label>First Name<input type="text" name="first_name" required></label>
                <label>Last Name<input type="text" name="last_name" required></label>
                <label>Email<input type="email" name="email" required></label>
                <label>Password<input type="password" name="password" required></label>
                <button type="submit">Register</button>
            </form>
        </section>
        <?php
        renderFooter();
    }

    public static function renderProfile(): void {
        requireLogin();
        $user = currentUser();
        renderHeader('Profile');
        ?>
        <section class="form-panel">
            <h2>Your Profile</h2>
            <form method="post" class="card-form">
                <input type="hidden" name="action" value="profile_update">
                <?php csrfField(); ?>
                <label>First Name<input type="text" name="first_name" value="<?php echo h($user['first_name']); ?>" required></label>
                <label>Last Name<input type="text" name="last_name" value="<?php echo h($user['last_name']); ?>" required></label>
                <label>Email<input type="email" name="email" value="<?php echo h($user['email']); ?>" required></label>
                <label>New Password<input type="password" name="password" placeholder="Leave blank to keep current password"></label>
                <button type="submit">Save Profile</button>
            </form>
        </section>
        <?php
        renderFooter();
    }

    public static function processLogin(): void {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($email === '' || $password === '') {
            flash('error', 'Email and password are required.');
            redirect('index.php?page=login');
        }
        $user = UserService::getUserByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Invalid email or password.');
            redirect('index.php?page=login');
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        flash('success', 'Welcome back, ' . $user['first_name'] . '!');
        redirect('index.php');
    }

    public static function processRegister(): void {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
            flash('error', 'All fields are required.');
            redirect('index.php?page=register');
        }
        if (UserService::getUserByEmail($email)) {
            flash('error', 'That email is already in use.');
            redirect('index.php?page=register');
        }
        $id = UserService::createUser([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'customer',
        ]);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        flash('success', 'Account created successfully.');
        redirect('index.php');
    }

    public static function processProfileUpdate(): void {
        requireLogin();
        $user = currentUser();
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($firstName === '' || $lastName === '' || $email === '') {
            flash('error', 'Name and email cannot be blank.');
            redirect('index.php?page=profile');
        }
        $existing = UserService::getUserByEmail($email);
        if ($existing && $existing['id'] !== $user['id']) {
            flash('error', 'That email is already in use.');
            redirect('index.php?page=profile');
        }
        $update = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        ];
        if ($password !== '') {
            $update['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }
        UserService::updateUserRecord($user['id'], $update);
        flash('success', 'Your profile has been updated.');
        redirect('index.php?page=profile');
    }

    public static function doLogout(): void {
        unset($_SESSION['user_id'], $_SESSION['cart_coupon']);
        flash('success', 'Logged out successfully.');
        redirect('index.php');
    }
}
