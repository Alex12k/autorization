<?php
/**
 * Главный файл конфигурации приложения
 */

// Настройки безопасности
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 3600); // 1 час

// Настройки приложения
define('APP_NAME', 'PHP 8.4 Auth Demo');
define('APP_VERSION', '1.0.0');

// Подключаем конфигурацию базы данных
require_once __DIR__ . '/database/config.php';
require_once __DIR__ . '/database/connection.php';
require_once __DIR__ . '/database/init.php';



