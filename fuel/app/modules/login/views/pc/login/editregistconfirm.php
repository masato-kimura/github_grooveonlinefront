<?php echo Asset::css('pc/login/editregistconfirm.css');?>
<?php echo Asset::js('pc/login/editregistconfirm.js');?>

<div class="main_div">

	<h3 class="introduction">グルーヴオンライン更新登録確認画面</h3>

	<table class="input_table">
		<tr class="title">
			<td>
				<span class="main">ユーザ名(必須)</span>
			</td>
		</tr>
		<tr class="ans">
			<td>
				<?php echo $this->user_name; ?>
			</td>
		</tr>

	<?php if ( ! empty($email_disp)): ?>
	<tr class="title">
		<td>
			<span class="main">メールアドレス</span>
		</td>
	</tr>
	<tr class="ans">
		<td>
			<?php echo $this->email_disp;?>
		</td>
	</tr>
	<?php endif;?>

    <?php if ($auth_type === 'grooveonline'):?>
    <tr class="title">
      <td>
        <span	class="main">パスワード(必須)</span>
      </td>
    </tr>
    <tr	class="ans">
      <td>
      <?php	echo	preg_replace('/./','*',$password); ?>
      </td>
    </tr>
    <?php endif; ?>

    <tr class="title option">
      <td>
        <span class="main">プロフィール画像</span>
      </td>
    </tr>
    <tr class="ans option">
      <td>
        <?php $obj_tmp_image = Session::get('tmp_image');?>
        <?php if ( ! empty($obj_tmp_image) && Asset::find_file('tmp/'. $obj_tmp_image->name, 'img')):?>
          <?php echo Asset::img('tmp/'. Session::get('tmp_image')->name);?>
        <?php else:?>
          <img src='<?php echo empty($user_image)? \Config::get('image.user.large'): $user_image; ?>?<?php echo \Date::forge()->format("%H%M%S");?>'>
        <?php endif;?>
      </td>
    </tr>

	<?php if (isset($this->gender_disp)):?>
	<tr class="title option">
		<td>
			<span class="main">性別</span>
		</td>
	</tr>
	<tr	class="ans option">
		<td><?php echo $this->gender_disp; ?>
		</td>
	</tr>
	<?php	endif;	?>

	<?php if ( ! empty($old)):?>
		<tr class="title option">
		<td>
			<span class="main">年齢</span>
		</td>
		</tr>
		<tr class="ans option">
		<td>
			<?php echo $old; ?>歳
			<?php if ($old_secret == '1'): ?>
			<br />年齢を公開しません
			<?php endif;?>
		</td>
		</tr>
	<?php endif; ?>

	<?php	if	(	!	empty($birthday_year)	||	!	empty($birthday_month)	||	!	empty($birthday_day)):	?>
		<tr	class="title	option">
		<td>
			<span	class="main">生年月日</span>
		</td>
		</tr>

		<tr	class="ans	option">
		<td>
		<?php	if	(	!	empty($birthday_year)):?>
		<?php	echo	$birthday_year;?>年
		<?php	endif;	?>

		<?php	if	(	!	empty($birthday_month)):?>
		<?php	echo	$birthday_month;?>月
		<?php	endif;	?>

		<?php	if	(	!	empty($birthday_day)):?>
		<?php	echo	$birthday_day;?>日
		<?php	endif;	?>
		<span>生まれ</span>
		<?php	if	($birthday_secret	==	'1'):?>
		<br	/>誕生日を公開しません
		<?php	endif;	?>
		</td>
		</tr>
	<?php	endif;?>

	<?php if ( ! empty($pref)): ?>
		<tr class="title	option">
		<td>
			<span	class="main">お住まい地域</span>
		</td>
		</tr>
		<tr	class="ans	option">
		<td><?php	echo	$pref;	?></td>
		</tr>
	<?php endif; ?>

	<?php $profile_fields = trim($profile_fields); ?>
	<?php if( ! empty($profile_fields)): ?>
		<tr	class="title	option">
		<td>
			<span	class="main">自己紹介</span>
			</td>
		</tr>
		<tr	class="ans	option">
			<td><?php	echo	$profile_fields;	?></td>
		</tr>
	<?php	endif;	?>
	</table>

	<br />
	<br />

	<?php echo Form::open(array('action' =>'login/editregistindex', 'method' => 'post', 'id' => 'back_form')). PHP_EOL; ?>
	<?php echo Form::hidden('user_name', $user_name). PHP_EOL; ?>
	<?php echo Form::hidden('email', $email). PHP_EOL; ?>
	<?php echo Form::hidden('password', $password). PHP_EOL; ?>
	<?php echo Form::hidden('gender', $gender). PHP_EOL; ?>
	<?php echo Form::hidden('birthday_year', $birthday_year).	PHP_EOL; ?>
	<?php echo Form::hidden('birthday_month', $birthday_month).	PHP_EOL; ?>
	<?php echo Form::hidden('birthday_day', $birthday_day).	PHP_EOL; ?>
	<?php echo Form::hidden('birthday_secret', $birthday_secret).	PHP_EOL; ?>
	<?php echo Form::hidden('old',	$old). PHP_EOL; ?>
	<?php echo Form::hidden('old_secret', $old_secret). PHP_EOL; ?>
	<?php echo Form::hidden('pref',	$pref).	PHP_EOL; ?>
	<?php echo Form::hidden('profile_fields', $profile_fields). PHP_EOL; ?>
	<?php echo Form::hidden('auth_type', $auth_type). PHP_EOL; ?>
	<?php echo Form::hidden('oauth_id', $oauth_id). PHP_EOL; ?>
	<?php echo	Form::submit('back', '入力フォームに戻る', array('class' => 'btn', 'type' => 'button','id' => 'back_button')). PHP_EOL; ?>
	<?php echo Form::close(). PHP_EOL; ?>

	<?php echo Form::open(array('action' => \Config::get('host.base_url_https'). '/login/editregistexecute/', 'method' => 'post')). PHP_EOL; ?>
	<?php echo Form::hidden('user_name', $user_name). PHP_EOL; ?>
	<?php echo Form::hidden('email',	$email).	PHP_EOL;	?>
	<?php echo Form::hidden('password',	$password).	PHP_EOL;	?>
	<?php echo Form::hidden('gender',	$gender).	PHP_EOL;	?>
	<?php echo Form::hidden('birthday_year',	$birthday_year).	PHP_EOL;	?>
	<?php echo Form::hidden('birthday_month',	$birthday_month).	PHP_EOL;	?>
	<?php echo Form::hidden('birthday_day',	$birthday_day).	PHP_EOL;?>
	<?php echo Form::hidden('birthday_secret',	$birthday_secret).	PHP_EOL;	?>
	<?php echo Form::hidden('old',	$old).	PHP_EOL;	?>
	<?php echo Form::hidden('old_secret',	$old_secret).	PHP_EOL;?>
	<?php echo Form::hidden('pref',	$pref).	PHP_EOL;?>
	<?php echo Form::hidden('profile_fields',	$profile_fields).	PHP_EOL;	?>
	<?php echo Form::hidden('auth_type',	$auth_type).	PHP_EOL;	?>
	<?php echo Form::hidden('oauth_id',	$oauth_id).	PHP_EOL;	?>
	<?php echo Form::hidden('date',	Date::forge()->format('%Y-%m-%d	%H:%M:%S')).	PHP_EOL;?>
	<?php echo Form::submit('submit', 'ユーザ情報を更新します', array('class' => 'global_submit_btn')).	PHP_EOL;?>
	<?php echo Form::close();?>
</div>
