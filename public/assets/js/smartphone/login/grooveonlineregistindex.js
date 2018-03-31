jQuery(function($) {
	$('.option').hide();
	$('.open_option').click(function() {
		$('.option').toggle('fast');
		$('.openclose').toggle();
	});
	
	$('.error').map(function(i,v){
		if ($(this).html().length > 0) {
			$('.option').show('slow');
		}
	});
});