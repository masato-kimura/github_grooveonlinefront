<?php use Fuel\Core\Form;
echo Asset::css('smartphone/tracklist/artist.css');
echo Asset::js('smartphone/tracklist/artist.js');
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
	<span class="main_navi_title"><?php echo Html::anchor('/tracklist/', 'トラックリスト');?></span>
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
	<section id="tracklist_artist_section">
		<h3 id="tracklist_artist_h">
			<span>トラックリスト投稿一覧</span><br />
			<span id="tracklist_artist_name"><?php echo $artist_name;?></span>
			<span id="tracklist_artist_pagelink"><?php echo Html::anchor('/artist/detail/'. $artist_id. '/', 'アーティストページへ');?></span>
		</h3>
		<div id="tracklist_artist_image_div">
			<img src="<?php echo $artist_image; ?>">
		</div>
	</section>

	<section id="tracklist_review_section">
		<div id="tracklist_count">全<span class="all_count"><?php echo $tracklist_count;?></span>件</div>
		<div id="tracklist_pagination_div" class="pagination_div"></div>
		<div id="about_detail_tracklist_div" class="review_list_div">
			<h3 class="review_list_title">最新トラックリスト投稿</h3>
			<ul data-role="listview" class="review_list_ul">
			<?php if (empty($arr_tracklist)):?>
				<li class="review_list_none">
				<?php echo Html::anchor('/tracklist/create/'. $this->artist_id. '/' , '投稿はまだございません。<br />お気に入リのトラックリストを投稿してみませんか？', array('rel' => 'external')); ?>
				</li>
			<?php else:?>
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
							<div class="tracklist_list_and_more">and more ・・・</div>
						<?php else:?>
							<div class="tracklist_list_and_more">&nbsp;</div>
						<?php endif;?>
						<div class="tracklist_list_user">
							<span>by <?php echo $val->user_name;?>さん</span>
							<span><?php echo Html::img($val->user_image, array('alt' => $val->user_name));?></span>
						</div>
					</li>
				<?php endforeach;?>
			<?php endif;?>
			</ul>
		</div>

		<div id="tracklist_pagination_div_footer" class="pagination_div"></div>
		<?php if ($arr_tracklist):?>
			<div class="review_list_create_link"><?php echo Html::anchor('/tracklist/create/'. $this->artist_id. '/', 'お気に入りトラックリストを投稿しよう！', array('rel' => 'external')); ?></div>
		<?php endif;?>
	</section>

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

<?php echo Form::hidden('user_id', $user_id, array('id' => 'user_id'));?>
<?php echo Form::hidden('artist_id', $artist_id);?>
<?php echo Form::hidden('tracklist_id', $tracklist_id);?>
<?php echo Form::hidden('page_offset', $page_offset, array('id' => 'page_offset'));?>