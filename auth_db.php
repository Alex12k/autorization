<?php
require_once 'config.php';

session_start();

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

// Обработка POST запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'login':
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!verifyCSRFToken($csrf_token)) {
                $login_error = 'Ошибка безопасности. Попробуйте еще раз.';
            } else {
                $result = authenticateUser($username, $password);
                if ($result['success']) {
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $login_error = $result['error'];
                }
            }
            break;
            
        case 'register':
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $csrf_token = $_POST['csrf_token'] ?? '';
            
            if (!verifyCSRFToken($csrf_token)) {
                $register_error = 'Ошибка безопасности. Попробуйте еще раз.';
            } else {
                $result = registerUser($username, $email, $password, $confirm_password);
                if ($result['success']) {
                    $register_success = 'Регистрация успешна! Теперь вы можете войти.';
                } else {
                    $register_error = $result['error'];
                }
            }
            break;
            
        case 'logout':
            logout();
            header('Location: index.php');
            exit;
            break;
    }
}
?>
