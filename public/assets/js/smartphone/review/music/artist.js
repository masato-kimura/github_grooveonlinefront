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

	// アーティストオブジェクト
	base.artist = {};
	base.artist.id = $('#artist_id').val();

	// レビューリストオブジェクト
	base.reviewlist = {};
	base.reviewlist.all_count = parseInt($('#review_list_all_count').html(), 10);
	base.reviewlist.ajax_response = {};
	base.reviewlist.get_ajax = function(offset, limit) {
		var params = {
			offset    : offset,
			limit     : limit,
			artist_id : base.artist.id,
			user_id   : '',
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
				return res;
			},
			error: function() {
				alert('network error');
				return true;
			}
		});
	};

	base.reviewlist.getlist = function(offset, limit) {
		$('#reviewlist_list_ul').css('opacity', '0');
		base.reviewlist.get_ajax(offset, limit).done(function(res) {
			base.reviewlist.all_count = base.reviewlist.ajax_response.count;
			var html = '';
			jQuery.each(base.reviewlist.ajax_response.arr_list, function(i, v) {
				if (v.artist_name === null) {
					v.artist_name = '';
				}
				html += '<li class="review_list_li reviewlist_list_li ui-li-static ui-body-inherit">';
				html += '<div class="review_list_image"><img src="'+ v.artist_image +'" data-original="'+ v.artist_image +'"></div>';
				html += '<div class="about">'+ v.about +'</div>';
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
				html += '</div>';
				html += '<span class="review_id">'+ v.id +'</span>';
				html += '<div class="user">';
				html += '<span><a href="/user/you/'+ v.user_id +'/">'+ v.user_name +'</a></span>';
				html += '<span><a href="/user/you/'+ v.user_id +'/"> <img src="'+ v.user_image +'" data-original="'+ v.user_image +'"></a></span>';
				html += '</div>';
				html += '</li>';
			}); // endEach

			$('#reviewlist_list_ul').html(html);
			$('#reviewlist_list_ul').animate({opacity: 1, speed: 100});
			base.page_default();
			base.reviewlist.list_hover();
			var params = {
					count: base.reviewlist.all_count,
					offset: offset,
					limit: limit
				};
			pagination.setEventListner(params, base.reviewlist.getlist);
		});
	};

	base.reviewlist.list_hover = function() {
		$('.review_list_li').hover(
				function() {
					$(this).css('background', 'rgba(100,100,100,0.2)');
					$(this).css('cursor', 'pointer');
					$(this).next('.review_list_li_user').css('background', 'rgba(100,100,100,0.2)');
				},
				function() {
					$(this).css('background', 'inherit');
					$(this).next('.review_list_li_user').css('background', 'inherit');
				}
			);
		$('.review_list_li').on('click',
				function() {
					var index     = $(this).index('.review_list_li');
					var about     = $('.about').eq(index).html();
					var review_id = $('.review_id').eq(index).html();
					location.href = '/review/music/detail/' + about + '/' + review_id + '/';
				}
			);
		$('.review_list_li_user').on('click',
				function() {
					var index     = $(this).index('.review_list_li_user');
					var about     = $('.about').eq(index).html();
					var review_id = $('.review_id').eq(index).html();
					location.href = '/review/music/detail/' + about + '/' + review_id + '/';
				}
			);
	};

	base.reviewlist.setEventListner = function() {
		var params = {
			count  : base.reviewlist.all_count,
			limit  : 10,
			offset : $('#page_offset').val(),
		};
		pagination.setEventListner(params, base.reviewlist.getlist);
		base.reviewlist.list_hover();
		return true;
	};

	base.reviewlist.setEventListner();
});
