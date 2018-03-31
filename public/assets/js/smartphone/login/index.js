jQuery(function($) {
	
	$('.oauth_login_btn').on('click', function() {
		var href = $(this).parent().attr('href');
		href = href.replace(/[0-9]*$/, '');
		if ($('#form_auto_login_oauth').prop('checked')) {
			href = href + '1';
		} else {
			href = href + '0';
		}
		$(this).parent().attr('href', href);
		
		return true;
	});
		
	$('#passreissue').click(function(){
		var org_href = $(this).attr('href');
		var email = $('#form_email').val();
		var new_href = org_href + '?email=' + email;
		$('#passreissue').attr('href', new_href);
	});
	$('#passreissuerequest').click(function(){
		var email = $('#login_index_email').val();
		var str_passreissuerequest = $(this).attr('href');
		$(this).attr('href', str_passreissuerequest + '?email=' + email);
	});
});
