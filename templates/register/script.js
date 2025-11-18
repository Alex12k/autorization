/**
 * Модуль обработки регистрации
 */


// Вызов формы регистрации по клику
$(document).on('click', '.open_register', function() {
    $.post('/templates/register/register.php', {action: 'open_register'}, function(res) {
        console.log(res);
        $('.authorization-ajax-container').html(res);
    });
});



// Обработка отправки формы регистрации через AJAX
$(document).on('submit', '.authorization-ajax-container form[data-action="register"]', function(e){
    e.preventDefault();
    
    let form = $(this);
    let formData = form.serialize() + '&ajax=1';
    
    $.post('/templates/register/register.php', formData, function(res) {
        console.log('Ответ сервера:', res);
        
        // jQuery автоматически парсит JSON, проверяем тип
        if (typeof res === 'object' && res !== null) {
            // Это уже объект (JSON распарсен автоматически)
            if (res.success) {
                // Показываем сообщение об успехе
                window.AuthAjaxHandler.showSuccess(form, res.message || 'Регистрация успешна! Теперь вы можете войти.');
                
                // Очищаем форму
                form[0].reset();
                
                // Загружаем форму логина через небольшую задержку
                setTimeout(function() {
                    $('.open_login').trigger('click');
                }, 1500);
            } else {
                // Ошибка - показываем сообщение
                window.AuthAjaxHandler.showError(form, res.error || 'Ошибка регистрации');
            }
        } else {
            // Это строка (HTML) - обновляем контейнер
            $('.authorization-ajax-container').html(res);
        }
    });
});

console.log('Register module initialized');
