<?php
/**
 * Dev-инструмент: Диагностика сессии
 * Показывает информацию о текущей сессии и пользователе
 * Доступен только администраторам
 */

// Загрузка зависимостей (если файл вызывается напрямую)
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/../../functions.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    define('SYSTEM_INITIALIZED', true);
}

/**
 * Функция отображения диагностики сессии в модальном окне
 */
function modal_check_session(): void
{
    // Проверка аутентификации и роли админа
    if (!isAuthenticated() || !hasRole('admin')) {
        ?>
        <div class="box-modal check-session-modal !w-[95%] lg:!w-[1280px] !max-w-[95%] lg:!max-w-[1280px]">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-error-warning-line text-3xl text-red-600"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Доступ запрещен</h2>
                <p class="text-gray-600 mb-4">Этот инструмент доступен только администраторам</p>
                <button class="arcticmodal-close bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                    Закрыть
                </button>
            </div>
        </div>
        <?php
        return;
    }
    
    $user = getCurrentUser();
    ?>
    <div class="box-modal check-session-modal !w-[95%] lg:!w-[1280px] !max-w-[95%] lg:!max-w-[1280px]">
        <!-- Заголовок модального окна -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="ri-bug-line text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Диагностика сессии</h2>
                    <p class="text-sm text-gray-500">Dev-инструмент для отладки</p>
                </div>
            </div>
            <button class="arcticmodal-close text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Содержимое -->
        <div class="space-y-6 max-h-[70vh] overflow-y-auto">
            <!-- Статус аутентификации -->
            <div class="p-4 <?= isAuthenticated() ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' ?> border rounded-lg">
                <h3 class="text-lg font-semibold mb-2">Статус аутентификации</h3>
                <?php if (isAuthenticated()): ?>
                    <p class="text-green-700">
                        <i class="ri-check-circle-fill mr-2"></i>
                        <strong>Вы авторизованы</strong>
                    </p>
                <?php else: ?>
                    <p class="text-gray-700">
                        <i class="ri-close-circle-line mr-2"></i>
                        <strong>Вы НЕ авторизованы</strong>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Текущий пользователь -->
            <?php if ($user): ?>
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Текущий пользователь</h3>
                    <ul class="space-y-2 text-sm">
                        <li><strong>ID:</strong> <?= htmlspecialchars($user['id']) ?></li>
                        <li><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></li>
                        <li><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
                        <li><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></li>
                        <li><strong>Создан:</strong> <?= htmlspecialchars($user['created_at']) ?></li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Данные сессии -->
            <div>
                <h3 class="text-lg font-semibold mb-3">Данные сессии</h3>
                <div class="bg-gray-50 p-4 rounded-lg overflow-x-auto border border-gray-200">
                    <pre class="text-xs"><?php print_r($_SESSION); ?></pre>
                </div>
            </div>

            <!-- Информация о доступе -->
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h3 class="font-semibold text-yellow-800 mb-2">
                    <i class="ri-shield-check-line mr-2"></i>
                    Информация о доступе
                </h3>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Этот инструмент доступен только администраторам</li>
                    <li>• Показывает чувствительные данные сессии - используйте осторожно!</li>
                    <li>• Предназначен для отладки и диагностики</li>
                </ul>
            </div>

            <!-- Информация об очистке сессии -->
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="font-semibold text-blue-800 mb-2">
                    <i class="ri-information-line mr-2"></i>
                    Что будет очищено при нажатии "Очистить сессию":
                </h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>✅ Очищены все данные сессии PHP</li>
                    <li>✅ Удалены все cookies сессии</li>
                    <li>✅ Удалены все cookies домена</li>
                    <li>✅ Сессия уничтожена и пересоздана</li>
                    <li>⚠️ После очистки вы будете автоматически разлогинены</li>
                </ul>
            </div>

            <!-- Действия -->
            <div class="flex gap-4 pt-4 border-t border-gray-200">
                <button type="button" class="clear_session_btn flex-1 bg-red-500 text-white py-3 px-4 rounded-lg hover:bg-red-600 transition-colors font-semibold cursor-pointer">
                    <i class="ri-delete-bin-line mr-2"></i>
                    Очистить сессию
                </button>
                <button type="button" class="refresh_session_btn flex-1 bg-blue-500 text-white py-3 px-4 rounded-lg hover:bg-blue-600 transition-colors font-semibold cursor-pointer">
                    <i class="ri-refresh-line mr-2"></i>
                    Обновить данные
                </button>
            </div>
        </div>
    </div>
    <?php
}

