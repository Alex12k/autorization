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

    $users = getAllUsers();
    $csrf_token = generateCSRFToken();

    ?>
        <!-- Навигация -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="gradient-bg w-10 h-10 rounded-lg flex items-center justify-center mr-3">
                            <i class="ri-admin-line text-white text-xl"></i>
                        </div>
                        <h1 class="text-xl font-bold text-gray-900">Админ панель</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <a href="/dashboard" class="text-gray-600 hover:text-gray-900">
                            <i class="ri-dashboard-line mr-1"></i>
                            Панель управления
                        </a>
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                <i class="ri-admin-line text-white text-sm"></i>
                            </div>
                            <span class="text-gray-700 font-medium"><?= htmlspecialchars(getCurrentUser()['username']) ?></span>
                        </div>

                        <a href="#" class="open_logout bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors inline-block cursor-pointer">
                            <i class="ri-logout-box-line mr-1"></i>
                            Выйти
                        </a>
                    </div>
                </div>
            </div>
        </nav>

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
                            <div class="text-2xl font-bold text-gray-900 stat-total"><?= count($users) ?></div>
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
                            <div class="text-2xl font-bold text-gray-900 stat-admins"><?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?></div>
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
                            <div class="text-2xl font-bold text-gray-900 stat-users"><?= count(array_filter($users, fn($u) => $u['role'] === 'user')) ?></div>
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
                            <div class="text-2xl font-bold text-gray-900"><?= count(array_filter($users, fn($u) => strtotime($u['created_at']) > strtotime('-7 days'))) ?></div>
                            <div class="text-sm text-gray-600">За неделю</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Таблица пользователей -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="ri-table-line mr-2"></i>
                        Список пользователей
                    </h3>
                </div>

                <div class="overflow-x-auto">
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
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $user['id'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                            <i class="ri-user-line text-white text-sm"></i>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($user['username']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($user['email']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d.m.Y H:i', strtotime($user['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button 
                                            onclick="openEditModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username'], ENT_QUOTES) ?>', '<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>', '<?= $user['role'] ?>')" 
                                            class="text-blue-600 hover:text-blue-900" 
                                            title="Редактировать пользователя">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <?php if ($user['id'] != getCurrentUser()['id']): ?>
                                        <button 
                                            onclick="deleteUserConfirm(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username'], ENT_QUOTES) ?>')"
                                            class="text-red-600 hover:text-red-900" 
                                            title="Удалить пользователя">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

