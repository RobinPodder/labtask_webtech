<?php
/**
 * signup.php — POST { name, email, phone, password, role }
 * role must be one of: doctor, sitter, patient
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

$name     = trim($data['name'] ?? '');
$email    = trim($data['email'] ?? '');
$phone    = trim($data['phone'] ?? '');
$password = $data['password'] ?? '';
$role     = $data['role'] ?? '';

$allowedRoles = ['doctor', 'sitter', 'patient'];

if ($name === '' || $email === '' || $phone === '' || $password === '' || !in_array($role, $allowedRoles, true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'সব ফিল্ড পূরণ করতে হবে এবং সঠিক role বাছতে হবে।']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Email সঠিক ফরম্যাটে দাও।']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Password অন্তত ৬ অক্ষরের হতে হবে।']);
    exit;
}

// Check duplicate email
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'এই ইমেইল দিয়ে আগেই একটা অ্যাকাউন্ট আছে।']);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$name, $email, $phone, $hash, $role]);
    $userId = (int) $pdo->lastInsertId();

    // Create the role-specific profile row with sensible defaults.
    // Users can fill these in later via the "Edit Profile" panel.
    if ($role === 'doctor') {
        $pdo->prepare(
            'INSERT INTO doctors (user_id, specialty, years_exp, consultation_fee, followup_fee, rating)
             VALUES (?, ?, 0, 0, 0, 0)'
        )->execute([$userId, 'General Physician']);
    } elseif ($role === 'sitter') {
        $pdo->prepare(
            'INSERT INTO sitters (user_id, specialization, service_area, daily_rate, hourly_rate, rating)
             VALUES (?, ?, ?, 0, 0, 0)'
        )->execute([$userId, 'General Care', 'Not set']);
    } else { // patient
        $pdo->prepare(
            'INSERT INTO patients (user_id, age, blood_group) VALUES (?, NULL, NULL)'
        )->execute([$userId]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'অ্যাকাউন্ট তৈরি করা যায়নি।', 'error' => $e->getMessage()]);
    exit;
}

// Log the user in immediately after signup
$_SESSION['user_id'] = $userId;
$_SESSION['role']    = $role;
$_SESSION['name']    = $name;

echo json_encode([
    'success'  => true,
    'message'  => 'অ্যাকাউন্ট তৈরি হয়ে গেছে।',
    'redirect' => "dashboard-{$role}.html",
]);
