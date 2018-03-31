<br />
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

<br />
<div>レビュー</div>
<?php echo Html::anchor('artist/search/review/', 'レヴューしよう！'); ?>