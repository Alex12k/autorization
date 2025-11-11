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
addGlobalScript(url('templates/login/script.js'));

// Отрисовка header
renderHeader();

// Запуск роутера (загрузка контента страницы)
route();

// Отрисовка footer
renderFooter();
