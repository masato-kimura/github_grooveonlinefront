jQuery(function($){
	var str_href_facebook = $('.oauth_facebook a').attr('href');
	var str_href_twitter  = $('.oauth_twitter a').attr('href');
	var str_href_yahoo    = $('.oauth_yahoo a').attr('href');
	var str_href_google   = $('.oauth_google a').attr('href');

	$('#form_auto_login').change(function(){
		if ($(this).prop('checked')){
			$('.oauth_facebook a').attr('href', str_href_facebook + '1');
			$('.oauth_twitter a').attr('href', str_href_twitter + '1');
			$('.oauth_yahoo a').attr('href', str_href_yahoo + '1');
			$('.oauth_google a').attr('href', str_href_google + '1');
		} else {
			$('.oauth_facebook a').attr('href', str_href_facebook + '0');
			$('.oauth_twitter a').attr('href', str_href_twitter + '0');
			$('.oauth_yahoo a').attr('href', str_href_yahoo + '0');
			$('.oauth_google a').attr('href', str_href_google + '0');
		}
	});

	if ($('#from_password_reissue_caption').length > 0) {
		setTimeout(function() {
			$('#login_index_password').val(null);
			$('#login_index_password').focus();
		}, 100);
	}

	$('#passreissuerequest_link').click(function(){
		var email = $('#form_email').val();
		var str_passreissuerequest = $(this).attr('href');
		$(this).attr('href', str_passreissuerequest + '?email=' + email);
	});
});
