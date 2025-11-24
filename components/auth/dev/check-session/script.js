/**
 * Модуль обработки диагностики сессии
 */

// Вызов диагностики сессии по клику (открывает модальное окно)
$(document).on('click', '.open_modal_check_session', function(e) {
    e.preventDefault();
    
    // Закрываем все открытые модальные окна перед открытием нового
    $.arcticmodal('close');
    
    $.post('/components/auth/dev/check-session/ajax/ajax.php', {action: 'open_modal_check_session'}, function(res) {
        $(res).arcticmodal({closeOnOverlayClick: false});
    });
});

// Обработка очистки сессии
$(document).on('click', '.clear_session_btn', function(e) {
    e.preventDefault();
    
    if (!confirm('Вы уверены, что хотите очистить сессию и все cookies? Это приведет к выходу из системы.')) {
        return;
    }
    
    let $btn = $(this);
    $btn.prop('disabled', true).html('<i class="ri-loader-4-line mr-2 animate-spin"></i>Очистка...');
    
    $.post('/components/auth/dev/check-session/ajax/ajax.php', {action: 'clear_session'}, function(res) {
        if (res && res.success) {
            // Закрываем модальное окно
            $.arcticmodal('close');
            // Редирект на главную страницу (не на /login)
            window.location.href = '/';
        } else {
            alert(res.error || 'Ошибка при очистке сессии');
            $btn.prop('disabled', false).html('<i class="ri-delete-bin-line mr-2"></i>Очистить сессию');
        }
    });
});

// Обработка обновления данных сессии
$(document).on('click', '.refresh_session_btn', function(e) {
    e.preventDefault();
    
    let $btn = $(this);
    $btn.prop('disabled', true).html('<i class="ri-loader-4-line mr-2 animate-spin"></i>Обновление...');
    
    // Закрываем текущее модальное окно
    $.arcticmodal('close');
    
    // Открываем заново для обновления данных
    setTimeout(function() {
        $.post('/components/auth/dev/check-session/ajax/ajax.php', {action: 'open_modal_check_session'}, function(res) {
            $(res).arcticmodal({closeOnOverlayClick: false});
        });
    }, 100);
});

console.log('Check session module initialized');

