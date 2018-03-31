<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<?php echo Asset::js(array('pc/jquery-2.0.3.min.js'));?>
<?php echo Asset::js(array('pc/jquery.rateit.min.js'));?>
<?php echo Asset::js(array('grooveonline.js'));?>
<?php echo Asset::css('pc/grooveonline.css');?>
<?php echo Asset::css('pc/rateit.css');?>
<title><?php echo $title; ?></title>
</head>
<body>
<div id="wrapper" class="wrapper">
  <div id="head" class="head">
    <span class="head_side head_left_side">
        <span><?php echo Html::Anchor(\Config::get('host.base_url_http'), \Config::get('host.base_url')); ?></span>
        <span class="page_name"><?php echo ! empty($page_name)? $page_name: null; ?>&nbsp;</span>
    </span>
    <span class="head_side head_right_side">
	    <?php if (empty($this->user_id)): ?>
	     <span id="login" class="login"><?php echo Html::anchor('login', 'ログイン', array(), \Config::get('host.https'));?></span>
	     &nbsp;
	    <?php else:?>
	     <span id="user_info_edit" class="user_info_edit"><?php echo Html::anchor('login/editregistindex', 'ユーザ情報更新', array(), \Config::get('host.https'));?></span>
	     &nbsp;&nbsp;
	     <span id="logout" class="logout"><?php echo Html::anchor('login/logout', 'ログアウト', array(), \Config::get('host.https'));?></span>
	    <?php endif;?>
     </span>
  </div>
  <div id="content" class="content">
    <?php echo isset($content) ? $content: null; ?>
  </div>


  <?php if (empty($this->hide_to_other_device)): ?>
    <?php if ($this->real_device === 'smartphone'):?>
      <div id="template_to_smartphone">
        <?php echo Html::anchor('user/device/smartphone/'. $this->segments, 'スマートフォン版で表示');?>
      </div>
    <?php endif;?>
  <?php endif;?>
  <div id="footer" class="footer">&nbsp;</div>
</div><!-- /.wrapper -->
</body>
</html>
