<?php
session_start();
header('Content-Type: application/json');

// ログインしていない場合
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'ログインしてください']);
  exit();
}

$user_id = $_SESSION['user_id'];


try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  $stmt = $pdo->prepare("SELECT point FROM users WHERE id = :id");
  $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
  $stmt->execute();
  $point = $stmt->fetchColumn();

  echo json_encode(['status' => 'success', 'point' => $point]);

} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => '取得失敗: ' . $e->getMessage()]);
}
