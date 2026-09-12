<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$configPath = __DIR__ . '/config.php';
if (!is_file($configPath)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'تنظیمات سرور کامل نشده است.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$config = require $configPath;
$pdo = new PDO($config['db']['dsn'], $config['db']['user'], $config['db']['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

function json_input(): array {
    $raw = file_get_contents('php://input') ?: '{}';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function json_response(array $payload, int $status = 200): never {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function normalize_mobile(string $mobile): string {
    $digits = preg_replace('/[^0-9+]/', '', $mobile);
    if (str_starts_with($digits, '+98')) $digits = '0' . substr($digits, 3);
    if (str_starts_with($digits, '98')) $digits = '0' . substr($digits, 2);
    return $digits;
}

function valid_mobile(string $mobile): bool {
    return (bool) preg_match('/^09\d{9}$/', $mobile);
}
