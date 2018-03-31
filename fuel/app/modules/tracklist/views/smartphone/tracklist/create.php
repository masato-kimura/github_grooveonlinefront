<?php echo Asset::js('smartphone/tracklist/create.js'); ?>
<?php echo Asset::js('jquery.leanModal.min.js'); ?>
<?php echo Asset::css('smartphone/tracklist/create.css'); ?>

<?php if ( ! \Agent::is_smartphone()):?>
<style>
.main_div {
  margin-top: 10px;
  padding: 10px 35px;
}
</style>
<?php endif;?>

<nav class="main_navi">
	<?php if ( ! empty($artist_id)):?>
		<span class="main_navi_title"><?php echo Html::anchor('/artist/detail/'. $artist_id. '/', 'アーティスト ['. $artist_name.']');?></span>
		<span class="main_navi_child">></span>
	<?php endif;?>
	<span class="main_navi_title">トラックリスト作成</span>
</nav>

<div class="tracklist_make_content main_div">
	<p id="tracklist_make_title">トラックリスト作成フォーム</p>
	<div id="to_select_caption">アーティスト名を検索して選択してください</div>
	<!-- アーティスト名入力 -->
	<div id="tracklist_make_artist_search_section">
		<?php echo Form::input('artist_search_input', $artist_name, array('type' => 'search', 'id' => 'artist_search_input', 'name' => 'artist_search_input', 'placeholder' => 'アーティスト名検索')); ?>
		<input type="button" value="アーティストを検索する" id="artist_search_button" class="ui-btn ui-icon-search ui-btn-icon-left ui-btn-b">
		<br />
	</div>

	<!-- アーティスト検索結果 -->
	<ul data-role="listview" id='artist_selected_ul'></ul>
	<div id="tracklist_make_artist_search_more"><a>もっとみる</a></div>
	<div id="tracklist_make_artist_not_exist_disp">検索結果は0件です。</div>
	<?php echo $this->loading_artist_search;?>

	<!-- アーティスト情報表示 -->
	<div id="tracklist_make_artist_section">
		<div id="tracklist_make_artist_image"><img src="<?php echo $artist_image;?>"></div>
		<div id="tracklist_make_artist_name"><?php echo $artist_name; ?></div>
	</div>

	<!-- アルバムアート一覧表示 -->
	<br />
	<div id="tracklist_make_album_art_section">
		<div id="tracklist_make_album_description" class="description">※ 下のアルバムまたはシングルを選択して、その中からトラック(楽曲)をリストに追加してください。</div>
		<div id="tracklist_make_album_append_area"></div>
		<div id="tracklist_make_album_art_more"><a>もっとみる</a></div>

		<div id="tracklist_make_hover_album_main_div">
			<div id="tracklist_make_hover_album_name"></div>
			<div id="tracklist_make_hover_album_action"></div>
			<div id="tracklist_make_hover_album_art"></div>
		</div>
	</div>
	<div id="tracklist_make_album_not_exist_disp">検索結果は0件です。</div>
	<?php echo $this->loading_album_search;?>


	<!-- アルバムトラック一覧 -->
	<div id="tracklist_make_album_section">
		<div id="tracklist_make_album_selected_image">
			<span id="tracklist_make_album_selected_image_span"></span>
		</div>
		<div id="tracklist_make_album_detail">
			<strong>
			<span id="tracklist_make_album_name_disp"></span>
			</strong>
			<br />
			<span id="tracklist_make_album_release"></span>
			<br />
			<span id="tracklist_make_album_copyright"></span>
		</div>
		<div id="tracklist_make_album_selected_tracks_area" class="">
			<div id="tracklist_make_album_selected_tracks_description">(曲名をタップするとリストに追加されます)</div>
			<ul id="tracklist_make_album_selected_tracks" class="ui-listview" data-role="listview"></ul>

		</div>
		<br />
		<span id="tracklist_make_album_scroll_header"></span>
	</div>
	<?php echo $this->loading_track_search;?>


	<!-- トラックリストの表示 -->
	<div id="tracklist_make_confirm"><a href="#tracklist_make_list" rel="leanModal" id="tracklist_make_check">作成中のトラックリストを確認する</a></div>
	<a href="#tracklist_make_list" rel="leanModal" id="tracklist_make_link_to_leanModal"></a>

	<br />
	<br />
	<br />

	<h3 id="artist_search_get_new_title">最近グルーヴオンラインで検索されたアーティストはこちら</h3>
	<ul id="artist_search_get_new_ul" class="ui-listview" data-role="listview">
		<?php foreach ($arr_last_search as $i => $val):?>
			<li class="ui-li-has-thumb ui-first-child ui-last-child">
				<a class="ui-btn ui-btn-icon-right ui-icon-carat-r">
					<img src="<?php echo $val->artist_image;?>" data-original="<?php echo $val->artist_image;?>">
					<h3 class="list_artist_name"><?php echo $val->artist_name;?></h3>
				</a>
				<input class="append_input_id" type="hidden" value="<?php echo $val->artist_id;?>">
			</li>
		<?php endforeach;?>
	</ul>

	<?php if ( ! empty($last_search_more_flg)):?>
	<div id="artist_search_get_more_new"><?php echo Html::anchor('#', 'もっと見る', array('id' => 'artist_search_get_more_new_a'));?></div>
	<?php endif;?>

	<br />
	<div class="qr_div">
		<span class="qr_description">読み取るとこのページが表示されます。</span>
		<br />
		<img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>/">
	</div>

