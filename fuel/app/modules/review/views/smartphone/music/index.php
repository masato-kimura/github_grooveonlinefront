<?php echo Asset::js('smartphone/review/music/index.js'); ?>
<?php echo Asset::css('smartphone/review/music/index.css'); ?>

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
	<div class="go_review"><?php echo Html::anchor("/artist/search/", 'レビューをかいてみませんか？', array('rel' => 'external')); ?></div>

	<article id="reviewlist_review_section">
		<h3 class="introduction">REVIEW</h3>
		<div class="list_count_area">
			<span id="reviewlist_count">全<span id="review_list_all_count" class="pagination_all_count"></span>件
				<span class="pagination_fromto"></span>
			</span>
		</div>
		<div id="reviewlist_pagination_div" class="pagination_div"></div>
		<h3 class="review_list_title">最新レビュー投稿</h3>
		<ul data-role="listview" class="review_list_ul" id="reviewlist_list_ul">
			<li>レビューはありません。</li>
		</ul>
		<div id="reviewlist_pagination_div_footer" class="pagination_div"></div>
	</article>

	<br />

	<div class="go_review"><?php echo Html::anchor("/artist/search/review/?from=artist/search/review/", 'レビューをかいてみませんか？', array('rel' => 'external')); ?></div>

	<br />

	<div class="oauth_main_div">
		<div class="fb-like oauth_div" data-action="like" data-colorscheme="dark" data-layout="button_count" data-share="true" data-show-faces="true"></div>
		<div class="oauth_div">
			<a href="https://twitter.com/share" class="twitter-share-button" data-hashtags="groove-online">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>	</div>
		<div class="oauth_div">
			<span>
			<script type="text/javascript" src="//media.line.me/js/line-button.js?v=20140411" ></script>
			<script type="text/javascript">
			new media_line_me.LineButton({"pc":false,"lang":"ja","type":"a"});
			</script>
			</span>
		</div>
	</div>

	<div class="qr_div">
		<span class="qr_description">読み取るとこのページが表示されます</span>
		<br />
		<img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>" data-original="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>">
	</div>

<?php echo Form::hidden('artist_id', $artist_id, array('id' => 'artist_id'));?>
<?php echo Form::hidden('page_offset', $page_offset, array('id' => 'page_offset'));?>

</div>