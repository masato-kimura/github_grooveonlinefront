<?php echo Asset::js('pc/artist/search.js'); ?>
<?php echo Asset::css('pc/artist/search.css');?>

<nav class="main_navi">
	<span class="main_navi_title"><?php echo Html::anchor('/review/music/', "レビュー一覧"); ?></span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title">レビュー投稿</span>
	<span class="main_navi_ans">[アーティスト検索]</span>
</nav>

<div id="artist_search_content">

	<section id="artist_search_section">
		<h3 class="description">アーティストを検索して視聴やレビューをしよう！</h3>

		<?php echo Form::open(array('id' => 'search_form')). PHP_EOL; ?>
		<!-- テキスト入力フォーム -->
		<label for="artist_search_input" class="title hidden_class">アーティスト検索</label>
		<?php echo Form::input('artist_search_input', $this->artist_name, array('id' => 'artist_search_input', 'name' => 'artist_search_input', 'placeholder' => 'アーティスト名をこちらに入力してください')); ?><span id="artist_search_clear_text">x</span>
		<input type="button" value="検索" id="artist_search_search_button" class="btn">
		<br />
		<?php if ( ! empty($this->arr_error)): ?>
			<div id="search_error_div">
			<?php foreach ($this->arr_error as $val): ?>
				<div><?php echo $val; ?></div>
			<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div id="missing_div"></div>
		<br />
		<?php echo Form::close(). PHP_EOL; ?>
	</section>

	<section id="list_section">
		<div id="loading_top" class="loading">
			<br />
			<span class="loading" >Loading</span>
			<span class="loading l-1"></span>
			<span class="loading l-2"></span>
			<span class="loading l-3"></span>
			<span class="loading l-4"></span>
			<span class="loading l-5"></span>
			<span class="loading l-6"></span>
		</div>
		<div class="main_image">
			<img src="http://local.groove-online.com/assets/img/profile/user/default/default.jpg">
		</div>
		<div class="main_name"></div>

		<?php if (isset($this->arr_error['artist_name'])):?>
			<br />
			<?php echo $this->arr_error['artist_name']; ?>
		<?php endif;?>

		<!-- 検索結果 -->
		<div id='artist_selected_div'></div>
		<div id='api_error_response'>error</div>
		<div id="search_more">もっとみる</div>
		<div id="loading_bottom" class="loading">
		<span class="loading" >Loading</span>
		<span class="loading l-1"></span>
		<span class="loading l-2"></span>
		<span class="loading l-3"></span>
		<span class="loading l-4"></span>
		<span class="loading l-5"></span>
		<span class="loading l-6"></span>
		</div>
	</section>

	<section id="confirmed_section">
		<?php echo Form::open(array('action' => 'artist/regist/'. $this->to. '/', 'name' => 'registed_form', 'id' => 'registed_form')). PHP_EOL; ?>
		<!-- 検索結果フォーム -->
		<?php echo Form::hidden('artist_id',          null, array('id' => 'artist_id')). PHP_EOL;?>
		<?php echo Form::hidden('artist_name',        null, array('id' => 'artist_name')). PHP_EOL;?>
		<?php echo Form::hidden('artist_name_api',    null, array('id' => 'artist_name_api')). PHP_EOL;?>
		<?php echo Form::hidden('artist_mbid_itunes', null, array('id' => 'artist_mbid_itunes')). PHP_EOL; ?>
		<?php echo Form::hidden('artist_mbid_lastfm', null, array('id' => 'artist_mbid_lastfm')). PHP_EOL; ?>
		<?php echo Form::hidden('artist_url_itunes',  null, array('id' => 'artist_url_itunes')). PHP_EOL;?>
		<?php echo Form::hidden('artist_url_lastfm',  null, array('id' => 'artist_url_lastfm')). PHP_EOL;?>
		<?php echo Form::hidden('artist_image_url',   null, array('id' => 'artist_image_url')). PHP_EOL;?>
		<?php echo Form::close(). PHP_EOL; ?>
	</section>

	<section id="hidden_section">
		<?php echo Form::hidden('artist_search_url', \Config::get('host.base_url_http'). '/api/artist/search.json', array('id' => 'artist_search_url')). PHP_EOL; ?>
	</section>
</div>
