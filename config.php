<?php
declare(strict_types=1);

session_start();

$dbDir = __DIR__ . '/data';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0775, true);
}

$pdo = new PDO('sqlite:' . $dbDir . '/app.sqlite', null, null, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$pdo->exec('CREATE TABLE IF NOT EXISTS users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name    TEXT NOT NULL,
    last_name     TEXT NOT NULL,
    identifier    TEXT NOT NULL UNIQUE,   -- email address or contact number
    password_hash TEXT NOT NULL,
    created_at    TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
)');

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $to): void
{
    header('Location: ' . $to);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
}

function normalize_identifier(string $raw): ?string
{
    $raw = trim($raw);
    if (filter_var($raw, FILTER_VALIDATE_EMAIL)) {
        return strtolower($raw);
    }
    $digits = preg_replace('/[\s\-().]/', '', $raw);
    if (preg_match('/^\+?\d{7,15}$/', $digits)) {
        return $digits;
    }
    return null;
}

function password_field(string $id, string $placeholder, string $autocomplete): void
{
    ?>
    <div class="password-wrap">
        <input type="password" id="<?= e($id) ?>" name="password" placeholder="<?= e($placeholder) ?>"
               autocomplete="<?= e($autocomplete) ?>" required>
        <button type="button" class="toggle-password" data-target="<?= e($id) ?>"
                aria-label="Show password" aria-pressed="false">
            <svg class="icon-hidden" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>
            </svg>
            <svg class="icon-visible" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
            </svg>
        </button>
    </div>
    <?php
}

function google_button(string $label): void
{
    ?>
    <button type="button" class="btn btn-google" id="google-btn">
        <svg viewBox="0 0 24 24" aria-hidden="true" width="14" height="14">
            <path fill="#EA4335" d="M12 5.04c1.62 0 3.07.56 4.21 1.65l3.15-3.15C17.45 1.74 14.97.75 12 .75 7.7.75 3.99 3.22 2.18 6.82l3.67 2.85C6.72 7.1 9.14 5.04 12 5.04z"/>
            <path fill="#4285F4" d="M23.25 12.27c0-.79-.07-1.54-.2-2.27H12v4.51h6.3a5.4 5.4 0 0 1-2.34 3.54l3.62 2.81c2.12-1.96 3.67-4.84 3.67-8.59z"/>
            <path fill="#FBBC05" d="M5.85 14.33a7.2 7.2 0 0 1 0-4.66L2.18 6.82a11.25 11.25 0 0 0 0 10.36l3.67-2.85z"/>
            <path fill="#34A853" d="M12 23.25c3.04 0 5.59-1 7.45-2.73l-3.62-2.81c-1.01.68-2.3 1.08-3.83 1.08-2.86 0-5.28-2.06-6.15-4.83l-3.67 2.85C3.99 20.78 7.7 23.25 12 23.25z"/>
        </svg>
        <?= e($label) ?>
    </button>
    <?php
}
