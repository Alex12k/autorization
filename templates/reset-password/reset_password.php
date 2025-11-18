<?php
/**
 * Шаблон страницы reset password
 * Универсальная функция для обработки сброса пароля
 */

// Загрузка зависимостей (если файл вызывается напрямую)
if (!defined('SYSTEM_INITIALIZED')) {
    require_once __DIR__ . '/../../config.php';
    require_once __DIR__ . '/../../functions/functions.php';
    require_once __DIR__ . '/../../functions/layout.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    define('SYSTEM_INITIALIZED', true);
}

/**
 * Универсальная функция обработки сброса пароля
 * Обрабатывает как загрузку формы, так и отправку данных
 */
function resetPassword(): void
{
    // Если пользователь уже авторизован, перенаправляем на dashboard
    if (isAuthenticated()) {
        redirect('dashboard');
        exit;
    }

    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    $reset_success = false;
    $reset_error = null;
    $token_validation = null;

    // Проверка токена при загрузке страницы
    if (!empty($token)) {
        $token_validation = validateResetToken($token);
        if (!$token_validation['success']) {
            $reset_error = $token_validation['error'];
            $token = ''; // Сбрасываем токен если он невалидный
        }
    } else {
        $reset_error = 'Токен восстановления не указан. Пожалуйста, используйте ссылку из письма.';
    }

    // Обработка отправки формы сброса пароля
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset-password') {
        $csrf_token = $_POST['csrf_token'] ?? '';
        $token = $_POST['token'] ?? $token;
        $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_POST['ajax']) && $_POST['ajax'] === '1');

        if (!verifyCSRFToken($csrf_token)) {
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Ошибка безопасности. Попробуйте еще раз.']);
                exit;
            }
            $reset_error = 'Ошибка безопасности. Попробуйте еще раз.';
        } else {
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            $result = resetPasswordWithToken($token, $new_password, $confirm_password);

            if ($result['success']) {
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Пароль успешно изменен! Теперь вы можете войти в систему.']);
                    exit;
                }
                $reset_success = true;
            } else {
                if ($is_ajax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $result['error']]);
                    exit;
                }
                $reset_error = $result['error'];
                if (!empty($token)) {
                    $token_validation = validateResetToken($token);
                }
            }
        }
    }

    // Генерация CSRF токена для формы
    $csrf_token = generateCSRFToken();
    setPageTitle('Сброс пароля');
    
    // Отображение формы сброса пароля
    ?>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Заголовок -->
            <div class="text-center">
                <div class="gradient-bg w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-shield-keyhole-line text-3xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Сброс пароля</h2>
                <?php if ($token_validation && $token_validation['success']): ?>
                    <p class="text-gray-600">
                        Установите новый пароль для 
                        <span class="font-semibold text-blue-600"><?= htmlspecialchars($token_validation['username']) ?></span>
                    </p>
                <?php else: ?>
                    <p class="text-gray-600">Установите новый пароль</p>
                <?php endif; ?>
            </div>

            <?php if ($reset_success): ?>
                <!-- Успешный сброс пароля -->
                <div class="bg-white rounded-lg shadow-xl p-8">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-check-line text-4xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Пароль успешно изменен!</h3>
                        <p class="text-gray-600">
                            Теперь вы можете войти в систему с новым паролем
                        </p>
                    </div>

                    <a href="/" class="open_login block w-full bg-gradient-to-r from-green-500 to-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 text-center cursor-pointer">
                        <i class="ri-login-box-line mr-2"></i>
                        Войти в систему
                    </a>
                </div>
            <?php elseif ($token && $token_validation && $token_validation['success']): ?>
                <!-- Форма сброса пароля -->
                <div class="bg-white rounded-lg shadow-xl p-8 form-focus transition-all duration-300">
                    <?php if ($reset_error): ?>
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="ri-error-warning-line text-red-500 mr-2"></i>
                                <span class="text-red-700"><?= htmlspecialchars($reset_error) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-6" data-action="reset-password" id="resetPasswordForm">
                        <input type="hidden" name="action" value="reset-password">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                        <!-- Информация о пользователе -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="ri-user-line text-blue-500 text-lg mr-2 mt-0.5"></i>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium">Аккаунт:</p>
                                    <p class="text-xs mt-1">
                                        <?= htmlspecialchars($token_validation['email']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Новый пароль -->
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="ri-lock-line mr-1"></i>
                                Новый пароль
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="new_password" 
                                    name="new_password" 
                                    required
                                    autofocus
                                    minlength="6"
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Введите новый пароль (минимум 6 символов)"
                                >
                                <button 
                                    type="button"
                                    onclick="togglePassword('new_password', 'toggle-new-password-icon')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                                    title="Показать/скрыть пароль"
                                >
                                    <i id="toggle-new-password-icon" class="ri-eye-line text-xl"></i>
                                </button>
                            </div>
                            <div id="password-strength" class="mt-2 hidden">
                                <div class="flex items-center space-x-1">
                                    <div class="flex-1 h-1 rounded" id="strength-bar-1"></div>
                                    <div class="flex-1 h-1 rounded" id="strength-bar-2"></div>
                                    <div class="flex-1 h-1 rounded" id="strength-bar-3"></div>
                                    <div class="flex-1 h-1 rounded" id="strength-bar-4"></div>
                                </div>
                                <p id="strength-text" class="text-xs mt-1"></p>
                            </div>
                        </div>

                        <!-- Подтверждение пароля -->
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="ri-lock-password-line mr-1"></i>
                                Подтвердите пароль
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    required
                                    minlength="6"
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="Повторите новый пароль"
                                >
                                <button 
                                    type="button"
                                    onclick="togglePassword('confirm_password', 'toggle-confirm-password-icon')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                                    title="Показать/скрыть пароль"
                                >
                                    <i id="toggle-confirm-password-icon" class="ri-eye-line text-xl"></i>
                                </button>
                            </div>
                            <p id="password-match-message" class="text-xs mt-1 hidden"></p>
                        </div>

                        <!-- Требования к паролю -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-yellow-800 mb-2">
                                <i class="ri-information-line mr-1"></i>
                                Требования к паролю:
                            </h3>
                            <ul class="text-xs text-yellow-700 space-y-1">
                                <li id="req-length" class="flex items-center">
                                    <i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>
                                    Минимум 6 символов
                                </li>
                                <li id="req-match" class="flex items-center">
                                    <i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>
                                    Пароли должны совпадать
                                </li>
                            </ul>
                        </div>

                        <!-- Кнопка сброса -->
                        <button 
                            type="submit"
                            id="submitBtn"
                            class="w-full bg-gradient-to-r from-green-500 to-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <i class="ri-key-line mr-2"></i>
                            Установить новый пароль
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Ошибка токена -->
                <div class="bg-white rounded-lg shadow-xl p-8">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-error-warning-line text-4xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Ошибка токена</h3>
                        <p class="text-gray-600 mb-4">
                            <?= htmlspecialchars($reset_error) ?>
                        </p>
                    </div>

                    <div class="space-y-3">
                        <a href="#" class="open_forgot-password block w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 text-center cursor-pointer">
                            <i class="ri-mail-send-line mr-2"></i>
                            Запросить новую ссылку
                        </a>
                        <a href="#" class="open_login block w-full bg-gray-300 text-gray-700 py-3 px-4 rounded-lg font-semibold hover:bg-gray-400 transition-colors text-center cursor-pointer">
                            <i class="ri-arrow-left-line mr-2"></i>
                            Вернуться к входу
                        </a>
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

    <script>
        // Переключение видимости пароля
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
            }
        }

        // Проверка силы пароля
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('password-strength');
            const strengthText = document.getElementById('strength-text');

            if (password.length === 0) {
                strengthBar.classList.add('hidden');
                return;
            }

            strengthBar.classList.remove('hidden');

            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;

            // Обновление индикатора
            const bars = [1, 2, 3, 4].map(i => document.getElementById(`strength-bar-${i}`));
            bars.forEach(bar => bar.className = 'flex-1 h-1 rounded bg-gray-200');

            if (strength <= 2) {
                bars.slice(0, 1).forEach(bar => bar.classList.replace('bg-gray-200', 'bg-red-500'));
                strengthText.textContent = 'Слабый пароль';
                strengthText.className = 'text-xs mt-1 text-red-600';
            } else if (strength <= 3) {
                bars.slice(0, 2).forEach(bar => bar.classList.replace('bg-gray-200', 'bg-yellow-500'));
                strengthText.textContent = 'Средний пароль';
                strengthText.className = 'text-xs mt-1 text-yellow-600';
            } else {
                bars.forEach(bar => bar.classList.replace('bg-gray-200', 'bg-green-500'));
                strengthText.textContent = 'Надежный пароль';
                strengthText.className = 'text-xs mt-1 text-green-600';
            }
        }

        // Проверка совпадения паролей
        function checkPasswordMatch() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchMessage = document.getElementById('password-match-message');
            const reqLength = document.getElementById('req-length');
            const reqMatch = document.getElementById('req-match');
            const submitBtn = document.getElementById('submitBtn');

            // Проверка длины
            if (newPassword.length >= 6) {
                reqLength.innerHTML = '<i class="ri-checkbox-circle-line text-xs mr-2 text-green-600"></i>Минимум 6 символов';
                reqLength.classList.add('text-green-600');
            } else {
                reqLength.innerHTML = '<i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>Минимум 6 символов';
                reqLength.classList.remove('text-green-600');
            }

            // Проверка совпадения
            if (confirmPassword.length > 0) {
                matchMessage.classList.remove('hidden');

                if (newPassword === confirmPassword) {
                    matchMessage.textContent = '✓ Пароли совпадают';
                    matchMessage.className = 'text-xs mt-1 text-green-600';
                    reqMatch.innerHTML = '<i class="ri-checkbox-circle-line text-xs mr-2 text-green-600"></i>Пароли должны совпадать';
                    reqMatch.classList.add('text-green-600');
                    submitBtn.disabled = newPassword.length < 6;
                } else {
                    matchMessage.textContent = '✗ Пароли не совпадают';
                    matchMessage.className = 'text-xs mt-1 text-red-600';
                    reqMatch.innerHTML = '<i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>Пароли должны совпадать';
                    reqMatch.classList.remove('text-green-600');
                    submitBtn.disabled = true;
                }
            } else {
                matchMessage.classList.add('hidden');
                reqMatch.innerHTML = '<i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>Пароли должны совпадать';
                reqMatch.classList.remove('text-green-600');
                submitBtn.disabled = true;
            }
        }

        // Добавление обработчиков событий
        document.addEventListener('DOMContentLoaded', function() {
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('confirm_password');

            if (newPasswordInput) {
                newPasswordInput.addEventListener('input', function() {
                    checkPasswordStrength(this.value);
                    checkPasswordMatch();
                });
            }

            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', checkPasswordMatch);
            }
        });
    </script>
    <?php
}

// Единая точка входа для всех запросов
// Определяем действие и вызываем универсальную функцию
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // Загрузка формы или обработка данных - все через одну функцию
    if ($action === 'open_reset-password' || $action === 'reset-password') {
        resetPassword();
        exit;
    }
}

