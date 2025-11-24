<?php
/**
 * Точка входа компонента auth
 * Инициализирует систему и подключает все необходимые компоненты
 */

// Инициализация системы
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Запуск сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключаем компоненты auth
require_once __DIR__ . '/dashboard/dashboard.php';
require_once __DIR__ . '/admin/admin.php';
require_once __DIR__ . '/navigation.php';
require_once __DIR__ . '/../header.php';

/**
 * Функция для вывода всех скриптов компонента auth
 */
function auth_scripts() {
    ?>
<script src="/components/auth/utils/form-utils.js"></script>
<script src="/components/auth/utils/toast.js"></script>
<script src="/components/auth/utils/animations.js"></script>
<script src="/components/auth/login/script.js"></script>
<script src="/components/auth/register/script.js"></script>
<script src="/components/auth/password-recovery/script.js"></script>
<script src="/components/auth/change-password/script.js"></script>
<script src="/components/auth/edit-profile/script.js"></script>
<script src="/components/auth/logout/script.js"></script>
<script src="/components/auth/admin/script.js"></script>
<script src="/components/auth/dev/check-session/script.js"></script>
<script src="/components/auth/dev/database-demo/script.js"></script>
<script src="/components/auth/dev/php-demo/script.js"></script>
<?php
}