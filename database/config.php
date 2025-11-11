<?php
/**
 * Конфигурация базы данных
 */

// Настройки для PostgreSQL
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'php_auth_demo');
define('DB_USER', get_current_user()); // Текущий пользователь системы
define('DB_PASS', ''); // Пустой пароль для локальной разработки

// Настройки для SQLite (резервный вариант)
define('SQLITE_PATH', __DIR__ . '/database.sqlite');

// Определяем, какую базу данных использовать
define('USE_POSTGRESQL', false); // Пока используем SQLite для простоты

