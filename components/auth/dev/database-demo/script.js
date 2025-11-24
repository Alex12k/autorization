/**
 * Модуль обработки демонстрации PostgreSQL
 */

// Вызов демонстрации PostgreSQL по клику (открывает модальное окно)
$(document).on('click', '.open_modal_database_demo', function(e) {
    e.preventDefault();
    
    // Закрываем все открытые модальные окна перед открытием нового
    $.arcticmodal('close');
    
    $.post('/components/auth/dev/database-demo/ajax/ajax.php', {action: 'open_modal_database_demo'}, function(res) {
        $(res).arcticmodal({closeOnOverlayClick: false});
    });
});

console.log('Database demo module initialized');

