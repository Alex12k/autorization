// Проверить версию jQuery
console.log('jQuery version:', $.fn.jquery);


// Вызов формы регистрации по клику
$(document).on('click', '.open_register', function(){

	$.post('/templates/register/register.php', {action: 'open_register'}, function(res){
		console.log(res);
		$('.authorization-ajax-container').html(res.trim())
	});

});

