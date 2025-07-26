<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();


try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    exit("DB接続エラー: " . $e->getMessage());
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($name && $email && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, point) VALUES (:name, :email, :password, 0)");
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hash, PDO::PARAM_STR);
            $stmt->execute();
            
            $message = '登録が完了しました。ログインしてください。';
            $messageType = 'success';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $message = 'このメールアドレスは既に登録されています。';
            } else {
                $message = '登録エラー：' . $e->getMessage();
            }
            $messageType = 'error';
        }
    } else {
        $message = 'すべての項目を入力してください。';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員登録 | Gakushoku+</title>
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
                        'fade-in': 'fadeIn 0.8s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'bounce-gentle': 'bounceGentle 2s infinite',
                        'shake': 'shake 0.5s ease-in-out',
                        'success-bounce': 'successBounce 0.6s ease-out',
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
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes bounceGentle {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @keyframes successBounce {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #064e3b 0%, #10b981 50%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .input-focus {
            transition: all 0.3s ease;
        }
        
        .input-focus:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        
        .floating-shapes::before,
        .floating-shapes::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
        }
        
        .floating-shapes::before {
            width: 400px;
            height: 400px;
            top: -200px;
            right: -200px;
            animation: bounceGentle 4s infinite;
        }
        
        .floating-shapes::after {
            width: 250px;
            height: 250px;
            bottom: -125px;
            left: -125px;
            animation: bounceGentle 3s infinite reverse;
        }
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .strength-weak { background: #ef4444; width: 25%; }
        .strength-fair { background: #f59e0b; width: 50%; }
        .strength-good { background: #10b981; width: 75%; }
        .strength-strong { background: #059669; width: 100%; }
    </style>
</head>

<body class="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 min-h-screen flex items-center justify-center relative">
    <!-- Floating Background Shapes -->
    <div class="floating-shapes"></div>
    
    <!-- Main Container -->
    <div class="w-full max-w-md mx-auto p-6 animate-fade-in">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4 shadow-2xl animate-bounce-gentle">
                <span class="text-white font-bold text-2xl">G+</span>
            </div>
            <h1 class="text-4xl font-bold hero-gradient mb-2">Gakushoku+</h1>
            <p class="text-gray-600">学食レビュー＆ポイント還元サービス</p>
        </div>

        <!-- Register Form -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8 animate-slide-up">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">会員登録</h2>
                <p class="text-gray-600">新しいアカウントを作成してください</p>
            </div>

            <!-- Message Display -->
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg <?= $messageType === 'success' ? 'bg-green-50 border-l-4 border-green-400 animate-success-bounce' : 'bg-red-50 border-l-4 border-red-400 animate-shake' ?>">
                    <div class="flex items-center">
                        <?php if ($messageType === 'success'): ?>
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-green-700 font-medium"><?= htmlspecialchars($message) ?></p>
                        <?php else: ?>
                            <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-red-700 font-medium"><?= htmlspecialchars($message) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="POST" class="space-y-6" id="registerForm">
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        ユーザー名
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 input-focus focus:outline-none" 
                        placeholder="山田太郎"
                        required
                        value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"
                    >
                    <p class="text-xs text-gray-500 mt-1">2文字以上で入力してください</p>
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                        メールアドレス
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 input-focus focus:outline-none" 
                        placeholder="example@email.com"
                        required
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                    >
                    <div class="flex items-center mt-1">
                        <svg class="w-3 h-3 text-gray-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-gray-500">有効なメールアドレスを入力してください</p>
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        パスワード
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 pr-12 input-focus focus:outline-none" 
                            placeholder="8文字以上のパスワード"
                            required
                            minlength="8"
                        >
                        <button 
                            type="button" 
                            id="togglePassword" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="eyeIcon">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Password Strength Indicator -->
                    <div class="mt-2">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs text-gray-500">パスワード強度</span>
                            <span id="strength-text" class="text-xs font-medium text-gray-500">弱い</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1">
                            <div id="strength-bar" class="password-strength bg-gray-300 rounded-full"></div>
                        </div>
                    </div>
                    
                    <div class="mt-2 text-xs text-gray-500">
                        <p>• 8文字以上</p>
                        <p>• 英数字を含む</p>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-start">
                    <input type="checkbox" id="terms" class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500 mt-1" required>
                    <label for="terms" class="ml-2 text-sm text-gray-600">
                        <a href="#" class="text-emerald-600 hover:text-emerald-700 hover:underline">利用規約</a>
                        および
                        <a href="#" class="text-emerald-600 hover:text-emerald-700 hover:underline">プライバシーポリシー</a>
                        に同意します
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full btn-primary text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl"
                    id="submitBtn"
                >
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    アカウントを作成
                </button>
            </form>

            <!-- Divider -->
            <div class="my-6 flex items-center">
                <div class="flex-1 border-t border-gray-300"></div>
                <span class="px-4 text-sm text-gray-500">または</span>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-gray-600 mb-4">既にアカウントをお持ちの方</p>
                <a href="login.php" class="inline-flex items-center px-6 py-3 border-2 border-emerald-300 text-emerald-600 rounded-xl hover:bg-emerald-50 hover:border-emerald-400 transition-all duration-200 font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    ログイン
                </a>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="index.php" class="inline-flex items-center text-gray-600 hover:text-emerald-600 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                ホームに戻る
            </a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Password toggle functionality
            $('#togglePassword').click(function() {
                const passwordField = $('#password');
                const eyeIcon = $('#eyeIcon');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    eyeIcon.html(`
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                    `);
                } else {
                    passwordField.attr('type', 'password');
                    eyeIcon.html(`
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    `);
                }
            });

            // Password strength checker
            $('#password').on('input', function() {
                const password = $(this).val();
                const strengthBar = $('#strength-bar');
                const strengthText = $('#strength-text');
                
                let strength = 0;
                let strengthLabel = '弱い';
                let strengthClass = 'strength-weak';
                
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[^a-zA-Z0-9]/)) strength++;
                
                switch(strength) {
                    case 0:
                    case 1:
                        strengthLabel = '弱い';
                        strengthClass = 'strength-weak';
                        break;
                    case 2:
                        strengthLabel = '普通';
                        strengthClass = 'strength-fair';
                        break;
                    case 3:
                    case 4:
                        strengthLabel = '良い';
                        strengthClass = 'strength-good';
                        break;
                    case 5:
                        strengthLabel = '強い';
                        strengthClass = 'strength-strong';
                        break;
                }
                
                strengthBar.removeClass('strength-weak strength-fair strength-good strength-strong')
                          .addClass(strengthClass);
                strengthText.text(strengthLabel);
            });

            // Form validation
            $('#registerForm').submit(function(e) {
                const name = $('#name').val().trim();
                const email = $('#email').val().trim();
                const password = $('#password').val();
                const terms = $('#terms').is(':checked');
                
                if (name.length < 2) {
                    alert('ユーザー名は2文字以上で入力してください。');
                    e.preventDefault();
                    return;
                }
                
                if (password.length < 8) {
                    alert('パスワードは8文字以上で入力してください。');
                    e.preventDefault();
                    return;
                }
                
                if (!terms) {
                    alert('利用規約とプライバシーポリシーに同意してください。');
                    e.preventDefault();
                    return;
                }
                
                // Show loading state
                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true)
                         .html(`
                            <svg class="w-5 h-5 inline mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            登録中...
                         `);
            });

            // Input field animations
            $('input').focus(function() {
                $(this).parent().addClass('animate-slide-up');
            });

            // Auto-focus on name field
            $('#name').focus();

            // Success message auto-redirect
            <?php if ($messageType === 'success'): ?>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 3000);
            <?php endif; ?>
        });
    </script>
</body>
</html>