<?php
require __DIR__ . '/config.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error      = '';
$notice     = isset($_GET['registered']) ? 'Account created. Log in to continue.' : '';
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim((string) ($_POST['username'] ?? ''));
    $password   = (string) ($_POST['password'] ?? '');

    if (!csrf_valid()) {
        $error = 'Your session expired. Refresh the page and try again.';
    } elseif ($identifier === '' || $password === '') {
        $error = 'Enter your username and password.';
    } else {
        $key  = normalize_identifier($identifier) ?? $identifier;
        $stmt = $pdo->prepare('SELECT * FROM users WHERE identifier = ?');
        $stmt->execute([$key]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = (int) $user['id'];
            $_SESSION['user_name'] = $user['first_name'];
            redirect('dashboard.php');
        }
        $error = 'Username or password is incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alike&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="card">
        <header class="card-header"><h1>Log in to Continue...</h1></header>

        <form class="card-body" method="post" action="login.php" novalidate data-form="login">
            <img class="logo" src="abstract-blue-and-navy-logo-design-free-vector.jpg" alt="" width="32" height="52" onerror="this.onerror=null;this.src='logo.svg'">

            <?php if ($notice): ?><p class="message message-ok" role="status"><?= e($notice) ?></p><?php endif; ?>
            <p class="message message-error" role="alert" id="form-error" <?= $error ? '' : 'hidden' ?>><?= e($error) ?></p>

            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username"
                   value="<?= e($identifier) ?>" autocomplete="username" required>

            <label for="password">Password</label>
            <?php password_field('password', 'Enter your password', 'current-password'); ?>

            <button type="submit" class="btn btn-primary">Log in</button>

            <div class="divider"><span>OR</span></div>
            <?php google_button('Sign in with Google'); ?>

            <p class="switch">Don't have an account? <a href="register.php">Register Here</a></p>
        </form>
    </main>
    <script src="script.js"></script>
</body>
</html>
