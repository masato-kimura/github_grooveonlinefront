<?php echo Asset::css('smartphone/login/editregistindex.css'); ?>
<?php echo Asset::js('smartphone/login/editregistindex.js');?>
<?php echo Asset::js('jquery.leanModal.min.js');?>
<div class="main_div">

	<h3 class="introduction"><?php echo $user_name;?>さんプロフィール編集</h3>

	<?php if ( ! empty($arr_error)):?>
	<p class="error">エラーが存在します。内容をもう一度ご確認ください。</p>
	<?php endif;?>
	<div id="editregistindex_to_userpage"><?php echo Html::anchor('user/you/'. $user_id, 'プロフィールページ', array('class' => 'ui-btn ui-btn-inline ui-btn-icon-right ui-icon-home'));?></div>

	<?php echo Form::open(array('action' => \Config::get('host.base_url_https'). '/login/editregistconfirm', 'method' => 'post', 'enctype' => 'multipart/form-data', 'data-ajax' => 'false', 'id' => 'login_editregistindex'));?>

		<?php if ($auth_type ==='grooveonline'):?>
			<fieldset class="ui-field-contain">
				<div class="label_div">
					<label for="editregistindex_email">メールアドレス<span class="span_required">（必須）</span></label>
				</div>

				<?php if ($high_level_disp_flg):?>
					<?php echo Form::input('email', $email, array('placeholder' => 'メールアドレス', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'editregistindex_email'));?>
				<?php else: ?>
					<div id="editregistindex_email_disp"><?php echo $email_convert;?></div>
					<?php echo Form::input('email', $email, array('placeholder' => 'メールアドレス', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'editregistindex_email', 'class' => 'high_login ui-screen-hidden'));?>
					<a href="#div_high_login" rel="leanModal"><?php echo Form::input('editregistindex_email_button', 'メールアドレスを変更', array('type' => 'button', 'id' => 'editregistindex_email_button', 'data-inline' => 'true', 'data-mini' => 'true'));?></a>
					<br />
				<?php endif;?>

				<?php if ( ! empty($arr_error['email'])):?>
					<span class="error"><?php echo $arr_error['email']; ?></span>
					<br />
				<?php endif;?>

				<span class="caption">グルーヴオンラインにログインする場合に使用します。公開はされません。</span>
				<br />
			</fieldset>

			<fieldset class="ui-field-contain">
				<div class="label_div">
					<label for="editregistindex_password">パスワード<span class="span_required">（必須）</span></label>
				</div>

				<?php if ($high_level_disp_flg):?>
					<?php echo Form::password('password', $password, array('placeholder' => 'パスワード', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'editregistindex_password'));?>
				<?php else:?>
					<div id="editregistindex_password_disp"><?php echo $password_astarisk; ?></div>
					<?php echo Form::password('password', $password, array('placeholder' => 'パスワード', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'editregistindex_password', 'class' => 'high_login ui-screen-hidden'));?>
					<a href="#div_high_login" rel="leanModal"><?php echo Form::input('editregistindex_password_button', 'パスワードを変更', array('type' => 'button' , 'data-inline' => 'true', 'data-mini' => 'true', 'id' => 'editregistindex_password_button'));?></a>
					<br />
				<?php endif;?>

				<?php if ( ! empty($arr_error['password'])):?>
					<span class="error"><?php echo $arr_error['password']; ?></span>
					<br />
				<?php endif;?>

				<span class="caption">半角英数(4～20文字まで)</span>
				<br />
			</fieldset>

		<?php else: ?>

			<fieldset class="ui-field-contain">
				<div class="label_div">
					<label for="editregistindex_email">メールアドレス</label>
				</div>
				<?php echo Form::input('email', $email, array('placeholder' => 'メールアドレス', 'autocomplete' => 'off', 'id' => 'editregistindex_email'));?>
				<?php if ( ! empty($arr_error['email'])):?>
					<span class="error"><?php echo $arr_error['email']; ?></span><br />
				<?php endif;?>
				<span class="caption">グルーヴオンラインからのお知らせをお送りします。公開はされません。</span>
				<br />
				<br />
			</fieldset>
		<?php endif;?>

		<fieldset class="ui-field-contain">
			<div class="label_div">
				<label for="editregistindex_user_name">ユーザ名<span class="span_required">（必須）</span></label>
			</div>
			<?php echo Form::input('user_name', $user_name, array('placeholder' => 'ユーザ名', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'editregistindex_user_name'));?>
			<?php if ( ! empty($arr_error['user_name'])):?>
				<span class="error"><?php echo $arr_error['user_name']; ?></span><br />
			<?php endif;?>
			<span class="caption">グルーヴオンラインで表示される名前です。(20文字まで)</span>
			<br />
			<br />
		</fieldset>

		<fieldset class="ui-field-contain">
			<div class="label_div">
				<label for="editregistindex_login">ログイン</label>
			</div>
			<?php
			switch ($auth_type)
			{
				case 'facebook':
					echo "<div class='global_facebook login_icon'>facebook</div>". PHP_EOL;
					break;
				case 'google':
					echo "<div class='global_google login_icon'>google+</div>". PHP_EOL;
					break;
				case 'twitter':
					echo "<div class='global_twitter login_icon'>twitter</div>". PHP_EOL;
					break;
				case 'yahoo':
					echo "<div class='global_yahoo login_icon'>yahoo</div>". PHP_EOL;
					break;
				case 'grooveonline':
					echo "<div class='global_grooveonline login_icon'>grooveonline</div>". PHP_EOL;
				break;
			}
			?>
			<span class="caption">ログイン方法は変更できません</span>
			<br />
			<br />
		</fieldset>

		<fieldset class="ui-field-contain" data-role="controlgroup">
			<div class="label_div">
				<legend>性別</legend>
			</div>
			<input type="radio" name="gender" value="male" id="gender_male" <?php if ($gender === 'male') echo "checked"; ?>><label for="gender_male">男</label>
			<input type="radio" name="gender" value="female" id="gender_female" <?php if ($gender === 'female') echo "checked"; ?>><label for="gender_female">女</label>
			<input type="radio" name="gender" value="secret" id="gender_secret" <?php if ($gender === 'secret') echo "checked"; ?>><label for="gender_secret">非公開</label>
			<?php if ( ! empty($arr_error['gender'])):?>
				<span class="error"><?php echo $arr_error['user_name']; ?></span><br />
			<?php endif;?>
			<br />
		</fieldset>

		<fieldset class="ui-field-contain" data-role="controlgroup" data-type="horizontal" style="padding-bottom: 0px;">
			<div class="label_div">
				<legend>誕生月日 / 年齢</legend>
			</div>
			<?php echo Form::select("birthday_month", $birthday_month, $arr_birthday_month, array('id' => 'registindex_birthday_month')); ?><label for="registindex_birthday_month">月</label>
			<?php echo Form::select("birthday_day",   $birthday_day,   $arr_birthday_day, array('id' => 'registindex_birthday_day')); ?><label for="registindex_birthday_day">日</label>
			<?php echo Form::select("old",  $old,  $arr_old, array('id' => 'registindex_old')); ?><label for="registindex_old">歳</label>
			<?php if ( ! empty($arr_error['old'])):?>
				<span class="error"><?php echo $arr_error['old']; ?></span><br />
			<?php endif;?>
 		</fieldset>

		<fieldset class="ui-field-contain" data-role="controlgroup" style="padding: 0px; margin-top: -15px;">
			<input type="checkbox" name="birthday_secret"  id="birthday_secret" value="1" <?php if ($birthday_secret === '1') echo "checked"; ?>><label for="birthday_secret">誕生日を公開しません</label>
			<input type="checkbox" name="old_secret"  id="old_secret" value="1" <?php if ($old_secret === '1') echo "checked"; ?>><label for="old_secret">年齢を公開しません</label>
			<br />
		</fieldset>

		<fieldset class="ui-field-contain option" data-role="controlgroup">
			<div class="label_div">
				<legend>お住まい地域</legend>
			</div>
			<label for="registindex_pref">都道府県</label>
			<?php echo Form::select("pref", $pref, $arr_pref, array('id' => 'registindex_pref')); ?>
			<?php if ( ! empty($arr_error['pref'])):?>
				<span class="error"><?php echo $arr_error['pref']; ?></span><br />
			<?php endif;?>
			<br />
		</fieldset>

		<fieldset class="ui-field-contain option">
			<div class="label_div">
				<label for="registindex_pic">プロフィール画像<br />使用可能画像（jpg, gif, png）5Mbyteまで</label>
			</div>
			<?php if ( ! empty($user_image)): ?>
				<img src='<?php echo $user_image; ?>?<?php echo \Date::forge()->format("%H%M%S");?>'>
			<?php endif; ?>

			<?php if ( ! empty($arr_error['image'])):?>
				<br />
				<span class='error'><?php echo $arr_error['image']; ?></span>
			<?php endif;?>
			<input type="file" name="pic">
			<br />
		</fieldset>

		<fieldset class="ui-field-contain option">
			<div class="label_div">
				<label for="registindex_profile_fields">自己紹介</label>
			</div>
			<textarea rows="5" cols="30" name="profile_fields" id="registindex_profile_fields"><?php echo $profile_fields; ?></textarea>
			<?php if ( ! empty($arr_error['profile_fields'])):?>
				<span class="error"><?php echo $arr_error['profile_fields']; ?></span><br />
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

		<br />

		<input type="hidden" name="auth_type" value="<?php echo $auth_type;?>">
		<input type="hidden" name="oauth_id" value="<?php echo $oauth_id;?>">
		<input type="hidden" name="picture_url" value="<?php echo $picture_url?>">
		<?php echo Form::submit('submit', "ユーザ登録内容を確認します", array('class' => 'global_submit_btn', 'data-role' => 'none'));?>

	</form>

	<br />

	<?php echo Html::anchor('login/logout', 'ログアウト', array('data-role' => 'button', 'rel' => 'external', 'id' => 'logout', 'style' => 'color: #555;'), \Config::get('host.https'));?>

	<br />

