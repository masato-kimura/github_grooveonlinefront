<?php echo Asset::css('pc/login/grooveonlinepassreissureform.css'); ?>

<script type="text/javascript">
<!--
// -->
</script>
<style type="text/css">
<!--
// -->
</style>

<?php echo Form::open('login/grooveonlinepassreissueupdate');?>
<div class="main_div">

  <div class="introduction">グルーヴオンライン・ログインパスワード再発行</div>

  <table class="gol_table">
    <tr>
      <td class="title">メールアドレス</td>
      <td class="ans"><?php echo htmlentities(\Input::get('email'), ENT_QUOTES, mb_internal_encoding());?></td>
    </tr>
    <tr>
      <td class="title">
        <span class="main">パスワード</span>
      </td>
      <td class="ans">
          <?php echo Form::input('password', \Input::post('password'), array('type' => 'password', 'class' => 'password')). PHP_EOL;?>
          <?php echo Form::hidden('email', \Input::get('email'), array('class' => 'email')). PHP_EOL; ?>
          <?php echo Form::hidden('tentative_password', \Input::get('tentative_password'), array('class' => 'tentative_password')). PHP_EOL; ?>
          <?php echo Form::hidden('tentative_id', \Input::get('tentative_id'), array('class' => 'tentative_id')); ?>
          <?php if (isset($this->arr_error['password'])):?>
          <br />
          <span class="error"><?php echo $this->arr_error['password'];?></span>
          <?php endif;?>
      </td>
    </tr>
    <tr>
      <td></td>
      <td class="submit">
        <?php echo Form::submit('submit', '送信', array('class' => 'global_submit_btn'));?>
      </td>
    </tr>
  </table>

  </div>