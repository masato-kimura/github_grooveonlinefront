<?php echo asset::js('pc/index/index.js'); ?>
<?php echo Asset::css('pc/index/index.css');?>

<div class="main_div">
	<section style="padding: 0px; margin: 0px;">
		<div class="main_title">GROOVE-ONLINE</div>
	</section>

	<section class="left_contents">
		<div id="index_artist_search_link_1"><?php echo Html::anchor('artist/search/review/', '大好きなアーティストのレビューをしよう!!');?></div>
		<div><?php echo Html::anchor('artist/search/review/', '気になるあの曲も検索し視聴できる！');?></div>

		<?php foreach($this->top_review_music as $i => $val):?>
			<?php if($i > 0) break;?>
			<h1 id="index_top_review_music_{$i}"><?php echo mb_strimwidth($val->review, 0, 50, ' ...'); ?></h1>
		<?php endforeach;?>
	</section>

	<section class="right_contents">
		<h3 class="sub_title">グルーヴオンライン・ジャパン</h3>
		<table class="date">
		<tr>
			<td>
				<span class="year"><?php echo date('Y'); ?></span>
				<br />
				<span class="month"><?php echo date('F'); ?>&nbsp;<?php echo date('m'); ?>&nbsp;/</span>
			</td>
			<td>
				<span class="day"><?php echo date('d'); ?></span>
			</td>
		</tr>
		</table>

		<?php if (empty($this->user_id)):?>
		<table class="user">
			<tr>
				<td class="welcome">ようこそ、ゲストさん</td>
			</tr>
			<tr>
				<td class="login">ユーザ登録済みの方<br /><?php echo Html::Anchor('login', '[ログイン]', array(), \Config::get('host.https'));?></td>
			</tr>
			<tr>
				<td class="regist">はじめてご利用の方<br /><?php echo Html::Anchor('login/grooveonlineregistindex', '[新規会員登録]', array(), \Config::get('host.https')); ?></td>
			</tr>
		</table>
		<?php else: ?>
		<table class="user">
			<tr>
				<td>こんにちは、<br />
					<?php echo $this->user_name; ?>&nbsp;さん
				</td>
			</tr>
			<tr>
				<td>
					<img src="<?php echo $this->user_image;?>" name="user_image">
				</td>
			</tr>
			<tr>
				<td class="logout">
					<?php echo Html::Anchor('login/editregistindex','[ユーザ情報]', array(), \Config::get('host.https'));?>&nbsp;
					<?php echo Html::Anchor('login/logout', '[ログアウト]', array(), \Config::get('host.https'));?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Html::Anchor('user/my', 'マイページ'); ?>
				</td>
			</tr>
		</table>
		<?php endif;?>

		<br />

		<div class="new_review_section">
			<h3 class="review_title"><?php echo html::anchor('/review/music/', '最新音楽レビュー'); ?></h3>

			<table class="review_list_table">
				<?php foreach ($this->review_music as $i => $val): ?>
					<tbody class="review_list_tbody" id="index_review_list_tbody_<?php echo "{$val->about}_{$val->id}" ?>">
					<tr class="review_list_tr">
						<td class="review_image" rowspan="3"><?php echo Html::img($val->image_small, array('alt' => ''));?></td>
						<td class="review">
							<span class="about"><?php echo $val->about; ?></span>
							<span class="about_name"><?php echo mb_strimwidth($val->about_name, 0, 35, '...');?></span>
							<br />
							<span class="star">
								<?php for($i=0; $i<$val->star; $i++): ?>★<?php endfor;?>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="about_review"><?php echo Html::anchor("/review/music/detail/{$val->about}/{$val->id}/", mb_strimwidth($val->review, 0, 120, '...')); ?></span>
						</td>
					</tr>
					<tr class="review_area_tr">
						<td>
							<span class="at">
								<i>
								<?php echo preg_replace('/:[\d]*$/', '', $val->created_at); ?>
								</i>
							</span>
							<span class="user_name">by&nbsp;<?php echo mb_strimwidth($val->user_name, 0, 30, ' ..');?></span>
						</td>
					</tr>
					</tbody>
				<?php endforeach;?>
			</table>
		</div>


	</section>



	<div class="bottom_div">
	<?php echo \Session::get_flash('error');?>
	</div>
</div>
