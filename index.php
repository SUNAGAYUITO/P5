<?php
session_start();

// DB接続

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    // メニュー一覧を取得
    $stmt = $pdo->prepare("SELECT name FROM menus ORDER BY id ASC");
    $stmt->execute();
    $menu_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit("DB接続エラー: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <title>Gakushoku+</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'noto': ['Noto Sans JP', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'bounce-gentle': 'bounceGentle 2s infinite',
                    }
                }
            }
        }
    </script>
    <style>
        body { 
            font-family: 'Noto Sans JP', sans-serif; 
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideUp {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes bounceGentle {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .tab-active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .search-result-item {
            transition: all 0.2s ease;
        }
        
        .search-result-item:hover {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            transform: translateX(5px);
        }
        
        .star-rating {
            color: #fbbf24;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
        }
    </style>
</head>

<body class="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 min-h-screen">
    <!-- Header -->
    <header class="fixed top-0 left-0 w-full z-50 glass-effect shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="flex items-center space-x-3">
                        <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-xl">G+</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                                Gakushoku+
                            </h1>
                            <p class="text-sm text-gray-600">学食レビュー＆ポイント還元</p>
                        </div>
                    </a>
                </div>
                
                <nav class="flex items-center space-x-6">
                    <?php if (isset($_SESSION['name'])): ?>
                        <div class="text-right">
                            <span class="text-sm text-gray-600">こんにちは、</span>
                            <span class="font-semibold text-emerald-700"><?= htmlspecialchars($_SESSION['name']) ?>さん</span>
                            <div id="current-point" class="flex items-center space-x-2 mt-1">
                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="font-bold text-emerald-600">--ポイント</span>
                            </div>
                        </div>
                        <a href="logout.php" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 border border-red-200">
                            ログアウト
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="px-4 py-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors duration-200">
                            ログイン
                        </a>
                        <a href="register.php" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:from-emerald-600 hover:to-teal-600 transition-all duration-200 shadow-md">
                            会員登録
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-32 pb-16 text-center animate-fade-in">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl md:text-6xl font-bold text-gray-800 mb-6 leading-tight pb-2">
                大学生のための<br>
                <span class="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent animate-bounce-gentle">
                    学食レビュー＆ポイント還元サービス
                </span>
            </h2>
            <div class="max-w-3xl mx-auto mb-12">
                <p class="text-xl text-gray-600 mb-4">学食の美味しいメニューをシェアして、ポイントを貯めよう！</p>
                <p class="text-lg text-gray-500">あなたのレビューが他の学生の食体験を豊かにします。</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#review">
                    <button class="px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full hover:from-emerald-600 hover:to-teal-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold text-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        レビューを投稿する
                    </button>
                </a>
                <a href="menu_list.php">
                    <button class="px-8 py-4 bg-white border-2 border-emerald-300 text-emerald-600 rounded-full hover:bg-emerald-50 hover:border-emerald-400 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1 font-semibold text-lg">
                        メニューを見る
                    </button>
                </a>
            </div>
        </div>
    </section>

    <!-- Tab Navigation -->
    <nav class="container mx-auto px-4 mb-8">
        <div class="flex justify-center">
            <div class="bg-white rounded-2xl p-2 shadow-xl border border-emerald-100">
                <ul class="flex space-x-2">
                    <li class="tab tab-active rounded-xl px-6 py-3 cursor-pointer transition-all duration-300 font-semibold flex items-center space-x-2" data-tab="home">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>ホーム</span>
                    </li>
                    <li class="tab rounded-xl px-6 py-3 cursor-pointer transition-all duration-300 font-semibold text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 flex items-center space-x-2" data-tab="ranking">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span>ランキング</span>
                    </li>
                    <li class="tab rounded-xl px-6 py-3 cursor-pointer transition-all duration-300 font-semibold text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 flex items-center space-x-2" data-tab="review">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>レビュー</span>
                    </li>
                    <li class="tab rounded-xl px-6 py-3 cursor-pointer transition-all duration-300 font-semibold text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 flex items-center space-x-2" data-tab="point">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                        <span>ポイント</span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 pb-16">
        <!-- Home Tab -->
        <div id="home" class="tab-content animate-fade-in">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <h3 class="text-2xl font-bold text-center mb-6 text-gray-800">メニュー検索</h3>
                    <div class="flex gap-3 mb-6">
                        <input type="text" id="keyword" placeholder="メニュー名を検索..." 
                               class="flex-1 py-3 px-4 rounded-xl border-2 border-gray-200 focus:border-emerald-400 focus:outline-none transition-colors duration-200" />
                        <button id="search-btn" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="results" class="space-y-2"></div>
                </div>
            </div>
        </div>

        <!-- Ranking Tab -->
        <div id="ranking" class="tab-content hidden">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <h3 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        人気メニューランキング
                    </h3>
                    <div id="ai-recommend" class="mb-6"></div>
                    <ul id="ranking-list" class="space-y-4"></ul>
                </div>
            </div>
        </div>

        <!-- Review Tab -->
        <div id="review" class="tab-content hidden">
            <div class="max-w-4xl mx-auto space-y-8">
                <!-- Review Form -->
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">レビュー投稿フォーム</h2>
                    <form id="review-form" class="space-y-6">
                        <div>
                            <label for="menu_name" class="block text-sm font-semibold text-gray-700 mb-2">メニュー名</label>
                            <select name="menu_name" id="menu_name" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-emerald-400 focus:outline-none transition-colors duration-200" required>
                                <option value="">メニューを選択してください</option>
                                <?php foreach ($menu_list as $menu): ?>
                                    <option value="<?= htmlspecialchars($menu['name']) ?>"><?= htmlspecialchars($menu['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="rating" class="block text-sm font-semibold text-gray-700 mb-2">評価（1〜5）</label>
                            <select name="rating" id="rating" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-emerald-400 focus:outline-none transition-colors duration-200" required>
                                <option value="5">★★★★★（5）</option>
                                <option value="4">★★★★☆（4）</option>
                                <option value="3">★★★☆☆（3）</option>
                                <option value="2">★★☆☆☆（2）</option>
                                <option value="1">★☆☆☆☆（1）</option>
                            </select>
                        </div>

                        <div>
                            <label for="comment" class="block text-sm font-semibold text-gray-700 mb-2">コメント</label>
                            <textarea name="comment" id="comment" rows="4" 
                                      class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-emerald-400 focus:outline-none transition-colors duration-200" 
                                      placeholder="味・量・コスパなど自由にレビューを書いてください！" required></textarea>
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">写真をアップロード（任意）</label>
                            <div class="border-2 border-dashed border-emerald-300 rounded-xl p-8 text-center hover:border-emerald-400 transition-colors duration-200 cursor-pointer">
                                <svg class="w-12 h-12 text-emerald-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-emerald-600 font-medium">クリックまたはドラッグして画像をアップロード</p>
                                <p class="text-sm text-gray-500 mt-2">JPG, PNG, GIF (最大5MB)</p>
                                <input type="file" name="image" id="image" accept="image/*" class="hidden">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            投稿する
                        </button>
                    </form>
                    <div id="review-message" class="mt-4 text-emerald-700 font-semibold"></div>
                </div>

                <!-- Reviews List -->
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <h3 class="text-2xl font-bold mb-6 text-gray-800">みんなのレビュー</h3>
                    <div id="review-list" class="space-y-6"></div>
                </div>
            </div>
        </div>

        <!-- Point Tab -->
        <div id="point" class="tab-content hidden">
            <div class="max-w-4xl mx-auto space-y-8">
                <!-- Point Exchange -->
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover text-center">
                    <div class="mb-6">
                        <svg class="w-16 h-16 text-yellow-500 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <h3 class="text-3xl font-bold text-emerald-600 mb-2">現在のポイント</h3>
                        <div class="text-5xl font-bold text-gray-800 mb-4">--ポイント</div>
                    </div>
                    <a href="reward.php">
                        <button class="px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-400 text-white font-bold rounded-xl hover:from-yellow-500 hover:to-orange-500 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                            ポイントと交換する
                        </button>
                    </a>
                </div>

                <!-- Point History -->
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">ポイント履歴</h2>
                    <ul id="point-list" class="space-y-4"></ul>
                </div>
            </div>
        </div>
        <!-- モーダル -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-40 hidden justify-center items-center z-50">
  <div class="bg-white rounded-xl shadow-xl p-6 max-w-md text-center">
    <h2 id="modal-title" class="text-xl font-bold mb-2 text-emerald-600">完了しました！</h2>
    <p id="modal-message" class="text-gray-700 mb-4">レビューが正常に投稿されました。</p>
    <button id="modal-close" class="px-6 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition">
      閉じる
    </button>
  </div>
</div>

    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./script.js"></script>
    <script>
  // モーダル表示関数
  function showModal(title, message) {
    $('#modal-title').text(title);
    $('#modal-message').text(message);
    $('#modal').removeClass('hidden').addClass('flex');
  }

  // モーダルを閉じる
  $('#modal-close').on('click', function () {
    $('#modal').addClass('hidden').removeClass('flex');
  });


</script>

</body>
</html>