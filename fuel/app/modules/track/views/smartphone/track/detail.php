<?php echo Asset::css('smartphone/track/detail.css'); ?>
<?php echo Asset::js('smartphone/track/detail.js');?>
<?php if ( ! \Agent::is_smartphone()):?>
<style>
.main_div {
  margin-top: 10px;
  padding: 10px 35px;
}
</style>
<?php endif;?>
<nav class="main_navi">
	<span class="main_navi_title">アーティスト</span>
	<span class="main_navi_ans">[<?php echo Html::anchor("/artist/detail/{$this->artist_id}/", $artist_name); ?>]</span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title">トラック</span>
</nav>

<div style="text-align: right; margin: 10px auto; max-width: 894px;">
	<a href="<?php echo \Config::get('amazon.url');?>&tag=<?php echo \Config::get('amazon.tracking_id');?>&keywords=<?php echo $artist_name. ' '. $track_name;?>" target="_new"><?php echo Asset::img('review/assocbtn_gray_amazon1._V288606497_.png', array('style' => 'width: 112px; height: 40px;'));?></a>
	<?php if ( ! empty($track_mbid_itunes)):?>
		&nbsp;<a href="https://geo.itunes.apple.com/jp/album/<?php echo $track_itunes_segment_name;?>/id<?php echo $track_mbid_itunes;?>?at=<?php echo \Config::get('itunes.affiliate_id');?>&app=itunes" target="new_win" style="display:inline-block;overflow:hidden;background:url(http://linkmaker.itunes.apple.com/images/badges/en-us/badge_itunes-lrg.svg) no-repeat;width:113px;height:40px;"></a>
	<?php endif;?>
</div>

