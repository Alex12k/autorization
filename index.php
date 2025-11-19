<?php
/**
 * Front Controller - Единая точка входа в приложение
 * Все запросы проходят через этот файл
 */

// Инициализация системы
require_once 'config.php';
require_once 'components/auth/functions.php';
require_once 'functions/route.php';
require_once 'functions/layout.php';

session_start();

// Глобальные скрипты (загружаются на ВСЕХ страницах)
// Порядок важен: сначала общий обработчик, потом модули
addGlobalScript(url('components/auth/auth-ajax-handler.js'));  // Общий обработчик (утилиты)
addGlobalScript(url('components/auth/login/script.js'));      // Модуль логина
addGlobalScript(url('components/auth/register/script.js'));    // Модуль регистрации
addGlobalScript(url('components/auth/forgot-password/script.js'));  // Модуль восстановления пароля
addGlobalScript(url('components/auth/reset-password/script.js'));    // Модуль сброса пароля
addGlobalScript(url('components/auth/logout/script.js'));            // Модуль выхода из системы

// Отрисовка header
renderHeader();

// Определяем текущий URI
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = dirname($_SERVER['SCRIPT_NAME']);

// Убираем базовый путь из URI
if ($script_name !== '/') {
    $request_uri = substr($request_uri, strlen($script_name));
}

// Убираем GET параметры
$request_uri = strtok($request_uri, '?');

// Убираем index.php из начала URI, если он там есть
if (strpos($request_uri, '/index.php') === 0) {
    $request_uri = substr($request_uri, strlen('/index.php'));
} elseif (strpos($request_uri, 'index.php/') === 0) {
    $request_uri = substr($request_uri, strlen('index.php/'));
}

// Убираем начальный и конечный слэш
$request_uri = trim($request_uri, '/');

// Если это корневой URL (точка входа)
if (empty($request_uri)) {
    // Проверка авторизации пользователя
    if (isAuthenticated()) {
        // Если пользователь залогинен, перенаправляем на dashboard
        redirect('dashboard');
        exit;
    }
    
    // Показываем главную страницу с кнопками входа и регистрации
    setPageTitle('Главная страница');
    ?>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-50 to-purple-50">
        <div class="max-w-md w-full space-y-8">
            <!-- Заголовок -->
            <div class="text-center">
                <div class="gradient-bg w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <i class="ri-shield-user-line text-4xl text-white"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-3">Добро пожаловать</h1>
                <p class="text-gray-600 text-lg">Выберите действие для продолжения</p>
            </div>

            <!-- Контейнер для форм авторизации -->
            <div class="authorization-ajax-container">
                <!-- Кнопки действий -->
                <div class="bg-white rounded-lg shadow-xl p-8 space-y-4">
                    <button 
                        type="button" 
                        class="open_login w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-md"
                    >
                        <i class="ri-login-box-line mr-2"></i>
                        Войти в систему
                    </button>
                    
                    <button 
                        type="button" 
                        class="open_register w-full bg-gradient-to-r from-green-500 to-teal-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:from-green-600 hover:to-teal-700 transition-all duration-300 transform hover:scale-105 shadow-md"
                    >
                        <i class="ri-user-add-line mr-2"></i>
                        Зарегистрироваться
                    </button>
                </div>

                <!-- Демо аккаунты -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                    <h3 class="text-sm font-semibold text-blue-800 mb-2">
                        <i class="ri-information-line mr-1"></i>
                        Демо аккаунты:
                    </h3>
                    <div class="space-y-1 text-xs text-blue-700">
                        <div><strong>Админ:</strong> admin / admin123</div>
                        <div><strong>Пользователь:</strong> user / user123</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    // Для всех остальных URL используем роутер
    route();
}

// Отрисовка footer
renderFooter();
