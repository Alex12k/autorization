/**
 * Модуль обработки админ панели
 * Управление пользователями через AJAX
 */

// Глобальные переменные (будут установлены из PHP)
let currentUserId;
let currentUserRole;
let csrfToken;
const apiUrl = '/components/auth/admin/ajax/ajax.php';

// Состояние для ленивой подгрузки
let usersOffset = 0;
const usersLimit = 50;
let usersHasMore = true;
let usersIsLoading = false;
let usersTotal = 0;

// Текущие фильтры
let currentFilters = {
    search: '',
    role: '',
    sort: 'created_desc',
};

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Если на странице нет данных админки — выходим и не инициализируем модуль
    const adminData = document.getElementById('admin-data');
    if (!adminData) {
        return;
    }

    // Получаем данные из data-атрибутов
    currentUserId = parseInt(adminData.dataset.userId) || 0;
    currentUserRole = adminData.dataset.userRole || '';
    csrfToken = adminData.dataset.csrfToken || '';

    // Инициализация фильтров и ленивой подгрузки
    initUsersFilters();
    initInfiniteScroll();

    // Первичная загрузка
    loadUsers(true);

    // Инициализация модального окна
    initEditModal();
});

/**
 * Инициализация фильтров
 */
function initUsersFilters() {
    const searchInput = document.getElementById('filter_search');
    const roleSelect = document.getElementById('filter_role');
    const sortSelect = document.getElementById('filter_sort');

    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentFilters.search = searchInput.value.trim();
                resetUsersAndLoad();
            }, 300);
        });
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', () => {
            currentFilters.role = roleSelect.value;
            resetUsersAndLoad();
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            currentFilters.sort = sortSelect.value || 'created_desc';
            resetUsersAndLoad();
        });
    }
}

/**
 * Инициализация бесконечной прокрутки
 */
function initInfiniteScroll() {
    const container = document.getElementById('users-table-container');
    if (!container) return;

    container.addEventListener('scroll', () => {
        if (usersIsLoading || !usersHasMore) return;

        const threshold = 150; // px до низа контейнера
        if (container.scrollTop + container.clientHeight >= container.scrollHeight - threshold) {
            loadUsers(false);
        }
    });
}

/**
 * Сброс списка пользователей и загрузка с нуля
 */
function resetUsersAndLoad() {
    usersOffset = 0;
    usersHasMore = true;
    const tbody = document.getElementById('users-table-body');
    if (tbody) {
        tbody.innerHTML = '';
    }
    hideEndIndicator();
    loadUsers(true);
}

/**
 * Загрузка пользователей через AJAX
 */
async function loadUsers(isInitial = false) {
    if (usersIsLoading || !usersHasMore && !isInitial) return;

    const loadingIndicator = document.getElementById('users-loading-indicator');
    if (loadingIndicator) {
        loadingIndicator.classList.remove('hidden');
    }

    usersIsLoading = true;

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'get_users',
                limit: usersLimit,
                offset: usersOffset,
                search: currentFilters.search,
                role: currentFilters.role,
                sort: currentFilters.sort,
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            showToast(data.error || `Ошибка сервера: ${response.status}`, 'error');
            return;
        }

        const users = data.users || [];
        usersTotal = data.total ?? usersTotal;
        usersHasMore = Boolean(data.has_more);

        appendUsersToTable(users);

        usersOffset += users.length;

        if (!usersHasMore) {
            showEndIndicator();
        } else {
            hideEndIndicator();
        }
    } catch (error) {
        console.error('Ошибка при загрузке пользователей:', error);
        showToast('Ошибка сети при загрузке пользователей. Попробуйте снова.', 'error');
    } finally {
        usersIsLoading = false;
        if (loadingIndicator) {
            loadingIndicator.classList.add('hidden');
        }
    }
}

/**
 * Добавление пользователей в таблицу
 */
