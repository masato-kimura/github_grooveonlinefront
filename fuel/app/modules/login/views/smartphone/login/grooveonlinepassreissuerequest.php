<?php echo Asset::css('smartphone/login/grooveonlinepassreissurerequest.css'); ?>
<script type="text/javascript">
<!--
$(document).bind("pageinit", function(){
  $.mobile.ajaxEnabled = false;
});
jQuery(function() {
  $('#login_gprr_submit').on('tap click', function() {
    $('#login_gprr_form').submit();
    return true;
  });
});
//-->
</script>
<div class="main_div">
	<?php if (isset($this->error_message)):?>
		<div><?php echo $this->error_message;?></div>
	<?php endif;?>

	<h3 class="introduction">グルーヴオンライン・ログインパスワード再発行</h3>
	<h5>ご指定のメールアドレスにパスワード再発行方法を記載したメールを送信いたします。</h5>

	<?php echo Form::open(array('action' => 'login/grooveonlinepassreissuesendmail', 'data-ajax' => 'false', 'id' => 'login_gprr_form')). PHP_EOL;?>

	<div class="ui-field-contain ui-hide-label">
		<?php if (empty($this->hide_send_btn)):?>
			<label for="login_gprr_mail">メールアドレス</label>
			<?php echo Form::input('email', $email, array('type' => 'email', 'class' => 'email', 'id' => 'login_gprr_mail', 'placeholder'=> 'ご登録のメールアドレスを入力してください'));?>
		<?php endif;?>

		<?php if ( ! empty($this->arr_error['email'])):?>
			<span class="error_disp"><?php echo ! empty($this->arr_error['email']) ? $this->arr_error['email']: null;?></span>
		<?php endif;?>
	</div>
	<?php if (empty($this->hide_send_btn)):?>
		<input type="button" value="送信" class="global_submit_btn" id="login_gprr_submit">
	<?php endif;?>
	<?php echo Form::close();?>
</div>