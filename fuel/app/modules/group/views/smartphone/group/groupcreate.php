<style type="text/css">
<!--
.error_disp { color:red; }
// -->
</style>

<h3>グループをつくろう</h3>

<?php echo Form::open(array('action' => 'group/groupconfirm/', 'method' => 'post', 'enctype' => 'multipart/form-data')); ?>
<table>
	<tr>
		<td>グループ名</td>
		<td><?php echo Form::input('name', $name);?></td>
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
		<?php echo Form::input('category_name', $category_name);?></td>
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
		<td>画像<?php echo Session::get('tmp_group_image_name');?></td>
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