<?php
require __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Alike&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="card">
        <header class="card-header"><h1>Welcome, <?= e($_SESSION['user_name']) ?></h1></header>
        <div class="card-body">
            <img class="logo" src="abstract-blue-and-navy-logo-design-free-vector.jpg" alt="" width="32" height="52" onerror="this.onerror=null;this.src='logo.svg'">
            <p class="switch">You're logged in.</p>
            <a class="btn btn-primary" href="logout.php">Log out</a>
        </div>
    </main>
</body>
</html>
