<?php echo Asset::css('pc/album/detail.css'); ?>

<nav class="main_navi">
	<span class="main_navi_title">アーティスト</span>
	<span class="main_navi_ans">[<?php echo Html::anchor("/artist/detail/{$this->artist_id}/", $this->artist_name); ?>]</span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title">アルバム</span>
	<span class="main_navi_ans">[<?php echo $this->album_name; ?>]</span>
</nav>

<div class="main_div">

	<table id="album_detail_table">
		<tr>
			<td id="album_detail_img">
				<span><img src="<?php echo $this->album_image;?>"></span>
			</td>
		</tr>
		<tr>
			<td id="album_detail_name">
				<div class="album_title"><?php echo $this->album_name; ?></div>
				<?php $track_number = 1;?>
				<?php foreach ($this->arr_tracks as $i => $val):?>
					<div class="album_track"><?php echo $track_number;?>.&nbsp;<?php echo preg_replace('/\(.+\)$/i', '', $val->name); ?></div>
					<?php $track_number++; ?>
				<?php endforeach;?>
			</td>
		</tr>
	</table>

	<table id="artist_detail_table">
		<tr>
			<td id="artist_detail_image">
				<img src="<?php echo $this->artist_image;?>">
			</td>
			<td id="artist_detail_name">
				<span class="artist_name"><?php echo html::anchor("/artist/detail/{$this->artist_id}/", $this->artist_name); ?></span>
				<hr />
				<span class="album_name"><?php echo $this->album_name; ?></span>
				<span class="about">album</span>
				<br />
				<span class="go_review">
					<?php echo Html::anchor('/review/music/write/?artist_id='. $this->artist_id , 'レビューをかいてみませんか？'); ?>
				</span>
			</td>
		</tr>
	</table>

	<div id="about_detail_review_div">
		<nav class="review_navi">
			<span class="review_navi_title"><?php echo Html::anchor("/review/music/", "レビュー"); ?></span>
			<span class="review_navi_child">></span>
			<span class="review_navi_ans"><?php echo Html::anchor("/artist/detail/{$this->artist_id}/", mb_strimwidth($this->artist_name, 0, 50), array('title' => $this->artist_name));?></span>
			<span class="review_navi_child">></span>
			<span class="review_navi_ans"><?php echo $this->album_name;?></span>
		</nav>
		<h3 class="review_title">このアルバムの最新レビュー</h3>

		<?php if ($this->pagination->total_pages > 1):?>
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
		<?php endif;?>

		<table class="review_list_table">
		<?php if (empty($this->arr_list)):?>
			<tr class="none_review_tr">
				<td class="none_review">レビューはまだありません。<?php echo Html::anchor("/review/music/write/?artist_id={$this->artist_id}", "レビューをかいてみませんか？"); ?></td>
			</tr>
		<?php else:?>
			<?php foreach ($this->arr_list as $i => $arr_list): ?>
				<?php
					$id          = $arr_list->id;
					$about       = $arr_list->about;
					$artist_id   = isset($arr_list->artist_id)? $arr_list->artist_id: null;
					$artist_name = isset($arr_list->artist_name)? mb_strimwidth($arr_list->artist_name, 0, 25, '...'): null;
					$artist_image= ( ! empty($arr_list->image_extralarge))? '<img src="'. $arr_list->image_extralarge. '">': Asset::img('/profile/user/default/default.jpg');
					$album_id    = isset($arr_list->about_id)? $arr_list->about_id: null;
					$track_id    = isset($arr_list->about_id)? $arr_list->about_id: null;
					$album_name  = isset($arr_list->about_name)? mb_strimwidth($arr_list->about_name, 0, 25, '...'): null;
					$track_name  = isset($arr_list->about_name)? mb_strimwidth($arr_list->about_name, 0, 25, '...'): null;
					$user_id     = $arr_list->user_id;
					$user_name   = $arr_list->user_name;
					$user_image  = $arr_list->user_image_medium;
					$review      = $arr_list->review;
					$star        = $arr_list->star;
					$created_at  = $arr_list->created_at;
					$updated_at  = isset($arr_list->updated_at)? $arr_list->updated_at: null;
				?>
				<tr>
					<td class="image">
						<span><?php echo Html::anchor("/user/you/{$user_id}/", Html::img($user_image, array('alt' => '')));?></span>
					</td>
					<td class="review">
						<span class="star">
							<?php for($i=0; $i<$star; $i++): ?>★<?php endfor;?>
						</span>
						<br />
						<span class="about_review"><?php echo Html::anchor("/review/music/detail/{$about}/{$id}/", $review);?></span>
						<br />
						<span class="at">
							<i><?php echo preg_replace('/:[\d]*$/', '', $updated_at); ?></i>
						</span>
						<span class="user_name"><i>by&nbsp;<?php echo Html::anchor("/user/you/{$user_id}/", mb_strimwidth($user_name, 0, 35, '...'));?></i></span>
					</td>
				</tr>
			<?php endforeach;?>
		<?php endif;?>
		</table>

		<?php if ($this->pagination->total_pages > 1):?>
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
		<?php endif;?>
	</div>

	<br />


	<br />

</div>