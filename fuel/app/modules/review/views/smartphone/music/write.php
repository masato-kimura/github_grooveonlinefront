<?php echo Asset::js('smartphone/review/music/write.js'); ?>
<?php echo Asset::js('jquery.leanModal.min.js');?>
<?php echo Asset::css('smartphone/review/music/write.css'); ?>

<?php if ( ! \Agent::is_smartphone()):?>
<style>
.main_div {
  margin-top: 10px;
  padding: 10px 35px;
}
</style>
<?php endif;?>

<nav class="main_navi">
	<span class="main_navi_title"><?php echo Html::anchor('/artist/search/', "アーティスト検索"); ?></span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title">アーティスト</span>
	<span class="main_navi_ans">[<?php echo Html::anchor("/artist/detail/{$this->artist_id}/", $this->artist_name); ?>]</span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title">レビュー</span>
</nav>

<div style="text-align: right; padding: 5px 5px 0px 0px; margin: 10px auto; max-width: 894px;">
	<a href="<?php echo \Config::get('amazon.url');?>&tag=<?php echo \Config::get('amazon.tracking_id');?>&keywords=<?php echo $artist_name;?>" target="_new" class="amazon_link"><?php echo Asset::img('review/assocbtn_gray_amazon1._V288606497_.png', array('style' => 'width: 112px; height: 40px;', 'data-original' => 'review/assocbtn_gray_amazon1._V288606497_.png'));?></a>
	&nbsp;
	<a href="<?php echo \Config::get('itunes.url_geo');?>/artist/<?php echo $artist_segment_name;?>/id<?php echo $artist_mbid_itunes;?>?at=<?php echo \Config::get('itunes.affiliate_id');?>&app=itunes" target="new_win" id="music_write_itunes_link" class="itunes_link" style="display: inline-block;overflow:hidden;background:url(http://linkmaker.itunes.apple.com/images/badges/en-us/badge_itunes-lrg.svg) no-repeat;width:113px;height:40px;margin-right: 10px;"></a>
</div>

