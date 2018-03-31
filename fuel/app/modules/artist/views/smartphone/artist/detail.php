<?php use Fuel\Core\Form;
echo Asset::css('smartphone/artist/detail.css');
echo Asset::js('jquery.leanModal.min.js');
echo Asset::js('smartphone/artist/detail.js');
?>
<?php if ( ! \Agent::is_smartphone()):?>
<style>
.main_div {
  margin-top: 10px;
  padding: 30px 35px;
}
</style>
<?php endif;?>
<nav class="main_navi">
	<span class="main_navi_title"><?php echo Html::anchor('/artist/search/?artist_name='. urlencode($artist_name), "アーティスト検索"); ?></span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title">アーティスト</span>
	<span class="main_navi_ans" id="main_navi_artist_name">[<?php echo $artist_name; ?>]</span>
	<span id="main_navi_artist_id" style="display:none;"><?php echo $artist_id;?></span>
</nav>

<div id="artist_detail_itunes_link_div" style="text-align: right; margin: 10px auto; max-width: 894px;">
	<a href="<?php echo \Config::get('amazon.url');?>&tag=<?php echo \Config::get('amazon.tracking_id');?>&keywords=<?php echo $artist_name;?>" target="_new"><?php echo Asset::img('review/assocbtn_gray_amazon1._V288606497_.png', array('style' => 'width: 112px; height: 40px;'));?></a>
	&nbsp;
	<a href="<?php echo \Config::get('itunes.url_geo');?>/artist/<?php echo $artist_segment_name;?>/id<?php echo $artist_mbid_itunes;?>?at=<?php echo \Config::get('itunes.affiliate_id'); ?>&app=itunes" target="new_win" id="music_write_itunes_link" class="itunes_link" style="display: inline-block;overflow:hidden;background:url(http://linkmaker.itunes.apple.com/images/badges/en-us/badge_itunes-lrg.svg) no-repeat;width:113px;height:40px;margin-right: 10px;"></a>
</div>

<div class="main_div">
	<table id="artist_detail_table">
		<tr>
			<td id="artist_detail_name">
				<span class="artist_name"><?php echo $artist_name; ?></span>
				<br />
				<span class="go_review">
					&nbsp;<?php echo Html::anchor('/tracklist/create/'. $this->artist_id. '/' , 'お気に入りトラックリストを投稿しよう！', array('rel' => 'external')); ?>
				</span>
				<span class="go_review">
					&nbsp;<?php echo Html::anchor('/review/music/write/'. $this->artist_id. '/' , 'レビュー投稿はこちらから', array('rel' => 'external')); ?>
				</span>
			</td>
			<td id="artist_detail_img">
				<img src="<?php echo $this->artist_image;?>">
			</td>
		</tr>
	</table>

	<div id="artist_detail_favorite_artist_div">
		<span id="artist_detail_favorite_artist_title"><?php echo $artist_name;?>を<br />お気に入りアーティストに登録</span>
	<?php if (isset($user_id)):?>
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

	<table id="artist_detail_album_table">
		<tr>
			<td id="artist_detail_album_img">
