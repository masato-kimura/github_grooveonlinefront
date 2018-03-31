$(document).bind("pageinit", function(){
  $.mobile.ajaxEnabled = false;
});

$(function() {
	var modal = $('a[rel*=leanModal]').leanModal({
		top: 40,
		overlay : 0.5,
		closeButton: ".modal_close",
	});

	setTimeout(function() {
		$('#editregistindex_email_org').val(null);
		$('#editregistindex_password_org').val(null);
	}, 500);

	var password_tmp = $('#editregistindex_password').val();

	$('#editregistindex_password').on('focus', function() {
		if ($(this).val() === password_tmp) {
			$(this).val(null);
		}
	});

	$('#editregistindex_password').on('blur', function() {
		if ($(this).val().length === 0) {
			$(this).val(password_tmp);
		}
	});

	$('#editregistindex_high_login_submit').click(function(){
		var modal_id = '#div_high_login';
		var email    = $('#editregistindex_email_org').val();
		var password = $('#editregistindex_password_org').val();
		email        = htmlentities(email);
		password     = htmlentities(password);

		is_available_login(email, password).done(function(res) {
			if (res.length == 0) {
				$('#editregistindex_org_error').show('fast');
				return true;
			}

			var result = res.result.is_available;
			if (result) {
				$("#lean_overlay").fadeOut(200);
				$(modal_id).css({ 'display' : 'none' });
				$('#editregistindex_email_disp').css('display', 'none');
				$('#editregistindex_password_disp').css('display', 'none');
				$('#editregistindex_email_button').parent().addClass('ui-screen-hidden');
				$('#editregistindex_password_button').parent().addClass('ui-screen-hidden');
				$('#editregistindex_email').val(email);

				setTimeout(function(){
					$('#editregistindex_email').removeClass('ui-screen-hidden');
					$('#editregistindex_password').removeClass('ui-screen-hidden');
				}, 50);
			} else {
				$('#editregistindex_org_error').show('fast');
			}

			return true;
		});
	});

	$('#logout').on('click', function(){
		if (confirm('ログアウトしてよろしいですか？')) {
			return true;
		}
		return false;
	});

	function is_available_login(email, password) {
		var params = {email: email, password: password};
		var result = null;
		return $.ajax({
			type: 'post',
			url: '/api/login/grooveonlineavailablelogin.json',
			datatype: 'json',
			data: JSON.stringify(params),
			contentType: 'application/json',
			cache: false,
			success: function(res, ans) {
				return true;
			},
			error: function(){
				alert('network error');
				return false;
			}
		});
	}

	function htmlentities(str) {
		return str.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
	}
});
