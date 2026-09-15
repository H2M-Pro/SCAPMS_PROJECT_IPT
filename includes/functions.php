<?php

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function base_url(string $path = ''): string
{
    // Adjust BASE_PATH in includes/auth.php if the app is deployed in a subfolder.
    return BASE_PATH . $path;
}

function now_datetime(): string
{
    return date('Y-m-d H:i:s');
}
