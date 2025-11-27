<?php
/**
 * Dev-инструмент: Генерация тестовых пользователей
 * Создает настраиваемое количество тестовых пользователей в базе данных
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
 * Функция отображения формы генерации пользователей в модальном окне
 */
function modal_seed_users(): void
{
    // Проверка прав доступа (только для админов)
    if (!isAuthenticated() || !hasRole('admin')) {
        ?>
        <div class="box-modal seed-users-modal">
            <!-- Заголовок модального окна -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                        <i class="ri-error-warning-line text-2xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Доступ запрещен</h2>
                        <p class="text-sm text-gray-500">Требуются права администратора</p>
                    </div>
                </div>
                <button class="arcticmodal-close text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>
            <div class="p-6 text-center">
                <p class="text-red-600">Доступ запрещен. Требуются права администратора.</p>
            </div>
        </div>
        <?php
        return;
    }
    
    ?>
    <div class="box-modal seed-users-modal !w-[95%] lg:!w-[800px] !max-w-[95%] lg:!max-w-[800px]">
        <!-- Заголовок модального окна -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="ri-user-add-line text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Генерация тестовых пользователей</h2>
                    <p class="text-sm text-gray-500">Создание настраиваемого количества пользователей</p>
                </div>
            </div>
            <button class="arcticmodal-close text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <!-- Содержимое -->
        <div class="space-y-6">
            <!-- Информация -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="ri-information-line text-blue-600 text-xl mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Внимание!</p>
                        <p>Этот инструмент создаст тестовых пользователей в базе данных. Все пользователи будут иметь пароль "password123".</p>
                    </div>
                </div>
            </div>

            <!-- Форма выбора количества -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <form id="seed-users-form">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="ri-group-line mr-2"></i>
                            Выберите количество пользователей:
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="seed-amount-option cursor-pointer">
                                <input type="radio" name="amount" value="10" class="hidden peer" checked>
                                <div class="p-4 border-2 border-gray-200 rounded-lg text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
                                    <div class="text-2xl font-bold text-gray-900">10</div>
                                    <div class="text-xs text-gray-500 mt-1">Быстро</div>
                                </div>
                            </label>
                            <label class="seed-amount-option cursor-pointer">
                                <input type="radio" name="amount" value="100" class="hidden peer">
                                <div class="p-4 border-2 border-gray-200 rounded-lg text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
                                    <div class="text-2xl font-bold text-gray-900">100</div>
                                    <div class="text-xs text-gray-500 mt-1">Средне</div>
                                </div>
                            </label>
                            <label class="seed-amount-option cursor-pointer">
                                <input type="radio" name="amount" value="1000" class="hidden peer">
                                <div class="p-4 border-2 border-gray-200 rounded-lg text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
                                    <div class="text-2xl font-bold text-gray-900">1,000</div>
                                    <div class="text-xs text-gray-500 mt-1">Много</div>
                                </div>
                            </label>
                            <label class="seed-amount-option cursor-pointer">
                                <input type="radio" name="amount" value="10000" class="hidden peer">
                                <div class="p-4 border-2 border-gray-200 rounded-lg text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
                                    <div class="text-2xl font-bold text-gray-900">10,000</div>
                                    <div class="text-xs text-gray-500 mt-1">Очень много</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Прогресс-бар (скрыт по умолчанию) -->
                    <div id="seed-progress-container" class="hidden mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-700">Прогресс:</span>
                            <span id="seed-progress-text" class="text-sm font-semibold text-gray-700">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div id="seed-progress-bar" class="bg-gradient-to-r from-green-500 to-teal-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <div id="seed-status-text" class="text-sm text-gray-600 mt-2 text-center"></div>
                    </div>

                    <!-- Кнопки -->
                    <div class="flex space-x-3">
                        <button type="submit" id="seed-submit-btn" class="flex-1 bg-gradient-to-r from-green-500 to-teal-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-600 hover:to-teal-700 transition-all duration-300 transform hover:scale-105 shadow-md">
                            <i class="ri-play-line mr-2"></i>
                            Создать пользователей
                        </button>
                        <button type="button" id="seed-cancel-btn" class="hidden bg-gray-500 text-white py-3 px-6 rounded-lg font-semibold hover:bg-gray-600 transition-all duration-300">
                            <i class="ri-close-line mr-2"></i>
                            Отмена
                        </button>
                    </div>
                </form>
            </div>

            <!-- Результат (скрыт по умолчанию) -->
            <div id="seed-result" class="hidden"></div>
        </div>
    </div>
    <?php
}

