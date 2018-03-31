<script type="text/javascript">
<!--
$(document).bind("pageinit", function(){
  $.mobile.ajaxEnabled = false;
});
//-->
</script>
<!--
// -->

<br />

<div class="main_div">
	<?php echo $this->email;?>にパスワード再設定用のアドレスを送信しました。<br />
	<?php echo $this->passreissue_expired_min;?>分以内に記載のアドレスにアクセスしてパスワードの再登録を行ってください。
</div>