<?php foreach ($this->arr_album_list as $i => $val): ?>
<?php if ( ! empty($val['name'])): ?><span class="album_image"><?php echo Form::hidden('album_id_input', $val['id'], array('class' => 'album_id_input'));?>
<?php echo Form::hidden('album_name_input', $val['name'], array('class' => 'album_name_input'));?>
<?php echo Form::hidden('album_mbid_itunes_input', $val['mbid_itunes'], array('class' => 'album_mbid_itunes_input'));?>
<?php echo Form::hidden('album_mbid_lastfm_input', $val['mbid_lastfm'], array('class' => 'album_mbid_lastfm_input'));?>
<?php echo Form::hidden('album_url_itunes_input', $val['url_itunes'], array('class' => 'album_url_itunes_input'));?>
<?php echo Form::hidden('album_image_input', $val['image_extralarge'], array('class' => 'album_image_input'));?>
<?php echo Form::hidden('is_smartphone', \Agent::is_smartphone(), array('id' => 'is_smartphone')); ?>
<img class="art_small" src="<?php echo $val['image_medium']; ?>" title="<?php echo $val['name']; ?>"></span><?php endif;?>
<?php endforeach;?>
			</td>
		</tr>
		<tr >
			<td>
				<?php if (count($this->arr_album_list) >= 20):?>
				<div id="music_write_more_album_area" style="text-align: center; margin-top: 15px;">
					<a id="music_write_more_album_link">もっとみる</a>
				</div>
				<?php endif;?>
			</td>
		</tr>
		<tr>
			<td>
				<div id="music_write_album_append_hidden_area">
					<?php echo Form::hidden('music_write_album_id', null, array('id' => 'music_write_album_id'));?>
				</div>
				<div id="music_write_hover_album_main_div">
					<div id="music_write_hover_album_name"></div>
					<div id="music_write_hover_album_action"></div>
					<div id="music_write_hover_album_art"></div>
				</div>
			</td>
		</tr>
	</table>

	<br />

	<table id="artist_detail_album_selected">
		<tr>
			<td id="artist_detail_album_selected_image_td">
				<div id="music_write_album_selected_image" class="">
					<span id="music_write_album_selected_image_span">
						<?php if ( ! empty($this->album_image_middle)): ?>
						<img src="<?php echo $this->album_image_middle; ?>">
						<?php endif; ?>
					</span>
				</div>
			</td>
			<td style="vertical-align: top; padding-top: 10px;">
				<div id="music_write_album_title">
					<strong><span id="music_write_album_name_disp"></span></strong>
					<br />
					<span id="music_write_album_release"></span>
					<br />
					<span id="music_write_album_copyright"></span>
				</div>
			</td>
		<tr>
			<td style="vertical-align: bottom;" colspan="2">
			</td>
		</tr>
	</table>

	<table id="artist_detail_album_song_list">
		<tr>
			<td id="music_write_music_area" colspan="2">
				<div id="all_play_div">
					<a class="ui-btn ui-btn-inline ui-btn-b ui-mini" id="review_music_write_listen_all">連続再生</a>
					<div id="current_div">
						<div id="current_track"></div>
						<div id="current_time"></div>
					</div>
				</div>
				<br />
				<div id="music_write_album_selected_tracks_area" class="">
				<ul id="music_write_album_selected_tracks" data-role="listview" data-inset="false"></ul>
				</div>
			</td>
		</tr>
	</table>

	<br />

	<div id="about_detail_tracklist_div" class="review_list_div">
		<nav class="review_list_navi">
			<span class="review_list_navi_title"><?php echo Html::anchor('/tracklist/artist/'. $artist_id. '/', 'トラックリスト一覧['. $artist_name. ']'); ?></span>
		</nav>
		<h3 class="review_list_title">最新トラックリスト投稿&nbsp;<span style="font-size: x-small; letter-spacing: 0px;">最新５件まで表示</span></h3>
		<?php if (empty($arr_tracklist)):?>
			<div class="review_list_none">
			<?php echo Html::anchor('/tracklist/create/'. $this->artist_id. '/' , '投稿はまだございません。<br />お気に入リのトラックリストを投稿してみませんか？', array('rel' => 'external')); ?>
			</div>
		<?php else:?>
			<ul data-role="listview" class="review_list_ul">
			<?php foreach ($arr_tracklist as $i => $val):?>
				<li id="tracklist_detail_id_<?php echo $val->id;?>" class="review_list_li tracklist_list_li">
					<div class="tracklist_list_title"><?php echo $val->title;?></div>
					<div class="tracklist_list_created_at"><?php echo $val->created_at;?></div>
					<ol class="tracklist_list_tracks">
						<?php foreach ($val->arr_tracks_list as $j => $track):?>
							<li>
								<span class="list_track_name"><?php echo $track->track_name;?></span>
								<span class="list_artist_name"><?php echo $track->track_artist_name;?></span>
							</li>
						<?php endforeach;?>
					</ol>
					<?php if ($val->is_tracks_and_more):?>
						<div style="font-size:small; color: #333; margin-left: 7px; margin-top: 5px; margin-bottom: 12px;">and more ・・・</div>
					<?php else:?>
						<div style="font-size:small; color: #333; margin-left: 0px;">&nbsp;</div>
					<?php endif;?>
					<div class="tracklist_list_user">
						<span>by <?php echo $val->user_name;?>さん</span>
						<span><?php echo Html::img($val->user_image, array('alt' => $val->user_name));?></span>
					</div>
				</li>
			<?php endforeach;?>
			</ul>
		<?php endif;?>
	</div>
	<?php if ($arr_tracklist):?>
		<div class="review_list_create_link"><?php echo Html::anchor('/tracklist/create/'. $this->artist_id. '/', 'お気に入りトラックを投稿しよう！', array('rel' => 'external')); ?></div>
	<?php endif;?>

	<br />
	<br />
	<br />

	<div id="about_detail_review_div" class="review_list_div">
		<nav class="review_list_navi">
			<span class="review_list_navi_title"><?php echo Html::anchor("/review/music/artist/". $artist_id. "/", "レビュー一覧[". $artist_name. "]"); ?></span>
		</nav>
		<h3 class="review_list_title">最新レビュー投稿&nbsp;<span style="font-size: x-small; letter-spacing: 0px;">最新５件まで表示</span></h3>
		<?php if (empty($arr_reviewlist)):?>
			<div class="review_list_none">
			<?php echo Html::anchor('/review/music/write/'. $this->artist_id. '/' , '投稿はまだございません。<br />このアーティストについてレビュー投稿してみませんか？', array('rel' => 'external')); ?>
			</div>
		<?php else:?>
			<ul data-role="listview" class="review_list_ul">
				<?php foreach ($arr_reviewlist as $i => $arr_list): ?>
				<li class="review_list_li">
					<div class="review_list_image"><img src="<?php echo $arr_list->artist_image;?>"></div>
					<div class="about"><?php echo $arr_list->about; ?></div>
					<div class="about_name"><?php echo $arr_list->about_name;?></div>
					<div class="star">
						<?php for($i=0; $i<$arr_list->star; $i++): ?>★<?php endfor;?>
					</div>
					<div class="review">
						<?php echo mb_strimwidth($arr_list->review, 0, 80, '...');?>
						<span class="updated_at"><?php echo preg_replace('/:[\d]*$/', '', $arr_list->updated_at); ?>	</span>
					</div>
					<span class="review_id"><?php echo $arr_list->review_id;?></span>
					<div class="user">
						<span class="name">by <?php echo mb_strimwidth($arr_list->review_user_name, 0, 35, '...');?>さん</span>
						<span><?php echo Html::img($arr_list->review_user_image, array('alt' => ''));?></span>
					</div>
				</li>
				<?php endforeach;?>
			</ul>
		<?php endif;?>
	</div>
	<?php if ($arr_reviewlist):?>
		<div class="review_list_create_link"><?php echo Html::anchor('/review/music/write/'. $this->artist_id. '/', 'レビュー投稿はこちらから！', array('rel' => 'external')); ?></div>
	<?php endif;?>

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
		<br />
		<br />
		<span class="qr_description">読み取るとこのページが表示されます</span>
		<br />
		<img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>">
	</div>
