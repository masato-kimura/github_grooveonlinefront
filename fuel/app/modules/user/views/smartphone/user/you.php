<?php
use login\domain\service\LoginService;
echo Asset::js('smartphone/user/you.js');
echo Asset::css('smartphone/user/you.css');
?>
<?php if ( ! \Agent::is_smartphone()):?>
<style>
.main_div {
  margin-top: 10px;
  padding: 10px 35px;
}
</style>
<?php endif;?>

<nav class="main_navi">
	<span class="main_navi_title"><?php echo $this->user_name; ?>さんのプロフィールページ</span>
</nav>

<div class="main_div" id="useryou_main_div">

	<table id="useryou_user_top">
		<tr>
			<td id="useryou_user_name" class="user_name"><?php echo $user_name; ?></td>
			<td id="useryou_user_image"><?php echo Html::img($user_image, array('alt' => '', 'data-original' => $user_image)); ?></td>
		</tr>
		<?php if (isset($client_user_id)):?>
			<?php if ($user_id != $client_user_id):?>
			<tr>
				<td id="useryou_favorite" colspan="2">
				<span id="useryou_favorite_title"><?php echo $user_name;?>さんを<br />お気に入りユーザーに登録</span>
				<?php if (isset($arr_favorite_user[$user_id])):?>
					<?php echo Form::select('favorite_status', '1', array("0" => '', "1" => '★'), array('data-role' => 'flipswitch'));?>
				<?php else:?>
					<?php echo Form::select('favorite_status', '0', array("0" => '', "1" => '★'), array('data-role' => 'flipswitch'));?>
				<?php endif;?>
				</td>
			</tr>
			<?php endif;?>
		<?php else:?>
			<tr>
				<td id="useryou_favorite" colspan="2">
				<span id="useryou_favorite_title"><?php echo $user_name;?>さんを<br />お気に入りユーザーに登録</span>
				<a id="favorite_status_disabled_anchor" style="display: inline-block;">
					<?php echo Form::select('favorite_status_disabled', '0', array("0" => '☆'), array('data-role' => 'flipswitch', 'disabled' => 'disabled'));?>
				</a>
				</td>
			</tr>
		<?php endif;?>
		<?php if ( isset($client_user_id) and $user_id == $client_user_id):?>
		<tr>
			<td id="useryou_profile_edit" colspan="2"><span><?php echo Html::anchor('login/editregistindex', 'プロフィール編集', array('class' => 'i-link ui-btn ui-btn-icon-right ui-icon-gear ui-btn-inline ui-shadow ui-corner-all'));?></span></td>
		</tr>
		<?php endif;?>
	</table>

	<div id="user_information_div">
		<?php if ( isset($client_user_id) and $user_id == $client_user_id):?>
			<?php if ($review_comment_count):?>
				<div><a id="user_information_comment_link">新着コメントが<?php echo number_format($review_comment_count);?>件あります</a></div>
			<?php endif;?>
		<?php endif;?>
	</div>

	<div id="useryou_profile" class="profile">
		<?php if ( ! empty($this->gender)): ?>
		<div class="title">GENDER:<span class="title_jp">性別</span></div>
		<div class="ans"><?php echo $this->gender; ?></div>
		<?php endif;?>

		<?php if (empty($this->old_secret) && ($this->old)): ?>
		<div class="title">OLD:<span class="title_jp">年齢</span></div>
		<div class="ans"><?php echo $this->old; ?></div>
		<?php endif;?>

		<?php if (empty($this->birthday_secret) && ($this->birthday_day) && ($this->birthday_month)): ?>
			<div class="title">BIRTHDAY:<span class="title_jp">誕生日</span></div>
			<div class="ans">
				<?php if (empty($this->old_secret)): ?>
					<?php echo $this->birthday_year; ?>/
				<?php  endif;?>
				<?php echo $this->birthday_month; ?>/
				<?php echo $this->birthday_day; ?>
			</div>
		<?php endif;?>

		<?php if ( ! empty($this->locale)): ?>
		<div class="title">LOCALE:<span class="title_jp">国</span></div>
		<div class="ans"><?php echo $this->locale; ?></div>
		<?php endif;?>

		<?php if ( ! empty($this->pref)): ?>
		<div class="title">PREF:<span class="title_jp">お住まい地域</span></div>
		<div class="ans"><?php echo $this->pref; ?></div>
		<?php endif;?>

		<?php if ( ! empty($favorite_artists)): ?>
		<div class="title">FAVORITE ARTIST:<span class="title_jp">お気に入りアーティスト</span></div>
		<div class="ans">
			<?php foreach ($favorite_artists as $sort => $val): ?>
				<?php echo Html::anchor("/artist/detail/{$val->artist_id}/", $val->artist_name);?>,&nbsp;
			<?php endforeach;?>
		</div>
		<?php endif;?>

		<?php if ( ! empty($this->profile_fields)): ?>
		<div class="title">PROFILE:<span class="title_jp">自己紹介</span></div>
		<div class="ans">
				<?php echo $profile_fields; ?>
		</div>
		<?php endif;?>

		<?php if ( ! empty($this->facebook_url)):?>
		<div class="title">Facebook:<span class="title_jp">フェイスブック</span></div>
		<div class="ans">
			<a href="<?php echo $facebook_url; ?>" target="_window">
				<?php echo $facebook_url; ?>
			</a>
		</div>
		<?php endif;?>

		<?php if ( ! empty($this->twitter_url)):?>
		<div class="title">Twitter:<span class="title_jp">ツィッター</span></div>
		<div class="ans">
			<a href="<?php echo $twitter_url;?>" target="_window">
				<?php echo $twitter_url; ?>
			</a>
		</div>
		<?php endif;?>

		<?php if ( ! empty($this->google_url)):?>
		<div class="title">Google:<span class="title_jp">グーグル＋</span></div>
		<div class="ans">
			<a href="<?php echo $google_url;?>" target="_window">
			<?php echo $google_url; ?>
			</a>
		</div>
		<?php endif;?>

		<?php if ( ! empty($this->instagram_url)):?>
		<div class="title">instagram:<span class="title_jp">インスタグラム</span></div>
		<div class="ans">
			<a href="<?php echo $instagram_url;?>" target="_window">
			<?php echo $instagram_url; ?>
			</a>
		</div>
		<?php endif;?>

		<?php if ( ! empty($this->site_url)):?>
		<div class="title">BLOG, WEB:<span class="title_jp">ブログ、ウェブサイト</span></div>
		<div class="ans">
			<a href="<?php echo $site_url;?>" target="_window">
			<?php echo $site_url; ?>
			</a>
		</div>
		<?php endif;?>

		<?php if ( ! empty($favorite_users)):?>
		<div class="title">FAVORITE USERS:<span class="title_jp">お気に入りユーザー</span></div>
		<div class="ans favorite_user_div">
				<?php foreach ($favorite_users as $favorite_user_id => $favorite_user_name): ?>
				<div class="favorite_user_div_list">
				<a href="/user/you/<?php echo $favorite_user_id; ?>/" rel="external">
					<?php $favorite_user_image = LoginService::get_user_image_url_small($favorite_user_id);?>
					<span class="favorite_user_div_list_img"><img src="<?php echo $favorite_user_image; ?>" title="<?php echo $favorite_user_id; ?>"></span>
					<span class="favorite_user_div_list_name"><?php echo $favorite_user_name; ?></span>
				</a>
				</div>
				<?php endforeach;?>
				<span style="display: inline-block; height: 5px; clear: both; float: left;">&nbsp;</span>
		</div>
		<?php endif;?>

		<?php if ( ! empty($thanks)): ?>
		<div class="title" style="clear:both;">THANKS:<span class="title_jp">最近クールしてくれた人</span></div>
		<div class="ans thanks_div">
				<?php foreach ($thanks as $i => $val): ?>
				<div class="thanks_div_list">
				<a href="/user/you/<?php echo $val->user_id;?>/" rel="external">
					<?php $thanks_user_image = LoginService::get_user_image_url_small($val->user_id);?>
					<span class="thanks_div_list_img"><img src="<?php echo $thanks_user_image;?>" title="<?php echo $val->user_name;?>"></span>
					<span class="thanks_div_list_name"><?php echo $val->user_name;?></span>
				</a>
				</div>
				<?php endforeach;?>
				<span style="display: inline-block; height: 5px; clear: both; float: left;">&nbsp;</span>
		</div>
		<?php endif;?>

		<?php if ( ! empty($cools)): ?>
		<div class="title" style="clear:both;" id="useryou_cools_title">COOLS:<span class="title_jp">最近クールを送った人</span></div>
		<div class="ans cools_div">
				<?php foreach ($cools as $i => $val): ?>
				<div class="cools_div_list">
				<a href="/user/you/<?php echo $val->user_id;?>/" rel="external">
					<?php $cool_user_image = LoginService::get_user_image_url_small($val->user_id);?>
					<span class="cools_div_list_img"><img src="<?php echo $cool_user_image;?>" title="<?php echo $val->user_name;?>"></span>
					<span class="cools_div_list_name"><?php echo $val->user_name;?></span>
				</a>
				</div>
				<?php endforeach;?>
		</div>
		<?php endif;?>

		<div class="title" style="clear:both;" id="useryou_tracklist_title">LIST:
			<span class="title_jp">お気に入りトラックリスト</span>
			<?php if (isset($client_user_id) and ($client_user_id === $user_id)):?>
				<span class="title_link"><?php echo Html::anchor('/tracklist/create/', 'リストを作成');?></span>
			<?php endif;?>
		</div>
		<div class="ans track_list_div">
			<div>
				<span id="tracklist_count"></span>
				<span id="tracklist_fromto"></span>
			</div>
			<div  id="tracklist_pagination_div_1" class="pagination_div"></div>
			<ul id="useryou_tracklist_ul" class="ui-listview" data-role="listview">
				<li>投稿はありません</li>
			</ul>
			<div id="tracklist_pagination_div_2" class="pagination_div"></div>
		</div>

		<div class="title" style="clear:both; position:relative; top: 5px;" id="review_id">REVIEW:
			<span class="title_jp"><?php echo $this->user_name;?>さんのレビュー</span>
		</div>
		<div class="ans review_div">
			<div>
				<span id="reviewlist_count"></span>
				<span id="reviewlist_fromto"></span>
				</div>
			<div id="reviewlist_pagination_div_1" class="pagination_div"></div>
			<ul id="useryou_reviewlist_ul" class="review_list_ul" data-role="listview">
				<li>投稿はありません</li>
			</ul>
	 		<div id="reviewlist_pagination_div_2" class="pagination_div"></div>
		</div>

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
		<img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>" data-original="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=<?php echo \Config::get('host.base_url_https'). Uri::main();?>">
	</div>

	<?php echo Form::hidden('user_id', $user_id). PHP_EOL;?>
	<?php echo Form::hidden('client_user_id', $client_user_id). PHP_EOL;?>

</div>


