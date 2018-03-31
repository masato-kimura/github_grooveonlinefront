<?php echo Asset::js('pc/login/grooveonlineregistindex.js'); ?>
<?php echo Asset::css('pc/login/grooveonlineregistindex.css'); ?>

<div class="main_div">

	<h3 class="introduction">グルーヴオンライン新規ユーザ登録</h3>
	<div class="main_form">
	<?php echo Form::open(array('action' => 'login/grooveonlineregistconfirm?'. $this->parameter , 'method' => 'post', 'data-ajax' => 'false', 'enctype' => 'multipart/form-data'));?>

		<table class="gol_table">
			<tr>
				<th>
					<span class="main">※ グルーヴオンラインオリジナルで登録</span>
					<br />
					<span class="sub">メールアドレスとパスワードでログインします。</span>
				</th>
			</tr>

			<tr class="title">
				<td>
					<span class="main">メールアドレス</span>
					<span class="span_required">(必須)</span><br />
					<span class="sub">グルーヴオンラインにログインする場合に使用します。公開はされません。</span>
					<?php if ( ! empty($this->arr_error['email'])):?>
						<br />
						<span class="error"><?php echo $this->arr_error['email']; ?></span>
					<?php endif;?>
				</td>
			</tr>
			<tr class="ans">
				<td>
					<?php echo Form::input('email', $email, array('placeholder' => 'sample@sample.com', 'required' => 'required', 'autocomplete' => 'off'));?>
				</td>
			</tr>

			<tr class="title">
				<td>
					<span class="main">パスワード</span>
					<span class="span_required">(必須)</span><br />
					<span class="sub">半角英数(4～20文字)</span>
					<?php if ( ! empty($this->arr_error['password'])):?>
						<br />
						<span class="error"><?php echo $this->arr_error['password']; ?></span>
					<?php endif;?>
				</td>
			</tr>
			<tr class="ans">
				<td>
					<?php echo Form::password('password', $password, array('placeholder' => '1234abcdefg', 'required' => 'required', 'autocomplete' => 'off'));?>
				</td>
			</tr>

			<tr class="title">
				<td>
					<span class="main">ユーザ名</span>
					<span class="span_required">(必須)</span><br />
					<span class="sub">グルーヴオンラインで表示される名前です。(20文字まで)</span>
					<?php if ( ! empty($this->arr_error['user_name'])):?>
						<br />
						<span class="error"><?php echo $this->arr_error['user_name']; ?></span>
					<?php endif;?>
				</td>
			</tr>
			<tr class="ans">
				<td>
					<?php echo Form::input('user_name', $user_name, array('placeholder' => 'フレディ', 'required' => 'required', 'autofocus' => 'autofocus')); ?>
				</td>
			</tr>

			<tr>
				<td class="open_option">
					<span class="openclose">▼</span>
					<span class="openclose" style="display:none;">▲</span>
					<span>その他のオプション項目</span>
					<span class="openclose">▼</span>
					<span class="openclose" style="display:none;">▲</span>
				</td>
			</tr>

		  <tr class="title option">
		    <td>
		      <span class="main">性別</span>
		    </td>
		  </tr>

		  <tr class="ans option">
		    <td>
		      <input type="radio" name="gender" value="male"   id="gender_male"     <?php if ($gender === 'male'  ) echo "checked"; ?>><label for="gender_male">男</label>
		      <input type="radio" name="gender" value="female" id="gender_female"   <?php if ($gender === 'female') echo "checked"; ?>><label for="gender_female">女</label>
		      <input type="radio" name="gender" value="secret"   id="gender_secret" <?php if ($gender === 'secret') echo "checked"; ?>><label for="gender_secret">非公開</label>
		      <?php if ( ! empty($this->arr_error['gender'])):?>
		      <br /><span class='error'><?php echo $this->arr_error['gender']; ?></span>
		      <?php endif;?>
		    </td>
		  </tr>

		  <tr class="title option">
		    <td>
		      <span class="main">生年月日 &nbsp; / &nbsp;年齢</span>
		    </td>
		  </tr>
		  <tr class="ans option">
		    <td>
		    <?php echo Form::select("birthday_month", $this->birthday_month, $this->arr_birthday_month); ?> <span class="sub">月</span>
		    <?php echo Form::select("birthday_day",   $this->birthday_day,   $this->arr_birthday_day);   ?> <span class="sub">日</span>
		    <span>&nbsp; / &nbsp;</span>
		    <?php echo Form::select("old",  $this->old,  $this->arr_old);  ?> <span class="sub">歳</span>
		    <div class="birthday_checkbox_div">
			    <input type="checkbox" name="birthday_secret"  id="birthday_secret" value="1" <?php if ($this->birthday_secret === '1') echo "checked"; ?>>
			    <label for="birthday_secret">誕生日を公開しません</label>&nbsp;&nbsp;
			    <input type="checkbox" name="old_secret"  id="old_secret" value="1" <?php if ($this->old_secret === '1') echo "checked"; ?>>
			    <label for="old_secret">年齢を公開しません</label>
		    </div>
		    <?php if ( ! empty($arr_error['old'])):?>
		      <br /><span class='error'><?php echo $this->arr_error['old']; ?></span>
		    <?php endif;?>
		    </td>
		  </tr>

		  <tr class="title option">
		    <td>
		      <span class="main">お住まい地域</span>
		    </td>
		  </tr>
		<tr class="ans option">
			<td>
			<?php echo Form::select("pref", $this->pref, $this->arr_pref); ?>
			<?php if ( ! empty($this->arr_error['pref'])):?>
				<span class="error"><?php echo $this->arr_error['pref']; ?></span><br />
			<?php endif;?>
			</td>
		</tr>

		  <tr class="title option">
		    <td>
		      <span class="main">プロフィール画像</span><br />
		      <span class="sub">使用可能画像(jpg, gif, png) 3Mbyteまで</span>
		    </td>
		  </tr>
		  <tr class="ans option">
		    <td>
		       <?php if (Asset::find_file('tmp/'. Session::get('tmp_image_name'), 'img')):?>
		         <?php echo Asset::img('tmp/'. Session::get('tmp_image_name'));?>
		       <?php elseif ( ! empty($picture_url)): ?>
		         <img src="<?php echo $picture_url; ?>">
		      <?php endif;?>
		      <?php if ( ! empty($this->arr_error['image'])):?>
		        <?php echo "<br /><span class='error'>". $this->arr_error['image']. "</span>"; ?>
		      <?php endif; ?>
		      <input type="file" name="pic">
		    </td>
		  </tr>

		  <tr class="title option">
		    <td>
		      <span class="main">自己紹介</span>
		    </td>
		  </tr>
		  <tr class="ans option">
		    <td>
		      <textarea rows="5" cols="30" name="profile_fields"><?php echo $profile_fields; ?></textarea>
		      <?php if ( ! empty($this->arr_error['profile_fields'])):?>
		        <?php echo "<br /><span class='error'>". $this->arr_error['profile_fields']; ?></span>
		      <?php endif;?>
		    </td>
		  </tr>

		  <tr class="autologin">
		    <td>
		      <input type="checkbox" name="auto_login" id="auto_login" value="1" <?php echo (isset($auto_login) && $auto_login == 1) ? ' checked' : ' checked'; ?>>
		      <label for="auto_login">次回からのログインを自動化します</label>
		    </td>
		  </tr>

		  <tr>
		    <td>
		      <input type="hidden" name="auth_type" value="<?php echo $auth_type;?>">
		      <input type="hidden" name="oauth_id" value="<?php echo $oauth_id;?>">
		      <input type="hidden" name="picture_url" value="<?php echo $picture_url?>">
		    </td>
		  </tr>

		  <tr class="submit">
		    <td>
		      <?php echo Form::submit('submit', "登録内容を確認します", array('class' => 'global_submit_btn'));?>
			 </td>
		  </tr>
		</table>
		</form>

		<table class="oauth_table">
		  <tr>
		    <th>
		      <span class="main">※ 次のアカウントお持ちの方は今すぐご利用可能です。</span><br />
		      <span class="sub">ログイン後にグルーヴオンラインで使用する表示名などのユーザ情報を変更することができます。</span><br />
		    </th>
		  </tr>
		  <tr class="oauth">
		    <td class="oauth_facebook">
		    	<?php if (isset($parameter)):?>
					<?php echo Html::anchor('login/facebook/?'. $parameter. '&auto_login=', Form::button('facebook', 'facebookでログイン', array('class' => 'global_facebook_login_btn'))); ?>
		    	<?php else: ?>
					<?php echo Html::anchor('login/facebook/?auto_login=', Form::button('facebook', 'facebookでログイン', array('class' => 'global_facebook_login_btn'))); ?>
		    	<?php endif; ?>
		    </td>
		  </tr>
		  <tr class="oauth">
		    <td class="oauth_google">
		    	<?php if (isset($parameter)):?>
		    		<?php echo Html::anchor('login/google/?'. $parameter. '&auto_login=', Form::button('google', 'Google+でログイン', array('class' => 'global_google_login_btn'))); ?>
		    	<?php else:?>
		    		<?php echo Html::anchor('login/google/?auto_login=', Form::button('google', 'Google+でログイン', array('class' => 'global_google_login_btn'))); ?>
		    	<?php endif;?>
		    </td>
		  </tr>
		  <tr class="oauth">
		    <td class="oauth_twitter">
		    	<?php if (isset($parameter)):?>
		    		<?php echo Html::anchor('login/twitter/?'. $parameter. '&auto_login=', Form::button('twitter', 'twitterでログイン', array('class' => 'global_twitter_login_btn'))); ?>
		    	<?php else: ?>
		    		<?php echo Html::anchor('login/twitter/?auto_login=', Form::button('twitter', 'twitterでログイン', array('class' => 'global_twitter_login_btn'))); ?>
		    	<?php endif;?>
		    </td>
		  </tr>
		  <tr class="oauth">
		    <td class="oauth_yahoo">
		    	<?php if (isset($parameter)):?>
		    		<?php echo Html::anchor('login/yahoo/?'. $parameter. '&auto_login=', Form::button('yahoo', 'Yahoo! JAPAN IDでログイン', array('class' => 'global_yahoo_login_btn')));?>
		    	<?php else: ?>
		    		<?php echo Html::anchor('login/yahoo/?auto_login=', Form::button('yahoo', 'Yahoo! JAPAN IDでログイン', array('class' => 'global_yahoo_login_btn')));?>
		    	<?php endif;?>
		    </td>
		  </tr>
		  <tr class="autologin">
		    <td>
		      <input type="checkbox" name="auto_login" id="auto_login_oauth" value="1" <?php echo (isset($auto_login) && $auto_login == 1) ? ' checked' : ' checked'; ?>>
		      <label for="auto_login_oauth">次回からのログインを自動化します</label>
		    </td>
		  </tr>
		</table>
</div>
</div>
