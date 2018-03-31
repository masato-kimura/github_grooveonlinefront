<?php echo Asset::css('pc/artist/detail.css'); ?>

<nav class="main_navi">
	<span class="main_navi_title">アーティスト</span>
	<span class="main_navi_ans">[<?php echo $this->artist_name; ?>]</span>
</nav>

<div class="main_div">

	<table id="artist_detail_table">
		<tr>
			<td id="artist_detail_name">
				<span class="artist_name"><?php echo $this->artist_name; ?></span>
				<br />
				<span class="go_review">
					<?php echo Html::anchor('/review/music/write/?artist_id='. $this->artist_id , 'レビューをかいてみませんか？'); ?>
				</span>
			</td>
			<td id="artist_detail_img"><img src="<?php echo $this->artist_image;?>"></td>
		</tr>
	</table>

	<table id="artist_detail_album_table">
		<tr>
			<td id="artist_detail_album_img">
			<?php foreach ($this->arr_album_list as $i => $val): ?>
				<?php if (empty($val['name'])): ?>
				<?php continue; ?>
				<?php endif; ?>
				<img src="<?php echo $val['image_medium']; ?>" title="<?php echo $val['name']; ?>">
				<?php // echo $val['name']; ?>
			<?php endforeach;?>
			</td>
			<td></td>
		</tr>
	</table>

	<div id="about_detail_review_div">
		<nav class="review_navi">
			<span class="review_navi_title"><?php echo Html::anchor("/review/music/", "レビュー"); ?></span>
			<span class="review_navi_child">></span>
			<span class="review_navi_ans"><?php echo $this->artist_name; ?></span>
		</nav>
		<h3 class="review_title">このアーティストの最新レビュー</h3>

		<?php if ($this->pagination->total_pages > 1): ?>
			<div class="pagination_div">
				<span class="pagination_previous">
				<?php if ($this->pagination->calculated_page > 1): ?>
					<?php echo $this->pagination->previous(); ?>
				<?php endif;?>
				</span>
				<span class="pagination_render">
				<?php echo $this->pagination->pages_render(); ?>
				</span>
				<span class="pagination_next">
				<?php if ($this->pagination->calculated_page != $this->pagination->total_pages): ?>
					<?php echo $this->pagination->next(); ?>
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
					$artist_name = isset($arr_list->artist_name)? mb_strimwidth($arr_list->artist_name, 0, 30, '...'): null;
					$artist_image= ( ! empty($arr_list->image_extralarge))? '<img src="'. $arr_list->image_extralarge. '">': Asset::img('/profile/user/default/default.jpg');
					$album_id    = isset($arr_list->about_id)? $arr_list->about_id: null;
					$track_id    = isset($arr_list->about_id)? $arr_list->about_id: null;
					$album_name  = isset($arr_list->about_name)? mb_strimwidth($arr_list->about_name, 0, 32, '...'): null;
					$track_name  = isset($arr_list->about_name)? mb_strimwidth($arr_list->about_name, 0, 32, '...'): null;
					$user_id     = $arr_list->user_id;
					$user_name   = $arr_list->user_name;
					$user_image  = $arr_list->user_image_medium;
					$review      = $arr_list->review;
					$star        = $arr_list->star;
					$created_at  = $arr_list->created_at;
					$updated_at  = isset($arr_list->updated_at)? $arr_list->updated_at: null;
				?>
			<tr class="review_list_tr">
				<td class="review_image" rowspan="3"><?php echo $artist_image;?></td>
				<td class="review">
					<span class="about"><?php echo $about; ?></span>
					<span class="about_name">
					<?php if ($about === 'artist'):?>
						<?php echo Html::anchor("/artist/detail/{$artist_id}", $artist_name);?>
					<?php elseif ($about === 'album'): ?>
						<?php echo Html::anchor("/album/detail/{$album_id}", $album_name); ?>
					<?php elseif ($about === 'track'): ?>
						<?php echo Html::anchor("/track/detail/{$track_id}", $track_name); ?>
					<?php endif;?>
					</span>
					<span class="star">&nbsp;
						<?php for($i=0; $i<$star; $i++): ?>★<?php endfor;?>
					</span>
				</td>
				<td class="user" rowspan="3">
					<span>
						<?php echo Html::anchor('/user/you/'. $user_id. '/', Html::img($user_image, array('alt' => '')));?>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<span class="about_review"><?php echo Html::anchor("/review/music/detail/{$about}/{$id}/", mb_strimwidth($review, 0, 200, '...'));?></span>
				</td>
			</tr>
			<tr class="review_area_tr">
				<td>
					<span class="at">
						<i>
						<?php echo preg_replace('/:[\d]*$/', '', $updated_at); ?>
						</i>
					</span>
					<span class="user_name">by <?php echo Html::anchor('/user/you/'. $user_id. '/', mb_strimwidth($user_name, 0, 35, '...'));?></span>
				</td>
			</tr>
			<?php endforeach;?>
		</table>

		<?php if ($this->pagination->total_pages > 1): ?>
			<div class="pagination_div">
				<span class="pagination_previous">
				<?php if ($this->pagination->calculated_page > 1): ?>
					<?php echo $this->pagination->previous(); ?>
				<?php endif;?>
				</span>
				<span class="pagination_render">
				<?php echo $this->pagination->pages_render(); ?>
				</span>
				<span class="pagination_next">
				<?php if ($this->pagination->calculated_page != $this->pagination->total_pages): ?>
					<?php echo $this->pagination->next(); ?>
				<?php endif;?>
				</span>
			</div>
	<?php endif; ?>
	</div>

	<br />


	<br />

</div>

<?php //var_dump($this->arr_list);?>
