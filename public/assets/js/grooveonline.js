jQuery(function(){
	$(".lazy img").lazyload({
		effect: "fadeIn" ,
		effect_speed: 300 ,
	});
	$('img').error(function() {
		$(this).attr('src', '/assets/img/default.jpg');
	});
});

// ローディング画面
var loading = {};
loading.display = function () {
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

var pagination = {};
pagination.params = {};
// 全件数
pagination.params.count  = 0;
// 表示件数
pagination.params.display_count = 0;
pagination.params.display_from = '';
pagination.params.display_to = '';
// 表示データのオフセット値
pagination.params.offset = 0;
// ページ内表示件数
pagination.params.limit  = 10;
// ページャー表示件数 << 3,4,5,6,7 >>
pagination.params.page_display_count = 5;


// コールバック関数
pagination.callback = function(offset, limit) {};

pagination.first_disp_exec_flg = false;

pagination.init = function(params, callback) {
	jQuery.each(params, function(i,v) {
		pagination.params[i]= v;
	});
	pagination.callback = callback;
};

/**
 * @params params.offset  // オフセット値 ex)150であれば150レコード目からlimit件取得することになる
 * @params params.limit   // ページ内表示件数
 * @params params.count   // 全件数
 * @callback ページャーをクリック後に実行する関数（この関数内で再度pagination.setEventListner()を呼び出すこと）
 */
pagination.setEventListner = function(params, callback) {
	pagination.init(params, callback);
	// 初回表示時はコールバック関数をそのまま実行（コールバックの中で自身が再度呼ばれる。初回実行フラグはtrueへ更新）
	if (pagination.first_disp_exec_flg === false) {
		var ans = pagination.callback(pagination.params.offset, pagination.params.limit);
		pagination.first_disp_exec_flg = true;
	}
	var html = '';
	if (pagination.params.count > pagination.params.limit) {
		// 最後になる予定のページ番号
		var page_number_last    = Math.ceil(pagination.params.count/pagination.params.limit);
		// 最初になる予定のページ番号
		var page_number_min     = 1;
		// 実際の最後のページ番号
		var page_number_max     = page_number_last;
		// カレントページ番号
		var page_number_current = Math.floor(pagination.params.offset/pagination.params.limit) + 1;
		if (page_number_last > pagination.params.page_display_count) {
			if (Math.floor(pagination.params.page_display_count/2) < page_number_current) {
				page_number_min = page_number_current - Math.floor(pagination.params.page_display_count/2);
			}
			if ((page_number_min + pagination.params.page_display_count) - 1 < page_number_last) {
				page_number_max = (page_number_min + pagination.params.page_display_count) - 1;
			}
		}
		var page_id = "page_number_";
		if ('pagination_id' in params) {
			page_id = params.pagination_id + '_number_';
		}
		if (page_number_min > 1) {
			html = '<span><a href="javascript:void(0)" class="pager_disp" id="' + page_id + '0"> << </a></span> ';
		}
		var offset = 0;
		for (var i=page_number_min; i<=page_number_max; i++) {
			offset = (i - 1) * pagination.params.limit;
			if (i === page_number_current) {
				html += "<span class='active'><a href='javascript:void(0)' class='pager_disp' id='" + page_id + offset +"'>"+ i +"</a></span> ";
			} else {
				html += "<span><a href='javascript:void(0)' class='pager_disp' id='" + page_id + offset +"'>"+ i +"</a></span> ";
			}
		}
		if (page_number_last > page_number_max) {
			var last_offset = (page_number_last - 1) * pagination.params.limit;
			html += '<span><a href="javascript:void(0)" class="pager_disp" id="' + page_id + last_offset +'"> >> </a></span>';
		}
		html = '<span class="pagination_render">' + html + '</span>';
	}
	// 件数表示
	$('.pagination_all_count').html(pagination.params.count);
	// fromTo表示
	pagination.params.disp_from = ((page_number_current - 1) * pagination.params.limit) + 1;
	pagination.params.disp_to   = (pagination.params.disp_from + pagination.params.display_count) - 1;
	$('.pagination_fromto').html('[' + pagination.params.disp_from + '-' + pagination.params.disp_to + ']');
	
	// ページャ表示
	if ('pagination_id' in params) {
		$('#' + params.pagination_id).html(html);
	} else {
		$('.pagination_div').eq(0).html(html);
		$('.pagination_div').eq(1).html(html);
	}
	
	$('.pagination_render span').off('click');
	$('.pagination_render span').on('click', function() {
		$('.pagination_render span').removeClass('active');
		$('.pagination_div:eq(0) .pagination_render span').eq($(this).index()).addClass('active');
		$('.pagination_div:eq(1) .pagination_render span').eq($(this).index()).addClass('active');
		pagination.params.offset = parseInt($(this).children('.pager_disp').attr('id').match(/[0-9]+$/), 10);
		pagination.callback(pagination.params.offset, pagination.params.limit);
	});
};