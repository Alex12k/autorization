<?php
/**
 * Шаблон страницы forgot password
 */

function forgotPassword(): void
{
    // Если пользователь уже авторизован, перенаправляем на dashboard
    if (isAuthenticated()) {
        redirect('dashboard');
        exit;
    }

    $reset_success = null;
    $reset_error = null;
    $reset_token = null;
    $reset_email = null;

    // Обработка запроса на восстановление пароля
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrf_token = $_POST['csrf_token'] ?? '';

        if (!verifyCSRFToken($csrf_token)) {
            $reset_error = 'Ошибка безопасности. Попробуйте еще раз.';
        } else {
            $email = trim($_POST['email'] ?? '');

            $result = requestPasswordReset($email);

            if ($result['success']) {
                $reset_success = true;
                $reset_token = $result['token'] ?? null;
                $reset_email = $result['email'] ?? null;
            } else {
                $reset_error = $result['error'];
            }
        }
    }

    $csrf_token = generateCSRFToken();
    setPageTitle('Восстановление пароля');
    ?>
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <!-- Заголовок -->
                <div class="text-center">
                    <div class="gradient-bg w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="ri-lock-password-line text-3xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Восстановление пароля</h2>
                    <p class="text-gray-600">Введите email адрес вашей учетной записи</p>
                </div>

                <?php if ($reset_success && $reset_token): ?>
                    <!-- Успешное создание токена (для демо целей показываем токен) -->
                    <div class="bg-white rounded-lg shadow-xl p-8">
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="ri-check-line text-green-500 text-xl mr-2 mt-0.5"></i>
                                <div>
                                    <span class="text-green-700 font-medium">Токен восстановления создан!</span>
                                    <p class="text-sm text-green-600 mt-1">
                                        В реальной системе ссылка будет отправлена на <?= htmlspecialchars($reset_email) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Демо: показываем ссылку для восстановления -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h3 class="text-sm font-semibold text-blue-800 mb-2 flex items-center">
                                <i class="ri-information-line mr-2"></i>
                                Демо режим
                            </h3>
                            <p class="text-xs text-blue-700 mb-3">
                                В production система отправит email со ссылкой. Для демонстрации используйте эту ссылку:
                            </p>
                            <div class="bg-white rounded p-3 mb-3">
                                <code class="text-xs break-all text-blue-900">
                                    <?= htmlspecialchars($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']) ?>/reset_password.php?token=<?= htmlspecialchars($reset_token) ?>
                                </code>
                            </div>
                            <a href="reset_password.php?token=<?= htmlspecialchars($reset_token) ?>" 
                               class="block w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors text-center font-semibold">
                                <i class="ri-key-line mr-2"></i>
                                Сбросить пароль
                            </a>
                        </div>

                        <div class="text-sm text-gray-600">
                            <i class="ri-time-line mr-1"></i>
                            Срок действия ссылки: 1 час
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Форма запроса восстановления -->
                    <div class="bg-white rounded-lg shadow-xl p-8 form-focus transition-all duration-300">
                        <?php if ($reset_error): ?>
                            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="ri-error-warning-line text-red-500 mr-2"></i>
                                    <span class="text-red-700"><?= htmlspecialchars($reset_error) ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="ri-mail-line mr-1"></i>
                                    Email адрес
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    required
                                    autofocus
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Введите ваш email"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                >
                            </div>

                            <!-- Информация -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="ri-information-line text-blue-500 text-lg mr-2 mt-0.5"></i>
                                    <div class="text-sm text-blue-700">
                                        <p class="font-medium mb-1">Что произойдет дальше?</p>
                                        <ul class="space-y-1 text-xs">
                                            <li>• Вы получите ссылку для сброса пароля</li>
                                            <li>• Ссылка будет действительна 1 час</li>
                                            <li>• Ваш текущий пароль останется активным</li>
                                            <li>• После сброса старый пароль перестанет работать</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Кнопка отправки -->
                            <button 
                                type="submit"
                                class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105"
                            >
                                <i class="ri-mail-send-line mr-2"></i>
                                Отправить ссылку для восстановления
                            </button>
                        </form>

                        <!-- Разделитель -->
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">или</span>
                            </div>
                        </div>

                        <!-- Ссылки -->
                        <div class="text-center space-y-2">
                            <p class="text-gray-600">
                                Вспомнили пароль? 
                                <a href="<?= url('login') ?>" class="text-blue-600 hover:text-blue-700 font-semibold">
                                    Войти
                                </a>
                            </p>
                            <p class="text-gray-600">
                                Нет учетной записи? 
                                <a href="<?= url('register') ?>" class="text-blue-600 hover:text-blue-700 font-semibold">
                                    Зарегистрироваться
                                </a>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Ссылка на главную -->
                <div class="text-center">
                    <a href="<?= url() ?>" class="text-gray-500 hover:text-gray-700 text-sm">
                        <i class="ri-arrow-left-line mr-1"></i>
                        Вернуться на главную
                    </a>
                </div>
            </div>
        </div>
    <?php
}
