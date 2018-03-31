$(document).bind("pageinit", function(){
	  $.mobile.ajaxEnabled = false;
});

jQuery(function() {
	/**
	 * 検索エリアクラス
	 */
	var SearchSection = function() {
		this.last_search_page = 1;

		this.init = function() {
			$('#missing_div').hide();
			$('#search_more').hide();

			// 最近検索された一覧クリック
			$('#artist_search_get_new_ul li a').click(function() {
				var artist_id = $(this).parent().children('.append_input_id').val();
				$('#artist_id').val(artist_id);
				$('#registed_form').submit();
				return true;
			});
		};

		this.eventListner = function() {
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
					$('#artist_selected_ul').html(null);
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

		// Ajaxアーティスト検索
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
						$('#api_error_response').html('回線が混雑しているようです。' + res['response']);
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
		};

	};

	/**
	 * 最近検索されたアーティスト情報クラス
	 */
	var LastSearchSection = function(){
		var page = 1;

		this.init = function(){

		}

		this.eventListner = function(){
			// 最近検索されたアーティスト一覧表示追加ページ
			$('#artist_search_get_more_new').on('click', function(){
				var objLastSearchSection = new LastSearchSection();
				page = page + 1;
				objLastSearchSection.getLastSearchArtistByAjax(page);
				return false;
			});
		}

		// Ajax過去に検索されたアーティスト一覧取得
		this.getLastSearchArtistByAjax = function(page){
			if (page === undefined) {
				page = 2;
			}
			var params = {page: page};
			$.ajax({
				type: 'post',
				url: '/api/artist/getlastsearchlist.json',
				datatype: 'json',
				data: JSON.stringify(params),
				contentType: 'application/json',
				cache: false,
				success: function(res, ans){
					if (res['success'] === false) {
						$('#api_error_response').html('回線が混雑しているようです。' + res['response']);
						return false;
					}
					objLastSearchSection.setList(res.result);
					return true;
				},
				error: function(){
					alert('network error');
					return false;
				}
			});
		};

		// リスト化
		this.setList = function(res){
			var cnt = 0;
			if (res.arr_list.length < $('#last_search_limit').val()){
				$('#artist_search_get_more_new').hide();
			}
			jQuery.each(res.arr_list, function(i, e){
				cnt = cnt + 1;
				var image     = '<img src="' + htmlentities(e['artist_image']) + '" data-original="' + htmlentities(e['artist_image']) + '">';
				var name      = '<h3>' + htmlentities(e['artist_name']) + '</h3>';
				var id_input  = '<input type="hidden" class="append_input_id" value="' + htmlentities(e['artist_id']) + '">';
				var append_li = '<li class="ui-li-has-thumb ui-first-child"><a class="ui-btn ui-btn-icon-right ui-icon-carat-r">'+ image + name + '</a>' + id_input + '</li>';
				$('#artist_search_get_new_ul').append(append_li);
				if (cnt >= $('#last_search_limit').val()){
					return false;
				}
			});

			$(".lazy img").lazyload({
				effect: "fadeIn" ,
				effect_speed: 300 ,
			});
			$('img').error(function() {
				$(this).attr('src', '/assets/img/default.jpg');
			});

			// 最近検索された一覧クリック
			$('#artist_search_get_new_ul li a').off('click');
			$('#artist_search_get_new_ul li a').click(function() {
				var artist_id = $(this).parent().children('.append_input_id').val();
				$('#artist_id').val(artist_id);
				$('#registed_form').submit();
				return true;
			});
		};
	}

	/**
	 * 検索結果エリアクラス
	 */
	var ListSection = function(){
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
				var image       = '<img src="' + htmlentities(e['image_medium']) + '" data-original="'+ htmlentities(e['image_medium']) +'">';
				var name        = '<h3>' + htmlentities(e['name']) + '</h3>';
				var id_input    = '<input type="hidden" class="append_input_id" value="' + htmlentities(e['id']) + '">';
				var append_li  = '<li class="ui-li-has-thumb ui-first-child ui-last-child"><a class="ui-btn ui-btn-icon-right ui-icon-carat-r">'+ image + name + '</a>' + id_input + '</li>';
				$('#artist_selected_ul').append(append_li);
			});
			if (res.arr_list.length <= 0) {
				$('#api_error_response').html('検索結果は0件です。');
				$('#search_more').hide();
				$('#missing_search_word').text($('#artist_search_input').val());
				$('#loading_top, #loading_bottom').hide();
				$('#missing_div').fadeIn('slow');
			} else {
				$('#search_more').fadeIn('slow');
				$('#missing_search_word').text($('#artist_search_input').val());
				$('#loading_top, #loading_bottom').hide();
				$('#missing_div').fadeIn('slow');
			}

			// 取得一覧クリック
			$('#artist_selected_ul li a').click(function() {
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

	var objLastSearchSection = new LastSearchSection();
	objLastSearchSection.init();
	objLastSearchSection.eventListner();

	var objListSection = new ListSection();
	objListSection.init();

	var objConfirmedSection = new ConfirmedSection();
	objConfirmedSection.init();


});
