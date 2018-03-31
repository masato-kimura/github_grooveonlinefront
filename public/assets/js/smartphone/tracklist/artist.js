$(document).bind("pageshow", function() {
	// ベースオブジェクト
	var base = {};

	base.page_default = function() {
		$(".lazy img").lazyload({
			effect: "fadeIn" ,
			effect_speed: 300 ,
		});
		$('img').error(function() {
			$(this).attr('src', '/assets/img/default.jpg');
		});
	};

	// スクロール
	base.scrollToSection = function(id, callback) {
		var speed = 600;
		var target = $('#' + id);
		var position = target.offset().top;
		$('body,html').animate({scrollTop:position}, speed, 'swing', callback);
	};

	// ログインユーザ
	base.user = {};
	base.user.id = $('#user_id').val();

	// アーティストクラス
	base.artist = {};
	base.artist.id = $('#form_artist_id').val();
	base.artist.setEventListner = function(){
		// お気に入りアーティスト
		$('#form_favorite_artist_status').on('change', function(){
			var params = {
				client_user_id: $('#form_user_id').val(),
				favorite_artist_id: $('#form_artist_id').val(),
				status: $(this).val(),
			};
			$.ajax({
				type: 'post',
				url: '/api/artist/setfavorite.json',
				datatype: 'json',
				data: JSON.stringify(params),
				contentType: 'application/json',
				cache: false,
				success: function(res, ans){
					return true;
				},
				error: function(){
					alert('ネットワークエラー');
					return true;
				}
			});
		});
		$('#favorite_artist_status_disabled_anchor').on('click', function(){
			$(this).off('click');
			alert('こちらの機能はログイン後に使用することができます');
			return false;
		});
	};

	base.tracklist = {};
	base.tracklist.all_count = parseInt($('#tracklist_count .all_count').html(), 10);
	base.tracklist.ajax_response = [];
	base.tracklist.get_ajax = function(offset, limit) {
		var params = {
			offset    : offset,
			limit     : limit,
			artist_id : base.artist.id,
			user_id   : '',
		};
		return $.ajax({
			type: 'post',
			url: '/api/tracklist/getlist.json',
			datatype: 'json',
			data: JSON.stringify(params),
			contentType: 'application/json',
			cache: false,
			success: function(res, ans) {
				base.tracklist.ajax_response = res.result;
				return res;
			},
			error: function() {
				alert('network error');
				return true;
			}
		});
	};
	base.tracklist.getlist = function(offset, limit) {
		$('#about_detail_tracklist_div .review_list_ul').eq(0).css('display', 'none');
		base.tracklist.get_ajax(offset, limit).done(function (res) {
			base.tracklist.all_count = base.tracklist.ajax_response.count;
			var html = '';
			jQuery.each(base.tracklist.ajax_response.arr_list, function(i, v) {
				if (v.artist_name === null) {
					v.artist_name = '';
				}
				html += '<li id="tracklist_detail_id_'+ v.id +'" class="review_list_li tracklist_list_li ui-li-static ui-body-inherit">';
				html += '<div class="tracklist_list_title">'+ v.title +'</div>';
				html += '<div class="tracklist_list_created_at">'+ v.created_at +'</div>';
				var detail_html = '<ol class="tracklist_list_tracks">';
				var cnt = 0;
				jQuery.each(v.arr_tracks, function(j, w) {
					detail_html += '<li><span class="list_track_name">'+ w.track_name +'</span> <span class="list_artist_name">'+ w.track_artist_name +'</span></li>';
					cnt++;
					if (cnt >= 3) {
						return false;
					}
				});
				detail_html += '</ol>';
				html += detail_html;
				if (v.arr_tracks.length > 3) {
					html += '<div class="tracklist_list_and_more">and more ・・・</div>';
				} else {
					html += '<div class="tracklist_list_and_more">&nbsp;</div>';
				}
				html += '<div class="tracklist_list_user">';
				html += '<span>by '+ v.user_name +'</span> ';
				html += '<span><img src="'+ v.user_image +'" alt=""></span>';
				html += '</div>';
				html += '</li>';
			}); // endEach
			$('#about_detail_tracklist_div .review_list_ul').eq(0).html(html);
			$('#about_detail_tracklist_div .review_list_ul').eq(0).show('slow');
			base.page_default();
			base.tracklist.list_hover();
		});
	};

	base.tracklist.list_hover = function() {
		// レビュー
		$('.tracklist_list_li').hover(
				function() {
					$(this).css('background', 'rgba(100,100,100,0.2)');
					$(this).css('cursor', 'pointer');
				},
				function() {
					$(this).css('background', 'inherit');
				}
			);
		$('.tracklist_list_li').on('click',
				function() {
					$(this).css('background', 'inherit');
					var tracklist_id = parseInt($(this).attr('id').match(/[0-9]+$/), 10);
					location.href = '/tracklist/detail/' + tracklist_id + '/';
				}
			);
	};

	base.tracklist.setEventListner = function() {
		var params = {
			count  : base.tracklist.all_count,
			limit  : 10,
			offset : $('#page_offset').val(),
		};
		pagination.setEventListner(params, base.tracklist.getlist);
		base.tracklist.list_hover();
	};

	base.tracklist.setEventListner();
});