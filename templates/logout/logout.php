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
    // Выход из системы
    logout();
    
    // Редирект на главную страницу
    redirect();
    exit;
}

