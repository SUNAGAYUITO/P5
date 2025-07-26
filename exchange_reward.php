<?php
session_start();
header('Content-Type: application/json');

// ログインチェック
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'ログインが必要です']);
  exit();
}

$user_id = $_SESSION['user_id'];
$reward = $_POST['reward'] ?? '';
$cost = (int) ($_POST['cost'] ?? 0);

if (!$reward || $cost <= 0) {
  echo json_encode(['status' => 'error', 'message' => '不正な入力です']);
  exit();
}

// DB接続


try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  // 所持ポイント確認
  $stmt = $pdo->prepare("SELECT point FROM users WHERE id = :id");
  $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
  $stmt->execute();
  $current_point = $stmt->fetchColumn();

  if ($current_point < $cost) {
    echo json_encode(['status' => 'error', 'message' => 'ポイントが足りません']);
    exit();
  }

  // ポイント減算
  $pdo->beginTransaction();

  $stmt = $pdo->prepare("UPDATE users SET point = point - :cost WHERE id = :id");
  $stmt->bindValue(':cost', $cost, PDO::PARAM_INT);
  $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
  $stmt->execute();

  // 履歴に記録
  $stmt = $pdo->prepare("INSERT INTO point_history (user_id, type, points, description, created_at)
                         VALUES (:user_id, 'use', :points, :desc, NOW())");
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->bindValue(':points', $cost, PDO::PARAM_INT);
  $stmt->bindValue(':desc', $reward, PDO::PARAM_STR);
  $stmt->execute();

  $pdo->commit();

  echo json_encode(['status' => 'success', 'message' => "「{$reward}」と交換しました"]);

} catch (PDOException $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['status' => 'error', 'message' => '交換処理エラー: ' . $e->getMessage()]);
}
