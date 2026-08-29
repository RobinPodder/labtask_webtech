<?php
/**
 * logout.php — destroys the session. Call via fetch() then redirect
 * to index.html on the frontend, or just link a "Log Out" button here
 * directly (it will redirect on its own if hit via a plain GET).
 */

session_start();
$_SESSION = [];
session_destroy();

header('Content-Type: application/json');
echo json_encode(['success' => true, 'redirect' => 'index.html']);
