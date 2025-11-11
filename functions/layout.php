<?php
/**
 * Функции для работы с layout (шаблоном страницы)
 * Header и Footer как функции вместо отдельных файлов
 */

/**
 * Глобальные переменные для layout
 */
$GLOBALS['page_title'] = 'PHP 8.4 Система';
$GLOBALS['additional_styles'] = '';
$GLOBALS['footer_text'] = 'Создано с PHP 8.4 и Tailwind CSS';

/**
 * Установка заголовка страницы
 * @param string $title Заголовок страницы
 */
function setPageTitle(string $title): void
{
    $GLOBALS['page_title'] = $title;
}

/**
 * Добавление дополнительных стилей
 * @param string $styles CSS стили
 */
function addStyles(string $styles): void
{
    $GLOBALS['additional_styles'] .= $styles;
}

/**
 * Установка текста футера
 * @param string $text Текст футера
 */
function setFooterText(string $text): void
{
    $GLOBALS['footer_text'] = $text;
}

/**
 * Отрисовка header (шапки сайта)
 */
function renderHeader(): void
{
    $page_title = $GLOBALS['page_title'] ?? 'PHP 8.4 Система';
    $additional_styles = $GLOBALS['additional_styles'] ?? '';
    ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .form-focus:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        <?= $additional_styles ?>
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<?php
}

/**
 * Отрисовка footer (подвала сайта)
 */
function renderFooter(): void
{
    $footer_text = $GLOBALS['footer_text'] ?? 'Создано с PHP 8.4 и Tailwind CSS';
    ?>
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-300">
                <i class="ri-heart-line text-red-500"></i>
                <?= htmlspecialchars($footer_text) ?>
            </p>
        </div>
    </footer>
</body>
</html>
<?php
}

/**
 * Отрисовка полного layout (header + контент + footer)
 * @param callable $content Функция, которая выводит контент страницы
 */
function renderLayout(callable $content): void
{
    renderHeader();
    $content();
    renderFooter();
}

