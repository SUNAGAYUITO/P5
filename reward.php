<?php
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>特典交換 | Gakushoku+</title>
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
                        'bounce-gentle': 'bounceGentle 2s infinite',
                        'pulse-glow': 'pulseGlow 2s infinite',
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
        
        @keyframes bounceGentle {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
            50% { box-shadow: 0 0 30px rgba(16, 185, 129, 0.6); }
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
        
        .reward-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 2px solid transparent;
            background-clip: padding-box;
        }
        
        .reward-card:hover {
            border: 2px solid rgba(16, 185, 129, 0.3);
        }
        
        .point-badge {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }
        
        .exchange-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            transition: all 0.3s ease;
        }
        
        .exchange-btn:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        }
        
        .exchange-btn:disabled {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .point-display {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 2px solid #f59e0b;
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
                            <p class="text-sm text-gray-600">特典交換</p>
                        </div>
                    </a>
                </div>
                
                <nav class="flex items-center space-x-6">
                    <?php if (isset($_SESSION['name'])): ?>
                        <div class="text-right">
                            <span class="text-sm text-gray-600">こんにちは、</span>
                            <span class="font-semibold text-emerald-700"><?= htmlspecialchars($_SESSION['name']) ?>さん</span>
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
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6">
                <span class="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                    ポイント特典交換
                </span>
            </h2>
            <p class="text-xl text-gray-600 mb-8">貯めたポイントで素敵な特典と交換しよう！</p>
            
            <!-- Current Points Display -->
            <div class="max-w-md mx-auto mb-8">
                <div class="point-display rounded-2xl p-6 shadow-xl animate-pulse-glow">
                    <div class="flex items-center justify-center space-x-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">現在の所持ポイント</p>
                            <p id="current-point" class="text-3xl font-bold text-gray-800">--ポイント</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rewards Grid -->
    <main class="container mx-auto px-4 pb-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <!-- からあげ無料券 -->
            <div class="reward-card rounded-2xl shadow-xl p-6 card-hover animate-slide-up">
                <div class="text-center">
                    <!-- Reward Icon -->
                    <div class="w-20 h-20 bg-gradient-to-r from-orange-400 to-red-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-2">からあげ無料券</h3>
                    <p class="text-gray-600 mb-4 text-sm">人気のからあげが無料で食べられる特別券</p>
                    
                    <!-- Point Cost -->
                    <div class="point-badge text-white px-4 py-2 rounded-full text-sm font-bold mb-4 inline-block shadow-md">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        10ポイント
                    </div>
                    
                    <button class="exchange-btn w-full text-white px-6 py-3 rounded-xl font-semibold shadow-lg" 
                            data-reward="からあげ無料券" data-cost="10">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        交換する
                    </button>
                </div>
            </div>

            <!-- ドリンク100円割引 -->
            <div class="reward-card rounded-2xl shadow-xl p-6 card-hover animate-slide-up" style="animation-delay: 0.1s">
                <div class="text-center">
                    <!-- Reward Icon -->
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-2">ドリンク100円割引</h3>
                    <p class="text-gray-600 mb-4 text-sm">お好きなドリンクが100円お得になる割引券</p>
                    
                    <!-- Point Cost -->
                    <div class="point-badge text-white px-4 py-2 rounded-full text-sm font-bold mb-4 inline-block shadow-md">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        5ポイント
                    </div>
                    
                    <button class="exchange-btn w-full text-white px-6 py-3 rounded-xl font-semibold shadow-lg" 
                            data-reward="ドリンク割引" data-cost="5">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        交換する
                    </button>
                </div>
            </div>

            <!-- 追加特典例 -->
            <div class="reward-card rounded-2xl shadow-xl p-6 card-hover animate-slide-up" style="animation-delay: 0.2s">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-2">デザート50円割引</h3>
                    <p class="text-gray-600 mb-4 text-sm">甘いデザートがお得に楽しめる割引券</p>
                    
                    <div class="point-badge text-white px-4 py-2 rounded-full text-sm font-bold mb-4 inline-block shadow-md">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        3ポイント
                    </div>
                    
                    <button class="exchange-btn w-full text-white px-6 py-3 rounded-xl font-semibold shadow-lg" 
                            data-reward="デザート割引" data-cost="3">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        交換する
                    </button>
                </div>
            </div>

            <!-- プレミアム特典 -->
            <div class="reward-card rounded-2xl shadow-xl p-6 card-hover animate-slide-up" style="animation-delay: 0.3s">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-2">定食セット無料券</h3>
                    <p class="text-gray-600 mb-4 text-sm">豪華な定食セットが無料で楽しめるプレミアム券</p>
                    
                    <div class="point-badge text-white px-4 py-2 rounded-full text-sm font-bold mb-4 inline-block shadow-md">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        25ポイント
                    </div>
                    
                    <button class="exchange-btn w-full text-white px-6 py-3 rounded-xl font-semibold shadow-lg" 
                            data-reward="定食セット無料券" data-cost="25">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        交換する
                    </button>
                </div>
            </div>
        </div>

        <!-- Message Display -->
        <div id="message" class="mt-12 text-center">
            <div class="max-w-md mx-auto p-4 rounded-xl shadow-lg hidden" id="message-box">
                <p class="font-semibold text-lg"></p>
            </div>
        </div>

        <!-- Back to Home Button -->
        <div class="text-center mt-12">
            <a href="./index.php" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                ホームに戻る
            </a>
        </div>
    </main>

    <script>
        let currentPoints = 0;

        function loadPoint() {
            $.getJSON("point_total.php", function (res) {
                if (res.status === "success") {
                    currentPoints = res.point;
                    $("#current-point").text(res.point + "ポイント");
                    updateButtonStates();
                } else {
                    $("#current-point").text("ポイント取得失敗");
                }
            });
        }

        function updateButtonStates() {
            $(".exchange-btn").each(function() {
                const cost = parseInt($(this).data("cost"));
                if (currentPoints < cost) {
                    $(this).prop("disabled", true)
                           .removeClass("exchange-btn")
                           .addClass("bg-gray-400")
                           .html('<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>ポイント不足');
                } else {
                    $(this).prop("disabled", false)
                           .removeClass("bg-gray-400")
                           .addClass("exchange-btn")
                           .html('<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>交換する');
                }
            });
        }

        function showMessage(message, isSuccess = true) {
            const messageBox = $("#message-box");
            const messageText = messageBox.find("p");
            
            messageText.text(message);
            
            if (isSuccess) {
                messageBox.removeClass("bg-red-100 border-red-300 text-red-700")
                          .addClass("bg-green-100 border-green-300 text-green-700 border-2");
            } else {
                messageBox.removeClass("bg-green-100 border-green-300 text-green-700")
                          .addClass("bg-red-100 border-red-300 text-red-700 border-2");
            }
            
            messageBox.removeClass("hidden").addClass("animate-slide-up");
            
            setTimeout(() => {
                messageBox.addClass("hidden").removeClass("animate-slide-up");
            }, 5000);
        }

        $(document).on("click", ".exchange-btn", function () {
            const reward = $(this).data("reward");
            const cost = $(this).data("cost");
            
            if (!confirm(`${reward}（${cost}ポイント）を交換しますか？`)) return;
            
            // ボタンを一時的に無効化
            $(this).prop("disabled", true).text("交換中...");
            
            $.post("exchange_reward.php", { reward, cost }, function (res) {
                if (res.status === "success") {
                    showMessage(res.message, true);
                    loadPoint();
                } else {
                    showMessage("交換失敗：" + res.message, false);
                }
            }, "json").always(() => {
                // ボタンを再有効化
                setTimeout(() => {
                    loadPoint(); // ボタン状態も更新される
                }, 1000);
            });
        });

        $(function () {
            loadPoint();
        });
    </script>
</body>
</html>