</div>


<!-- トラックリスト一覧 -->
<a href="#tracklist_detail" rel="leanModal" id="tracklist_detail_link_to_leanModal"></a>
	<div id="tracklist_detail" class="modaldiv modaldiv_pc<?php ( ! \Agent::is_smartphone())? 'modaldiv modaldiv_pc': 'modaldiv modaldiv_smartphone'; ?>">
		<section id="tracklist_detail_section">
			<div id="tracklist_detail_header">
				<span id="tracklist_detail_header_close_btn" title="閉じる">X</span>
			</div>
			<div id="tracklist_detail_ul_div">
				<ul id="tracklist_detail_ul" class="ui-listview" data-inset="false" data-role="listview"></ul>
			</div>
			<div id="tracklist_detail_description_play">再生ボタンがあるトラックが視聴可能です。</div>
			<div id="tracklist_detail_control_panel">
				<span id="tracklist_detail_all_play_btn_span">
					<a class="ui-btn ui-btn-inline ui-btn-b ui-mini" id="tracklist_detail_all_play_btn">連続再生</a>
				</span>
				<span id="tracklist_detail_current_track_display"></span>
				<span id="tracklist_detail_time_display">00.00</span>
			</div>
			<h3 id="tracklist_detail_title"></h3>
			<h5 id="tracklist_detail_user_name"></h5>
			<div id="tracklist_detail_created_at"></div>
		</section>
	</div>

<?php echo Form::hidden('user_id', $user_id);?>
<?php echo Form::hidden('artist_id', $artist_id);?>
<?php echo Form::hidden('tracklist_id', $tracklist_id);?>