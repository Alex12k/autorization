/**
 * Модуль обработки логина
 */

/**
 * Переключение видимости пароля
 */
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

// Вызов формы логина по клику (открывает модальное окно)
$(document).on('click', '.open_modal_login_form', function(e) {
    e.preventDefault();
    
    // Закрываем все открытые модальные окна перед открытием нового
    $.arcticmodal('close');
    
    $.post('/components/auth/login/ajax/ajax.php', {action: 'open_modal_login_form'}, function(res) {
        $(res).arcticmodal({closeOnOverlayClick: false});
    });
});

// Обработка отправки формы логина из модального окна
$(document).on('submit', '.login-modal form[data-action="login"]', function(e){
    e.preventDefault();
    
    let form = $(this);
    let formData = form.serialize() + '&ajax=1';
    
    $.post('/components/auth/login/ajax/ajax.php', formData, function(res) {
        console.log('Ответ сервера:', res);
        
        // jQuery автоматически парсит JSON, проверяем тип
        if (typeof res === 'object' && res !== null) {
            // Это уже объект (JSON распарсен автоматически)
            if (res.success) {
                // Закрываем модальное окно
                $('.arcticmodal-container').arcticmodal('close');
                // Успешный вход - редирект
                let redirectUrl = res.redirect || '/dashboard';
                console.log('Редирект на:', redirectUrl);
                window.location.href = redirectUrl;
            } else {
                // Ошибка - показываем сообщение
                window.AuthFormUtils.showError(form, res.error || 'Ошибка входа');
            }
        }
    });
});

console.log('Login module initialized');