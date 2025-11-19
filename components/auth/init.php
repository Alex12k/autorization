<?php
/**
 * Инициализация компонента auth
 * Загружает все необходимые файлы и настраивает систему
 */

// Загрузка конфигурации
require_once __DIR__ . '/config.php';

// Загрузка функций
require_once __DIR__ . '/functions.php';

// Инициализация сессии (если еще не запущена)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

