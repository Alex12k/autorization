<?php
/**
 * Функции компонента admin
 * Специфичные функции для управления пользователями администратором
 */

/**
 * Получение всех пользователей (только для админов)
 */
function getAllUsers(): array
{
    if (!hasRole('admin')) {
        return [];
    }
    
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("
            SELECT id, username, email, role, created_at 
            FROM users 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        error_log("Get all users error: " . $e->getMessage());
        return [];
    }
}

/**
 * Обновление пользователя админом
 */
function updateUserByAdmin(int $user_id, string $username, string $email, string $role): array
{
    try {
        // Проверяем права админа
        if (!hasRole('admin')) {
            return ['success' => false, 'error' => 'Недостаточно прав'];
        }
        
        $pdo = getDB();
        
        // Проверяем существование пользователя
        $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'error' => 'Пользователь не найден'];
        }
        
        // Проверяем, не пытается ли админ понизить свою роль
        if ($user_id == $_SESSION['user_id'] && $user['role'] === 'admin' && $role === 'user') {
            // Подсчитываем количество администраторов
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
            $stmt->execute();
            $adminCount = $stmt->fetchColumn();
            
            if ($adminCount <= 1) {
                return ['success' => false, 'error' => 'Нельзя понизить роль последнего администратора. В системе должен быть хотя бы один администратор.'];
            }
            
            return ['success' => false, 'error' => 'Нельзя понизить свою собственную роль администратора. Попросите другого администратора изменить вашу роль.'];
        }
        
        // Валидация
        if (empty($username) || empty($email)) {
            return ['success' => false, 'error' => 'Имя пользователя и email обязательны'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Некорректный email адрес'];
        }
        
        if (!in_array($role, ['user', 'admin'])) {
            return ['success' => false, 'error' => 'Некорректная роль'];
        }
        
        // Проверяем уникальность username и email
        $stmt = $pdo->prepare("
            SELECT id FROM users 
            WHERE (username = ? OR email = ?) AND id != ?
        ");
        $stmt->execute([$username, $email, $user_id]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'Пользователь с таким именем или email уже существует'];
        }
        
        // Обновляем данные
        $stmt = $pdo->prepare("
            UPDATE users 
            SET username = ?, email = ?, role = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->execute([$username, $email, $role, $user_id]);
        
        // Обновляем сессию, если редактируем свой аккаунт
        if ($user_id == $_SESSION['user_id']) {
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
        }
        
        return [
            'success' => true,
            'message' => 'Пользователь успешно обновлен'
        ];
        
    } catch (Exception $e) {
        error_log("Update user by admin error: " . $e->getMessage());
        return ['success' => false, 'error' => 'Ошибка системы'];
    }
}

/**
 * Удаление пользователя (только для админов)
 */
function deleteUser(int $user_id): array
{
    try {
        // Проверяем права админа
        if (!hasRole('admin')) {
            return ['success' => false, 'error' => 'Недостаточно прав'];
        }
        
        // Запрещаем удалять самого себя
        if ($user_id == $_SESSION['user_id']) {
            return ['success' => false, 'error' => 'Нельзя удалить свою учетную запись'];
        }
        
        $pdo = getDB();
        
        // Проверяем существование пользователя
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'error' => 'Пользователь не найден'];
        }
        
        // Удаляем пользователя
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        
        return [
            'success' => true,
            'message' => 'Пользователь ' . $user['username'] . ' успешно удален'
        ];
        
    } catch (Exception $e) {
        error_log("Delete user error: " . $e->getMessage());
        return ['success' => false, 'error' => 'Ошибка системы'];
    }
}

/**
 * Получение агрегированной статистики по пользователям
 */
function getUsersStats(): array
{
    if (!hasRole('admin')) {
        return [
            'total' => 0,
            'admins' => 0,
            'users' => 0,
            'last_week' => 0,
        ];
    }

    try {
        $pdo = getDB();

        // Общее количество пользователей
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM users");
        $total = (int)$totalStmt->fetchColumn();

        // Количество админов
        $adminsStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
        $admins = (int)$adminsStmt->fetchColumn();

        // Количество обычных пользователей
        $usersStmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
        $users = (int)$usersStmt->fetchColumn();

        // Пользователи за последнюю неделю
        $weekStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM users 
            WHERE created_at >= datetime('now', '-7 days')
        ");
        $weekStmt->execute();
        $lastWeek = (int)$weekStmt->fetchColumn();

        return [
            'total' => $total,
            'admins' => $admins,
            'users' => $users,
            'last_week' => $lastWeek,
        ];
    } catch (Exception $e) {
        error_log("Get users stats error: " . $e->getMessage());
        return [
            'total' => 0,
            'admins' => 0,
            'users' => 0,
            'last_week' => 0,
        ];
    }
}

/**
 * Получение пользователей с учетом фильтров и пагинации
 */
function getUsersWithFilters(array $options): array
{
    if (!hasRole('admin')) {
        return ['users' => [], 'total' => 0];
    }

    $search = trim($options['search'] ?? '');
    $role = $options['role'] ?? '';
    $sort = $options['sort'] ?? 'created_desc';
    $limit = max(1, min(200, (int)($options['limit'] ?? 50)));
    $offset = max(0, (int)($options['offset'] ?? 0));

    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = "(username LIKE :search OR email LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if (in_array($role, ['user', 'admin'], true)) {
        $where[] = "role = :role";
        $params[':role'] = $role;
    }

    // Сортировка
    switch ($sort) {
        case 'username_asc':
            $orderBy = 'username ASC';
            break;
        case 'username_desc':
            $orderBy = 'username DESC';
            break;
        case 'created_asc':
            $orderBy = 'created_at ASC';
            break;
        case 'created_desc':
        default:
            $orderBy = 'created_at DESC';
            break;
    }

    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    try {
        $pdo = getDB();

        // Общее количество с учетом фильтров
        $countSql = "SELECT COUNT(*) FROM users {$whereSql}";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Данные текущей страницы
        $dataSql = "
            SELECT id, username, email, role, created_at 
            FROM users 
            {$whereSql}
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ";

        $dataStmt = $pdo->prepare($dataSql);
        foreach ($params as $key => $value) {
            $dataStmt->bindValue($key, $value);
        }
        $dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();

        $users = $dataStmt->fetchAll();

        return [
            'users' => $users,
            'total' => $total,
        ];
    } catch (Exception $e) {
        error_log("Get users with filters error: " . $e->getMessage());
        return [
            'users' => [],
            'total' => 0,
        ];
    }
}

