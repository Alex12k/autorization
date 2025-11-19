<?php
/**
 * AJAX обработчик для компонента восстановления пароля
 * Обрабатывает все AJAX запросы, связанные с восстановлением пароля
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

// Обработка запроса на открытие формы восстановления пароля
if ($action === 'open_forgot-password' || $action === 'open_forgot_password') {
    // Для HTML ответа устанавливаем соответствующий заголовок
    header('Content-Type: text/html; charset=utf-8');
    
    // Загружаем функцию forgotPassword() для отображения формы
    require_once __DIR__ . '/../forgot_password.php';
    
    // Временно очищаем POST данные, чтобы forgotPassword() не обрабатывал их
    $original_post = $_POST;
    $_POST = [];
    
    // Захватываем вывод функции forgotPassword()
    ob_start();
    forgotPassword();
    $form_html = ob_get_clean();
    
    // Восстанавливаем POST данные
    $_POST = $original_post;
    
    // Возвращаем HTML формы
    echo $form_html;
    exit;
}

// Обработка отправки формы восстановления пароля
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

// Неизвестное действие
echo json_encode([
    'success' => false,
    'error' => 'Неизвестное действие'
]);
exit;


