<?php
session_start();
header('Content-Type: application/json');


try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);

  $user_id = $_SESSION['user_id'] ?? null;
  $name = $_SESSION['name'] ?? null;

  $menu_name = $_POST['menu_name'] ?? '';
  $rating = $_POST['rating'] ?? '';
  $comment = $_POST['comment'] ?? '';
  $image_path = null;

  if (!$user_id || !$name) {
    echo json_encode(['status' => 'error', 'message' => 'ログインしてください']);
    exit();
  }

  if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_path = 'uploads/' . uniqid() . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
  }

  // 1. レビュー登録
  $stmt = $pdo->prepare("
    INSERT INTO gs_gakusyoku_table (user_id, name, menu_name, rating, comment, image, indate)
    VALUES (:user_id, :name, :menu_name, :rating, :comment, :image, NOW())
  ");
  $stmt->execute([
    ':user_id' => $user_id,
    ':name' => $name,
    ':menu_name' => $menu_name,
    ':rating' => $rating,
    ':comment' => $comment,
    ':image' => $image_path,
  ]);

  // 2. ポイント履歴に記録
  $stmt = $pdo->prepare("
    INSERT INTO point_history (user_id, type, points, description, indate, created_at, used)
    VALUES (:user_id, 'earn', :points, :description, NOW(), NOW(), 0)
  ");
  $stmt->execute([
    ':user_id' => $user_id,
    ':points' => 5,
    ':description' => 'レビュー投稿によるポイント獲得'
  ]);

  // 3. ユーザーのポイント加算
  $stmt = $pdo->prepare("UPDATE users SET point = point + :points WHERE id = :id");
  $stmt->execute([
    ':points' => 5,
    ':id' => $user_id
  ]);

  echo json_encode(['status' => 'success', 'message' => 'レビューを投稿しました']);

} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => 'DBエラー: ' . $e->getMessage()]);
}
