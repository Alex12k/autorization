<?php
/**
 * Компонент админ панели
 * Управление пользователями системы
 */

// Загрузка функций компонента admin
require_once __DIR__ . '/functions.php';

function admin(): void
{
    // Проверка аутентификации и роли админа
    if (!isAuthenticated() || !hasRole('admin')) {
        redirect('login');
        exit;
    }

    $stats = getUsersStats();
    $csrf_token = generateCSRFToken();

    // Подключаем header
    if (!function_exists('renderHeader')) {
        require_once __DIR__ . '/../../header.php';
    }
    renderHeader('Админ панель', 'ri-admin-line');

    ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Уведомления -->
            <?php if (isset($delete_success)): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="ri-check-line text-green-500 text-xl mr-2"></i>
                        <span class="text-green-700 font-medium"><?= htmlspecialchars($delete_success) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($delete_error)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="ri-error-warning-line text-red-500 text-xl mr-2"></i>
                        <span class="text-red-700 font-medium"><?= htmlspecialchars($delete_error) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($update_success)): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="ri-check-line text-green-500 text-xl mr-2"></i>
                        <span class="text-green-700 font-medium"><?= htmlspecialchars($update_success) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($update_error)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="ri-error-warning-line text-red-500 text-xl mr-2"></i>
                        <span class="text-red-700 font-medium"><?= htmlspecialchars($update_error) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Заголовок -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="ri-user-settings-line mr-2"></i>
                    Управление пользователями
                </h2>
                <p class="text-gray-600">Просмотр и управление всеми пользователями системы</p>
            </div>

            <!-- Статистика -->
            <div class="grid md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="ri-user-line text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 stat-total"><?= $stats['total'] ?></div>
                            <div class="text-sm text-gray-600">Всего пользователей</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="ri-admin-line text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 stat-admins"><?= $stats['admins'] ?></div>
                            <div class="text-sm text-gray-600">Администраторов</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="ri-user-line text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 stat-users"><?= $stats['users'] ?></div>
                            <div class="text-sm text-gray-600">Обычных пользователей</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="ri-calendar-line text-white text-xl"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900"><?= $stats['last_week'] ?></div>
                            <div class="text-sm text-gray-600">За неделю</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблица пользователей -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="ri-table-line mr-2"></i>
                            Список пользователей
                        </h3>
                        <p class="text-sm text-gray-500">Фильтры, поиск и умная подгрузка при прокрутке</p>
                    </div>
                    <!-- Фильтры -->
                    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto" id="admin-filters">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="ri-search-line"></i>
                            </span>
                            <input 
                                type="text" 
                                id="filter_search" 
                                class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                placeholder="Поиск по имени или email">
                        </div>
                        <select 
                            id="filter_role" 
                            class="sm:w-40 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Все роли</option>
                            <option value="user">Только пользователи</option>
                            <option value="admin">Только админы</option>
                        </select>
                        <select 
                            id="filter_sort" 
                            class="sm:w-44 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="created_desc">Новые сначала</option>
                            <option value="created_asc">Старые сначала</option>
                            <option value="username_asc">Имя A→Z</option>
                            <option value="username_desc">Имя Z→A</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[600px]" id="users-table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Пользователь
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Роль
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Дата регистрации
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Действия
                                </th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body" class="bg-white divide-y divide-gray-200">
                            <!-- Строки пользователей будут подгружаться лениво через JS -->
                        </tbody>
                    </table>
                    <!-- Индикатор загрузки / конца списка -->
                    <div id="users-loading-indicator" class="py-3 text-center text-sm text-gray-500 hidden">
                        <i class="ri-loader-4-line animate-spin mr-1"></i>
                        Загрузка пользователей...
                    </div>
                    <div id="users-end-indicator" class="py-3 text-center text-sm text-gray-400 hidden">
                        <i class="ri-check-line mr-1"></i>
                        Все пользователи загружены
                    </div>
                </div>
            </div>

            <!-- Информация о системе -->
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="ri-information-line mr-2"></i>
                    Информация о системе
                </h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">База данных</h4>
                        <div class="space-y-1 text-sm text-gray-600">
                            <div>Тип: <?= USE_POSTGRESQL ? 'PostgreSQL' : 'SQLite' ?></div>
                            <div>Путь: <?= USE_POSTGRESQL ? DB_HOST . ':' . DB_PORT . '/' . DB_NAME : SQLITE_PATH ?></div>
                            <div>Пользователь: <?= USE_POSTGRESQL ? DB_USER : 'N/A' ?></div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">PHP</h4>
                        <div class="space-y-1 text-sm text-gray-600">
                            <div>Версия: <?= PHP_VERSION ?></div>
                            <div>Расширения: <?= extension_loaded('pdo') ? 'PDO ✓' : 'PDO ✗' ?> <?= extension_loaded('pdo_sqlite') ? 'PDO_SQLite ✓' : 'PDO_SQLite ✗' ?></div>
                            <div>Сессия: <?= session_status() === PHP_SESSION_ACTIVE ? 'Активна ✓' : 'Неактивна ✗' ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Данные для JavaScript (скрытый элемент) -->
        <div id="admin-data" 
             data-user-id="<?= getCurrentUser()['id'] ?>" 
             data-user-role="<?= htmlspecialchars(getCurrentUser()['role']) ?>" 
             data-csrf-token="<?= htmlspecialchars($csrf_token) ?>"
             style="display: none;"></div>

        <!-- Toast уведомления -->
        <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

        <!-- Модальное окно редактирования пользователя -->
        <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Заголовок -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="ri-edit-line mr-2"></i>
                            Редактировать пользователя
                        </h3>
                        <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="ri-close-line text-2xl"></i>
                        </button>
                    </div>

                    <!-- Форма редактирования -->
                    <form id="editUserForm" class="space-y-4" onsubmit="return false;">
                        <input type="hidden" id="edit_user_id">
                        <input type="hidden" id="edit_csrf_token" value="<?= $csrf_token ?>">

                        <!-- Предупреждение для своего профиля -->
                        <div id="self_edit_warning" class="hidden p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="ri-alert-line text-yellow-600 text-lg mr-2 mt-0.5"></i>
                                <div class="text-sm text-yellow-800">
                                    <strong>Внимание!</strong> Вы редактируете свой собственный профиль. 
                                    Изменение роли с Admin на User будет заблокировано.
                                </div>
                            </div>
                        </div>

                        <!-- Имя пользователя -->
                        <div>
                            <label for="edit_username" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="ri-user-line mr-1"></i>
                                Имя пользователя
                            </label>
                            <input 
                                type="text" 
                                id="edit_username" 
                                name="username" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Введите имя пользователя"
                            >
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="ri-mail-line mr-1"></i>
                                Email адрес
                            </label>
                            <input 
                                type="email" 
                                id="edit_email" 
                                name="email" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Введите email"
                            >
                        </div>

                        <!-- Роль -->
                        <div>
                            <label for="edit_role" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="ri-shield-user-line mr-1"></i>
                                Роль
                            </label>
                            <select 
                                id="edit_role" 
                                name="role" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="user">User (Пользователь)</option>
                                <option value="admin">Admin (Администратор)</option>
                            </select>
                        </div>

                        <!-- Кнопки -->
                        <div class="flex space-x-3 pt-4">
                            <button 
                                type="button"
                                onclick="saveUser()"
                                id="saveUserBtn"
                                class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <i class="ri-save-line mr-1"></i>
                                Сохранить
                            </button>
                            <button 
                                type="button"
                                onclick="closeEditModal()"
                                class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition-colors font-semibold"
                            >
                                <i class="ri-close-line mr-1"></i>
                                Отмена
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php
}

