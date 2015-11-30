<?php defined("APP") or die() ?>
<section id="page">
  <div class="container">
    <div class="row">
      <div class="col-md-8 content">
      	<div class="panel panel-default">
      		<div class="panel-heading"><?php echo $page->name ?></div>
      		<div class="panel-body">
            <?php Main::plug("before_content", array("data" => $page)) ?>
      			<?php echo $page->content ?>
      		</div>
      	</div>
        <?php Main::plug("after_content", array("id" => $page->id,"url" => Main::href("blog/{$page->slug}"))) ?>
      </div>
      <div class="col-md-4 sidebar">
        <?php $this->sidebar(array("featured" => array("limit" => 5),"topusers" => array("limit" => 10))) ?>
      </div>
    </div>
  </div>
</section>