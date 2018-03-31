<?php echo Asset::css('pc/login/grooveonlinepassreissuresendmail.css'); ?>

<script type="text/javascript">
<!--
// -->
</script>
<style type="text/css">
<!--

// -->
</style>

<div class="introduction">グルーヴオンライン・ログインパスワード再発行</div>

<div class="main_div">
	<?php echo $this->email;?>にパスワード再設定用のアドレスを送信しました。<br />
	<?php echo $this->passreissue_expired_min;?>分以内に記載のアドレスにアクセスしてパスワードの再登録を行ってください。
</div>