function appendUsersToTable(users) {
    const tbody = document.getElementById('users-table-body');
    if (!tbody || !Array.isArray(users)) return;

    const currentUserIdLocal = currentUserId;

    users.forEach(user => {
        const isAdmin = user.role === 'admin';

        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.setAttribute('data-user-id', user.id);

        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${user.id}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                        <i class="ri-user-line text-white text-sm"></i>
                    </div>
                    <div class="text-sm font-medium text-gray-900 user-username">
                        ${escapeHtml(user.username)}
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 user-email">
                ${escapeHtml(user.email)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${isAdmin ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'} user-role">
                    ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${formatDateTime(user.created_at)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                    <button 
                        onclick="openEditModal(${user.id}, '${escapeJs(user.username)}', '${escapeJs(user.email)}', '${user.role}')" 
                        class="text-blue-600 hover:text-blue-900" 
                        title="Редактировать пользователя">
                        <i class="ri-edit-line"></i>
                    </button>
                    ${user.id != currentUserIdLocal ? `
                    <button 
                        onclick="deleteUserConfirm(${user.id}, '${escapeJs(user.username)}')"
                        class="text-red-600 hover:text-red-900" 
                        title="Удалить пользователя">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                    ` : ''}
                </div>
            </td>
        `;

        tbody.appendChild(row);
    });
}

/**
 * Показ / скрытие индикаторов конца списка
 */
function showEndIndicator() {
    const end = document.getElementById('users-end-indicator');
    if (end) end.classList.remove('hidden');
}

function hideEndIndicator() {
    const end = document.getElementById('users-end-indicator');
    if (end) end.classList.add('hidden');
}

/**
 * Инициализация модального окна редактирования
 */
function initEditModal() {
    const editModal = document.getElementById('editModal');
    if (!editModal) return;

    // Закрытие при клике вне окна
    editModal.addEventListener('click', function(event) {
        if (event.target === this) {
            closeEditModal();
        }
    });

    // Закрытие по клавише Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeEditModal();
        }
    });
}

/**
 * Обновление строки пользователя в таблице
 */
function updateUserRow(user) {
    const row = document.querySelector(`tr[data-user-id="${user.id}"]`);
    if (!row) return;

    const roleClass = user.role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';

    // Обновляем username
    const usernameCell = row.querySelectorAll('td')[1];
    const usernameDiv = usernameCell.querySelector('div.flex > div.text-sm');
    if (usernameDiv) {
        usernameDiv.textContent = user.username;
    }

    // Обновляем email и роль
    row.querySelector('.user-email').textContent = user.email;
    row.querySelector('.user-role').className = `px-2 py-1 text-xs font-semibold rounded-full ${roleClass} user-role`;
    row.querySelector('.user-role').textContent = user.role.charAt(0).toUpperCase() + user.role.slice(1);

    // Обновляем onclick для кнопки редактирования
    const editButton = row.querySelector('button[onclick*="openEditModal"]');
    if (editButton) {
        const escapedUsername = user.username.replace(/'/g, "\\'");
        const escapedEmail = user.email.replace(/'/g, "\\'");
        editButton.setAttribute('onclick', 
            `openEditModal(${user.id}, '${escapedUsername}', '${escapedEmail}', '${user.role}')`
        );
    }

    // Визуальная обратная связь
    row.style.transition = 'background-color 0.3s ease';
    row.style.backgroundColor = '#d1fae5';
    setTimeout(() => {
        row.style.backgroundColor = '';
    }, 500);
}

/**
 * Удаление строки пользователя из таблицы
 */
function removeUserRow(userId) {
    const row = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (!row) return;

    row.style.transition = 'all 0.3s ease';
    row.style.opacity = '0';
    row.style.transform = 'translateX(-20px)';
    setTimeout(() => {
        row.remove();
        updateStatistics();
    }, 300);
}

/**
 * Обновление статистики пользователей
 */
function updateStatistics() {
    const rows = document.querySelectorAll('#users-table-body tr');
    const totalUsers = rows.length;
    const adminUsers = Array.from(rows).filter(row => 
        row.querySelector('.user-role')?.textContent.toLowerCase() === 'admin'
    ).length;
    const regularUsers = totalUsers - adminUsers;

    // Используем общую утилиту для анимации
    animateCounter(document.querySelector('.stat-total'), totalUsers);
    animateCounter(document.querySelector('.stat-admins'), adminUsers);
    animateCounter(document.querySelector('.stat-users'), regularUsers);
}

/**
 * Вспомогательная функция: безопасный HTML
 */
function escapeHtml(str) {
    if (typeof str !== 'string') return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Вспомогательная функция: экранирование для вставки в JS-строку
 */
function escapeJs(str) {
    if (typeof str !== 'string') return '';
    return str
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'")
        .replace(/"/g, '\\"');
}

/**
 * Форматирование даты/времени
 */
function formatDateTime(value) {
    if (!value) return '';
    try {
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return value;
        }
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${day}.${month}.${year} ${hours}:${minutes}`;
    } catch (e) {
        return value;
    }
}

/**
 * Открытие модального окна редактирования
 */
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
        // Предупреждение при попытке понизить роль
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

/**
 * Закрытие модального окна
 */
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    const warningElement = document.getElementById('self_edit_warning');
    if (warningElement) {
        warningElement.classList.add('hidden');
    }
}

/**
 * Сохранение пользователя (AJAX)
 */
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
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="ri-save-line mr-1"></i> Сохранить';
    }
}

/**
 * Подтверждение удаления пользователя
 */
function deleteUserConfirm(userId, username) {
    if (confirm(`Вы уверены, что хотите удалить пользователя "${username}"?`)) {
        deleteUser(userId);
    }
}

/**
 * Удаление пользователя (AJAX)
 */
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

console.log('Admin module initialized');
