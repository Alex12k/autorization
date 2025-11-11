// Проверить версию jQuery
console.log('jQuery version:', $.fn.jquery);


// Вызов формы логина по клику
$(document).on('click', '.open_login', function(){

	$.post('/templates/login/login.php', {action: 'open_login'}, function(res){
		console.log(res);
		$('.authorization-ajax-container').html(res.trim())
	});

});