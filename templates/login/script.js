// Проверить версию jQuery
console.log('jQuery version:', $.fn.jquery);


// Вызов формы логина по клику
$(document).on('click', '.open_login', function(){

	$.post('/login', {action: 'open_login'}, function(res){
		console.log(res.trim());
		//$('.authorization-ajax-container').html(res.trim())
	});
	
});