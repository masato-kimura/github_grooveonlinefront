<?php echo Asset::css('pc/review/music/detail.css'); ?>
<script type="text/javascript">
<!--
jQuery(function() {
	var CoolClass = {};
	CoolClass.user_me_image = $('#review_music_detail_user_me_image').val();
	CoolClass.sendAjax = function(params) {
		var response = {};
		return $.ajax({
			type: 'post',
			url: $('#review_music_detail_cool_api_url').val(), // /api/review/sendcool.json
			datatype: 'json',
			data: JSON.stringify(params),
			cache: false,
			success: function (res, ans) {
				if (res.success === false) {
					return false;
				}
				return res;
			},
			error: function() {
				alert('network error');
				return false;
			}
		});
	};
	CoolClass.setEventListner = function() {
		$('.cool_btn').on('click', function() {
			$(this).off('click');
			var params = {};
			params.about          = $('#review_music_detail_about').val();
			params.review_id      = $('#review_music_detail_review_id').val();
			params.review_user_id = $('#review_music_detail_review_user_id').val();
			CoolClass.sendAjax(params).done(function(res) {
				if (res.success == false) {
					return false;
				}
				if (res.result.length === 0) {
					return false;
				}
				if (res.result.reflection === true) {
					if (CoolClass.user_me_image.length > 0) {
						var user_me_name = $('#review_music_detail_user_me_name').val();
						var img_src = "<img src='" + CoolClass.user_me_image + "' title='" + user_me_name + "がナイスです'>";
						$('#review_music_detail_send_cool_div').prepend(img_src);
					}
					$('.cool_btn').addClass('cool_btn_disabled');
					$('.cool_btn').removeClass('cool_btn');
					$('.cool_howmany').html(res.result.cool_count);
				} else {
					$(this).on('click');
					alert('申し訳ございません。投稿を処理できませんでした。');
				}
			});
		});
	};

	CoolClass.setEventListner();

});
-->
</script>

<nav class="main_navi">
	<span class="main_navi_title"><?php echo Html::anchor('/review/music/', "レビュー一覧"); ?></span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title">アーティスト</span>
	<span class="main_navi_ans">[<?php echo Html::anchor("/artist/detail/{$this->artist_id}/", $this->artist_name); ?>]</span>
	<span class="main_navi_child">></span>
	<span class="main_navi_title">レビュー</span>
</nav>

<?php
	$id            = current($this->arr_list)->id;
	$about         = current($this->arr_list)->about;
	$artist_id     = isset(current($this->arr_list)->artist_id)? current($this->arr_list)->artist_id: null;
	$artist_name   = isset(current($this->arr_list)->artist_name)? mb_strimwidth(current($this->arr_list)->artist_name, 0, 500, ' ..'): null;
	$artist_image  = ( ! empty(current($this->arr_list)->image_extralarge))? '<img src="'. current($this->arr_list)->image_extralarge. '">': Asset::img('/profile/user/default/default.jpg');
	$album_id      = isset(current($this->arr_list)->about_id)? current($this->arr_list)->about_id: null;
	$track_id      = isset(current($this->arr_list)->about_id)? current($this->arr_list)->about_id: null;
	$album_name    = isset(current($this->arr_list)->about_name)? mb_strimwidth(current($this->arr_list)->about_name, 0, 500, ' ..'): null;
	$track_name    = isset(current($this->arr_list)->about_name)? mb_strimwidth(current($this->arr_list)->about_name, 0, 500, ' ..'): null;
	$user_id       = current($this->arr_list)->user_id;
	$user_name     = current($this->arr_list)->user_name;
	$user_image    = current($this->arr_list)->user_image_medium;
	$review        = current($this->arr_list)->review;
	$review        = preg_replace('/'. PHP_EOL. '/', '<br />', $review);
	$star          = current($this->arr_list)->star;
	$cool_count    = current($this->arr_list)->cool_count;
	if (empty($cool_count))
	{
		$cool_count = null;
	}
	$comment_count = current($this->arr_list)->comment_count;
	$created_at    = current($this->arr_list)->created_at;
	$updated_at    = isset(current($this->arr_list)->updated_at)? current($this->arr_list)->updated_at: null;
	if ($created_at === $updated_at)
	{
		$updated_at = null;
	}
	switch ($about)
	{
		case 'artist':
			$about_id   = $artist_id;
			$about_name = $artist_name;
			$about_j_name = 'アーティスト';
			break;
		case 'album':
			$about_id   = $album_id;
			$about_name = $album_name;
			$about_j_name = 'アルバム';
			break;
		case 'track':
			$about_id   = $track_id;
			$about_name = $track_name;
			$about_j_name = 'トラック';
			if ($this->track_image)
			{
				$artist_image = "<img src='". $this->track_image. "' >";
			}
			break;
		default:
			$about_id   = null;
			$about_name = null;
	}
?>