</div>

<!-- トラックリスト一覧 -->
<div id="tracklist_make_list" class="<?php echo( ! \Agent::is_smartphone())? 'modaldiv modaldiv_pc': 'modaldiv modaldiv_smartphone';?>">
<section id="tracklist_make_form_top">
	<div id="tracklist_make_header">
		<span id="tracklist_make_header_close_btn" title="閉じる">X</span>
	</div>
	<h5>■ このリストのタイトル</h5>
	<?php echo Form::input('tracklist_title', $tracklist_title, array('type' => 'text', 'id' => 'tracklist_title', 'name' => 'tracklist_title', 'placeholder' => '例) マイベストトラック！'));?>

	<?php if (empty($user_id)):?>
		<h5>■ お名前（ユーザ登録されている方はログインしてください）</h5>
		<?php echo Form::input('tracklist_username', $tracklist_username, array('type' => 'text', 'id' => 'tracklist_username', 'name' => 'tracklist_username', 'placeholder' => 'あなたのお名前'));?>
	<?php endif;?>

	<h5>■ アーティスト</h5>
	<div id="tracklist_make_list_artist_name"><?php echo empty($tracklist_artistname)? '指定なし': $tracklist_artistname;?></div>
	<h5>■ ドラッグアンドドロップで曲順を入れ替えることができます</h5>
	<div id="tracklist_make_list_count_description">ただいま<span id="tracklist_make_list_count">0</span>曲&nbsp;(<span id="tracklist_make_max_count">5</span>曲まで選択可)</div>
</section>
<section id="tracklist_make_form_bottom">
	<div id="tracklist_make_list_updown">
		<span id="tracklist_make_list_up" title="up">▲</span>
		<br /><br /><br />
		<span id="tracklist_make_list_down" title="down">▼</span>
	</div>
	<div id="tracklist_make_list_div">
		<ul id="tracklist_make_list_tracks" class="ui-listview" data-role="listview">
		<?php foreach ($tracklist_arr_list as $i => $val):?>
			<li class="ui-li-static ui-body-inherit">
			<span class="no"><?php echo $val->sort;?>. </span>
			<span class="trackname"><?php echo $val->track_name;?></span>
			<br>
			<span>
				<span class="del_track_list">削除</span> / <span class="track_artistname"><?php echo $val->track_artist_name;?></span>
			</span>
			<input class="trackid" type="hidden" value="<?php echo $val->track_id;?>" />
			<input class="track_artistid" type="hidden" value="<?php echo $val->artist_id;?>">
			<input class="track_albumid" type="hidden" value="<?php echo $val->album_id;?>">
			<input class="track_albumname" type="hidden" value="<?php echo $val->album_name;?>">
			</li>
		<?php endforeach;?>
		</ul>
	</div>
	<?php echo Form::hidden('artist_id', $tracklist_artist_id, array('id' => 'tracklist_make_list_artist_id'));?>
	<div id="tracklist_make_list_submit">
		<a class="ui-btn ui-btn-inline modal_close" id="tracklist_make_list_close_btn" title="keep">さらに追加</a>
		<a class="ui-btn ui-btn-inline ui-btn-b" id="tracklist_make_list_submit_btn" title="submit!">投稿する</a>
	</div>
</section>
</div>


<div id="artist_id" style="visibility: hidden;"><?php echo $artist_id;?></div>
<div id="user_id" style="visibility: hidden;"><?php echo $user_id;?></div>
<div id="edit_mode" style="visibility: hidden;"><?php echo $edit_mode;?></div>
<div id="tracklist_id" style="visibility: hidden;"><?php echo $tracklist_id;?></div>
<?php echo Form::hidden('last_search_limit', \Config::get('artist.last_search_limit', 10), array('id' => 'last_search_limit'));?>

