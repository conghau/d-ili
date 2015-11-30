<?php if(!defined("APP")) die(); // Protect this page ?>
<?php if(isset($beforehead)) echo $beforehead ?>
<div class="panel panel-default">
  <div class="panel-heading">
    <?php echo $header ?>
  </div>      
  <div class="panel-body">
  	<?php echo $content ?>
  </div>
</div>