<?php
/**
 * auth-check.php — include this at the very top of any protected
 * PHP page (e.g. dashboard pages once converted to .php) to guard
 * against unauthenticated or wrong-role access.
 *
 * Usage in a dashboard page:
 *   <?php
 *   $requiredRole = 'doctor'; // or 'sitter' / 'patient'
 *   require __DIR__ . '/api/auth-check.php';
 *   ?>
 *
 * If $requiredRole is not set before including this file, it only
 * checks that *some* user is logged in (any role).
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /telemedicine-plus-plus/login.html');
    exit;
}

if (isset($requiredRole) && $_SESSION['role'] !== $requiredRole) {
    header('Location: /telemedicine-plus-plus/login.html');
    exit;
}

// Convenience variables available to the including page:
$currentUserId   = (int) $_SESSION['user_id'];
$currentUserRole = $_SESSION['role'];
$currentUserName = $_SESSION['name'];
