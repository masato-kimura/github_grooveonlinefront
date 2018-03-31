<?php echo Asset::css('pc/review/music/index.css'); ?>
<script type="text/javascript">
<!--
jQuery(function() {
	$('.list_tr').hover(
		function() {
			$(this).css('background', 'rgba(100,100,100,0.2)');
		},
		function() {
			$(this).css('background', 'inherit');
		}
	);
	$('.list_tr').click(function() {
		var match = $(this).attr('id').match(/([^_]+)_([0-9]+)$/);
		var about = match[1];
		var id    = match[2];
		var url   = '/review/music/detail/' + about + '/' + id + '/';
		location.href = url;
	});
});
// -->
</script>

<nav class="main_navi"><span class="main_navi_title">レビュー一覧</span></nav>

<div class="main_div">
	<div class="search_div">
		<?php echo Form::open(array('action' => '/review/music/', 'class' => 'search_form', 'method' => 'post'));?>
			<span class="search_form_title">レビュー検索</span>
			<span><?php echo Form::input('search_word', $this->search_word); ?></span>
			<span><?php echo Form::hidden('about', $this->about);?></span>
			<span><?php echo Form::submit('submit', '検索');?></span>
		<?php echo Form::close(); ?>
	</div>
	<div class="go_review">
		<?php echo Html::anchor('/artist/search/review/?from=artist/search/review/', 'レビューや視聴はこちらから！'); ?>
	</div>
	<nav>
		<ul class="review_nav_ul">
			<?php if ($this->about === null or $this->about === ''):?>
				<li class="this_about" title="ALL REVIEW">すべてのレビュー</li>
			<?php else: ?>
				<li><?php echo Html::anchor('/review/music/', 'すべてのレビュー', array('title' => 'ALL REVIEW')); ?></li>
			<?php endif;?>

			<?php if ($this->about === 'artist'):?>
				<li class="this_about" title="ARTIST REVIEW">アーティストレビュー</li>
			<?php else:?>
				<li><?php echo Html::anchor('/review/music/?about=artist', 'アーティストレビュー', array('title' => 'ARTIST REVIEW')); ?></li>
			<?php endif;?>

			<?php if ($this->about === 'album'):?>
				<li class="this_about" title="ALBUM REVIEW">アルバムレビュー</li>
			<?php else:?>
				<li><?php echo Html::anchor('/review/music/?about=album', 'アルバムレビュー', array('title' => 'ALBUM REVIEW')); ?></li>
			<?php endif;?>

			<?php if ($this->about === 'track'):?>
				<li class="this_about" title="TRACK REVIEW">トラックレビュー</li>
			<?php else:?>
				<li><?php echo Html::anchor('/review/music/?about=track', 'トラックレビュー', array('title' => 'TRACK REVIEW'));?></li>
			<?php endif;?>
		</ul>
	</nav>

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

	<table class="review_list_table">
	<?php foreach ($this->arr_list as $i => $arr_list): ?>
		<?php
			$id          = $arr_list->id;
			$about       = $arr_list->about;
			$artist_id   = isset($arr_list->artist_id)? $arr_list->artist_id: null;
			$artist_name = isset($arr_list->artist_name)? mb_strimwidth($arr_list->artist_name, 0, 25, ' ..'): null;
			$artist_image= ( ! empty($arr_list->image_medium))? '<img src="'. $arr_list->image_medium. '">': Asset::img('/profile/user/default/default.jpg');
			$album_id    = isset($arr_list->about_id)? $arr_list->about_id: null;
			$track_id    = isset($arr_list->about_id)? $arr_list->about_id: null;
			$album_name  = isset($arr_list->about_name)? mb_strimwidth($arr_list->about_name, 0, 35, ' ..'): null;
			$track_name  = isset($arr_list->about_name)? mb_strimwidth($arr_list->about_name, 0, 35, ' ..'): null;
			$user_id     = $arr_list->user_id;
			$user_name   = $arr_list->user_name;
			$user_image  = $arr_list->user_image_medium;
			$review_id   = $arr_list->id;
			$review      = mb_strimwidth($arr_list->review, 0, 60, ' ..');
			$star        = $arr_list->star;
			$created_at  = $arr_list->created_at;
			$updated_at  = isset($arr_list->updated_at)? $arr_list->updated_at: null;
		?>
	<tr id="review_music_index_list_tr_<?php echo $about?>_<?php echo $review_id?>" class="list_tr">
		<td class="image"><?php echo Html::anchor("/review/music/detail/{$about}/{$review_id}/", $artist_image);?></td>
		<td class="name">
			<span class="about"><?php echo $about; ?></span>
			<span class="about_name">
			<?php if ($about === 'artist'):?>
				<span class="artist_name"><?php echo $artist_name;?></span>
			<?php elseif ($about === 'album'): ?>
				<?php echo $album_name; ?>&nbsp;/&nbsp;<span class="artist_name"><?php echo $artist_name;?></span>
			<?php elseif ($about === 'track'): ?>
				<?php echo $track_name; ?>&nbsp;/&nbsp;<span class="artist_name"><?php echo $artist_name;?></span>
			<?php endif;?>
			</span>
			<span class="star">&nbsp;
				<?php for($i=0; $i<$star; $i++): ?>★<?php endfor;?>
			</span>
			<br />
			<span class="about_review"><?php echo Html::anchor("/review/music/detail/{$about}/{$review_id}/", $review);?></span>
			<span class="at">
				<i>
				<?php echo preg_replace('/:[\d]*$/', '', $updated_at); ?>
				</i>
			</span>
		</td>
		<td class="user">
			<span class="user_name"><i><?php echo $user_name;?></i></span>
			<span><?php echo Html::img($user_image, array('alt' => ''));?></span>
		</td>
	</tr>
	<?php endforeach;?>
	</table>

	<br />

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

	<br />

</div>

<?php //var_dump($this->arr_list);?>
