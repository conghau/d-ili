<?php defined("APP") or die() ?>
<section id="media-<?php echo $media->id ?>" class="media-<?php echo $media->type ?>">
  <div class="container" id="main-container">
    <div class="row">
      <div class="col-md-8 content video">      
        <div class="video-player">          
          <?php echo $media->player ?>
        </div><!--/#video-player -->   
        <div class="panel panel-default">
          <div class="video-info panel-body" itemprop="video" itemscope itemtype="http://schema.org/VideoObject">
            <h2 itemprop="name">
              <?php echo $media->title ?> 
              <?php if ($media->featured): ?>
                <span class="featured"><?php echo e("Featured") ?></span>
              <?php endif ?>              
              <?php if ($media->nsfw): ?>
                <span class="nsfw"><?php echo e("NSFW") ?></span>
              <?php endif ?>              
            </h2>
            <meta itemprop="description" content="<?php echo Main::at(Main::hash($media->description, Main::href("search/")), Main::href("user/")) ?>" />
            <meta itemprop="thumbnailUrl" content="<?php echo $media->thumb ?>" />  
            <meta itemprop="embedURL" content="<?php echo Main::href("embed/{$media->uniqueid}") ?>" />        
            <div class="row">
              <div class="col-xs-8 col-md-9">
                <div class="video-author" data-id="<?php echo $media->userid ?>">
                  <!-- Author Scheme -->
                  <span itemprop="author" itemscope itemtype="http://schema.org/Person">
                    <meta itemprop="url" content="<?php echo $media->profile ?>" />
                    <meta itemprop="image" content="<?php echo $author->avatar ?>" />        
                    <meta itemprop="name" content="<?php echo $author->profile->name ?>" />
                  </span>   
                  <!-- Author Scheme -->            
                  <a href="<?php echo $media->profile ?>" title="<?php echo $media->author ?>"><img src="<?php echo $author->avatar ?>" alt="<?php echo $media->author ?>" class="video-author-avatar"></a>
                </div>
                <strong class="video-author-name"><a href="<?php echo $media->profile ?>"><?php echo $author->profile->name ?></a></strong>
                <a href="#subscribe" id="this-subscribe" data-action="subscribe" data-data='["id":<?php echo $media->userid ?>]' class="btn btn-danger btn-xs this-action subscribe this-tooltip" data-content="<?php echo e("Subscribe") ?>">
                  <?php echo e("Subscribe") ?>
                </a>
                <span class="bubble bubble-left"><?php echo Main::formatnumber($author->subscribers, 2) ?></span>
              </div>

              <div class="col-xs-4 col-md-3 stats" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                <meta itemprop="ratingValue" content="<?php echo $media->rating ?>" />
                <meta itemprop="ratingCount" content="<?php echo $media->votes ?>" />
                <?php echo $media->views ?> <small><?php echo e("views") ?></small> 
                <p>
                  <span class="fa fa-thumbs-up"></span> <?php echo $media->likes ?>&nbsp;&nbsp;
                  <span class="fa fa-thumbs-down"></span> <?php echo $media->dislikes ?>
              </div><!--/.stats -->                
            </div>
          </div><!--/.video-info -->
          <div class="video-option">
            <ul class="share panel-body">
              <li><a href="#like" id="this-like-<?php echo $media->id ?>" class="this-action this-tooltip" data-content="<?php echo e("Like") ?>" data-action="like" data-data='["id":<?php echo $media->id ?>, "check":<?php echo $media->userid ?>]'><span class="fa fa-thumbs-up"></span> <?php echo e("Like") ?></a></li>

              <li><a href="#dislike" id="this-dislike-<?php echo $media->id ?>" class="this-action this-tooltip" data-content="<?php echo e("Dislike") ?>" data-action="dislike" data-data='["id":<?php echo $media->id ?>, "check":<?php echo $media->userid ?>]'><span class="fa fa-thumbs-down"></span></a></li>

              <li class="right"><a href="" id="this-report" class="this-action this-tooltip" data-content="<?php echo e("Report this page") ?>" title="<?php echo e("Report this page") ?>" data-action="report" data-data='["id":<?php echo $media->id ?>, "check":"media"]'><span class="fa fa-flag"></span></a></li>   

              <li class="right"><a href="#fav" id="this-addto" class="this-toggle toggle" data-target=".addto"><?php echo e("Add to"); ?></a></li>

              <li class="right"><a href="#share" id="share" class="this-toggle toggle" data-target=".share_content"><?php echo e("Share") ?></a></li>
            </ul><!--/.share-->  
            <hr> 
            <div class="addto this-hide">
              <?php if($this->logged()): ?>
                <div class="row">
                  <div class="col-md-4">
                    <a href="#addto" id="this-addtofav" class="this-action this-tooltip" data-content="<?php echo e("Add to Favorite") ?>"  data-action="addtofav" data-data='["id":<?php echo $media->id ?>, "check":<?php echo $media->userid ?>]'><span class="fa fa-heart"></span> <?php echo e("Add to Favorite") ?></a>
                  </div>
                  <div class="col-md-8">
                    <div class="panel panel-default" id="user-playlist">
                      <div class="panel-heading">
                        <?php echo e("My Playlists") ?>
                      </div>
                      <div class="panel-body">
                        <ul id="playlists-list">
                          <?php echo $this->user->playlists ?>
                        </ul>                      
                      </div>
                    </div>
                  </div>
                </div>
                <hr>
              <?php endif; ?>
            </div>           
            <div class="share_content this-hide">
              <div class="panel-body">
                <?php if($this->config["sharing"]): ?>
                  <div class="social-media">
                    <?php echo Main::share($media->url,urlencode($media->title)) ?>
                  </div>              
                <?php endif ?>
                <div id="shorturl">
                  <div class="row">
                    <div class="col-sm-8">
                      <input type="text" class="form-control this-select" id="shortlink" value="<?php echo $media->url ?>" />
                    </div>     
                    <div class="col-sm-4 text-right">
                      <a href="#short" data-type='<?php echo $this->config["shorturl"] ?>' data-short="<?php echo Main::href("v/{$media->uniqueid}") ?>" class="btn btn-primary btn-sm shorten"><span class="fa fa-link"></span> <?php echo e("Short Link") ?></a> 
                      <a href="#embed" class="this-toggle btn btn-info btn-sm" data-target="#embed"><span class="fa fa-code"></span> <?php echo e("Embed") ?></a>                      
                    </div>              
                  </div>
                </div>  
                <div id="embed" class="this-hide">
                  <textarea class="form-control this-select" id="embed-code" rows="4"><?php echo $media->code ?></textarea>             
                  <select id="predefined-size">
                    <option value="550x350">550x350</option>
                    <option value="650x450">650x450</option>
                    <option value="800x500">800x500</option>
                  </select>                   
                </div>
              </div>
              <hr>
            </div><!-- /.share_conent -->
            <div class="video-description panel-body">
              <div class="row">
                <div class="col-sm-9">
                  <p itemprop="description" class="this-description truncate"><?php echo $this->at(Main::hash($media->description, Main::href("search/")), Main::href("user/")) ?></p>
                </div>
                <div class="col-sm-3">
                  <p>
                    <strong><?php echo e("Published") ?></strong> 
                    <?php echo $media->date ?>
                   </p>                  
                  <p>
                    <strong><?php echo e("Category") ?></strong> 
                    <a href="<?php echo Main::href("{$media->type}") ?>" title="<?php echo types($media->type) ?>"><?php echo types($media->type) ?></a>
                    <?php if (isset($category->name)): ?>
                      &nbsp;/&nbsp;
                      <?php if(isset($parent->name)): ?>
                        <a href="<?php echo Main::href("channel/{$parent->type}/{$parent->slug}") ?>" title="<?php echo $parent->description ?>"><?php echo $parent->name ?></a>
                        &nbsp;/&nbsp;
                      <?php endif; ?>
                      <a href="<?php echo Main::href("channel/{$category->type}/{$category->slug}") ?>" title="<?php echo $category->description ?>"><?php echo $category->name ?></a>
                    <?php endif ?>
                   </p>
                  <?php echo $media->tags_html ?>
                </div>
              </div>              
            </div>  
            <a href="#" class="toggle-description"><?php echo e("View More") ?></a>        
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