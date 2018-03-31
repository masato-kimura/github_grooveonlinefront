<?php echo Asset::js('smartphone/login/index.js'); ?>
<?php echo Asset::css('smartphone/login/index.css'); ?>

<script type="text/javascript">
<!--
$(document).bind("pageinit", function(){
  $.mobile.ajaxEnabled = false;
});

jQuery(function($) {
	if ($('#from_password_reissue_caption').html().length > 0) {
		setTimeout(function() {
			$('#login_index_password').val(null);
			//$('#login_index_password').focus();
			return true;
		}, 500);
	}
});

//-->
</script>

<div class="main_div">

	<?php echo Form::open(array('data-ajax' => 'false', 'action' => \Config::get('host.base_url_https'). '/login/grooveonline', 'method' => 'post', 'id' => 'login_index_form'));?>

	<h3 class="main_title">※ グルーヴオンラインアカウントでログイン</h3>
	<h5>グルーヴオンライン登録済みの方</h5>

	<div class="ui-field-contain ui-hide-label">
		<label for="login_index_email">メールアドレス</label>
		<?php echo Form::input('email', isset($this->email)? $this->email: null, array('type' => 'email', 'class' => 'email', 'id' => 'login_index_email', 'placeholder' => 'メールアドレス'));?>
		<?php if (isset($this->arr_error['email'])):?>
			<br />
			<span class="error"><?php echo $this->arr_error['email'];?></span>
		<?php endif;?>
	</div>

	<div class="ui-field-contain ui-hide-label">
		<label for="login_index_password">パスワード</label>
		<?php echo Form::input('password', $password, array('type' => 'password', 'class' => 'password', 'id' => 'login_index_password', 'placeholder' => 'パスワード'));?>
		<?php if (isset($this->arr_error['password'])):?>
			<br />
			<span class="error"><?php echo $this->arr_error['password'];?></span>
		<?php endif;?>
	</div>

	<?php if (isset($from_password_reissue_caption)): ?>
		<div id="from_password_reissue_caption">
		<?php echo $from_password_reissue_caption; ?>
		</div>
	<?php else:?>
		<div id="from_password_reissue_caption"></div>
	<?php endif;?>

	<?php $checked = ($auto_login) ? 'checked' : null;?>
	<?php echo Form::input('auto_login', '1', array('type' => 'checkbox', 'checked' => $checked, 'id' => 'form_auto_login_gol', 'data-role' => 'none', 'checked' => 'checked')). PHP_EOL;?>
	<?php echo Form::label('次回からのログインを自動化', 'auto_login_gol', array('data-role'=> 'none'));?>
	<br />
	<?php echo Form::submit('submit', 'ログイン', array('class' => 'ui-btn'));?>
	<br />
	<?php echo Html::anchor('login/grooveonlinepassreissuerequest', 'パスワードをお忘れの方', array('id' => 'passreissuerequest', 'rel' => 'external'), \Config::get('host.https'));?>
	<br /><br />
	<?php echo Html::anchor('login/grooveonlineregistindex/', 'グルーヴオンライン未登録の方はこちら', array('rel' => 'external', 'data-ajax' => 'false'), \Config::get('host.https'));?>
	<?php echo Form::close(). PHP_EOL; ?>

	<br />
	<br />

	<h3 class="main_title">※ 次のアカウントをお持ちの方は今すぐご利用可能です。</h3>
	<h5>ログイン後にグルーヴオンラインで使用する名前などのユーザー情報を変更することができます。</h5>

	<table class="oauth_table">
	  <tr class="oauth">
	    <td class="oauth_facebook">
	      <?php echo Html::anchor('login/facebook/?auto_login=', Form::input('facebook', 'facebookでログイン', array('class' => 'oauth_login_btn global_facebook_login_btn', 'id' => 'global_facebook_login_btn','type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https')); ?>
	    </td>
	  </tr>
	  <tr class="oauth">
	    <td class="oauth_google">
	      <?php echo Html::anchor('login/google/?auto_login=', Form::input('google', 'googleでログイン', array('class' => 'oauth_login_btn global_google_login_btn', 'id' => 'global_google_login_btn', 'type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https')); ?>
	    </td>
	  </tr>
	  <tr class="oauth">
	    <td class="oauth_twitter">
	      <?php echo Html::anchor('login/twitter/?auto_login=', Form::input('twitter', 'twitterでログイン', array('class' => 'oauth_login_btn global_twitter_login_btn', 'id' => 'global_twitter_login_btn', 'type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https')); ?>
	    </td>
	  </tr>
	  <tr class="oauth">
	    <td class="oauth_yahoo">
	      <?php echo Html::anchor('login/yahoo/?auto_login=', Form::input('yahoo', 'yahooでログイン', array('class' => 'oauth_login_btn global_yahoo_login_btn', 'id' => 'global_yahoo_login_btn', 'type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https'));?>
	    </td>
	  </tr>
	  <tr class="autologin">
	    <td>
	      <?php $checked = ($auto_login) ? 'checked' : null;?>
	      <?php echo Form::input('auto_login', '1', array('type' => 'checkbox', 'checked' => $checked, 'id' => 'form_auto_login_oauth', 'data-role' => 'none', 'checked' => 'checked')). PHP_EOL;?>
	      <?php echo Form::label('次回からのログインを自動化します', 'auto_login_oauth');?>
	    </td>
	  </tr>
	</table>
</div>