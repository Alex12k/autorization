<?php
/**
 * Шаблон компонента edit-profile
 * Форма редактирования профиля для авторизованных пользователей
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
 * Функция отображения формы редактирования профиля в модальном окне
 */
function modal_edit_profile_form(): void
{
    // Проверка аутентификации
    if (!isAuthenticated()) {
        ?>
        <div class="box-modal edit-profile-modal">
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
    <div class="box-modal edit-profile-modal">
        <!-- Заголовок модального окна -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="ri-edit-line text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Редактировать профиль</h2>
                    <p class="text-sm text-gray-500">Измените данные вашей учетной записи</p>
                </div>
            </div>
            <button class="arcticmodal-close text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Форма редактирования профиля -->
        <div class="space-y-6">
            <form method="POST" class="space-y-6" data-action="edit-profile" id="editProfileForm">
                <input type="hidden" name="action" value="edit-profile">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- Имя пользователя -->
                <div>
                    <label for="modal_edit_username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="ri-user-line mr-1"></i>
                        Имя пользователя
                    </label>
                    <input
                        type="text"
                        id="modal_edit_username"
                        name="username"
                        required
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        placeholder="Введите имя пользователя"
                        value="<?= htmlspecialchars($user['username']) ?>"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label for="modal_edit_email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="ri-mail-line mr-1"></i>
                        Email адрес
                    </label>
                    <input
                        type="email"
                        id="modal_edit_email"
                        name="email"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        placeholder="Введите email"
                        value="<?= htmlspecialchars($user['email']) ?>"
                    >
                </div>

                <!-- Информация -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="ri-information-line text-blue-500 text-lg mr-2 mt-0.5"></i>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium mb-1">Важно:</p>
                            <ul class="space-y-1 text-xs">
                                <li>• Имя пользователя и email должны быть уникальными</li>
                                <li>• Email должен быть корректным</li>
                                <li>• После изменения данные обновятся в вашей сессии</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Кнопка отправки -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105"
                >
                    <i class="ri-save-line mr-2"></i>
                    Сохранить изменения
                </button>
            </form>
        </div>
    </div>
    <?php
}

