<?php
/**
 * login.php — POST { email, password, role }
 * Returns JSON { success, message, redirect }
 */

session_start();
header('Content-Type: application/json');
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$role     = $data['role'] ?? '';

$allowedRoles = ['doctor', 'sitter', 'patient'];

if ($email === '' || $password === '' || !in_array($role, $allowedRoles, true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Email, password এবং role — সবগুলো দিতে হবে।']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, name, password_hash, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Login query ব্যর্থ হয়েছে — ডাটাবেস ঠিকমতো সেটআপ হয়েছে কিনা চেক করো।',
        'error'   => $e->getMessage(),
    ]);
    exit;
}

if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'ভুল email অথবা password।']);
    exit;
}

if ($user['role'] !== $role) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => "এই অ্যাকাউন্টটা {$user['role']} হিসেবে রেজিস্টার করা — সঠিক role বেছে আবার চেষ্টা করো।",
    ]);
    exit;
}

$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['role']    = $user['role'];
$_SESSION['name']    = $user['name'];

echo json_encode([
    'success'  => true,
    'message'  => 'Login successful.',
    'redirect' => "dashboard-{$user['role']}.html",
]);
