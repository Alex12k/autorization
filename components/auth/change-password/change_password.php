<?php
/**
 * Шаблон компонента change-password
 * Форма смены пароля для авторизованных пользователей
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

// Загрузка функций компонента change-password
require_once __DIR__ . '/functions.php';

/**
 * Функция отображения формы смены пароля в модальном окне
 */
function modal_change_password_form(): void
{
    // Проверка аутентификации
    if (!isAuthenticated()) {
        ?>
        <div class="box-modal change-password-modal">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-error-warning-line text-3xl text-red-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Доступ запрещен</h2>
                <p class="text-gray-600 mb-4">Необходима авторизация</p>
                <button class="arcticmodal-close bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                    Закрыть
                </button>
            </div>
        </div>
        <?php
        return;
    }
    
    $user = getCurrentUser();
    $csrf_token = generateCSRFToken();
    ?>
    <div class="box-modal change-password-modal">
        <!-- Заголовок модального окна -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="ri-lock-password-line text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Смена пароля</h2>
                    <p class="text-sm text-gray-500">Измените пароль вашей учетной записи</p>
                </div>
            </div>
            <button class="arcticmodal-close text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Форма смены пароля -->
        <div class="space-y-6">
            <form method="POST" class="space-y-6" data-action="change-password" id="changePasswordForm">
                <input type="hidden" name="action" value="change-password">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- Информация о пользователе -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="ri-user-line text-blue-500 text-lg mr-2 mt-0.5"></i>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium">Аккаунт:</p>
                            <p class="text-xs mt-1"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)</p>
                        </div>
                    </div>
                </div>

                <!-- Текущий пароль -->
                <div>
                    <label for="modal_change_current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="ri-lock-line mr-1"></i>
                        Текущий пароль
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="modal_change_current_password" 
                            name="current_password" 
                            required
                            autofocus
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Введите текущий пароль"
                        >
                        <button 
                            type="button"
                            onclick="togglePassword('modal_change_current_password', 'toggle-modal-change-current-password-icon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                            title="Показать/скрыть пароль"
                        >
                            <i id="toggle-modal-change-current-password-icon" class="ri-eye-line text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Новый пароль -->
                <div>
                    <label for="modal_change_new_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="ri-lock-line mr-1"></i>
                        Новый пароль
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="modal_reset_new_password" 
                            name="new_password"
                            required
                            minlength="6"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Введите новый пароль (минимум 6 символов)"
                            oninput="if(typeof checkPasswordStrength==='function')checkPasswordStrength(this.value,true);if(typeof checkPasswordMatch==='function')checkPasswordMatch(true);"
                        >
                        <button 
                            type="button"
                            onclick="togglePassword('modal_reset_new_password', 'toggle-modal-reset-new-password-icon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                            title="Показать/скрыть пароль"
                        >
                            <i id="toggle-modal-reset-new-password-icon" class="ri-eye-line text-xl"></i>
                        </button>
                    </div>
                    <div id="modal-password-strength" class="mt-2 hidden">
                        <div class="flex items-center space-x-1">
                            <div class="flex-1 h-1 rounded" id="modal-strength-bar-1"></div>
                            <div class="flex-1 h-1 rounded" id="modal-strength-bar-2"></div>
                            <div class="flex-1 h-1 rounded" id="modal-strength-bar-3"></div>
                            <div class="flex-1 h-1 rounded" id="modal-strength-bar-4"></div>
                        </div>
                        <p id="modal-strength-text" class="text-xs mt-1"></p>
                    </div>
                </div>

                <!-- Подтверждение нового пароля -->
                <div>
                    <label for="modal_change_confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="ri-lock-line mr-1"></i>
                        Подтвердите новый пароль
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="modal_reset_confirm_password" 
                            name="confirm_password"
                            oninput="if(typeof checkPasswordMatch==='function')checkPasswordMatch(true);" 
                            required
                            minlength="6"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Повторите новый пароль"
                        >
                        <button 
                            type="button"
                            onclick="togglePassword('modal_reset_confirm_password', 'toggle-modal-reset-confirm-password-icon')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                            title="Показать/скрыть пароль"
                        >
                            <i id="toggle-modal-reset-confirm-password-icon" class="ri-eye-line text-xl"></i>
                        </button>
                    </div>
                    <p id="modal-password-match-message" class="text-xs mt-1 hidden"></p>
                </div>

                <!-- Информация -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="ri-information-line text-blue-500 text-lg mr-2 mt-0.5"></i>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium mb-1">Требования к паролю:</p>
                            <ul class="space-y-1 text-xs">
                                <li>• Минимум 6 символов</li>
                                <li>• Рекомендуется использовать буквы, цифры и символы</li>
                                <li>• Новый пароль должен отличаться от текущего</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Кнопка отправки -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-green-500 to-teal-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-teal-700 transition-all duration-300 transform hover:scale-105"
                >
                    <i class="ri-save-line mr-2"></i>
                    Изменить пароль
                </button>
            </form>
        </div>
    </div>
    <?php
}