<div class="review_music_content main_div">
	<div id="music_write_artist_review_section">
		<div class="music_write_review_title">アーティストレビュー投稿</div>
		<div class="music_write_review_name"><?php echo $artist_name; ?></div>
		<div class="music_write_review_to_artist_page"><?php echo Html::anchor('/artist/detail/' . $artist_id. '/', 'アーティストページへ');?></div>
		<div id="music_write_artist_review_image"><img src="<?php echo $this->artist_image_middle; ?>" data-original="<?php echo $this->artist_image_middle; ?>"></div>

		<div id="music_write_favorite_artist_div">
			<span id="music_write_favorite_artist_title"><?php echo $artist_name;?>を<br />お気に入りアーティストに登録</span>
			<?php if (isset($client_user_id)):?>
					<?php if ($favorite_status):?>
						<?php echo Form::select('favorite_artist_status', '1', array("0" => '', "1" => '★'), array('data-role' => 'flipswitch'));?>
					<?php else:?>
						<?php echo Form::select('favorite_artist_status', '0', array("0" => '', "1" => '★'), array('data-role' => 'flipswitch'));?>
					<?php endif;?>
			<?php else:?>
					<a id="favorite_artist_status_disabled_anchor" style="display: inline-block;">
						<?php echo Form::select('favorite_status_disabled', '0', array("0" => ''), array('data-role' => 'flipswitch', 'disabled' => 'disabled'));?>
					</a>
			<?php endif;?>
		</div>

		<div id="music_write_artist_itunes_link_div" style="text-align: right;">
			&nbsp;<a href="" target="new_win" id="music_write_artist_itunes_link" class="itunes_link" style="display:none;overflow:hidden;background:url(http://linkmaker.itunes.apple.com/images/badges/en-us/badge_itunes-lrg.svg) no-repeat;width:113px;height:40px;margin-right: 10px;"></a>
		</div>

		<div class="star_review_area ui-hide-label" >
			<label for="music_write_artist_star">スターレビュー</label>
			<?php echo Form::select('artist_star', $this->artist_star, $this->arr_star_review, array('class' => 'star_select artist', 'id' => 'music_write_artist_star', 'data-role' => 'none')). PHP_EOL; ?>
			<div id="music_write_artist_star_rated" class="rateit" data-rateit-backingfld="#music_write_artist_star"></div>
		</div>
		<div class="commentbox_area">
			<a href="#modaldiv_artist" class="ui-btn ui-btn-inline ui-mini ui-icon-edit ui-btn-icon-left ui-corner-all ui-shadow commentbox" id="commentbox_artist" rel="leanModal">コメントボックス</a>
		</div>
		<div class="comment_review_area">
			<div class="comment_review_textarea_wrap ui-field-contain ui-hide-label">
				<label for="music_write_artist_comment_review_textarea">アーティストレビューはこちら</label>
				<?php echo Form::textarea(
					'artist_review',
					$this->artist_review,
					array(
						'class'       => 'comment_review_textarea artist',
						'id'          => 'music_write_artist_comment_review_textarea',
						'placeholder' => 'このアーティストのコメントレビューはこちらへお書きください。',
						'spellcheck'  => 'false',
						'style'       => 'width: 93%; float: left;',
					)
				). PHP_EOL;
				?>
				<span class="textarea_resize">
					<span class="textarea_resize_up">▲</span>
					<span class="textarea_resize_down">▼</span>
				</span>
			</div>
		</div>
		<div id="music_write_artist_updated_at" class="music_write_updated_at"><?php echo isset($this->artist_updated_at)? $this->artist_updated_at. '　LastUpdated': null; ?>&nbsp;</div>
		<div class="submit_area">
			<?php if ( ! empty($this->artist_review_id)): ?>
				<?php echo Form::button('artist_delete', '削除', array('id' => 'music_write_artist_delete', 'class' => 'ui-btn ui-btn-inline ui-icon-delete review_delete_button btn delete_btn')). PHP_EOL; ?>
			<?php else: ?>
				<?php echo Form::button('artist_delete', '削除', array('id' => 'music_write_artist_delete', 'class' => 'ui-btn ui-btn-inline review_delete_button btn delete_disabled_btn', 'disabled' => 'disabled')). PHP_EOL; ?>
			<?php endif; ?>
			<?php echo Form::button('artist_submit', 'アーティストレビューを投稿', array('id' => 'music_write_artist_submit', 'class' => 'ui-btn ui-btn-inline music_write_submit btn send_disabled_btn', 'disabled' => 'disabled')); ?>
			&nbsp;
		</div>
	</div>

	<div id="music_write_album_art_section">
		<?php echo $this->loading. PHP_EOL; ?>
		<div id="music_write_album_append_area"></div>
		<div id="music_write_more_album_area" style="text-align: center; margin-top: 25px;">
			<a id="music_write_more_album_link">もっとみる</a>
		</div>

		<div id="music_write_album_append_hidden_area"></div>

		<div id="music_write_hover_album_main_div">
			<div id="music_write_hover_album_name"></div>
			<div id="music_write_hover_album_action"></div>
			<div id="music_write_hover_album_art"></div>
		</div>
		<?php $artist_review_id = $this->artist_review_id; ?>
		<?php echo Form::hidden('already_artist_review', empty($artist_review_id)? '' : 'true', array('id' => 'music_write_already_artist_review')); ?>
	</div>

	<hr class="clearboth" />

	<!-- アルバムレビュー -->
	<div id="music_write_album_review_section">
		<div class="music_write_review_title">アルバムレビュー投稿</div>

		<div class="music_write_review_name_input">
			<?php echo Form::input('album_name', '', array('type' => 'search', 'class' => 'search_title_area', 'id' => 'music_write_album_name', 'placeholder' => 'アルバムタイトルを検索')); ?>
			<div id="music_write_review_album_search_result"></div>
			<div id="music_write_review_album_search_error" class="search_error"></div>
			<input type="button" value="検索" id="music_write_album_search_button" class="search_button">
		</div>

		<div id="music_write_album_selected_image" class="">
			<span id="music_write_album_selected_image_span">
				<?php if ( ! empty($this->album_image_middle)): ?>
				<img src="<?php echo $this->album_image_middle; ?>" data-original="<?php echo $this->album_image_middle; ?>">
				<?php endif; ?>
			</span>
		</div>
		<div id="music_write_album_title">
			<strong>
			<span id="music_write_album_name_disp">
				<?php echo isset($this->album_name)? $this->album_name: null; ?>
			</span>
			</strong>
			<br />
			<span id="music_write_album_release"></span>
			<br />
			<span id="music_write_album_copyright"></span>
		</div>

		<div id="music_write_album_itunes_link_div" style="text-align: right; margin: 10px 0px; paddin-top: 10px;">
			<a href="<?php echo \Config::get('amazon.url');?>&tag=<?php echo \Config::get('amazon.tracking_id');?>&keywords=<?php echo $artist_name. ' '. $album_name;?>"  target="_new"><?php echo Asset::img('review/assocbtn_gray_amazon1._V288606497_.png', array('style' => 'width: 112px; height: 40px;', 'data-original' => 'review/assocbtn_gray_amazon1._V288606497_.png'));?></a>
			&nbsp;<a href="" target="new_win" id="music_write_album_itunes_link" style="display:none;overflow:hidden;background:url(http://linkmaker.itunes.apple.com/images/badges/en-us/badge_itunes-lrg.svg) no-repeat;width:113px;height:40px;margin-right: 10px;"></a>
		</div>

		<div id="all_play_div">
			<a class="ui-btn ui-mini ui-btn-inline" id="review_music_write_listen_all">全曲試聴</a>
			<div id="current_div">
				<div id="current_track"></div>
				<div id="current_time"></div>
			</div>
		</div>

		<div id="music_write_album_selected_tracks_area" class="">
			<?php echo $this->loading;?>
			<ul id="music_write_album_selected_tracks" data-role="listview" data-inset="false">
				<?php foreach($this->album_tracks as $i => $val): ?>
					<li class="track_name">
					<?php echo $val->name; ?>
					<?php echo Form::hidden('track_id', $val->id, array('class' => 'track_id')); ?>
					<?php echo Form::hidden('track_name_hidden', $val->name, array('class' => 'track_name_hidden')); ?>
					<?php echo Form::hidden('mbid_itunes', $val->mbid_itunes, array('class' => 'mbid_itunes')); ?>
					<?php echo Form::hidden('mbid_lastfm', $val->mbid_lastfm, array('class' => 'mbid_lastfm')); ?>
					<?php echo Form::hidden('url_itunes', $val->url_itunes, array('class' => 'url_itunes')); ?>
					<?php echo Form::hidden('url_lastfm', $val->url_lastfm, array('class' => 'url_lastfm')); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<br />
		<span id="music_write_album_scroll_header"></span>
		<div class="star_review_area ui-hide-label">
			<label for="music_write_album_star">スターレビュー</label>
			<?php echo Form::select('album_star', $this->album_star, $this->arr_star_review, array('class' => 'star_select album', 'id' => 'music_write_album_star', 'data-role' => 'none')). PHP_EOL; ?>
			<div class="rateit" data-rateit-backingfld="#music_write_album_star"></div>
		</div>
		<div class="commentbox_area">
			<a href="#modaldiv_album" class="ui-btn ui-btn-inline ui-mini ui-icon-edit ui-btn-icon-left commentbox" id="music_write_commentbox_album" rel="leanModal">コメントボックス</a>
		</div>
		<div class="comment_review_area ui-hide-label">
			<div class="comment_review_textarea_wrap ui-field-contain ui-hide-label">
				<label for="music_write_album_comment_review_textarea">コメントレビューはこちら</label>
				<?php echo Form::textarea(
						'album_review',
						$this->album_review,
						array(
							'class'       => 'comment_review_textarea album',
							'id'          => 'music_write_album_comment_review_textarea',
							'spellcheck'  => 'false',
							'placeholder' => 'このアルバムのコメントレビューはこちらへお書きください。',
							'style' => 'width: 93%; float: left;',
						)
					). PHP_EOL;
				?>
				<span class="textarea_resize">
					<span class="textarea_resize_up">▲</span>
					<span class="textarea_resize_down">▼</span>
				</span>
			</div>
		</div>

		<div id="music_write_album_updated_at" class="music_write_updated_at"></div>
		<div class="submit_area">
			<?php echo Form::button('album_delete', '削除', array('id' => 'music_write_album_delete', 'class' => 'review_delete_button btn delete_disabled_btn ui-btn ui-btn-inline', 'disabled' => 'disabled')); ?>
			<?php echo Form::button('album_submit', 'アルバムレビューを投稿', array('id' => 'music_write_album_submit', 'class' => 'music_write_submit btn send_disabled_btn ui-btn ui-btn-inline', 'disabled' => 'disabled')); ?>
		</div>
	</div>

	<hr class="clearboth" />



	<!-- トラックレビュー -->
	<section id="music_write_track_review_section">
		<div class="music_write_review_title">トラックレビュー投稿</div>
		<div class="music_write_review_name_input">
			<?php echo Form::input('track_name', '', array('type' => 'search', 'class' => 'title_area', 'id' => 'music_write_track_name', 'placeholder' => '楽曲タイトルを検索'));?>
			<div id="music_write_review_track_search_result"></div>
			<div id="music_write_review_track_search_error" class="search_error"></div>
			<span><input type="button" value="検索" id="music_write_track_search_button" class="search_button"></span>
		</div>


		<div id="music_write_track_selected_image" class="">
			<span id="music_write_track_selected_image_span">
				<?php if ( ! empty($this->track_image_middle)): ?>
				<img src="<?php echo $this->track_image_middle; ?>" data-original="<?php echo $this->track_image_middle; ?>">
				<?php endif; ?>
			</span>
		</div>
		<div id="music_write_track_selected_content_area" class="">
			<?php echo $this->loading;?>
			<span id="music_write_track_selected_tracks">
				<table id="music_write_track_selected_table">
					<tr>
						<td><span class="music_write_track_info">トラック名：</span></td>
						<td><span id="music_write_track_disp"><?php echo $this->track_name; ?></span></td>
					</tr>
					<tr>
						<td><span class="music_write_track_info">アーティスト：</span></td>
						<td><span><?php echo $this->track_artist_name; ?></span></td>
					</tr>
					<tr>
						<td><span class="music_write_track_info">収録アルバム：</span></td>
						<td><span><?php echo $this->track_album_name; ?></span></td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
				</table>
			</span>
		</div>

		<div id="music_write_track_itunes_link_div" style="text-align: right;">
			<a href="<?php echo \Config::get('amazon.url');?>&tag=<?php echo \Config::get('amazon.tracking_id');?>&keywords=<?php echo $artist_name. ' '. $track_name;?>" target="_new"><?php echo Asset::img('review/assocbtn_gray_amazon1._V288606497_.png', array('style' => 'width: 112px; height: 40px;'));?></a>
			<a href="" target="new_win" id="music_write_track_itunes_link" style="display:none;overflow:hidden;background:url(http://linkmaker.itunes.apple.com/images/badges/en-us/badge_itunes-lrg.svg) no-repeat;width:113px;height:40px;margin-right: 10px;"></a>
		</div>


		<span id="music_write_track_scroll_header"></span>
		<div class="star_review_area ui-hide-label">
			<label for="music_write_track_star">スターレビュー</label>
			<?php echo Form::select('track_star', $this->track_star, $this->arr_star_review, array('class' => 'star_select track', 'id' => 'music_write_track_star', 'data-role' => 'none')). PHP_EOL; ?>
			<div class="rateit" data-rateit-backingfld="#music_write_track_star"></div>
		</div>
		<div class="commentbox_area">
			<a href="#modaldiv_track" class="ui-btn ui-btn-inline ui-mini ui-icon-edit ui-btn-icon-left commentbox" id="music_write_commentbox_track" rel="leanModal">コメントボックス</a>
		</div>
		<div class="comment_review_area">
			<div class="comment_review_control ui-field-contain ui-hide-label">
				<label for="music_write_track_comment_review_textarea">コメントレビュー</label>
				<?php echo Form::textarea('track_review',
						$this->track_review,
						array(
							'class'       => 'comment_review_textarea track',
							'id'          => 'music_write_track_comment_review_textarea',
							'spellcheck'  => 'false',
							'placeholder' => 'このトラックのコメントレビューはこちらへお書きください。',
							'style' => 'width: 93%; float: left;',
						)
					). PHP_EOL;
				?>
				<span class="textarea_resize">
					<span class="textarea_resize_up">▲</span>
					<span class="textarea_resize_down">▼</span>
				</span>
			</div>
		</div>

		<div id="music_write_track_updated_at" class="music_write_updated_at"></div>
		<div class="submit_area">
			<?php echo Form::button('track_delete', '削除', array('id' => 'music_write_track_delete', 'class' => 'review_delete_button btn delete_disabled_btn ui-btn ui-btn-inline', 'disabled' => 'disabled')). PHP_EOL; ?>
			<?php echo Form::button('track_submit', 'トラックレビューを投稿', array('id' => 'music_write_track_submit', 'class' => 'music_write_submit btn send_disabled_btn ui-btn ui-btn-inline', 'disabled' => 'disabled')). PHP_EOL; ?>
		</div>
	</section>

	<br />
	<div class="qr_div">
		<span class="qr_description">読み取るとこのページが表示されます</span>
		<br />
		<img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>"  data-original="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>">
	</div>


	<p class="hidden_form" id="music_write_artist_hidden_form">
	<?php echo Form::hidden('artist_star_tmp',    $this->artist_star_tmp,   array('id' => 'music_write_artist_star_tmp')). PHP_EOL; ?>
	<?php echo Form::hidden('aritst_review_tmp',  $this->artist_review_tmp, array('id' => 'music_write_artist_comment_review_textarea_tmp')). PHP_EOL; ?>
	<?php echo Form::hidden('artist_id',          $this->artist_id,         array('id' => 'music_write_artist_id')). PHP_EOL; ?>
	<?php echo Form::hidden('artist_name',        $this->artist_name,       array('id' => 'music_write_artist_name')). PHP_EOL; ?>
	<?php echo Form::hidden('artist_review_id',   $this->artist_review_id,  array('id' => 'music_write_artist_review_id')). PHP_EOL; ?>
	<?php echo Form::hidden('artist_url',         '',                       array('id' => 'music_write_artist_url')). PHP_EOL; ?>
	</p>

	<p class="hidden_form" id="music_write_album_hidden_form">
	<?php echo Form::hidden('album_star_tmp',      '',                        array('id' => 'music_write_album_star_tmp')). PHP_EOL; ?>
	<?php echo Form::hidden('album_review_tmp',    '',                        array('id' => 'music_write_album_comment_review_textarea_tmp')). PHP_EOL; ?>
	<?php echo Form::hidden('album_id',            $this->album_id,           array('id' => 'music_write_album_id')). PHP_EOL; ?>
	<?php echo Form::hidden('album_name_hidden',   $this->album_name,         array('id' => 'music_write_album_name_hidden')). PHP_EOL; ?>
	<?php echo Form::hidden('album_review_id',     '',                        array('id' => 'music_write_album_review_id')). PHP_EOL; ?>
	<?php echo Form::hidden('album_mbid_itunes',   $this->album_mbid_itunes,  array('id' => 'music_write_album_mbid_itunes')). PHP_EOL; ?>
	<?php echo Form::hidden('album_mbid_lastfm',   $this->album_mbid_lastfm,  array('id' => 'music_write_album_mbid_lastfm')). PHP_EOL; ?>
	<?php echo Form::hidden('album_image',         $this->album_image,        array('id' => 'music_write_hidden_album_image')). PHP_EOL;?>
	<?php echo Form::hidden('album_url_itunes',    $this->album_url_itunes,   array('id' => 'music_write_album_url_itunes')). PHP_EOL; ?>
	<?php echo Form::hidden('album_url_lastfm',    $this->album_url_lastfm,   array('id' => 'music_write_album_url_lastfm')). PHP_EOL; ?>
	<?php echo Form::hidden('album_not_exist_flag', false,                    array('id' => 'music_write_album_not_exist_flag')). PHP_EOL; ?>
	</p>

	<p class="hidden_form" id="music_write_track_hidden_form">
	<?php echo Form::hidden('track_star_tmp',      '',                        array('id' => 'music_write_track_star_tmp')). PHP_EOL; ?>
	<?php echo Form::hidden('track_review_tmp',    '',                        array('id' => 'music_write_track_comment_review_textarea_tmp')). PHP_EOL; ?>
	<?php echo Form::hidden('track_id',            $this->track_id,           array('id' => 'music_write_track_id')). PHP_EOL; ?>
	<?php echo Form::hidden('track_name_hidden',   $this->track_name,         array('id' => 'music_write_track_name_hidden')). PHP_EOL; ?>
	<?php echo Form::hidden('track_review_id',     '',                        array('id' => 'music_write_track_review_id')). PHP_EOL; ?>
	<?php echo Form::hidden('mbid_itunes',   '',                              array('id' => 'music_write_mbid_itunes')). PHP_EOL; ?>
	<?php echo Form::hidden('mbid_lastfm',   '',                              array('id' => 'music_write_mbid_lastfm')). PHP_EOL; ?>
	<?php echo Form::hidden('track_content',       '',                        array('id' => 'music_write_track_content')). PHP_EOL; ?>
	<?php echo Form::hidden('url_itunes',    $this->url_itunes,               array('id' => 'music_write_url_itunes')). PHP_EOL; ?>
	<?php echo Form::hidden('url_lastfm',    $this->url_lastfm,               array('id' => 'music_write_url_lastfm')). PHP_EOL; ?>
	<?php echo Form::hidden('track_album_name',    '',                        array('id' => 'music_write_track_album_name')). PHP_EOL; ?>
	<?php echo Form::hidden('track_album_mbid_itunes', '',                    array('id' => 'music_write_track_album_mbid_itunes')). PHP_EOL; ?>
	<?php echo Form::hidden('track_album_mbid_lastfm', '',                    array('id' => 'music_write_track_album_mbid_lastfm')). PHP_EOL; ?>
	<?php echo Form::hidden('track_album_url_itunes',  '',                    array('id' => 'music_write_track_album_url_itunes')). PHP_EOL; ?>
	<?php echo Form::hidden('track_album_url_lastfm',  '',                    array('id' => 'music_write_track_album_url_lastfm')). PHP_EOL; ?>
	<?php echo Form::hidden('track_album_artist',  '',                        array('id' => 'music_write_track_album_artist')). PHP_EOL; ?>
	<?php echo Form::hidden('track_album_image',   '',                        array('id' => 'music_write_track_album_image')). PHP_EOL; ?>
	<?php echo Form::hidden('track_not_exist_flag', false,                    array('id' => 'music_write_track_not_exist_flag')). PHP_EOL; ?>
	</p>

	<p class="hidden_form" id="music_write_api_url" >
	<?php echo Form::hidden('api_url_send_review',       '/api/review/set.json',             array('id' => 'music_write_api_url_send_review')). PHP_EOL; ?>
	<?php echo Form::hidden('api_url_search_review',     '/api/review/one.json',             array('id' => 'music_write_api_url_search_review')). PHP_EOL; ?>
	<?php echo Form::hidden('api_url_setusercomment',    '/api/review/setusercomment.json',  array('id' => 'music_write_api_url_setusercomment')). PHP_EOL; ?>
	<?php echo Form::hidden('api_url_removeusercomment', '/api/review/removeusercomment.json',array('id' => 'music_write_api_url_removeusercomment')). PHP_EOL; ?>
	<?php echo Form::hidden('api_url_search_album',      '/api/album/list.json',             array('id' => 'music_write_api_url_album_list')). PHP_EOL; ?>
	<?php echo Form::hidden('api_url_search_word_album', '/api/album/search.json',       array('id' => 'music_write_api_url_search_album_word')). PHP_EOL; ?>
	<?php echo Form::hidden('api_url_search_albumtrack', '/api/track/albumtracklist.json', array('id' => 'music_write_api_url_search_albumtrack')). PHP_EOL; ?>
	<?php echo Form::hidden('api_url_search_track',      '/api/track/info.json',        array('id' => 'music_write_api_url_search_track')). PHP_EOL; ?>
	<?php echo Form::hidden('api_url_search_track_searchlist', '/api/track/search.json', array('id' => 'music_write_api_url_search_track_searchlist')). PHP_EOL; ?>
	</p>

	<p class="hidden_form">
	<?php echo Form::hidden('client_user_id', $client_user_id);?>
	</p>
