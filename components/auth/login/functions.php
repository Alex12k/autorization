<?php
/**
 * Функции компонента login
 * Специфичные функции для входа в систему
 */

/**
 * Аутентификация пользователя
 */
function authenticateUser(string $username, string $password): array
{
    try {
        $pdo = getDB();
        
        // Поиск пользователя по имени пользователя или email
        $stmt = $pdo->prepare("
            SELECT id, username, email, password_hash, role 
            FROM users 
            WHERE username = ? OR email = ?
        ");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        // Проверка пароля
        if ($user && password_verify($password, $user['password_hash'])) {
            // Успешная аутентификация
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['authenticated'] = true;
            $_SESSION['login_time'] = time();
            
            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ];
        }
        
        return [
            'success' => false,
            'error' => 'Неверное имя пользователя или пароль'
        ];
        
    } catch (Exception $e) {
        error_log("Authentication error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Ошибка системы. Попробуйте позже.'
        ];
    }
}

