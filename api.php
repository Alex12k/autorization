<?php
/**
 * API endpoint для асинхронных операций
 * Обрабатывает AJAX запросы и возвращает JSON
 */

header('Content-Type: application/json');

// Инициализация системы
require_once 'config.php';
require_once 'functions/functions.php';
session_start();

// Проверяем, что запрос методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Метод не разрешен']);
    exit;
}

// Проверяем аутентификацию
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

// Получаем данные из запроса
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

// Обрабатываем различные действия
switch ($action) {
    case 'update_user':
        handleUpdateUser($data);
        break;
        
    case 'delete_user':
        handleDeleteUser($data);
        break;
        
    case 'get_users':
        handleGetUsers();
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
        break;
}

/**
 * Обновление пользователя
 */
function handleUpdateUser($data) {
    $user_id = (int)($data['user_id'] ?? 0);
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $role = $data['role'] ?? '';
    $csrf_token = $data['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Ошибка безопасности']);
        return;
    }
    
    $result = updateUserByAdmin($user_id, $username, $email, $role);
    
    if ($result['success']) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(400);
        echo json_encode($result);
    }
}

/**
 * Удаление пользователя
 */
function handleDeleteUser($data) {
    $user_id = (int)($data['user_id'] ?? 0);
    $csrf_token = $data['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Ошибка безопасности']);
        return;
    }
    
    $result = deleteUser($user_id);
    
    if ($result['success']) {
        http_response_code(200);
        echo json_encode($result);
    } else {
        http_response_code(400);
        echo json_encode($result);
    }
}

/**
 * Получение списка пользователей
 */
function handleGetUsers() {
    if (!hasRole('admin')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Недостаточно прав']);
        return;
    }
    
    $users = getAllUsers();
    echo json_encode(['success' => true, 'users' => $users]);
}
