<?php echo Asset::js('jquery.leanModal.min.js'); ?>
<?php echo Asset::js('smartphone/review/music/detail.js'); ?>
<?php echo Asset::css('smartphone/review/music/detail.css'); ?>
<?php if ( ! \Agent::is_smartphone()):?>
<style>
.main_div {
  padding: 30px 35px;
}
</style>
<?php endif;?>

<nav class="main_navi">
	<span class="main_navi_title"><?php echo Html::anchor('/review/music/', "レビュー一覧"); ?></span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title"><?php echo $about_j_name;?>レビュー[<?php echo Html::anchor('/artist/detail/'. $artist_id. '/', $artist_name);?>]</span>
</nav>

<div style="text-align: right; margin: 10px auto; max-width: 894px;">
	<a href="<?php echo \Config::get('amazon.url');?>&tag=<?php echo \Config::get('amazon.tracking_id');?>&keywords=<?php echo $this->artist_name. ' '. $this->about_name;?>" target="_new"><?php echo Asset::img('review/assocbtn_gray_amazon1._V288606497_.png', array('style' => 'width: 112px; height: 40px;', 'data-original' => 'review/assocbtn_gray_amazon1._V288606497_.png'));?></a>
		&nbsp;
	<?php if ( ! empty($this->mbid_itunes)):?>
		<a href="https://geo.itunes.apple.com/jp/album/<?php echo $this->track_itunes_segment_name;?>/id<?php echo $this->mbid_itunes;?>?at=<?php echo \Config::get('itunes.affiliate_id');?>&app=itunes" target="new_win" style="display:inline-block;overflow:hidden;background:url(http://linkmaker.itunes.apple.com/images/badges/en-us/badge_itunes-lrg.svg) no-repeat;width:112px;height:40px;"></a>
	<?php elseif ( ! empty($this->artist_mbid_itunes)):?>
		<a href="<?php echo \Config::get('itunes.url_geo');?>/artist/<?php echo $artist_segment_name;?>/id<?php echo $artist_mbid_itunes;?>?at=<?php echo \Config::get('itunes.affiliate_id');?>&app=itunes" target="new_win" id="music_write_itunes_link" class="itunes_link" style="display: inline-block;overflow:hidden;background:url(http://linkmaker.itunes.apple.com/images/badges/en-us/badge_itunes-lrg.svg) no-repeat;width:113px;height:40px;margin-right: 10px;"></a>
	<?php endif;?>
</div>

