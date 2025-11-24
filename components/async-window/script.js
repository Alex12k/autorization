




$(document).on('click', '.open_async-window', function() {
    $.post('/components/async-window/ajax/ajax.php', {action: 'open_async-window'}, function(res) {
        $(res).arcticmodal({closeOnOverlayClick: false});
    });
});