</div>


<div id="modaldiv_artist" class="modaldiv">
	<div class="available_comment">最大20件のコメントが保存できます。</div>
	<div class="when_comment_over">20件を超えた場合は下から順に削除されます。</div>
	<ul data-role="listview">
	<?php foreach ($arr_all_comment as $i => $val):?>
		<li>
			<a class="modal_link" rel="external"><?php echo $val['user_comment'];?></a>
			<span class="main_text"><?php echo $val['user_comment'];?></span>
			<span class="user_comment_id"><?php echo $val['id'];?></span>
			<?php if (empty($val['id'])):?>
				<div class="modal_link_delete_div"><input type="button" value="削除" data-role="none" disabled="disabled" style="color: #888;display:none;"></div>
			<?php else:?>
				<div class="modal_link_delete_div"><input type="button" value="削除" data-role="none"></div>
			<?php endif;?>
		</li>
	<?php endforeach;?>
	</ul>
</div>

<div id="modaldiv_album" class="modaldiv">
	<div class="available_comment">最大20件のコメントが保存できます。</div>
	<div class="when_comment_over">20件を超えた場合は下から順に削除されます。</div>
	<ul data-role="listview">
	<?php foreach ($arr_all_comment as $i => $val):?>
		<li>
			<a class="modal_link" rel="external"><?php echo $val['user_comment'];?></a>
			<span class="main_text"><?php echo $val['user_comment'];?></span>
			<span class="user_comment_id"><?php echo $val['id'];?></span>
			<?php if (empty($val['id'])):?>
				<div class="modal_link_delete_div"><input type="button" value="削除" data-role="none" disabled="disabled" style="color: #888;display:none;"></div>
			<?php else:?>
				<div class="modal_link_delete_div"><input type="button" value="削除" data-role="none"></div>
			<?php endif;?>
		</li>
	<?php endforeach;?>
	</ul>
</div>

<div id="modaldiv_track" class="modaldiv">
	<div class="available_comment">最大20件のコメントが保存できます。</div>
	<div class="when_comment_over">20件を超えた場合は下から順に削除されます。</div>
	<ul data-role="listview">
	<?php foreach ($arr_all_comment as $i => $val):?>
		<li>
			<a class="modal_link" rel="external"><?php echo $val['user_comment'];?></a>
			<span class="main_text"><?php echo $val['user_comment'];?></span>
			<span class="user_comment_id"><?php echo $val['id'];?></span>
			<?php if (empty($val['id'])):?>
				<div class="modal_link_delete_div"><input type="button" value="削除" data-role="none" disabled="disabled" style="color: #888;display:none;"></div>
			<?php else:?>
				<div class="modal_link_delete_div"><input type="button" value="削除" data-role="none"></div>
			<?php endif;?>
		</li>
	<?php endforeach;?>
	</ul>
</div>

<?php if (isset($is_first_regist) && $is_first_regist == true):?>
	<?php echo Form::hidden('is_first_regist', true, array('id' => 'review_write_is_first_regist'));?>
<?php else:?>
	<?php echo Form::hidden('is_first_regist', true, array('id' => 'review_write_is_first_regist'));?>
<?php endif;?>


