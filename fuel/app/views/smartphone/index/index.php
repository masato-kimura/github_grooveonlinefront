<?php echo Asset::js('smartphone/index/index.js'); ?>
<?php echo Asset::css('smartphone/index/index.css');?>

<?php if ( ! \Agent::is_smartphone()):?>
	<style>
	.main_div {
	  background-size: auto;
	  background-position: center 170px;
	  padding: 0px 35px;
	}
	</style>
<?php endif;?>

<div class="main_div">
	<div class="index_main_title_div">
		<span class="index_main_title">GROOVE-ONLINE</span>
	</div>
	<h3 class="index_sub_title"><strong>グルーヴオンライン</strong>  <span class="bata"><i>Beta</i></span></h3>
	<nav id="index_artist_search_link">
		<h4 class="search_link_1"><?php echo Html::anchor('artist/search/', '大好きなアーティストについて書いてみませんか？');?></h4>
		<h4 class="search_link_2"><?php echo Html::anchor('artist/search/', '気になるあの曲も検索して視聴できる');?></h4>
		<h2 class="search_link_3"><?php echo Html::anchor('artist/search/', 'アルバムも無料で<br />ダイジェスト再生！');?></h2>
	</nav>

	<article class="article_list_section" id="about_detail_tracklist_div">
		<h3 class="review_title"><?php echo html::anchor('/tracklist/', '新着トラックリスト'); ?></h3>
		<ul class="review_list_ul">
		<?php foreach ($arr_tracklist as $i => $val):?>
			<li id="tracklist_detail_id_<?php echo $val->id;?>" class="review_list_li">
				<div class="tracklist_list_title"><?php echo $val->title;?></div>
				<ol class="tracklist_list_tracks">
					<?php foreach ($val->arr_tracks_list as $j => $track):?>
						<li>
							<span class="list_track_name"><?php echo $track->track_name;?></span>
							<span class="list_artist_name"><?php echo $track->track_artist_name;?></span>
						</li>
					<?php endforeach;?>
				</ol>
				<?php if ($val->is_tracks_and_more):?>
					<div class="tracklist_list_and_more">and more ・・・</div>
				<?php endif;?>
				<div class="article_list_footer">
					<div class="article_list_at"><?php echo \Date::forge(strtotime($val->created_at))->format('%Y-%m-%d');?></div>
					<div class="article_list_user">
						<span>by <?php echo $val->user_name;?>さん</span>
					</div>
				</div>
			</li>
		<?php endforeach;?>
		</ul>
		<p class="article_section_more_link"><?php echo Html::anchor('/tracklist/', 'もっとみる');?></p>
	</article>

	<article id="index_top_review">
		<h5 style="margin-bottom: 5px;">注目のレビュー</h5>
		<?php foreach($this->top_review_music as $i => $val):?>
			<?php if($i > 2) break;?>
			<h3 id="index_top_review_music_<?php echo $i;?>">
				<a href="/review/music/detail/<?php echo $val->about;?>/<?php echo $val->id;?>/">
					<span class="attention_1">
						<?php echo mb_strimwidth($val->about_name, 0, 30, ' ...');?>
					</span>
					<?php if ($val->about != 'artist'):?>
					<span class="attention_2">
						<?php echo mb_strimwidth($val->artist_name, 0, 50, ' ...'); ?>
					</span>
					<?php endif;?>
					<span class="annotation">
						<?php echo mb_strimwidth($val->review, 0, 42, ' ...');?>
					</span>
				</a>
			</h3>
		<?php endforeach;?>
	</article>

	<br />

	<article class="article_list_section" id="about_detail_reviewlist_div">
		<table class="review_list_table">
			<tr>
				<th colspan="2">
					<h3 class="review_title"><?php echo html::anchor('/review/music/', '新着レビュー'); ?></h3>
				</th>
			</tr>
			<?php foreach ($this->review_music as $i => $val): ?>
				<?php
					$image = $val->image_medium;
					if (empty($image))
					{
						$image = $val->artist_image_medium;
					}
				?>
				<tbody class="review_list_tbody" id="index_review_list_tbody_<?php echo "{$val->about}_{$val->id}" ?>">
				<tr class="review_list_tr">
					<td class="review_image" rowspan="3"><?php echo Html::img($image, array('alt' => '', 'data-original' => $image));?></td>
					<td class="review">
						<span class="about"><?php echo $val->about; ?></span>
						<span class="about_name"><strong><?php echo mb_strimwidth($val->about_name, 0, 35, '...');?></strong></span>
						<span class="star"><?php for($i=0; $i<$val->star; $i++): ?>★<?php endfor;?></span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="about_review"><?php echo Html::anchor("/review/music/detail/{$val->about}/{$val->id}/", mb_strimwidth($val->review, 0, 65, '...')); ?></span>
					</td>
				</tr>
				<tr class="review_area_tr">
					<td>
						<span class="article_list_at">
							<i><?php echo preg_replace('/:[\d]*$/', '', $val->created_at); ?></i>
						</span>
						<span class="article_list_user">by&nbsp;<?php echo mb_strimwidth($val->user_name, 0, 30, ' ..');?>さん</span>
					</td>
				</tr>
				</tbody>
			<?php endforeach;?>
		</table>
		<p class="article_section_more_link"><?php echo Html::anchor('/review/music/', 'もっとみる');?></p>
	</article>

	<br />

	<?php if ( ! \Agent::is_smartphone()):?>
		<div style="text-align:right;">
			<iframe src="http://rcm-fe.amazon-adsystem.com/e/cm?t=grooveonlin0c-22&o=9&p=42&l=ur1&category=primemusic&banner=1ZYWWHCKHBGP5KSN1P82&f=ifr" width="234" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>
			<br />
			<span class="pr" style="font-size: x-small;display: block; color: #777; margin-right: 10px; margin-top: -3px;">PR</span>
		</div>
	<?php else:?>
		<div style="text-align:center;">
			<iframe src="http://rcm-fe.amazon-adsystem.com/e/cm?t=grooveonlin0c-22&o=9&p=42&l=ur1&category=primemusic&banner=1ZYWWHCKHBGP5KSN1P82&f=ifr" width="234" height="60" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>
			<br />
			<span class="pr" style="font-size: x-small;display: block; color: #777; margin-right: 10px; margin-top: -3px;">PR</span>
		</div>
	<?php endif;?>

	<br />

	<article class="weekly_rank_track_section article_ranking_section">
		<table class="weekly_rank_track_list_table">
			<tr>
				<th colspan="2" class="rank_title"><h4>週間トラックランキング</h4></th>
			</tr>
			<tr>
				<td colspan="2" class="rank_while">
					<?php echo $weekly_rank_from;?> ～ <?php echo $weekly_rank_to;?>
				</td>
			</tr>
			<?php if (empty($weekly_rank_track)):?>
				<tbody class="weekly_rank_track_list_tbody" >
					<tr class="weekly_rank_track_list_tr">
						<td class="weekly_rank_track">
							<br />今週のランキングはありません。次週までお待ちください。
							<br />
							<br />
						</td>
					</tr>
				</tbody>
			<?php else:?>
				<?php foreach ($weekly_rank_track as $i => $val): ?>
					<tbody class="weekly_rank_track_list_tbody" id="index_weekly_rank_track_tbody_<?php echo "{$val->track_id}"; ?>">
					<tr class="weekly_rank_track_list_tr">
						<td class="weekly_rank_track_image"><?php echo Html::img($val->image_medium, array('alt' => '', 'data-original' => $val->image_medium));?></td>
						<td class="weekly_rank_track">
							<a href="/track/detail/<?php echo "{$val->track_id}"; ?>">
							<span class="rank"><?php echo "{$val->rank}"; ?>位</span>
							<span class="rank_track_name"><?php echo "{$val->track_name}";?></span>
							<span class="rank_artist_name"><?php echo "{$val->artist_name}";?></span>
							</a>
						</td>
					</tr>
					</tbody>
				<?php endforeach;?>
			<?php endif;?>
		</table>
		<div class="weekly_rank_next">次の集計は<?php echo $weekly_rank_next;?> 深夜1:00予定です</div>
	</article>

	<br />
	<br />

	<article class="weekly_rank_album_section article_ranking_section">
		<table class="weekly_rank_album_list_table">
			<tr>
				<th colspan="2" class="rank_title"><h4>週間アルバムランキング</h4></th>
			</tr>
			<tr>
				<td colspan="2" class="rank_while"><?php echo \Date::forge(time() - 60 * 60 * 24 * 7)->format('%Y/%m/%d');?> ～ <?php echo \Date::forge()->format('%Y/%m/%d');?></td>
			</tr>
			<?php if (empty($weekly_rank_album)):?>
				<tbody class="weekly_rank_album_list_tbody" >
					<tr class="weekly_rank_album_list_tr">
						<td class="weekly_rank_album">
							<br />今週のランキングはありません。次週までお待ちください。
							<br />
							<br />
						</td>
					</tr>
				</tbody>
			<?php else:?>
				<?php foreach ($weekly_rank_album as $i => $val): ?>
					<tbody class="weekly_rank_album_list_tbody" id="index_weekly_rank_album_tbody_<?php echo "{$val->album_id}"; ?>">
					<tr class="weekly_rank_album_list_tr">
						<td class="weekly_rank_album_image"><?php echo Html::img($val->image_medium, array('alt' => '', 'data-original' => $val->image_medium));?></td>
						<td class="weekly_rank_album">
							<a href="/album/detail/<?php echo "{$val->album_id}"; ?>">
							<span class="rank"><?php echo "{$val->rank}"; ?>位</span>
							<span class="rank_album_name"><?php echo "{$val->album_name}";?></span>
							<span class="rank_artist_name"><?php echo "{$val->artist_name}";?></span>
							</a>
						</td>
					</tr>
					</tbody>
				<?php endforeach;?>
			<?php endif;?>
		</table>
		<div class="weekly_rank_next">次の集計は<?php echo $weekly_rank_next;?> 深夜1:00予定です</div>
	</article>

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
<?php if (\Agent::is_smartphone() or \Agent::is_mobiledevice()):?>
	<div class="qr_div" style="text-align: center;">
<?php else:?>
	<div class="qr_div" style="text-align: right;">
<?php endif;?>
		<span class="qr_description">読み取るとこのページが表示されます</span>
		<br />
		<img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). \Uri::main();?>" data-original="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). \Uri::main();?>">
	</div>

	<br />

	<div class="bottom_div">
		<?php echo \Session::get_flash('error');?>
	</div>

</div>


<?php if (isset($arr_user_info_from_session['is_first_regist']) && $arr_user_info_from_session['is_first_regist'] == true):?>
<?php echo Form::hidden('is_first_regist', true, array('id' => 'index_is_first_regist'));?>
<?php endif;?>
