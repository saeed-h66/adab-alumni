<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_response(['ok' => false, 'message' => 'روش درخواست نامعتبر است.'], 405);
$user = require_user($pdo);
json_response(['ok' => true, 'membership' => [
    'status' => $user['membership_status'],
    'code' => $user['membership_code'],
    'full_name' => $user['full_name'],
]]);
