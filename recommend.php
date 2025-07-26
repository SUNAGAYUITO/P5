<?php
header("Content-Type: application/json; charset=UTF-8");

$apiKey = 'sk-XXXXXXXXXXXXXXXXXXXXXXXXXXXX'; 

try {
  $pdo = new PDO($dsn, $user, $pass);
  $stmt = $pdo->query("SELECT menu_name, comment FROM gs_gakusyoku_table ORDER BY indate DESC LIMIT 30");
  $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // GPTに送るテキスト作成
  $texts = [];
  foreach ($reviews as $r) {
    $texts[] = "【" . $r["menu_name"] . "】" . $r["comment"];
  }
  $joined = implode("\n", $texts);
} catch (PDOException $e) {
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
  exit();
}

$messages = [
  ["role" => "system", "content" => "あなたは大学生のために学食メニューを分析するAIです。"],
  ["role" => "user", "content" => "次のレビューを参考に、いま人気・満足度が高いおすすめメニューを1つ選んで、その理由とともに教えてください：\n" . $joined]
];

$data = [
  "model" => "gpt-3.5-turbo",
  "messages" => $messages,
  "max_tokens" => 150,
  "temperature" => 0.8
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Content-Type: application/json",
  "Authorization: Bearer {$apiKey}"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
if (isset($result["choices"][0]["message"]["content"])) {
  echo json_encode([
    "status" => "success",
    "recommendation" => trim($result["choices"][0]["message"]["content"])
  ], JSON_UNESCAPED_UNICODE);
} else {
  echo json_encode(["status" => "error", "message" => "おすすめ生成に失敗しました"]);
}
