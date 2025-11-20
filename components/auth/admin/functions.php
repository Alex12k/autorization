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

