<?php
/**
 * Функции компонента change-password
 * Смена пароля для авторизованных пользователей
 */

/**
 * Смена пароля авторизованным пользователем
 * Переиспользует логику валидации из resetPasswordWithToken
 */
function changePassword(int $user_id, string $current_password, string $new_password, string $confirm_password): array
{
    try {
        $pdo = getDB();
        
        // Проверка аутентификации
        if (!isAuthenticated() || $_SESSION['user_id'] != $user_id) {
            return ['success' => false, 'error' => 'Недостаточно прав'];
        }
        
        // Валидация паролей (переиспользуем логику из resetPasswordWithToken)
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            return ['success' => false, 'error' => 'Все поля обязательны для заполнения'];
        }
        
        if ($new_password !== $confirm_password) {
            return ['success' => false, 'error' => 'Пароли не совпадают'];
        }
        
        if (strlen($new_password) < 6) {
            return ['success' => false, 'error' => 'Пароль должен содержать минимум 6 символов'];
        }
        
        // Проверка текущего пароля (переиспользуем логику из login)
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($current_password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Неверный текущий пароль'];
        }
        
        // Проверка, что новый пароль отличается от текущего
        if (password_verify($new_password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Новый пароль должен отличаться от текущего'];
        }
        
        // Обновление пароля (переиспользуем логику из resetPasswordWithToken)
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$password_hash, $user_id]);
        
        // Логирование
        error_log("Password changed for user ID: {$user_id}");
        
        return [
            'success' => true,
            'message' => 'Пароль успешно изменен'
        ];
        
    } catch (Exception $e) {
        error_log("Change password error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => 'Ошибка при изменении пароля. Попробуйте позже.'
        ];
    }
}

