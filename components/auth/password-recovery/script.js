/**
 * Модуль обработки восстановления пароля
 * Объединенный модуль для запроса и сброса пароля
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

/**
 * Проверка силы пароля (универсальная для обычной и модальной формы)
 */
function checkPasswordStrength(password, isModal = false) {
    const prefix = isModal ? 'modal-' : '';
    const strengthBar = document.getElementById(prefix + 'password-strength');
    const strengthText = document.getElementById(prefix + 'strength-text');

    if (!strengthBar || !strengthText) return;

    if (password.length === 0) {
        strengthBar.classList.add('hidden');
        return;
    }

    strengthBar.classList.remove('hidden');

    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z\d]/.test(password)) strength++;

    const bars = [1, 2, 3, 4].map(i => document.getElementById(prefix + `strength-bar-${i}`));
    bars.forEach(bar => {
        if (bar) bar.className = 'flex-1 h-1 rounded bg-gray-200';
    });

    if (strength <= 2) {
        bars.slice(0, 1).forEach(bar => {
            if (bar) bar.classList.replace('bg-gray-200', 'bg-red-500');
        });
        strengthText.textContent = 'Слабый пароль';
        strengthText.className = 'text-xs mt-1 text-red-600';
    } else if (strength <= 3) {
        bars.slice(0, 2).forEach(bar => {
            if (bar) bar.classList.replace('bg-gray-200', 'bg-yellow-500');
        });
        strengthText.textContent = 'Средний пароль';
        strengthText.className = 'text-xs mt-1 text-yellow-600';
    } else {
        bars.forEach(bar => {
            if (bar) bar.classList.replace('bg-gray-200', 'bg-green-500');
        });
        strengthText.textContent = 'Надежный пароль';
        strengthText.className = 'text-xs mt-1 text-green-600';
    }
}

/**
 * Проверка совпадения паролей (универсальная для обычной и модальной формы)
 */
function checkPasswordMatch(isModal = false) {
    const prefix = isModal ? 'modal_reset_' : '';
    const newPasswordInput = document.getElementById(prefix + 'new_password');
    const confirmPasswordInput = document.getElementById(prefix + 'confirm_password');
    
    if (!newPasswordInput || !confirmPasswordInput) return;
    
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    const matchMessage = document.getElementById(isModal ? 'modal-password-match-message' : 'password-match-message');
    const reqLength = document.getElementById(isModal ? 'modal-req-length' : 'req-length');
    const reqMatch = document.getElementById(isModal ? 'modal-req-match' : 'req-match');
    const submitBtn = document.getElementById(isModal ? 'modal-submitBtn' : 'submitBtn');

    if (reqLength) {
        if (newPassword.length >= 6) {
            reqLength.innerHTML = '<i class="ri-checkbox-circle-line text-xs mr-2 text-green-600"></i>Минимум 6 символов';
            reqLength.classList.add('text-green-600');
        } else {
            reqLength.innerHTML = '<i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>Минимум 6 символов';
            reqLength.classList.remove('text-green-600');
        }
    }

    if (confirmPassword.length > 0 && matchMessage && reqMatch && submitBtn) {
        matchMessage.classList.remove('hidden');
        if (newPassword === confirmPassword) {
            matchMessage.textContent = '✓ Пароли совпадают';
            matchMessage.className = 'text-xs mt-1 text-green-600';
            reqMatch.innerHTML = '<i class="ri-checkbox-circle-line text-xs mr-2 text-green-600"></i>Пароли должны совпадать';
            reqMatch.classList.add('text-green-600');
            submitBtn.disabled = newPassword.length < 6;
        } else {
            matchMessage.textContent = '✗ Пароли не совпадают';
            matchMessage.className = 'text-xs mt-1 text-red-600';
            reqMatch.innerHTML = '<i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>Пароли должны совпадать';
            reqMatch.classList.remove('text-green-600');
            submitBtn.disabled = true;
        }
    } else if (matchMessage && reqMatch && submitBtn) {
        matchMessage.classList.add('hidden');
        if (reqMatch) {
            reqMatch.innerHTML = '<i class="ri-checkbox-blank-circle-line text-xs mr-2"></i>Пароли должны совпадать';
            reqMatch.classList.remove('text-green-600');
        }
        submitBtn.disabled = true;
    }
}

// Инициализация обработчиков событий для формы сброса пароля
function initResetPasswordForm(isModal = false) {
    const prefix = isModal ? 'modal_reset_' : '';
    const newPasswordInput = document.getElementById(prefix + 'new_password');
    const confirmPasswordInput = document.getElementById(prefix + 'confirm_password');

    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value, isModal);
            checkPasswordMatch(isModal);
        });
    }

    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', () => checkPasswordMatch(isModal));
    }
}

