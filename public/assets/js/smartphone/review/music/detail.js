$(document).bind("pageinit", function(){
	$.mobile.ajaxEnabled = false;
});
jQuery(function() {

	var modal = $('a[rel*=leanModal]').leanModal({
		top: 80,
		overlay : 0.5,
		closeButton: ".modal_close",
	});
	$('.modaldiv a').on('click', function() {
		$("#lean_overlay").fadeOut(200);
		$('.modaldiv').css({ 'display' : 'none' });
	});

	var LoginClass = {};
	// ログインユーザID
	LoginClass.user_id    = $('#review_music_detail_user_me_id').val();
	// ログインユーザ名
	LoginClass.user_name  = $('#review_music_detail_user_me_name').val();
	// ログイン画像URL
	LoginClass.user_image = $('#review_music_detail_user_me_image').val();

	var ReviewClass = {};
	// レビューユーザID
	ReviewClass.user_id   = $('#review_music_detail_review_user_id').val();
	// レビューID
	ReviewClass.review_id = $('#review_music_detail_review_id').val();
	// レビュータイプ
	ReviewClass.about     = $('#review_music_detail_about').val();

	var CommentClass = {};
	// api取得のオフセット値
	CommentClass.offset = 0;
	// コメント欄のスクロールトップのセット位置
	CommentClass.scroll_top_position = 0;
	// コメント一覧の最初のコメント位置
	CommentClass.first_position = 0;
	// コメント一覧の最後のコメント位置
	CommentClass.last_position = 0;
	// コメント一覧の高さサイズ
	CommentClass.height = 0;

	if ($('.user_comment_div_pub').length > 0) {
		CommentClass.first_position = $('.user_comment_div_pub').first().position().top;
		CommentClass.last_position  = $('.user_comment_div_pub').last().position().top;
		CommentClass.height         = CommentClass.last_position - CommentClass.first_position + $('.user_comment_div_pub').last().innerHeight();
	}
	CommentClass.scroll_top_position = CommentClass.height;
	$('#comment_disp').animate({scrollTop:CommentClass.scroll_top_position}, 'slow', null, function(){
		CommentClass.setCommentPagination();
		CommentClass.scroll_top_position = $('#comment_disp').scrollTop();
	});

	// ページネーションリンクの出し分けを操作する
	CommentClass.setCommentPagination = function() {

		// 一旦クリックバインドは無効
		$('.comment_pagination_up, .comment_pagination_down').off('click');

		if ($('.user_comment_div_pub').length > 0) {
			CommentClass.first_position = $('.user_comment_div_pub').first().position().top;
			CommentClass.last_position  = $('.user_comment_div_pub').last().position().top;
			CommentClass.height         = CommentClass.last_position - CommentClass.first_position + $('.user_comment_div_pub').last().innerHeight();
			if ($('.user_comment_div_pub:first').offsetParent().innerHeight() >= CommentClass.height) {
				$('.comment_pagination_up, .comment_pagination_down').css('visibility', 'hidden');
				return true;
			}
		} else {
			$('.comment_pagination_span').css('visibility', 'hidden');
			$('.comment_updown_td').css('display', 'none');
			$('#comment_disp').css('background', 'inherit');
			return true;
		}

		if (CommentClass.first_position >= 0) {
			$('.comment_pagination_up').css('visibility', 'hidden');
		} else {
			$('.comment_updown_td').css('display', 'table-cell');
			$('.comment_pagination_up').css('visibility', 'visible');

			$('.comment_pagination_up').on('click', function() {
				$('.comment_pagination_up').off('click');
				$('.comment_pagination_up').css('visibility', 'hidden');
				CommentClass.scroll_top_position = CommentClass.scroll_top_position - 200; // 200px戻る
				if (CommentClass.scroll_top_position <= 0) {
					CommentClass.scroll_top_position = 0;
					$('.comment_pagination_up').css('visibility', 'hidden');
				}
				$('#comment_disp').animate({scrollTop:CommentClass.scroll_top_position}, 'slow', null, function() {
					CommentClass.setCommentPagination();
					CommentClass.scroll_top_position = $('#comment_disp').scrollTop();
				});
			});
		}

		if (CommentClass.last_position + $('.user_comment_div_pub:last').innerHeight() <= $('.user_comment_div_pub:last').offsetParent().innerHeight()) {
			$('.comment_pagination_down').css('visibility', 'hidden');
		} else {
			$('.comment_updown_td').css('display', 'table-cell');
			$('.comment_pagination_down').css('visibility', 'visible');
			$('.comment_pagination_down').on('click', function() {
				$('.comment_pagination_down').off('click');
				$('.comment_pagination_down').css('visibility', 'hidden');
				CommentClass.scroll_top_position = CommentClass.scroll_top_position + 200; // 200px進む
				$('#comment_disp').animate({scrollTop:CommentClass.scroll_top_position}, 'slow', null, function() {
					CommentClass.setCommentPagination();
					CommentClass.scroll_top_position = $('#comment_disp').scrollTop();
				});
			});
		}
	};

	// コメントを送信する
	CommentClass.setSendCommentEventListner = function() {
		$('#comment_submit').click(function() {
			var params = {};
			params.about           = ReviewClass.about;
			params.review_id       = ReviewClass.review_id;
			params.review_user_id  = ReviewClass.user_id;
			params.comment_user_id = LoginClass.user_id;
			params.comment         = $('#comment').val();
			if (params.comment.length == 0) {
				return false;
			}
			$(this).prop('disabled', true);
			CommentClass.sendSetAjax(params).done(function(res) {
				if (res.success == false) {
					alert('APIからのレスポンスがありません');
					$(this).prop('disabled', false);
					return false;
				}
				if (res.result.reflection != true) {
					$(this).on('click');
					alert('申し訳ございません。投稿を処理できませんでした。');
					$(this).prop('disabled', false);
					return true;
				}
				var html = '';
				var last_id = null;
				$.each(res.result.arr_list, function() {
					last_id = this.comment_id;
					if (params.review_user_id === this.comment_user_id) {
						html +=
							'<div class="user_comment_div_me user_comment_div_pub" id="user_comment_' + this.comment_id +'" >' +
								'<div class="user_comment_text_name_me">' +
									'<span class="user_comment_datetime_me">'+ this.comment_datetime +'</span>' +
									'<span class="user_comment_user_name_me"><a href="/user/you/'+ this.comment_user_id +'"> ' + htmlentities(this.comment_user_name) + '</a></span> ' +
									'<div class="user_comment_text_me">' + htmlentities(this.comment);
									if (this.comment_user_id == LoginClass.user_id) {
										html += '<a class="comment_delete_link_me">削除</a>';
									}
							html += '</div>' +
								'</div>' +
								' <div class="user_comment_img_me">' +
									' <div><a href="/user/you/' + this.comment_user_id + '"><img src="' + this.comment_user_image + '"></a></div>' +
								' </div>' +
							'</div>' +
							'<hr class="user_comment_hr" id="user_comment_hr_'+ this.comment_id +'" />';
					} else {
						html +=
							'<div class="user_comment_div user_comment_div_pub" id="user_comment_' + this.comment_id +'" >' +
								'<div class="user_comment_img">' +
									'<div><a href="/user/you/' + this.comment_user_id + '"><img src="' + this.comment_user_image + '"></a></div>' +
								'</div>' +
								'<div class="user_comment_text_name">' +
									'<span class="user_comment_user_name"><a href="/user/you/'+ this.comment_user_id +'"> ' + htmlentities(this.comment_user_name) + '</a></span> ' +
									'<span class="user_comment_datetime">'+ this.comment_datetime +'</span>' +
									'<div class="user_comment_text">' + htmlentities(this.comment);
									if (this.comment_user_id == LoginClass.user_id) {
										html += '<a class="comment_delete_link">削除</a>';
									}
							html += '</div>' +
								'</div>' +
							'</div>' +
							'<hr class="user_comment_hr" id="user_comment_hr_'+ this.comment_id +'" />';
					}
				});
				$('#comment_disp').fadeOut('fast', function() {
					$('.comment_disp_td').html('<div class="comment_disp" id="comment_disp"></div>');
					$('#comment_disp').html(html);
					var comment_disp_height = $('#comment_disp').height();
					CommentClass.scroll_top_position = $('#user_comment_' + last_id).position().top + 100;
					$('#comment_table').fadeIn('fast', function() {
						$('#comment_disp').animate({scrollTop:CommentClass.scroll_top_position}, 'slow', null, function() {
							CommentClass.setCommentPagination();
							CommentClass.setDeleteComment();
							CommentClass.scroll_top_position = $('#comment_disp').scrollTop();
							var comment_table_position = $('#comment_table').offset().top - $('header').outerHeight();
							$('html,body').animate({scrollTop:comment_table_position}, 'slow');
						});
					});
				});
				$('#comment_count_span').html(res.result.count);
				$('#comment').val(null);
				$('#comment_submit').prop('disabled', false);
			});
		});
	};

	// コメントを削除
	CommentClass.setDeleteComment = function() {
		$('.comment_delete_link, .comment_delete_link_me').off('click');
		$('.comment_delete_link, .comment_delete_link_me').on('click', function() {
			if ( ! confirm('コメントを削除してよろしいですか？')) {
				return false;
			}
			var params = {};
			params.comment_id = $(this).parent().parent().parent('.user_comment_div_pub').attr('id').match(/[0-9]+$/);
			params.comment_id = parseInt(params.comment_id);
			params.user_me_id = LoginClass.user_id;
			CommentClass.sendDeleteAjax(params).done(function(res) {
				if (res.success == false) {
					alert('APIからのレスポンスがありません');
					$(this).prop('disabled', false);
					return false;
				}
				if (res.result.reflection != true) {
					alert('コメントの削除ができませんでした');
					$(this).prop('disabled', false);
					return false;
				}
				$('#user_comment_' + params.comment_id).hide('fast', function() {
					$('#user_comment_hr_' + params.comment_id).remove();
					var comment_count = $('#comment_count_span').html();
					comment_count = parseInt(comment_count) - 1;
					$('#comment_count_span').html(comment_count);
					$('#user_comment_' + params.comment_id).remove();
					CommentClass.setCommentPagination();
				});
				CommentClass.comment_count = CommentClass.comment_count - 1;
			});
		});
	};

	// もっと見るリンクからの過去のコメントを表示
	CommentClass.setMoreComment = function() {
		$('#comment_more_link').on('click', function() {
			var html   = "";
			var params = {};
			params.limit          = parseInt($('#comment_limit').val());
			params.offset         = CommentClass.offset + params.limit;
			params.review_id      = ReviewClass.review_id;
			params.review_user_id = ReviewClass.user_id;
			params.about          = ReviewClass.about;
			CommentClass.getAjax(params).done(function(res) {
				if (res.success == false) {
					alert('APIからのレスポンスがありません');
					$(this).prop('disabled', false);
					return false;
				}
				$('#comment_count_span').html(res.result.count);
				$('#comment_more_link').parent('div').remove();
				var first_comment_id = res.result.arr_list[0].comment_id;
				var last_comment_id  = null;
				$.each(res.result.arr_list, function() {
					last_comment_id = this.comment_id;
					if (params.review_user_id === this.comment_user_id) {
						html +=
							'<div class="user_comment_div_me user_comment_div_pub" id="user_comment_' + this.comment_id +'" >' +
								'<div class="user_comment_text_name_me">' +
									'<span class="user_comment_datetime_me">'+ this.comment_datetime +'</span>' +
									'<span class="user_comment_user_name_me"><a href="/user/you/'+ this.comment_user_id +'"> ' + htmlentities(this.comment_user_name) + '</a></span> ' +
									'<div class="user_comment_text_me">' + htmlentities(this.comment);
									if (this.comment_user_id == LoginClass.user_id) {
										html += '<a class="comment_delete_link_me">削除</a>';
									}
							html += '</div>' +
								'</div>' +
								' <div class="user_comment_img_me">' +
									' <div><a href="/user/you/' + this.comment_user_id + '"><img src="' + this.comment_user_image + '"></a></div>' +
								' </div>' +
							'</div>' +
							'<hr class="user_comment_hr" id="user_comment_hr_'+ this.comment_id +'" />';
					} else {
						html +=
							'<div class="user_comment_div user_comment_div_pub" id="user_comment_' + this.comment_id +'" >' +
								'<div class="user_comment_img">' +
									'<div><a href="/user/you/' + this.comment_user_id + '"><img src="' + this.comment_user_image + '"></a></div>' +
								'</div>' +
								'<div class="user_comment_text_name">' +
									'<span class="user_comment_user_name"><a href="/user/you/'+ this.comment_user_id +'"> ' + htmlentities(this.comment_user_name) + '</a></span> ' +
									'<span class="user_comment_datetime">'+ this.comment_datetime +'</span>' +
									'<div class="user_comment_text">' + htmlentities(this.comment);
									if (this.comment_user_id == LoginClass.user_id) {
										html += '<a class="comment_delete_link">削除</a>';
									}
							html += '</div>' +
								'</div>' +
							'</div>' +
							'<hr class="user_comment_hr" id="user_comment_hr_'+ this.comment_id +'" />';
					}
				});
				CommentClass.offset = params.offset;
				CommentClass.comment_count = CommentClass.comment_count + res.result.arr_list.length;
				if (params.offset + params.limit < res.result.count) {
					html = '<div><a id="comment_more_link">もっと見る</a></div>' + html;
				}
				$('#comment_disp').prepend(html);
				var diff = $('#user_comment_' + last_comment_id).position().top - $('#user_comment_' + first_comment_id).position().top;
				CommentClass.scroll_top_position = CommentClass.scroll_top_position + diff - 150;
				$('#comment_disp').scrollTop(CommentClass.scroll_top_position + 80);
				$('#comment_disp').animate({scrollTop:CommentClass.scroll_top_position}, 'slow', null, function() {
					CommentClass.scroll_top_position = $('#comment_disp').scrollTop();
					CommentClass.setCommentPagination();
					CommentClass.setDeleteComment();
					CommentClass.setMoreComment();
				});
			});
		});
	}

	CommentClass.sendSetAjax = function(params) {
		var response = {};
		return $.ajax({
			type: 'post',
			url: '/api/review/sendcomment.json',
			datatype: 'json',
			data: JSON.stringify(params),
			cache: false,
			success: function(res, ans) {
				if (res.success === false) {
					return false;
				}
			},
			error: function() {
				alert('申し訳ございません。ネットワークが混雑し通信に失敗しました。[sendcomment]');
				return false;
			}
		});
	};
	CommentClass.sendDeleteAjax = function(params) {
		var response = {};
		return $.ajax({
			type: 'post',
			url: '/api/review/deletecomment.json',
			datatype: 'json',
			data: JSON.stringify(params),
			cache: false,
			success: function(res, ans) {
				if (res.success === false) {
					return false;
				}
			},
			error: function() {
				alert('申し訳ございません。ネットワークが混雑し通信に失敗しました。[deletecomment]');
				return false;
			}
		});
	};
	CommentClass.getAjax = function(params) {
		var response = {};
		return $.ajax({
			type: 'post',
			url: '/api/review/getcomment.json',
			datatype: 'json',
			data: JSON.stringify(params),
			cache: false,
			success: function(res, ans) {
				if (res.success === false) {
					return false;
				}
			},
			error: function() {
				alert('申し訳ございません。ネットワークが混雑し通信に失敗しました。[getcomment]');
			}
		});
	};


	var CoolClass = {};
	CoolClass.cool_offset = 30;
	CoolClass.sendAjax = function(params) {
		var response = {};
		return $.ajax({
			type: 'post',
			url: $('#review_music_detail_cool_api_url').val(),
			datatype: 'json',
			data: JSON.stringify(params),
			cache: false,
			success: function(res, ans) {
				if (res.success === false) {
					return false;
				}
				return res;
			},
			error: function() {
				alert('申し訳ございません。ネットワークが混雑しております。[cool]');
				return false;
			}
		});
	};
	CoolClass.getAjax = function(params) {
		var response = {};
		return $.ajax({
			type: 'post',
			url: $('#review_music_detail_cool_more_api_url').val(),
			datatype: 'json',
			data: JSON.stringify(params),
			cache: false,
			success: function (res, ans) {
				if (res.success === false) {
					return false;
				}
				return res;
			},
			error: function() {
				alert('network error[1]');
				return false;
			}
		});
	}

	CoolClass.setEventListner = function() {
		$('.cool_btn').on('click', function() {
			$(this).off('click');
			var params = {};
			params.about          = ReviewClass.about;
			params.review_id      = ReviewClass.review_id;
			params.review_user_id = ReviewClass.user_id;
			params.cool_user_id   = LoginClass.user_id;
			CoolClass.sendAjax(params).done(function(res) {
				if (res.success == false) {
					return false;
				}
				if (res.result.length === 0) {
					return false;
				}
				if (res.result.reflection === true) {
					if (LoginClass.user_image.length > 0) {
						var img_src = "<img src='" + LoginClass.user_image + "' title='" + LoginClass.user_name + "がクール！'>";
						var modaldiv_src = "<div class='modaldiv_list'><a class='ui-link' href='/user/you/" + LoginClass.user_id +"' rel='external'><span class='modaldiv_list_img'><img src='"+ LoginClass.user_image +"'></span> <span class='modaldiv_list_name'>" + LoginClass.user_name + "</span></div>";
						$('#review_music_detail_send_cool_div').prepend(img_src);
						$('#modaldiv_cool_users').prepend(modaldiv_src);
					}
					$('.cool_btn').addClass('cool_btn_disabled');
					$('.cool_btn').removeClass('cool_btn');
					$('.cool_howmany').html(res.result.cool_count);
				} else {
					$(this).on('click');
					alert('申し訳ございません。投稿を処理できませんでした。');
				}
			});
		});

		var cool_offset = 20;
		$('#modaldiv_cool_more').on('click', function() {
			var params = {};
			params.review_id = ReviewClass.review_id;
			params.about     = ReviewClass.about;
			params.user_id   = LoginClass.user_id;
			params.offset    = cool_offset;
			params.limit     = 20;
			CoolClass.getAjax(params).done(function(res) {
				if (res.success == false) {
					return false;
				}
				if (res.result.length === 0) {
					return false;
				}
				var user_id      = '';
				var user_name    = '';
				var image_url    = '';
				var modaldiv_src = '';
				res.result.arr_list.forEach(function(v) {
					user_id   = v.user_id;
					user_name = v.user_name;
					image_url = v.image_url;
					modaldiv_src += "<div class='modaldiv_list'><a class='ui-link' href='/user/you/" + user_id +"' rel='external'><span class='modaldiv_list_img'><img src='"+ image_url +"'></span> <span class='modaldiv_list_name'>" + user_name + "</span></div>";
				});
				cool_offset = cool_offset + params.limit;
				$('#modaldiv_cool_users').append(modaldiv_src);
				var p = $('#modaldiv_cool_more').offset().top;
				$('#modaldiv_cool').animate({ scrollTop: p}, 'fast');
				if (res.result.arr_list.length < params.limit) {
					$('#modaldiv_cool_more').css('display', 'none');
				}
				return true;
			});
		});

		$('.modaldiv_list').hover(
			function() {
				$(this).children('a').children('.modaldiv_list_img').css('filter', 'brightness(150%)');
				$(this).children('a').children('.modaldiv_list_img').css('-webkit-filter', 'brightness(150%)');
				$(this).children('a').children('.modaldiv_list_img').css('-moz-filter', 'brightness(150%)');
				$(this).children('a').children('.modaldiv_list_img').css('-o-filter', 'brightness(150%)');
				$(this).children('a').children('.modaldiv_list_img').css('-ms-filter', 'brightness(150%)');
			},
			function() {
				$(this).children('a').children('.modaldiv_list_img').css('filter', 'brightness(70%)');
				$(this).children('a').children('.modaldiv_list_img').css('-webkit-filter', 'brightness(70%)');
				$(this).children('a').children('.modaldiv_list_img').css('-moz-filter', 'brightness(70%)');
				$(this).children('a').children('.modaldiv_list_img').css('-o-filter', 'brightness(70%)');
				$(this).children('a').children('.modaldiv_list_img').css('-ms-filter', 'brightness(70%)');
			}
		);

		$('.modaldiv_list').on('touchstart', function() {
			$(this).children('a').children('.modaldiv_list_img').css('filter', 'brightness(150%)');
			$(this).children('a').children('.modaldiv_list_img').css('-webkit-filter', 'brightness(150%)');
			$(this).children('a').children('.modaldiv_list_img').css('-moz-filter', 'brightness(150%)');
			$(this).children('a').children('.modaldiv_list_img').css('-o-filter', 'brightness(150%)');
			$(this).children('a').children('.modaldiv_list_img').css('-ms-filter', 'brightness(150%)');
		});
	};


	// eventListner
	CommentClass.setSendCommentEventListner();
	CommentClass.setDeleteComment();
	CommentClass.setMoreComment();
	CoolClass.setEventListner();


	var global_timer = null;
	var is_all_play  = false;
	var number = 0;
	var a = {};

	// 全曲再生
	$('#review_music_write_listen_all').on('click', function() {
		$(this).css('background', 'red').css('color', '#fff');
		reset_preview(number);

		if (is_all_play === true) {
			is_all_play = false;
			$('.loading').hide();
			$('#review_music_write_listen_all').css('background', 'inherit').css('color', '#000');
			return true;
		}

		is_all_play = true;
		$(this).css('background', 'red').css('color', '#fff');
		var all_count = $('.preview_button').length;
		a = {};
		for (i=0; i<all_count; i++) {
			a[i] = $('.preview_itunes').eq(i)[0];
			a[i].load();
		}

		var disp_track_number = number + 1;
		$('#current_track').html('TRACK<span id="current_track_num">' + disp_track_number + '</span>');
		$('.loading').hide();
		a[number].play();
		$('.preview_button').eq(number).html('◾');
		$('.preview_button').eq(number).addClass('preview_button_pause');

		global_timer = setInterval(function() {
			var current_time = Math.round(a[number].currentTime * 100)/100;
			$('#current_time').html(current_time);
			if (a[number].paused === true && a[number].currentTime > 0) {
				$('.preview_button').eq(number).html('▶️');
				$('.preview_button').eq(number).removeClass('preview_button_pause');
				number = number + 1;
				$('.preview_button').eq(number).html('◾️');
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


	// 個別再生
	$('.preview_button').on('click', function() {
		var global_i = $('.preview_button').index(this);

		// 一旦全アルバムトラック再生を停止させる
		reset_preview(global_i);
		$('#review_music_write_listen_all').css('background', 'inherit').css('color', '#000').css('text-decoration', 'none');

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

	function htmlentities(str) {
		return str.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
	}
});
