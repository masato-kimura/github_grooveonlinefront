<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php echo Asset::js(array('smartphone/jquery-2.0.3.min.js'));?>
<?php echo Asset::css('smartphone/grooveonline.css');?>
<title><?php echo $title; ?></title>
</head>
<body style="background: green;">
<div id="wrapper" class="wrapper">
  <div id="head" class="head">test
    <span id="head_title" class="head_title"><?php echo Html::Anchor(\Config::get('host.base_url_http'), \Config::get('host.base_url')); ?></span>
    <span>ようこそ</span>
    <span class="head_right_side">
	    <?php if (empty($user_info)): ?>
	     <span id="hello" class="hello"><?php echo isset($user_info) ? $user_info['user_name']: null;?></span>
	     <span id="login" class="login"><?php echo Html::anchor('login', 'ログイン', array(), \Config::get('host.https'));?></span>
	    <?php else:?>
	     <span id="user_info_edit" class="user_info_edit"><?php echo Html::anchor('login/editregistindex', 'ユーザ情報更新');?></span>
	     <span id="logout" class="logout"><?php echo Html::anchor('login/logout', 'ログアウト', array(), \Config::get('host.https'));?></span>
	    <?php endif;?>
     </span>
  </div>
  <div id="content" class="content">
    <?php echo isset($content) ? $content: null; ?>
  </div>
  <div id="footer" class="footer">Groove-online 2014</div>
</div><!-- /.wrapper -->
</body>
</html>
