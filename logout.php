<?php
session_start();              // セッション開始
$_SESSION = array();          // セッション変数を全て削除
session_destroy();            // セッションを完全に破棄
header("Location: login.php"); // ログインページへリダイレクト
exit();
?>