// Вызов формы запроса восстановления пароля по клику (открывает модальное окно)
$(document).on('click', '.open_modal_forgot_password_form', function(e) {
    e.preventDefault();
    
    // Закрываем все открытые модальные окна перед открытием нового
    $.arcticmodal('close');
    
    $.post('/components/auth/password-recovery/ajax/ajax.php', {action: 'open_modal_forgot_password_form'}, function(res) {
        $(res).arcticmodal({closeOnOverlayClick: false});
    });
});

// Обработка отправки формы запроса восстановления пароля из модального окна
$(document).on('submit', '.forgot-password-modal form[data-action="forgot-password"]', function(e){
    e.preventDefault();
    
    let form = $(this);
    let formData = form.serialize() + '&ajax=1';
    
    $.post('/components/auth/password-recovery/ajax/ajax.php', formData, function(res) {
        console.log('Ответ сервера:', res);
        
        if (typeof res === 'object' && res !== null) {
            if (res.success) {
                // Заменяем форму на сообщение об успехе
                let successHtml = '<div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">' +
                    '<h3 class="text-sm font-semibold text-green-800 mb-2 flex items-center">' +
                    '<i class="ri-check-line mr-2"></i>Демо режим</h3>' +
                    '<p class="text-xs text-green-700 mb-3">' + (res.message || 'Ссылка для восстановления пароля отправлена на ваш email') + '</p>';
                
                if (res.token) {
                    let resetUrl = window.location.origin + '/?token=' + res.token;
                    successHtml += '<div class="bg-white rounded p-3 mb-3">' +
                        '<code class="text-xs break-all text-green-900">' + resetUrl + '</code>' +
                        '</div>' +
                        '<a href="/?token=' + res.token + '" ' +
                        'class="block w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors text-center font-semibold">' +
                        '<i class="ri-key-line mr-2"></i>Сбросить пароль</a>';
                }
                
                successHtml += '</div>';
                form.closest('.space-y-6').html(successHtml);
            } else {
                window.AuthFormUtils.showError(form, res.error || 'Ошибка восстановления пароля');
            }
        }
    });
});

// Автоматическая загрузка формы при наличии токена в URL (всегда в модальном окне)
$(document).ready(function() {
    let urlParams = new URLSearchParams(window.location.search);
    let token = urlParams.get('token');
    if (token) {
        $.arcticmodal('close');
        $.post('/components/auth/password-recovery/ajax/ajax.php', {
            action: 'open_modal_reset_password_form',
            token: token
        }, function(res) {
            $(res).arcticmodal({closeOnOverlayClick: false});
            setTimeout(() => initResetPasswordForm(true), 100);
        });
    }
});

// Вызов формы сброса пароля в модальном окне по клику
$(document).on('click', '.open_modal_reset_password_form', function(e) {
    e.preventDefault();
    
    // Закрываем все открытые модальные окна перед открытием нового
    $.arcticmodal('close');
    
    let urlParams = new URLSearchParams(window.location.search);
    let token = urlParams.get('token') || $(this).data('token') || '';
    
    $.post('/components/auth/password-recovery/ajax/ajax.php', {
        action: 'open_modal_reset_password_form',
        token: token
    }, function(res) {
        $(res).arcticmodal({closeOnOverlayClick: false});
        setTimeout(() => initResetPasswordForm(true), 100);
    });
});

// Обработка отправки формы сброса пароля из модального окна
$(document).on('submit', '.reset-password-modal form[data-action="reset-password"]', function(e){
    e.preventDefault();
    
    let form = $(this);
    let formData = form.serialize() + '&ajax=1';
    
    $.post('/components/auth/password-recovery/ajax/ajax.php', formData, function(res) {
        console.log('Ответ сервера:', res);
        
        if (typeof res === 'object' && res !== null) {
            if (res.success) {
                // Заменяем форму на сообщение об успехе
                let successHtml = '<div class="text-center mb-6">' +
                    '<div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">' +
                    '<i class="ri-check-line text-4xl text-green-600"></i>' +
                    '</div>' +
                    '<h3 class="text-xl font-bold text-gray-900 mb-2">Пароль успешно изменен!</h3>' +
                    '<p class="text-gray-600">Теперь вы можете войти в систему с новым паролем</p>' +
                    '</div>' +
                    '<a href="#" class="open_modal_login_form block w-full bg-gradient-to-r from-green-500 to-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 text-center cursor-pointer">' +
                    '<i class="ri-login-box-line mr-2"></i>Войти в систему</a>';
                
                form.closest('.space-y-6').html(successHtml);
            } else {
                window.AuthFormUtils.showError(form, res.error || 'Ошибка сброса пароля');
            }
        }
    });
});

console.log('Password Recovery module initialized');

