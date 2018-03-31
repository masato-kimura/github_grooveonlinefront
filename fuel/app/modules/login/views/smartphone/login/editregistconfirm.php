<?php echo Asset::css('smartphone/login/editregistconfirm.css');?>
<?php echo Asset::js('smartphone/login/editregistconfirm.js');?>

<script type="text/javascript">
<!--
$(document).bind("pageinit", function(){
  $.mobile.ajaxEnabled = false;
});
//-->
</script>

<div class="main_div">
	<h3 class="introduction">グルーヴオンライン・ユーザープロフィール更新内容の確認</h3>
	<br />
	<table	class="input_table">
		<tr class="title">
			<td>
				<span class="main">ユーザー名(必須)</span>
			</td>
		</tr>
		<tr class="ans">
			<td>
				<?php echo $user_name; ?>
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
				<?php echo $email_disp;?>
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

	<?php if (isset($gender_disp)):?>
	<tr class="title option">
		<td>
			<span class="main">性別</span>
		</td>
	</tr>
	<tr class="ans option">
		<td><?php echo $gender_disp; ?>
		</td>
	</tr>
	<?php	endif;	?>

	<?php if ( ! empty($old)):?>
		<tr class="title option">
		<td>
			<span class="main">年齢</span>
		</td>
		</tr>
		<tr class="ans	option">
		<td>
			<?php echo $old;?>歳
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

		<tr class="ans option">
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

	<?php if ( ! empty($facebook_url)):?>
		<tr class="title option">
			<td>
				<span class="main">Facebook</span>
			</td>
		</tr>
		<tr class="ans option">
			<td><?php echo $facebook_url?></td>
		</tr>
	<?php endif;?>

	<?php if ( ! empty($twitter_url)):?>
		<tr class="title option">
			<td>
				<span class="main">Twitter</span>
			</td>
		</tr>
		<tr class="ans option">
			<td><?php echo $twitter_url?></td>
		</tr>
	<?php endif;?>

	<?php if ( ! empty($google_url)):?>
		<tr class="title option">
			<td>
				<span class="main">Google</span>
			</td>
		</tr>
		<tr class="ans option">
			<td><?php echo $google_url?></td>
		</tr>
	<?php endif;?>

	<?php if ( ! empty($instagram_url)):?>
		<tr class="title option">
			<td>
				<span class="main">instagram</span>
			</td>
		</tr>
		<tr class="ans option">
			<td><?php echo $instagram_url?></td>
		</tr>
	<?php endif;?>

	<?php if ( ! empty($site_url)):?>
		<tr class="title option">
			<td>
				<span class="main">ブログ、ウェブサイト URL</span>
			</td>
		</tr>
		<tr class="ans option">
			<td><?php echo $site_url?></td>
		</tr>
	<?php endif;?>

		<tr>
			<td></td>
			<td>
			</td>
		</tr>
	</table>

	<br />
	<br />
	<?php echo Form::open(array('action' =>'login/editregistindex', 'method' => 'post', 'id' => 'back_form', 'data-ajax' => 'false')). PHP_EOL; ?>
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
	<?php echo Form::hidden('facebook_url', $facebook_url). PHP_EOL;?>
	<?php echo Form::hidden('twitter_url', $twitter_url). PHP_EOL;?>
	<?php echo Form::hidden('google_url', $google_url). PHP_EOL;?>
	<?php echo Form::hidden('instagram_url', $instagram_url). PHP_EOL;?>
	<?php echo Form::hidden('site_url', $site_url). PHP_EOL;?>
	<?php echo Form::hidden('auth_type', $auth_type). PHP_EOL; ?>
	<?php echo Form::hidden('oauth_id', $oauth_id). PHP_EOL; ?>
	<?php echo Form::input('back', '入力フォームに戻る', array('type' => 'button','id' => 'back_button', 'data-role' => 'none', 'class' => 'global_back_btn btn')). PHP_EOL; ?>
	<?php echo Form::close(). PHP_EOL; ?>

	<?php echo Form::open(array('action' => \Config::get('host.base_url_https'). '/login/editregistexecute/', 'id' => 'submit_form', 'method' => 'post', 'data-ajax' => 'false')). PHP_EOL; ?>
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
	<?php echo Form::hidden('facebook_url', $facebook_url). PHP_EOL;?>
	<?php echo Form::hidden('twitter_url', $twitter_url). PHP_EOL;?>
	<?php echo Form::hidden('google_url', $google_url). PHP_EOL;?>
	<?php echo Form::hidden('instagram_url', $instagram_url). PHP_EOL;?>
	<?php echo Form::hidden('site_url', $site_url). PHP_EOL;?>
	<?php echo Form::hidden('auth_type',	$auth_type).	PHP_EOL;	?>
	<?php echo Form::hidden('oauth_id',	$oauth_id).	PHP_EOL;	?>
	<?php echo Form::hidden('date',	Date::forge()->format('%Y-%m-%d	%H:%M:%S')).	PHP_EOL;?>
	<?php echo Form::submit('submit', 'ユーザ情報を更新します', array('class' => 'global_submit_btn', 'data-role' => 'none')). PHP_EOL;?>
	<?php echo Form::close();?>

</div>
