<?php
/**
 * Функции компонента forgot-password
 * Специфичные функции для запроса восстановления пароля
 */

/**
 * Запрос на восстановление пароля
 * Создает токен восстановления и возвращает его для отправки пользователю
 */
function requestPasswordReset(string $email): array
{
    try {
        $pdo = getDB();
        
        // Валидация email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Некорректный email адрес'];
        }
        
        // Проверка существования пользователя
        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // Из соображений безопасности не сообщаем, что пользователь не найден
            // Это предотвращает перебор существующих email адресов
            return [
                'success' => true,
                'message' => 'Если пользователь с таким email существует, на него будет отправлена ссылка для восстановления пароля'
            ];
        }
        
        // Удаление старых неиспользованных токенов для этого пользователя
        $stmt = $pdo->prepare("DELETE FROM password_reset_tokens WHERE user_id = ? AND used_at IS NULL");
        $stmt->execute([$user['id']]);
        
        // Генерация криптографически безопасного токена
        $token = bin2hex(random_bytes(32)); // 64 символа
        
        // Срок действия токена - 1 час
        $expires_at = date('Y-m-d H:i:s', time() + 3600);
        
        // Получение IP адреса пользователя
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        
        // Сохранение токена в базе данных
        $stmt = $pdo->prepare("
            INSERT INTO password_reset_tokens (user_id, token, expires_at, ip_address)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$user['id'], $token, $expires_at, $ip_address]);
        
        // Логирование запроса
        error_log("Password reset requested for user: {$user['username']} ({$user['email']}) from IP: {$ip_address}");
        
        return [
            'success' => true,
            'token' => $token,
            'email' => $user['email'],
            'username' => $user['username'],
            'expires_in' => '1 час',
            'message' => 'Токен восстановления пароля создан'
        ];
        
    } catch (Exception $e) {
        error_log("Password reset request error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Ошибка системы. Попробуйте позже.'
        ];
    }
}

