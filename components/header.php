<?php
/**
 * Универсальный header сайта
 * Отображает навигацию и доступ к функционалу в зависимости от статуса авторизации
 */

function renderHeader(?string $pageTitle = null, ?string $pageIcon = null): void
{
    // Защита от двойного вызова (но позволяем переопределить заголовок)
    static $rendered = false;
    static $currentTitle = null;
    static $currentIcon = null;
    
    // Если уже был вызов, но новый с заголовком - используем новый
    if ($rendered && !$pageTitle) return;
    
    // Сохраняем параметры
    if ($pageTitle) {
        $currentTitle = $pageTitle;
        $currentIcon = $pageIcon;
    }
    
    $rendered = true;
    $isAuthenticated = isAuthenticated();
    $user = $isAuthenticated ? getCurrentUser() : null;
    
    // Используем сохраненные параметры, если они есть
    $displayTitle = $currentTitle ?: $pageTitle;
    $displayIcon = $currentIcon ?: $pageIcon;
    ?>
    <header class="bg-white dark:bg-gray-800 shadow-lg sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Логотип и название -->
                <div class="flex items-center space-x-3 flex-shrink-0">
                    <?php if ($displayTitle): ?>
                        <!-- Заголовок страницы (для dashboard/admin) -->
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <div class="gradient-bg w-10 h-10 rounded-lg flex items-center justify-center shadow-md flex-shrink-0">
                                <i class="<?= htmlspecialchars($displayIcon ?? 'ri-dashboard-line') ?> text-white text-xl"></i>
                            </div>
                            <span class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white truncate"><?= htmlspecialchars($displayTitle) ?></span>
                        </div>
                    <?php else: ?>
                        <!-- Логотип сайта -->
                        <a href="/" class="flex items-center space-x-2 sm:space-x-3 hover:opacity-80 transition-opacity">
                            <div class="gradient-bg w-10 h-10 rounded-lg flex items-center justify-center shadow-md flex-shrink-0">
                                <i class="ri-shield-user-line text-white text-xl"></i>
                            </div>
                            <span class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white hidden sm:inline">Auth System</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Навигация для десктопа -->
                <div class="hidden md:flex items-center space-x-4">
                    <!-- Общая навигация сайта -->
                    <a href="/" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="ri-home-line mr-1"></i>
                        Главная
                    </a>
                    
                    <!-- Навигация модуля auth -->
                    <?php renderAuthNavigation(); ?>
                </div>

                <!-- Кнопка мобильного меню -->
                <button id="mobile-menu-button" class="md:hidden p-2 rounded-md text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" aria-label="Открыть меню">
                    <i class="ri-menu-line text-2xl"></i>
                </button>
            </div>

            <!-- Мобильное меню -->
            <div id="mobile-menu" class="md:hidden pb-4 border-t border-gray-200 dark:border-gray-700 mt-2 overflow-hidden transition-all duration-300 ease-in-out max-h-0 opacity-0">
                <div class="flex flex-col space-y-2 pt-4">
                    <!-- Общая навигация сайта -->
                    <a href="/" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="ri-home-line mr-2"></i>
                        Главная
                    </a>
                    
                    <!-- Навигация модуля auth для мобильных -->
                    <?php 
                    $isAuthenticated = isAuthenticated();
                    $user = $isAuthenticated ? getCurrentUser() : null;
                    
                    if ($isAuthenticated && $user): ?>
                        <a href="/dashboard" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="ri-dashboard-line mr-2"></i>
                            Dashboard
                        </a>
                        
                        <?php if (hasRole('admin')): ?>
                            <a href="/admin" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                <i class="ri-admin-line mr-2"></i>
                                Админ панель
                            </a>
                        <?php endif; ?>

                        <div class="px-3 py-2 border-t border-gray-200 dark:border-gray-700 mt-2 pt-2">
                            <div class="flex items-center space-x-2 mb-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-md">
                                    <i class="ri-user-line text-white text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($user['username']) ?></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400"><?= ucfirst($user['role']) ?></div>
                                </div>
                            </div>
                            
                            <a href="#" class="open_logout block bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-sm font-medium text-center cursor-pointer">
                                <i class="ri-logout-box-line mr-2"></i>
                                Выйти
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="#" class="open_modal_login_form text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors cursor-pointer">
                            <i class="ri-account-circle-line mr-2"></i>
                            Аккаунт
                        </a>

                        <div class="flex flex-col space-y-2 px-3 pt-2 border-t border-gray-200 dark:border-gray-700 mt-2">
                            <a href="#" class="open_modal_login_form bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all text-sm font-medium text-center cursor-pointer">
                                <i class="ri-login-box-line mr-2"></i>
                                Войти
                            </a>
                            
                            <a href="#" class="open_modal_register_form bg-gradient-to-r from-green-500 to-teal-600 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-teal-700 transition-all text-sm font-medium text-center cursor-pointer">
                                <i class="ri-user-add-line mr-2"></i>
                                Регистрация
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <script>
        // Мобильное меню - только переключение Tailwind классов
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            const icon = this.querySelector('i');
            
            if (menu.classList.contains('max-h-0')) {
                // Открываем
                menu.classList.remove('max-h-0', 'opacity-0');
                menu.classList.add('max-h-96', 'opacity-100');
                if (icon) icon.className = 'ri-close-line text-2xl';
            } else {
                // Закрываем
                menu.classList.remove('max-h-96', 'opacity-100');
                menu.classList.add('max-h-0', 'opacity-0');
                if (icon) icon.className = 'ri-menu-line text-2xl';
            }
        });
    </script>
    <?php
}

