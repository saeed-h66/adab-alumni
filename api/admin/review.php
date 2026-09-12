<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_response(['ok' => false, 'message' => 'روش درخواست نامعتبر است.'], 405);
$admin = require_admin($pdo);
$userId = (int)($_GET['user_id'] ?? 0);
$input = json_input();
$decision = $input['decision'] ?? '';
$note = trim((string)($input['note'] ?? ''));
if ($userId < 1 || !in_array($decision, ['approved','revision','rejected'], true)) json_response(['ok' => false, 'message' => 'اطلاعات بررسی نامعتبر است.'], 422);

$pdo->beginTransaction();
try {
    $code = $decision === 'approved' ? 'ADAB-' . strtoupper(bin2hex(random_bytes(4))) : null;
    $stmt = $pdo->prepare('UPDATE users SET membership_status=?, membership_code=COALESCE(?, membership_code) WHERE id=?');
    $stmt->execute([$decision, $code, $userId]);
    $pdo->prepare('INSERT INTO membership_reviews (user_id, admin_id, decision, note) VALUES (?, ?, ?, ?)')->execute([$userId, $admin['id'], $decision, $note]);
    $pdo->prepare('INSERT INTO audit_logs (user_id, action, entity_type, entity_id, metadata) VALUES (?, ?, ?, ?, ?)')->execute([$admin['id'], 'membership_review', 'user', $userId, json_encode(['decision' => $decision], JSON_UNESCAPED_UNICODE)]);
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['ok' => false, 'message' => 'ثبت تصمیم مدیر انجام نشد.'], 500);
}
json_response(['ok' => true, 'message' => 'تصمیم مدیر ثبت شد.', 'decision' => $decision]);
