<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'ログインが必要です']);
  exit();
}

$user_id = $_SESSION['user_id'];
$history_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;


try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => 'DB接続エラー：' . $e->getMessage()]);
  exit();
}

// 使用済みチェックと更新
$sql = "SELECT * FROM point_history WHERE id = :id AND user_id = :user_id AND used = 0 AND type = 'use'";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $history_id, ':user_id' => $user_id]);
$row = $stmt->fetch();

if (!$row) {
  echo json_encode(['status' => 'error', 'message' => 'すでに使用済みか見つかりません']);
  exit();
}

$sql = "UPDATE point_history SET used = 1 WHERE id = :id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $history_id, ':user_id' => $user_id]);

echo json_encode(['status' => 'success', 'message' => '使用済みにしました']);
