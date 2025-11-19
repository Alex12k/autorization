<?php

setPageTitle('Проверка сессии');
?>
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">
                <i class="ri-bug-line text-blue-600"></i>
                Диагностика сессии
            </h1>

            <!-- Статус аутентификации -->
            <div class="mb-6 p-4 <?= isAuthenticated() ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' ?> border rounded-lg">
                <h2 class="text-xl font-semibold mb-3">Статус аутентификации</h2>
                <?php if (isAuthenticated()): ?>
                    <p class="text-green-700">
                        <i class="ri-check-circle-fill mr-2"></i>
                        <strong>Вы авторизованы</strong>
                    </p>
                <?php else: ?>
                    <p class="text-gray-700">
                        <i class="ri-close-circle-line mr-2"></i>
                        <strong>Вы НЕ авторизованы</strong>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Данные сессии -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-3">Данные сессии</h2>
                <div class="bg-gray-50 p-4 rounded-lg overflow-x-auto">
                    <pre class="text-sm"><?php print_r($_SESSION); ?></pre>
                </div>
            </div>

            <!-- Текущий пользователь -->
            <?php if (isAuthenticated()): ?>
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h2 class="text-xl font-semibold mb-3">Текущий пользователь</h2>
                    <?php $user = getCurrentUser(); ?>
                    <?php if ($user): ?>
                        <ul class="space-y-2">
                            <li><strong>ID:</strong> <?= htmlspecialchars($user['id']) ?></li>
                            <li><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></li>
                            <li><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
                            <li><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></li>
                            <li><strong>Создан:</strong> <?= htmlspecialchars($user['created_at']) ?></li>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Действия -->
            <div class="flex gap-4 flex-wrap">
                <a href="<?= url() ?>" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="ri-home-line mr-2"></i>
                    На главную
                </a>
                
                <?php if (isAuthenticated()): ?>
                    <a href="<?= url('dashboard') ?>" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors">
                        <i class="ri-dashboard-line mr-2"></i>
                        Dashboard
                    </a>
                    
                    <a href="<?= url('logout') ?>" class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition-colors">
                        <i class="ri-logout-box-line mr-2"></i>
                        Выйти
                    </a>
                <?php else: ?>
                    <a href="#" class="open_login bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors cursor-pointer">
                        <i class="ri-login-box-line mr-2"></i>
                        Войти
                    </a>
                    
                    <a href="#" class="open_register bg-purple-500 text-white px-6 py-3 rounded-lg hover:bg-purple-600 transition-colors cursor-pointer">
                        <i class="ri-user-add-line mr-2"></i>
                        Регистрация
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Контейнер для асинхронной загрузки форм авторизации -->
            <div class="authorization-ajax-container mt-8"></div>

            <!-- Инструкции -->
            <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h3 class="font-semibold text-yellow-800 mb-2">
                    <i class="ri-information-line mr-2"></i>
                    Инструкции
                </h3>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Если вы авторизованы, но хотите зарегистрировать нового пользователя - сначала нажмите "Выйти"</li>
                    <li>• После выхода вы сможете перейти на страницу регистрации</li>
                    <li>• Это нормальное поведение - авторизованные пользователи не могут регистрировать новые аккаунты</li>
                </ul>
            </div>
        </div>
    </div>
</div>

