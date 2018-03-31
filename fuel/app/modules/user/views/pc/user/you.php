<?php echo Asset::css('pc/user/you.css'); ?>
<div class="main_div">
	<table id="user_you_userinfo_table">
		<tr>
			<td class="left_td">
				<div id="user_you_main_title" class="main_title">GROOVE<br />ON<br />LINE</div>
				<br />

				<div class="user_name"><?php echo $this->user_name; ?></div>
				<div class="first_last_name"><?php echo $this->last_name;?>&nbsp;<?php echo $this->first_name;?></div>

			</td>
			<td class="right_td">
				<?php echo Html::img($this->user_image, array('alt' => '')); ?>
			</td>
		</tr>
		<tr>
			<td class="profile">
				<?php if ( ! empty($this->gender)): ?>
				<div class="title">GENDER:</div>
				<div class="ans"><?php echo $this->gender; ?></div>
				<?php endif;?>

				<?php if (empty($this->old_secre) && ($this->old)): ?>
				<div class="title">OLD:</div>
				<div class="ans"><?php echo $this->old; ?></div>
				<?php endif;?>

				<?php if (empty($this->birthday_secret) && ($this->birthday_day) && ($this->birthday_month)): ?>
				<div class="title">BIRTHDAY:</div>
				<div class="ans">
					<?php if ( ! empty($this->birthday_year)): ?>
					<?php echo $this->birthday_year; ?>/
					<?php endif;?>
					<?php echo $this->birthday_month; ?>/
					<?php echo $this->birthday_day; ?>
				</div>
				<?php endif;?>

				<?php if ( ! empty($this->locale)): ?>
				<div class="title">LOCALE:</div>
				<div class="ans"><?php echo $this->locale; ?></div>
				<?php endif;?>

				<?php if ( ! empty($this->pref)): ?>
				<div class="title">PREF:</div>
				<div class="ans"><?php echo $this->pref; ?></div>
				<?php endif;?>

				<?php if ( ! empty($this->profile_fields)): ?>
				<div class="title">PROFILE:</div>
				<div class="ans"><?php echo $this->profile_fields; ?></div>
				<?php endif;?>
			</td>
			<td class="written">

			<div id="about_detail_review_div">
				<nav class="review_navi">
					<span class="review_navi_title"><?php echo Html::anchor("/review/music/", "レビュー"); ?></span>
					<span class="review_navi_child">></span>
					<span class="review_navi_ans"><?php echo $this->user_name;?></span>
				</nav>
				<h3 class="review_title"><?php echo $this->user_name;?>さんの最新レビュー</h3>

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
				<?php endif;?>

				<table class="review_list_table">
				<?php if ( empty($this->arr_music_review)): ?>
					<tr class="none_review_tr">
						<td class="none_review">レビューはまだありません。<?php echo Html::anchor("/artist/search/review/?from=artist/search/review/", "レビューをかいてみませんか"); ?></td>
					</tr>
				<?php else:?>
					<?php foreach ($this->arr_music_review as $i => $val): ?>
						<tr class="review_area_tr">
							<td class="review_image"><?php echo Html::anchor("/review/music/detail/{$val->about}/{$val->id}/", Html::img($val->image_medium, array('alt' => ''))); ?></td>
							<td class="review_about">
								<div>
									<span class="about"><?php echo $val->about;?></span>
								<?php
									switch ($val->about)
									{
										case 'artist':
											echo '<span class="about_name">'. Html::anchor("/artist/detail/{$val->artist_id}/", mb_strimwidth($val->about_name, 0, 50, ' ..')). '</span>';
											break;
										case 'album':
											echo '<span class="about_name">'. Html::anchor("/album/detail/{$val->about_id}/", mb_strimwidth($val->about_name, 0, 30, ' ..')). '&nbsp;/&nbsp;' . Html::anchor("/artist/detail/{$val->artist_id}/", mb_strimwidth($val->artist_name, 0, 30, '...')). '</span>';
											break;
										case 'track':
											echo '<span class="about_name">'. Html::anchor("/track/detail/{$val->about_id}/", mb_strimwidth($val->about_name, 0, 30, ' ..')). '&nbsp;/&nbsp;'. Html::anchor("/artist/detail/{$val->artist_id}/", mb_strimwidth($val->artist_name, 0, 30, '...')). '</span>';
											break;
										default :
									}
								?>
								</div>
								<div class="review">
									<?php echo Html::anchor("/review/music/detail/{$val->about}/{$val->id}/", mb_strimwidth($val->review, 0, 100, ' ..'));?>
								</div>
								<div class="star">
									<?php for($i=0; $i<$val->star; $i++): ?>★<?php endfor;?>
								</div>
								<div class="at">
									<i><?php echo preg_replace('/:[\d]*$/', '', $val->updated_at); ?></i>
								</div>
							</td>
						</tr>
					<?php endforeach;?>

				<?php endif;?>
				</table>

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
				<?php endif;?>
			</div>
			</td>
		</tr>
	</table>
</div>