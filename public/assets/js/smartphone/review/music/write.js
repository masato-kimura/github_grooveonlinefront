$(document).bind("pageinit", function(){
	$.mobile.ajaxEnabled = false;
});

jQuery(function($) {

	/* コメントボックス */
	var modal = $('a[rel*=leanModal]').leanModal({
		top: 120,
		overlay : 0.5,
		closeButton: ".modal_close",
	});

	$('.modaldiv .modal_link').on('click', function() {
		var text = $(this).parent().children('.main_text').html();

		switch($(this).parent().parent().parent().attr('id')) {
			case 'modaldiv_artist':
				var modal_id = '#modaldiv_artist';
				var textarea = $('#music_write_artist_comment_review_textarea');
				var send_btn = $('#music_write_artist_submit');
				break;
			case 'modaldiv_album':
				var modal_id = '#modaldiv_album';
				var textarea = $('#music_write_album_comment_review_textarea');
				var send_btn = $('#music_write_album_submit');
				break;
			case 'modaldiv_track':
				var modal_id = '#modaldiv_track';
				var textarea = $('#music_write_track_comment_review_textarea');
				var send_btn = $('#music_write_track_submit');
				break;
		}

		$("#lean_overlay").fadeOut(200);
		$(modal_id).css({ 'display' : 'none' });
		setTimeout(function() {
			$(textarea).val(text);
			$(textarea).css('color', '#000');
			$(send_btn).removeClass('send_disabled_btn');
			$(send_btn).addClass('send_btn');
			$(send_btn).prop('disabled', false);
		}, 300);
	});

	$('.modaldiv .modal_link_delete_div input[type=button]').on('click', function() {
		var obj_this = $(this);
		var _removeusercomment = function(data) {
			return $.ajax({
				type: 'post',
				url: $('#music_write_api_url_removeusercomment').val(), // api/review/removeusercomment.json
				datatype: 'json',
				data: JSON.stringify(data),
				contentType: 'application/json',
				cache: false,
				success: function(obj_response, ans) {
					return obj_response;
				},
				error: function() {
					alert('_setusercomment error');
					return false;
				}
			});
		};

		var user_comment_id = $(this).parent().parent().children('.user_comment_id').html();
		var user_comment    = $(this).parent().parent().children('.main_text').html();
		if ( ! confirm('コメントボックスから『' + user_comment + '』を削除しますか')) {
			return true;
		}
		var remove_data = {};
		remove_data.id = user_comment_id;
		_removeusercomment(remove_data).done(function(res) {
			if (res.success == true) {
				obj_this.parent().parent('li').remove();
			}
		});
	});

	var base = {};
	base.artist_id   = $('#music_write_artist_id').val();
	base.artist_name = $('#music_write_artist_name').val();
	base.construct   = function() {
		base.album._init();
		base.track._init();

		// 初回表示時にテキストが存在した場合
		$('.comment_review_textarea').each(function(i,v) {
			var submit_btn = $(this).parent().parent().nextAll('.submit_area').children('.music_write_submit');
			var delete_btn = $(this).parent().parent().nextAll('.submit_area').children('.review_delete_button');
			var delete_btn_id = delete_btn.attr('id');
			submit_btn.attr('disabled', 'disabled');
			submit_btn.removeClass('send_btn');
			submit_btn.addClass('send_disabled_btn');
			if (delete_btn_id !== 'music_write_artist_delete') {
				$('#' + delete_btn_id).attr('disabled', 'disabled');
			}
			$(this).css('color', '#999');
			var id = $(this).attr('id');
			switch (id) {
				case 'music_write_artist_comment_review_textarea':
					var about = 'アーティスト';
					var about_e = 'artist';
					var star_val = $('#music_write_artist_star').val();
					var star_tmp = $('#music_write_artist_star_tmp').val();
					star_tmp = (star_tmp === '')? 0: '';
					break;
				case 'music_write_album_comment_review_textarea':
					var about = 'アルバム';
					var about_e = 'album';
					var star_val = $('#music_write_album_star').val();
					var star_tmp = $('#music_write_album_star_tmp').val();
					star_tmp = (star_tmp === '')? 0: '';
					break;
				case 'music_write_track_comment_review_textarea':
					var about = 'トラック';
					var about_e = 'track';
					var star_val = $('#music_write_track_star').val();
					var star_tmp = $('#music_write_track_star_tmp').val();
					star_tmp = (star_tmp === '')? 0: '';
					break;
			}
			var comment_val = $(this).val();
			var comment_tmp = $('#' + id + '_tmp').val();

			// 未ログイン状態でレビューが入りログイン後戻ってきた場合
			if ($('#music_write_already_artist_review').val() !== 'true') {
				if (comment_val != comment_tmp || star_val != star_tmp) {
					submit_btn.removeAttr('disabled');
					submit_btn.removeClass('send_disabled_btn');
					submit_btn.addClass('send_btn');
					delete_btn.removeAttr('disabled');
					delete_btn.removeClass('delete_disabled_btn');
					delete_btn.addClass('delete_btn');
					$(this).css('color', '#111');
					if ($('#logout').length > 0) {
						alert('ログインありがとうございます。\n\rまだレビュー投稿は完了しておりませんので、\n\rこのあと『' + about + 'レビューを投稿』ボタンをクリックしてください。');
					}
					return true;
				}
			}
		}); // each

		// テキストエリアのリサイズ
		$('.textarea_resize_up').on('click', function(){
			var textarea = $(this).parent().parent().children('textarea');
			if (textarea.val().length > 0){
				var height = textarea.css('height');
				height = parseInt(height, 10);
				var resize_height = height - 180;
				if (resize_height > 75) {
					textarea.css('height', resize_height + 'px');
				} else {
					textarea.css('height', '75px');
				}
			}
		});
		$('.textarea_resize_down').on('click', function(){
			var textarea = $(this).parent().parent().children('textarea');
			if (textarea.val().length > 0){
				var height = textarea.css('height');
				height = parseInt(height, 10);
				textarea.css('height', height + 180 + 'px');
			}
		});

	};

	base.imageUrlConvert = function(url, size) {
		return url.replace(/\/serve\/[0-9s]+\//i, '/serve/' + size + '/');
	};

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

	base._getMyReview = function(data) {
		return $.ajax({
			type: 'post',
			url: $('#music_write_api_url_search_review').val(), // api/review/one.json
			datatype: 'json',
			data: JSON.stringify(data),
			contentType: 'application/json',
			cache: false,
			success: function(obj_response, ans) {
				return obj_response;
			},
			error: function() {
				return false;
			}
		});
	};

	base._getTextareaRows = function(selector) {
		var tx = selector.val();
		//var num = tx.match(/\r\n/g); // for IE
		var num = tx.match(/\n/g); // for FireFox
		if (num != null){
			var rows = num.length + 1;
		} else {
			var rows = 1;
		}
		return rows;
	}

	base.htmlentities = function(str) {
		return str.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
	};


	base.develop = {}; // 開発環境用表示クラス
	base.artmix  = {}; // 画面右側アルバムアート一覧クラス
	base.artist  = {}; // 画面左側アーティストレビュークラス
	base.album   = {}; // 画面左側アルバムレビュークラス
	base.track   = {}; // 画面左側トラックレビュークラス





	/*********************************
	 * 開発環境用表示クラス          *
	 *********************************/
	base.develop.toHidden = function(){
		//$('.hidden_form input').addClass('data-role-none');
		$('.hidden_form').css('visibility', 'hidden');
	};



	/*********************************
	 * アーティストクラス  *
	 *********************************/
	base.artist.setEventListner = function(){
		// レビューテキストサイズに合わせて高さを調整
		var selector = $('#music_write_artist_comment_review_textarea');
		if (selector.val().length == 0){
			selector.css('height', '75px');
		} else {
			var rows = base._getTextareaRows(selector);
			selector.css('height', (rows * 25) + 75 + 'px');
		}

		$('#form_favorite_artist_status').on('change', function(){
			var params = {
				client_user_id: $('#form_client_user_id').val(),
				favorite_artist_id: $('#music_write_artist_id').val(),
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
	}



	/*********************************
	 * アルバムアート一覧クラス  *
	 *********************************/
	base.artmix._page = 1;
	base.artmix._loading = function() {
		$('#music_write_album_art_section .loading').css('display', 'block');
	};
	base.artmix._loaded = function() {
		$('#music_write_album_art_section .loading').fadeOut('fast');
	};
	base.artmix._append = function(html) {
		$('#music_write_album_append_area').append(html);
	};
	base.artmix._appendShow = function() {
		$('#music_write_album_append_area').fadeIn('slow');
		$('#music_write_more_album_link').fadeIn('slow');
		$('.more').fadeIn('slow');
	};
	base.artmix._hoverArt = function() {
		$('#music_write_album_append_area .album_image img').on('mouseenter',
			function() {
				var image_tag = '<img src="' + $(this).parent().children('.album_image_input').val() + '">';
				var album_name = $(this).parent().children('.album_name_input').val();
				if ($(this).parent().children('.album_mbid_itunes_input').val().length > 0) {
					album_name += '&nbsp;*';
				}
				//$('#music_write_hover_album_action').html('<a class="to_album_review">&emsp;アルバム情報をみる</a><span class="close_art">閉じる</span>');
				$('#music_write_hover_album_name').html(album_name);
				$('#music_write_hover_album_art').html(image_tag);

				$('#music_write_hover_album_main_div').css('top', $(this).position().top + 100);
				$('#music_write_hover_album_main_div').fadeIn('fast');

				$('.to_album_review').on('click', setAlbumTracks);
				$('.close_art').on('click', function() {
					setTimeout(function() {
						$('#music_write_hover_album_action').html(null);
						$('#music_write_hover_album_art').html(null);
						$('#music_write_hover_album_name').html(null);
					}, 50);
					return true;
				});
			}
		);
	};

	// アルバムアート一覧を取得
	base.artmix.getAlbum = function() {
		// 待ち画面
		base.artmix._loading();

		// 初期化
		$('#music_write_album_art_section img').off('click');

		// パラメータセット
		var data ={};
		data.artist_name = base.artist_name;
		data.artist_id   = base.artist_id;
		data.limit       = 20;
		data.page        = 1;

		$('#music_write_album_append_area').mouseleave(function(){
			$('#music_write_hover_album_main_div').css('display', 'none');
		});

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
				base.artmix._loaded();
				base.artmix._append('<span id="music_write_album_list_none">アルバム情報は取得できませんでした</span>');
				$('#music_write_more_album_link').remove();
				base.artmix._appendShow();
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
				var html = '<span class="album_image">';
				html += '<input type="hidden" value="' + e['id']          + '" class="album_id_input">';
				html += '<input type="hidden" value="' + e['name']        + '" class="album_name_input">';
				html += '<input type="hidden" value="' + e['mbid_itunes'] + '" class="album_mbid_itunes_input">';
				html += '<input type="hidden" value="' + e['mbid_lastfm'] + '" class="album_mbid_lastfm_input">';
				html += '<input type="hidden" value="' + e['url_itunes']  + '" class="album_url_itunes_input">';
				html += '<input type="hidden" value="' + e['url_lastfm']  + '" class="album_url_lastfm_input">';
				html += '<input type="hidden" value="' + e['image_extralarge'] + '" class="album_image_input">';
				html += '<img src="' + e['image_medium'] + '" class="art_small"  title="アルバム 『' + e['name'] + '』 をレビュー">';
				html += '</span>';
				base.artmix._append(html);
			});

			// アルバムアートイベントリスナのセット
			base.artmix._hoverArt();

			// アルバムアートクリック
			$('#music_write_album_art_section img').on('click', setAlbumTracks);

			// 待ち画面消去＆画面出力
			setTimeout(function() {
				base.artmix._appendShow();
			}, 1000);
		});
	};


	// アルバムアート一覧(もっと)を取得
	base.artmix.getMoreAlbum = function() {
		$('#music_write_more_album_link').css('display', 'none');

		// 待ち画面
		var loading_html = base.generateLoadingHtml();
		$('#music_write_more_album_area').prepend(loading_html);
		$('#music_write_more_album_area .loading').fadeIn('slow');

		// パラメータセット
		var data ={};
		data.artist_name = base.artist_name;
		data.artist_id   = base.artist_id;
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
				html += '<input type="hidden" value="' + e['url_lastfm']  + '" class="album_url_lastfm_input">';
				html += '<input type="hidden" value="' + e['image_extralarge'] + '" class="album_image_input">';
				html += '<img src="' + e['image_medium'] + '" class="art_small"  title="アルバム 『' + e['name'] + '』 をレビュー">';
				html += '</span>';
				base.artmix._append(html);
			});

			$(".lazy img").lazyload({
				effect: "fadeIn" ,
				effect_speed: 300 ,
			});
			$('img').error(function() {
				$(this).attr('src', '/assets/img/default.jpg');
			});

			// アルバムアートイベントリスナのセット
			base.artmix._hoverArt();

			// アルバムアートクリック
			$('#music_write_album_art_section img').off('click');
			$('#music_write_album_art_section img').on('click', setAlbumTracks);

			// 待ち画面消去＆画面出力
			setTimeout(function() {
				base.artmix._appendShow();
				$('#music_write_more_album_link').fadeIn();
				$('#music_write_more_album_area .loading').remove();
			}, 1000);
		});
	};

	function setAlbumTracks() {
		var obj = this;
		base.scrollToSection('music_write_album_review_section');

		$('#music_write_hover_album_main_div').css('display', 'none');
		$('#music_write_hover_album_action').html(null);
		$('#music_write_hover_album_art').html(null);
		$('#music_write_hover_album_name').html(null);

		var index = $(obj).parent('.album_image').index();

		$('#all_play_div').css('display', 'none');

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

	$('#music_write_more_album_link').click(function() {
		base.artmix.getMoreAlbum();
	});




	/**
	 * by artist_id
	 */
	base.artmix._getAlbumListFromAjax = function(data) {
		return $.ajax({
			type:        'post',
			url:         $('#music_write_api_url_album_list').val(), // api/album/list.json
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

	/***************************************************
	 * フォームスクロールクラス  *
	 ***************************************************/
	base.scrollEventListner = function() {

		// アルバムレビュー存在時
		if ($('#music_write_album_delete').prop('disabled') === false) {
			setTimeout(function() {
				base.scrollToSection('music_write_album_scroll_header');
			}, 300);
			return true;
		}

		// トラックレビュー存在時
		if ($('#music_write_track_delete').prop('disabled') === false) {
			setTimeout(function() {
				base.scrollToSection('music_write_track_scroll_header');
			}, 300);
			return true;
		}

		// アーティストレビューエリアへ移動してくる
		$('.to_top').click(function() {
			var id = $(this).attr('id');
			switch (id) {
				case 'music_write_to_artist_review':
					base.scrollToSection('head');
					break;
				case 'music_write_to_album_review':
					base.scrollToSection('music_write_album_review_section');
					break;
				case 'music_write_to_track_review':
					base.scrollToSection('music_write_track_review_section');
					break;
			}
		});
	};

	base.scrollToSection = function(id, callback) {
		var speed = 'fast';
		var target = $('#' + id);
		var position = target.offset().top;
		$('body,html').animate({scrollTop:position}, speed, 'swing', callback);
	};




	/***************************************************
	 * スターレビュークラス  *
	 ***************************************************/
	base.starReviewEventListner = function() {

		// スターレビュー更新時
		$('.star_select').change(function() {
			switch ($(this).attr('id')) {
				case 'music_write_artist_star':
					if ($('#music_write_artist_id').val().length === 0) {
						$(this).val(0);
						return false;
					}
					break;
				case 'music_write_album_star':
					if ($('#music_write_album_id').val().length === 0 && $('#music_write_album_not_exist_flag').val() == false) {
						$(this).val(0);
						return false;
					}
					break;
				case 'music_write_track_star':
					if ($('#music_write_track_id').val().length === 0 && $('#music_write_track_not_exist_flag').val() == false) {
						$(this).val(0);
						return false;
					}
					break;
			}
			var submit_btn = $(this).parent().nextAll('.submit_area').children('.music_write_submit');
			var id = $(this).attr('id');
			if (($(this).val() != $('#' + id + '_tmp').val())) {
				submit_btn.removeAttr('disabled');
				submit_btn.removeClass('send_disabled_btn');
				submit_btn.addClass('send_btn');
			} else {
				submit_btn.attr('disabled', 'disabled');
				submit_btn.removeClass('send_btn');
				submit_btn.addClass('send_disabled_btn');
			}
		});

		// スターレビューリセット時
		$('.rateit-reset').on('click', function() {
			var submit_btn = $(this).parent().parent().nextAll('.submit_area').children('.music_write_submit');
			var id = $(this).parent().prevAll('.star_select').attr('id');
			if ($('#' + id + '_tmp').val() != 0) {
				submit_btn.removeAttr('disabled');
				submit_btn.removeClass('send_disabled_btn');
				submit_btn.addClass('send_btn');
			} else {
				submit_btn.attr('disabled', 'disabled');
				submit_btn.removeClass('send_btn');
				submit_btn.addClass('send_disabled_btn');
			}
		});
	};




	/***************************************************
	 * レビュー投稿クラス  *
	 ***************************************************/
	base.sendReviewEventListner = function() {

		var _sendReview = function(data) {
			return $.ajax({
				type: 'post',
				url: $('#music_write_api_url_send_review').val(), // api/review/set.json
				datatype: 'json',
				data: JSON.stringify(data),
				contentType: 'application/json',
				cache: false,
				success: function(obj_response, ans) {
					return obj_response;
				},
				error: function(){
					alert('base._sendReview error');
					return false;
				}
			});
		};

		// 入力ボックスのフォーカス取得時
		$('.comment_review_textarea').on('keydown focus', function() {
			switch ($(this).attr('id')) {
				case 'music_write_artist_comment_review_textarea':
					if ($('#music_write_artist_id').val().length === 0) {
						return false;
					}
					break;
				case 'music_write_album_comment_review_textarea':
					if ($('#music_write_album_id').val().length === 0 && $('#music_write_album_not_exist_flag').val() == false) {
						return false;
					}
					break;
				case 'music_write_track_comment_review_textarea':
					if ($('#music_write_track_id').val().length === 0 && $('#music_write_track_not_exist_flag').val() == false) {
						return false;
					}
					break;
			}
			$(this).css('color', '#000');
		});

		// 入力ボックスのフォーカス消失時
		$('.comment_review_textarea').on('keyup blur', function() {
			var submit_btn = $(this).parent().parent().nextAll('.submit_area').children('.music_write_submit');
			var id = $(this).attr('id');
			if ($(this).val() !== $('#' + id + '_tmp').val()) {
				submit_btn.removeAttr('disabled');
				submit_btn.removeClass('send_disabled_btn');
				submit_btn.addClass('send_btn');
				$(this).css('color', '#111');
			} else {
				submit_btn.attr('disabled', 'disabled');
				submit_btn.removeClass('send_btn');
				submit_btn.addClass('send_disabled_btn');
				$(this).css('color', '#999');
			}
		});

		$('.music_write_submit').click(function() {
			var id = $(this).attr('id');
			var about = null;
			switch (id) {
				case 'music_write_artist_submit':
					about = 'artist';
					break;
				case 'music_write_album_submit':
					about = 'album';
					break;
				case 'music_write_track_submit':
					about = 'track';
					break;
				default:
					return false;
			} //switch

			var review_tmp   = $('#music_write_'+ about +'_comment_review_textarea_tmp').val();
			var star_tmp     = $('#music_write_'+ about +'_star_tmp').val();
			var data = {};
			data.about       = about;
			data.artist_id   = base.artist_id;
			data.artist_name = base.artist_name;
			data.review_id   = $('#music_write_'+ about +'_review_id').val();
			data.review      = $('#music_write_'+ about +'_comment_review_textarea').val();
			data.star        = $('#music_write_'+ about +'_star').val();
			data.artist_url  = $('#music_write_'+ about +'_url').val();
			switch (about) {
				case 'album':
					data.album_id            = $('#music_write_album_id').val();
					data.album_name          = $('#music_write_album_name').val();
					data.album_name_hidden   = $('#music_write_album_name_hidden').val();
					data.album_mbid_itunes   = $('#music_write_album_mbid_itunes').val();
					data.album_mbid_lastfm   = $('#music_write_album_mbid_lastfm').val();
					data.album_url_itunes    = $('#music_write_album_url_itunes').val();
					data.album_url_lastfm    = $('#music_write_album_url_lastfm').val();
					data.album_image         = $('#music_write_hidden_album_image').val();
					var tracks = [];
					for (var i=0; i<=$('.track_name_hidden').length; i++){
						tracks[i] = {};
						tracks[i].track_id   = $('.track_id')         .eq(i).val();
						tracks[i].track_name = $('.track_name_hidden').eq(i).val();
						tracks[i].track_mbid_itunes = $('.track_mbid_itunes').eq(i).val();
						tracks[i].track_mbid_lastfm = $('.track_mbid_lastfm').eq(i).val();
						tracks[i].track_url_itunes  = $('.track_url_itunes').eq(i).val();
						tracks[i].track_url_lastfm  = $('.track_url_lastfm').eq(i).val();
					}
					data.tracks = tracks;
					if (data.album_name_hidden.length == 0){
						return false;
					}
					break;
				case 'track':
					data.album_id            = $('#music_write_album_id')          .val();
					data.album_name          = $('#music_write_album_name_hidden') .val();
					data.image_url           = $('#music_write_track_album_image') .val();
					data.track_id            = $('#music_write_track_id')          .val();
					data.track_name          = $('#music_write_track_name')        .val();
					data.track_name_hidden   = $('#music_write_track_name_hidden') .val();
					data.track_mbid_itunes   = $('#music_write_track_mbid_itunes').val();
					data.track_mbid_lastfm   = $('#music_write_track_mbid_lastfm').val();
					data.track_url_itunes    = $('#music_write_track_url_itunes').val();
					data.track_url_lastfm    = $('#music_write_track_url_lastfm').val();
					data.content             = $('#music_write_track_content')     .val();
					data.track_album_name    = $('#music_write_track_album_name')  .val();
					data.track_album_mbid_itunes = $('#music_write_track_album_mbid_itunes')  .val();
					data.track_album_mbid_lastfm = $('#music_write_track_album_mbid_lastfm')  .val();
					data.track_album_url_itunes  = $('#music_write_track_album_url_itunes')   .val();
					data.track_album_url_lastfm  = $('#music_write_track_album_url_lastfm')   .val();
					data.track_album_artist  = $('#music_write_track_album_artist').val();
					if (data.track_name_hidden.length == 0){
						return false;
					}
					break;
			}


			if (data.star == 0 && data.review == '') {
				alert('スターレビューとコメントレビューを削除するには削除ボタンをクリックしてください');
				return false;
			}

			if (data.review === review_tmp) {
				if (data.review_id == '') {
					if (data.star == 0) {
						return false;
					}
				} else {
					if (data.star === star_tmp) {
						return false;
					}
				}
			}

			if (data.review.length === 0) {
				alert('コメントレビューが入力されていません');
				return false;
			}

			// レビューを送信
			_sendReview(data).done(function(res) {
				if (typeof(res.success) === 'undefined') {
					alert('申し訳ございません。ネットワークエラーが発生し投稿に失敗しました。');
					return false;
				}
				if (res.success === false && res.code == '7010') { // no_login
					if (confirm('レビューを投稿するには会員登録しログインする必要があります。ログイン画面に移動しますか？')) {
						location.href="/login/";
					}
					return false;
				}
				if (res.success === false) {
					alert('申し訳ございません。ネットワークエラーが発生し投稿に失敗しました。');
					return false;
				}
				$('#music_write_'+ about +'_review_id') .val(res.result.review_id);
				$('#music_write_'+ about +'_star_tmp')  .val(data.star);
				$('#music_write_'+ about +'_comment_review_textarea_tmp').val(data.review);
				$('#music_write_'+ about +'_updated_at').html(res.result.updated_at.replace(/:[0-9]*$/, '') + '&nbsp;&nbsp;<span class="up_mark">UP！</span>');
				$('#music_write_'+ about +'_submit')    .attr('disabled', 'disabled');
				$('#music_write_'+ about +'_submit')    .removeClass('send_btn');
				$('#music_write_'+ about +'_submit')    .addClass('send_disabled_btn');
				$('#music_write_'+ about +'_delete')    .removeAttr('disabled');
				$('#music_write_'+ about +'_delete')    .removeClass('delete_disabled_btn');
				$('#music_write_'+ about +'_delete')    .addClass('delete_btn');
				$('#music_write_'+ about +'_comment_review_textarea').css('color', '#999');

				switch (about) {
					case 'artist':
						var obj_main_text = $('#modaldiv_artist .main_text');
						break;
					case 'album':
						$('#music_write_album_id').val(res.result.album_id);
						$('#music_write_album_name').val(null);
						$('.album_search_result').remove();
						var obj_main_text = $('#modaldiv_album .main_text');
						break;
					case 'track':
						$('#music_write_track_id').val(res.result.track_id);
						$('#music_write_track_name').val(null);
						$('.track_search_result').remove();
						var obj_main_text = $('#modaldiv_track .main_text');
						break;
				}

				var arr_tmp_comment = [];
				obj_main_text.each(function(i, v) {
					arr_tmp_comment[i] = obj_main_text.eq(i).html();
				});

				// コメントボックスに同じ文章が存在しない場合
				if (jQuery.inArray(data.review, arr_tmp_comment) < 0) {
					var _setusercomment = function(data) {
						return $.ajax({
							type: 'post',
							url: $('#music_write_api_url_setusercomment').val(), // api/review/setusercomment.json
							datatype: 'json',
							data: JSON.stringify(data),
							contentType: 'application/json',
							cache: false,
							success: function(obj_response, ans) {
								return obj_response;
							},
							error: function() {
								alert('_setusercomment error');
								return false;
							}
						});
					};

					var _removeusercomment = function(data) {
						return $.ajax({
							type: 'post',
							url: $('#music_write_api_url_removeusercomment').val(), // api/review/setusercomment.json
							datatype: 'json',
							data: JSON.stringify(data),
							contentType: 'application/json',
							cache: false,
							success: function(obj_response, ans) {
								return obj_response;
							},
							error: function() {
								alert('_setusercomment error');
								return false;
							}
						});
					};


					if (confirm('投稿を受け付けました。トップページへの反映は少々お時間をいただきます。\n\n' + 'こちらのコメントをコメントボックスに保存しますか？')) {
						var data_usercomment = {};
						data_usercomment.about = about;
						data_usercomment.user_comment = data.review;

						_setusercomment(data_usercomment).done(function(res) {
							if (typeof(res.success) === 'undefined') {
								alert('申し訳ございません。ネットワークエラーが発生しました。');
								return false;
							}
							if (res.success === false) {
								alert('申し訳ございません。コメントボックス登録に失敗しました');
								return false;
							} else {
								$('.modaldiv ul').prepend('<li><a class="modal_link ui-btn ui-btn-icon-right ui-icon-carat-r" rel="external">'+ data.review +'</a><span class="main_text">'+ data.review +'</span><span class="user_comment_id">'+ res.result.id +'</span><div class="modal_link_delete_div"><input type="button" value="削除" data-role="none"></div></li>');
								$('.modaldiv .modal_link').off('click');
								$('.modaldiv .modal_link_delete_div input[type=button]').off('click');
								$('.modaldiv .modal_link').on('click', function() {
									var text = $(this).parent().children('.main_text').html();

									switch($(this).parent().parent().parent().attr('id')) {
										case 'modaldiv_artist':
											var modal_id = '#modaldiv_artist';
											var textarea = $('#music_write_artist_comment_review_textarea');
											var send_btn = $('#music_write_artist_submit');
											break;
										case 'modaldiv_album':
											var modal_id = '#modaldiv_album';
											var textarea = $('#music_write_album_comment_review_textarea');
											var send_btn = $('#music_write_album_submit');
											break;
										case 'modaldiv_track':
											var modal_id = '#modaldiv_track';
											var textarea = $('#music_write_track_comment_review_textarea');
											var send_btn = $('#music_write_track_submit');
											break;
									}

									$("#lean_overlay").fadeOut(200);
									$(modal_id).css({ 'display' : 'none' });
									setTimeout(function() {
										$(textarea).val(text);
										$(textarea).css('color', '#000');
										$(send_btn).removeClass('send_disabled_btn');
										$(send_btn).addClass('send_btn');
										$(send_btn).prop('disabled', false);
									}, 300);
								});

								$('.modaldiv .modal_link_delete_div input[type=button]').on('click', function() {
									var obj_this = $(this);
									var _removeusercomment = function(data) {
										return $.ajax({
											type: 'post',
											url: $('#music_write_api_url_removeusercomment').val(), // api/review/removeusercomment.json
											datatype: 'json',
											data: JSON.stringify(data),
											contentType: 'application/json',
											cache: false,
											success: function(obj_response, ans) {
												return obj_response;
											},
											error: function() {
												alert('_setusercomment error');
												return false;
											}
										});
									};

									var user_comment_id = $(this).parent().parent().children('.user_comment_id').html();
									var user_comment    = $(this).parent().parent().children('.main_text').html();
									if ( ! confirm('コメントボックスから『' + user_comment + '』を削除しますか')) {
										return true;
									}
									var remove_data = {};
									remove_data.id = user_comment_id;
									_removeusercomment(remove_data).done(function(res) {
										if (res.success == true) {
											obj_this.parent().parent('li').remove();
										}
									});

								});

							}

						});

						return true;
					}

				} else {
					alert('ありがとうございます。投稿を受け付けました。トップページへの反映は少々お時間いただきます。');

					return true;
				}
			});

			return true;
		}); // $('.music_write_submit').click

	};




	/***************************************************
	 * レビュー削除クラス  *
	 ***************************************************/
	base.deleteReviewSetEventListner = function() {
		var _deleteReview = function(data) {
			return $.ajax({
				type: 'post',
				url: $('#music_write_api_url_send_review').val(), // api/review/set.json
				datatype: 'json',
				data: JSON.stringify(data),
				contentType: 'application/json',
				cache: false,
				success: function(obj_response, ans) {
					return obj_response;
				},
				error: function(){
					alert('_deleteReview error');
					return false;
				}
			});
		};

		// レビュー削除
		$('.review_delete_button').click(function() {
			if ( ! confirm('レビューを削除しますがよろしいですか？')) {
				return false;
			}
			var id = $(this).attr('id');
			var about = null;
			switch (id) {
				case 'music_write_artist_delete':
					about = 'artist';
					break;
				case 'music_write_album_delete':
					about = 'album';
					break;
				case 'music_write_track_delete':
					about = 'track';
					break;
				default :
					return false;
			}
			var data = {};
			data.about     = about;
			data.review_id = $('#music_write_'+ about +'_review_id').val();
			data.artist_id = base.artist_id;
			data.is_delete = true;
			_deleteReview(data).done(function(res) {
				if (typeof(res.success) === 'undefined') {
					alert('申し訳ございません。ネットワークエラーが発生し投稿取り消しに失敗しました。');
					return false;
				}
				if (res.success === false) {
					if (res.code != '7010') {
						alert('申し訳ございません。取り消しに失敗しました');
						return false;
					}
				}
				$('#music_write_'+ about +'_star_tmp').val(null);
				$('#music_write_'+ about +'_comment_review_textarea_tmp').val(null);
				$('#music_write_'+ about +'_review_id').val(null);
				$('#music_write_'+ about +'_url').val(null);
				$('#music_write_'+ about +'_updated_at').html(null);
				$('#music_write_'+ about +'_comment_review_textarea').val(null);
				$('#music_write_'+ about +'_star').val(0);
				$('#music_write_'+ about +'_star').parent().children('.rateit').children('.rateit-range').children('.rateit-selected').css('width', '0px');
				$('#music_write_'+ about +'_submit').attr('disabled', 'disabled');
				$('#music_write_'+ about +'_submit').removeClass('send_btn');
				$('#music_write_'+ about +'_submit').addClass('send_disabled_btn');
				$('#music_write_'+ about +'_delete').attr('disabled', 'disabled');
				$('#music_write_'+ about +'_delete').removeClass('delete_btn');
				$('#music_write_'+ about +'_delete').addClass('delete_disabled_btn');
				$('#music_write_'+ about +'_hidden_form').find('input').val(null);

				switch (about) {
					case 'artist':
						break;
					case 'album':
						$('#music_write_album_name_disp').text('');
						$('#music_write_album_selected_image_span').html(null);
						$('#music_write_album_selected_tracks').html(null);
						break;
					case 'track':
						$('#music_write_track_name_disp').html(null);
						$('#music_write_track_selected_image_span').html(null);
						$('#music_write_track_selected_content_area').html(null);
						break;
				}

				alert('レビュー投稿を取り消しました');

			});
		});
	};




	/***************************************************
	 * 検索実行ボタンアクティブ化クラス
	 ***************************************************/
	base.activeSearchButtonEventListner = function() {
		$('.title_area').focus(function() {
			var id = $(this).attr('id');
			var about = null;
			switch (id) {
				case 'music_write_album_name':
					about = 'album';
					break;
				case 'music_write_track_name':
					about = 'track';
					break;
				default:
					return false;
			} // switch

			$('#music_write_'+ about +'_search_button').removeAttr('disabled');

			return true;
		});
	};

	/***************************************************
	 * 検索入力取り消しクラス
	 ***************************************************/
	base.clearSearchAreaEventListner = function() {
		$('.clear_text').click(function() {
			var id = $(this).attr('id');
			var about = null;
			switch (id) {
				case 'album_clear_text':
					$('#music_write_album_name').val(null);
					$('.album_search_result').remove();
					base.album._init();
					break;
				case 'track_clear_text':
					$('#music_write_track_name').val(null);
					$('.track_search_result').remove();
					base.track._init();
					break;
				default:
					return false;
			} // switch
			return true;
		});
	};



	/***************************************************
	 * 検索実行クラス
	 ***************************************************/
	base.execSearchEventListner = function() {
		$('.search_button').on ('click', function() {
			var id = $(this).attr('id');
			var about = null;
			switch (id) {
				case 'music_write_album_search_button':
					about = 'album';
					break;
				case 'music_write_track_search_button':
					about = 'track';
					break;
				default:
					return false;
			} // switch

			var search_text = $('#music_write_' + about + '_name').val();

			if (search_text.length === 0){
				return true;
			}

			// レビューエリア初期化
			var dotted = '';
			var times = 0;
			var interval = setInterval(function() {
				times++;
				dotted += '・';
				$('#music_write_'+ about +'_search_button').val('検索中' + dotted);
				if (times == 5) {
					dotted = '';
					times = 0;
				}
			},500);
			$('body').css('cursor', 'wait');
			$(this).attr('disabled', 'disabled');
			$(this).css('cursor', 'default');
			$('.search_error').html(null);

			// apiから取得
			var params = {};
			params.search_text = search_text;
			switch (about) {
				case 'album':
					base.album._getSearchAlbumListFromAjax(params).done(function(res) {
						clearInterval(interval);
						$('#music_write_album_search_button').val('検索');
						if (res === false) {
							alert('error base.album._getSearchAlbumListFromAjax response');
							$('body').css('cursor', 'default');
							return false;
						}
						// 検索リスト
						res.about = about;
						if (res.result.arr_list.length === 0) {
							$('#music_write_review_album_search_error').html('見つかりませんでした');
						} else {
							base.pulldownSearchResult(res);
						}
						$('body').css('cursor', 'default');
						return true;
					});
					break;
				case 'track':
					base.track._getSearchTrackListFromAjax(params).done(function(res) {
						clearInterval(interval);
						$('#music_write_track_search_button').val('検索');
						if (res.length === 0 || res === false){
							alert('error base.track._getSearchTrackListFromAjax response');
							$('body').css('cursor', 'default');
							return false;
						}
						// 検索リスト
						res.about = about;
						if (res.result.length === 0) {
							$('#music_write_review_track_search_error').html('見つかりませんでした');
						} else {
							base.pulldownSearchResult(res);
						}
						$('body').css('cursor', 'default');
						return true;
					});
					break;
			}
			return true;
		});
	};





	/***********************************************
	 * プルダウンイベントクラス  *
	 ***********************************************/
	base.pulldownSearchResult = function(res) {
		if (res.success !== true) {
			alert('error');
			return false;
		}

		var html = '';
		var about = res.about;
		$('#music_write_review_'+ about +'_search_result').html(null);
		$('#music_write_'+ about +'_comment_review_textarea').val(null);
		$('#music_write_'+ about +'_comment_review_textarea_tmp').val(null);
		$('#music_write_'+ about +'_review_id').val(null);
		$('#music_write_'+ about +'_updated_at').html(null);
		$('#music_write_'+ about +'_star').val(0);
		$('#music_write_'+ about +'_star_tmp').val(null);
		$('#music_write_'+ about +'_star').parent().children('.rateit').children('.rateit-range').children('.rateit-selected').css('width', '0px');
		$('#music_write_'+ about +'_name_hidden').val($('#music_write_'+ about +'_name').val());
		$('#music_write_'+ about +'_not_exist_flag').val(true);
		//$('#music_write_'+ about +'_selected_image_span').html(null);
		$('#music_write_'+ about +'_delete').removeClass('delete_btn');
		$('#music_write_'+ about +'_delete').addClass('delete_disabled_btn');
		//$('#music_write_'+ about +'_selected_tracks').html(null);

		switch (about) {
			case 'album':
				if (res.result.arr_list.length === 0) {
					html = '検索結果はありませんが検索したアルバム名でレビューすることができます。';
					$('#music_write_album_name_disp').text('');
					$('#music_write_album_artist_disp').html(null);
					$('#music_write_album_selected_tracks').html(null);
				} else {
					res.result.arr_list.forEach(function(e, ans) {
						html += '<div class="album_search_result">';
						html += '<span><img src="' + base.htmlentities(e['image_small']) + '"></span>';
						html += '<span>' + base.htmlentities(e['name']) + '</span>';
						html += '<input type="hidden" value="' + base.htmlentities(e['name'])          + '" class="album_search_result_name">';
						html += '<input type="hidden" value="' + base.htmlentities(e['id'])            + '" class="album_search_result_id">';
						html += '<input type="hidden" value="' + base.htmlentities(e['mbid_itunes']) + '" class="album_search_result_mbid_itunes">';
						html += '<input type="hidden" value="' + base.htmlentities(e['mbid_lastfm']) + '" class="album_search_result_mbid_lastfm">';
						html += '<input type="hidden" value="' + base.htmlentities(e['url_itunes']) + '" class="album_search_result_url_itunes">';
						html += '<input type="hidden" value="' + base.htmlentities(e['url_lastfm']) + '" class="album_search_result_url_lastfm">';
						html += '<input type="hidden" value="' + base.htmlentities(e['image_extralarge']) + '" class="album_search_result_image">';
						html += '</div>';
					});
					$('#music_write_review_'+ about +'_search_result').hide();
					$('#music_write_review_'+ about +'_search_result').append(html);

					// プルダウンリストクリック
					$('.album_search_result').on('click', function() {
						var params = {};
						params.album_id    = $(this).children('.album_search_result_id').val();
		 				params.album_name  = $(this).children('.album_search_result_name').val();
						params.album_mbid_itunes = $(this).children('.album_search_result_mbid_itunes').val();
						params.album_mbid_lastfm = $(this).children('.album_search_result_mbid_lastfm').val();
						params.album_url_itunes = $(this).children('.album_search_result_url_itunes').val();
						params.album_url_lastfm = $(this).children('.album_search_result_url_lastfm').val();
						params.album_image = base.imageUrlConvert($(this).children('.album_search_result_image').val(), 252);
						params.artist_name = base.artist_name;
						// アルバム収録曲を取得
						base.album._setAlbumTracks(params);
					});
				}
				break;

			case 'track':
				if (res.result.length === 0) {
					html = '検索結果はありませんが検索したトラック名でレビューすることができます。';
					$('#music_write_track_id').val(null);
					$('#music_write_track_mbid_itunes').val(null);
					$('#music_write_track_mbid_lastfm').val(null);
					$('#music_write_track_content').val(null);
					$('#music_write_track_url_itunes').val(null);
					$('#music_write_track_url_lastfm').val(null);
					$('#music_write_track_album_name').val(null);
					$('#music_write_track_album_mbid_itunes').val(null);
					$('#music_write_track_album_mbid_lastfm').val(null);
					$('#music_write_track_album_url_itunes').val(null);
					$('#music_write_track_album_url_lastfm').val(null);
					$('#music_write_track_album_artist').val(null);
					$('#music_write_track_album_image').val(null);
					$('#music_write_track_selected_tracks').find('span').html(null);
					$('#music_write_album_delete').attr('disabled', 'disabled');
				} else {
					res.result.forEach(function(e, ans) {
						html += '<div class="track_search_result">';
						html += '<span><img src="' + base.htmlentities(e['image_small']) + '"></span>';
						html += '<span>' + base.htmlentities(e['name']) + '</span>';
						html += '<input type="hidden" value="' + base.htmlentities(e['name'])        + '" class="track_search_result_name">';
						html += '<input type="hidden" value="' + base.htmlentities(e['id'])          + '" class="track_search_result_id">';
						html += '<input type="hidden" value="' + base.htmlentities(e['mbid_itunes']) + '" class="track_search_result_mbid_itunes">';
						html += '<input type="hidden" value="' + base.htmlentities(e['mbid_lastfm']) + '" class="track_search_result_mbid_lastfm">';
						html += '<input type="hidden" value="' + base.htmlentities(e['image_extralarge'])   + '" class="track_search_result_image">';
						html += '</div>';
					});
					$('#music_write_review_'+ about +'_search_result').hide();
					$('#music_write_review_'+ about +'_search_result').append(html);

					// プルダウンリストクリック
					$('.track_search_result').click(function() {
						var params = {};
						params.track_id    = $(this).children('.track_search_result_id').val();
		 				params.track_name  = $(this).children('.track_search_result_name').val();
						params.track_mbid_itunes = $(this).children('.track_search_result_mbid_itunes').val();
						params.track_mbid_lastfm = $(this).children('.track_search_result_mbid_lastfm').val();
						params.track_image = base.imageUrlConvert($(this).children('.track_search_result_image').val(), 252);
						params.artist_id   = base.artist_id;
						params.artist_name = base.artist_name;
						// トラック詳細情報を取得
						base.track._setTrackDetail(params);
					});
				}
				break;

		} // switch

		$('#music_write_review_'+ about +'_search_result').show('slow');

		return true;
	};



	/***********************************************
	 * 左側アルバムレビューフォームイベントクラス  *
	 ***********************************************/
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
			$('#music_write_commentbox_album').addClass('ui-state-disabled');
		}
		return true;
	};

	// アルバム収録トラック ＆レビュー取得
	var album_all_preview_timer = null;
	base.album._setAlbumTracks = function(params) {
		var data = {};
		clearInterval(album_all_preview_timer);
		setTimeout(function() {
			$('#current_track').html(null);
			$('#current_time').html(null);
		}, 500);
		$('#review_music_write_listen_all').css('background', '#fff').css('color', '#000').css('text-decoration', 'none');

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

		// フォーム初期化
		base.album._init();
		// $('#music_write_album_name').val(null); // 検索ボックスクリア => しない
		$('#music_write_album_name_disp').html(null);
		$('#music_write_album_release').html(null);
		$('#music_write_album_copyright').html(null);
		$('#music_write_album_artist_disp').html(null);
		$('#music_write_album_selected_image_span').html(null);
		$('#music_write_album_selected_tracks').html(null);
		$('#music_write_review_album_search_result').html(null);
		$('#music_write_album_comment_review_textarea').val(null);
		$('#music_write_album_star').val(0);
		$('#music_write_album_star_tmp').val(null);
		$('#music_write_album_comment_review_textarea_tmp').val(null);
		$('#music_write_album_review_id').val(null);
		$('#music_write_album_updated_at').html(null);
		$('#music_write_album_not_exist_flag').val(false);
		$('#music_write_album_selected_tracks_area .loading').fadeIn('fast');
		$('#review_music_write_listen_all').css('visibility', 'hidden');

		setTimeout(function(){
			$('#music_write_hover_album_main_div').css('display', 'none');
		}, 500);

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
		$('#music_write_album_name_disp').text(data.album_name);
		$('#music_write_album_artist_disp').text($('#music_write_artist_name').val());

		// アルバムアートを表示
		$('#music_write_album_selected_image').fadeIn('slow');

		// itunesアフィリエイトリンク
		if (data.album_mbid_itunes.length > 0) {
			var segment = data.album_url_itunes.replace(/^.+album\//i, '').match(/[^\/]+/i);
			var mbid    = data.album_mbid_itunes;
			var href = "https://geo.itunes.apple.com/jp/album/"+ segment +"/id"+ mbid +"?at=1000l6TJ&app=itunes";
			$('#music_write_album_itunes_link').attr('href', href);
			$('#music_write_album_itunes_link').css('display', 'inline-block');
		} else {
			$('#music_write_album_itunes_link').css('display', 'none');
		}

		// 自身のレビューを取得
		$('#music_write_album_delete').attr('disabled', 'disabled');
		$('#music_write_album_delete').removeClass('delete_btn');
		$('#music_write_album_delete').addClass('delete_disabled_btn');
		$('#music_write_album_star')  .parent().children('.rateit').children('.rateit-range').children('.rateit-selected').css('width', '0px');
		base._getMyReview(data).done(function(res) {
			if (typeof(res.success) === 'undefined') {
				alert('ネットワークエラーのためレビューを取得できませんでした');
			}
			if (res.success === true) {
				if (typeof(res.result.id) !== 'undefined') {
					$('#music_write_album_review_id')              .val(res.result.id);
					$('#music_write_album_star')                   .val(res.result.star);
					$('#music_write_album_star_tmp')               .val(res.result.star);
					$('#music_write_album_comment_review_textarea_tmp').val(res.result.review);
					$('#music_write_album_comment_review_textarea').val(res.result.review);
					$('#music_write_album_comment_review_textarea').css('color', '#999');
					$('#music_write_album_updated_at')             .html(res.result.updated_at.replace(/:[0-9]*$/, '') + '　Last Updated');
					$('#music_write_album_delete')                 .removeAttr('disabled');
					$('#music_write_album_delete')                 .removeClass('delete_disabled_btn');
					$('#music_write_album_delete')                 .addClass('delete_btn');
					var star_range = (res.result.star * 16) + 'px';
					$('#music_write_album_star').parent().children('.rateit').children('.rateit-range').children('.rateit-selected').css('width', star_range);
					// テキストエリアの高さ調整
					var selector = $('#music_write_album_comment_review_textarea');
					var rows = base._getTextareaRows(selector);
					selector.css('height', (rows * 25) + 75 + 'px');
				} else {
					// テキストエリアの高さ調整
					$('#music_write_album_comment_review_textarea').css('height', '75px');
				}
			}

			return true;
		});

		// アルバムトラックを取得
		base.album._getAlbumTracksFromAjax(data).done(function(res) {
			if (typeof(res.success) === 'undefined'){
				alert('トラック取得に失敗しました(undefined)');
				return false;
			}
			if (res.success === false){
				alert('トラック取得に失敗しました(false)');
				return false;
			}

			$('#music_write_commentbox_album').removeClass('ui-state-disabled');

			// ローディング画面を消す
			$('.loading').css('display', 'none');
			$('#music_write_more_album_area').children('.loading').removeClass('loading');

			if (res.length == 0 || res.result.arr_list.length == 0){
				$('#music_write_album_selected_tracks').html('<span id="music_write_track_list_none">アルバム収録曲の情報は取得できませんでした</span>');
				return false;
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
				html += '<span class="track_name">'    + base.htmlentities(e['name'])        + '</span>';
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
				$('#review_music_write_listen_all').css('visibility', 'visible');
				$('#review_music_write_listen_all').on('click', function() {
					$(this).css('background', 'red').css('color', '#fff');
					reset_preview(number);

					if (is_all_play === true) {
						is_all_play = false;
						$('.loading').hide();
						$('#review_music_write_listen_all').css('background', '#fff').css('color', '#000');
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
								$('#review_music_write_listen_all').css('background', '#fff').css('color', '#000');
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
						$('#review_music_write_listen_all').css('background', '#fff').css('color', '#000').css('text-decoration', 'none');

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
				}
			}

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
		});
	};

	base.album._getSearchAlbumListFromAjax = function(params) {
		var data = {};
		data.artist_id   = base.artist_id;
		data.artist_name = base.artist_name;
		data.album_name  = params.search_text;
		return $.ajax({
			type: 'post',
			url: $('#music_write_api_url_search_album_word').val(), // api/album/search.json
			datatype: 'json',
			data: JSON.stringify(data),
			contentType: 'application/json',
			cache: false,
			success: function(res, ans) {
				return res;
			},
			error: function() {
				return false;
			}
		});
	};

	base.album._getAlbumTracksFromAjax = function(params) {
		var data = {};
		data = params;
		return $.ajax({
			type: 'post',
			url: $('#music_write_api_url_search_albumtrack').val(), // api/track/albumtracklist.json
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





	/***********************************************
	 * 左側トラックレビューフォームイベントクラス  *
	 ***********************************************/
	base.track._init = function() {
		$('#music_write_track_search_button').removeAttr('disabled');
		if ($('#music_write_track_disp').html().replace(/[\s]*/, '').length === 0) {
			$('#music_write_track_comment_review_textarea').val(null);
			$('#music_write_track_star').val(0);
			$('#music_write_track_hidden_form').find('input').val(null);
			$('#music_write_review_track_search_error').html(null);
			$('#music_write_track_selected_tracks').hide('fast');
			$('#music_write_commentbox_track').addClass('ui-state-disabled');
		}
		return true;
	};

	// トラック明細 ＆ レビュー取得
	base.track._setTrackDetail = function(params) {
		var data = {};
		data.about       = 'track';
		data.artist_id   = base.artist_id;
		data.artist_name = base.artist_name;
		data.track_id    = params.track_id;
		data.track_name  = params.track_name;
		data.track_image = params.track_image;
		data.track_mbid_itunes = params.track_mbid_itunes;
		data.track_mbid_lastfm = params.track_mbid_lastfm;

		// フォーム初期化
		base.track._init();
		//$('#music_write_track_name').val(null); // 検索ボックスクリア => しない
		$('#music_write_track_star').val(null);
		$('#music_write_track_star_tmp').val(null);
		$('#music_write_track_comment_review_textarea').val(null);
		$('#music_write_track_comment_review_textarea_tmp').val(null);
		$('#music_write_track_review_id').val(null);
		$('#music_write_track_updated_at').html(null);
		$('#music_write_track_selected_image_span').html(null);
		$('#music_write_track_selected_tracks').find('span').html(null);
		$('#music_write_review_track_search_result').html(null);
		$('#music_write_track_not_exist_flag').val(false);
		$('#music_write_track_selected_content_area .loading').fadeIn('fast');
		base.scrollToSection('music_write_track_review_section'); // 移動

		// 自身のレビューを取得
		$('#music_write_track_delete').attr('disabled', 'disabled');
		$('#music_write_track_delete').removeClass('delete_btn');
		$('#music_write_track_delete').addClass('delete_disabled_btn');
		$('#music_write_track_star')  .parent().children('.rateit').children('.rateit-range').children('.rateit-selected').css('width', '0px');
		base._getMyReview(data).done(function(res_review){
			if (typeof(res_review.success) === 'undefined'){
				alert('レビューを取得できませんでした');
			}
			if (res_review.success === true){
				if (typeof(res_review.result.id) !== 'undefined'){
					$('#music_write_track_review_id')              .val(res_review.result.id);
					$('#music_write_track_star')                   .val(res_review.result.star);
					$('#music_write_track_star_tmp')               .val(res_review.result.star);
					$('#music_write_track_comment_review_textarea_tmp')     .val(res_review.result.review);
					$('#music_write_track_comment_review_textarea').val(res_review.result.review);
					$('#music_write_track_comment_review_textarea').css('color', '#999');
					$('#music_write_track_updated_at')             .html(res_review.result.updated_at.replace(/:[0-9]*$/, '') + '　Last Updated');
					$('#music_write_track_delete')                 .removeAttr('disabled');
					$('#music_write_track_delete')                 .removeClass('delete_disabled_btn');
					$('#music_write_track_delete')                 .addClass('delete_btn');
					var star_range = (res_review.result.star * 16) + 'px';
					$('#music_write_track_star').parent().children('.rateit').children('.rateit-range').children('.rateit-selected').css('width', star_range);
					// テキストエリアの高さ調整
					var selector = $('#music_write_track_comment_review_textarea');
					var rows = base._getTextareaRows(selector);
					selector.css('height', (rows * 25) + 75 + 'px');
				} else {
					// テキストエリアの高さ調整
					$('#music_write_track_comment_review_textarea').css('height', '75px');
				}
			}

			return true;
		});

		// トラック明細情報を取得
		base.track._getTrackDetailFromAjax(params).done(function(res) {
			if (res === false){
				alert('base.track._setTrackDetail error');
				return false;
			}

			if (typeof(res.success) === 'undefined'){
				alert('トラック明細取得に失敗しました(underined)');
				return false;
			}
			if (res.success === false){
				alert('トラック明細取得に失敗しました(underined)');
				return false;
			}

			$('#music_write_commentbox_track').removeClass('ui-state-disabled');

			res.result.forEach(function(e, ans){ // 実際は１件
				$('#music_write_track_id')          .val(e['id']);
				//$('#music_write_track_name')        .val(e['name']);
				$('#music_write_track_mbid_itunes') .val(e['mbid_itunes']);
				$('#music_write_track_mbid_lastfm') .val(e['mbid_lastfm']);
				$('#music_write_track_name_hidden') .val(e['name']);
				$('#music_write_track_url_itunes')  .val(e['url_itunes']);
				$('#music_write_track_url_lastfm')  .val(e['url_lastfm']);
				$('#music_write_track_album_image') .val(e['image_extralarge']);
				$('#music_write_track_album_name')  .val(e['track_album_name']);
				$('#music_write_track_album_mbid_itunes').val(e['track_album_mbid_itunes']);
				$('#music_write_track_album_mbid_lastfm').val(e['track_album_mbid_lastfm']);
				$('#music_write_track_album_url_itunes').val(e['url_itunes']);
				$('#music_write_track_album_url_lastfm').val(e['url_lastfm']);
				$('#music_write_track_album_artist').val(e['track_album_artist']);
				$('#music_write_track_content')     .val(e['content']);

				var image_html = '<img src="'+ e['image_extralarge'] +'">';
				$('#music_write_track_selected_image_span').html(image_html);

				// itunesアフィリエイト
				if (e['mbid_itunes'].length > 0) {
					var segment = e['url_itunes'].replace(/^.+album\//i, '').match(/[^\/]+/i);
					var mbid    = e['mbid_itunes'];
					var href = "https://geo.itunes.apple.com/jp/album/"+ segment +"/id"+ mbid +"?at=1000l6TJ&app=itunes";
					$('#music_write_track_itunes_link').attr('href', href);
					$('#music_write_track_itunes_link').css('display', 'inline-block');
				} else {
					$('#music_write_track_itunes_link').css('display', 'none');
				}


				setTimeout(function(){
					// ローディング画面を消す
					$('#music_write_track_selected_content_area .loading').fadeOut();
					$('#music_write_track_selected_content_area .loading').css('display', 'none');
					var content_html = '<table id="music_write_track_selected_table">';
					content_html += '<tr><td class="track_label"><span class="music_write_track_info">トラック名：</span></td><td class="track_ans"><span id="music_write_track_disp">'  + e['name'] + '</span></td></tr>';
					content_html += '<tr><td class="track_label"><span class="music_write_track_info">アーティスト：</span></td><td class="track_ans"><span>'+ e['artist_name'] + '</span></td></tr>';
					content_html += '<tr><td class="track_label"><span class="music_write_track_info">収録アルバム：</span></td><td class="track_ans"><span>'+ e['album_name'] +'</span></td></tr>';
					if (e['preview_itunes'].length > 0) {
						content_html += '<tr><td colspan="2" class="track_audio_td">';
						content_html += '<audio class="preview_itunes preview_itunes_track" controls>';
						content_html += '<source src="'+ e['preview_itunes'] +'">';
						content_html += '<a href="'+ e['preview_itunes'] +'" target="new_win">▶</a>';
						content_html += '</audio>';
						content_html += '<td></tr>';
					}
					content_html += '</table>';

					$('#music_write_track_selected_tracks').html(content_html);

					// トラックアートを表示
					$('#music_write_track_selected_image_span').fadeIn('slow');

					// 詳細情報を表示
					$('#music_write_track_selected_tracks').fadeIn('fast');

					return true;
				}, 1000);
			});
		});
	};


	base.track._getSearchTrackListFromAjax = function(params) {
		var data = {};
		data.artist_id   = base.artist_id;
		data.artist_name = base.artist_name;
		data.track_name  = params.search_text;
		return $.ajax({
			type: 'post',
			url: $('#music_write_api_url_search_track_searchlist').val(), // api/track/list.json
			datatype: 'json',
			data: JSON.stringify(data),
			contentType: 'application/json',
			cache: false,
			success: function(res, ans){
				return res;
			},
			error: function(){
				return false;
			}
		});
	};

	base.track._getTrackDetailFromAjax = function(params) {
		var data = {};
		data.artist_id   = base.artist_id;
		data.artist_name = base.artist_name;
		data.album_id    = params.album_id;
		data.track_id    = params.track_id;
		data.track_name  = params.track_name;
		data.track_mbid  = params.track_mbid;
		return $.ajax({
			type: 'post',
			url: $('#music_write_api_url_search_track').val(), // api/track/search.json
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


	/****** 実行 *******/
	base.construct();
	base.develop.toHidden();
	base.artmix.getAlbum();
	base.artist.setEventListner();
	base.album.setEventListner();
	base.scrollEventListner();
	base.starReviewEventListner();
	base.sendReviewEventListner();
	base.deleteReviewSetEventListner();
	base.activeSearchButtonEventListner();
	base.clearSearchAreaEventListner();
	base.execSearchEventListner();
});