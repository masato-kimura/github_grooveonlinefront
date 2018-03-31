use Fuel\Core\Input;
<style type="text/css">
<!--
.error_disp { color:red; }
// -->
</style>

<h3>グループをつくろうの確認</h3>

<table>
	<tr>
		<td>グループ名</td>
		<td><?php echo $name; ?></td>
	</tr>
	<tr>
		<td>カテゴリー</td>
		<td><?php echo $category; ?></td>
	</tr>
	<tr>
		<td>カテゴリー名</td>
		<td><?php echo $category_name; ?></td>
	</tr>
	<tr>
		<td>紹介文</td>
		<td><?php echo $profile_fields; ?></td>
	</tr>
	<tr>
		<td>
		<?php if (Asset::find_file('tmp/'. Session::get('tmp_group_image_name'), 'img')):?>
			<?php echo Asset::img('tmp/'. Session::get('tmp_group_image_name')); ?>
		<?php else: ?>
			<img src="<?php echo $this->picture_url;?>">
		<?php endif;?>
		</td>
	</tr>
</table>


<?php echo Form::open(array('action' => 'group/groupdone/', 'method' => 'post', 'enctype' => 'multipart/form-data')); ?>
<?php echo Form::input('name', $name); ?>
<?php echo Form::input('category_id', $category_id); ?>
<?php echo Form::input('category_name', $category_name);?>
<?php echo Form::input('profile_fields', $profile_fields);?>
<?php echo Form::submit('submit', '送信');?>
<?php echo Form::close();?>

<?php echo Form::open(array('action' => 'group/groupcreate/', 'method' => 'post', 'enctype' => 'multipart/form-data')); ?>
<?php echo Form::input('name', $name); ?>
<?php echo Form::input('category_id', $category_id); ?>
<?php echo Form::input('category_name', $category_name);?>
<?php echo Form::input('profile_fields', $profile_fields);?>
<?php echo Form::submit('submit', '戻る');?>
<?php echo Form::close();?>