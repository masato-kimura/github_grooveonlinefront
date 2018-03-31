<?php echo Asset::css('pc/login/editregistindex.css'); ?>
<?php echo Asset::js('jquery.leanModal.min.js');?>
<script type="text/javascript">
<!--
$(document).bind("pageinit", function(){
  $.mobile.ajaxEnabled = false;
});

$(function() {
	var modal = $('a[rel*=leanModal]').leanModal({
		top: 40,
		overlay : 0.5,
		closeButton: ".modal_close",
	});

	setTimeout(function() {
		$('#editregistindex_email_org').val(null);
		$('#editregistindex_password_org').val(null);
	}, 500);

	var password_tmp = $('#editregistindex_password').val();

	$('#editregistindex_password').on('focus', function() {
		if ($(this).val() === password_tmp) {
			$(this).val(null);
		}
	});

	$('#editregistindex_password').on('blur', function() {
		if ($(this).val().length === 0) {
			$(this).val(password_tmp);
		}
	});

	$('#editregistindex_high_login_submit').click(function(){
		var modal_id = '#div_high_login';
		var email    = $('#editregistindex_email_org').val();
		var password = $('#editregistindex_password_org').val();
		email        = htmlentities(email);
		password     = htmlentities(password);

		is_available_login(email, password).done(function(res) {
			if (res.length == 0) {
				$('#editregistindex_org_error').show('fast');
				return true;
			}

			var result = res.result.is_available;
			if (result) {
				$("#lean_overlay").fadeOut(200);
				$(modal_id).css({ 'display' : 'none' });
				$('#editregistindex_email_disp').css('display', 'none');
				$('#editregistindex_password_disp').css('display', 'none');
				$('#editregistindex_email_button').parent().addClass('ui-screen-hidden');
				$('#editregistindex_password_button').parent().addClass('ui-screen-hidden');
				$('#editregistindex_email').val(email);

				setTimeout(function(){
					$('#editregistindex_email').removeClass('ui-screen-hidden');
					$('#editregistindex_password').removeClass('ui-screen-hidden');
				}, 50);
			} else {
				$('#editregistindex_org_error').show('fast');
			}

			return true;
		});
	});

	function is_available_login(email, password) {
		var params = {email: email, password: password};
		var result = null;
		return $.ajax({
			type: 'post',
			url: '/api/login/grooveonlineavailablelogin.json',
			datatype: 'json',
			data: JSON.stringify(params),
			contentType: 'application/json',
			cache: false,
			success: function(res, ans) {
				return true;
			},
			error: function(){
				alert('network error');
				return false;
			}
		});
	}

	function htmlentities(str) {
		return str.replace(/&/g, "&amp;")
		.replace(/"/g, "&quot;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;");
	}
});

//-->
</script>

<style type="text/css">
#lean_overlay {
    position: fixed;
    z-index:100;
    top: 0px;
    left: 0px;
    height:100%;
    width:100%;
    background: #000;
    display: none;
}
.ui-screen-hidden {
  display: none;
}
#editregistindex_email_disp,
#editregistindex_password_disp {
  padding: 8px 5px;
  color: #555;
}
</style>


