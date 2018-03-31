$(document).bind("pageinit", function(){
	  $.mobile.ajaxEnabled = false;
});

jQuery(function($) {
	var base = {};
	var review_id    = null;
	var global_timer = null;
	var is_all_play  = false;
	var number = 0;
	var a = {};

	// 個別再生
	$('.preview_button').on('click', function() {
		var global_i = $('.preview_button').index(this);

		// 一旦全アルバムトラック再生を停止させる
		reset_preview(global_i);
		$('#track_detail_listen_all').css('background', 'inherit').css('color', '#000').css('text-decoration', 'none');

		var a = $('.preview_itunes').eq(global_i)[0];

		if (a.currentTime === 0) {
			a.play();
			var track_number = global_i + 1;
			$('#current_track').html('TRACK<span id="current_track_num">' + track_number + '</span>');
			$(this).html('◾');
			$(this).addClass('preview_button_pause');
		} else {
			a.pause();
			a.currentTime = 0;
			$(this).html('▶️');
			$(this).removeClass('preview_button_pause');
			clearInterval(timer);
		}

		var timer = setInterval(function() {
			if (a.currentTime > 0) {
				var current_time = Math.round(a.currentTime * 100)/100;
				$('#current_time').html(current_time);
			}
			if (a.paused) {
				clearInterval(timer);
			}
		}, 200);

		return true;
	});

	function reset_preview(g_i) {
		//a = {};
		clearInterval(global_timer);
		$('.preview_itunes').each(function(i, ans) {
			$('#current_track, #current_time').html(null);
			ans.pause();
			$('.preview_button').eq(i).html('▶️');
			if (g_i != i) {
				if (ans.currentTime > 0) {
					ans.currentTime = 0;
				}
				$('.preview_button').removeClass('preview_button_pause');
			}
		});
		return true;
	}


	// レビュー
	$('.review_list_tr').hover(
			function() {
				$(this).css('background', 'rgba(100,100,100,0.2)');
				$(this).css('cursor', 'pointer');
				$(this).next('.review_list_tr_user').css('background', 'rgba(100,100,100,0.2)');
			},
			function() {
				$(this).css('background', 'inherit');
				$(this).next('.review_list_tr_user').css('background', 'inherit');
			}
		);
	$('.review_list_tr').on('click',
			function() {
				var index     = $(this).index('.review_list_tr');
				var about     = $('.about').eq(index).html();
				var review_id = $('.review_id').eq(index).html();
				location.href = '/review/music/detail/' + about + '/' + review_id + '/';
			}
		);
	$('.review_list_tr_user').on('click',
			function() {
				var index     = $(this).index('.review_list_tr_user');
				var about     = $('.about').eq(index).html();
				var review_id = $('.review_id').eq(index).html();
				location.href = '/review/music/detail/' + about + '/' + review_id + '/';
			}
		);

	base.htmlentities = function(str) {
		return str.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
	};

});