<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div class="container">
  <?php echo $content_top; ?>
  <h2><?php echo $heading_title; ?></h2>
 
  <?php echo nl2br($notification_message); ?>
  <br /><br />
  <?php 
    if(isset($continue)) {
      echo 'Click <a href="'.$continue.'">here</a> to continue';
    }
  ?>
  <?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>