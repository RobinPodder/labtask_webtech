<?php
/**
 * me.php — GET, no params
 * Returns the currently logged-in user's basic info from the session,
 * so dashboard pages can show the real name instead of hardcoded mock data.
 * Returns JSON { success, name, role, initials } or { success:false } if not logged in.
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$name = $_SESSION['name'];
$role = $_SESSION['role'];

$parts = preg_split('/\s+/', trim($name));
$initials = strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));

echo json_encode([
    'success'  => true,
    'name'     => $name,
    'role'     => $role,
    'initials' => $initials,
]);
