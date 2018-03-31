<script type="text/javascript">
<!--
$(document).bind("pageinit", function(){
  $.mobile.ajaxEnabled = false;
});
//-->
</script>

<div class="main_div">
	<h3>※グルーヴオンラインでログインパスワード再発行</h3>
	<?php echo Form::open(array('action' => 'login/grooveonlinepassreissueupdate', 'data-ajax' => 'false'));?>
	<div><?php echo \Input::get('email');?></div>
	<div class="ui-field-contain ui-hide-label">
		<label for="login_gprf_password">パスワード</label>
		<?php echo Form::input('password', \Input::post('password'), array('type' => 'password', 'class' => 'password', 'id' => 'login_gprf_password', 'placeholder' => 'あたらしいパスワードを入力してください')). PHP_EOL;?>
	</div>
	<?php echo Form::hidden('email', \Input::get('email'), array('class' => 'email')). PHP_EOL; ?>
	<?php echo Form::hidden('tentative_password', \Input::get('tentative_password'), array('class' => 'tentative_password')). PHP_EOL; ?>
	<?php echo Form::hidden('tentative_id', $this->tentative_id, array('class' => 'tentative_id')); ?>
	<?php if (isset($this->arr_error['password'])):?>
	<br />
	<span class="error"><?php echo $this->arr_error['password'];?></span>
	<?php endif;?>

	<?php echo Form::submit('submit', '送信');?>
</div>