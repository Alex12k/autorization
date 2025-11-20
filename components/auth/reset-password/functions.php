<?php
/**
 * Функции компонента reset-password
 * Специфичные функции для сброса пароля по токену
 */

/**
 * Проверка валидности токена восстановления пароля
 */
function validateResetToken(string $token): array
{
    try {
        $pdo = getDB();
        
        // Поиск токена
        $stmt = $pdo->prepare("
            SELECT prt.*, u.username, u.email 
            FROM password_reset_tokens prt
            JOIN users u ON prt.user_id = u.id
            WHERE prt.token = ?
        ");
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch();
        
        if (!$tokenData) {
            return [
                'success' => false,
                'error' => 'Неверный токен восстановления пароля'
            ];
        }
        
        // Проверка, не использован ли токен
        if ($tokenData['used_at'] !== null) {
            return [
                'success' => false,
                'error' => 'Этот токен уже был использован'
            ];
        }
        
        // Проверка срока действия
        if (strtotime($tokenData['expires_at']) < time()) {
            return [
                'success' => false,
                'error' => 'Срок действия токена истек. Запросите новую ссылку для восстановления пароля'
            ];
        }
        
        return [
            'success' => true,
            'user_id' => $tokenData['user_id'],
            'username' => $tokenData['username'],
            'email' => $tokenData['email']
        ];
        
    } catch (Exception $e) {
        error_log("Token validation error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Ошибка системы. Попробуйте позже.'
        ];
    }
}

/**
 * Сброс пароля по токену
 */
function resetPasswordWithToken(string $token, string $new_password, string $confirm_password): array
{
    try {
        $pdo = getDB();
        
        // Валидация паролей
        if (empty($new_password) || empty($confirm_password)) {
            return ['success' => false, 'error' => 'Все поля обязательны для заполнения'];
        }
        
        if ($new_password !== $confirm_password) {
            return ['success' => false, 'error' => 'Пароли не совпадают'];
        }
        
        if (strlen($new_password) < 6) {
            return ['success' => false, 'error' => 'Пароль должен содержать минимум 6 символов'];
        }
        
        // Проверка токена
        $validation = validateResetToken($token);
        if (!$validation['success']) {
            return $validation;
        }
        
        $user_id = $validation['user_id'];
        
        // Начало транзакции
        $pdo->beginTransaction();
        
        try {
            // Обновление пароля
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$password_hash, $user_id]);
            
            // Отметка токена как использованного
            $stmt = $pdo->prepare("UPDATE password_reset_tokens SET used_at = CURRENT_TIMESTAMP WHERE token = ?");
            $stmt->execute([$token]);
            
            // Удаление всех других неиспользованных токенов для этого пользователя
            $stmt = $pdo->prepare("DELETE FROM password_reset_tokens WHERE user_id = ? AND used_at IS NULL");
            $stmt->execute([$user_id]);
            
            $pdo->commit();
            
            // Логирование
            error_log("Password successfully reset for user ID: {$user_id} ({$validation['username']})");
            
            return [
                'success' => true,
                'message' => 'Пароль успешно изменен. Теперь вы можете войти с новым паролем.'
            ];
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log("Password reset error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Ошибка при изменении пароля. Попробуйте позже.'
        ];
    }
}

