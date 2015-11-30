<?php defined("APP") or die() ?>
<section id="browse">
  <div class="container">
    <div class="row">
      <div class="col-md-8 content">
        <div class="panel panel-default">
          <div class="panel-heading panel-heading-alt">
            <h1 class="pull-left"><?php echo $title ?></h1>
            <?php if(!isset($nofilter)): ?>
              <div class="pull-right hidden-xs">
                <?php echo filter($this->action) ?>
              </div>    
            <?php else: ?>
              <div class="pull-right">
                <?php echo $nofilter ?>        
              </div>
            <?php endif; ?>
          </div>
          <ul class="media">
            <?php echo $videos ?>
          </ul>            
          <div class="panel-body">
            <?php echo $pagination ?>
          </div>
        </div>
      </div>
      <div class="col-md-4 sidebar">
        <?php $this->sidebar(array("featured" => array("limit" => 5),"topusers" => array("limit" => 10))) ?>
      </div>
    </div>
  </div>
</section>