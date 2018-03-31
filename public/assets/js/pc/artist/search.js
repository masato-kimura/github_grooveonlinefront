jQuery(function() {
	/**
	 * 検索エリアクラス
	 */
	var SearchSection = function() {
		this.init = function() {
			$('#artist_search_input').focus();
			$('#missing_div').hide();
			$('#search_more').hide();
		};

		this.eventListner = function() {
			// テキストボックスをクリア
			$('#artist_search_clear_text').on('click', function() {
				$('#artist_search_input').val(null);
				objSearchSection = new SearchSection();
				objSearchSection.init();
				objListSection = new ListSection();
				objListSection.init();
				objConfirmedSection = new ConfirmedSection();
				objConfirmedSection.init();
				// エラー文言を非表示
				$('#search_error_div').hide();
			});
			// apiへ検索要求
			$('#artist_search_search_button').on('click', function() {
				var objSearchSection = new SearchSection();
				objSearchSection.init();
				$('#search_form').submit();
				$('#search_error_div').hide();
			});
			// 検索実行時
			$('#search_form').submit(function() {
				$('#search_more').hide();
				var artist_name = $('#artist_search_input').val();
				if (artist_name.length > 0) {
					objListSection = new ListSection();
					objListSection.init();
					$('#loading_top').fadeIn('fast');
					var objSearchSection = new SearchSection();
					objSearchSection.getArtistByAjax(artist_name); // ajax
					$('#artist_search_input').blur();
				}
				return false;
			});
			// 追加検索
			$('#search_more').click(function() {
				$('#search_more').hide();
				$('#loading_bottom').fadeIn('slow');
				var artist_name = $('#artist_search_input').val();
				if (artist_name.length > 0) {
					var objSearchSection = new SearchSection();
					objSearchSection.getArtistByAjax(artist_name, 2); // ajax
				}
				return false;
			});
			// 検索フォーム入力項目でレビュー
			$('#missing_div').on('click', function() {
				var objConfirmedSection = new ConfirmedSection();
				objConfirmedSection.doSubmit($('#artist_search_input').val());
			});
		};

		// アーティスト検索
		this.getArtistByAjax = function(req, page) {
			if (page === undefined) {
				page = 0;
			}
			if (req.length == 0) {
				return false;
			}
			var params = {artist_name : req, page : page};
			$.ajax({
				type: 'post',
				url: $('#artist_search_url').val(), // api/artist/search.json
				datatype: 'json',
				data: JSON.stringify(params),
				contentType: 'application/json',
				cache: false,
				success: function(res, ans) {
					if (res['success'] === false) {
						$('#api_error_response').html('lastfm_api回線が混雑しているようです。' + res['response']);
						return false;
					}
					var objListSection = new ListSection();
					objListSection.appendResult(res.result);
					return true;
				},
				error: function() {
					alert('network error');
					return false;
				}
			});
		}
	};



	/**
	 * 検索結果エリアクラス
	 */
	var ListSection = function() {
		this.init = function(){
			$('#loading_top').hide();
			$('#loading_bottom').hide();
			$('.main_image').hide();
			$('.main_name').hide();
			$('#artist_selected_div').html(null);
			$('#api_error_response').html(null);
		};
		// 検索結果をアペンドする
		this.appendResult = function(res) {
			jQuery.each(res.arr_list, function(i, e) {
				var name        = '<span class="append_name">' + htmlentities(e['name']) + '</span>';
				var id_input    = '<input type="hidden" class="append_input_id" value="' + htmlentities(e['id']) + '">';
				var image       = '<span class="append_image"><img src="' + htmlentities(e['image_medium']) + '"></span>';
				var image_big   = '<input type="hidden" value="' + htmlentities(e['image_extralarge']) + '" class="image_big">';
				var append_div  = '<div class="appended">'+ image + image_big + name + id_input + '</div>';
				$('#artist_selected_div').append(append_div);
			});

			$('#content').css('padding-bottom', '150px');

			if (res.arr_list.length <= 0) {
				$('#api_error_response').html('検索結果はありませんでした');
				$('#search_more').hide();
				$('#missing_search_word').text($('#artist_search_input').val());
				$('#loading_top, #loading_bottom').hide();
				//$('#missing_div').text('下の一覧に見つからないため入力されたアーティスト『' + $('#artist_search_input').val() + '』でレビュー');
				$('#missing_div').fadeIn('slow');
			} else {
				$('#search_more').fadeIn('slow');
				$('#missing_search_word').text($('#artist_search_input').val());
				$('#loading_top, #loading_bottom').hide();
				//$('#missing_div').text('下の一覧に見つからないため入力されたアーティスト『' + $('#artist_search_input').val() + '』でレビュー');
				$('#missing_div').fadeIn('slow');
			}

			// 一覧画像マウスオーバー
			$('.append_image').on('mouseover', function() {
				$('.main_image img').attr('src', $(this).parent().children('.image_big').val());
				$('.main_name').html($(this).parent().children('.append_input_name').val());
				var top_position  = $(this).position().top;
				var left_position = $(this).position().left + 102;
				$('.main_image').css('top', top_position);
				$('.main_image').css('left', left_position);
				$('.main_name').css('top', top_position + 10);
				$('.main_name').css('left', left_position + 5);
				$('.main_image, .main_name').fadeIn();
				return true;
			});
			$('.append_image').on('mouseout', function() {
				$('.main_image, .main_name').css('display', 'none');
				$('.main_image img').attr('src', null);
				$('.main_name').html(null);
				return true;
			});
			// 取得一覧クリック
			$('.to_review, .append_image').click(function() {
				var artist_name = $(this).parent().children('.append_input_name').val();
				var artist_id = $(this).parent().children('.append_input_id').val();
				$('#artist_id').val(artist_id);
				$('#registed_form').submit();
				return true;
			});
		}
	}

	function htmlentities(str) {
		return str.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
	}

	/**
	 * アーティスト確定エリアクラス
	 */
	var ConfirmedSection = function() {
		this.init = function() {
			$('#registed_form').find('input[type=hidden]').val(null);
		};
		this.doSubmit = function(req) {
			$('#artist_name').val(req);
			$('#registed_form').submit();
		};
	};



	// 実行
	var objSearchSection = new SearchSection();
	objSearchSection.init();
	objSearchSection.eventListner();

	var objListSection = new ListSection();
	objListSection.init();

	var objConfirmedSection = new ConfirmedSection();
	objConfirmedSection.init();


});
