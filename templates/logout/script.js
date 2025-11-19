/**
 * Модуль обработки выхода из системы
 */


// Вызов выхода из системы по клику
$(document).on('click', '.open_logout', function(e) {
    e.preventDefault();
    
    $.post('/templates/logout/ajax/ajax.php', {action: 'logout', ajax: '1'}, function(res) {
        console.log('Ответ сервера:', res);
        
        // jQuery автоматически парсит JSON, проверяем тип
        if (typeof res === 'object' && res !== null) {
            // Это уже объект (JSON распарсен автоматически)
            if (res.success) {
                // Успешный выход - редирект
                let redirectUrl = res.redirect || '/';
                console.log('Редирект на:', redirectUrl);
                window.location.href = redirectUrl;
            } else {
                // Ошибка - обновляем страницу
                console.error('Ошибка выхода:', res.error);
                window.location.reload();
            }
        } else {
            // Это строка (HTML) или другой формат - обновляем страницу
            window.location.reload();
        }
    });
});

console.log('Logout module initialized');

