<?php
/**
 * Шаблон страницы admin
 */

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
                        <a href="<?= url('dashboard') ?>" class="text-gray-600 hover:text-gray-900">
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

        <script>
            const currentUserId = <?= getCurrentUser()['id'] ?>;
            const currentUserRole = '<?= getCurrentUser()['role'] ?>';
            const csrfToken = '<?= $csrf_token ?>';
            const apiUrl = '<?= url('api') ?>';

            // Toast уведомления
            function showToast(message, type = 'success') {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');

                const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
                const icon = type === 'success' ? 'ri-check-line' : 'ri-error-warning-line';

                toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 transform transition-all duration-300 opacity-0 translate-x-full`;
                toast.innerHTML = `
                    <i class="${icon} text-2xl"></i>
                    <span class="font-medium">${message}</span>
                `;

                container.appendChild(toast);

                // Анимация появления
                setTimeout(() => {
                    toast.classList.remove('opacity-0', 'translate-x-full');
                }, 10);

                // Удаление через 3 секунды
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-x-full');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // Обновление строки пользователя в таблице
            function updateUserRow(user) {
                const row = document.querySelector(`tr[data-user-id="${user.id}"]`);
                if (row) {
                    const roleClass = user.role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';

                    // Обновляем текстовое содержимое
                    // Используем более точный селектор для username (второй div, не иконка)
                    const usernameCell = row.querySelectorAll('td')[1]; // вторая ячейка
                    const usernameDiv = usernameCell.querySelector('div.flex > div.text-sm'); // второй div после иконки
                    if (usernameDiv) {
                        usernameDiv.textContent = user.username;
                    }

                    row.querySelector('.user-email').textContent = user.email;
                    row.querySelector('.user-role').className = `px-2 py-1 text-xs font-semibold rounded-full ${roleClass} user-role`;
                    row.querySelector('.user-role').textContent = user.role.charAt(0).toUpperCase() + user.role.slice(1);

                    // ВАЖНО: Обновляем data-атрибуты для кнопки редактирования
                    const editButton = row.querySelector('button[onclick*="openEditModal"]');
                    if (editButton) {
                        // Экранируем кавычки в данных
                        const escapedUsername = user.username.replace(/'/g, "\'");
                        const escapedEmail = user.email.replace(/'/g, "\'");
                        editButton.setAttribute('onclick', 
                            `openEditModal(${user.id}, '${escapedUsername}', '${escapedEmail}', '${user.role}')`
                        );
                    }

                    // Добавляем визуальную обратную связь (мигание)
                    row.style.transition = 'background-color 0.3s ease';
                    row.style.backgroundColor = '#d1fae5';
                    setTimeout(() => {
                        row.style.backgroundColor = '';
                    }, 500);
                }
            }

            // Удаление строки пользователя из таблицы
            function removeUserRow(userId) {
                const row = document.querySelector(`tr[data-user-id="${userId}"]`);
                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';
                    setTimeout(() => {
                        row.remove();
                        updateStatistics();
                    }, 300);
                }
            }

            // Обновление статистики
            function updateStatistics() {
                const rows = document.querySelectorAll('tbody tr');
                const totalUsers = rows.length;
                const adminUsers = Array.from(rows).filter(row => 
                    row.querySelector('.user-role')?.textContent.toLowerCase() === 'admin'
                ).length;
                const regularUsers = totalUsers - adminUsers;

                // Обновляем счетчики с анимацией
                animateCounter(document.querySelector('.stat-total'), totalUsers);
                animateCounter(document.querySelector('.stat-admins'), adminUsers);
                animateCounter(document.querySelector('.stat-users'), regularUsers);
            }

            // Анимация изменения числа
            function animateCounter(element, newValue) {
                if (!element) return;

                const currentValue = parseInt(element.textContent) || 0;
                if (currentValue === newValue) return;

                element.style.transition = 'transform 0.3s ease';
                element.style.transform = 'scale(1.2)';
                element.textContent = newValue;

                setTimeout(() => {
                    element.style.transform = 'scale(1)';
                }, 150);
            }

            // Открытие модального окна редактирования
            function openEditModal(userId, username, email, role) {
                document.getElementById('edit_user_id').value = userId;
                document.getElementById('edit_username').value = username;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_role').value = role;

                const isSelfEdit = (userId === currentUserId);
                const isAdmin = (currentUserRole === 'admin');
                const warningElement = document.getElementById('self_edit_warning');
                const roleSelect = document.getElementById('edit_role');

                if (isSelfEdit && isAdmin) {
                    warningElement.classList.remove('hidden');
                    roleSelect.addEventListener('change', function(e) {
                        if (e.target.value === 'user') {
                            if (!confirm('⚠️ ВНИМАНИЕ!\n\nВы пытаетесь понизить свою роль администратора.\n\nЭто действие будет заблокировано системой для защиты от потери административного доступа.\n\nЕсли вы действительно хотите перестать быть администратором, попросите другого администратора изменить вашу роль.')) {
                                e.target.value = 'admin';
                            }
                        }
                    });
                } else {
                    warningElement.classList.add('hidden');
                }

                document.getElementById('editModal').classList.remove('hidden');
            }

            // Закрытие модального окна
            function closeEditModal() {
                document.getElementById('editModal').classList.add('hidden');
                document.getElementById('self_edit_warning').classList.add('hidden');
            }

            // Сохранение пользователя (AJAX)
            async function saveUser() {
                const userId = parseInt(document.getElementById('edit_user_id').value);
                const username = document.getElementById('edit_username').value;
                const email = document.getElementById('edit_email').value;
                const role = document.getElementById('edit_role').value;
                const saveBtn = document.getElementById('saveUserBtn');

                // Блокируем кнопку
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-1"></i> Сохранение...';

                try {
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'update_user',
                            user_id: userId,
                            username: username,
                            email: email,
                            role: role,
                            csrf_token: csrfToken
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        showToast(data.error || `Ошибка сервера: ${response.status}`, 'error');
                        return;
                    }

                    if (data.success) {
                        showToast(data.message || 'Пользователь успешно обновлен', 'success');
                        updateUserRow({ id: userId, username, email, role });
                        updateStatistics();
                        closeEditModal();
                    } else {
                        showToast(data.error || 'Ошибка при обновлении', 'error');
                    }
                } catch (error) {
                    console.error('Ошибка при обновлении пользователя:', error);
                    showToast('Ошибка сети. Попробуйте снова.', 'error');
                } finally {
                    // Разблокируем кнопку
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="ri-save-line mr-1"></i> Сохранить';
                }
            }

            // Подтверждение удаления пользователя
            function deleteUserConfirm(userId, username) {
                if (confirm(`Вы уверены, что хотите удалить пользователя "${username}"?`)) {
                    deleteUser(userId);
                }
            }

            // Удаление пользователя (AJAX)
            async function deleteUser(userId) {
                try {
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'delete_user',
                            user_id: userId,
                            csrf_token: csrfToken
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        showToast(data.error || `Ошибка сервера: ${response.status}`, 'error');
                        return;
                    }

                    if (data.success) {
                        showToast(data.message || 'Пользователь успешно удален', 'success');
                        removeUserRow(userId);
                    } else {
                        showToast(data.error || 'Ошибка при удалении', 'error');
                    }
                } catch (error) {
                    console.error('Ошибка при удалении пользователя:', error);
                    showToast('Ошибка сети. Попробуйте снова.', 'error');
                }
            }

            // Добавляем data-user-id к строкам таблицы
            document.addEventListener('DOMContentLoaded', function() {
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length > 0) {
                        const userId = cells[0].textContent.trim();
                        row.setAttribute('data-user-id', userId);

                        // Добавляем классы для быстрого доступа
                        // Для username берем второй div (после иконки), а не первый
                        const usernameDiv = cells[1].querySelector('div.flex > div.text-sm');
                        if (usernameDiv) {
                            usernameDiv.classList.add('user-username');
                        }
                        cells[2].classList.add('user-email');
                        cells[3].querySelector('span').classList.add('user-role');
                    }
                });
            });

            // Закрытие модального окна при клике вне его
            document.getElementById('editModal').addEventListener('click', function(event) {
                if (event.target === this) {
                    closeEditModal();
                }
            });

            // Закрытие модального окна по клавише Escape
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeEditModal();
                }
            });
        </script>
    <?php
}
