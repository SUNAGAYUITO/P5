<?php
session_start();
header('Content-Type: application/json');


try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  $id = $_POST['id'] ?? null;
  $user_id = $_SESSION['user_id'] ?? null;

  if (!$id || !$user_id) {
    echo json_encode(['status' => 'error', 'message' => '不正なリクエストです']);
    exit();
  }

  $stmt = $pdo->prepare("SELECT user_id FROM gs_gakusyoku_table WHERE id = :id");
  $stmt->execute([':id' => $id]);
  $review = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$review || $review['user_id'] != $user_id) {
    echo json_encode(['status' => 'error', 'message' => '削除権限がありません']);
    exit();
  }

  $stmt = $pdo->prepare("DELETE FROM gs_gakusyoku_table WHERE id = :id");
  $stmt->execute([':id' => $id]);

  echo json_encode(['status' => 'success', 'message' => 'レビューを削除しました']);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
