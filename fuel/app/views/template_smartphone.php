<!DOCTYPE html>
<html lang="ja">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta property="twitter:card" content="summary" />
<meta property="twitter:site" content="@_grooveonline" />
<meta property="twitter:description" content="<?php echo isset($og_description)? $og_description: $title;?>" />
<meta property="og:site_name" content="グルーヴオンライン" />
<meta property="og:description" content="<?php echo isset($og_description)? $og_description: '';?>" />
<meta property="og:title" content="<?php echo $title;?>" />
<meta property="og:type" content="<?php echo isset($og_type)? $og_type: "website"; ?>" />
<meta property="og:image" content="<?php echo ! empty($og_image)? $og_image: \Config::get('host.base_url_http'). "/assets/img/web/index/ogp20151029.jpg";?>" />
<meta name="google-site-verification" content="AGmO2DhL1vTdBRgCaF-3Xh2xKXAv51JuIOJtzh2OKbQ" />
<title><?php  echo $title; ?></title>
<link rel="stylesheet" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
<link rel="alternate" hreflang="ja" href="http://groove-online.com/" />
<link rel="apple-touch-icon-precomposed" href="//groove-online.com/assets/img/web/index/webclip.png" />
<link rel="alternate" hreflang="ja" href="<?php echo \Config::get('host.base_url_https'). Uri::main();?>" />
<?php echo Asset::css('smartphone/grooveonline.css');?>
<?php echo Asset::css('smartphone/rateit.css');?>
<script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript">
$(document).bind("mobileinit", function() {
	$.mobile.ajaxEnabled = false;
	$.mobile.pushStateEnabled = false;
	$.mobile.ajaxFormsEnabled = false;
});
</script>
<script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
<?php echo Asset::js(array('smartphone/jquery.ui.touch-punch.min.js'));?>
<?php echo Asset::js(array('smartphone/jquery.rateit.min.js'));?>
<?php echo Asset::js(array('jquery.lazyload.min.js'));?>
<?php echo Asset::js(array('grooveonline.js')); ?>
<?php if (\Agent::is_smartphone() or \Agent::is_mobiledevice()):?>
<style>
.main_div {
  margin-top: 0px;
}
</style>
<?php else:?>
<style>
.main_div {
  padding: 20px 35px;
}
</style>
<?php endif;?>
</head>

<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.5&appId=722324784566290";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- ページ領域の定義 -->
<div id="<?php echo isset($this->segment)? $this->segment: null;?>" data-role="page" data-title="<?php echo $title; ?>" class="lazy">
	<!-- ヘッダー領域の定義 -->
	<header data-role="header" data-theme="b" data-position="fixed">
		<?php echo Html::Anchor(\Config::get('host.base_url_http'), 'トップ', array('rel' => 'external')); ?>
		<h3></h3>
		<?php if (empty($user_id)): ?>
			<div data-role="controlgroup" data-type="horizontal" class="ui-btn-right">
			<?php echo Html::anchor('/info/1/', $unread_information_count, array('data-role' => 'button', 'data-icon' => 'info', 'data-iconpos' => '', 'data-inline' => 'true', 'id' => 'information-icon', 'class' => 'ui-btn ui-icon-info ui-btn-icon-left information-icon', 'title' => 'インフォメーション', 'style' => 'red'), \Config::get('host.https'));?>
			<?php echo PHP_EOL;?>
			<?php echo Html::anchor('login', 'ログイン', array('data-role' => 'button','data-inline' => 'true', 'rel' => 'external'), \Config::get('host.https'));?>
			</div>
		<?php else:?>
			<?php echo PHP_EOL;?>
			<div data-role="controlgroup" data-type="horizontal" class="ui-btn-right">
			<?php echo Html::anchor(
					'/info/1/',
					$unread_information_count,
					array(
							'data-role'    => 'button',
							'data-icon'    => 'info',
							'data-iconpos' => '',
							'data-inline ' => 'true',
							'id'           => 'information-icon',
							'class'        => 'ui-btn ui-icon-info ui-btn-icon-left',
							'title'        => 'インフォメーション',
							'style'        => 'red'
					),
					\Config::get('host.https'));
			?>
			<?php echo PHP_EOL;?>
			<?php echo Html::anchor(
					'login/editregistindex',
					'ユーザ情報更新',
					array(
							'data-role'    => 'button',
							'data-icon'    => 'gear',
							'data-iconpos' => 'notext',
							'data-inline'  => 'true',
							'title'        => $user_name. 'さんのプロフィール編集ページ',
							'alt'          => $user_name. 'さんのプロフィール編集ページ',
					),
					\Config::get('host.https'));
			?>
			<?php echo PHP_EOL;?>
			<?php echo Html::anchor(
					'user/you/'. $user_id,
					$unread_user_information_count,
					array(
							'data-role'    => 'button',
							'data-icon'    => 'home',
							'data-iconpos' => '',
							'data-inline'  => true,
							'rel'          => 'external',
							'id'           => 'user_profile',
							'class'        => 'ui-btn ui-icon-home ui-btn-icon-left',
							'title'        => $user_name. 'さんのプロフィールページ',
							'alt'          => $user_name. 'さんのプロフィールページ',
					),
					\Config::get('host.https'));
			?>
			<?php echo PHP_EOL;?>
			</div>
		<?php endif;?>
		<?php if ( ! empty($this->user_id)):?>
			<h5 id="header_login_user">
				<span class="welcome">Hello !</span>
				<span class="user_name"><?php echo $this->user_name;?>さん</span>
			</h5>
		<?php endif;?>

	</header>
	<!-- コンテンツ領域の定義 -->
	<div role="main" class="ui-content">
		<div class="top_pr_div">
			<?php echo $this->top_banner. PHP_EOL;?>
		</div>
		<?php echo isset($content) ? $content: null; ?>
	</div>

	<br />

	<?php if (empty($this->hide_to_other_device)): ?>
	<div id="template_to_pc">
		<?php // echo Html::anchor('user/device/pc/'. $this->segments, 'PCサイトで表示', array('rel' => 'external')); ?>
	</div>
	<?php endif;?>

	<!-- フッター領域の定義 -->
	<footer data-role="footer" data-theme="b" data-position="fixed" class="footer">
		<span id="footer_grooveonline">GROOVE-ONLINE<span class="trade_mark">TM</span></span>
		<?php echo Html::anchor('/aboutus/', 'about us', array('data-role' => 'none', 'id' => 'aboutus'));?>
	</footer>
</div>
</body>
</html>
