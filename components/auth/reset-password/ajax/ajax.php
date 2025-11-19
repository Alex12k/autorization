<?php
/**
 * AJAX обработчик для компонента сброса пароля
 * Обрабатывает все AJAX запросы, связанные со сбросом пароля
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

// Обработка запроса на открытие формы сброса пароля
if ($action === 'open_reset-password') {
    // Для HTML ответа устанавливаем соответствующий заголовок
    header('Content-Type: text/html; charset=utf-8');
    
    // Получаем токен из POST (в AJAX запросах токен передается через POST)
    $token = $_POST['token'] ?? '';
    
    // Загружаем функцию resetPassword() для отображения формы
    require_once __DIR__ . '/../reset_password.php';
    
    // Временно очищаем POST данные, чтобы resetPassword() не обрабатывал их
    $original_post = $_POST;
    $original_get = $_GET;
    $_POST = [];
    
    // Устанавливаем токен в GET для функции resetPassword()
    if (!empty($token)) {
        $_GET['token'] = $token;
    }
    
    // Захватываем вывод функции resetPassword()
    ob_start();
    resetPassword();
    $form_html = ob_get_clean();
    
    // Восстанавливаем данные
    $_POST = $original_post;
    $_GET = $original_get;
    
    // Возвращаем HTML формы
    echo $form_html;
    exit;
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

