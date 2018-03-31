<?php echo Asset::css('pc/login/grooveonlinepassreissurerequest.css'); ?>

<div class="main_div">

  <?php if (isset($error_message)):?>
    <div><?php echo $error_message;?></div>
  <?php endif;?>

  <h3 class="introduction">グルーヴオンライン・ログインパスワード再発行</h3>
  <h5>ご指定のメールアドレスにパスワード再発行方法を記載したメールを送信いたします。</h5>

  <?php echo Form::open('login/grooveonlinepassreissuesendmail');?>

  <table class="gol_table">
    <?php if (empty($this->hide_send_btn)):?>
    <tr>
      <td class="title">
        <span class="main">メールアドレス</span>
      </td>
      <td class="ans">
        <span>
          <?php echo Form::input('email', $email, array('type' => 'email', 'class' => 'email', 'placeholder'=> 'ご登録のメールアドレスを入力してください'));?>
        </span>
      </td>
    </tr>
    <?php endif;?>
    <tr class="ans">
      <td></td>
      <td>
        <?php if ( ! empty($this->arr_error['email'])):?>
          <span class="error_disp"><?php echo ! empty($this->arr_error['email']) ? $this->arr_error['email']: null;?></span>
        <?php endif;?>
      </td>
    </tr>
    <tr class="submit">
      <td></td>
      <td>
        <?php if (empty($this->hide_send_btn)):?>
          <?php echo Form::submit('submit', '送信', array('class' => 'global_submit_btn')); ?>
        <?php endif;?>
      </td>
    </tr>
  </table>

</div>