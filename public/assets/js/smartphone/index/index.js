$(document).bind("pageshow", function() {
	var base = {};
	base.logo = {};
	base.logo.display = function() {
		var w = $('.main_div').width() * 1.15;
		var fontsize = w / 7.5;
		$('.index_main_title').css('letter-spacing', fontsize/25 + 'px');
		$('.index_main_title').css('font-size', fontsize + 'px');
		$('.index_main_title').fadeIn(900);
		// リサイズ時
		$(window).off('resize');
		$(window).on('resize', function() {
			base.logo.display();
		});
	};
	
	// ロゴの表示
	base.logo.display();
	
	// 週間トラックランキング
	$('.weekly_rank_track_list_tbody, .weekly_rank_album_list_tbody').hover(
		function() {
			$(this).css('background', 'rgba(100,100,100,0.3)');
		},
		function() {
			$(this).css('background', 'inherit');
		}
	);
	$('.weekly_rank_track_list_tbody').on('click', function() {
		$(this).css('background', 'inherit');
		var id = $(this).attr('id');
		var track_id = id.match(/[0-9]+$/);
		location.href = 'track/detail/' + track_id +'/';
	});
	
	// トラックリスト一覧
	$('#about_detail_tracklist_div .review_list_li').hover(
			function() {
				$(this).css('background', 'rgba(100,100,100,0.2)');
				$(this).css('cursor', 'pointer');
			},
			function() {
				$(this).css('background', 'inherit');
			}
		);
	$('#about_detail_tracklist_div .review_list_li').on('click',
			function() {
				var tracklist_id = parseInt($(this).attr('id').match(/[0-9]+$/), 10);
				location.href = '/tracklist/detail/' + tracklist_id + '/';
			}
		);

	// レビュー一覧
	$('.review_list_tbody').hover(
		function() {
			$(this).css('background', 'rgba(100,100,100,0.3)');
		},
		function() {
			$(this).css('background', 'inherit');
		}
	);
	$('.review_list_tbody').on('click', function() {
		var id = $(this).attr('id');
		var about = id.match(/([^_]+)_[0-9]+$/)[1];
		var review_id = id.match(/[0-9]+$/);
		location.href = 'review/music/detail/'+ about +'/'+ review_id +'/';
	});

	// 初回ユーザ登録時
	if ($('#index_is_first_regist').val() == true) {
		alert('グルーヴオンラインへようこそ \n\r\n\r ユーザー表示情報は右上のリンクから変更することができます。 \n\r どうぞ当サイトをお楽しみください。');
	}
});
