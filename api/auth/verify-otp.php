<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['ok' => false, 'message' => 'روش درخواست نامعتبر است.'], 405);

$input = json_input();
$mobile = normalize_mobile((string)($input['mobile'] ?? ''));
$code = trim((string)($input['code'] ?? ''));
$purpose = ($input['purpose'] ?? 'login') === 'signup' ? 'signup' : 'login';
if (!valid_mobile($mobile) || !preg_match('/^\d{4}$/', $code)) json_response(['ok' => false, 'message' => 'اطلاعات تأیید نامعتبر است.'], 422);

$stmt = $pdo->prepare('SELECT * FROM otp_requests WHERE mobile = ? AND purpose = ? AND consumed_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
$stmt->execute([$mobile, $purpose]);
$otp = $stmt->fetch();
if (!$otp || (int)$otp['attempts'] >= (int)$config['otp']['max_attempts']) json_response(['ok' => false, 'message' => 'کد منقضی یا غیرقابل استفاده است.'], 401);

$pdo->prepare('UPDATE otp_requests SET attempts = attempts + 1 WHERE id = ?')->execute([$otp['id']]);
if (!password_verify($code, $otp['code_hash'])) json_response(['ok' => false, 'message' => 'کد تأیید صحیح نیست.'], 401);

$pdo->prepare('UPDATE otp_requests SET consumed_at = NOW() WHERE id = ?')->execute([$otp['id']]);
$pdo->prepare('INSERT INTO users (mobile) VALUES (?) ON DUPLICATE KEY UPDATE updated_at = NOW()')->execute([$mobile]);
$userStmt = $pdo->prepare('SELECT id, membership_status, role FROM users WHERE mobile = ? LIMIT 1');
$userStmt->execute([$mobile]);
$user = $userStmt->fetch();
$token = bin2hex(random_bytes(32));
$expires = (new DateTimeImmutable('now'))->modify('+30 days')->format('Y-m-d H:i:s');
$pdo->prepare('INSERT INTO sessions (id, user_id, expires_at) VALUES (?, ?, ?)')->execute([$token, $user['id'], $expires]);

setcookie('adab_session', $token, ['expires' => time() + 2592000, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Lax']);
json_response(['ok' => true, 'authenticated' => true, 'next' => $user['membership_status'] === 'approved' ? 'dashboard' : 'complete-profile', 'user' => $user]);
