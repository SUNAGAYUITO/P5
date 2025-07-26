<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

// DB接続

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'ログインしていません']);
  exit;
}

$user_id = $_SESSION['user_id'];
$change = intval($_POST['points'] ?? 0); // 加減ポイント（+/-）
$type = $_POST['type'] ?? ($change >= 0 ? 'earn' : 'use');
$desc = $_POST['description'] ?? '';

if ($change === 0) {
  echo json_encode(['status' => 'error', 'message' => 'ポイントが指定されていません']);
  exit;
}

try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  // 現在のポイント取得
  $stmt = $pdo->prepare("SELECT point FROM users WHERE id = :id");
  $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$user) throw new Exception("ユーザーが見つかりません");

  $new_point = $user['point'] + $change;
  if ($new_point < 0) {
    echo json_encode(['status' => 'error', 'message' => 'ポイントが不足しています']);
    exit;
  }

  // トランザクションで更新と履歴記録
  $pdo->beginTransaction();

  // ポイント更新
  $update = $pdo->prepare("UPDATE users SET point = :point WHERE id = :id");
  $update->bindValue(':point', $new_point, PDO::PARAM_INT);
  $update->bindValue(':id', $user_id, PDO::PARAM_INT);
  $update->execute();

  // 履歴追加
  $insert = $pdo->prepare("INSERT INTO point_history (user_id, type, points, description) VALUES (:uid, :type, :points, :desc)");
  $insert->bindValue(':uid', $user_id, PDO::PARAM_INT);
  $insert->bindValue(':type', $type, PDO::PARAM_STR);
  $insert->bindValue(':points', $change, PDO::PARAM_INT);
  $insert->bindValue(':desc', $desc, PDO::PARAM_STR);
  $insert->execute();

  $pdo->commit();

  // セッションのポイントも更新
  $_SESSION['user_point'] = $new_point;

  echo json_encode(['status' => 'success', 'message' => 'ポイント更新成功', 'new_point' => $new_point]);

} catch (Exception $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
