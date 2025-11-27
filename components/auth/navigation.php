<?php
/**
 * Компонент навигации модуля auth
 * Обеспечивает удобный доступ к функционалу авторизации
 */

/**
 * Рендерит навигационные элементы модуля auth
 * Отображает различные элементы в зависимости от статуса авторизации
 */
function renderAuthNavigation(): void
{
    $isAuthenticated = isAuthenticated();
    $user = $isAuthenticated ? getCurrentUser() : null;
    
    if ($isAuthenticated && $user): ?>
        <!-- Для авторизованных пользователей -->
        <a href="/dashboard" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
            <i class="ri-dashboard-line mr-1"></i>
            Dashboard
        </a>
        
        <?php if (hasRole('admin')): ?>
            <a href="/admin" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                <i class="ri-admin-line mr-1"></i>
                Админ панель
            </a>
        <?php endif; ?>

        <!-- Профиль пользователя -->
        <div class="flex items-center space-x-3 pl-4 border-l border-gray-300 dark:border-gray-700">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-md">
                    <i class="ri-user-line text-white text-sm"></i>
                </div>
                <div class="hidden md:block">
                    <div class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($user['username']) ?></div>
                    <div class="text-xs text-gray-500 dark:text-gray-400"><?= ucfirst($user['role']) ?></div>
                </div>
            </div>
            
            <a href="#" class="open_logout bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-sm font-medium cursor-pointer">
                <i class="ri-logout-box-line mr-1"></i>
                <span class="hidden md:inline">Выйти</span>
            </a>
        </div>
    <?php else: ?>
        <!-- Для неавторизованных пользователей -->
        <a href="#" class="open_modal_login_form text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors cursor-pointer">
            <i class="ri-account-circle-line mr-1"></i>
            Аккаунт
        </a>

        <div class="flex items-center space-x-2 pl-4 border-l border-gray-300 dark:border-gray-700">
            <a href="#" class="open_modal_login_form bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all text-sm font-medium cursor-pointer">
                <i class="ri-login-box-line mr-1"></i>
                Войти
            </a>
            
            <a href="#" class="open_modal_register_form bg-gradient-to-r from-green-500 to-teal-600 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-teal-700 transition-all text-sm font-medium cursor-pointer">
                <i class="ri-user-add-line mr-1"></i>
                Регистрация
            </a>
        </div>
    <?php endif;
}