<div class="main_div">
	<div id="about_detail_review_div">
		<div style="float: left; width: 75%;">
			<i style="font-size: x-large; line-height: 25px; display: inline-block;"><?php echo Html::anchor("/user/you/{$user_id}/", $user_name);?></i>&nbsp;さんの<br />
			<span class="about_review"><?php echo $about_j_name; ?>レビュー</span>
			<span style="line-height: 15px; font-size: small; display: block; font-style: italic;">
			<?php echo Html::anchor('/'. $about.'/detail/'. $about_id. '/', $about_name); ?>
			<?php if ($about === 'album' or $about === 'track'): ?>
				/<?php echo Html::anchor('/artist/detail/'. $artist_id. '/', $artist_name);?>
			<?php endif;?>
			</span>
		</div>
		<div style="float: right;">
			<span class="user_name_image"><?php echo Html::anchor("/user/you/{$user_id}/", Html::img($user_image, array('alt' => $user_name)));?></span>
		</div>

		<div class="star" style="clear: both;">
			<?php for($i=0; $i<$star; $i++): ?>★<?php endfor;?>
		</div>
		<div class="review">
			<?php echo $this->review;?>
		</div>

		<div class="at">
			<i class="created"><?php echo preg_replace('/:[\d]*$/', '', $created_at);?>&nbsp;<span>[create]</span></i>
			<?php if ($updated_at):?>
			<br />
			<i><?php echo preg_replace('/:[\d]*$/', '', $updated_at); ?>&nbsp;<span>[modified]</span></i>
			<?php endif;?>
		</div>

		<?php if ($comment_count > 0):?>
			<table id="comment_table">
		<?php else:?>
			<table id="comment_table" style="display:none;">
		<?php endif;?>
			<tr>
				<td colspan="2">
					<?php if ($comment_count > 0):?>
					<div id="comment_count_div"><span id="comment_count_span"><?php echo number_format($comment_count);?></span>件のコメント</div>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<?php if ($comment_count > 1):?>
					<td class="comment_updown_td">
						<span class="comment_pagination_up comment_pagination_span" title="前">▲</span>
						<span class="comment_pagination_down comment_pagination_span" title="次">▼</span>
					</td>
				<?php else:?>
					<td class="comment_updown_td" style="display: none;">
						<span class="comment_pagination_up comment_pagination_span" title="前" style="visibility: hidden;">▲</span>
						<span class="comment_pagination_down comment_pagination_span" title="次" style="visibility: hidden;">▼</span>
					</td>
				<?php endif;?>

				<td class="comment_disp_td">
					<div class="comment_disp" id="comment_disp">

					<?php if ($this->comment_more_flg === true):?>
						<div><a id="comment_more_link">もっと見る</a></div>
					<?php endif;?>

					<?php foreach ($arr_comment_list as $i => $val):?>
						<?php if ($user_id === $val->comment_user_id):?>
							<div id="user_comment_<?php echo $val->comment_id;?>" class="user_comment_div_me user_comment_div_pub">
								<div class="user_comment_text_name_me">
									<span class="user_comment_datetime_me"><?php echo $val->comment_datetime;?></span>
									<span class="user_comment_user_name_me"><a href="/user/you/<?php echo $val->comment_user_id;?>/"><?php echo $val->user_name;?></a></span>
									<div class="user_comment_text_me">
										<?php echo $val->comment;?>
										<?php if ($user_me_id === $val->comment_user_id):?>
										<a class="comment_delete_link_me">削除</a>
										<?php endif;?>
									</div>
								</div>
								<div class="user_comment_img_me">
									<div><a href="/user/you/<?php echo $val->comment_user_id;?>"><img src="<?php echo $val->user_image_medium;?>"></a></div>
								</div>
							</div>
							<hr class="user_comment_hr" id="user_comment_hr_<?php echo $val->comment_id;?>">
						<?php else:?>
							<div id="user_comment_<?php echo $val->comment_id;?>" class="user_comment_div user_comment_div_pub">
								<div class="user_comment_img">
									<div><a href="/user/you/<?php echo $val->comment_user_id;?>"><img src="<?php echo $val->user_image_medium;?>"></a></div>
								</div>
								<div class="user_comment_text_name">
									<span class="user_comment_user_name"><a href="/user/you/<?php echo $val->comment_user_id;?>/"><?php echo $val->user_name;?></a></span>
									<span class="user_comment_datetime"><?php echo $val->comment_datetime;?></span>
									<div class="user_comment_text">
										<?php echo $val->comment;?>
										<?php if ($user_me_id === $val->comment_user_id):?>
										<a class="comment_delete_link">削除</a>
										<?php endif;?>
									</div>
								</div>
							</div>
							<hr class="user_comment_hr" id="user_comment_hr_<?php echo $val->comment_id;?>">
						<?php endif;?>
					<?php endforeach;?>

					</div>
				</td>
			</tr>
		</table>

		<hr style="clear: both;border: 0px;" />

		<div class="cool_div">
			<span class="cool_btn_span" title="cool!">
			<?php if ($user_id === $user_me_id or $is_cool_done === true):?>
				<a class="cool_btn_disabled">クール！</a>
			<?php else:?>
				<a class="cool_btn">クール！</a>
			<?php endif;?>
			</span>
			<span class="cool_howmany"><?php echo $cool_count;?></span>
			<br />
			<a href="#modaldiv_cool" rel="leanModal">
			<div id="review_music_detail_send_cool_div" rel="leanModal">
			<?php foreach ($arr_cool_users as $i => $val):?>
				<span><img src="<?php echo $val['user_image']; ?>" title="<?php echo $val['user_name'];?>さんがクール！"></span>
			<?php endforeach;?>
			</div>
			</a>
		</div>

		<div class="comment">
		<?php if ( ! empty($user_me_id)):?>
			<span id="comment_label"><?php echo $user_name;?>さんのレビューにコメント</span>
			<?php echo Form::textarea('comment', '', array('id' => 'comment', 'placeholder' => '200文字以内で入力してください（HTMLタグは使用できません）', 'rows' => '80', 'cols' => '100'));?>
			<div id="comment_submit_div">
				<span style="font-size: x-small;"></span>
				<?php echo Form::button('submit', 'コメント送信', array('class' => 'ui-btn ui-btn-inline ui-shadow ui-mini', 'id' => 'comment_submit'));?>
			</div>
		<?php else:?>
			<span id="comment_label"><?php echo $user_name;?>さんのレビューにコメント</span>
			<?php echo Form::textarea('comment', '', array('id' => 'comment', 'placeholder' => 'こちらの機能はログイン後に使用できます。', 'disabled' => 'disabled'));?>
			<div id="comment_submit_div">
				<span style="font-size: x-small;"></span>
				<?php echo Form::button('submit', 'コメント送信', array('class' => 'ui-btn ui-btn-inline ui-shadow ui-mini', 'id' => 'comment_submit', 'disabled' => 'disabled'));?>
			</div>
		<?php endif;?>
		</div>

	</div>

	<br />
	<br />

	<table id="review_music_detail_artist_name">
		<tr class="about_detail">
			<td class="about_detail_img">
				<img src="<?php echo $this->about_image; ?>">
			</td>
			<td class="about_detail_info">
				<span class="about"><?php echo $this->about; ?></span>
				<?php if ($this->about === 'artist'): ?>
					<span class="about_detail_info_name">
						<?php echo Html::anchor("/artist/detail/{$artist_id}/", $about_name); ?>
					</span>
				<?php else:?>
					<span class="about_detail_info_name">
						<?php echo Html::anchor("/{$about}/detail/{$about_id}/", $about_name); ?>
					</span>
				<?php endif;?>

				<br />

				<?php if ($this->about === 'album' or $this->about === 'track'): ?>
					by&nbsp;<span class="about_detail_info_artist"><strong><?php echo Html::anchor("/artist/detail/{$this->artist_id}/", $this->artist_name);?></strong></span>
					<br />
				<?php endif;?>
				<?php if ($this->album_release):?>
					<?php echo $this->album_release;?>
					<br />
					<span id="about_detail_info_copyright"><?php echo $this->copyright?></span>
				<?php endif;?>
			</td>
		</tr>
	</table>

	<?php echo $this->loading;?>

	<?php if ($this->about === 'album'):?>
		<?php if ( ! empty(current($this->album)->preview_itunes)):?>
			<div id="all_play_div">
				<a class="ui-btn ui-mini ui-btn-inline" id="review_music_write_listen_all">全曲試聴</a>
				<div id="current_div">
					<div id="current_track"></div>
					<div id="current_time"></div>
				</div>
			</div>
		<?php endif;?>
		<ul id="review_music_detail_list" data-role="listview">
			<?php foreach ($album as $val):?>
				<li class="review_music_detail_name">
				<?php $preview_itunes = $val->preview_itunes;?>
				<?php if ( ! empty($preview_itunes)):?>
					<span class="preview_button">▶️</span>
					<audio class="preview_itunes">
						<source src="<?php echo $preview_itunes;?>">
						<?php echo Html::anchor($preview_itunes, '▶️', array('target' => 'new_win'));?>
					</audio>
				<?php endif;?>
				<span class="track_name"><a href="/track/detail/<?php echo $val->id;?>"><?php echo $val->name;?></a></span>
				</li>
			<?php endforeach;?>
		</ul>
	<?php elseif ($this->about === 'track'):?>
		<div id="all_play_div" style="height: 14px;">
			<div id="current_div">
				<div id="current_track"></div>
				<div id="current_time"></div>
			</div>
		</div>

		<br />

		<ul id="review_music_detail_list" data-role="listview">
			<li class="review_music_detail_name">
				<?php if ( ! empty($this->track_preview_itunes)):?>
					<span class="preview_button">▶️</span>
					<audio class="preview_itunes">
						<source src="<?php echo $this->track_preview_itunes;?>">
						<?php echo Html::anchor($this->track_preview_itunes, '▶️', array('target' => 'new_win'));?>
					</audio>
				<?php endif;?>
				<span class="track_name"><a href="/track/detail/<?php echo $track_id;?>"><?php echo $track_name; ?></a></span>
			</li>
		</ul>
	<?php endif;?>

	<br />
	<br />

	<div class="go_review"><?php echo Html::anchor("/review/music/write/{$this->artist_id}/", 'レビューをかいてみませんか？', array('rel' => 'external')); ?></div>

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

	<br />

	<div class="qr_div">
		<span class="qr_description">読み取るとこのページが表示されます</span>
		<br />
		<img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>">
	</div>
	<br />

