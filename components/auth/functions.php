<?php
/**
 * Общие функции компонента auth
 * Утилиты, используемые во всех компонентах
 */

/**
 * Редирект на указанный роут
 * @param string $route Имя роута
 * @param int $status_code HTTP статус код
 */
function redirect(string $route = '', int $status_code = 302): void
{
    $path = empty($route) ? '/' : '/' . ltrim($route, '/');
    header('Location: ' . $path, true, $status_code);
    exit;
}

/**
 * Получение соединения с базой данных
 */
function getDB(): PDO
{
    return getDatabaseConnection();
}

/**
 * Проверка аутентификации
 */
function isAuthenticated(): bool
{
    // Проверяем наличие флага авторизации и user_id для более надежной проверки
    return isset($_SESSION['authenticated']) && 
           $_SESSION['authenticated'] === true && 
           isset($_SESSION['user_id']) && 
           !empty($_SESSION['user_id']);
}

/**
 * Получение текущего пользователя
 */
function getCurrentUser(): ?array
{
    if (!isAuthenticated()) {
        return null;
    }
    
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("
            SELECT id, username, email, role, created_at 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user) {
            return $user;
        }
        
        // Если пользователь не найден в БД, очищаем сессию
        // Используем функцию из компонента logout
        if (function_exists('logout')) {
            logout();
        } else {
            require_once __DIR__ . '/logout/functions.php';
            logout();
        }
        return null;
        
    } catch (Exception $e) {
        error_log("Get current user error: " . $e->getMessage());
        return null;
    }
}

/**
 * Проверка роли пользователя
 */
function hasRole(string $role): bool
{
    $user = getCurrentUser();
    return $user && $user['role'] === $role;
}

/**
 * Валидация email
 */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Генерация CSRF токена
 */
function generateCSRFToken(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Проверка CSRF токена
 */
function verifyCSRFToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Обновление профиля пользователя
 * Может использоваться в dashboard для редактирования своего профиля
 */
function updateUserProfile(int $user_id, string $username, string $email): array
{
    try {
        $pdo = getDB();
        
        // Проверяем права доступа
        if (!isAuthenticated() || ($_SESSION['user_id'] != $user_id && !hasRole('admin'))) {
            return ['success' => false, 'error' => 'Недостаточно прав'];
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
            SET username = ?, email = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->execute([$username, $email, $user_id]);
        
        // Обновляем сессию
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        return ['success' => true];
        
    } catch (Exception $e) {
        error_log("Update profile error: " . $e->getMessage());
        return ['success' => false, 'error' => 'Ошибка системы'];
    }
}

// ============================================
// Функции для работы с токенами восстановления пароля
// (временно оставлены здесь, будут рефакторены позже)
// ============================================

/**
 * Очистка истекших токенов восстановления пароля
 * Рекомендуется запускать периодически (например, через cron)
 */
function cleanupExpiredResetTokens(): int
{
    try {
        $pdo = getDB();
        
        $stmt = $pdo->prepare("
            DELETE FROM password_reset_tokens 
            WHERE expires_at < CURRENT_TIMESTAMP OR used_at IS NOT NULL
        ");
        $stmt->execute();
        
        $deleted_count = $stmt->rowCount();
        error_log("Cleaned up {$deleted_count} expired/used password reset tokens");
        
        return $deleted_count;
        
    } catch (Exception $e) {
        error_log("Token cleanup error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Получение статистики по токенам восстановления (для админов)
 */
function getPasswordResetStats(): array
{
    try {
        $pdo = getDB();
        
        $stats = [];
        
        // Общее количество активных токенов
        $stmt = $pdo->query("
            SELECT COUNT(*) as count 
            FROM password_reset_tokens 
            WHERE used_at IS NULL AND expires_at > CURRENT_TIMESTAMP
        ");
        $stats['active_tokens'] = $stmt->fetchColumn();
        
        // Использованные токены за последние 24 часа
        $stmt = $pdo->query("
            SELECT COUNT(*) as count 
            FROM password_reset_tokens 
            WHERE used_at IS NOT NULL 
            AND used_at > datetime('now', '-1 day')
        ");
        $stats['used_last_24h'] = $stmt->fetchColumn();
        
        // Истекшие токены
        $stmt = $pdo->query("
            SELECT COUNT(*) as count 
            FROM password_reset_tokens 
            WHERE used_at IS NULL AND expires_at < CURRENT_TIMESTAMP
        ");
        $stats['expired_tokens'] = $stmt->fetchColumn();
        
        return $stats;
        
    } catch (Exception $e) {
        error_log("Stats retrieval error: " . $e->getMessage());
        return ['active_tokens' => 0, 'used_last_24h' => 0, 'expired_tokens' => 0];
    }
}
