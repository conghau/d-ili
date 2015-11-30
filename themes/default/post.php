<?php defined("APP") or die() ?>
<section id="media-<?php echo $media->id ?>" class="media-<?php echo $media->type ?>">
  <div class="container" id="main-container">
    <div class="row">
      <div class="col-md-8 content video">      
        <div class="panel panel-default">
          <?php if (!empty($media->file)): ?>
            <img src="<?php echo $this->config["url"] ?>/content/media/<?php echo $media->file ?>" alt="<?php echo $media->title ?>" class="post-image">
          <?php endif ?>
          <div class="video-info panel-body" itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
            <div class="row">
              <div class="col-xs-10">
                <h2 itemprop="name">
                  <?php echo $media->title ?> 
                  <?php if ($media->featured): ?>
                    <span class="featured"><?php echo e("Featured") ?></span>
                  <?php endif ?>              
                  <?php if ($media->nsfw): ?>
                    <span class="nsfw"><?php echo e("NSFW") ?></span>
                  <?php endif ?>  
                  <p class='post-info'>
                    <span><i class="fa fa-user"></i>  <a href="<?php echo $media->profile ?>" title="<?php echo $media->author ?>"><?php echo $author->profile->name ?></a></span>
                    <span><i class="fa fa-clock-o"></i> <?php echo $media->date ?></span>
                    <span><i class="fa fa-folder"></i> 
                      <?php if (isset($category->name)): ?>
                        <?php if(isset($parent->name)): ?>
                          <a href="<?php echo Main::href("channel/{$parent->type}/{$parent->slug}") ?>" title="<?php echo $parent->description ?>"><?php echo $parent->name ?></a>
                          &nbsp;/&nbsp;
                        <?php endif; ?>
                        <a href="<?php echo Main::href("channel/{$category->type}/{$category->slug}") ?>" title="<?php echo $category->description ?>"><?php echo $category->name ?></a>
                      <?php endif ?>
                    </span>
                    <span><i class="fa fa-eye"></i> <?php echo $media->views ?></span>
                    <span><i class="fa fa-thumbs-up"></i> <?php echo $media->likes ?></span>
                  </p>            
                </h2>                
              </div>
              <div class="col-xs-2">
               <a href="#like" id="this-like-<?php echo $media->id ?>" class="this-action icon-heart" data-action="like" data-data='["id":<?php echo $media->id ?>, "check":<?php echo $media->userid ?>]'><span class="fa fa-heart pull-right this-tooltip" data-content="<?php echo e("Like") ?>"></span></a>
              </div>
            </div>
            <meta itemprop="description" content="<?php echo Main::truncate(Main::clean($media->description, 3), 250) ?>" />
            <meta itemprop="thumbnailUrl" content="<?php echo $media->thumb ?>" />         
            <div class="row">
              <!-- Author Scheme -->
              <span itemprop="author" itemscope itemtype="http://schema.org/Person">
                <meta itemprop="url" content="<?php echo $media->profile ?>" />
                <meta itemprop="image" content="<?php echo $author->avatar ?>" />        
                <meta itemprop="name" content="<?php echo $author->profile->name ?>" />
              </span>                
              <div class="col-md-12" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                <meta itemprop="ratingValue" content="<?php echo $media->rating ?>" />
                <meta itemprop="ratingCount" content="<?php echo $media->votes ?>" />
                <?php if($this->config["sharing"]): ?>
                  <div class="social-media">
                    <?php echo Main::share($media->url,urlencode($media->title)) ?>
                    <a href="#share" id="share" class="this-toggle toggle" data-target=".share_content"><span class="fa fa-share-alt"></span></a>
                  </div>              
                <?php endif ?>                
              </div><!--/.stats -->                
            </div>
          </div><!--/.video-info -->
          <hr>
          <div class="video-option">      
            <div class="share_content this-hide">
              <div class="panel-body">
                <?php if($this->logged()): ?>
                  <div class="row addto">
                    <div class="col-md-6">
                      <a href="#addto" id="this-addtofav" class="this-action this-tooltip this-fav-block" data-content="<?php echo e("Add to Favorite") ?>"  data-action="addtofav" data-data='["id":<?php echo $media->id ?>, "check":<?php echo $media->userid ?>]'><span class="fa fa-heart"></span> <?php echo e("Add to Favorite") ?></a>
                    </div>
                    <div class="col-md-6">
                      <a href="" id="this-report" class="this-action this-tooltip this-fav-block" data-content="<?php echo e("Report this page") ?>" title="<?php echo e("Report this page") ?>" data-action="report" data-data='["id":<?php echo $media->id ?>, "check":"media"]'><span class="fa fa-flag"></span> <?php echo e("Report this page") ?></a>
                    </div>                                      
                  </div>
                  <hr>
                <?php endif; ?>
                <div id="shorturl">
                  <div class="row">
                    <div class="col-sm-10">
                      <input type="text" class="form-control this-select" id="shortlink" value="<?php echo $media->url ?>" />
                    </div>     
                    <div class="col-sm-2 text-right">
                      <a href="#short" data-type='<?php echo $this->config["shorturl"] ?>' data-short="<?php echo Main::href("v/{$media->uniqueid}") ?>" class="btn btn-primary btn-sm shorten"><span class="fa fa-link"></span> <?php echo e("Short Link") ?></a>                    
                    </div>              
                  </div>
                </div>                  
              </div>
              <hr>
            </div><!-- /.share_conent -->
            <div class="video-description panel-body">   
              <div itemprop="description" class="post-content"><?php echo $this->at(Main::hash($media->description, Main::href("search/")), Main::href("user/")) ?></div> 
              <hr>  
              <?php echo $media->tags_html ?>
            </div>                               
          </div><!-- /.video-option -->  
        </div>
        <?php echo $this->ads(728) ?>
        <?php echo $this->comments($media->id, $media->url, $media->comments) ?>
      </div><!--/.content-->
      <div class="col-md-4 sidebar">        
        <?php echo $this->sidebar(array("related" => $media)) ?>
      </div>
    </div>
  </div>
</section>