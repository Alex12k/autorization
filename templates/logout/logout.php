<?php
/**
 * Шаблон страницы logout
 */

// Загрузка зависимостей (если файл вызывается напрямую)
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/../../functions/functions.php';
    require_once __DIR__ . '/../../functions/layout.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    define('SYSTEM_INITIALIZED', true);
}

function logoutPage(): void
{
    // Проверяем, это AJAX запрос
    $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_POST['ajax']) && $_POST['ajax'] === '1');
    
    // Выход из системы
    logout();
    
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'redirect' => url()]);
        exit;
    }
    
    redirect();
    exit;
}

// Единая точка входа для всех запросов
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    logoutPage();
    exit;
}

