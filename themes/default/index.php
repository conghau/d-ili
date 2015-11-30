<?php defined("APP") or die() ?>
<section>
  <div class="container">
    <?php if ($this->logged() && $subscription): ?>
      <?php echo $subscription ?> 
    <?php endif ?>
    <div class="row">
      <div class="col-md-8 content">   
        <?php echo $this->homeMedia() ?>                             
      </div>
      <div class="col-md-4 sidebar">
        <?php $this->sidebar(array("featured" => array("limit" => 5), "blog" => array("limit" => 5), "topusers" => array("limit" => 10))) ?>
      </div>
    </div>
  </div>
</section>