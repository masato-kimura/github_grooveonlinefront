<?php echo Asset::js('pc/review/music/write.js'); ?>
<?php echo Asset::css('pc/review/music/write.css'); ?>
<nav class="main_navi">
	<span class="main_navi_title"><?php echo Html::anchor('/review/music/', "レビュー一覧"); ?></span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title">レビュー投稿</span>
	<span class="main_navi_ans"><?php echo Html::anchor('artist/search/review/?artist_name='. $this->artist_name, '[アーティスト検索]');?></span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title"><?php echo $this->artist_name;?></span>
</nav>

<div class="main_div">
	<section id="music_write_artist_review_section">
		<div class="music_write_review_title">アーティストレビュー</div>
		<div class="music_write_review_name"><?php echo $this->artist_name; ?></div>
		<div id="music_write_artist_review_image"><img src="<?php echo $this->artist_image_middle; ?>" ></div>
		<div class="star_review_area">
			<span class="review_title">スターレビュー</span>
			<span class="review_value">
				<?php echo Form::select('artist_star', $this->artist_star, $this->arr_star_review, array('class' => 'star_select artist', 'id' => 'music_write_artist_star')). PHP_EOL; ?>
				<div id="music_write_artist_star_rated" class="rateit" data-rateit-backingfld="#music_write_artist_star"></div>
			</span>
		</div>
		<div class="comment_review_area">
			<div class="comment_review_control">
				<span class="review_title">コメントレビュー</span>
				<span class="review_value updown_mark">▼</span>
				<span class="review_value updown_mark" style="display:none;">▲</span>
			</div>
			<div class="comment_review_textarea_wrap">
				<?php echo Form::textarea('artist_review', $this->artist_review, array('class' => 'comment_review_textarea artist', 'id' => 'music_write_artist_comment_review_textarea', 'spellcheck' => 'false', 'style' => 'height: 20px;')). PHP_EOL;?>
			</div>
		</div>
		<div id="music_write_artist_updated_at" class="music_write_updated_at"><?php echo isset($this->artist_updated_at)? $this->artist_updated_at. '　LastUpdated': null; ?></div>
		<div class="submit_area">
			<?php if ( ! empty($this->artist_review_id)): ?>
				<?php echo Form::button('artist_delete', '削除', array('id' => 'music_write_artist_delete', 'class' => 'review_delete_button btn delete_btn')). PHP_EOL; ?>
			<?php else: ?>
				<?php echo Form::button('artist_delete', '削除', array('id' => 'music_write_artist_delete', 'class' => 'review_delete_button btn delete_disabled_btn', 'disabled' => 'disabled')). PHP_EOL; ?>
			<?php endif; ?>
			<?php echo Form::button('artist_submit', 'アーティストレビューを投稿', array('id' => 'music_write_artist_submit', 'class' => 'music_write_submit btn send_disabled_btn', 'disabled' => 'disabled')); ?>
		</div>
	</section>

	<section id="music_write_album_art_section">
		<?php echo $this->loading. PHP_EOL; ?>
		<div id="music_write_album_append_area"></div>
		<div id="music_write_more_album_area" style="text-align: center; margin-top: 10px;"><a id="music_write_more_album_link">もっとみる</a></div>
		<div id="music_write_album_append_hidden_area"></div>
		<div id="music_write_hover_album_art"></div>
		<div id="music_write_hover_album_name"></div>
		<?php $artist_review_id = $this->artist_review_id; ?>
		<?php echo Form::hidden('already_artist_review', empty($artist_review_id)? '' : 'true', array('id' => 'music_write_already_artist_review')); ?>
	</section>

	<hr class="clearboth" />

	<!-- アルバムレビュー -->
	<section id="music_write_album_review_section">
		<div class="music_write_review_title">アルバムレビュー</div>
		<div class="music_write_review_name_input">
			<?php echo Form::input('album_name', '', array('class' => 'title_area', 'id' => 'music_write_album_name', 'placeholder' => 'アルバムタイトルを検索')); ?><span class="clear_text" id="album_clear_text">x</span>
			<span><input type="button" value="検索" id="music_write_album_search_button" class="search_button"></span>
			<span id="music_write_to_artist_review" class="to_top" title="アーティストレビューへ">^</span>
		</div>

		<div id="music_write_review_album_search_result"></div>
		<div id="music_write_review_album_search_error" class="search_error"></div>

		<div id="music_write_album_title">
			<span id="music_write_album_name_disp">
				<?php echo isset($this->album_name)? $this->album_name: null; ?>
			</span>
		</div>
		<div id="music_write_album_selected_image" class="">
			<span id="music_write_album_selected_image_span">
				<?php if ( ! empty($this->album_image_middle)): ?>
				<img src="<?php echo $this->album_image_middle; ?>">
				<?php endif; ?>
			</span>
		</div>
		<div id="music_write_album_selected_tracks_area" class="">
			<?php echo $this->loading;?>
			<span id="music_write_album_selected_tracks" class="">
				<?php foreach($this->album_tracks as $i => $val): ?>
					<span class="track_span">
						<span class="track_name"><?php echo $val->name;?></span>
						<?php echo Form::hidden('track_id', $val->id, array('class' => 'track_id')); ?>
						<?php echo Form::hidden('track_name_hidden', $val->name, array('class' => 'track_name_hidden')); ?>
						<?php echo Form::hidden('mbid_itunes', $val->mbid_itunes, array('class' => 'mbid_itunes')); ?>
						<?php echo Form::hidden('mbid_lastfm', $val->mbid_lastfm, array('class' => 'mbid_lastfm')); ?>
						<?php echo Form::hidden('url_itunes', $val->url_itunes, array('class' => 'url_itunes')); ?>
						<?php echo Form::hidden('url_lastfm', $val->url_lastfm, array('class' => 'url_lastfm')); ?>
						</span>
					<br />
				<?php endforeach; ?>
			</span>
		</div>

		<div class="star_review_area">
			<span class="review_title">スターレビュー</span>
			<span class="review_value">
				<?php echo Form::select('album_star', $this->album_star, $this->arr_star_review, array('class' => 'star_select album', 'id' => 'music_write_album_star')). PHP_EOL; ?>
				<div class="rateit" data-rateit-backingfld="#music_write_album_star"></div>
			</span>
		</div>
		<div class="comment_review_area">
			<div class="comment_review_control">
				<span class="review_title">コメントレビュー</span>
				<span class="review_value updown_mark">▼</span>
				<span class="review_value updown_mark" style="display:none;">▲</span>
			</div>
			<div class="comment_review_textarea_wrap">
				<?php echo Form::textarea('album_review', $this->album_review, array('class' => 'comment_review_textarea album', 'id' => 'music_write_album_comment_review_textarea', 'spellcheck' => 'false', 'style' => 'height: 17px;')). PHP_EOL;?>
			</div>
		</div>

		<div id="music_write_album_updated_at" class="music_write_updated_at"></div>
		<div class="submit_area">
			<?php echo Form::button('album_delete', '削除', array('id' => 'music_write_album_delete', 'class' => 'review_delete_button btn delete_disabled_btn', 'disabled' => 'disabled')); ?>
			<?php echo Form::button('album_submit', 'アルバムレビューを投稿', array('id' => 'music_write_album_submit', 'class' => 'music_write_submit btn send_disabled_btn', 'disabled' => 'disabled')); ?>
		</div>
	</section>

	<hr class="clearboth" />

	<!-- トラックレビュー -->
	<section id="music_write_track_review_section">
		<div class="music_write_review_title">トラックレビュー</div>
		<div class="music_write_review_name_input">
			<?php echo Form::input('track_name', '', array('class' => 'title_area', 'id' => 'music_write_track_name', 'placeholder' => '楽曲タイトルを検索'));?>
			<span class="clear_text" id="track_clear_text">x</span>
			<span><input type="button" value="検索" id="music_write_track_search_button" class="search_button"></span>
			<span id="music_write_to_album_review" class="to_top" title="アーティストレビューへ">^</span>
		</div>

		<div id="music_write_review_track_search_result"></div>
		<div id="music_write_review_track_search_error" class="search_error"></div>

		<div id="music_write_track_selected_image" class="">
			<span id="music_write_track_selected_image_span">
				<?php if ( ! empty($this->track_image_middle)): ?>
				<img src="<?php echo $this->track_image_middle; ?>">
				<?php endif; ?>
			</span>
		</div>
		<div id="music_write_track_selected_content_area" class="">
			<?php echo $this->loading;?>
			<span id="music_write_track_selected_tracks">
				<span class="music_write_track_info">トラック名：</span>
				<span id="music_write_track_disp"><?php echo $this->track_name; ?></span><br />
				<span class="music_write_track_info">アーティスト：</span>
				<span><?php echo $this->track_artist_name; ?></span><br />
				<span class="music_write_track_info">収録アルバム：</span>
				<span><?php echo $this->track_album_name; ?></span><br />
			</span>
		</div>
		<div class="star_review_area">
			<span class="review_title">スターレビュー</span>
			<span class="review_value">
				<?php echo Form::select('track_star', $this->track_star, $this->arr_star_review, array('class' => 'star_select track', 'id' => 'music_write_track_star')). PHP_EOL; ?>
				<div class="rateit" data-rateit-backingfld="#music_write_track_star"></div>
			</span>
		</div>
		<div class="comment_review_area">
			<div class="comment_review_control">
				<span class="review_title">コメントレビュー</span>
				<span class="review_value updown_mark">▼</span>
				<span class="review_value updown_mark" style="display:none;">▲</span>
			</div>
			<div class="comment_review_textarea_wrap">
				<?php echo Form::textarea('track_review', $this->track_review, array('class' => 'comment_review_textarea track', 'id' => 'music_write_track_comment_review_textarea', 'spellcheck' => 'false', 'style' => 'height: 17px;')). PHP_EOL;?>
			</div>
		</div>

		<div id="music_write_track_updated_at" class="music_write_updated_at"></div>
		<div class="submit_area">
			<?php echo Form::button('track_delete', '削除', array('id' => 'music_write_track_delete', 'class' => 'review_delete_button btn delete_disabled_btn', 'disabled' => 'disabled')). PHP_EOL; ?>
			<?php echo Form::button('track_submit', 'トラックレビューを投稿', array('id' => 'music_write_track_submit', 'class' => 'music_write_submit btn send_disabled_btn', 'disabled' => 'disabled')). PHP_EOL; ?>
		</div>
	</section>

	<div><?php echo Html::anchor('artist/search/review/?artist_name='. $this->artist_name, '戻る'); ?></div>

	<p class="hidden_form" id="music_write_artist_hidden_form">
	<?php echo Form::input('artist_star_tmp',    $this->artist_star_tmp,   array('id' => 'music_write_artist_star_tmp')). PHP_EOL; ?>
	<?php echo Form::input('aritst_review_tmp',  $this->artist_review_tmp, array('id' => 'music_write_artist_comment_review_textarea_tmp')). PHP_EOL; ?>
	<?php echo Form::input('artist_id',          $this->artist_id,         array('id' => 'music_write_artist_id')). PHP_EOL; ?>
	<?php echo Form::input('artist_name',        $this->artist_name,       array('id' => 'music_write_artist_name')). PHP_EOL; ?>
	<?php echo Form::input('artist_review_id',   $this->artist_review_id,  array('id' => 'music_write_artist_review_id')). PHP_EOL; ?>
	<?php echo Form::input('artist_url',         '',                       array('id' => 'music_write_artist_url')). PHP_EOL; ?>
	</p>

	<p class="hidden_form" id="music_write_album_hidden_form">
	<?php echo Form::input('album_star_tmp',      '',                        array('id' => 'music_write_album_star_tmp')). PHP_EOL; ?>
	<?php echo Form::input('album_review_tmp',    '',                        array('id' => 'music_write_album_comment_review_textarea_tmp')). PHP_EOL; ?>
	<?php echo Form::input('album_id',            $this->album_id,           array('id' => 'music_write_album_id')). PHP_EOL; ?>
	<?php echo Form::input('album_name_hidden',   $this->album_name,         array('id' => 'music_write_album_name_hidden')). PHP_EOL; ?>
	<?php echo Form::input('album_review_id',     '',                        array('id' => 'music_write_album_review_id')). PHP_EOL; ?>
	<?php echo Form::input('album_mbid_itunes',   $this->album_mbid_itunes,  array('id' => 'music_write_album_mbid_itunes')). PHP_EOL; ?>
	<?php echo Form::input('album_mbid_lastfm',   $this->album_mbid_lastfm,  array('id' => 'music_write_album_mbid_lastfm')). PHP_EOL; ?>
	<?php echo Form::input('album_image',         $this->album_image,        array('id' => 'music_write_hidden_album_image')). PHP_EOL;?>
	<?php echo Form::input('album_url_itunes',    $this->album_url_itunes,   array('id' => 'music_write_album_url_itunes')). PHP_EOL; ?>
	<?php echo Form::input('album_url_lastfm',    $this->album_url_lastfm,   array('id' => 'music_write_album_url_lastfm')). PHP_EOL; ?>
	<?php echo Form::input('album_not_exist_flag', false,                    array('id' => 'music_write_album_not_exist_flag')). PHP_EOL; ?>
	</p>

	<p class="hidden_form" id="music_write_track_hidden_form">
	<?php echo Form::input('track_star_tmp',      '',                        array('id' => 'music_write_track_star_tmp')). PHP_EOL; ?>
	<?php echo Form::input('track_review_tmp',    '',                        array('id' => 'music_write_track_comment_review_textarea_tmp')). PHP_EOL; ?>
	<?php echo Form::input('track_id',            $this->track_id,           array('id' => 'music_write_track_id')). PHP_EOL; ?>
	<?php echo Form::input('track_name_hidden',   $this->track_name,         array('id' => 'music_write_track_name_hidden')). PHP_EOL; ?>
	<?php echo Form::input('track_review_id',     '',                        array('id' => 'music_write_track_review_id')). PHP_EOL; ?>
	<?php echo Form::input('mbid_itunes',   '',                              array('id' => 'music_write_mbid_itunes')). PHP_EOL; ?>
	<?php echo Form::input('mbid_lastfm',   '',                              array('id' => 'music_write_mbid_lastfm')). PHP_EOL; ?>
	<?php echo Form::input('track_content',       '',                        array('id' => 'music_write_track_content')). PHP_EOL; ?>
	<?php echo Form::input('url_itunes',    $this->url_itunes,               array('id' => 'music_write_url_itunes')). PHP_EOL; ?>
	<?php echo Form::input('url_lastfm',    $this->url_lastfm,               array('id' => 'music_write_url_lastfm')). PHP_EOL; ?>
	<?php echo Form::input('track_album_name',    '',                        array('id' => 'music_write_track_album_name')). PHP_EOL; ?>
	<?php echo Form::input('track_album_mbid_itunes', '',                    array('id' => 'music_write_track_album_mbid_itunes')). PHP_EOL; ?>
	<?php echo Form::input('track_album_mbid_lastfm', '',                    array('id' => 'music_write_track_album_mbid_lastfm')). PHP_EOL; ?>
	<?php echo Form::input('track_album_url_itunes',  '',                    array('id' => 'music_write_track_album_url_itunes')). PHP_EOL; ?>
	<?php echo Form::input('track_album_url_lastfm',  '',                    array('id' => 'music_write_track_album_url_lastfm')). PHP_EOL; ?>
	<?php echo Form::input('track_album_artist',  '',                        array('id' => 'music_write_track_album_artist')). PHP_EOL; ?>
	<?php echo Form::input('track_album_image',   '',                        array('id' => 'music_write_track_album_image')). PHP_EOL; ?>
	<?php echo Form::input('track_not_exist_flag', false,                    array('id' => 'music_write_track_not_exist_flag')). PHP_EOL; ?>
	</p>

	<p class="hidden_form" id="music_write_api_url">
	<?php echo Form::input('api_url_send_review',       '/api/review/set.json',             array('id' => 'music_write_api_url_send_review')). PHP_EOL; ?>
	<?php echo Form::input('api_url_search_review',     '/api/review/one.json',             array('id' => 'music_write_api_url_search_review')). PHP_EOL; ?>
	<?php echo Form::input('api_url_search_album',      '/api/album/list.json',             array('id' => 'music_write_api_url_album_list')). PHP_EOL; ?>
	<?php echo Form::input('api_url_search_word_album', '/api/album/search.json',       array('id' => 'music_write_api_url_search_album_word')). PHP_EOL; ?>
	<?php echo Form::input('api_url_search_albumtrack', '/api/track/albumtracklist.json', array('id' => 'music_write_api_url_search_albumtrack')). PHP_EOL; ?>
	<?php echo Form::input('api_url_search_track',      '/api/track/info.json',        array('id' => 'music_write_api_url_search_track')). PHP_EOL; ?>
	<?php echo Form::input('api_url_search_track_searchlist', '/api/track/search.json', array('id' => 'music_write_api_url_search_track_searchlist')). PHP_EOL; ?>
	</p>
</div>