<div class="main_div">

	<table id="review_music_detail_table">
		<tr>
			<td id="review_music_detail_img"><?php echo $artist_image ;?></td>
		</tr>
		<tr>
			<td class="review_music_detail_name">
				<span>アーティスト</span>
				<br />
				<span class="artist_name"><i><?php echo html::anchor("/artist/detail/{$artist_id}/", $artist_name); ?></i></span>
				<br />
				<span><?php echo $about_j_name?></span>
				<br />
				<span class="artist_name"><i><?php echo $about_name;?></i></span>
				<br />
				<?php if ($this->about === 'album'):?>
					<?php foreach ($this->album as $val):?>
						<span><?php echo $val->name;?></span>
						<?php $preview_itunes = $val->preview_itunes;?>
						<?php if ( ! empty($preview_itunes)):?>
							<span class="preview_button">▶️</span>
							<br />
							<audio class="preview_itunes">
								<source src="<?php echo $preview_itunes;?>">
								<?php echo Html::anchor($preview_itunes, '▶️', array('target' => 'new_win'));?>
							</audio>
						<?php endif;?>
					<?php endforeach;?>
				<?php elseif ($this->about === 'track'): ?>
					<span><?php echo $this->track_name;?></span>
					<?php if ( ! empty($this->track_preview_itunes)):?>
						<span class="preview_button">▶️</span>
						<br />
						<audio class="preview_itunes">
							<source src="<?php echo $this->track_preview_itunes;?>">
							<?php echo Html::anchor($this->track_preview_itunes, '▶️', array('target' => 'new_win'));?>
						</audio>
					<?php endif;?>
				<?php endif;?>
				<script type="text/javascript">
				<!--
				jQuery(function() {
					$('.preview_button').on('click', function() {
						var global_i = $('.preview_button').index(this);
						// 一旦全アルバムトラック再生を停止させる
						$('.preview_itunes').each(function(i, ans) {
							ans.pause();
							$('.preview_button').eq(i).html('▶');
							if (global_i != i) {
								if (ans.currentTime > 0) {
									ans.currentTime = 0;
								}
								$('.preview_button').removeClass('preview_button_pause');
							}
						});

						var a = $('.preview_itunes').eq(global_i)[0];
						if (a.currentTime === 0) {
							a.play();
							$(this).html('■');
							$(this).addClass('preview_button_pause');
						} else {
							a.pause();
							a.currentTime = 0;
							$(this).html('▶');
							$(this).removeClass('preview_button_pause');
						}
					});
				});
				// -->
				</script>

				<?php foreach ($this->track as $val):?>
					<?php echo $val->name;?><br />
					<?php \Log::info($val);?>
				<?php endforeach;?>

				<br />
				<br />
				<span class="go_review">
					<?php echo Html::anchor("/review/music/write/{$this->artist_id}/", 'レビューをかいてみませんか？'); ?>
				</span>

			</td>
		</tr>
	</table>

	<table id="review_music_detail_artist_table">
		<tr>
			<td id="review_music_detail_artist_img">
				<span class="user_image"><?php echo Html::anchor("/user/you/{$user_id}/", Html::img($user_image, array('alt' => '')));?></span>
			</td>
			<td id="review_music_detail_artist_name">
				<span><?php echo $about_j_name; ?>レビュー</span>
				<span class="about"><?php echo $about; ?></span>
				<br />
				<span class="about_review_name"><?php echo $about_name; ?></span>
				<hr />
				<span class="user_name">by <i><?php echo Html::anchor("/user/you/{$user_id}/", $user_name);?></i></span>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="cool_td">
				<span class="cool_btn_span" title="cool!">
				<?php if ($this->user_id === $this->user_me_id or $this->is_cool_done === true):?>
					<a class="cool_btn_disabled">ナイスです(^^)</a>
				<?php else:?>
					<a class="cool_btn">ナイスです(^^)</a>
				<?php endif;?>
				</span>
				<span class="cool_howmany"><?php echo $cool_count;?></span>
<!--
				<span class="comment_btn_span"><a class="comment_btn" title="comment">コメント</a></span>
				<span class="comment_howmany"><?php echo $comment_count;?></span>
 -->
				<div id="review_music_detail_send_cool_div">
				<?php foreach ($this->arr_cool_users as $i => $val):?>
					<span><img src="<?php echo $val['user_image']; ?>" title="<?php echo $val['user_name'];?>さんがナイスです(^^)"></span>
				<?php endforeach;?>
				</div>
			</td>
		</tr>
	</table>

	<div id="about_detail_review_div">
		<table class="review_list_table">
			<tr>
				<td class="review">
					<span class="star">
						<?php for($i=0; $i<$star; $i++): ?>★<?php endfor;?>
					</span>
					<br />
					<span class="about_review"><?php echo $review;?></span>
					<span class="at">
						<i><?php echo preg_replace('/:[\d]*$/', '', $created_at);?></i>&nbsp;
						<?php if ($updated_at):?>
							<span>[create]</span>&nbsp;&nbsp;&nbsp;
							<i><?php echo preg_replace('/:[\d]*$/', '', $updated_at); ?></i>&nbsp;<span>[modified]</span>
						<?php endif;?>
					</span>
				</td>
			</tr>
		</table>
	</div>

	<br />

</div>

<?php echo Form::hidden('review_id', $id, array('id' => 'review_music_detail_review_id'));?><br />
<?php echo Form::hidden('review_user_id', $this->user_id, array('id' => 'review_music_detail_review_user_id'));?><br />
<?php echo Form::hidden('user_me_id', $this->user_me_id, array('id' => 'review_music_detail_user_me_id'));?><br />
<?php echo Form::hidden('user_me_name', htmlentities($this->user_me_name, ENT_QUOTES, mb_internal_encoding()), array('id' => 'review_music_detail_user_me_name'));?><br />
<?php echo Form::hidden('user_me_image', $this->user_me_image, array('id' => 'review_music_detail_user_me_image')); ?><br />
<?php echo Form::hidden('cool_api_url', \Config::get('host.base_url_http'). '/api/review/sendcool.json', array('id' => 'review_music_detail_cool_api_url'));?><br />
<?php echo Form::hidden('about', $this->about, array('id' => 'review_music_detail_about')); ?><br />