<?php echo Asset::js('smartphone/review/music/artist.js'); ?>
<?php echo Asset::css('smartphone/review/music/artist.css'); ?>

<?php if ( ! \Agent::is_smartphone()):?>
<style>
.main_div {
  margin-top: 10px;
  padding: 30px 35px;
}
</style>
<?php endif;?>

<nav class="main_navi">
	<span class="main_navi_title"><?php echo Html::anchor('/review/music/', 'レビュー');?></span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title">一覧</span>
</nav>

<div class="main_div">

	<table id="artist_detail_table">
		<tr>
			<td id="artist_detail_name">
				<span class="artist_name"><?php echo $artist_name; ?>・レビュー一覧</span>
				<br />
				<span class="to_artist_page">
					&nbsp;<?php echo Html::anchor('/artist/detail/'. $artist_id. '/' , 'アーティストページへ', array('rel' => 'external')); ?>
				</span>
			</td>
			<td id="artist_detail_img">
				<img src="<?php echo $this->artist_image;?>">
			</td>
		</tr>
	</table>

	<div class="go_review"><?php echo Html::anchor("/review/music/write/". $artist_id. "/", $artist_name. 'について<br />レビューをかいてみませんか？', array('rel' => 'external')); ?></div>

	<article id="reviewlist_review_section">
		<h3 class="introduction">新着レビュー</h3>
		<span>全<span id="review_list_all_count" class="all_count"></span>件</span>

		<div id="reviewlist_pagination_div" class="pagination_div"></div>

		<ul data-role="listview" class="review_list_ul" id="reviewlist_list_ul">
		<?php foreach ($arr_list as $i => $list): ?>
			<li class="review_list_li tracklist_list_li ui-li-static ui-body-inherit">
				<div class="review_list_image"><img src="<?php echo $list->artist_image;?>" data-original="<?php echo $list->artist_image;?>"></div>
				<div class="about"><?php echo $list->about;?></div>
				<div class="about_name">
					<?php echo $list->about_name; ?>
					<?php if ($list->about !== 'artist'):?>
						<div>by<span class="review_list_artist_name"><?php echo $list->artist_name;?></span></div>
					<?php endif;?>
				</div>
				<div class="star">
					<?php for($i=0; $i<$list->star; $i++): ?>★<?php endfor;?>
				</div>
				<div class="review">
					<?php echo $list->review;?>
					<span class="created_at"><?php echo $list->created_at;?></span>
				</div>
				<span class="review_id"><?php echo $list->id;?></span>
				<div class="user">
					<span><?php echo Html::anchor('/user/you/'. $list->user_id, $list->user_name);?></span>
					<span><?php echo Html::anchor('/user/you/'. $list->user_id, '<img src='. $list->user_image. ' data-original='. $list->user_image.'>');?></span>
				</div>
			</li>
		<?php endforeach;?>
		</ul>

		<div id="reviewlist_pagination_div_footer" class="pagination_div"></div>

	</article>

	<div class="go_review"><?php echo Html::anchor("/artist/search/review/?from=artist/search/review/", 'レビューをかいてみませんか？', array('rel' => 'external')); ?></div>

	<br />
	<br />

	<div class="qr_div">
		<span class="qr_description">読み取るとこのページが表示されます</span>
		<br />
		<img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>" data-original="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>">
	</div>

<?php echo Form::hidden('artist_id', $artist_id, array('id' => 'artist_id'));?>
<?php echo Form::hidden('page_offset', $page_offset, array('id' => 'page_offset'));?>

</div>