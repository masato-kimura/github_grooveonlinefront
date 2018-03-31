<?php echo Asset::js('smartphone/index/info.js'); ?>
<?php echo Asset::css('smartphone/index/info.css');?>

<div class="main_div">
	<p class="description">information</p>
	<?php foreach ($this->arr_information as $i => $val):?>
		<?php if (trim($val->comment) == '') continue;?>
		<span class="date"><?php echo \Date::forge(strtotime($val->date))->format('%Y-%m-%d');?></span>
		<?php if (strtotime($val->date) >= \Date::forge()->get_timestamp() - 60 * 60 * 24 * 6):?>
			&nbsp;<span class="new">new</span>
		<?php endif;?>
		<br />
		<span class="text"><?php echo preg_replace('/'. PHP_EOL.'/', '<br />', preg_replace('/(http[s]*:\/\/[a-z0-9\/\.\-#_\?\=]+)/i', '<a href="$1">$1</a>', $val->comment));?></span>
		<br />
		<br />
		<br />
	<?php endforeach;?>

	<?php if ($this->pagination->total_pages > 1): ?>
	<div class="pagination_div">
		<span class="pagination_previous">
		<?php if ($this->pagination->calculated_page > 1):?>
			<?php echo $this->pagination->previous();?>
		<?php endif;?>
		</span>
		<span class="pagination_render">
		<?php echo $this->pagination->pages_render(); ?>
		</span>
		<span class="pagination_next">
		<?php if ($this->pagination->calculated_page != $this->pagination->total_pages):?>
			<?php echo $this->pagination->next();?>
		<?php endif;?>
		</span>
	</div>
	<?php endif; ?>

</div>

