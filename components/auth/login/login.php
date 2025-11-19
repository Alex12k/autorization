<?php
/**
 * Шаблон страницы login
 * Универсальная функция для обработки логина
 */

// Загрузка зависимостей (если файл вызывается напрямую)
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../functions.php';
    require_once __DIR__ . '/../../functions/layout.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    define('SYSTEM_INITIALIZED', true);
}

/**
 * Функция отображения формы логина
 * Отвечает только за отображение UI формы входа
 * Обработка данных формы выполняется в ajax/ajax.php
 */
function login(): void
{
    $registration_success = null;

    // Проверяем сообщение об успешной регистрации
    if (isset($_SESSION['registration_success'])) {
        $registration_success = $_SESSION['registration_success'];
        unset($_SESSION['registration_success']);
    }

    // Генерация CSRF токена для формы
    $csrf_token = generateCSRFToken();
    setPageTitle('Вход в систему');
    
    // Отображение формы логина
    ?>
    <div class="authorization-ajax-container">
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
                <?php if ($registration_success): ?>
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="ri-check-line text-green-500 mr-2"></i>
                            <span class="text-green-700"><?= htmlspecialchars($registration_success) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6" data-action="login">
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
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                <i class="ri-lock-line mr-1"></i>
                                Пароль
                            </label>
                            <a href="#" class="open_forgot-password text-sm text-blue-600 hover:text-blue-700 font-medium cursor-pointer">
                                Забыли пароль?
                            </a>
                        </div>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="Введите пароль"
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
                        <a href="#" class="open_register text-blue-600 hover:text-blue-700 font-semibold cursor-pointer">
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
                <a href="<?= url() ?>" class="text-gray-500 hover:text-gray-700 text-sm">
                    <i class="ri-arrow-left-line mr-1"></i>
                    Вернуться на главную
                </a>
            </div>
        </div>
    </div>
    </div>
    <?php
}
