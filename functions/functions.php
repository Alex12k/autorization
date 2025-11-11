<?php
/**
 * Файл содержит все функции проекта
 * Организация: все PHP функции собраны в одном месте
 */

/**
 * Генерация URL для роутинга
 * @param string $route Имя роута (например, 'login', 'dashboard')
 * @return string Полный URL
 */
function url(string $route = ''): string
{
    // Всегда используем корень сайта для роутов
    // Это работает независимо от того, откуда вызывается функция
    $base_url = '';
    
    // Если проект в поддиректории, можно определить через SCRIPT_NAME
    // Но для простоты используем корень
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    
    // Если SCRIPT_NAME содержит index.php, используем его директорию
    if (strpos($script_name, 'index.php') !== false) {
        $base_url = rtrim(dirname($script_name), '/');
        // Если получили '/', значит корень
        if ($base_url === '/') {
            $base_url = '';
        }
    }
    // Иначе (прямой доступ к файлу) - используем корень
    // base_url остается пустым
    
    if (empty($route) || $route === 'home') {
        return $base_url . '/';
    }
    
    return $base_url . '/' . ltrim($route, '/');
}

/**
 * Редирект на указанный роут
 * @param string $route Имя роута
 * @param int $status_code HTTP статус код
 */
function redirect(string $route = '', int $status_code = 302): void
{
    header('Location: ' . url($route), true, $status_code);
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

/**
 * Проверка аутентификации
 */
function isAuthenticated(): bool
{
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
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
        logout();
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
 * Выход пользователя
 */
function logout(): void
{
    session_destroy();
    session_start();
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
 * Обновление профиля пользователя
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

