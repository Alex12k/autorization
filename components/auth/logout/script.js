/**
 * Модуль обработки выхода из системы
 */


// Вызов выхода из системы по клику
$(document).on('click', '.open_logout', function(e) {
    e.preventDefault();
    
    $.post('/components/auth/logout/ajax/ajax.php', {action: 'logout', ajax: '1'}, function(res) {
        if (res && res.success) {
            window.location.href = res.redirect || '/';
        } else {
            window.location.reload();
        }
    });
});

console.log('Logout module initialized');

