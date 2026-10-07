<?php
require __DIR__ . '/config.php';
redirect(isset($_SESSION['user_id']) ? 'dashboard.php' : 'login.php');
