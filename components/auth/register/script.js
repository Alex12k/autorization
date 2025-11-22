/**
 * Модуль обработки регистрации
 */


/**
 * Переключение видимости пароля для формы регистрации
 * Управляет двумя полями пароля одновременно
 */
function togglePasswordRegister(inputId, iconId) {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordIcon = document.getElementById('toggle-password-icon');
    const confirmPasswordIcon = document.getElementById('toggle-confirm-password-icon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        confirmPasswordInput.type = 'text';
        passwordIcon.classList.remove('ri-eye-line');
        passwordIcon.classList.add('ri-eye-off-line');
        confirmPasswordIcon.classList.remove('ri-eye-line');
        confirmPasswordIcon.classList.add('ri-eye-off-line');
    } else {
        passwordInput.type = 'password';
        confirmPasswordInput.type = 'password';
        passwordIcon.classList.remove('ri-eye-off-line');
        passwordIcon.classList.add('ri-eye-line');
        confirmPasswordIcon.classList.remove('ri-eye-off-line');
        confirmPasswordIcon.classList.add('ri-eye-line');
    }
}

// Вызов формы регистрации по клику
$(document).on('click', '.open_register', function() {
    $.post('/components/auth/register/ajax/ajax.php', {action: 'open_register', ajax: '1'}, function(res) {
        console.log(res);
        $('.authorization-ajax-container').html(res);
    });
});



// Обработка отправки формы регистрации через AJAX
$(document).on('submit', '.authorization-ajax-container form[data-action="register"]', function(e){
    e.preventDefault();
    
    let form = $(this);
    let formData = form.serialize() + '&ajax=1';
    
    $.post('/components/auth/register/ajax/ajax.php', formData, function(res) {
        console.log('Ответ сервера:', res);
        
        // jQuery автоматически парсит JSON, проверяем тип
        if (typeof res === 'object' && res !== null) {
            // Это уже объект (JSON распарсен автоматически)
            if (res.success) {
                // Сразу открываем форму логина
                $('.open_login').trigger('click');
            } else {
                // Ошибка - показываем сообщение
                window.AuthFormUtils.showError(form, res.error || 'Ошибка регистрации');
            }
        } else {
            // Это строка (HTML) - обновляем контейнер
            $('.authorization-ajax-container').html(res);
        }
    });
});

console.log('Register module initialized');
