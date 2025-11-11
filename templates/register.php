<?php
/**
 * Шаблон страницы register
 */

// Загрузка зависимостей (если файл вызывается напрямую)
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/../../functions/functions.php';
    require_once __DIR__ . '/../../functions/layout.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    define('SYSTEM_INITIALIZED', true);
}

function register(): void
{
    // Если пользователь уже авторизован, перенаправляем на dashboard
    if (isAuthenticated()) {
        redirect('dashboard');
        exit;
    }

    $register_error = null;

    // Обработка регистрации
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
        $csrf_token = $_POST['csrf_token'] ?? '';

        if (!verifyCSRFToken($csrf_token)) {
            $register_error = 'Ошибка безопасности. Попробуйте еще раз.';
        } else {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            $result = registerUser($username, $email, $password, $confirm_password);

            if ($result['success']) {
                // Перенаправляем на страницу входа с сообщением об успехе
                // Используем паттерн Post/Redirect/Get (PRG) для предотвращения повторной отправки формы
                $_SESSION['registration_success'] = 'Регистрация успешна! Теперь вы можете войти.';
                redirect('login');
                exit;
            } else {
                $register_error = $result['error'];
            }
        }
    }

    $csrf_token = generateCSRFToken();

    setPageTitle('Регистрация');
    ?>
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
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Введите пароль (минимум 6 символов)"
                                >
                                <button 
                                    type="button"
                                    onclick="togglePassword('password', 'toggle-password-icon')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                                    title="Показать/скрыть пароль"
                                >
                                    <i id="toggle-password-icon" class="ri-eye-line text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Подтверждение пароля -->
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="ri-lock-password-line mr-1"></i>
                                Подтвердите пароль
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    required
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Повторите пароль"
                                >
                                <button 
                                    type="button"
                                    onclick="togglePassword('confirm_password', 'toggle-confirm-password-icon')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                                    title="Показать/скрыть пароль"
                                >
                                    <i id="toggle-confirm-password-icon" class="ri-eye-line text-xl"></i>
                                </button>
                            </div>
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
                            <a href="<?= url('login') ?>" class="text-blue-600 hover:text-blue-700 font-semibold">
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
                    <a href="<?= url() ?>" class="text-gray-500 hover:text-gray-700 text-sm">
                        <i class="ri-arrow-left-line mr-1"></i>
                        Вернуться на главную
                    </a>
                </div>
            </div>
        </div>

        <script>
            function togglePassword(inputId, iconId) {
                const passwordInput = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('ri-eye-line');
                    icon.classList.add('ri-eye-off-line');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('ri-eye-off-line');
                    icon.classList.add('ri-eye-line');
            }
        }
    </script>
    <?php
}

// Обработка прямого вызова файла (AJAX запрос)
if (isset($_POST['action']) && $_POST['action'] === 'open_register') {
    // Вызываем функцию register() для обработки
    register();
    exit;
}
