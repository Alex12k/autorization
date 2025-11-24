<?php
/**
 * AJAX обработчик для компонента password-recovery
 * Обрабатывает все AJAX запросы, связанные с восстановлением пароля (запрос и сброс)
 */

// Загрузка зависимостей
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/../../functions.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    define('SYSTEM_INITIALIZED', true);
}

// Загрузка функций компонента password-recovery
require_once __DIR__ . '/../functions.php';

// Проверяем, что это AJAX запрос
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$is_ajax = $is_ajax || (isset($_POST['ajax']) && $_POST['ajax'] === '1');

if (!$is_ajax) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Этот endpoint предназначен только для AJAX запросов'
    ]);
    exit;
}

// Получаем действие
$action = $_POST['action'] ?? '';

// Обработка запроса на открытие формы запроса восстановления пароля в модальном окне
if ($action === 'open_modal_forgot_password_form') {
    header('Content-Type: text/html; charset=utf-8');
    require_once __DIR__ . '/../password_recovery.php';
    modal_forgot_password_form();
    exit;
}

// Обработка запроса на открытие формы сброса пароля в модальном окне
if ($action === 'open_modal_reset_password_form') {
    header('Content-Type: text/html; charset=utf-8');
    require_once __DIR__ . '/../password_recovery.php';
    $token = $_POST['token'] ?? '';
    modal_reset_password_form($token);
    exit;
}

// Обработка отправки формы запроса восстановления пароля
if ($action === 'forgot-password') {
    // Устанавливаем заголовок для JSON ответов
    header('Content-Type: application/json');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Проверка CSRF токена
    if (!verifyCSRFToken($csrf_token)) {
        echo json_encode([
            'success' => false,
            'error' => 'Ошибка безопасности. Попробуйте еще раз.'
        ]);
        exit;
    }
    
    // Получение данных формы
    $email = trim($_POST['email'] ?? '');
    
    // Запрос на восстановление пароля
    $result = requestPasswordReset($email);
    
    if ($result['success']) {
        // Успешный запрос
        echo json_encode([
            'success' => true,
            'message' => 'Ссылка для восстановления пароля отправлена на ваш email',
            'token' => $result['token'] ?? null,
            'email' => $result['email'] ?? null
        ]);
        exit;
    } else {
        // Ошибка
        echo json_encode([
            'success' => false,
            'error' => $result['error']
        ]);
        exit;
    }
}

// Обработка отправки формы сброса пароля
if ($action === 'reset-password') {
    // Устанавливаем заголовок для JSON ответов
    header('Content-Type: application/json');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Проверка CSRF токена
    if (!verifyCSRFToken($csrf_token)) {
        echo json_encode([
            'success' => false,
            'error' => 'Ошибка безопасности. Попробуйте еще раз.'
        ]);
        exit;
    }
    
    // Получение данных формы
    $token = $_POST['token'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Сброс пароля по токену
    $result = resetPasswordWithToken($token, $new_password, $confirm_password);
    
    if ($result['success']) {
        // Успешный сброс пароля
        echo json_encode([
            'success' => true,
            'message' => 'Пароль успешно изменен! Теперь вы можете войти в систему.'
        ]);
        exit;
    } else {
        // Ошибка
        echo json_encode([
            'success' => false,
            'error' => $result['error']
        ]);
        exit;
    }
}

// Неизвестное действие
echo json_encode([
    'success' => false,
    'error' => 'Неизвестное действие'
]);
exit;

