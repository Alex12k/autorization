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
    <title>Регистрация</title>
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
                    <i class="ri-user-add-line text-3xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Регистрация</h2>
                <p class="text-gray-600">Создайте новую учетную запись</p>
            </div>

            <!-- Форма регистрации -->
            <div class="bg-white rounded-lg shadow-xl p-8 form-focus transition-all duration-300">
                <?php if (isset($register_error)): ?>
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="ri-error-warning-line text-red-500 mr-2"></i>
                            <span class="text-red-700"><?= htmlspecialchars($register_error) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($register_success)): ?>
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="ri-check-line text-green-500 mr-2"></i>
                            <span class="text-green-700"><?= htmlspecialchars($register_success) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="register">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    
                    <!-- Имя пользователя -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="ri-user-line mr-1"></i>
                            Имя пользователя
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Введите имя пользователя"
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="ri-mail-line mr-1"></i>
                            Email адрес
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Введите email адрес"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
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
                            placeholder="Введите пароль (минимум 6 символов)"
                        >
                    </div>

                    <!-- Подтверждение пароля -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="ri-lock-password-line mr-1"></i>
                            Подтвердите пароль
                        </label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Повторите пароль"
                        >
                    </div>

                    <!-- Кнопка регистрации -->
                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-green-500 to-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105"
                    >
                        <i class="ri-user-add-line mr-2"></i>
                        Зарегистрироваться
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

                <!-- Ссылка на вход -->
                <div class="text-center">
                    <p class="text-gray-600">
                        Уже есть учетная запись? 
                        <a href="login.php" class="text-blue-600 hover:text-blue-700 font-semibold">
                            Войти
                        </a>
                    </p>
                </div>
            </div>

            <!-- Требования к паролю -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-yellow-800 mb-2">
                    <i class="ri-information-line mr-1"></i>
                    Требования к паролю:
                </h3>
                <ul class="text-xs text-yellow-700 space-y-1">
                    <li>• Минимум 6 символов</li>
                    <li>• Рекомендуется использовать буквы и цифры</li>
                    <li>• Не используйте простые пароли</li>
                </ul>
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