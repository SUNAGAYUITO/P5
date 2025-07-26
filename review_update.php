<?php
session_start();
header('Content-Type: application/json');


try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  $id = $_POST['id'] ?? null;
  $user_id = $_SESSION['user_id'] ?? null;
  $comment = $_POST['comment'] ?? '';
  $rating = $_POST['rating'] ?? '';
  $image_path = null;

  if (!$id || !$user_id) {
    echo json_encode(['status' => 'error', 'message' => '不正なアクセスです']);
    exit();
  }

  $stmt = $pdo->prepare("SELECT user_id FROM gs_gakusyoku_table WHERE id = :id");
  $stmt->execute([':id' => $id]);
  $review = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$review || $review['user_id'] != $user_id) {
    echo json_encode(['status' => 'error', 'message' => '編集権限がありません']);
    exit();
  }

  if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_path = 'uploads/' . uniqid() . '.' . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
  }

  $sql = "UPDATE gs_gakusyoku_table SET comment = :comment, rating = :rating";
  if ($image_path) $sql .= ", image = :image";
  $sql .= " WHERE id = :id";

  $stmt = $pdo->prepare($sql);
  $params = [
    ':comment' => $comment,
    ':rating' => $rating,
    ':id' => $id
  ];
  if ($image_path) $params[':image'] = $image_path;

  $stmt->execute($params);

  echo json_encode(['status' => 'success', 'message' => 'レビューを更新しました']);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
