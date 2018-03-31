$(document).bind("pageint", function() {
	$.mobile.ajaxEnabled = false;
});

$(document).bind("pageshow", function() {
	$.mobile.ajaxEnabled = false;
	$('a[rel*=leanModal]').leanModal({
		top: 32,
		overlay : 0.75,
		closeButton: ".modal_close",
	});

	var base = {};

	// スクロール
	base.scrollToSection = function(id, callback) {
		var speed = 600;
		var target = $('#' + id);
		var position = target.offset().top;
		$('body,html').animate({scrollTop:position}, speed, 'swing', callback);
	};

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

			return false;
		});

		$('#favorite_artist_status_disabled_anchor').on('click', function(){
			$(this).off('click');
			alert('こちらの機能はログイン後に使用することができます');
			return false;
		});
	};

	// 画面アルバムアート一覧クラス
	base.artmix  = {};
	base.artmix._page = 1;
	base.artmix._loading = function() {
		$('#music_write_more_album_area .loading').css('display', 'block');
	};
	base.artmix._loaded = function() {
		$('#music_write_more_album_area .loading').fadeOut('fast');
	};
	base.artmix._append = function(html) {
		$('#artist_detail_album_img').append(html);
	};
	base.artmix._appendShow = function() {
		$('#artist_detail_album_img').fadeIn('slow');
		$('#music_write_more_album_link').fadeIn('slow');
		$('.more').fadeIn('slow');
	};
	base.artmix._getAlbumListFromAjax = function(data) {
		return $.ajax({
			type:        'post',
			url:         '/api/album/list.json',
			datatype:    'json',
			data:        JSON.stringify(data),
			contentType: 'application/json',
			cache:       false,
			success:     function(obj_response, ans){
				return obj_response;
			},
			error:       function(){
				return false;
			}
		});
	};
	base.artmix._hoverArt = function() {
		$('#artist_detail_album_img .album_image img').hover(
			function() {
				var index = $(this).parent('span').index();
				var image_tag = '<img src="' + $('.album_image_input').eq(index).val() + '">';
				var album_name = $('.album_name_input').eq(index).val();
				if ($('.album_mbid_itunes_input').eq(index).val().length > 0) {
					album_name += '&nbsp;*';
				}
				$('#music_write_hover_album_name').html(album_name);
				$('#music_write_hover_album_art').html(image_tag);

				$('#music_write_hover_album_main_div').css('top', $(this).position().top + 120);
				$('#music_write_hover_album_main_div').fadeIn('fast');

				var params = {};
				params.artist_name        = base.artist_name;
				params.album_name         = $('.album_name_input').eq(index).val();
				params.album_mbid_itunes  = $('.album_mbid_itunes_input').eq(index).val();
				params.album_mbid_lastfm  = $('.album_mbid_lastfm_input').eq(index).val();
				params.album_id           = $('.album_id_input').eq(index).val();
				params.album_url_itunes   = $('.album_url_itunes_input').eq(index).val();
				params.album_url_lastfm   = $('.album_url_lastfm_input').eq(index).val();
				params.album_image        = $('.album_image_input').eq(index).val();

				$('#to_album_review').off('click', setAlbumTracks);
				$('#to_album_review').on('click', setAlbumTracks);

				$('#close_art').on('tap', function() {
					setTimeout(function() {
						$('#music_write_hover_album_action').html(null);
						$('#music_write_hover_album_art').html(null);
						$('#music_write_hover_album_name').html(null);
						$('#artist_detail_album_img img').on('click', setAlbumTracks);
					}, 100);
					return true;
				});
			}
		);
	};
	// もっとみる
	base.artmix.getMoreAlbum = function() {
		$('#music_write_more_album_link').css('display', 'none');

		// 待ち画面
		var loading_html = base.generateLoadingHtml();
		$('#music_write_more_album_area').prepend(loading_html);
		$('#music_write_more_album_area .loading').fadeIn('slow');

		// パラメータセット
		var data ={};
		data.artist_name = $('#artist_detail_name .artist_name').html();
		data.artist_id   = $('#main_navi_artist_id').html();
		data.limit       = 20;
		data.page        = ++base.artmix._page;

		// ajax
		base.artmix._getAlbumListFromAjax(data).done(function(res) {
			// ajaxエラー対応
			if (res === false){
				base.artmix._loaded();
				base.artmix._appendShow();
				alert('申し訳ございません。ただいまネットワークエラーが発生しております。');
				return true;
			}

			if (res.success === false) {
				base.artmix._loaded();
				base.artmix._appendShow();
				alert('申し訳ございません。ただいまネットワークエラーが発生しております。');
				return true;
			}

			if (res.result.arr_list.length == 0) {
				$('#music_write_more_album_link').remove();
				base.artmix._loaded();
				return true;
			}

			if (res.result.arr_list.length < data.limit) {
				$('#music_write_more_album_link').remove();
			}

			// 大きい画像の先読み
			res.result.arr_list.forEach(function(e) {
				var album_image = '<img src="' + e['image_extralarge'] + '" class="art_middle_hidden">';
				$('#music_write_album_append_hidden_area').append(album_image);
			});

			base.artmix._loaded();

			// 画面データのセット
			res.result.arr_list.forEach(function(e) {
				var html = '<span class="album_image more" style="display:none;">';
				html += '<input type="hidden" value="' + e['id']          + '" class="album_id_input">';
				html += '<input type="hidden" value="' + e['name']        + '" class="album_name_input">';
				html += '<input type="hidden" value="' + e['mbid_itunes'] + '" class="album_mbid_itunes_input">';
				html += '<input type="hidden" value="' + e['mbid_lastfm'] + '" class="album_mbid_lastfm_input">';
				html += '<input type="hidden" value="' + e['url_itunes']  + '" class="album_url_itunes_input">';
				html += '<input type="hidden" value="' + e['image_extralarge'] + '" class="album_image_input">';
				html += '<img src="' + e['image_medium'] + '" class="art_small"  title="アルバム 『' + e['name'] + '』 をレビュー">';
				html += '</span>';
				base.artmix._append(html);
			});

			// アルバムアートイベントリスナのセット
			base.artmix._hoverArt();

			// アルバムアートクリック
			$('#artist_detail_album_img img').off('click');
			$('#artist_detail_album_img img').on('click', function() {
				var params = {};
				params.artist_name        = base.artist_name;
				params.album_name         = $(this).parent().children('.album_name_input').val();
				params.album_mbid_itunes  = $(this).parent().children('.album_mbid_itunes_input').val();
				params.album_mbid_lastfm  = $(this).parent().children('.album_mbid_lastfm_input').val();
				params.album_id           = $(this).parent().children('.album_id_input').val();
				params.album_url_itunes   = $(this).parent().children('.album_url_itunes_input').val();
				params.album_url_lastfm   = $(this).parent().children('.album_url_lastfm_input').val();
				params.album_image        = $(this).parent().children('.album_image_input').val();

				base.album._setAlbumTracks(params);
			});

			// 待ち画面消去＆画面出力
			setTimeout(function() {
				base.artmix._appendShow();
				$('#music_write_more_album_link').fadeIn();
				$('#music_write_more_album_area .loading').remove();
			}, 1000);
		});
	};



	/***********************************************
	 * アルバムレビューフォームイベントクラス  *
	 ***********************************************/
	 base.album = {};

	// フォームイベントリスナ
	base.album.setEventListner = function() {
		// アルバムエリアトラックタイトルクリック(トラック個別リンク)
		$('#music_write_album_selected_tracks_area .track_name').click(function() {
			var params = {};
			params.album_id   = $('#music_write_album_id').val();
			params.track_name = $(this).children('.track_name_hidden').val();
			params.track_mbid_itunes = $(this).children('.track_mbid_itunes').val();
			params.track_mbid_lastfm = $(this).children('.track_mbid_lastfm').val();
			params.track_id   = $(this).children('.track_id').val();

			// apiからtrack_data取得
			base.track._setTrackDetail(params);

			return true;
		});
	};

	base.album._init = function() {
		$('#music_write_album_search_button').removeAttr('disabled');
		if ($('#music_write_album_selected_tracks').html().replace(/[\s]*/, '').length === 0) {
			$('#music_write_album_comment_review_textarea').val(null);
			$('#music_write_album_star').val(0);
			$('#music_write_album_hidden_form').find('input').val(null);
			$('#music_write_review_album_search_error').html(null);
		}
		return true;
	};

	// アルバム収録トラック ＆レビュー取得
	var album_all_preview_timer = null;
	base.album._setAlbumTracks = function(params) {
		var data = {};
		clearInterval(album_all_preview_timer);
		$('#current_track').html(null);
		$('#current_time').html(null);
		$('#review_music_write_listen_all').css('background', '#333').css('color', '#fff').css('text-decoration', 'none');

		data.about             = 'album';
		data.artist_id         = base.artist_id;
		data.artist_name       = base.artist_name;
		data.album_id          = params.album_id;
		data.album_url_itunes  = params.album_url_itunes;
		data.album_url_lastfm  = params.album_url_lastfm;
		data.album_name        = params.album_name;
		data.album_image       = params.album_image;
		data.album_mbid_itunes = params.album_mbid_itunes;
		data.album_mbid_lastfm = params.album_mbid_lastfm;

		if (data.album_mbid_itunes.length > 0) {
			var segment = data.album_url_itunes.replace(/^.+album\//i, '').match(/[^\/]+/i);
			var mbid    = data.album_mbid_itunes;
			var href = "https://geo.itunes.apple.com/jp/album/"+ segment +"/id"+ mbid +"?at=1000l6TJ&app=itunes";
			$('#artist_detail_itunes_link_div a.itunes_link').attr('href', href);
			$('#artist_detail_itunes_link_div a').css('display', 'inline-block');
		}

		// フォーム初期化
		base.album._init();
		$('#music_write_album_name_disp').html(null);
		$('#music_write_album_artist_disp').html(null);
		$('#music_write_album_release').html(null);
		$('#music_write_album_copyright').html(null);
		$('#music_write_album_selected_image_span').html(null);
		$('#music_write_album_selected_tracks').html(null);
		$('#music_write_review_album_search_result').html(null);

		// 取得アルバムデータをセット
		$('#music_write_album_id')            .val(data.album_id);
		$('#music_write_album_url_itunes')    .val(data.album_url_itunes);
		$('#music_write_album_url_lastfm')    .val(data.album_url_lastfm);
		$('#music_write_album_name_hidden')   .val(data.album_name);
		$('#music_write_hidden_album_image')  .val(data.album_image);
		$('#music_write_album_mbid_itunes')   .val(data.album_mbid_itunes);
		$('#music_write_album_mbid_lastfm')   .val(data.album_mbid_lastfm);
		$('#music_write_album_selected_image span').html('<img src="' + data.album_image + '">');

		// アルバムタイトルを表示
		$('#music_write_album_name_disp').html("<a href='/album/detail/" + data.album_id + "/'>"+ data.album_name + '</a>');
		$('#music_write_album_artist_disp').text($('#music_write_artist_name').val());

		// アルバムアートを表示
		$('#music_write_album_selected_image').fadeIn('slow');
		setTimeout(function(){
			$('#music_write_hover_album_main_div').css('display', 'none');
		}, 500);

		// アルバム詳細までスクロール
		base.scrollToSection('music_write_album_title');

		// アルバムトラックを取得
		base.album._getAlbumTracksFromAjax(data).done(function(res) {
			if (typeof(res.success) === 'undefined'){
				alert('トラック取得に失敗しました(undefined)');
			}
			if (res.success === false){
				alert('トラック取得に失敗しました(false)');
			}

			// ローディング画面を消す
			$('.loading').css('display', 'none');
			$('#music_write_music_area').children('.loading').removeClass('loading');
			if (res.length == 0 || res.result.arr_list.length == 0){
				$('#music_write_album_release').append('アルバム情報は取得できませんでした');
				return true;
			}
			var is_active_preview_button = false;
			$('#review_music_write_listen_all').css('visibility', 'hidden');
			var release_itunes = res.result.release_itunes.match(/^[0-9]{4}/);
			if (release_itunes != '0000') {
				$('#music_write_album_release').html(release_itunes + '年発売');
				$('#music_write_album_copyright').html(res.result.copyright_itunes);
			}

			res.result.arr_list.forEach(function(e, ans){
				var html = '';
				html += '<li class="track_name_list">';
				if (e['preview_itunes'].length > 0) {
					html += '<span class="preview_button" id="music_write_track_list_preview_button_'+ base.htmlentities(e['id']) +'">▶️</span>';
					html += '<audio class="preview_itunes" id="music_write_track_list_preview_itunes_'+ base.htmlentities(e['id']) +'">';
					html += '<source src="'+ e['preview_itunes'] +'" id="music_write_track_list_preview_itunes_source_'+ base.htmlentities(e['id']) +'">';
					html += '<a href="'+ e['preview_itunes'] +'" target="new_win">▶️</a>';
					html += '</audio>';
					is_active_preview_button = true;
				}
				html += '<span class="track_name"><a href="/track/detail/' +  base.htmlentities(e['id']) +'">' + base.htmlentities(e['name']) + '</a></span>';
				html += '<input type="hidden" value="' + base.htmlentities(e['id'])          + '" class="track_id">';
				html += '<input type="hidden" value="' + base.htmlentities(e['name'])        + '" class="track_name_hidden">';
				html += '<input type="hidden" value="' + base.htmlentities(e['mbid_itunes']) + '" class="track_mbid_itunes">';
				html += '<input type="hidden" value="' + base.htmlentities(e['mbid_lastfm']) + '" class="track_mbid_lastfm">';
				html += '<input type="hidden" value="' + base.htmlentities(e['url_itunes'])  + '" class="track_url_itunes">';
				html += '<input type="hidden" value="' + base.htmlentities(e['url_lastfm'])  + '" class="track_url_lastfm">';
				html += '</li>';
				$('#music_write_album_selected_tracks').append(html);
				$('#music_write_album_selected_tracks').listview('refresh');
			});

			var global_album_id = null;
			var global_timer = null;
			var is_all_play  = false;
			var number = 0;
			var a = {};

			// 全曲再生
			if (is_active_preview_button === true) {
				$('#all_play_div').css('display', 'block');
				$('#review_music_write_listen_all').css('visibility', 'visible');

				$('#review_music_write_listen_all').on('click', function() {
					$(this).css('background', 'red').css('color', '#fff');
					reset_preview(number);

					if (is_all_play === true) {
						is_all_play = false;
						$('.loading').hide();
						$('#review_music_write_listen_all').css('background', '#333').css('color', '#fff');
						return true;
					}

					is_all_play = true;
					$(this).css('background', 'red').css('color', '#fff');
					var all_count = $('.preview_button').length;
					if ($('#music_write_album_id').val() != global_album_id) {
						a = {};
						global_album_id = $('#music_write_album_id').val();
						for (i=0; i<all_count; i++) {
							a[i] = $('.preview_itunes').eq(i)[0];
							a[i].load();
						}
					}

					var disp_track_number = number + 1;
					$('#current_track').html('TRACK<span id="current_track_num">' + disp_track_number + "</span>");
					$('.loading').hide();
					a[number].play();
					$('.preview_button').eq(number).html('◾');
					$('.preview_button').eq(number).addClass('preview_button_pause');

					album_all_preview_timer = setInterval(function() {
						var current_time = Math.round(a[number].currentTime * 100)/100;
						$('#current_time').html(current_time);
						if (a[number].paused === true && a[number].currentTime > 0) {
							$('.preview_button').eq(number).html('▶️');
							$('.preview_button').eq(number).removeClass('preview_button_pause');
							number = number + 1;
							$('.preview_button').eq(number).html('◾');
							$('.preview_button').eq(number).addClass('preview_button_pause');

							if (number >= all_count) {
								reset_preview(number);
								$('#review_music_write_listen_all').css('background', '#333').css('color', '#fff');
								number = 0;
								is_all_play = false;
								return false;
							} else {
								disp_track_number = number + 1;
								$('#current_track').html('TRACK<span id="current_track_num">' + disp_track_number + '</span>');
								a[number].play();
							}
						}
					}, 200);
				});


				// 個別再生ボタンクリック時
				$('.preview_button').on('click',
					function() {
						var global_i = $('.preview_button').index(this);

						// 一旦全アルバムトラック再生を停止させる
						reset_preview(global_i);
						$('#review_music_write_listen_all').css('background', '#333').css('color', '#fff').css('text-decoration', 'none');

						var a = $('.preview_itunes').eq(global_i)[0];

						if (a.currentTime === 0) {
							a.play();
							var track_number = global_i + 1;
							$('#current_track').html('TRACK<span id="current_track_num">' + track_number + "</span>");
							$(this).html('<span class="stop_mark">◾</span>');
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
					}
				);


				function reset_preview(g_i) {
					//a = {};
					clearInterval(album_all_preview_timer);
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
				};

				// アルバムエリアトラックタイトルクリック(トラック個別リンク)
				$('#music_write_album_selected_tracks_area .track_name').click(function() {
					// 一旦全アルバムトラック再生を停止させる
					$('.preview_itunes').each(function(i, ans) {
						ans.pause();
						$('.preview_button').eq(i).html('▶️');
						$('.preview_button').removeClass('preview_button_pause');
					});

					var params = {};
					params.album_id   = data.album_id;
					params.track_name = $(this).parent().children('.track_name_hidden').val();
					params.track_mbid_itunes = $(this).parent().children('.track_mbid_itunes').val();
					params.track_mbid_lastfm = $(this).parent().children('.track_mbid_lastfm').val();
					params.track_id   = $(this).parent().children('.track_id').val();

					// apiからtrack_data取得
					base.track._setTrackDetail(params);

					return true;
				});
			}
		});
	};

	base.album._getAlbumTracksFromAjax = function(params) {
		var data = {};
		data = params;
		return $.ajax({
			type: 'post',
			url: '/api/track/albumtracklist.json',
			datatype: 'json',
			data: JSON.stringify(data),
			contentType: 'application/json',
			cache: false,
			success: function(res, ans) {
				return res;
			},
			error: function(){
				return false;
			}
		});
	};


	base.tracklist = {};
	base.tracklist.tracklist_id = '';
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
			$('#tracklist_detail_time_display').html('00:00');
		} else {
			html = 'Track ';
			html = html + (parseInt(base.tracklist.current_index, 10) + 1);
		}
		$('#tracklist_detail_current_track_display').html(html);
	};
	base.tracklist.modal_close = function() {
		$("#lean_overlay").fadeOut(80, function() {
			$('#tracklist_detail').css({ 'display' : 'none' });
		});
		history.replaceState('','','/artist/detail/' + base.artist.id + '/');
		$('meta[property="og:description"]').attr('content', base.tracklist.meta_description);
		$('meta[property="twitter:description"]').attr('content', base.tracklist.meta_description);
	};

	base.tracklist.getAjax = function() {
		var params = {
			artist_id    : $('#form_artist_id').val(),
			tracklist_id : base.tracklist.tracklist_id,
		};
		return $.ajax({
			type : 'post',
			url  : '/api/tracklist/detail.json',
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

	base.tracklist.deleteAjax = function() {
		var params = {
				tracklist_id : base.tracklist.tracklist_id,
				user_id      : $('#form_user_id').val(),
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
		base.tracklist.getAjax().done(function() {
			if ( ! base.tracklist.ajax_response.title) {
				if ($('#form_tracklist_id').val().length === 0) {
					alert('こちらの投稿はユーザによって削除された可能性がございます。');
				} else {
					$('#form_tracklist_id').val(null);
				}
				return true;
			}
			if (base.tracklist.ajax_response.title.length === 0 ) {
				return true;
			}
			if (base.tracklist.ajax_response.user_id) {
				var artist_name = base.tracklist.ajax_response.arr_list[0].artist_name;
				if (artist_name === null ) {
					artist_name = '複数のアーティスト';
				}
				if ($('#form_user_id').val() == base.tracklist.ajax_response.user_id) {
					$('#tracklist_detail_title').html(base.tracklist.ajax_response.title + " / "
						+ artist_name +" <a id='tracklist_detail_delbtn'><span style='display:inline-block;'>[削除]</span></a> ");
				} else {
					$('#tracklist_detail_title').html(base.tracklist.ajax_response.title + " / "
						+ artist_name);
				}
				$('#tracklist_detail_user_name').html('by ' + '<a href="/user/you/' + base.tracklist.ajax_response.user_id + '/">'
						+ base.tracklist.ajax_response.user_name + '</a>さん');
			} else {
				$('#tracklist_detail_title').html(base.tracklist.ajax_response.title);
				$('#tracklist_detail_user_name').html('by ' + base.tracklist.ajax_response.user_name + 'さん');
			}
			$('#tracklist_detail_created_at').html(base.tracklist.ajax_response.created_at);
			var html = "";
			jQuery.each(base.tracklist.ajax_response.arr_list, function(i, v) {
				html += "<li class='ui-li-static'>";
				html += "<span class='tracklist_play_btn play_mark' title='プレイ！'>▶️</span>";
				html += "<audio class='preview_tracklist'>";
				html += "<source src='"+ v.preview_itunes +"'></source>";
				html += "<a target='new_win' href='" + v.preview_itunes + "'>▶️</a>";
				html += "</audio>";
				html += "<span>" + v.sort + ".&nbsp;</span>";
				html += "<span>" + v.track_name +"</span>";
				html += "<span class='tracklist_list_artist'><a class='ui-link' href='/artist/detail/"+ v.artist_id +"/'>" + v.track_artist_name +"</a></span>";
				html += "</li>";
			});
			$('#tracklist_detail_ul').html(html);
			$('#tracklist_detail_link_to_leanModal').click();

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
							$('#tracklist_detail_time_display').html(current_time);
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
					$('#tracklist_detail_time_display').html(current_time);
					if (base.tracklist.arr_tracks[base.tracklist.current_index].paused === true && base.tracklist.arr_tracks[base.tracklist.current_index].currentTime > 0) {
						$('#tracklist_detail_time_display').html('00.00');
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

			$('#tracklist_detail_delbtn').on('click', function() {
				if (confirm('こちらの投稿を削除しますか？')) {
					base.tracklist.deleteAjax().done(function() {
						location.href = '/artist/detail/' + base.artist.id + '/';
						return true;
					});
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

		});
	};

	base.tracklist.setEventListner = function() {
		$('.tracklist_list_li').off('click');
		if ($('#form_tracklist_id').val() > 0) {
			base.tracklist.tracklist_id = parseInt($('#form_tracklist_id').val().match(/[0-9]*/));
			base.tracklist.dispTracklist();
			var title = $('#tracklist_detail_id_' + base.tracklist.tracklist_id).find('.tracklist_list_title').eq(0).html();
			var user  = $('#tracklist_detail_id_' + base.tracklist.tracklist_id).find('.tracklist_list_user').eq(0).children('span').html();
			$('meta[property="og:description"]').attr('content', title + ' / by ' + user);
			$('meta[property="twitter:description"]').attr('content', title + ' / by ' + user);
		}
		$('.tracklist_list_li').hover(
			function() {
				$(this).css('background', 'rgba(100,100,100,0.2)');
				$(this).css('cursor', 'pointer');
			},
			function() {
				$(this).css('background', 'inherit');
			}
		);

		$('.tracklist_list_li').on('click', function() {
			// イベント初期化
			$('#tracklist_detail_all_play_btn').off('click');
			$('.tracklist_play_btn').off('click');
			$("#lean_overlay").off('click');
			base.tracklist.tracklist_id = parseInt($(this).attr('id').match(/[\d]+$/), 10);
			base.tracklist.dispTracklist();
			history.replaceState('','','?tracklist_id=' + base.tracklist.tracklist_id);
			var title = $(this).find('.tracklist_list_title').eq(0).html();
			var user  = $(this).find('.tracklist_list_user').eq(0).children('span').html();
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
			$('#tracklist_detail_time_display').html('00.00');
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


	// レビュー
	$('.review_list_li').hover(
			function() {
				$(this).css('background', 'rgba(100,100,100,0.2)');
				$(this).css('cursor', 'pointer');
			},
			function() {
				$(this).css('background', 'inherit');
			}
		);
	$('.review_list_li').on('click',
			function() {
				var index     = $(this).index('.review_list_tr');
				var about     = $('.about').eq(index).html();
				var review_id = $('.review_id').eq(index).html();
				location.href = '/review/music/detail/' + about + '/' + review_id + '/';
			}
		);

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

	// アーティストイベントをセット
	base.artist.setEventListner();

	// トラックリストイベントをセット
	base.tracklist.setEventListner();

	// アートマウスオーバーをセット
	base.artmix._hoverArt();

	$('#artist_detail_album_img').mouseleave(function(){
		setTimeout(function(){
			$('#music_write_hover_album_main_div').css('display', 'none');
		}, 80);
	});

	// アートクリック
	$('#artist_detail_album_img img').on('click', setAlbumTracks);

	function setAlbumTracks() {
		setTimeout(function(){
			$('#music_write_hover_album_action').html(null);
			$('#music_write_hover_album_name').html(null);
			$('#music_write_hover_album_art').html(null);
		}, 200);

		var index = $(this).parent('.album_image').index();
		$('#all_play_div').css('display', 'none');

		// 待ち画面
		var loading_html = base.generateLoadingHtml();
		$('#music_write_music_area').prepend(loading_html);
		$('#music_write_music_area .loading').fadeIn('fast');

		var params = {};
		params.artist_name        = base.artist_name;
		params.album_name         = $('.album_name_input').eq(index).val();
		params.album_mbid_itunes  = $('.album_mbid_itunes_input').eq(index).val();
		params.album_mbid_lastfm  = $('.album_mbid_lastfm_input').eq(index).val();
		params.album_id           = $('.album_id_input').eq(index).val();
		params.album_url_itunes   = $('.album_url_itunes_input').eq(index).val();
		params.album_url_lastfm   = $('.album_url_lastfm_input').eq(index).val();
		params.album_image        = $('.album_image_input').eq(index).val();

		base.album._setAlbumTracks(params);
	}

	// アルバムアート一覧(もっとみる)を取得
	$('#music_write_more_album_link').click(function() {
		base.artmix.getMoreAlbum();
	});


});