<div class="main_div">

	<div class="go_review">
		<?php echo Html::anchor("/review/music/write/{$this->artist_id}/", 'レビューをかいてみませんか？', array('rel' => 'external')); ?>
	</div>

	<table id="track_detail_artist_name">
		<tr class="about_detail">
			<td class="about_detail_img">
				<img src="<?php echo $track_image; ?>">
			</td>
			<td class="about_detail_info">
				<span class="about_detail_info_name">
					<?php echo $track_name; ?>
				</span>
				<br />
					by&nbsp;<span class="about_detail_info_artist"><strong><?php echo Html::anchor("/artist/detail/{$artist_id}/", $artist_name);?></strong></span>
				<br />
				<?php echo $track_release_itunes;?>
			</td>
		</tr>
	</table>

	<div id="all_play_div">
		<div id="current_div">
			<div id="current_track"></div>
			<div id="current_time"></div>
		</div>
	</div>

	<ul id="track_detail_list" data-role="listview">
		<li class="track_detail_name">
			<?php if ( ! empty($this->track_preview_itunes)):?>
				<span class="preview_button">▶️</span>
				<audio class="preview_itunes">
					<source src="<?php echo $this->track_preview_itunes;?>">
					<?php echo Html::anchor($this->track_preview_itunes, '▶️', array('target' => 'new_win'));?>
				</audio>
			<?php endif;?>
			<span class="track_name"><?php echo $this->track_name;?></span>
		</li>
	</ul>

	<br />
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

	<div id="about_detail_review_div">
		<nav class="review_navi">
			<span class="review_navi_title"><?php echo Html::anchor("/review/music/", "レビュー"); ?></span>
			<span class="review_navi_child">></span>
			<span class="review_navi_ans"><?php echo $this->artist_name; ?></span>
		</nav>
		<h3 class="review_title">このトラックの最新レビュー</h3>

		<?php if ($this->pagination->total_pages > 1): ?>
			<div class="pagination_div">
				<span class="pagination_previous">
				<?php if ($this->pagination->calculated_page > 1): ?>
					<?php echo $this->pagination->previous(); ?>
				<?php endif;?>
				</span>
				<span class="pagination_render">
				<?php echo $this->pagination->pages_render(); ?>
				</span>
				<span class="pagination_next">
				<?php if ($this->pagination->calculated_page != $this->pagination->total_pages): ?>
					<?php echo $this->pagination->next(); ?>
				<?php endif;?>
				</span>
			</div>
		<?php endif; ?>


		<table class="review_list_table">
			<?php if (empty($arr_review_list)):?>
				<tr>
					<td style="text-align: center;">
						<br />
						<span>このトラックのレビューはまだありません。</span><br />
						<div class=""><?php echo Html::anchor("/review/music/write/{$this->artist_id}/", 'レビューをかいてみませんか？', array('rel' => 'external')); ?></div>
					</td>

				</tr>
			<?php endif;?>

			<?php foreach ($arr_review_list as $i => $arr_review_list_detail): ?>
				<?php
					$id          = $arr_review_list_detail->id;
					$about       = $arr_review_list_detail->about;
					$artist_id   = isset($arr_review_list_detail->artist_id)? $arr_review_list_detail->artist_id: null;
					$album_id    = isset($arr_review_list_detail->about_id)? $arr_review_list_detail->about_id: null;
					$track_id    = isset($arr_review_list_detail->about_id)? $arr_review_list_detail->about_id: null;
					$artist_image= ( ! empty($arr_review_list_detail->image_extralarge))? '<img src="'. $arr_review_list_detail->image_extralarge. '">': Asset::img('/profile/user/default/default.jpg');
					$user_id     = $arr_review_list_detail->user_id;
					$user_name   = $arr_review_list_detail->user_name;
					$user_image  = $arr_review_list_detail->user_image_medium;
					$review_id   = $arr_review_list_detail->id;
					$review      = $arr_review_list_detail->review;
					$star        = $arr_review_list_detail->star;
					$created_at  = $arr_review_list_detail->created_at;
					$updated_at  = isset($arr_review_list_detail->updated_at)? $arr_review_list_detail->updated_at: null;
					$about_name = isset($arr_review_list_detail->about_name)? mb_strimwidth($arr_review_list_detail->about_name, 0, 32, '...'): null;
				?>
			<tr class="review_list_tr">
				<td class="review_detail">
					<span class="about"><?php echo $about; ?></span>
					<span class="about_name"><?php echo $about_name;?> / <?php echo $artist_name?></span>
					<div class="star">
						<?php for($i=0; $i<$star; $i++): ?>★<?php endfor;?>
					</div>
					<div class="review">
						<?php echo mb_strimwidth($review, 0, 200, '...');?>
					</div>
					<div class="updated_at">
						<span><?php echo preg_replace('/:[\d]*$/', '', $updated_at); ?>	</span>
					</div>
					<span class="review_id"><?php echo $review_id;?></span>
				</td>
			</tr>
			<tr class="review_list_tr_user">
				<td class="review_list_bottom_td" colspan="2">
					<div class="user">
						<span><?php echo Html::anchor('/user/you/'. $user_id, mb_strimwidth($user_name, 0, 35, '...'));?></span>
						<span><?php echo Html::anchor('/user/you/'. $user_id, Html::img($user_image, array('alt' => '')));?></span>
					</div>
				</td>
			</tr>
			<?php endforeach;?>
		</table>

		<?php if ($this->pagination->total_pages > 1): ?>
			<div class="pagination_div">
				<span class="pagination_previous">
				<?php if ($this->pagination->calculated_page > 1): ?>
					<?php echo $this->pagination->previous(); ?>
				<?php endif;?>
				</span>
				<span class="pagination_render">
				<?php echo $this->pagination->pages_render(); ?>
				</span>
				<span class="pagination_next">
				<?php if ($this->pagination->calculated_page != $this->pagination->total_pages): ?>
					<?php echo $this->pagination->next(); ?>
				<?php endif;?>
				</span>
			</div>
		<?php endif; ?>
	</div>

	<br />
	<br />
	<br />
	<div class="qr_div">
		<span class="qr_description">読み取るとこのページが表示されます。</span>
		<br />
		<img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>">
	</div>

</div>


