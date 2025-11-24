<?php
/**
 * Компонент dashboard
 * Панель управления для авторизованных пользователей
 */

function dashboard(): void
{
    // Проверка аутентификации
    if (!isAuthenticated()) {
        redirect('login');
        exit;
    }

    $user = getCurrentUser();
    $login_time = $_SESSION['login_time'] ?? time();
    $session_duration = time() - $login_time;

    // Подключаем header
    if (!function_exists('renderHeader')) {
        require_once __DIR__ . '/../../header.php';
    }
    renderHeader('Панель управления', 'ri-dashboard-line');

    ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Приветствие -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">
                            Добро пожаловать, <?= htmlspecialchars($user['username']) ?>!
                        </h2>
                        <p class="text-gray-600">
                            <i class="ri-time-line mr-1"></i>
                            Время в системе: <?= gmdate('H:i:s', $session_duration) ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Роль</div>
                        <div class="text-lg font-semibold text-blue-600">
                            <?= ucfirst($user['role']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Информация о пользователе -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <i class="ri-user-line text-3xl text-blue-500 mr-3"></i>
                        <h3 class="text-xl font-semibold">Профиль</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID:</span>
                            <span class="font-semibold"><?= $user['id'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Имя пользователя:</span>
                            <span class="font-semibold"><?= htmlspecialchars($user['username']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-semibold"><?= htmlspecialchars($user['email']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Роль:</span>
                            <span class="font-semibold text-blue-600"><?= ucfirst($user['role']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <i class="ri-shield-check-line text-3xl text-green-500 mr-3"></i>
                        <h3 class="text-xl font-semibold">Безопасность</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="ri-check-line text-green-500 mr-2"></i>
                            <span>Сессия активна</span>
                        </div>
                        <div class="flex items-center">
                            <i class="ri-check-line text-green-500 mr-2"></i>
                            <span>CSRF защита</span>
                        </div>
                        <div class="flex items-center">
                            <i class="ri-check-line text-green-500 mr-2"></i>
                            <span>Пароль хеширован</span>
                        </div>
                        <div class="flex items-center">
                            <i class="ri-check-line text-green-500 mr-2"></i>
                            <span>Валидация данных</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <i class="ri-settings-3-line text-3xl text-purple-500 mr-3"></i>
                        <h3 class="text-xl font-semibold">Настройки</h3>
                    </div>
                    <div class="space-y-3">
                        <button type="button" class="open_modal_edit_profile_form w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors cursor-pointer">
                            <i class="ri-edit-line mr-1"></i>
                            Редактировать профиль
                        </button>
                        <button type="button" class="open_modal_change_password_form w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 transition-colors cursor-pointer">
                            <i class="ri-lock-password-line mr-1"></i>
                            Сменить пароль
                        </button>
                        <button class="w-full bg-purple-500 text-white py-2 px-4 rounded hover:bg-purple-600 transition-colors">
                            <i class="ri-notification-line mr-1"></i>
                            Уведомления
                        </button>
                    </div>
                </div>
            </div>

            <!-- Дополнительные возможности -->
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <i class="ri-database-2-line text-3xl text-indigo-500 mr-3"></i>
                        <h3 class="text-xl font-semibold">База данных</h3>
                    </div>
                    <div class="space-y-3">
                        <button type="button" class="open_modal_database_demo w-full bg-indigo-500 text-white py-2 px-4 rounded hover:bg-indigo-600 transition-colors cursor-pointer">
                            <i class="ri-database-2-line mr-1"></i>
                            Демонстрация PostgreSQL
                        </button>
                        <div class="text-sm text-gray-600 mt-2">
                            Изучите возможности работы с базой данных PostgreSQL
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <i class="ri-code-s-slash-line text-3xl text-orange-500 mr-3"></i>
                        <h3 class="text-xl font-semibold">PHP 8.4</h3>
                    </div>
                    <div class="space-y-3">
                        <button type="button" class="open_modal_php_demo w-full bg-orange-500 text-white py-2 px-4 rounded hover:bg-orange-600 transition-colors cursor-pointer">
                            <i class="ri-code-s-slash-line mr-1"></i>
                            Демонстрация PHP
                        </button>
                        <div class="text-sm text-gray-600 mt-2">
                            Изучите новые возможности PHP 8.4
                        </div>
                    </div>
                </div>

                <?php if (hasRole('admin')): ?>
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <i class="ri-admin-line text-3xl text-red-500 mr-3"></i>
                        <h3 class="text-xl font-semibold">Админ панель</h3>
                    </div>
                    <div class="space-y-3">
                        <a href="/admin" class="block bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 transition-colors text-center">
                            <i class="ri-admin-line mr-1"></i>
                            Управление пользователями
                        </a>
                        <div class="text-sm text-gray-600 mt-2">
                            Просмотр и управление всеми пользователями
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center mb-4">
                        <i class="ri-bug-line text-3xl text-yellow-500 mr-3"></i>
                        <h3 class="text-xl font-semibold">Dev-инструменты</h3>
                    </div>
                    <div class="space-y-3">
                        <button type="button" class="open_modal_check_session w-full bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600 transition-colors cursor-pointer">
                            <i class="ri-bug-line mr-1"></i>
                            Диагностика сессии
                        </button>
                        <div class="text-sm text-gray-600 mt-2">
                            Просмотр данных сессии и отладочная информация
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Статистика сессии -->
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold mb-4">
                    <i class="ri-bar-chart-line mr-2"></i>
                    Статистика сессии
                </h3>
                <div class="grid md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600"><?= $user['id'] ?></div>
                        <div class="text-sm text-gray-600">ID пользователя</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600"><?= gmdate('H:i:s', $session_duration) ?></div>
                        <div class="text-sm text-gray-600">Время в системе</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600"><?= ucfirst($user['role']) ?></div>
                        <div class="text-sm text-gray-600">Роль</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600"><?= date('H:i:s') ?></div>
                        <div class="text-sm text-gray-600">Текущее время</div>
                    </div>
                </div>
            </div>
        </div>
    <?php
}

