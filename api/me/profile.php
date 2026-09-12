<?php
require_once __DIR__ . '/../bootstrap.php';

$user = require_user($pdo);
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') json_response(['ok' => false, 'message' => 'روش درخواست نامعتبر است.'], 405);

$input = json_input();
$fields = [
    'full_name' => trim((string)($input['full_name'] ?? '')),
    'national_code' => trim((string)($input['national_code'] ?? '')),
    'graduation_year' => (int)($input['graduation_year'] ?? 0),
    'field_of_study' => trim((string)($input['field_of_study'] ?? '')),
    'school' => trim((string)($input['school'] ?? '')),
    'city' => trim((string)($input['city'] ?? '')),
    'email' => trim((string)($input['email'] ?? '')),
];
if ($fields['full_name'] === '' || $fields['school'] === '') json_response(['ok' => false, 'message' => 'نام و مدرسه الزامی است.'], 422);

$stmt = $pdo->prepare('UPDATE users SET full_name=?, national_code=?, graduation_year=?, field_of_study=?, school=?, city=?, email=?, membership_status="pending" WHERE id=?');
$stmt->execute([...array_values($fields), $user['id']]);
json_response(['ok' => true, 'message' => 'اطلاعات برای بررسی مدیر ارسال شد.', 'membership_status' => 'pending']);
