jQuery(function() {
	$(window).on('load resize', function(){
		var w = $(window).width() * 1.04;
		var fontsize = w / 7.4;
		$('.main_title').css('font-size', fontsize + 'px');
		$('.main_title').css('letter-spacing', fontsize/31 + 'px');
	});

	// レビュー一覧
	$('.review_list_tbody').hover(
		function() {
			$(this).css('background', 'rgba(100,100,100,0.2)');
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
});
