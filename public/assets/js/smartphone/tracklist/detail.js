$(document).bind("pageshow", function() {
	$.mobile.ajaxEnabled = false;
	var base = {};
	// スクロール
	base.scrollToSection = function(id, callback) {
		var speed = 600;
		var target = $('#' + id);
		var position = target.offset().top;
		$('body,html').animate({scrollTop:position}, speed, 'swing', callback);
	};
	// ローディング画面
	base.generateLoadingHtml = function () {
		var html = "<div class='loading'>";
		html += "<span class='loading'>Loading</span>";
		html += "<br />";
		html += "<span class='l-1'></span>";
		html += "<span class='l-2'></span>";
		html += "<span class='l-3'></span>";
		html += "<span class='l-4'></span>";
		html += "<span class='l-5'></span>";
		html += "<span class='l-6'></span>";
		html += "</div>";
		return html;
	}

	base.htmlentities = function(str) {
		return str.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
	};

	base.tracklist = {};
	base.tracklist.artist_id = $('#artist_id').val();
	base.tracklist.tracklist_user_id = '';
	base.tracklist.login_user_id = '';
	base.tracklist.tracklist_id = '';
	base.tracklist.arr_audio = [];
	base.tracklist.ajax_response = [];
	base.tracklist.track = '';
	base.tracklist.arr_tracks = [];
	base.tracklist.current_index = null;
	base.tracklist.interval = null;
	base.tracklist.meta_description = $('meta[property="og:description"]').attr('content');
	base.tracklist.set_display_track = function() {
		var html = '';
		if (base.tracklist.current_index === null) {
			html = '<span class="tracklist_disp_ready">Ready</span>';
			$('#tracklist_time_display').html('00:00');
		} else {
			html = 'Track ';
			html = html + (parseInt(base.tracklist.current_index, 10) + 1);
		}
		$('#tracklist_current_track_display').html(html);
	};
	base.tracklist.modal_close = function() {
		$("#lean_overlay").fadeOut(80, function() {
			$('#tracklist_detail').css({ 'display' : 'none' });
		});
		history.replaceState('','','/artist/detail/' + base.artist.id + '/');
		$('meta[property="og:description"]').attr('content', base.tracklist.meta_description);
		$('meta[property="twitter:description"]').attr('content', base.tracklist.meta_description);
	};

	base.tracklist.deleteAjax = function() {
		var params = {
			tracklist_id : base.tracklist.tracklist_id,
			user_id      : base.tracklist.login_user_id,
		};
		return $.ajax({
			type : 'post',
			url  : '/api/tracklist/delete.json',
			datatype : 'json',
			data : JSON.stringify(params),
			contentType : 'application/json',
			cache : false,
			success : function(res, ans) {
				if (res.success === false) {
					alert('入力項目をご確認ください');
					return false;
				}
				base.tracklist.ajax_response = res.result;
				return true;
			},
			error : function() {
				alert('Network Error');
				return false;
			}
		});
	};

	base.tracklist.dispTracklist = function() {
		// 個別に再生
		$('.tracklist_play_btn').on('click', function() {
			base.tracklist.current_index = $('.tracklist_play_btn').index(this);
			base.tracklist.play_reset(base.tracklist.current_index);
			base.tracklist.track = $('.preview_tracklist').eq(base.tracklist.current_index)[0];
			if (base.tracklist.track.currentTime === 0) {
				base.tracklist.set_display_track();
				base.tracklist.track.play();
				$(this).html('◾');
				$(this).removeClass('play_mark');
				$(this).addClass('stop_mark');
				$(this).attr('title', 'ストップ');
				base.tracklist.interval = setInterval(function() {
					if (base.tracklist.track.paused === true) {
						base.tracklist.current_index = null;
						base.tracklist.play_reset();
						base.tracklist.set_display_track();
					} else {
						var current_time = Math.round(base.tracklist.track.currentTime * 100)/100;
						current_time = String(current_time).replace(/^([0-9]{1})\./, '0$1.');
						$('#tracklist_time_display').html(current_time);
					}
				}, 200);
			} else {
				clearInterval(base.tracklist.interval);
				base.tracklist.track.pause();
				base.tracklist.track.currentTime = 0;
				base.tracklist.current_index = null;
				base.tracklist.set_display_track();
				$(this).html("▶️");
				$(this).removeClass('stop_mark');
				$(this).addClass('play_mark');
				$(this).attr('title', 'プレイ！');
			}
		});

		// 全曲再生
		$('#tracklist_detail_all_play_btn').on('click', function() {
			$('#tracklist_detail_all_play_btn').addClass('now_playing');
			if (base.tracklist.interval) {
				base.tracklist.play_reset();
				base.tracklist.current_index = null;
				base.tracklist.set_display_track();
				return true;
			}
			base.tracklist.arr_tracks = [];
			$('.preview_tracklist').each(function(i, v) {
				base.tracklist.arr_tracks[i] = $('.preview_tracklist').eq(i)[0];
				base.tracklist.arr_tracks[i].load();
			});
			var current_time = 0;
			var track_count = base.tracklist.arr_tracks.length;
			base.tracklist.current_index = 0;
			base.tracklist.set_display_track();
			base.tracklist.arr_tracks[base.tracklist.current_index].play();
			$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').html('◾');
			$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').removeClass('play_mark');
			$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').addClass('stop_mark');
			$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').attr('title', 'ストップ');
			base.tracklist.interval = setInterval(function() {
				current_time = Math.round(base.tracklist.arr_tracks[base.tracklist.current_index].currentTime * 100)/100;
				current_time = String(current_time).replace(/^([0-9]{1})\./, '0$1.');
				$('#tracklist_time_display').html(current_time);
				if (base.tracklist.arr_tracks[base.tracklist.current_index].paused === true && base.tracklist.arr_tracks[base.tracklist.current_index].currentTime > 0) {
					$('#tracklist_time_display').html('00.00');
					$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').html('▶️');
					$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').removeClass('stop_mark');
					$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').addClass('play_mark');
					$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').attr('title', 'プレイ！');
					base.tracklist.current_index = base.tracklist.current_index + 1;
					if (base.tracklist.current_index >= track_count) {
						base.tracklist.play_reset(base.tracklist.current_index);
						base.tracklist.current_index = null;
						base.tracklist.set_display_track();
						return true;
					} else {
						base.tracklist.set_display_track();
						base.tracklist.arr_tracks[base.tracklist.current_index].play();
						$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').html('◾');
						$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').removeClass('play_mark');
						$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').addClass('stop_mark');
						$('.preview_tracklist').eq(base.tracklist.current_index).parent().children('.tracklist_play_btn').attr('title', 'プレイ！');
					}
				}
			}, 200);
		});
		
		$('#tracklist_detail_updbtn').on('click', function() {
			if (base.tracklist.login_user_id > 0) {
				var tracklist_id = $('#tracklist_id').val();
				location.href = '/tracklist/create/' + base.tracklist.artist_id + '/?tracklist_id=' + tracklist_id;
			}
		});

		$('#tracklist_detail_delbtn').on('click', function() {
			if (base.tracklist.login_user_id > 0) {
				if (confirm('こちらの投稿を削除しますか？')) {
					base.tracklist.deleteAjax().done(function() {
						location.href = '/tracklist/';
						return true;
					});
				}
			}
		});

		$('#tracklist_detail_header_close_btn').off('click');
		$('#tracklist_detail_header_close_btn').on('click', function() {
			base.tracklist.modal_close();
			base.tracklist.play_reset();
		});

		$("#lean_overlay").off('click');
		$("#lean_overlay").on('click', function() {
			base.tracklist.modal_close();
			base.tracklist.play_reset();
		});
	};

	base.tracklist.setEventListner = function() {
		base.tracklist.tracklist_user_id = $('#tracklist_user_id').val();
		base.tracklist.login_user_id = $('#login_user_id').val();
		$('.tracklist_detail').off('click');
		base.tracklist.tracklist_id = parseInt($('#tracklist_id').val().match(/[0-9]*/));
		base.tracklist.dispTracklist();
		if (base.tracklist.tracklist_user_id > 0) {
			var title = $('#tracklist_detail_id_' + base.tracklist.tracklist_id).find('.tracklist_detail_list_title').eq(0).html();
			var user  = $('#tracklist_detail_id_' + base.tracklist.tracklist_id).find('.tracklist_detail_list_user').eq(0).children('span').html();
			$('meta[property="og:description"]').attr('content', title + ' / by ' + user);
			$('meta[property="twitter:description"]').attr('content', title + ' / by ' + user);
		}

		$('.tracklist_detail').on('click', function() {
			// イベント初期化
			$('#tracklist_detail_all_play_btn').off('click');
			$('.tracklist_play_btn').off('click');
			$("#lean_overlay").off('click');
			base.tracklist.tracklist_id = parseInt($(this).attr('id').match(/[\d]+$/), 10);
			base.tracklist.dispTracklist();
			history.replaceState('','','?tracklist_id=' + base.tracklist.tracklist_id);
			var title = $(this).find('.tracklist_detail_list_title').eq(0).html();
			var user  = $(this).find('.tracklist_detail_list_user').eq(0).children('span').html();
			$('meta[property="og:description"]').attr('content', title + ' / by ' + user);
			$('meta[property="twitter:description"]').attr('content', title + ' / by ' + user);
			$("#lean_overlay").off('click');
			$("#lean_overlay").on('click', function() {
				base.tracklist.modal_close();
			});
		});
		$('#tracklist_detail_header_close_btn').off('click');
		$('#tracklist_detail_header_close_btn').on('click', function() {
			base.tracklist.modal_close();
			base.tracklist.play_reset();
		});

		$("#lean_overlay").off('click');
		$("#lean_overlay").on('click', function() {
			base.tracklist.modal_close();
			base.tracklist.play_reset();
		});
	};

	base.tracklist.play_reset = function (current_index) {
		// 全曲再生中ならば停止
		if (base.tracklist.interval) {
			clearInterval(base.tracklist.interval);
			base.tracklist.interval = null;
			$('#tracklist_detail_all_play_btn').removeClass('now_playing');
		}
		base.tracklist.set_display_track();
		$('#tracklist_detail_all_play_btn').removeClass('now_playing');
		$('.preview_tracklist').each(function(i, ans) {
			if (i === current_index) {
				return true;
			}
			ans.pause();
			$('#tracklist_time_display').html('00.00');
			$('.tracklist_play_btn').eq(i).html("▶️");
			$('.tracklist_play_btn').eq(i).removeClass('stop_mark');
			$('.tracklist_play_btn').eq(i).addClass('play_mark');
			$('.tracklist_play_btn').eq(i).attr('title', 'プレイ！');
			if (ans.currentTime > 0) {
				ans.currentTime = 0;
			}
		});
		return true;
	};

	// トラックリストイベントをセット
	base.tracklist.setEventListner();
});