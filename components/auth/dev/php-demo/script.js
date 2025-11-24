/**
 * Модуль обработки демонстрации PHP 8.4
 */

// Вызов демонстрации PHP по клику (открывает модальное окно)
$(document).on('click', '.open_modal_php_demo', function(e) {
    e.preventDefault();
    
    // Закрываем все открытые модальные окна перед открытием нового
    $.arcticmodal('close');
    
    $.post('/components/auth/dev/php-demo/ajax/ajax.php', {action: 'open_modal_php_demo'}, function(res) {
        $(res).arcticmodal({closeOnOverlayClick: false});
    });
});

console.log('PHP demo module initialized');

