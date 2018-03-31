confirm


<table>
	<tr>
		<td>レビュータイプ</td>
		<td><?php echo $this->about; ?></td>
	</tr>
	<tr>
		<td>評価</td>
		<td><?php echo $this->star; ?></td>
	</tr>
	<tr>
		<td>アーティスト</td>
		<td><?php echo $this->artist_name; ?></td>
	</tr>
	<tr>
		<td>アルバム</td>
		<td><?php echo $this->album_name; ?></td>
	</tr>
	<tr>
		<td>楽曲</td>
		<td><?php echo $this->music_name; ?></td>
	</tr>
	<tr>
		<td>リンク</td>
		<td><?php echo $this->link; ?></td>
	</tr>
	<tr>
		<td>レビュー</td>
		<td><?php echo $this->review; ?></td>
	</tr>
</table>

<?php echo Form::open('review/music/write/'); ?>
<?php echo Form::input('artist_name', $this->artist_name); ?>
<?php echo Form::input('album_name', $this->album_name); ?>
<?php echo Form::input('music_name', $this->music_name); ?>
<?php echo Form::input('link', $this->link); ?>
<?php echo Form::input('review', $this->review); ?>
<?php echo Form::input('about', $this->about); ?>
<?php echo Form::input('star', $this->star); ?>
<?php echo Form::submit('submit', '戻る'); ?>
<?php echo Form::close(); ?>


<?php echo Form::open('review/music/done/'); ?>
<?php echo Form::input('artist_name', $this->artist_name); ?>
<?php echo Form::input('album_name', $this->album_name); ?>
<?php echo Form::input('music_name', $this->music_name); ?>
<?php echo Form::input('link', $this->link); ?>
<?php echo Form::input('review', $this->review); ?>
<?php echo Form::input('about', $this->about); ?>
<?php echo Form::input('star', $this->star); ?>
<?php echo Form::submit('submit', '送信'); ?>
<?php echo Form::close(); ?>

