$(document).bind("pageinit", function(){
	  $.mobile.ajaxEnabled = false;
});

function htmlentities(str) {
	return str.replace(/&/g, "&amp;")
	.replace(/"/g, "&quot;")
	.replace(/</g, "&lt;")
	.replace(/>/g, "&gt;");
};

function scrollToSection(id, callback) {
	var speed = 'fast';
	var target = $('#' + id);
	var position = target.offset().top - 60;
	$('body,html').animate({scrollTop:position}, speed, 'swing', callback);
};

jQuery(function($) {
	$('a[rel*=leanModal]').leanModal({
		top: 20,
		overlay : 0.5,
		closeButton: ".modal_close",
	});

	var obj_artist_search = {};
	obj_artist_search.LIMIT = 10
	obj_artist_search.current_page  = 0;
	obj_artist_search.search_word   = '';
	obj_artist_search.ajax_response = [];
	obj_artist_search.error         = '';

	obj_artist_search.init =  function() {
		$('#tracklist_make_artist_search_more').css('display', 'none');
		$('#tracklist_make_artist_not_exist_disp').css('display', 'none');
		$('#tracklist_make_artist_not_exist_disp').html(null);
		$('#artist_selected_ul').html(null);
		$('#artist_search_button').parent('div').removeClass('ui-state-disabled');
		$('#artist_search_input').parent('div').removeClass('ui-state-disabled');
		$('#tracklist_make_artist_search_section .description').css('color', '#000');
		obj_artist_search.current_page  = 0;
	};

	obj_artist_search.submit = function() {
		if (obj_artist_search.search_word.length === 0) {
			return $.ajax({});
		}
		var params = {
			artist_name: obj_artist_search.search_word,
			page       : obj_artist_search.current_page,
			available_play : true,
		};
		return $.ajax({
			type        : 'post',
			url         : '/api/artist/search.json',
			datatype    : 'json',
			data        : JSON.stringify(params),
			contentType : 'application/json',
			cache       : false,
			success     : function(res, ans) {
				if (res.success === false) {
					alert('入力項目をご確認ください');
					return false;
				}
				obj_artist_search.ajax_response = res.result;
				return true;
			},
			error       : function() {
				alert('Network Error');
				return false;
			}
		});
	};

	obj_artist_search.listCommit = function() {
		$('#artist_selected_ul li, #artist_search_get_new_ul li').off('click');
		$('#artist_selected_ul li, #artist_search_get_new_ul li').on('click', function() {
			obj_artist_search.current_page = 0;
			obj_artist.name    = $(this).find('h3').html();
			obj_artist.id      = $(this).find('.append_input_id').val();
			obj_artist.img_src = $(this).find('img').attr('src');
			
			obj_album.init();
			obj_album.getAlbumList();
			$('#tracklist_make_to_artist_page a').attr('href', '/artist/detail/' + obj_artist.id + '/');
			$('.main_navi_title').eq(0).children('a').html('アーティスト ['+ obj_artist.name +']');
			$('.main_navi_title').eq(0).children('a').attr('href', '/artist/detail/' + obj_artist.id + '/');
			$('#artist_selected_ul').fadeOut('fast', function() {
				$('#artist_selected_ul').html(null);
				$('#artist_selected_ul').css('display', 'block');
				$('#tracklist_make_artist_search_more').css('display', 'none');
				$('#tracklist_make_artist_section').fadeIn('fast');
			});
			obj_artist.setDisplay();
			scrollToSection('tracklist_make_artist_section');
		});
	};

	obj_artist_search.eventListner = function() {
		$('#artist_search_button').on('click', function() {
			$('#artist_id').html(null);
			obj_artist_search.init();
			obj_artist.init();
			obj_album.init();
			obj_artist_search.search_word = $('#artist_search_input').val();
			if (obj_artist_search.search_word.length === 0) {
				$('#artist_search_input').focus();
				return true;
			}
			$('#loading_artist_search').css('display', 'block');
			obj_artist_search.submit().done(function() {
				$('#loading_artist_search').css('display', 'none');
				if (Object.keys(obj_artist_search.ajax_response.arr_list).length === 0) {
					$('#tracklist_make_artist_not_exist_disp').fadeIn('fast');
					return true;
				}
				if (Object.keys(obj_artist_search.ajax_response.arr_list).length > obj_artist_search.LIMIT) {
					$('#tracklist_make_artist_search_more').css('display', 'block');
				}
				jQuery.each(obj_artist_search.ajax_response.arr_list, function(i, e) {
					var image     = '<img src="' + htmlentities(e['image_medium']) + '" data-original="'+ htmlentities(e['image_medium']) +'">';
					var name      = '<h3>' + htmlentities(e['name']) + '</h3>';
					var id_input  = '<input type="hidden" class="append_input_id" value="' + htmlentities(e['id']) + '">';
					var append_li = '<li class="ui-li-has-thumb ui-first-child ui-last-child"><a class="ui-btn ui-btn-icon-right ui-icon-carat-r">'+ image + name + '</a>' + id_input + '</li>';
					$('#artist_selected_ul').append(append_li);
					$(".lazy img").lazyload({
						effect: "fadeIn" ,
						effect_speed: 300 ,
					});
				});
				obj_artist_search.listCommit();
				obj_artist_search.ajax_response.arr_list = [];
				$('#tracklist_make_artist_section').css('display', 'none');
				$('#tracklist_make_to_artist_page').css('display', 'none');
			});
		});
		$('#tracklist_make_artist_search_more').on('click', function() {
			$('#tracklist_make_artist_search_more').css('display', 'block');
			obj_artist_search.current_page++;
			obj_artist_search.submit().done(function() {
				if (Object.keys(obj_artist_search.ajax_response.arr_list).length === 0) {
					$('#tracklist_make_artist_not_exist_disp').fadeIn('fast', function() {
						$('#tracklist_make_artist_search_more').fadeOut('slow');
					});
					return true;
				}
				jQuery.each(obj_artist_search.ajax_response.arr_list, function(i, e) {
					var image     = '<img src="' + htmlentities(e['image_medium']) + '" data-original="'+ htmlentities(e['image_medium']) +'">';
					var name      = '<h3>' + htmlentities(e['name']) + '</h3>';
					var id_input  = '<input type="hidden" class="append_input_id" value="' + htmlentities(e['id']) + '">';
					var append_li = '<li class="ui-li-has-thumb ui-first-child ui-last-child"><a class="ui-btn ui-btn-icon-right ui-icon-carat-r">'+ image + name + '</a>' + id_input + '</li>';
					$('#artist_selected_ul').append(append_li);
					$(".lazy img").lazyload({
						effect: "fadeIn" ,
						effect_speed: 300 ,
					});
				});
				obj_artist_search.listCommit();
				obj_artist_search.ajax_response.arr_list = [];
			});
		});
	};



	var obj_artist = {};
	obj_artist.name    = '';
	obj_artist.id      = '';
	obj_artist.img_src = '';
	obj_artist.init    = function() {
		if ($('#artist_id').html().length > 0) {
			obj_artist.name    = $('#tracklist_make_artist_name').html();
			obj_artist.id      = $('#artist_id').html();
			obj_artist.img_src = $('#tracklist_make_artist_image').attr('src');
			$('#artist_search_input').val(obj_artist.name);
			$('#tracklist_make_artist_section').css('display', 'block');
			$('tracklist_make_to_artist_page a').on('click', function() {
				window.location.href = '/artist/detail/' + obj_artist.id + '/';
			});
		} else {
			obj_artist.name    = '';
			obj_artist.id      = '';
			obj_artist.img_src = '';
			$('#tracklist_make_artist_name').html(null);
			$('#tracklist_make_artist_image').html(null);
		}
	};
	obj_artist.setDisplay = function() {
		$('#tracklist_make_artist_name').html(obj_artist.name);
		$('#tracklist_make_artist_image').html('<img src="' + obj_artist.img_src + '">');
	};
	obj_artist.eventListner = function() {
		obj_artist.init();
	};



	var obj_album = {};
	obj_album.id = '';
	obj_album.name = '';
	obj_album.SEARCH_LIMIT  = 20;
	obj_album.current_page  = 1;
	obj_album.ajax_response = [];
	obj_album.ajax_album_tracks = [];
	obj_album.is_active_preview_button = false;
	obj_album.init          = function() {
		obj_album.id                = '';
		obj_album.name              = '';
		obj_album.current_page      = 1;
		obj_album.ajax_response     = [];
		obj_album.ajax_album_tracks = [];
		obj_album.is_active_preview_button = false;
		$('#tracklist_make_album_art_more').css('display', 'none');
		$('#tracklist_make_album_not_exist_disp').css('display', 'none');
		$('#tracklist_make_album_append_area').html(null);
		$('#tracklist_make_album_description').css('display', 'none');
		$('#tracklist_make_album_selected_image_span').html(null);
		$('#tracklist_make_album_name_disp').html(null);
		$('#tracklist_make_album_release').html(null);
		$('#tracklist_make_album_copyright').html(null);
		$('#tracklist_make_album_selected_tracks').html(null);
		$('#tracklist_make_album_selected_tracks_description').css('display', 'none');
	};
	obj_album.submit = function() {
		if (obj_artist.id.length === 0) {
			return $.ajax({});
		}
		var params = {
			artist_name : obj_artist.name,
			artist_id   : obj_artist.id,
			limit       : obj_album.SEARCH_LIMIT,
			page        : obj_album.current_page
		};
		return $.ajax({
			type        : 'post',
			url         : '/api/album/list.json',
			datatype    : 'json',
			data        : JSON.stringify(params),
			contentType : 'application/json',
			cache       : false,
			success     : function(res, ans) {
				if (res.success === false) {
					alert('回線が混雑しているようです。' + res.response);
					return false;
				}
				jQuery.each(res.result.arr_list, function(i, e) {
					if (e.mbid_itunes.length > 0) {
						obj_album.ajax_response.push(e);
					}
				});
				return true;
			},
			error       : function() {
				alert('Network Error');
				return false;
			}
		});
	};
	obj_album.artHover     = function() {
		$('.album_image').off('mouseenter');
		$('.album_image').on('mouseenter', function() {
			var arr_list   = obj_album.ajax_response;
			var index      = $(this).index();
			var album_name = arr_list[index].name;
			var image_tag  = '<img src="' + arr_list[index].image_large + '" style="z-index:99999;">';
			if (arr_list[index].mbid_itunes.length > 0) {
				album_name += '&nbsp;<span class="play_exist">*</span>';
			}
			var width = $(window).width();
			width = width/2 - 100;
			$('#tracklist_make_hover_album_main_div').css('top', $(this).position().top + 40);
			$('#tracklist_make_hover_album_main_div').css('left', width);
			$('#tracklist_make_hover_album_name').html(album_name);
			$('#tracklist_make_hover_album_art').html(image_tag);
		});
		$('#tracklist_make_album_append_area').on('mouseleave', function() {
			$('#tracklist_make_hover_album_main_div div').html(null);
		});
	};
	obj_album.artClick = function() {
		$('.album_image').off('click');
		$('.album_image').on('click', function() {
			$('#loading_track_search').css('display', 'block');
			$('#tracklist_make_album_selected_tracks_description').fadeOut('fast');
			$('#tracklist_make_album_selected_tracks').fadeOut('fast');
			scrollToSection('tracklist_make_album_selected_image', function() {
				$('a[rel*=leanModal]').leanModal({
					top: 20,
					overlay : 0.5,
					closeButton: ".modal_close",
				});
			});
			$('#tracklist_make_hover_album_main_div div').html(null);
			var arr_list   = obj_album.ajax_response;
			var index      = $(this).index();
			var album_id   = arr_list[index].id;
			var album_name = arr_list[index].name;
			var copyright  = arr_list[index].copyright_itunes;
			var release    = arr_list[index].release_itunes.match(/^[\d]+-[\d]+-[\d]+/);
			var image_tag  = '<img src="' + arr_list[index].image_extralarge + '">';
			obj_album.id   = album_id;
			obj_album.name = album_name;
			$('#tracklist_make_album_selected_image_span').html(image_tag);
			$('#tracklist_make_album_name_disp').html(album_name);
			$('#tracklist_make_album_release').html(copyright);
			$('#tracklist_make_album_copyright').html(release);
			obj_album.getTracks({album_id: album_id}).done(function() {
				var html = '';
				jQuery.each(obj_album.ajax_album_tracks, function(i, e) {
					html += '<li class="track_name_list ui-li-static ui-body-inherit">';
					html += '<span>'+ ++i +'. </span>';
					if (e.preview_itunes.length > 0) {
						obj_album.is_active_preview_button = true;
					}
					html += '<span class="track_name">' + htmlentities(e.name) + '</span>';
					html += '<span class="track_name_add_btn">を追加</span>';
					html += '</li>';
				});
				$('#tracklist_make_album_selected_tracks_description').fadeIn('fast');
				$('#tracklist_make_album_selected_tracks').html(html);
				$('#tracklist_make_album_selected_tracks').fadeIn('fast', function() {
					$('#loading_track_search').css('display', 'none');
					obj_album.trackClick();
				});
			});
		});
	};
	obj_album.trackClick   = function() {
		// 初期化
		$('.track_name_list').off('click');

		$('.track_name_list').on('click', function() {
			if (obj_tracklist.arr_list.length >= obj_tracklist.max_count) {
				alert('最大数に達しました。選択できる数は' + obj_tracklist.max_count + '曲までになります。');
				return false;
			}
			var index = $(this).index();
			var arr_list = obj_album.ajax_album_tracks;
			if (obj_tracklist.isExistTrackId(arr_list[index].id)) {
				alert('この曲はすでに登録されています。');
				return true;
			}
			$('#tracklist_make_link_to_leanModal').click();
			$('#tracklist_make_list_close_btn').off('click');
			$('#tracklist_make_list_close_btn').on('click', function() {
				obj_tracklist.modal_close();
			});

			obj_tracklist.arr_list.push({
				track_id    : arr_list[index].id,
				artist_id   : arr_list[index].artist_id,
				album_id    : arr_list[index].album_id,
				track_name  : arr_list[index].name,
				artist_name : obj_artist.name,
				album_name  : obj_album.name,
			});
			obj_tracklist.setTrackList(true);
			obj_tracklist.adjustSize();
		});
	};
	obj_album.getTracks    = function(params) {
		return $.ajax({
			type: 'post',
			url: '/api/track/albumtracklist.json',
			datatype: 'json',
			data: JSON.stringify(params),
			contentType: 'application/json',
			cache: false,
			success: function(res, ans) {
				obj_album.ajax_album_tracks = res.result.arr_list;
				return true;
			},
			error: function(){
				return false;
			}
		});
	};
	obj_album.getAlbumList = function() {
		$('#loading_album_search').css('display', 'block');
		obj_album.submit().done(function() {
			$('#loading_album_search').css('display', 'none');
			if (Object.keys(obj_album.ajax_response).length === 0) {
				$('#tracklist_make_album_art_more').css('display', 'none');
				$('#tracklist_make_album_not_exist_disp').fadeIn('fast');
				return true;
			}

			setTimeout(function() {
				scrollToSection('tracklist_make_artist_section');
			}, 500);

			if (Object.keys(obj_album.ajax_response).length < obj_album.SEARCH_LIMIT) {
				$('#tracklist_make_album_art_more').css('display', 'none');
			} else {
				$('#tracklist_make_album_art_more').css('display', 'block');
			}
			var html = '';
			jQuery.each(obj_album.ajax_response, function(i, e) {
				html += '<span class="album_image">';
				html += '<img src="' + e.image_medium + '" class="art_small"  title="アルバム 『' + e.name + '』 を選択">';
				html += '<img src="' + e.image_large + '" class="art_large" style="display: none;">';
				html += '</span>';
			});
			$('#tracklist_make_album_append_area').html(html);
			$('#tracklist_make_album_description').fadeIn('fast');
			obj_album.artHover();
			obj_album.artClick();
		});
	};
	obj_album.eventListner = function() {
		if ($('#artist_id').html().length > 0) {
			obj_album.init();
			obj_album.getAlbumList();
		}
		$('#tracklist_make_album_search').on('click', function() {
			obj_album.init();
			obj_album.getAlbumList();
		});
		$('#tracklist_make_album_art_more').on('click', function() {
			obj_album.current_page++;
			obj_album.getAlbumList();
		});
	};



	var obj_tracklist = {};
	obj_tracklist.max_count = 5;
	obj_tracklist.list_name = '';
	obj_tracklist.arr_list  = [];
	obj_tracklist.current_position = null;
	obj_tracklist.max_position = null;
	obj_tracklist.user_name = '';
	obj_tracklist.edit_mode = false;
	obj_tracklist.tracklist_id = null;

	obj_tracklist.init = function() {
		obj_tracklist.max_count = 5;
		obj_tracklist.list_name = '';
		obj_tracklist.arr_list  = [];
		obj_tracklist.current_position = null;
		obj_tracklist.max_position = null;
		obj_tracklist.user_name = '';
		$('#tracklist_title').val(null);
		$('#tracklist_username').val(null);
		$('#tracklist_make_list_artist_name').html(null);
		$('#tracklist_make_list_tracks').html(null);
		$('#tracklist_make_list_count').html('0');
	};

	obj_tracklist.adjustSize = function() {
		var body_height = $('#tracklist_make_list').innerHeight();
		var top_height = $('#tracklist_make_form_top').innerHeight();
		var frame_height = (body_height - top_height) * 0.65;
		$('#tracklist_make_list_div').css('height', frame_height);
	};

	obj_tracklist.setArtist = function() {
		$('#tracklist_make_list_artist_name').css('display', 'none');
		$('#tracklist_make_list_artist_name').html(obj_artist.name);
		$('#tracklist_make_list_artist_id').val(obj_artist.id);
		$('#tracklist_make_list_artist_name').fadeIn('fast', function() {
		});
	};

	/* artist_id, artist_nameを削除 */
	obj_tracklist.removeArtist = function() {
		$('#tracklist_make_list_artist_name').html(null);
		$('#tracklist_make_list_artist_id').val('指定なし');
	};

	/* artist_id以外がリストに存在するか？ */
	obj_tracklist.isOnlyArtist = function(artist_id) {
		if (obj_tracklist.arr_list.length === 0) {
			return true;
		}
		var result = true;
		jQuery.each(obj_tracklist.arr_list, function(i, v) {
			if (v.artist_id != artist_id) {
				result = false;
				return false;
			}
		});
		return result;
	};

	/* track_idがそんざいするか？ */
	obj_tracklist.isExistTrackId = function(track_id) {
		var result = false;
		jQuery.each(obj_tracklist.arr_list, function(i, v) {
			if (v.track_id === track_id) {
				result = true;
				return false;
			}
		});
		return result;
	};

	obj_tracklist.setTrackList = function(is_last) {
		var html = '';
		if (obj_tracklist.arr_list.length > 0) {
			var obj_artist_id = {};
			var artist_name = '';
			jQuery.each(obj_tracklist.arr_list, function(i, v) {
				obj_artist_id[v.artist_id] = true;
				artist_name = v.artist_name;
				html += '<li class="ui-li-static ui-body-inherit">';
				html += '<span class="no">'+ ++i +'. </span>';
				html += '<span>' + v.track_name + '</span>';
				html += '<br />';
				html += '<span>&nbsp;&nbsp;&nbsp;';
				html += '<span class="del_track_list">削除</span>';
				html += ' / ' + v.artist_name + '</span>';
				html += '<input ';
				html += '<input type="hidden" value="'+ v.track_id +'">';
				html += '</li>';
			});
			if (Object.keys(obj_artist_id).length > 1) {
				$('#tracklist_make_list_artist_name').html('複数のアーティスト');
			} else {
				$('#tracklist_make_list_artist_name').html(artist_name);
			}
		}
		$('#tracklist_make_list_tracks').html(html);
		$('#tracklist_make_list_count').html(obj_tracklist.arr_list.length);

		var height_div = $('#tracklist_make_list_div').innerHeight();
		var height_ul  = $('#tracklist_make_list_tracks').innerHeight() + $('#tracklist_make_list_tracks li:last').innerHeight();
		obj_tracklist.max_position = height_ul -  height_div;
		if (obj_tracklist.max_position < 0) {
			obj_tracklist.max_position = 0;
		}
		obj_tracklist.current_position = 0;
		if (is_last) {
			obj_tracklist.current_position = obj_tracklist.max_position;
			$('#tracklist_make_list_div').animate({scrollTop:obj_tracklist.current_position}, 1200, null);
		}
		obj_tracklist.removeTrackList();
	};

	obj_tracklist.removeTrackList = function() {
		$('.del_track_list').on('click', function() {
			var index = $(this).closest('li').index();
			if (confirm(obj_tracklist.arr_list[index].track_name + 'をリストから削除しますか？')) {
				obj_tracklist.arr_list.splice(index, 1);
				obj_tracklist.setTrackList(false);
			}
		});
	};

	obj_tracklist.sendAjax = function() {
		var params = {
			artist_id : obj_artist.id,
			title     : obj_tracklist.list_name,
			user_name : obj_tracklist.user_name,
			arr_list  : obj_tracklist.arr_list,
			edit_mode : obj_tracklist.edit_mode,
			tracklist_id: obj_tracklist.tracklist_id,
		};
		return $.ajax({
			type        : 'post',
			url         : '/api/tracklist/set.json',
			datatype    : 'json',
			data        : JSON.stringify(params),
			contentType : 'application/json',
			cache       : false,
			success     : function(res, ans) {
				if (res.success == false) {
					alert('入力項目をご確認ください');
					return false;
				}
				return true;
			},
			error       : function(res, ans) {
				alert('Network Error');
				return false;
			}
		});
	}

	obj_tracklist.modal_close = function() {
		$("#lean_overlay").fadeOut(80, function() {
			$('#tracklist_make_list').css({ 'display' : 'none' });
		});
	};

	obj_tracklist.eventListner = function() {
		if ($('#user_id').html().length > 0) {
			obj_tracklist.max_count = 30;
		}
		$('#tracklist_make_max_count').html(obj_tracklist.max_count);

		$('#tracklist_title_ok_btn').on('click', function() {
			if ($('#tracklist_title').val().length === 0) {
				$(this).focus();
				return true;
			}
		});
		$('#tracklist_make_header_close_btn').on('click', function() {
			obj_tracklist.modal_close();
		});

		/* draggable */
		$('#tracklist_make_list_tracks').sortable({
			axis   : 'y',
			revert : false,
			scroll : 'auto',
			update : function() {
				var arr_list = [];
				var track_id = '';
				jQuery('#tracklist_make_list_tracks li').each(function() {
					track_id = $(this).children('input').val();
					jQuery.each(obj_tracklist.arr_list, function(ii, vv) {
						if (track_id === vv.track_id) {
							arr_list.push(vv);
						}
					});
				});
				obj_tracklist.arr_list = arr_list;
				setTimeout(function() {
					$('#tracklist_make_list_tracks li').each(function(i, v) {
						var index = (i + 1) + '. ';
						$('.no').eq(i).html(index);
					});
				}, 250);
			}
		});

		/* up down */
		$('#tracklist_make_list_up').on('click', function() {
			if (obj_tracklist.max_position < 0) {
				return true;
			}
			obj_tracklist.current_position = obj_tracklist.current_position - 90;
			if (obj_tracklist.current_position < 0) {
				obj_tracklist.current_position = 0;
			}
			$('#tracklist_make_list_div').animate({scrollTop:obj_tracklist.current_position}, 500, null);
		});

		$('#tracklist_make_list_down').on('click', function() {
			obj_tracklist.current_position = obj_tracklist.current_position + 90;
			if (obj_tracklist.current_position > obj_tracklist.max_position) {
				obj_tracklist.current_position = obj_tracklist.max_position;
			}
			$('#tracklist_make_list_div').animate({scrollTop:obj_tracklist.current_position}, 500, null);
		});

		/* tracklist submit */
		$('#tracklist_make_list_submit_btn').on('click', function() {
			if (obj_tracklist.arr_list.length === 0) {
				alert('リストが選択されていません。追加したのちにもう一度お願いします。');
				return false;
			}
			var confirm_txt = "";
			var default_list_name = "マイベストトラック！";
			var default_user_name = "ゲスト";

			// タイトル
			obj_tracklist.list_name = $('#tracklist_title').val().replace(/^[\s　]+/g, '').replace(/[\s　]+$/, '');
			if (obj_tracklist.list_name.length === 0) {
				obj_tracklist.list_name = default_list_name;
			}

			// 未ログイン
			if ($('#tracklist_username')[0]) {
				obj_tracklist.user_name = $('#tracklist_username').val().replace(/^[\s　]+/g, '').replace(/[\s　]+$/, '');
				if (obj_tracklist.user_name.length === 0) {
					obj_tracklist.user_name = default_user_name;
					confirm_txt = "タイトル： " + obj_tracklist.list_name + "\n\rお名前 ： " + obj_tracklist.user_name + "\n\rで投稿を送信します。よろしいですか？";
				}
			// ログイン済み
			} else {
				obj_tracklist.user_name = "";
				confirm_txt = "タイトル： " + obj_tracklist.list_name + "\n\rで投稿を送信します。よろしいですか？";
			}

			if ( ! confirm(confirm_txt)) {
				return false;
			}

			// アーティスト限定
			var artist_id = {};
			var artist_name = '';
			jQuery.each(obj_tracklist.arr_list, function(i, v) {
				artist_id[v.artist_id] = true;
				artist_name = v.artist_name;
			});
			if (Object.keys(artist_id).length > 1) {
				obj_tracklist.artist_id   = '';
				obj_tracklist.artist_name = '';
			}

			obj_tracklist.sendAjax().done(function(res, ans) {
				if (obj_artist.id) {
					alert('送信完了いたしました。投稿されたページを表示します。');
					location.href = '/tracklist/detail/' + res.result.tracklist_id + '/';
				} else {
					obj_tracklist.init();
					$('#tracklist_make_list_close_btn').click();
					alert('投稿完了しました。\n\rアーティストページ等に反映されますので、もうしばらくお待ちください。');
				}
			});
		});
	};

	// 編集モード
	if ($('#edit_mode').html() == true) {
		window.onload = function() {
			obj_tracklist.edit_mode = true;
			obj_tracklist.tracklist_id = $('#tracklist_id').html();
			$('#tracklist_make_link_to_leanModal').click();
			var li_count = $('#tracklist_make_list_tracks li').length;
			$('#tracklist_make_list_count').html(li_count);
			for (var i=0; i<li_count; i++) {
				$('.no').eq(i).html;
				obj_tracklist.arr_list.push({
					track_id: $('.trackid').eq(i).val(),
					artist_id: $('.track_artistid').eq(i).val(),
					album_id: $('.track_albumid').eq(i).val(),
					track_name: $('.trackname').eq(i).html(),
					artist_name: $('.track_artistname').eq(i).html(),
					album_name: $('.track_albumname').eq(i).val(),
				});
			}
			obj_tracklist.adjustSize();
			var height_div = $('#tracklist_make_list_div').innerHeight();
			var height_ul  = $('#tracklist_make_list_tracks').innerHeight() + $('#tracklist_make_list_tracks li:last').innerHeight();
			obj_tracklist.max_position = height_ul - height_div;
			if (obj_tracklist.max_position < 0) {
				obj_tracklist.max_position = 0;
			}
			obj_tracklist.current_position = 0;
			obj_tracklist.removeTrackList();
		}
	}
	
	
	
	
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
				obj_artist_search.listCommit();
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
	
	
	obj_tracklist.eventListner();
	obj_artist_search.eventListner();
	obj_artist.eventListner();
	obj_album.eventListner();
	
	var objLastSearchSection = new LastSearchSection();
	objLastSearchSection.init();
	objLastSearchSection.eventListner();
	obj_artist_search.listCommit();



});