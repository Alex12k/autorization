<?php
setPageTitle('404 - Страница не найдена');
?>

<div class="min-h-screen authorization-ajax-container flex items-center justify-center bg-gradient-to-br from-purple-50 to-blue-50 px-4">
    <div class="max-w-md w-full text-center">
        <!-- Иконка 404 -->
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-red-500 to-pink-600 rounded-full shadow-2xl">
                <i class="ri-error-warning-line text-6xl text-white"></i>
            </div>
        </div>

        <!-- Заголовок -->
        <h1 class="text-6xl font-bold text-gray-900 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Страница не найдена</h2>
        
        <!-- Описание -->
        <p class="text-gray-600 mb-8">
            К сожалению, запрашиваемая страница не существует или была перемещена.
        </p>

        <!-- Действия -->
        <div class="space-y-3">
            <a href="/" class="block w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                <i class="ri-home-line mr-2"></i>
                Вернуться на главную
            </a>
            
            <?php if (isAuthenticated()): ?>
                <a href="/dashboard" class="block w-full bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                    <i class="ri-dashboard-line mr-2"></i>
                    Перейти в Dashboard
                </a>
            <?php else: ?>
                <a href="#" class="open_login block w-full bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors cursor-pointer">
                    <i class="ri-login-box-line mr-2"></i>
                    Войти в систему
                </a>
            <?php endif; ?>
        </div>
        

        <!-- Дополнительная информация -->
        <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
                <i class="ri-information-line mr-1"></i>
                Если вы считаете, что это ошибка, пожалуйста, свяжитесь с администратором.
            </p>
        </div>
    </div>
</div>


