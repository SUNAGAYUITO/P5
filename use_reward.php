<?php
session_start();
header("Content-Type: application/json");
require_once("db_connect.php");

$id = $_POST['id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$id || !$user_id) {
  echo json_encode(["status" => "error", "message" => "IDが不正です"]);
  exit;
}

$stmt = $pdo->prepare("UPDATE point_history SET used = 1 WHERE id = :id AND user_id = :user_id");
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status) {
  echo json_encode(["status" => "success"]);
} else {
  echo json_encode(["status" => "error", "message" => "使用処理に失敗"]);
}
