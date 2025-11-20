/**
 * Модуль обработки админ панели
 * Управление пользователями через AJAX
 */

// Глобальные переменные (будут установлены из PHP)
let currentUserId;
let currentUserRole;
let csrfToken;
const apiUrl = '/components/auth/admin/ajax/ajax.php';

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Получаем данные из data-атрибутов
    const adminData = document.getElementById('admin-data');
    if (adminData) {
        currentUserId = parseInt(adminData.dataset.userId) || 0;
        currentUserRole = adminData.dataset.userRole || '';
        csrfToken = adminData.dataset.csrfToken || '';
    }

    // Инициализация таблицы пользователей
    initUsersTable();

    // Инициализация модального окна
    initEditModal();
});

/**
 * Инициализация таблицы пользователей
 */
function initUsersTable() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 0) {
            const userId = cells[0].textContent.trim();
            row.setAttribute('data-user-id', userId);

            // Добавляем классы для быстрого доступа
            const usernameDiv = cells[1].querySelector('div.flex > div.text-sm');
            if (usernameDiv) {
                usernameDiv.classList.add('user-username');
            }
            cells[2].classList.add('user-email');
            cells[3].querySelector('span').classList.add('user-role');
        }
    });
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
    const rows = document.querySelectorAll('tbody tr');
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
