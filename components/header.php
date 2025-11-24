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
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Логотип и название -->
                <div class="flex items-center space-x-3">
                    <?php if ($displayTitle): ?>
                        <!-- Заголовок страницы (для dashboard/admin) -->
                        <div class="flex items-center space-x-3">
                            <div class="gradient-bg w-10 h-10 rounded-lg flex items-center justify-center shadow-md">
                                <i class="<?= htmlspecialchars($displayIcon ?? 'ri-dashboard-line') ?> text-white text-xl"></i>
                            </div>
                            <span class="text-xl font-bold text-gray-900"><?= htmlspecialchars($displayTitle) ?></span>
                        </div>
                    <?php else: ?>
                        <!-- Логотип сайта -->
                        <a href="/" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                            <div class="gradient-bg w-10 h-10 rounded-lg flex items-center justify-center shadow-md">
                                <i class="ri-shield-user-line text-white text-xl"></i>
                            </div>
                            <span class="text-xl font-bold text-gray-900">Auth System</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Навигация -->
                <div class="flex items-center space-x-4">
                    <!-- Общая навигация сайта -->
                    <a href="/" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="ri-home-line mr-1"></i>
                        Главная
                    </a>
                    
                    <!-- Навигация модуля auth -->
                    <?php renderAuthNavigation(); ?>
                </div>
            </div>
        </nav>
    </header>
    <?php
}

