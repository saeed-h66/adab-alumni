<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_response(['ok' => false, 'message' => 'روش درخواست نامعتبر است.'], 405);
require_admin($pdo);
$status = $_GET['status'] ?? 'pending';
$allowed = ['incomplete','pending','approved','revision','rejected'];
if (!in_array($status, $allowed, true)) json_response(['ok' => false, 'message' => 'وضعیت نامعتبر است.'], 422);
$stmt = $pdo->prepare('SELECT id, mobile, full_name, school, city, graduation_year, membership_status, created_at FROM users WHERE membership_status = ? ORDER BY created_at DESC');
$stmt->execute([$status]);
json_response(['ok' => true, 'members' => $stmt->fetchAll()]);