<!--
  <table class="group_table">
    <tr>
      <td colspan="3">※所属グループ</td>
    </tr>
    <?php foreach ($group as $obj_group): ?>
    <tr>
      <td><?php echo $obj_group->group_id; ?></td>
      <td><?php echo Html::anchor('/group/groupedit/'. $obj_group->group_id. '/', $obj_group->group_name); ?></td>
      <td><?php echo $obj_group->category_name; ?></td>
    </tr>
    <?php endforeach;?>
  </table>
 -->
</div>

<style type="text/css">
<!--
#div_high_login {
  background: none repeat scroll 0 0 #FFFFFF;
  box-shadow: 0 0 4px rgba(0, 0, 0, 0.7);
  margin: 10px auto 0px auto;
  padding: 10px;
  width: 80%;
  display: none;
}
.switch_login,
.switch_login:hover {
  height: auto;
  text-align: center;
}
#editregistindex_org_error {
  display: none;
}
// -->
</style>

<div id="modaldiv">
	<div id="div_high_login">
		<h3>ログイン再確認</h3>
		<h5>セキュリティ情報を更新するためグルーヴオンラインへのログインを再確認させていただきます。</h5>

			<fieldset class="ui-field-contain">
				<div class="label_div">
					<label for="editregistindex_email_org">メールアドレス</label>
				</div>
				<?php echo Form::input('email', null, array('placeholder' => 'メールアドレス', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'editregistindex_email_org'));?>
			</fieldset>

			<fieldset class="ui-field-contain">
				<div class="label_div">
					<label for="editregistindex_password_org">パスワード</label>
				</div>
				<?php echo Form::password('password', null, array('placeholder' => 'パスワード', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'editregistindex_password_org'));?>
			</fieldset>

			<div class="error" id="editregistindex_org_error">認証に失敗しました。メールアドレスまたはパスワードをご確認ください。</div>

			<br />

		<?php echo Form::input('submit', "送信", array('type' => 'submit', 'class' => 'global_submit_btn switch_login', 'data-role' => 'none', 'id' => 'editregistindex_high_login_submit'));?>

		<br />
		<br />

	</div>
</div>

