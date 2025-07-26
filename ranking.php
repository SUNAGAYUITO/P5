<?php
header("Content-Type: application/json; charset=UTF-8");



try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  // 平均評価と件数を取得（グループ化してソート）
  $sql = "
    SELECT menu_name, ROUND(AVG(rating), 2) AS avg_rating, COUNT(*) AS review_count
    FROM gs_gakusyoku_table
    GROUP BY menu_name
    HAVING review_count >= 1
    ORDER BY avg_rating DESC, review_count DESC
    LIMIT 10;
  ";
  $stmt = $pdo->query($sql);
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'status' => 'success',
    'data' => $data
  ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
  echo json_encode([
    'status' => 'error',
    'message' => $e->getMessage()
  ]);
}
exit();
