<?php
/**
 * Шаблон страницы forgot password
 * Универсальная функция для обработки восстановления пароля
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

/**
 * Функция отображения формы восстановления пароля
 * Отвечает только за отображение UI формы восстановления пароля
 * Обработка данных формы выполняется в ajax/ajax.php
 */
function forgotPassword(): void
{
    // Генерация CSRF токена для формы
    $csrf_token = generateCSRFToken();
    setPageTitle('Восстановление пароля');
    
    // Отображение формы восстановления пароля
    ?>
    <div class="authorization-ajax-container">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Заголовок -->
            <div class="text-center">
                <div class="gradient-bg w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-lock-password-line text-3xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Восстановление пароля</h2>
                <p class="text-gray-600">Введите email адрес вашей учетной записи</p>
            </div>

            <!-- Форма запроса восстановления -->
            <div class="bg-white rounded-lg shadow-xl p-8 form-focus transition-all duration-300">
                <form method="POST" class="space-y-6" data-action="forgot-password">
                        <input type="hidden" name="action" value="forgot-password">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

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
                                autofocus
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="Введите ваш email"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            >
                        </div>

                        <!-- Информация -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="ri-information-line text-blue-500 text-lg mr-2 mt-0.5"></i>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium mb-1">Что произойдет дальше?</p>
                                    <ul class="space-y-1 text-xs">
                                        <li>• Вы получите ссылку для сброса пароля</li>
                                        <li>• Ссылка будет действительна 1 час</li>
                                        <li>• Ваш текущий пароль останется активным</li>
                                        <li>• После сброса старый пароль перестанет работать</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопка отправки -->
                        <button 
                            type="submit"
                            class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105"
                        >
                            <i class="ri-mail-send-line mr-2"></i>
                            Отправить ссылку для восстановления
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

                    <!-- Ссылки -->
                    <div class="text-center space-y-2">
                        <p class="text-gray-600">
                            Вспомнили пароль? 
                            <a href="#" class="open_login text-blue-600 hover:text-blue-700 font-semibold cursor-pointer">
                                Войти
                            </a>
                        </p>
                        <p class="text-gray-600">
                            Нет учетной записи? 
                            <a href="#" class="open_register text-blue-600 hover:text-blue-700 font-semibold cursor-pointer">
                                Зарегистрироваться
                            </a>
                        </p>
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

