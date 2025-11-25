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
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                
                <!-- Hero Section - Современный приветственный блок -->
                <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8 mb-8 text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row items-center justify-between">
                            <div class="flex items-center space-x-6 mb-6 md:mb-0">
                                <!-- Аватар пользователя -->
                                <div class="w-20 h-20 bg-white bg-opacity-20 backdrop-blur-lg rounded-full flex items-center justify-center border-4 border-white border-opacity-30 shadow-xl">
                                    <i class="ri-user-3-fill text-4xl text-white"></i>
                                </div>
                                <div>
                                    <h1 class="text-3xl md:text-4xl font-bold mb-2">
                                        Добро пожаловать, <?= htmlspecialchars($user['username']) ?>!
                                    </h1>
                                    <p class="text-blue-100 text-lg flex items-center">
                                        <i class="ri-time-line mr-2"></i>
                                        В системе: <span class="dashboard-session-time" data-login-time="<?= $login_time ?>"><?= gmdate('H:i:s', $session_duration) ?></span>
                                    </p>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-20 backdrop-blur-lg rounded-xl px-6 py-4 border border-white border-opacity-30">
                                <div class="text-sm text-blue-100 mb-1">Ваша роль</div>
                                <div class="text-2xl font-bold flex items-center">
                                    <i class="ri-shield-user-line mr-2"></i>
                                    <?= ucfirst($user['role']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Быстрые действия (Quick Actions) - Тренд 2025-2026 -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="ri-flashlight-line mr-2 text-purple-600"></i>
                        Быстрые действия
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <button type="button" class="open_modal_edit_profile_form bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:scale-105 cursor-pointer group border-2 border-transparent hover:border-blue-500">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mb-3 mx-auto group-hover:scale-110 transition-transform">
                                <i class="ri-edit-line text-2xl text-white"></i>
                            </div>
                            <div class="text-sm font-semibold text-gray-900">Профиль</div>
                            <div class="text-xs text-gray-500 mt-1">Редактировать</div>
                        </button>
                        
                        <button type="button" class="open_modal_change_password_form bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:scale-105 cursor-pointer group border-2 border-transparent hover:border-green-500">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mb-3 mx-auto group-hover:scale-110 transition-transform">
                                <i class="ri-lock-password-line text-2xl text-white"></i>
                            </div>
                            <div class="text-sm font-semibold text-gray-900">Пароль</div>
                            <div class="text-xs text-gray-500 mt-1">Изменить</div>
                        </button>
                        
                        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-transparent">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mb-3 mx-auto">
                                <i class="ri-notification-line text-2xl text-white"></i>
                            </div>
                            <div class="text-sm font-semibold text-gray-900">Уведомления</div>
                            <div class="text-xs text-gray-500 mt-1">Настройки</div>
                        </div>
                        
                        <a href="/" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:scale-105 group border-2 border-transparent hover:border-indigo-500">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mb-3 mx-auto group-hover:scale-110 transition-transform">
                                <i class="ri-home-line text-2xl text-white"></i>
                            </div>
                            <div class="text-sm font-semibold text-gray-900">Главная</div>
                            <div class="text-xs text-gray-500 mt-1">На сайт</div>
                        </a>
                    </div>
                </div>

                <!-- Визуальные метрики - Тренд 2025-2026 -->
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Метрика: ID пользователя -->
                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover border-l-4 border-blue-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="ri-user-line text-2xl text-blue-600"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-gray-900"><?= $user['id'] ?></div>
                                <div class="text-xs text-gray-500">ID пользователя</div>
                            </div>
                        </div>
                    </div>

                    <!-- Метрика: Время в системе -->
                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover border-l-4 border-green-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="ri-time-line text-2xl text-green-600"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900 dashboard-session-time" data-login-time="<?= $login_time ?>"><?= gmdate('H:i:s', $session_duration) ?></div>
                                <div class="text-xs text-gray-500">В системе</div>
                            </div>
                        </div>
                    </div>

                    <!-- Метрика: Роль -->
                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover border-l-4 border-purple-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="ri-shield-user-line text-2xl text-purple-600"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-bold text-gray-900"><?= ucfirst($user['role']) ?></div>
                                <div class="text-xs text-gray-500">Роль</div>
                            </div>
                        </div>
                    </div>

                    <!-- Метрика: Статус безопасности -->
                    <div class="bg-white rounded-xl shadow-lg p-6 card-hover border-l-4 border-emerald-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="ri-shield-check-line text-2xl text-emerald-600"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-bold text-gray-900">Активна</div>
                                <div class="text-xs text-gray-500">Сессия</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Основной контент в две колонки -->
                <div class="grid lg:grid-cols-3 gap-6 mb-8">
                    <!-- Левая колонка: Профиль и безопасность -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Карточка профиля -->
                        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                    <i class="ri-user-3-line text-2xl text-blue-600 mr-3"></i>
                                    Информация о профиле
                                </h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center">
                                        <i class="ri-id-card-line text-blue-500 mr-3"></i>
                                        <span class="text-gray-600">ID:</span>
                                    </div>
                                    <span class="font-semibold text-gray-900"><?= $user['id'] ?></span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center">
                                        <i class="ri-user-line text-blue-500 mr-3"></i>
                                        <span class="text-gray-600">Имя пользователя:</span>
                                    </div>
                                    <span class="font-semibold text-gray-900"><?= htmlspecialchars($user['username']) ?></span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center">
                                        <i class="ri-mail-line text-blue-500 mr-3"></i>
                                        <span class="text-gray-600">Email:</span>
                                    </div>
                                    <span class="font-semibold text-gray-900"><?= htmlspecialchars($user['email']) ?></span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center">
                                        <i class="ri-shield-user-line text-blue-500 mr-3"></i>
                                        <span class="text-gray-600">Роль:</span>
                                    </div>
                                    <span class="font-semibold text-blue-600"><?= ucfirst($user['role']) ?></span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center">
                                        <i class="ri-calendar-line text-blue-500 mr-3"></i>
                                        <span class="text-gray-600">Регистрация:</span>
                                    </div>
                                    <span class="font-semibold text-gray-900"><?= date('d.m.Y', strtotime($user['created_at'])) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Карточка безопасности -->
                        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="ri-shield-check-line text-2xl text-green-600 mr-3"></i>
                                Безопасность аккаунта
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex items-center">
                                        <i class="ri-checkbox-circle-line text-green-600 text-xl mr-3"></i>
                                        <span class="text-gray-700">Сессия активна</span>
                                    </div>
                                    <span class="text-green-600 font-semibold">✓</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex items-center">
                                        <i class="ri-checkbox-circle-line text-green-600 text-xl mr-3"></i>
                                        <span class="text-gray-700">CSRF защита</span>
                                    </div>
                                    <span class="text-green-600 font-semibold">✓</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex items-center">
                                        <i class="ri-checkbox-circle-line text-green-600 text-xl mr-3"></i>
                                        <span class="text-gray-700">Пароль хеширован</span>
                                    </div>
                                    <span class="text-green-600 font-semibold">✓</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                                    <div class="flex items-center">
                                        <i class="ri-checkbox-circle-line text-green-600 text-xl mr-3"></i>
                                        <span class="text-gray-700">Валидация данных</span>
                                    </div>
                                    <span class="text-green-600 font-semibold">✓</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Правая колонка: Дополнительные инструменты -->
                    <div class="space-y-6">
                        <!-- Демонстрации -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <i class="ri-flask-line text-xl text-indigo-600 mr-2"></i>
                                Демонстрации
                            </h3>
                            <div class="space-y-3">
                                <button type="button" class="open_modal_database_demo w-full bg-gradient-to-r from-indigo-500 to-indigo-600 text-white py-3 px-4 rounded-lg hover:from-indigo-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-md cursor-pointer">
                                    <i class="ri-database-2-line mr-2"></i>
                                    PostgreSQL
                                </button>
                                <button type="button" class="open_modal_php_demo w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 px-4 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300 transform hover:scale-105 shadow-md cursor-pointer">
                                    <i class="ri-code-s-slash-line mr-2"></i>
                                    PHP 8.4
                                </button>
                            </div>
                        </div>

                        <?php if (hasRole('admin')): ?>
                        <!-- Админ панель -->
                        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-red-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <i class="ri-admin-line text-xl text-red-600 mr-2"></i>
                                Администрирование
                            </h3>
                            <div class="space-y-3">
                                <a href="/admin" class="block w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-3 px-4 rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105 shadow-md text-center">
                                    <i class="ri-user-settings-line mr-2"></i>
                                    Управление пользователями
                                </a>
                                <button type="button" class="open_modal_check_session w-full bg-gradient-to-r from-yellow-500 to-yellow-600 text-white py-3 px-4 rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-300 transform hover:scale-105 shadow-md cursor-pointer">
                                    <i class="ri-bug-line mr-2"></i>
                                    Диагностика сессии
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Статистика сессии -->
                        <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl shadow-lg p-6 text-white">
                            <h3 class="text-lg font-bold mb-4 flex items-center">
                                <i class="ri-bar-chart-line text-xl mr-2"></i>
                                Статистика
                            </h3>
                            <div class="space-y-3">
                                <div class="bg-white bg-opacity-20 backdrop-blur-lg rounded-lg p-3 border border-white border-opacity-30">
                                    <div class="text-xs text-purple-100 mb-1">Время в системе</div>
                                    <div class="text-2xl font-bold dashboard-session-time" data-login-time="<?= $login_time ?>"><?= gmdate('H:i:s', $session_duration) ?></div>
                                </div>
                                <div class="bg-white bg-opacity-20 backdrop-blur-lg rounded-lg p-3 border border-white border-opacity-30">
                                    <div class="text-xs text-purple-100 mb-1">Текущее время</div>
                                    <div class="text-2xl font-bold dashboard-current-time"><?= date('H:i:s') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
}

