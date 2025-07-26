<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");



try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit();
}

$keyword = $_REQUEST['keyword'] ?? '';

$sql = "SELECT name, price, image_path FROM menus WHERE name LIKE :keyword ORDER BY name ASC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);

try {
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $results
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'SQL Error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
exit();
