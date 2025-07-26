<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB接続設定


try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // GETパラメータで menu_id を取得
    $menu_id = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : null;

    // SQL文を条件に応じて構築
    $sql = "SELECT 
                id, 
                name, 
                menu_name, 
                rating, 
                comment, 
                image AS image_path, 
                indate, 
                user_id 
            FROM gs_gakusyoku_table";
    $params = [];

    if ($menu_id !== null) {
        $sql .= " WHERE menu_id = :menu_id";
        $params[':menu_id'] = $menu_id;
    }

    $sql .= " ORDER BY indate DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $reviews
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'DBエラー: ' . $e->getMessage()
    ]);
}
