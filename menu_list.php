<?php
// エラー表示 & セッション開始
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// DB接続（Sakuraサーバー用）

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    exit("DB接続エラー: " . $e->getMessage());
}

// メニュー一覧取得
$sql = "SELECT * FROM menus ORDER BY price ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メニュー一覧 | Gakushoku+</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'noto': ['Noto Sans JP', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-in-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                        'scale-in': 'scaleIn 0.3s ease-out',
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
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .menu-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid rgba(16, 185, 129, 0.1);
        }
        
        .price-badge {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }
        
        .no-image-placeholder {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        }
        
        .filter-btn {
            transition: all 0.2s ease;
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
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
                            <p class="text-sm text-gray-600">メニュー一覧</p>
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
    <section class="pt-32 pb-12 text-center animate-fade-in">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                <span class="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                    学食メニュー一覧
                </span>
            </h2>
            <p class="text-xl text-gray-600 mb-8">美味しいメニューを価格順でご紹介</p>
            
            <!-- Search and Filter -->
            <div class="max-w-4xl mx-auto mb-8">
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex flex-col md:flex-row gap-4 mb-6">
                        <div class="flex-1">
                            <input type="text" id="search-input" placeholder="メニュー名で検索..." 
                                   class="w-full py-3 px-4 rounded-xl border-2 border-gray-200 focus:border-emerald-400 focus:outline-none transition-colors duration-200">
                        </div>
                        <button id="search-btn" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all duration-200 shadow-md">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            検索
                        </button>
                    </div>
                    
                    <!-- Price Filter -->
                    <div class="flex flex-wrap gap-2 justify-center">
                        <button class="filter-btn active px-4 py-2 rounded-full border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50" data-filter="all">
                            すべて
                        </button>
                        <button class="filter-btn px-4 py-2 rounded-full border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50" data-filter="0-300">
                            〜300円
                        </button>
                        <button class="filter-btn px-4 py-2 rounded-full border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50" data-filter="300-500">
                            300〜500円
                        </button>
                        <button class="filter-btn px-4 py-2 rounded-full border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50" data-filter="500-1000">
                            500〜1000円
                        </button>
                        <button class="filter-btn px-4 py-2 rounded-full border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50" data-filter="1000+">
                            1000円〜
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Grid -->
    <main class="container mx-auto px-4 pb-16">
        <div class="text-center mb-8">
            <p class="text-gray-600">
                <span id="menu-count"><?= count($menus) ?></span>件のメニューが見つかりました
            </p>
        </div>
        
        <div id="menu-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <?php foreach ($menus as $index => $menu): ?>
                <div class="menu-card bg-white rounded-2xl shadow-lg p-6 card-hover animate-scale-in" 
                     data-name="<?= htmlspecialchars($menu['name']) ?>" 
                     data-price="<?= $menu['price'] ?>"
                     style="animation-delay: <?= $index * 0.1 ?>s">
                    
                    <!-- Menu Image -->
                    <div class="mb-4 relative overflow-hidden rounded-xl">
                        <?php if (!empty($menu['image_path'])): ?>
                            <img src="<?= htmlspecialchars($menu['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($menu['name']) ?>" 
                                 class="w-full h-48 object-cover transition-transform duration-300 hover:scale-110">
                        <?php else: ?>
                            <div class="w-full h-48 no-image-placeholder flex items-center justify-center rounded-xl">
                                <div class="text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-gray-400 text-sm">画像なし</p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Price Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="price-badge text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                                ¥<?= number_format($menu['price']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Menu Info -->
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">
                            <?= htmlspecialchars($menu['name']) ?>
                        </h3>
                        

                        
                        <!-- Action Button -->
                        <button class="w-full py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:from-emerald-600 hover:to-teal-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            レビューを見る
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- No Results Message -->
        <div id="no-results" class="hidden text-center py-16">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">該当するメニューが見つかりません</h3>
            <p class="text-gray-500">検索条件を変更してお試しください</p>
        </div>
    </main>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 opacity-0 pointer-events-none">
        <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>

    <script>
        $(document).ready(function() {
            // Search functionality
            $('#search-btn, #search-input').on('click keyup', function(e) {
                if (e.type === 'keyup' && e.keyCode !== 13) return;
                
                const searchTerm = $('#search-input').val().toLowerCase();
                filterMenus(searchTerm, $('.filter-btn.active').data('filter'));
            });
            
            // Price filter functionality
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                
                const searchTerm = $('#search-input').val().toLowerCase();
                const priceFilter = $(this).data('filter');
                filterMenus(searchTerm, priceFilter);
            });
            
            // Filter function
            function filterMenus(searchTerm, priceFilter) {
                let visibleCount = 0;
                
                $('.menu-card').each(function() {
                    const menuName = $(this).data('name').toLowerCase();
                    const menuPrice = parseInt($(this).data('price'));
                    
                    let showBySearch = searchTerm === '' || menuName.includes(searchTerm);
                    let showByPrice = true;
                    
                    if (priceFilter !== 'all') {
                        switch(priceFilter) {
                            case '0-300':
                                showByPrice = menuPrice <= 300;
                                break;
                            case '300-500':
                                showByPrice = menuPrice > 300 && menuPrice <= 500;
                                break;
                            case '500-1000':
                                showByPrice = menuPrice > 500 && menuPrice <= 1000;
                                break;
                            case '1000+':
                                showByPrice = menuPrice > 1000;
                                break;
                        }
                    }
                    
                    if (showBySearch && showByPrice) {
                        $(this).removeClass('hidden').addClass('animate-scale-in');
                        visibleCount++;
                    } else {
                        $(this).addClass('hidden').removeClass('animate-scale-in');
                    }
                });
                
                $('#menu-count').text(visibleCount);
                
                if (visibleCount === 0) {
                    $('#no-results').removeClass('hidden');
                } else {
                    $('#no-results').addClass('hidden');
                }
            }
            
            // Back to top button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#back-to-top').removeClass('opacity-0 pointer-events-none').addClass('opacity-100');
                } else {
                    $('#back-to-top').addClass('opacity-0 pointer-events-none').removeClass('opacity-100');
                }
            });
            
            $('#back-to-top').click(function() {
                $('html, body').animate({scrollTop: 0}, 600);
            });
        });
    </script>
</body>
</html>