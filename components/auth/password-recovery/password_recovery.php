<?php
/**
 * Шаблон компонента password-recovery
 * Объединенные формы для восстановления пароля (запрос и сброс)
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

// Загрузка функций компонента password-recovery
require_once __DIR__ . '/functions.php';

/**
 * Функция отображения формы запроса восстановления пароля в модальном окне
 */
function modal_forgot_password_form(): void
{
    // Генерация CSRF токена для формы
    $csrf_token = generateCSRFToken();
    ?>
    <div class="box-modal forgot-password-modal">
        <!-- Заголовок модального окна -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="ri-lock-password-line text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Восстановление пароля</h2>
                    <p class="text-sm text-gray-500">Введите email адрес вашей учетной записи</p>
                </div>
            </div>
            <button class="arcticmodal-close text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Форма восстановления пароля -->
        <div class="space-y-6">
            <form method="POST" class="space-y-6" data-action="forgot-password">
                <input type="hidden" name="action" value="forgot-password">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- Email -->
                <div>
                    <label for="modal_forgot_email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="ri-mail-line mr-1"></i>
                        Email адрес
                    </label>
                    <input 
                        type="email" 
                        id="modal_forgot_email" 
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
                    <a href="#" class="open_modal_login_form text-blue-600 hover:text-blue-700 font-semibold cursor-pointer">
                        Войти
                    </a>
                </p>
                <p class="text-gray-600">
                    Нет учетной записи? 
                    <a href="#" class="open_modal_register_form text-blue-600 hover:text-blue-700 font-semibold cursor-pointer">
                        Зарегистрироваться
                    </a>
                </p>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Функция отображения формы сброса пароля в модальном окне
 */
function modal_reset_password_form(?string $token = null): void
{
    // Получаем токен из параметра или POST
    $token = $token ?? $_POST['token'] ?? '';
    $reset_error = null;
    $token_validation = null;

    // Проверка токена
    if (!empty($token)) {
        $token_validation = validateResetToken($token);
        if (!$token_validation['success']) {
            $reset_error = $token_validation['error'];
            $token = '';
        }
    } else {
        $reset_error = 'Токен восстановления не указан. Пожалуйста, используйте ссылку из письма.';
    }

    // Генерация CSRF токена для формы
    $csrf_token = generateCSRFToken();
    
    ?>
    <div class="box-modal reset-password-modal">
        <!-- Заголовок модального окна -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="ri-shield-keyhole-line text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Сброс пароля</h2>
                    <?php if ($token_validation && $token_validation['success']): ?>
                        <p class="text-sm text-gray-500">Установите новый пароль для <?= htmlspecialchars($token_validation['username']) ?></p>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">Установите новый пароль</p>
                    <?php endif; ?>
                </div>
            </div>
            <button class="arcticmodal-close text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Форма сброса пароля -->
        <div class="space-y-6">
            <?php if ($token && $token_validation && $token_validation['success']): ?>
                <form method="POST" class="space-y-6" data-action="reset-password" id="resetPasswordForm">
                    <input type="hidden" name="action" value="reset-password">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <!-- Информация о пользователе -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="ri-user-line text-blue-500 text-lg mr-2 mt-0.5"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium">Аккаунт:</p>
                                <p class="text-xs mt-1"><?= htmlspecialchars($token_validation['email']) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Новый пароль -->
                    <div>
                        <label for="modal_reset_new_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="ri-lock-line mr-1"></i>
                            Новый пароль
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="modal_reset_new_password" 
                                name="new_password" 
                                required
                                autofocus
                                minlength="6"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="Введите новый пароль (минимум 6 символов)"
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

                    <!-- Подтверждение пароля -->
                    <div>
                        <label for="modal_reset_confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="ri-lock-password-line mr-1"></i>
                            Подтвердите пароль
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="modal_reset_confirm_password" 
                                name="confirm_password" 
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

                    <!-- Требования к паролю -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-yellow-800 mb-2">
                            <i class="ri-information-line mr-1"></i>
                            Требования к паролю:
                        </h3>
                        <ul class="text-xs text-yellow-700 space-y-1">
                            <li id="modal-req-length" class="flex items-center">
                                <i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>
                                Минимум 6 символов
                            </li>
                            <li id="modal-req-match" class="flex items-center">
                                <i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>
                                Пароли должны совпадать
                            </li>
                        </ul>
                    </div>

                    <!-- Кнопка сброса -->
                    <button 
                        type="submit"
                        id="modal-submitBtn"
                        class="w-full bg-gradient-to-r from-green-500 to-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <i class="ri-key-line mr-2"></i>
                        Установить новый пароль
                    </button>
                </form>
            <?php else: ?>
                <!-- Ошибка токена -->
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-error-warning-line text-4xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Ошибка токена</h3>
                    <p class="text-gray-600 mb-4"><?= htmlspecialchars($reset_error) ?></p>
                </div>

                <div class="space-y-3">
                    <a href="#" class="open_modal_forgot_password_form block w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 text-center cursor-pointer">
                        <i class="ri-mail-send-line mr-2"></i>
                        Запросить новую ссылку
                    </a>
                    <a href="#" class="open_modal_login_form block w-full bg-gray-300 text-gray-700 py-3 px-4 rounded-lg font-semibold hover:bg-gray-400 transition-colors text-center cursor-pointer">
                        <i class="ri-arrow-left-line mr-2"></i>
                        Вернуться к входу
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

