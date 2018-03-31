
<div class="row">
	<div class="col-md-12">
		<?php if (isset($this->title)):?>
		<h1><?php echo $this->title; ?></h1>
		<?php else:?>
		<h1>Error</h1>
		<?php endif;?>

		<?php if (isset($this->detail)):?>
		<div><?php echo $this->detail;?></div>
		<br />
		<?php endif;?>
	</div>
</div>
