<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['ok' => false, 'message' => 'روش درخواست نامعتبر است.'], 405);

$input = json_input();
$mobile = normalize_mobile((string)($input['mobile'] ?? ''));
$purpose = ($input['purpose'] ?? 'login') === 'signup' ? 'signup' : 'login';
if (!valid_mobile($mobile)) json_response(['ok' => false, 'message' => 'شماره موبایل معتبر نیست.'], 422);

$code = (string) random_int(1000, 9999);
$hash = password_hash($code, PASSWORD_DEFAULT);
$expires = (new DateTimeImmutable('now'))->modify('+' . (int)$config['otp']['ttl_seconds'] . ' seconds')->format('Y-m-d H:i:s');

$pdo->prepare('UPDATE otp_requests SET consumed_at = NOW() WHERE mobile = ? AND consumed_at IS NULL')->execute([$mobile]);
$pdo->prepare('INSERT INTO otp_requests (mobile, code_hash, purpose, expires_at) VALUES (?, ?, ?, ?)')->execute([$mobile, $hash, $purpose, $expires]);

// TODO: جایگزینی با سرویس پیامک واقعی؛ کد هرگز در پاسخ API برگردانده نشود.
json_response(['ok' => true, 'message' => 'کد تأیید ارسال شد.', 'expires_in' => (int)$config['otp']['ttl_seconds']]);
