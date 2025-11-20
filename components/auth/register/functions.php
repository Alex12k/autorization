<?php
/**
 * Функции компонента register
 * Специфичные функции для регистрации пользователей
 */

/**
 * Регистрация нового пользователя
 */
function registerUser(string $username, string $email, string $password, string $confirm_password): array
{
    try {
        $pdo = getDB();
        
        // Валидация
        if (empty($username) || empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Все поля обязательны для заполнения'];
        }
        
        if ($password !== $confirm_password) {
            return ['success' => false, 'error' => 'Пароли не совпадают'];
        }
        
        if (strlen($password) < 6) {
            return ['success' => false, 'error' => 'Пароль должен содержать минимум 6 символов'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Некорректный email адрес'];
        }
        
        // Проверка существования пользователя
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'Пользователь с таким именем или email уже существует'];
        }
        
        // Создание нового пользователя
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, role, created_at) 
            VALUES (?, ?, ?, 'user', CURRENT_TIMESTAMP)
        ");
        
        $stmt->execute([$username, $email, $password_hash]);
        $user_id = $pdo->lastInsertId();
        
        return [
            'success' => true,
            'user' => [
                'id' => $user_id,
                'username' => $username,
                'email' => $email,
                'role' => 'user'
            ]
        ];
        
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Ошибка системы. Попробуйте позже.'
        ];
    }
}

