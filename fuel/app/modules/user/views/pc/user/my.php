<style type="text/css">
<!--
.main_div {
  margin: auto;
  width: 1000px;
}
// -->
</style>


<div class="main_div">
	<br />
<!--
	<?php echo Html::anchor('group/groupcreate/', 'グループをつくろう'); ?>
	<br />
	<br />

	<div>group</div>
	<table>
	<?php foreach ($this->group as $group):?>
	<tr>
		<td><?php echo $group->group_name; ?></td>
	</tr>
	<?php  endforeach; ?>
	</table>
 -->
	<br />
	<?php echo Html::anchor('artist/search/review/', 'レヴューしよう！'); ?>
	<br />
	<br />
</div>
