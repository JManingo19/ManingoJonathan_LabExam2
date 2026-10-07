<?php
require __DIR__ . '/config.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error = '';
$old   = ['first_name' => '', 'last_name' => '', 'identifier' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['first_name'] = trim((string) ($_POST['first_name'] ?? ''));
    $old['last_name']  = trim((string) ($_POST['last_name'] ?? ''));
    $old['identifier'] = trim((string) ($_POST['identifier'] ?? ''));
    $password          = (string) ($_POST['password'] ?? '');

    $identifier = normalize_identifier($old['identifier']);

    if (!csrf_valid()) {
        $error = 'Your session expired. Refresh the page and try again.';
    } elseif ($old['first_name'] === '' || $old['last_name'] === '') {
        $error = 'Enter your first and last name.';
    } elseif ($identifier === null) {
        $error = 'Enter a valid email address or contact number.';
    } elseif (strlen($password) < 8) {
        $error = 'Use a password with at least 8 characters.';
    } else {
        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE identifier = ?');
        $stmt->execute([$identifier]);

        if ($stmt->fetch()) {
            $error = 'An account with that email or number already exists.';
        } else {
            $pdo->prepare('INSERT INTO users (first_name, last_name, identifier, password_hash) VALUES (?, ?, ?, ?)')
                ->execute([
                    $old['first_name'],
                    $old['last_name'],
                    $identifier,
                    password_hash($password, PASSWORD_DEFAULT),
                ]);
            redirect('login.php?registered=1');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alike&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="card">
        <header class="card-header"><h1>Create Account</h1></header>

        <form class="card-body" method="post" action="register.php" novalidate data-form="register">
            <img class="logo" src="abstract-blue-and-navy-logo-design-free-vector.jpg" alt="" width="32" height="52" onerror="this.onerror=null;this.src='logo.svg'">

            <p class="message message-error" role="alert" id="form-error" <?= $error ? '' : 'hidden' ?>><?= e($error) ?></p>

            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

            <div class="name-row">
                <div>
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter here...."
                           value="<?= e($old['first_name']) ?>" autocomplete="given-name" required>
                </div>
                <div>
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter here...."
                           value="<?= e($old['last_name']) ?>" autocomplete="family-name" required>
                </div>
            </div>

            <label for="identifier">Email Address or Contact Number</label>
            <input type="text" id="identifier" name="identifier" placeholder="Enter here...."
                   value="<?= e($old['identifier']) ?>" autocomplete="username" required>

            <label for="password">Password</label>
            <?php password_field('password', 'Enter your password', 'new-password'); ?>

            <button type="submit" class="btn btn-primary">Register</button>

            <div class="divider"><span>OR</span></div>
            <?php google_button('Register with Google'); ?>

            <p class="switch">Already Registered? <a href="login.php">Log In</a></p>
        </form>
    </main>
    <script src="script.js"></script>
</body>
</html>
