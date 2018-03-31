<?php echo Asset::js('smartphone/artist/search.js'); ?>
<?php echo Asset::css('smartphone/artist/search.css');?>

<nav class="main_navi">
	<span class="main_navi_title"><?php echo Html::anchor('/', "トップ", array('data-role' => 'none')); ?></span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title">アーティスト検索</span>
	<span class="main_navi_ans"></span>
</nav>

<div class="artist_search_content main_div">
	<p class="description">アーティスト検索<br />
	<span style="font-size: small;">アルバム無料視聴やそのレビューができます。</span></p>
	<!-- フォーム -->
	<?php echo Form::open(array('id' => 'search_form')). PHP_EOL; ?>
		<div data-role="fieldcontain" class="ui-hide-label">
			<label for="artist_search_input">アーティスト検索</label>
			<?php echo Form::input('artist_search_input', $artist_name, array('type' => 'search', 'id' => 'artist_search_input', 'name' => 'artist_search_input', 'placeholder' => 'アーティスト名')); ?>
			<br />
			<input type="button" value="アーティストを検索する" id="artist_search_search_button" class="ui-btn ui-icon-search ui-btn-icon-left">
			<br />
			<div id="missing_div"></div>
		</div>
	<?php echo Form::close(). PHP_EOL; ?>

	<!-- loading表示 -->
	<?php echo $this->loading_top;?>

	<div class="main_name"></div>

	<!-- 検索結果 -->
	<ul data-role="listview" id='artist_selected_ul'></ul>

	<br />
	<br />

	<div id='api_error_response'>error</div>
	<div id="search_more">
		<a href="#" class="">もっとみる</a>
	</div>

	<!-- loading表示 -->
	<?php echo $this->loading_bottom;?>

	<h3 id="artist_search_get_new_title">最近グルーヴオンラインで検索されたアーティストはこちら</h3>
	<ul id="artist_search_get_new_ul" class="ui-listview" data-role="listview">
		<?php foreach ($arr_last_search as $i => $val):?>
			<li class="ui-li-has-thumb ui-first-child ui-last-child">
				<a class="ui-btn ui-btn-icon-right ui-icon-carat-r">
					<img src="<?php echo $val->artist_image;?>" data-original="<?php echo $val->artist_image;?>">
					<strong class="list_artist_name"><?php echo $val->artist_name;?></strong>
				</a>
				<input class="append_input_id" type="hidden" value="<?php echo $val->artist_id;?>">
			</li>
		<?php endforeach;?>
	</ul>

	<?php if ( ! empty($last_search_more_flg)):?>
	<div id="artist_search_get_more_new"><?php echo Html::anchor('#', 'もっと見る', array('id' => 'artist_search_get_more_new_a'));?></div>
	<?php endif;?>

	<section id="confirmed_section">
		<?php echo Form::open(array('action' => "artist/regist/{$this->to}/", 'name' => 'registed_form', 'id' => 'registed_form', 'data-ajax' => 'false')). PHP_EOL; ?>
		<?php echo Form::hidden('artist_id', null, array('id' => 'artist_id')). PHP_EOL;?>
		<?php echo Form::close(). PHP_EOL; ?>
	</section>

	<section id="hidden_section">
		<?php echo Form::hidden('artist_search_url', \Config::get('host.base_url_http'). '/api/artist/search.json', array('id' => 'artist_search_url')). PHP_EOL; ?>
		<?php echo Form::hidden('last_search_limit', \Config::get('artist.last_search_limit', 10), array('id' => 'last_search_limit'));?>
	</section>

	<br />

	<div class="qr_div">
		<div class="qr_description">読み取るとこのページが表示されます</div>
		<img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>" data-original="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>">
	</div>


</div>
