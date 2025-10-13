<?php
session_start();

// Конфигурация базы данных (для демонстрации используем массив)
$users = [
    [
        'id' => 1,
        'username' => 'admin',
        'email' => 'admin@example.com',
        'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'admin',
        'created_at' => '2024-01-01 00:00:00'
    ],
    [
        'id' => 2,
        'username' => 'user',
        'email' => 'user@example.com',
        'password_hash' => password_hash('user123', PASSWORD_DEFAULT),
        'role' => 'user',
        'created_at' => '2024-01-01 00:00:00'
    ]
];

/**
 * Аутентификация пользователя
 */
function authenticateUser(string $username, string $password): array
{
    global $users;
    
    // Поиск пользователя
    $user = null;
    foreach ($users as $u) {
        if ($u['username'] === $username || $u['email'] === $username) {
            $user = $u;
            break;
        }
    }
    
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
}

/**
 * Регистрация нового пользователя
 */
function registerUser(string $username, string $email, string $password, string $confirm_password): array
{
    global $users;
    
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
    
    // Проверка существования пользователя
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return ['success' => false, 'error' => 'Пользователь с таким именем уже существует'];
        }
        if ($user['email'] === $email) {
            return ['success' => false, 'error' => 'Пользователь с таким email уже существует'];
        }
    }
    
    // Создание нового пользователя
    $new_user = [
        'id' => count($users) + 1,
        'username' => $username,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'user',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $users[] = $new_user;
    
    return [
        'success' => true,
        'user' => [
            'id' => $new_user['id'],
            'username' => $new_user['username'],
            'email' => $new_user['email'],
            'role' => $new_user['role']
        ]
    ];
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
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role']
    ];
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