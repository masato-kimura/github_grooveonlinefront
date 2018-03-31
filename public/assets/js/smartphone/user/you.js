$(document).bind("pageshow", function() {
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

	/* user */
	base.user = {};
	base.user.id = $('#form_user_id').val();

	/* tracklist */
	base.tracklist = {};
	base.tracklist.all_count  = 0;
	base.tracklist.display_count = 0;
	base.tracklist.offset     = 0;
	base.tracklist.limit      = 10;
	base.tracklist.arr_list = [];
	base.tracklist.ajax_response = [];
	base.tracklist.get_ajax = function(offset, limit) {
		var params = {
			user_id: base.user.id,
			offset : offset,
			limit  : limit,
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
				return true;
			},
			error: function(){
				alert('ネットワークエラー');
				return true;
			}
		});
	};
	base.tracklist.getlist = function(offset, limit) {
		$('#useryou_tracklist_ul').css('opacity', '0');
		base.tracklist.get_ajax(offset, limit).done(function(res) {
			base.tracklist.all_count = base.tracklist.ajax_response.count;
			base.tracklist.display_count = base.tracklist.ajax_response.arr_list.length;
			var html = '';
			jQuery.each(base.tracklist.ajax_response.arr_list, function(i, v) {
				if (v.artist_name === null) {
					v.artist_name = '';
				}
				var list = "<li>";
				list += "<a href='/tracklist/detail/"+ v.id +"/'>";
				list += "<span class='tracklist_title'>"+ v.title +"</span>&nbsp;";
				list += "<span class='tracklist_artist'>"+ v.artist_name +"</span>";
				list += "</a>";
				list += "</li>";
				html += list;
			});
			if (html.length > 0) {
				$('#useryou_tracklist_ul').html(html);
				$('#useryou_tracklist_ul').animate({opacity: 1, speed: 100});
				base.page_default();
				base.tracklist.pagination();
			} else {
				$('#useryou_tracklist_ul li').css('background', 'inherit');
				$('#useryou_tracklist_ul li').css('border', '0px');
				$('#useryou_tracklist_ul').css('opacity', '1');
			}
		});
	};
	base.tracklist.pagination = function() {
		var html = '';
		if (base.tracklist.all_count <= base.tracklist.limit) {
			return true;
		}
		// ページャー表示件数 << 3,4,5,6,7 >>
		var page_display_count = 5;
		// 最後になる予定のページ番号
		var page_number_last = Math.ceil(base.tracklist.all_count/base.tracklist.limit);
		// 最初になる予定のページ番号
		var page_number_min  = 1;
		// 実際の最後のページ番号
		var page_number_max  = page_number_last;
		// カレントページ番号
		var page_number_current = Math.ceil(base.tracklist.offset/base.tracklist.limit) + 1;

		if (page_number_last > page_display_count) {
			if (Math.floor(page_display_count/2) < page_number_current) {
				page_number_min = page_number_current - Math.floor(page_display_count/2);
			}
			if ((page_number_min + page_display_count) - 1 < page_number_last) {
				page_number_max = (page_number_min + page_display_count) - 1;
			}
		}
		if ((page_number_min + page_display_count) - 1 < page_number_last) {
			page_number_max = (page_number_min + page_display_count) - 1;
		}
		if (page_number_min > 1) {
			html = '<span><a href="javascript:void(0)" class="pager_disp" id="tracklist_page_0"> << </a></span> ';
		}
		var offset = 0;
		for (var i=page_number_min; i<=page_number_max; i++) {
			offset = (i - 1) * base.tracklist.limit;
			if (i === page_number_current) {
				html += "<span class='active'><a href='javascript:void(0)' class='pager_disp' id='tracklist_page_" + offset +"'>"+ i +"</a></span> ";
			} else {
				html += "<span><a href='javascript:void(0)' class='pager_disp' id='tracklist_page_" + offset +"'>"+ i +"</a></span> ";
			}
		}
		if (page_number_last > page_number_max) {
			var last_offset = (page_number_last - 1) * base.tracklist.limit;
			html += '<span><a href="javascript:void(0)" class="pager_disp" id="tracklist_page_' + last_offset +'"> >> </a></span>';
		}
		// 件数
		if (base.tracklist.all_count > 0) {
			var list_count = '<span class="list_count">全' + base.tracklist.all_count + '件</span>';
			$('#tracklist_count').html(list_count);
			// fromTo表示
			var pagination_tracklist_disp_from = ((page_number_current - 1) * base.tracklist.limit) + 1;
			var pagination_tracklist_disp_to   = (pagination_tracklist_disp_from + base.tracklist.display_count) - 1;
			$('#tracklist_fromto').html('[' + pagination_tracklist_disp_from + '-' + pagination_tracklist_disp_to + ']');
		}
		html = '<span class="pagination_render">' + html + '</span>';
		$('#tracklist_pagination_div_1').html(html);
		$('#tracklist_pagination_div_1 .pagination_render span').off('click');
		$('#tracklist_pagination_div_1 .pagination_render span').on('click', function() {
			$('#tracklist_pagination_div_1 .pagination_render span').removeClass('active');
			$('#tracklist_pagination_div_1 .pagination_render span').eq($(this).index()).addClass('active');
			base.tracklist.offset = parseInt($(this).children('.pager_disp').attr('id').match(/[0-9]+$/), 10);
			base.tracklist.getlist(base.tracklist.offset, base.tracklist.limit);
		});
		$('#tracklist_pagination_div_2').html(html);
		$('#tracklist_pagination_div_2 .pagination_render span').off('click');
		$('#tracklist_pagination_div_2 .pagination_render span').on('click', function() {
			$('#tracklist_pagination_div_2 .pagination_render span').removeClass('active');
			$('#tracklist_pagination_div_2 .pagination_render span').eq($(this).index()).addClass('active');
			base.tracklist.offset = parseInt($(this).children('.pager_disp').attr('id').match(/[0-9]+$/), 10);
			base.tracklist.getlist(base.tracklist.offset, base.tracklist.limit);
		});

	};
	// トラックリスト取得ajax実行
	base.tracklist.getlist(base.tracklist.offset, base.tracklist.limit);


	/* review list*/
	base.reviewlist = {};
	base.reviewlist.all_count = 0;
	base.reviewlist.offset = 0;
	base.reviewlist.limit = 5;
	base.reviewlist.display_count = 0;
	base.reviewlist.arr_list = [];
	base.reviewlist.ajax_response = [];
	base.reviewlist.get_ajax = function(offset, limit) {
		var params = {
				user_id: base.user.id,
				offset : offset,
				limit  : limit,
			};
			return $.ajax({
				type: 'post',
				url: '/api/review/getlist.json',
				datatype: 'json',
				data: JSON.stringify(params),
				contentType: 'application/json',
				cache: false,
				success: function(res, ans) {
					base.reviewlist.ajax_response = res.result;
					return true;
				},
				error: function(){
					alert('ネットワークエラー');
					return true;
				}
			});
	};
	base.reviewlist.getlist = function(offset, limit) {
		$('#useryou_reviewlist_ul').css('opacity', '0');
		base.reviewlist.get_ajax(offset, limit).done(function(res) {
			base.reviewlist.display_count = base.reviewlist.ajax_response.arr_list.length;
			base.reviewlist.all_count = base.reviewlist.ajax_response.count;
			var html = '';
			var client_user_id = $('#form_client_user_id').val();
			var unread_comment = 0;
			jQuery.each(base.reviewlist.ajax_response.arr_list, function(i, v) {
				unread_comment = v.comment_count - v.was_read_comment_count;
				if (v.artist_name === null) {
					v.artist_name = '';
				}
				html += '<li class="review_list_li reviewlist_list_li ui-li-static ui-body-inherit">';
				html += '<div class="review_list_image"><img src="'+ v.artist_image +'" data-original="'+ v.artist_image +'"></div>';
				html += '<div class="about">'+ v.about +'</div>';
				if (client_user_id == base.user.id) {
					if (unread_comment > 0) {
						html += '<span class="unread_comment">新着コメントが'+ unread_comment +'件あります</span>';
					}
				}
				html += '<div class="about_name">';
				html += v.about_name;
				if (v.about !== 'artist') {
					html += '<div>by<span class="review_list_artist_name">'+ v.artist_name +'</span></div>';
				}
				html += '</div>';
				html += '<div class="star">';
				for (var i=0; i<v.star; i++) {
					html += '★';
				}
				html += '</div>';
				html += '<div class="review">';
				html += v.review;
				html += '<span class="created_at">'+ v.created_at +'</span>';
				html += '<span class="review_id">'+ v.id +'</span>';
				html += '</div>';
				html += '</li>';
			}); // endEach
			if (html.length > 0) {
				$('#useryou_reviewlist_ul').html(html);
				$('#useryou_reviewlist_ul').animate({opacity: 1, speed: 100});
				base.page_default();
				base.reviewlist.list_hover();
				base.reviewlist.pagination();
			} else {
				$('#useryou_reviewlist_ul li').css('background', 'inherit');
				$('#useryou_reviewlist_ul li').css('border', '0px');
				$('#useryou_reviewlist_ul').css('opacity', '1');
			}			
		});
	};
	base.reviewlist.pagination = function() {
		var html = '';
		if (base.reviewlist.all_count <= base.reviewlist.limit) {
			return true;
		}
		// ページャー表示件数 << 3,4,5,6,7 >>
		var page_display_count = 5;
		// 最後になる予定のページ番号
		var page_number_last = Math.ceil(base.reviewlist.all_count/base.reviewlist.limit);
		// 最初になる予定のページ番号
		var page_number_min  = 1;
		// 実際の最後のページ番号
		var page_number_max  = page_number_last;
		// カレントページ番号
		var page_number_current = Math.floor(base.reviewlist.offset/base.reviewlist.limit) + 1;

		if (page_number_last > page_display_count) {
			if (Math.floor(page_display_count/2) < page_number_current) {
				page_number_min = page_number_current - Math.floor(page_display_count/2);
			}
			if ((page_number_min + page_display_count) - 1 < page_number_last) {
				page_number_max = (page_number_min + page_display_count) - 1;
			}
		}
		if ((page_number_min + page_display_count) - 1 < page_number_last) {
			page_number_max = (page_number_min + page_display_count) - 1;
		}
		if (page_number_min > 1) {
			html = '<span><a href="javascript:void(0)" class="pager_disp" id="reviewlist_page_0"> << </a></span> ';
		}

		var offset = 0;
		for (var i=page_number_min; i<=page_number_max; i++) {
			offset = (i - 1) * base.reviewlist.limit;
			if (i === page_number_current) {
				html += "<span class='active'><a href='javascript:void(0)' class='pager_disp' id='reviewlist_page_" + offset +"'>"+ i +"</a></span> ";
			} else {
				html += "<span><a href='javascript:void(0)' class='pager_disp' id='reviewlist_page_" + offset +"'>"+ i +"</a></span> ";
			}
		}
		if (page_number_last > page_number_max) {
			var last_offset = (page_number_last - 1) * base.reviewlist.limit;
			html += '<span><a href="javascript:void(0)" class="pager_disp" id="reviewlist_page_' + last_offset +'"> >> </a></span>';
		}
		// 件数
		if (base.reviewlist.all_count > 0) {
			var list_count = '<span class="list_count">全' + base.reviewlist.all_count + '件</span>';
			$('#reviewlist_count').html(list_count);
			// fromTo表示
			var pagination_reviewlist_disp_from = ((page_number_current - 1) * base.reviewlist.limit) + 1;
			var pagination_reviewlist_disp_to   = (pagination_reviewlist_disp_from + base.reviewlist.display_count) - 1;
			$('#reviewlist_fromto').html('[' + pagination_reviewlist_disp_from + '-' + pagination_reviewlist_disp_to + ']');
		}
		
		// リスト
		html = '<span class="pagination_render">' + html + '</span>';
		$('#reviewlist_pagination_div_1').html(html);
		$('#reviewlist_pagination_div_1 .pagination_render span').off('click');
		$('#reviewlist_pagination_div_1 .pagination_render span').on('click', function() {
			$('#reviewlist_pagination_div_1 .pagination_render span').removeClass('active');
			$('#reviewlist_pagination_div_1 .pagination_render span').eq($(this).index()).addClass('active');
			base.reviewlist.offset = parseInt($(this).children('.pager_disp').attr('id').match(/[0-9]+$/), 10);
			base.reviewlist.getlist(base.reviewlist.offset, base.reviewlist.limit);
		});
		$('#reviewlist_pagination_div_2').html(html);
		$('#reviewlist_pagination_div_2 .pagination_render span').off('click');
		$('#reviewlist_pagination_div_2 .pagination_render span').on('click', function() {
			$('#reviewlist_pagination_div_2 .pagination_render span').removeClass('active');
			$('#reviewlist_pagination_div_2 .pagination_render span').eq($(this).index()).addClass('active');
			base.reviewlist.offset = parseInt($(this).children('.pager_disp').attr('id').match(/[0-9]+$/), 10);
			base.reviewlist.getlist(base.reviewlist.offset, base.reviewlist.limit);
		});

	};
	base.reviewlist.list_hover = function() {
		$('.review_list_li').hover(
				function() {
					$(this).css('background', 'rgba(100,100,100,0.2)');
					$(this).css('cursor', 'pointer');
				},
				function() {
					$(this).css('background', 'inherit');
					$(this).next('.review_list_tr_user').css('background', 'inherit');
				}
			);
		$('.review_list_li').on('click',
				function() {
					var index = $(this).index('.review_list_li');
					var about = $('.about').eq(index).html();
					var review_id = $('.review_id').eq(index).html();
					location.href = '/review/music/detail/' + about + '/' + review_id + '/';
				}
			);
	};
	// レビュー取得ajax実行
	base.reviewlist.getlist(base.reviewlist.offset, base.reviewlist.limit);


	$('.favorite_user_div_list').hover(
			function() {
				$(this).children('a').children('.favorite_user_div_list_img').css('filter', 'brightness(150%)');
				$(this).children('a').children('.favorite_user_div_list_img').css('-webkit-filter', 'brightness(150%)');
				$(this).children('a').children('.favorite_user_div_list_img').css('-moz-filter', 'brightness(150%)');
				$(this).children('a').children('.favorite_user_div_list_img').css('-o-filter', 'brightness(150%)');
				$(this).children('a').children('.favorite_user_div_list_img').css('-ms-filter', 'brightness(150%)');
			},
			function() {
				$(this).children('a').children('.favorite_user_div_list_img').css('filter', 'brightness(70%)');
				$(this).children('a').children('.favorite_user_div_list_img').css('-webkit-filter', 'brightness(70%)');
				$(this).children('a').children('.favorite_user_div_list_img').css('-moz-filter', 'brightness(70%)');
				$(this).children('a').children('.favorite_user_div_list_img').css('-o-filter', 'brightness(70%)');
				$(this).children('a').children('.favorite_user_div_list_img').css('-ms-filter', 'brightness(70%)');
			}
		);

		$('.favorite_user_div_list').on('touchstart', function() {
			$(this).children('a').children('.favorite_user_div_list_img').css('filter', 'brightness(150%)');
			$(this).children('a').children('.favorite_user_div_list_img').css('-webkit-filter', 'brightness(150%)');
			$(this).children('a').children('.favorite_user_div_list_img').css('-moz-filter', 'brightness(150%)');
			$(this).children('a').children('.favorite_user_div_list_img').css('-o-filter', 'brightness(150%)');
			$(this).children('a').children('.favorite_user_div_list_img').css('-ms-filter', 'brightness(150%)');
		});


	$('.thanks_div_list').hover(
			function() {
				$(this).children('a').children('.thanks_div_list_img').css('filter', 'brightness(150%)');
				$(this).children('a').children('.thanks_div_list_img').css('-webkit-filter', 'brightness(150%)');
				$(this).children('a').children('.thanks_div_list_img').css('-moz-filter', 'brightness(150%)');
				$(this).children('a').children('.thanks_div_list_img').css('-o-filter', 'brightness(150%)');
				$(this).children('a').children('.thanks_div_list_img').css('-ms-filter', 'brightness(150%)');
			},
			function() {
				$(this).children('a').children('.thanks_div_list_img').css('filter', 'brightness(70%)');
				$(this).children('a').children('.thanks_div_list_img').css('-webkit-filter', 'brightness(70%)');
				$(this).children('a').children('.thanks_div_list_img').css('-moz-filter', 'brightness(70%)');
				$(this).children('a').children('.thanks_div_list_img').css('-o-filter', 'brightness(70%)');
				$(this).children('a').children('.thanks_div_list_img').css('-ms-filter', 'brightness(70%)');
			}
		);

		$('.thanks_div_list').on('touchstart', function() {
			$(this).children('a').children('.thanks_div_list_img').css('filter', 'brightness(150%)');
			$(this).children('a').children('.thanks_div_list_img').css('-webkit-filter', 'brightness(150%)');
			$(this).children('a').children('.thanks_div_list_img').css('-moz-filter', 'brightness(150%)');
			$(this).children('a').children('.thanks_div_list_img').css('-o-filter', 'brightness(150%)');
			$(this).children('a').children('.thanks_div_list_img').css('-ms-filter', 'brightness(150%)');
		});

	$('.cools_div_list').hover(
			function() {
				$(this).children('a').children('.cools_div_list_img').css('filter', 'brightness(150%)');
				$(this).children('a').children('.cools_div_list_img').css('-webkit-filter', 'brightness(150%)');
				$(this).children('a').children('.cools_div_list_img').css('-moz-filter', 'brightness(150%)');
				$(this).children('a').children('.cools_div_list_img').css('-o-filter', 'brightness(150%)');
				$(this).children('a').children('.cools_div_list_img').css('-ms-filter', 'brightness(150%)');
			},
			function() {
				$(this).children('a').children('.cools_div_list_img').css('filter', 'brightness(70%)');
				$(this).children('a').children('.cools_div_list_img').css('-webkit-filter', 'brightness(70%)');
				$(this).children('a').children('.cools_div_list_img').css('-moz-filter', 'brightness(70%)');
				$(this).children('a').children('.cools_div_list_img').css('-o-filter', 'brightness(70%)');
				$(this).children('a').children('.cools_div_list_img').css('-ms-filter', 'brightness(70%)');
			}
		);

		$('.cools_div_list').on('touchstart', function() {
			$(this).children('a').children('.cools_div_list_img').css('filter', 'brightness(150%)');
			$(this).children('a').children('.cools_div_list_img').css('-webkit-filter', 'brightness(150%)');
			$(this).children('a').children('.cools_div_list_img').css('-moz-filter', 'brightness(150%)');
			$(this).children('a').children('.cools_div_list_img').css('-o-filter', 'brightness(150%)');
			$(this).children('a').children('.cools_div_list_img').css('-ms-filter', 'brightness(150%)');
		});

	$('#user_information_comment_link').on('click', function() {
		var review_offset = $('#review_id').offset().top;
		$('html,body').animate({scrollTop: review_offset - 60}, 450);
	});

	$('#form_favorite_status').on('change', function(){
		if ($('#form_client_user_id').val().length == 0){
			return false;
		}
		var params = {
			client_user_id: $('#form_client_user_id').val(),
			favorite_user_id: $('#form_user_id').val(),
			status: $(this).val(),
		};
		$.ajax({
			type: 'post',
			url: '/api/favorite/set.json',
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

	$('#favorite_status_disabled_anchor').on('click', function(){
			$(this).off('click');
			alert('こちらの機能はログイン後に使用することができます');
			return false;
	});

});
