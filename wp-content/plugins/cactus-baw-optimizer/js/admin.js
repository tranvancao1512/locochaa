var converter = {};

;(function($){
    converter.doAjax = function (btn, params){
        $.ajax({
            type: 'post',
            url: ajaxurl,
            data: params,
            success: function(result){
                var obj = JSON.parse(result);                        
                
                if($('#optimizer').val() == 'delete'){
                    alert(obj.message);
                    btn.removeClass('disabled');
                } else {
                    if(obj.error){
                        $('#converter-message').prepend('<p class="error">' + obj.message + '</p>');
                    } else {
                        $('#converter-message').prepend('<p>' + obj.message + '</p>');
                        progress = obj.progress;
                        if(progress < 100){
                            btn.next().children('.inner').attr('style','width:' + progress + '%');
                            
                            params.index = params.index + 1;
                            converter.doAjax(btn, params);
                        } else {
                            btn.next().children('.inner').attr('style','width:' + 100 + '%');
                            
                            $('#converter-message').prepend('<p class="success">DONE! Congratulations! You can now delete old data from BAW to reduce database size</p>');
                            
                            btn.removeClass('disabled');
                            
                            setTimeout(function(){$('#converter-message').fadeOut();}, 2000);
                        }
                    }
                }
            }
        });
    }
	converter.submit = function(index, btn){
        if(confirm('Make sure you have correct date. This action is irreversible')){
            btn.addClass('disabled');
            
            params = {
                    action: 'cactus_baw_optimizer',
                    date: $('#baw-date').val(),
                    dataType: $('#baw-type').val(),
                    work: $('#optimizer').val(),
                    index: index
                    };

            converter.doAjax(btn, params);
        }
	}
	
	
}(jQuery));

jQuery(document).ready(function($) {
	$('#baw-optimizer-button').on('click', function(evt){
		if(!$(this).hasClass('disabled')){
			converter.submit(0, $(this));	
		}		
	});
    
    $('#optimizer').on('change', function(evt){
        $('.opt-panel').addClass('hide');
        $('#opt-' + $(this).val()).removeClass('hide');
    });
});