</div>
<?php echo Form::hidden('review_id', $id, array('id' => 'review_music_detail_review_id'));?><br />
<?php echo Form::hidden('review_user_id', $this->user_id, array('id' => 'review_music_detail_review_user_id'));?><br />
<?php echo Form::hidden('user_me_id', $this->user_me_id, array('id' => 'review_music_detail_user_me_id'));?><br />
<?php echo Form::hidden('user_me_name', htmlentities($this->user_me_name, ENT_QUOTES, mb_internal_encoding()), array('id' => 'review_music_detail_user_me_name'));?><br />
<?php echo Form::hidden('user_me_image', $this->user_me_image, array('id' => 'review_music_detail_user_me_image')); ?><br />
<?php echo Form::hidden('cool_api_url', \Config::get('host.base_url_http'). '/api/review/sendcool.json', array('id' => 'review_music_detail_cool_api_url'));?><br />
<?php echo Form::hidden('cool_more_api_url', \Config::get('host.base_url_http'). '/api/review/getcool.json', array('id' => 'review_music_detail_cool_more_api_url'));?><br />
<?php echo Form::hidden('send_comment_api_url', \Config::get('host.base_url_http'). '/api/review/sendcomment.json', array('id' => 'review_music_detail_send_comment_api_url'));?><br />
<?php echo Form::hidden('delete_comment_api_url', \Config::get('host.base_url_http'). '/api/review/deletecomment.json', array('id' => 'review_music_detail_delete_comment_api_url'));?><br />
<?php echo Form::hidden('get_comment_api_url', \Config::get('host.base_url_http'). '/api/review/getcomment.json', array('id' => 'review_music_detail_get_comment_api_url'));?><br />

<?php echo Form::hidden('about', $this->about, array('id' => 'review_music_detail_about')); ?><br />
<?php echo Form::hidden('comment_limit', $comment_limit, array('id' => 'comment_limit'));?>

<div id="modaldiv_cool" class="modaldiv">
	<div id="modaldiv_cool_title">thanks!</div>
	<div id="modaldiv_cool_users">
		<?php foreach ($this->arr_cool_users as $i => $val):?>
			<div class="modaldiv_list">
				<a href="/user/you/<?php echo $val['user_id'];?>/" rel="external">
					<span class="modaldiv_list_img"><img src="<?php echo $val['user_image']; ?>" title="<?php echo $val['user_name'];?>さんがクール！"></span>
					<span class="modaldiv_list_name"><?php echo $val['user_name'];?></span>
				</a>
			</div>
		<?php endforeach;?>
	</div>
	<?php if ($cool_all_count > 20):?>
	<div id="modaldiv_cool_more"><span>もっとみる</span></div>
	<?php endif;?>
</div>
