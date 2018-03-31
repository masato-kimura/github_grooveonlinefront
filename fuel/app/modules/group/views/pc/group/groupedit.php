<style type="text/css">
<!--
.error_disp { color:red; }
// -->
</style>

<h3>グループ編集</h3>

<?php echo Form::open(array('action' => 'group/groupeditconfirm/', 'method' => 'post', 'enctype' => 'multipart/form-data')); ?>
<table class="group_table">
	<tr>
		<td>グループ名</td>
		<td>
			<?php echo Form::input('name', $group_name);?>
			<?php echo Form::input('group_id', $group_id);?>
		</td>
	</tr>
	<tr>
		<td colspan="2"><span class="error_disp"><?php echo ! empty($arr_error['name']) ? $arr_error['name']: null;?></span></td>
	</tr>
	<tr>
		<td>カテゴリー</td>
		<td>
		<?php foreach ($this->obj_category as $val): ?>
			<?php if ($val->id == $category_id):?>
				<?php echo Form::radio('category_id', $val->id, true, array('id' => 'form_category_'. $val->id)); ?>
			<?php else:?>
				<?php echo Form::radio('category_id', $val->id, false, array('id' => 'form_category_'. $val->id)); ?>
			<?php endif; ?>
		<?php echo Form::label($val->name, 'category_'. $val->id); ?>
		<?php endforeach;?>
		<br />
		<?php echo Form::input('category_name', null);?></td>
	</tr>
	<tr>
		<td colspan="2">
			<span class="error_disp"><?php echo ! empty($arr_error['category_id']) ? $arr_error['category_id']: null;?></span>
			<br />
			<span class="error_disp"><?php echo ! empty($arr_error['category_name']) ? $arr_error['category_name']: null;?></span>
		</td>
	</tr>
	<tr>
		<td>紹介文</td>
		<td><?php echo Form::textarea('profile_fields', $profile_fields);?></td>
	</tr>
	<tr>
		<td colspan="2"><span class="error_disp"><?php echo ! empty($arr_error['profile_fields']) ? $arr_error['profile_fields']: null;?></span></td>
	</tr>
	<tr>
		<td>画像</td>
		<td>
		<?php if (Asset::find_file('tmp/'. Session::get('tmp_group_image_name'), 'img')):?>
			<?php echo Asset::img('tmp/'. Session::get('tmp_group_image_name'));?>
		<?php elseif ( ! empty($group_image)): ?>
			<img src='<?php echo $group_image; ?>?<?php echo \Date::forge()->format("%H%M%S");?>'>
		<?php endif; ?>

		<?php echo "<br /><span class='error'>". $error_image. "</span>"; ?>

		<input type="file" name="pic">
		<?php echo "<br /><span class='error'>". ""; ?></span>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><?php echo Form::submit('submit', '送信');?></td>
	</tr>
</table>
<?php echo Form::close();?>

<hr />

<h1>※グループメンバー</h1>

<div>メンバーを追加する</div>
<?php echo Form::open(array('action' => 'group/memberadd/', 'method' => 'post')); ?>
<table>
	<tr>
		<th>名前</th>
	</tr>
	<tr>
		<td><?php echo Form::input('name'); ?><?php echo Form::input('group_id', $this->group_id);?></td>
	</tr>
	<tr>
		<td><?php echo Form::submit('submit', '登録'); ?></td>
	</tr>
</table>
<?php echo Form::close();?>

<br />

<div>メンバーを外す</div>
<table>
<?php foreach ($this->members as $member):?>
	<tr>
		<td><?php echo $member->user_name; ?></td>
		<td>
			<?php echo Form::open(array('action' => 'group/memberdel/', 'method' => 'post'));?>
			<?php echo Form::input('group_id', $this->group_id);?>
			<?php echo Form::input('member_id', $member->user_id);?>
			<?php echo Form::input('link', \Config::get('host.base_url_https'). "/login/grooveonlineregistindex/?invited_by=group&group_id={$this->group_id}&target_id={$member->user_id}&invite_id={$this->user_id}");?>
			<?php echo Form::submit('submit', '脱退'); ?>
			<?php echo Form::close();?>
		</td>
	</tr>
<?php endforeach;?>
</table>


