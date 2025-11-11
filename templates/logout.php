<?php
/**
 * Шаблон страницы logout
 */

function logoutPage(): void
{
    // Выход из системы
    logout();

    // Перенаправление на главную страницу
    redirect('home');
    exit;
}
