<?php defined("APP") or die() ?>
<section id="channels">
  <div class="container">
    <div class="row">
      <div class="col-md-8 content">
        <?php echo $this->getChannels() ?>                 
      </div>
      <div class="col-md-4 sidebar">
        <?php $this->sidebar(array("featured" => array("limit" => 5),"topusers" => array("limit" => 10))) ?>
      </div>
    </div>
  </div>
</section>