<div class="main_div">

	<h3 class="introduction">グルーヴオンラインユーザ情報更新</h3>

	<?php echo Form::open(array('action' => \Config::get('host.base_url_https'). '/login/editregistconfirm', 'method' => 'post', 'enctype' => 'multipart/form-data'));?>
	<table class="user_table">
		<?php if ($auth_type ==='grooveonline'):?>
			<tr class="title">
				<td>
					<span class="main">メールアドレス</span>
					<span class="span_required">(必須)</span>
					<br />
					<span class="sub">グルーヴオンラインにログインする場合に使用します。公開はされません。</span>
					<?php if ( ! empty($this->arr_error['email'])):?>
						<br />
						<span class="error"><?php echo $this->arr_error['email']; ?></span>
					<?php endif;?>
				</td>
			</tr>
			<tr class="ans">
				<td>
					<?php if ($this->high_level_disp_flg):?>
						<?php echo Form::input('email', $this->email, array('placeholder' => 'メールアドレス', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'editregistindex_email'));?>
					<?php else: ?>
						<div id="editregistindex_email_disp"><?php echo $this->email_convert;?></div>
						<?php echo Form::input('email', $this->email, array('placeholder' => 'メールアドレス', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'editregistindex_email', 'class' => 'high_login ui-screen-hidden'));?>
						<a href="#div_high_login" rel="leanModal"><?php echo Form::input('editregistindex_email_button', 'メールアドレスを変更', array('type' => 'button', 'id' => 'editregistindex_email_button', 'class' => 'btn'));?></a>
						<br />
					<?php endif;?>
				</td>
			</tr>
		<?php else: ?>
			<tr class="title">
				<td>
					<span class="main">メールアドレス</span>
					<br />
					<span class="sub">グルーヴオンラインからのお知らせをお送りします。公開はされません。</span>
					<?php if ( ! empty($this->arr_error['email'])):?>
						<br />
						<span class="error"><?php echo $this->arr_error['email']; ?></span>
					<?php endif;?>
				</td>
			</tr>
			<tr class="ans">
				<td>
					<input type="text" name="email" value="<?php echo $this->email;?>">
				</td>
			</tr>
		<?php endif; ?>

		<?php if ($auth_type === 'grooveonline'):?>
		<tr class="title">
			<td>
				<span class="main">パスワード</span>
				<span class="span_required">(必須)</span>
				<span class="sub">半角英数(4～20文字)</span>
				<?php if ( ! empty($this->arr_error['password'])):?>
					<br />
					<span class="error"><?php echo $this->arr_error['password']; ?></span>
				<?php endif;?>
			</td>
		</tr>
		<tr class="ans">
			<td>
				<?php if ($this->high_level_disp_flg):?>
					<?php echo Form::password('password', $this->password, array('placeholder' => 'パスワード', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'editregistindex_password'));?>
				<?php else:?>
					<div id="editregistindex_password_disp"><?php echo $this->password_astarisk; ?></div>
					<?php echo Form::password('password', $this->password, array('placeholder' => 'パスワード', 'required' => 'required', 'autocomplete' => 'off', 'id' => 'editregistindex_password', 'class' => 'high_login ui-screen-hidden'));?>
					<a href="#div_high_login" rel="leanModal"><?php echo Form::input('editregistindex_password_button', 'パスワードを変更', array('type' => 'button' , 'id' => 'editregistindex_password_button', 'data-inline' => 'true', 'class' => 'btn'));?></a>
					<br />
				<?php endif;?>
			</td>
		</tr>
		<?php endif; ?>

		<tr class="title">
			<td>
				<span class="main">ユーザ名</span>
				<span class="span_required">(必須)</span>
				<br />
				<span class="sub">グルーヴオンラインで表示されるユーザー名です。(20文字まで)</span>
				<?php if ( ! empty($this->arr_error['user_name'])):?>
					<br />
					<span class="error"><?php echo $this->arr_error['user_name']; ?></span>
				<?php endif;?>
			</td>
		</tr>
		<tr class="ans">
			<td>
				<?php echo Form::input('user_name', $this->user_name, array()); ?>
			</td>
		</tr>

		<tr class="title">
			<td>
				<span class="main">ログイン</span>
				<br />
				<span class="sub">ログイン方法は変更できません。</span>
				<br />
			</td>
		</tr>
		<tr class="ans">
			<td>
			<?php
			switch ($auth_type)
			{
				case 'facebook':
					echo "<div class='global_facebook'>facebook</div>". PHP_EOL;
					break;
				case 'google':
					 echo "<div class='global_google'>google+</div>". PHP_EOL;
					break;
				case 'twitter':
					echo "<div class='global_twitter'>twitter</div>". PHP_EOL;
					break;
				case 'yahoo':
					echo "<div class='global_yahoo'>yahoo</div>". PHP_EOL;
					break;
				case 'grooveonline':
					echo "<div class='global_grooveonline'>grooveonline</div>". PHP_EOL;
				break;
			}
			?>
			</td>
		</tr>

		<tr class="title option">
			<td>
				<span class="main">性別</span>
			</td>
		</tr>

		<tr class="ans option">
			<td>
				<input type="radio" name="gender" value="male" id="gender_male"     <?php if ($gender === 'male')   echo "checked"; ?>><label for="gender_male">男</label>
				<input type="radio" name="gender" value="female" id="gender_female" <?php if ($gender === 'female') echo "checked"; ?>><label for="gender_female">女</label>
				<input type="radio" name="gender" value="secret" id="gender_secret" <?php if ($gender === 'secret') echo "checked"; ?>><label for="gender_secret">非公開</label>
				<?php if ( ! empty($this->arr_error['gender'])):?>
					<br />
					<span class='error'><?php echo $this->arr_error['gender']; ?></span>
				<?php endif;?>
			</td>
		</tr>

		<tr class="title option">
			<td>
				<span class="main">誕生月日 &nbsp; / &nbsp;年齢</span>
			</td>
		</tr>
		<tr class="ans option">
			<td>
				<?php echo Form::select("birthday_month", $this->birthday_month, $this->arr_birthday_month); ?>
				<?php echo Form::select("birthday_day", $this->birthday_day,	$this->arr_birthday_day); ?>
				<span>&nbsp; / &nbsp;</span>
				<?php echo Form::select("old", $old, $this->arr_old);?> 歳
				<br />
				<input type="checkbox" name="birthday_secret"  id="birthday_secret" value="1" <?php if ($birthday_secret === '1') echo "checked"; ?>>
				<label for="birthday_secret">誕生日を公開しません</label>&nbsp;&nbsp;
				<input type="checkbox" name="old_secret"  id="old_secret" value="1" <?php if ($old_secret === '1') echo "checked"; ?>>
				<label for="old_secret">年齢を公開しません</label>
				<?php if ( ! empty($this->arr_error['old'])):?>
					<br />
					<span class='error'><?php echo $this->arr_error['old']; ?></span>
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
				<?php echo  Form::select("pref", $pref, $this->arr_pref, array('id' => 'registindex_pref')); ?>
				<?php if ( ! empty($this->arr_error['pref'])):?>
					<br />
					<span class="error"><?php echo $this->arr_error['pref']; ?></span>
				<?php endif;?>
			</td>
		</tr>

		<tr class="title option">
			<td>
				<span class="main">プロフィール画像</span>
				<br />
				<span class="sub">使用可能画像(jpg, gif, png) 3Mbyteまで</span>
			</td>
		</tr>
		<tr class="ans option">
			<td>
				<?php if ( ! empty($this->user_image)): ?>
					<img src='<?php echo $this->user_image; ?>?<?php echo \Date::forge()->format("%H%M%S");?>'>
				<?php endif; ?>

				<?php if ( ! empty($this->arr_error['image'])):?>
					<br />
					<span class='error'><?php echo $this->arr_error['image']; ?></span>
				<?php endif;?>
				<br />
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
					<br />
					<span class='error'><?php echo $this->arr_error['profile_fields']; ?></span>
				<?php endif;?>
			</td>
		</tr>

		<tr class="submit">
			<td>
				<input type="hidden" name="auth_type"   value="<?php echo $auth_type;?>">
				<input type="hidden" name="oauth_id"    value="<?php echo $oauth_id;?>">
				<input type="hidden" name="picture_url" value="<?php echo $picture_url?>">
				<?php echo Form::submit('submit', "登録内容を確認します", array('class' => 'global_submit_btn'));?>
			</td>
		</tr>
	</table>
	</form>

<!--
  <table class="group_table">
    <tr>
      <td colspan="3">※所属グループ</td>
    </tr>
    <?php foreach ($this->group as $group): ?>
    <tr>
      <td><?php echo $group->group_id; ?></td>
      <td><?php echo Html::anchor('/group/groupedit/'. $group->group_id. '/', $group->group_name); ?></td>
      <td><?php echo $group->category_name; ?></td>
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
  margin: 0px auto 0px auto;
  padding: 0px 30px 20px 30px;
  width: 480px;
  display: none;
}
.switch_login,
.switch_login:hover {
  height: auto;
  text-align: center;
}
.ui-field-contain {
  border: 0px;
  margin-left: 25px;
}
#editregistindex_email_org,
#editregistindex_password_org {
  width: 90%;
  height: 30px;
  padding: 0px 10px;
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
