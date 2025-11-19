<?php
/**
 * Front Controller - Единая точка входа в приложение
 * Все запросы проходят через этот файл
 */

// Инициализация системы
require_once 'config.php';
require_once 'functions/functions.php';
require_once 'functions/route.php';
require_once 'functions/layout.php';

session_start();


// Глобальные скрипты (загружаются на ВСЕХ страницах)
// Порядок важен: сначала общий обработчик, потом модули
addGlobalScript(url('templates/auth-ajax-handler.js'));  // Общий обработчик (утилиты)
addGlobalScript(url('components/auth/login/script.js'));      // Модуль логина
addGlobalScript(url('components/auth/register/script.js'));    // Модуль регистрации
addGlobalScript(url('components/auth/forgot-password/script.js'));  // Модуль восстановления пароля
addGlobalScript(url('components/auth/reset-password/script.js'));    // Модуль сброса пароля
addGlobalScript(url('components/auth/logout/script.js'));            // Модуль выхода из системы

// Отрисовка header
renderHeader();

// Запуск роутера (загрузка контента страницы)
route();

// Отрисовка footer
renderFooter();
