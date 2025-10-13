<?php
require_once 'auth_db.php';

// Если пользователь уже авторизован, перенаправляем на dashboard
if (isAuthenticated()) {
    header('Location: dashboard.php');
    exit;
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .form-focus:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Заголовок -->
            <div class="text-center">
                <div class="gradient-bg w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-user-line text-3xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Добро пожаловать</h2>
                <p class="text-gray-600">Войдите в свою учетную запись</p>
            </div>

            <!-- Форма входа -->
            <div class="bg-white rounded-lg shadow-xl p-8 form-focus transition-all duration-300">
                <?php if (isset($login_error)): ?>
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="ri-error-warning-line text-red-500 mr-2"></i>
                            <span class="text-red-700"><?= htmlspecialchars($login_error) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- Имя пользователя/Email -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="ri-user-line mr-1"></i>
                            Имя пользователя или Email
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Введите имя пользователя или email"
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        >
                    </div>

                    <!-- Пароль -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="ri-lock-line mr-1"></i>
                            Пароль
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Введите пароль"
                        >
                    </div>

                    <!-- Кнопка входа -->
                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105"
                    >
                        <i class="ri-login-box-line mr-2"></i>
                        Войти
                    </button>
                </form>

                <!-- Разделитель -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">или</span>
                    </div>
                </div>

                <!-- Ссылка на регистрацию -->
                <div class="text-center">
                    <p class="text-gray-600">
                        Нет учетной записи? 
                        <a href="register.php" class="text-blue-600 hover:text-blue-700 font-semibold">
                            Зарегистрироваться
                        </a>
                    </p>
                </div>
            </div>

            <!-- Демо аккаунты -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-800 mb-2">
                    <i class="ri-information-line mr-1"></i>
                    Демо аккаунты:
                </h3>
                <div class="space-y-1 text-xs text-blue-700">
                    <div><strong>Админ:</strong> admin / admin123</div>
                    <div><strong>Пользователь:</strong> user / user123</div>
                </div>
            </div>

            <!-- Ссылка на главную -->
            <div class="text-center">
                <a href="index.php" class="text-gray-500 hover:text-gray-700 text-sm">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Вернуться на главную
                </a>
            </div>
        </div>
    </div>
</body>
</html> 