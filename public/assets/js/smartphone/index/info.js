$(document).bind("pageinit", function(){
  $.mobile.ajaxEnabled = false;
});

jQuery(function(){
	var info_count = $('#information-icon').html();
	if (info_count != '&nbsp;') {
		if (info_count > 0) {
			info_count = info_count - 1;
			if (info_count === 0) {
				info_count = '&nbsp;';
			}
			setTimeout(function(){
				$('#information-icon').html('^-');
				setTimeout(function(){
					$('#information-icon').html(info_count);
				},250);
			}, 650);
			

		}
	}
});
