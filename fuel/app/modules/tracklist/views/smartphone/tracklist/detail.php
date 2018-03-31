<?php echo Asset::js('smartphone/tracklist/detail.js'); ?>
<?php echo Asset::css('smartphone/tracklist/detail.css'); ?>
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
		<span class="main_navi_title"><?php echo Html::anchor('/tracklist/artist/'. $artist_id. '/', 'トラックリスト ['. $artist_name.']');?></span>
		<span class="main_navi_child">></span>
	<?php else:?>
		<span class="main_navi_title"><?php echo Html::anchor('/tracklist/', 'トラックリスト');?></span>
		<span class="main_navi_child">></span>
	<?php endif;?>
	<span class="main_navi_title"><?php echo $tracklist_user_name;?>さん</span>
</nav>

<div id="artist_detail_itunes_link_div">
	<a href="<?php echo \Config::get('amazon.url');?>&tag=<?php echo \Config::get('amazon.tracking_id');?>&keywords=<?php echo $artist_name;?>" target="_new"><?php echo Asset::img('review/assocbtn_gray_amazon1._V288606497_.png', array('style' => 'width: 112px; height: 40px;'));?></a>
	&nbsp;
	<a href="<?php echo \Config::get('itunes.url_geo');?>/artist/<?php echo $artist_segment_name;?>/id<?php echo $artist_mbid_itunes;?>?at=<?php echo \Config::get('itunes.affiliate_id'); ?>&app=itunes" target="new_win" id="music_write_itunes_link" class="itunes_link" style="display: inline-block;overflow:hidden;background:url(http://linkmaker.itunes.apple.com/images/badges/en-us/badge_itunes-lrg.svg) no-repeat;width:113px;height:40px;margin-right: 10px;"></a>
</div>

<div class="tracklist_make_content main_div">
	<!-- トラックリスト一覧 -->
	<a href="#tracklist_detail" rel="leanModal" id="tracklist_detail_link_to_leanModal"></a>
	<div id="tracklist_detail" class="<?php echo( ! \Agent::is_smartphone())? 'modaldiv modaldiv_pc': 'modaldiv modaldiv_smartphone'; ?>">
	<section id="tracklist_detail_section">
		<table class="main_table">
			<?php if ( ! empty($artist_name)):?>
			<tr>
				<td rowspan="2" class="artist_area">
					<img class="tracklist_artist_image" src="<?php echo $artist_image;?>">
					<div class="tracklist_artist_name"><?php echo Html::anchor('/artist/detail/'. $artist_id. '/', $artist_name);?></div>
				</td>
			</tr>
			<?php endif;?>
			<tr>
				<td class="review_area">
					<div>タイトル</div>
					<div id="tracklist_detail_title">
						<div>
							<span id='tracklist_title_display'>
								<?php echo $tracklist_title;?>
							</span>
							<?php if ( ! empty($login_user_id) and ($tracklist_user_id === $login_user_id)):?>
								<div id='tracklist_detail_btn'>
									<a id='tracklist_detail_updbtn'><span>[更新]</span></a>
									<a id='tracklist_detail_delbtn'><span>[削除]</span></a>
								</div>
							<?php endif;?>
						</div>
					</div>
					<h5 id="tracklist_detail_user_name">
						<?php if ( ! empty($tracklist_user_id)):?>
							by <?php echo Html::anchor('/user/you/'. $tracklist_user_id. '/', $tracklist_user_name);?>さん
						<?php else:?>
							by <?php echo $tracklist_user_name;?>さん
						<?php endif;?>
					</h5>
					<div id="tracklist_detail_created_at"><?php echo $tracklist_created_at;?> &nbsp;&nbsp;[created]</div>
					<?php if ($tracklist_created_at != $tracklist_updated_at):?>
						<div id="tracklist_detail_updated_at"><?php echo $tracklist_updated_at;?> [modified]</div>
					<?php endif;?>
					</td>
			</tr>
		</table>
		<div>
			<span id="all_play_btn_span">
				<a class="ui-btn ui-btn-inline ui-btn-b ui-mini" id="tracklist_detail_all_play_btn">連続再生</a>
			</span>
			<span id="tracklist_current_track_display"></span>
			<span id="tracklist_time_display">00.00</span>
		</div>
		<div id="tracklist_description_play">再生ボタンがあるトラックが視聴可能です。</div>
		<div style="background: #666; margin-bottom: 10px; min-height: 370px;">
			<div id="tracklist_detail_ul_div">
				<ul id="tracklist_detail_ul" class="ui-listview" data-inset="false" data-role="listview">
				<?php foreach ($arr_tracklist as $i => $val):?>
					<li class='ui-li-static'>
						<span class='tracklist_play_btn play_mark' title='プレイ！'>▶️</span>
						<audio class='preview_tracklist'>
							<source src='<?php echo $val->preview_itunes;?>' />
							<a href='<?php echo $val->preview_itunes;?>' target='new_win'>▶️</a>
						</audio>
						<span><?php echo $val->sort;?></span>
						<span><?php echo $val->track_name;?></span>
						<span class="tracklist_list"><?php echo Html::anchor('/artist/detail/'. $val->track_artist_id. '/', $val->track_artist_name);?></span>
					</li>
				<?php endforeach;?>
				</ul>
			</div>
		</div>

		<div class="oauth_main_div" style="clear: both;">
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
	</section>
	</div>

	<br />

	<div id="tracklist_create_link">
		<?php if (empty($artist_id)): ?>
			<?php echo Html::anchor('/tracklist/create/', 'トラックリストを作成してみませんか？');?>
		<?php else: ?>
			<?php echo Html::anchor('/tracklist/create/'. $artist_id. '/', 'トラックリストを作成してみませんか？');?>
		<?php endif;?>
	</div>

	<br />
	<br />

</div>

<?php echo Form::hidden('tracklist_user_id', $tracklist_user_id, array('id' => 'tracklist_user_id')). PHP_EOL;?>
<?php echo Form::hidden('login_user_id', $login_user_id, array('id' => 'login_user_id')). PHP_EOL;?>
<?php echo Form::hidden('artist_id', $artist_id, array('id' => 'artist_id')). PHP_EOL;?>
<?php echo Form::hidden('tracklist_id', $tracklist_id, array('id' => 'tracklist_id')). PHP_EOL;?>

