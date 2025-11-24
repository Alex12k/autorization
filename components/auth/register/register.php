<?php
/**
 * Шаблон страницы register
 * Универсальная функция для обработки регистрации
 */

// Загрузка зависимостей (если файл вызывается напрямую)
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../functions.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    define('SYSTEM_INITIALIZED', true);
}

/**
 * Функция отображения формы регистрации в модальном окне
 * Универсальная функция для отображения формы регистрации
 */
function modal_register_form(): void
{
    // Генерация CSRF токена для формы
    $csrf_token = generateCSRFToken();
    ?>
    <div class="box-modal register-modal">
        <!-- Заголовок модального окна -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="ri-user-add-line text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Регистрация</h2>
                    <p class="text-sm text-gray-500">Создайте новую учетную запись</p>
                </div>
            </div>
            <button class="arcticmodal-close text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Форма регистрации -->
        <div class="space-y-6">
            <form method="POST" class="space-y-6" data-action="register">
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- Имя пользователя -->
                <div>
                    <label for="modal_register_username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="ri-user-line mr-1"></i>
                        Имя пользователя
                    </label>
                    <input 
                        type="text" 
                        id="modal_register_username" 
                        name="username" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        placeholder="Введите имя пользователя"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label for="modal_register_email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="ri-mail-line mr-1"></i>
                        Email адрес
                    </label>
                    <input 
                        type="email" 
                        id="modal_register_email" 
                        name="email" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        placeholder="Введите email адрес"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    >
                </div>

                <!-- Пароль -->
                <div>
                    <label for="modal_register_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="ri-lock-line mr-1"></i>
                        Пароль
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="modal_register_password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Введите пароль (минимум 6 символов)"
                        >
                        <button 
                            type="button"
                            onclick="togglePasswordRegister('modal_register_password', 'toggle-modal-register-password-icon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                            title="Показать/скрыть пароль"
                        >
                            <i id="toggle-modal-register-password-icon" class="ri-eye-line text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Подтверждение пароля -->
                <div>
                    <label for="modal_register_confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="ri-lock-password-line mr-1"></i>
                        Подтвердите пароль
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="modal_register_confirm_password" 
                            name="confirm_password" 
                            required
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Повторите пароль"
                        >
                        <button 
                            type="button"
                            onclick="togglePasswordRegister('modal_register_confirm_password', 'toggle-modal-register-confirm-password-icon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                            title="Показать/скрыть пароль"
                        >
                            <i id="toggle-modal-register-confirm-password-icon" class="ri-eye-line text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Кнопка регистрации -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-green-500 to-teal-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-teal-700 transition-all duration-300 transform hover:scale-105"
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
                    <a href="#" class="open_modal_login_form text-blue-600 hover:text-blue-700 font-semibold cursor-pointer">
                        Войти
                    </a>
                </p>
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
        </div>
    </div>
    <?php
}
