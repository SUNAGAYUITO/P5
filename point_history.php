<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

header("Content-Type: application/json");

// ログインチェック
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'ログインしてください']);
  exit();
}

$user_id = $_SESSION['user_id'];

// DB接続

try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => 'DB接続エラー: ' . $e->getMessage()]);
  exit();
}

// 所持ポイント取得
$point_sql = "SELECT point FROM users WHERE id = :user_id";
$point_stmt = $pdo->prepare($point_sql);
$point_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$point_stmt->execute();
$user_point = $point_stmt->fetch(PDO::FETCH_ASSOC)['point'] ?? 0;

// ポイント履歴取得
$sql = "SELECT id, type, points, description, created_at, used FROM point_history 
        WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

try {
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'status' => 'success',
    'point' => $user_point,
    'data' => $rows
  ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => '取得エラー: ' . $e->getMessage()]);
}
