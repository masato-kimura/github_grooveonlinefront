<?php echo Asset::js('pc/login/index.js'); ?>
<?php echo Asset::css('pc/login/index.css'); ?>

<div class="main_div">

	<h3 class="introduction">グルーヴオンラインへログイン</h3>

	<div class="main_form">
	<?php echo Form::open(\Config::get('host.base_url_https'). '/login/grooveonline');?>
	<table class="gol_table">
		<tr>
			<th>
				<span class="main">※ グルーヴオンラインアカウントでログイン</span>
				<br />
				<span class="sub">グルーヴオンライン登録済みの方</span>
			</th>
		</tr>
		<tr class="title">
			<td>
				<span class="main">・メールアドレス</span>
			</td>
		</tr>
		<tr class="ans">
			<td>
				<?php echo Form::input('email', isset($this->email)? $this->email: null, array('type' => 'email', 'class' => 'email'));?>
				<?php if (isset($this->arr_error['email'])):?>
					<br />
					<span class="error"><?php echo $this->arr_error['email'];?></span>
				<?php endif;?>
			</td>
		</tr>

		<tr class="title">
			<td>
				<span class="main">・パスワード</span>
			</td>
		</tr>
		<tr class="ans">
			<td>
			<?php echo Form::input('password', $password, array('type' => 'password', 'class' => 'password', 'id' => 'login_index_password'));?>
			<?php if (isset($this->from_password_reissue_caption)):?>
	          <div id="from_password_reissue_caption"><?php echo $this->from_password_reissue_caption; ?></div>
	        <?php endif;?>

	        <?php if (isset($this->arr_error['password'])):?>
	          <br />
	          <span class="error"><?php echo $this->arr_error['password'];?></span>
	        <?php endif;?>
	        </td>
	    </tr>

	    <tr class="autologin">
	      <td>
	        <?php $checked = ($auto_login) ? 'checked' : null;?>
	        <?php echo Form::input('auto_login', '1', array('type' => 'checkbox', 'checked' => $checked, 'id' => 'form_auto_login_gol')). PHP_EOL;?>
	        <?php echo Form::label('次回からのログインを自動化します', 'auto_login_gol');?>
	      </td>
	    </tr>

	    <tr class="submit">
	      <td>
	        <?php echo Form::submit('submit', 'グルーヴオンラインアカウントでログイン', array('class' => 'global_submit_btn'));?>
	        <br />
	        <?php echo Html::anchor('login/grooveonlinepassreissuerequest', 'パスワードをお忘れの方', array('id' => 'passreissuerequest_link'), \Config::get('host.https'));?>
	        <br />
	        <?php echo Html::anchor('login/grooveonlineregistindex/', 'グルーヴオンライン未登録の方はこちら', array(), \Config::get('host.https'));?>
	      </td>
	    </tr>
	  </table>
	<?php echo Form::close(). PHP_EOL; ?>

	<table class="oauth_table">
	  <tr>
	    <th>
	      <span class="main">※ 次のアカウントお持ちの方は今すぐご利用可能です。</span><br />
	      <span class="sub">ログイン後にグルーヴオンラインで使用する表示名などのユーザ情報を変更することができます。</span><br />
	    </th>
	  </tr>
	  <tr class="oauth">
	    <td class="oauth_facebook">
	      <?php echo Html::anchor('login/facebook/?auto_login=', Form::input('facebook', 'facebookでログイン', array('class' => 'global_facebook_login_btn', 'type' => 'button')), array(), \Config::get('host.https')); ?>
	    </td>
	  </tr>
	  <tr class="oauth">
	    <td class="oauth_google">
	      <?php echo Html::anchor('login/google/?auto_login=', Form::input('google', 'googleでログイン', array('class' => 'global_google_login_btn', 'type' => 'button')), array(), \Config::get('host.https')); ?>
	    </td>
	  </tr>
	  <tr class="oauth">
	    <td class="oauth_twitter">
	      <?php echo Html::anchor('login/twitter/?auto_login=', Form::input('twitter', 'twitterでログイン', array('class' => 'global_twitter_login_btn', 'type' => 'button')), array(), \Config::get('host.https')); ?>
	    </td>
	  </tr>
	  <tr class="oauth">
	    <td class="oauth_yahoo">
	      <?php echo Html::anchor('login/yahoo/?auto_login=', Form::input('yahoo', 'yahooでログイン', array('class' => 'global_yahoo_login_btn', 'type' => 'button')), array(), \Config::get('host.https'));?>
	    </td>
	  </tr>
	  <tr class="autologin">
	    <td>
	      <?php $checked = ($auto_login) ? 'checked' : null;?>
	      <?php echo Form::input('auto_login', '1', array('type' => 'checkbox', 'checked' => $checked, 'id' => 'form_auto_login_oauth')). PHP_EOL;?>
	      <?php echo Form::label('次回からのログインを自動化します', 'auto_login_oauth');?>
	    </td>
	  </tr>
	</table>
</div>
</div>