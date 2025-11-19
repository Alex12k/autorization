<?php
// Проверка авторизации пользователя
if (isAuthenticated()) {
    // Если пользователь залогинен, перенаправляем на dashboard
    redirect('dashboard');
    exit;
}

// Загружаем модуль логина и вызываем функцию login()
// Компонент login() отвечает только за отображение формы и обработку входа
require_once __DIR__ . '/../templates/login/login.php';
login();

