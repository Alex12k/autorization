<?php
require_once 'auth_db.php';

// Проверка аутентификации и роли админа
if (!isAuthenticated() || !hasRole('admin')) {
    header('Location: login.php');
    exit;
}

$users = getAllUsers();
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
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
                    <a href="dashboard.php" class="text-gray-600 hover:text-gray-900">
                        <i class="ri-dashboard-line mr-1"></i>
                        Панель управления
                    </a>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <i class="ri-admin-line text-white text-sm"></i>
                        </div>
                        <span class="text-gray-700 font-medium"><?= htmlspecialchars(getCurrentUser()['username']) ?></span>
                    </div>
                    
                    <form method="POST" action="auth_db.php" class="inline">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                            <i class="ri-logout-box-line mr-1"></i>
                            Выйти
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                        <div class="text-2xl font-bold text-gray-900"><?= count($users) ?></div>
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
                        <div class="text-2xl font-bold text-gray-900"><?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?></div>
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
                        <div class="text-2xl font-bold text-gray-900"><?= count(array_filter($users, fn($u) => $u['role'] === 'user')) ?></div>
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
                                    <button class="text-blue-600 hover:text-blue-900">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <?php if ($user['id'] != getCurrentUser()['id']): ?>
                                    <button class="text-red-600 hover:text-red-900">
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

    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-300">
                <i class="ri-heart-line text-red-500"></i>
                Админ панель PHP 8.4
            </p>
        </div>
    </footer>
</body>
</html>
