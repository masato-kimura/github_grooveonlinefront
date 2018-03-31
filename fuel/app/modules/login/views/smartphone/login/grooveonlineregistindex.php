<?php echo Asset::js('smartphone/login/grooveonlineregistindex.js'); ?>
<?php echo Asset::css('smartphone/login/grooveonlineregistindex.css'); ?>

<script type="text/javascript">
<!--
$(document).bind("pageinit", function(){
  $.mobile.ajaxEnabled = false;
});
//-->
</script>

<div class="main_div">

	<h3 class="introduction">※ グルーヴオンライン新規ユーザー登録</h3>

	<?php echo Form::open(array('action' => 'login/grooveonlineregistconfirm?'. $this->parameter , 'method' => 'post', 'enctype' => 'multipart/form-data', 'data-ajax' => 'false', 'id' => 'regist_index_form'));?>

	<fieldset class="ui-field-contain">
		<div class="label_div">
			<label for="registindex_email">メールアドレス<span class="span_required">（必須）</span></label>
		</div>
		<?php echo Form::input('email', $this->email, array('placeholder' => 'メールアドレス', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'registindex_email'));?>
		<?php if ( ! empty($this->arr_error['email'])):?>
			<span class="error"><?php echo $this->arr_error['email']; ?></span><br />
		<?php endif;?>
		<span class="caption">グルーヴオンラインにログインする場合に使用します。公開はされません。</span>
		<br />
		<br />
	</fieldset>

	<fieldset class="ui-field-contain">
		<div class="label_div">
			<label for="registindex_password">パスワード<span class="span_required">（必須）</span></label>
		</div>
		<?php echo Form::password('password', $password, array('placeholder' => 'パスワード', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'registindex_password'));?>
		<?php if ( ! empty($this->arr_error['password'])):?>
			<span class="error"><?php echo $this->arr_error['password']; ?></span><br />
		<?php endif;?>
		<span class="caption">半角英数(4～20文字)</span>
		<br />
		<br />
	</fieldset>

	<fieldset class="ui-field-contain">
		<div class="label_div">
			<label for="registindex_user_name">ユーザー名<span class="span_required">（必須）</span></label>
		</div>
		<?php echo Form::input('user_name', $user_name, array('placeholder' => 'お名前', 'required' => 'required', 'id' => 'registindex_user_name')); ?>
		<?php if ( ! empty($this->arr_error['user_name'])):?>
			<span class="error"><?php echo $this->arr_error['user_name']; ?></span><br />
		<?php endif;?>
		<span class="caption">グルーヴオンラインで表示される名前です。(20文字まで)</span>
		<br />
		<br />
	</fieldset>

	<div class="open_option">
		<span class="openclose">▼</span>
		<span class="openclose" style="display:none;">▲</span>
		<span>その他のオプション項目</span>
		<span class="openclose">▼</span>
		<span class="openclose" style="display:none;">▲</span>
		<br /><br />
	</div>

	<fieldset class="ui-field-contain option" data-role="controlgroup">
		<div class="label_div">
			<legend>性別</legend>
		</div>
		<input type="radio" name="gender" value="male"   id="gender_male"   <?php if ($gender === 'male'  ) echo "checked"; ?>><label for="gender_male">男</label>
		<input type="radio" name="gender" value="female" id="gender_female" <?php if ($gender === 'female') echo "checked"; ?>><label for="gender_female">女</label>
		<input type="radio" name="gender" value="secret" id="gender_secret" <?php if ($gender === 'secret') echo "checked"; ?>><label for="gender_secret">非公開</label>
		<?php if ( ! empty($this->arr_error['gender'])):?>
			<span class="error"><?php echo $this->arr_error['gender']; ?></span><br />
		<?php endif;?>
		<br />
	</fieldset>

	<fieldset class="ui-field-contain option" data-role="controlgroup" data-type="horizontal" style="padding-bottom: 0px;">
		<div class="label_div">
			<legend>誕生月日 / 年齢</legend>
		</div>
		<?php echo Form::select("birthday_month", $this->birthday_month, $this->arr_birthday_month, array('id' => 'registindex_birthday_month')); ?><label for="registindex_birthday_month">月</label>
		<?php echo Form::select("birthday_day",   $this->birthday_day,   $this->arr_birthday_day, array('id' => 'registindex_birthday_day')); ?><label for="registindex_birthday_day">日</label>
		<?php echo Form::select("old",  $this->old,  $this->arr_old, array('id' => 'registindex_old')); ?><label for="registindex_old">歳</label>
		<?php if ( ! empty($this->arr_error['old'])):?>
			<span class="error"><?php echo $this->arr_error['old']; ?></span><br />
		<?php endif;?>
	</fieldset>

	<fieldset class="ui-field-contain option" data-role="controlgroup" style="padding: 0px; margin: 0px;">
		<input type="checkbox" name="birthday_secret" id="birthday_secret" value="1" <?php if ($birthday_secret === '1') echo "checked"; ?>><label for="birthday_secret">誕生日を公開しません</label>
		<input type="checkbox" name="old_secret" id="old_secret" value="1" <?php if ($old_secret === '1') echo "checked"; ?>><label for="old_secret">年齢を公開しません</label>
		<br />
	</fieldset>

	<fieldset class="ui-field-contain option" data-role="controlgroup">
		<div class="label_div">
			<legend>お住まい地域</legend>
		</div>
		<label for="registindex_pref">都道府県</label>
		<?php echo Form::select("pref", $pref, $this->arr_pref, array('id' => 'registindex_pref')); ?>
		<?php if ( ! empty($this->arr_error['pref'])):?>
			<span class="error"><?php echo $this->arr_error['pref']; ?></span><br />
		<?php endif;?>
		<br />
	</fieldset>

	<fieldset class="ui-field-contain option">
		<div class="label_div">
			<label for="registindex_pic">プロフィール画像<br />使用可能画像（jpg, gif, png）5Mbyteまで</label>
		</div>
		<?php if ( ! empty($this->picture_url)): ?>
			<img src="<?php echo $this->picture_url; ?>">
		<?php endif;?>
		<?php if ( ! empty($this->arr_error['image'])):?>
			<span class="error"><?php echo $this->arr_error['image']; ?></span><br />
		<?php endif;?>
		<input type="file" name="pic" id="registindex_pic">
		<br />
	</fieldset>

	<fieldset class="ui-field-contain option">
		<div class="label_div">
			<label for="registindex_profile_fields">自己紹介</label>
		</div>
		<textarea rows="5" cols="30" name="profile_fields" id="registindex_profile_fields"><?php echo $profile_fields; ?></textarea>
		<?php if ( ! empty($this->arr_error['profile_fields'])):?>
			<span class="error"><?php echo $this->arr_error['profile_fields']; ?></span><br />
		<?php endif;?>
		<span class="caption">1000文字以内で入力してください</span>
		<br />
		<br />
		</fieldset>

	<fieldset class="ui-field-contain option">
		<div class="label_div">
			<label for="registindex_facebook_fields"><a href="https://www.facebook.com/" target="new_win">Facebook</a></label>
		</div>
		<?php echo Form::input('facebook_url', $facebook_url, array('id' => 'registindex_facebook_fields', 'autocomplete' => 'off', 'placeholder' => 'https://www.facebook.com/xxxxx'));?>
		<?php if ( ! empty($arr_error['facebook_url'])):?>
			<span class="error"><?php echo $arr_error['facebook_url']; ?></span><br />
		<?php endif;?>
		<br />

		<div class="label_div">
			<label for="registindex_twitter_fields"><a href="https://twitter.com/" target="new_win">twitter</a></label>
		</div>
		<?php echo Form::input('twitter_url', $twitter_url, array('id' => 'registindex_twitter_fields', 'autocomplete' => 'off', 'placeholder' => 'https://twitter.com/xxxxx'));?>
		<?php if ( ! empty($arr_error['twitter_url'])):?>
			<span class="error"><?php echo $arr_error['twitter_url']; ?></span><br />
		<?php endif;?>
		<br />

		<div class="label_div">
			<label for="registindex_google_fields"><a href="https://plus.google.com/" target="new_win">google+</a></label>
		</div>
		<?php echo Form::input('google_url', $google_url, array('id' => 'registindex_google_fields', 'autocomplete' => 'off', 'placeholder' => 'https://plus.google.com/xxxxx'));?>
		<?php if ( ! empty($arr_error['google_url'])):?>
			<span class="error"><?php echo $arr_error['google_url']; ?></span><br />
		<?php endif;?>
		<br />

		<div class="label_div">
			<label for="registindex_instagram_fields"><a href="https://instagram.com/" target="new_win">instagram</a></label>
		</div>
		<?php echo Form::input('instagram_url', $instagram_url, array('id' => 'registindex_instagram_fields', 'autocomplete' => 'off', 'placeholder' => 'https://www.instagram.com/xxxxx'));?>
		<?php if ( ! empty($arr_error['instagram_url'])):?>
			<span class="error"><?php echo $arr_error['instagram_url']; ?></span><br />
		<?php endif;?>
		<br />

	</fieldset>

	<fieldset class="ui-field-contain option">
		<div class="label_div">
			<label for="registindex_site_fields">ブログ、ウェブサイト</label>
		</div>
		<?php echo Form::input('site_url', $site_url, array('id' => 'registindex_site_fields', 'autocomplete' => 'off', 'placeholder' => 'http://your_address'));?>
		<?php if ( ! empty($arr_error['site_url'])):?>
			<span class="error"><?php echo $arr_error['site_url']; ?></span><br />
		<?php endif;?>
		<span class="caption">プロフィールで紹介したい自身のブログやサイトがありましたらアドレスを入力してください</span>
		<br />
	</fieldset>


	<fieldset class="field_checkbox">
		<br />
		<input type="checkbox" name="auto_login" id="registindex_autologin" value="1" data-role="none" <?php echo (isset($auto_login) && $auto_login == 1) ? ' checked' : ' checked'; ?>>
		<label for="registindex_autologin">次回からのログインを自動化</label>
		<br />
	</fieldset>

	<?php echo Form::submit('submit', "ユーザ登録内容を確認します", array('class' => 'global_submit_btn', 'data-role' => 'none'));?>
	<input type="hidden" name="auth_type" value="<?php echo $auth_type;?>">
	<input type="hidden" name="oauth_id" value="<?php echo $oauth_id;?>">
	<input type="hidden" name="picture_url" value="<?php echo $picture_url?>">
	<?php echo Form::close();?>

	<br />
	<br />
	<br />

	<h3 class="main_title">※ 次のアカウントをお持ちの方は今すぐご利用可能です。</h3>
	<h5>ログイン後にグルーヴオンラインで使用する名前などのユーザ情報を変更することができます。</h5>

	<table class="oauth_table">
		<tr class="oauth">
			<td class="oauth_facebook">
			<?php if (isset($parameter)):?>
				<?php echo Html::anchor('login/facebook/?'. $parameter. '&auto_login=', Form::button('facebook', 'facebookでログイン', array('class' => 'global_facebook_login_btn', 'type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https')); ?>
			<?php else: ?>
				<?php echo Html::anchor('login/facebook/?auto_login=', Form::button('facebook', 'facebookでログイン', array('class' => 'global_facebook_login_btn', 'type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https')); ?>
			<?php endif; ?>
			</td>
		</tr>
		<tr class="oauth">
			<td class="oauth_google">
			<?php if (isset($parameter)):?>
				<?php echo Html::anchor('login/google/?'. $parameter. '&auto_login=', Form::button('google', 'Google+でログイン', array('class' => 'global_google_login_btn', 'type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https')); ?>
			<?php else:?>
				<?php echo Html::anchor('login/google/?auto_login=', Form::button('google', 'Google+でログイン', array('class' => 'global_google_login_btn', 'type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https')); ?>
			<?php endif;?>
			</td>
		</tr>
		<tr class="oauth">
			<td class="oauth_twitter">
			<?php if (isset($parameter)):?>
				<?php echo Html::anchor('login/twitter/?'. $parameter. '&auto_login=', Form::button('twitter', 'twitterでログイン', array('class' => 'global_twitter_login_btn', 'type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https')); ?>
			<?php else: ?>
				<?php echo Html::anchor('login/twitter/?auto_login=', Form::button('twitter', 'twitterでログイン', array('class' => 'global_twitter_login_btn', 'type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https')); ?>
			<?php endif;?>
			</td>
		</tr>
		<tr class="oauth">
			<td class="oauth_yahoo">
			<?php if (isset($parameter)):?>
				<?php echo Html::anchor('login/yahoo/?'. $parameter. '&auto_login=', Form::button('yahoo', 'Yahoo! JAPAN IDでログイン', array('class' => 'global_yahoo_login_btn', 'type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https'));?>
			<?php else: ?>
				<?php echo Html::anchor('login/yahoo/?auto_login=', Form::button('yahoo', 'Yahoo! JAPAN IDでログイン', array('class' => 'global_yahoo_login_btn', 'type' => 'button', 'data-role' => 'none', 'rel' => 'external')), array(), \Config::get('host.https'));?>
			<?php endif;?>
			</td>
		</tr>
		<tr class="autologin">
			<td>
				<fieldset class="field_checkbox" data-role="none">
					<br />
					<input type="checkbox" name="auto_login" id="auto_login_oauth" value="1" data-role="none" <?php echo (isset($auto_login) && $auto_login == 1) ? ' checked' : ' checked'; ?>>
					<label for="auto_login_oauth" data-role="none">次回からのログインを自動化</label>
				</fieldset>
			</td>
		</tr>
	</table>
</div>
