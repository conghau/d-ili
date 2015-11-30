<?php defined("APP") or die() ?>
<section id="upload">
  <div class="container">
    <div class="row">
      <div class="col-md-8 content">
        <div class="panel panel-default">
          <div class="upload-switcher tabs">
            <a href="#submit" class="active <?php echo (!$this->config["upload"] ? "full" : "") ?>">
              <?php echo e("Submit URL") ?>
              <span><?php echo e("A link to the media is all we need") ?></span>
            </a>
            <?php if($this->config["upload"]): ?>
              <a href="#media">
                <?php echo e("Upload Media") ?>
                <span><?php echo e("Upload and host your media on our site") ?></span>
              </a>
            <?php endif; ?>
          </div>
          <div class="panel-body">
            <div id="submit" class="tabbed">
              <form action="<?php echo Main::href("upload/url") ?>" method="post" id="submit-url">
                <p>
                  <?php echo e("We support many major video providers so this means that you can import your favorite videos in one click. We currently support") ?> <strong><?php echo providers() ?></strong>.
                </p>
                <hr>              
                <div class="form-group">
                  <div class="return-data"></div>                  
                  <label for="url" class="control-label"><?php echo e("Link to the media file") ?></label>                  
                  <div class="row">
                    <div class="col-sm-10">
                      <div class="form-group">
                        <input type="text" class="form-control" id="url" name="url" value="<?php echo Main::is_set("url") ?>" placeholder="e.g. http://www.youtube.com/watch?v=VIDEOID">                      
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <a href="#fetch-data" class="btn btn-primary btn-md" id="fetch-media"><?php echo e("Fetch Media") ?></a>
                    </div>
                  </div>
                </div>
                <div class="this-hide">
                  <hr>      
                  <div id="preview"></div>            
                  <div class="form-group">
                    <label for="title" class="control-label"><?php echo e("Title") ?></label>
                    <input type="text" class="form-control" id="title" name="title" value="" placeholder="e.g. The best video ever">
                  </div>
                  <div class="form-group">
                    <label for="description" class="control-label"><?php echo e("Description") ?></label>
                    <textarea class="form-control" id="description" name="description" value="" rows="5"></textarea>
                  </div>
                  <div class="form-group">
                    <label for="tags" class="control-label"><?php echo e("Tags") ?></label>
                    <input type="text" class="form-control tags" id="tags" name="tags" value="" placeholder="e.g. game, call of duty, xbox">
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="type" class="label-block"><?php echo e("Media Type") ?></label>
                        <p class="help-block"><?php echo e("Please choose the appropriate media type.") ?></p>
                        <?php echo types(NULL, TRUE) ?>
                      </div>            
                    </div>
                    <div class="col-md-4">
                      <div class="form-group" id="category-holder">
                        <label for="category" class="label-block"><?php echo e("Category") ?></label>
                        <p class="help-block"><?php echo e("Please choose the most appropriate category.") ?></p>
                        <select name="category" id="category" data-active="video">
                          <?php $categories = $this->db->get("category",array("type"=>"video", "parentid" => "0")) ?>
                          <?php foreach ($categories as $category): ?>                  
                            <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                            <?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
                            <?php foreach ($child as $ch): ?>
                              <option value="<?php echo $ch->id ?>">&nbsp;&nbsp;|_&nbsp;<?php echo $ch->name ?></option>
                            <?php endforeach ?>
                          <?php endforeach ?> 
                        </select>   
                        <?php $lists = types(); ?>              
                        <?php foreach ($lists as $key => $value): ?>
                          <div class="<?php echo $key ?> hide">
                            <?php $categories = $this->db->get("category",array("type"=>$key, "parentid" => "0")) ?>
                            <?php foreach ($categories as $category): ?>
                              <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                              <?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
                              <?php foreach ($child as $ch): ?>
                                <option value="<?php echo $ch->id ?>">&nbsp;&nbsp;&nbsp;|_<?php echo $ch->name ?></option>
                              <?php endforeach ?>
                            <?php endforeach ?>                   
                          </div>
                        <?php endforeach ?>                                            
                      </div>                
                    </div> 
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="nsfw" class="label-block"><?php echo e("Not Safe For Work (NSFW)") ?></label>
                        <p class="help-block"><?php echo e("Not safe for work media will require registered users.") ?></p>
                        <select name="nsfw" id="nsfw">
                          <option value="0"><?php echo e("No") ?></option>
                          <option value="1"><?php echo e("Yes") ?></option>
                        </select>
                      </div>            
                    </div>                         
                  </div>                               
                  <hr>
                  <?php echo Main::csrf_token(TRUE) ?>                                                           
                  <p>
                    <button type="submit" class="btn btn-primary"><?php echo e("Submit") ?></button>
                  </p>
                </div>             
              </form>
            </div>
            <!-- /#submit -->
            <?php if($this->config["upload"]): ?>
              <div id="media" class="tabbed">
                <form action="<?php echo Main::href("upload/media") ?>" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="title" class="control-label"><?php echo e("Title") ?></label>
                    <input type="text" class="form-control" id="title" name="title" value="" placeholder="e.g. The best video ever">
                  </div>
                  <div class="form-group">
                    <label for="description" class="control-label"><?php echo e("Description") ?></label>
                    <textarea class="form-control" id="description" name="description" value="" rows="5"></textarea>
                  </div>
                  <div class="form-group">
                    <label for="tags" class="control-label"><?php echo e("Tags") ?></label>
                    <input type="text" class="form-control tags" id="tags" name="tags" value="" placeholder="e.g. game, call of duty, xbox">
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="type" class="label-block"><?php echo e("Media Type") ?></label>
                        <p class="help-block"><?php echo e("Please choose the appropriate media type.") ?></p>
                        <?php echo types(NULL, TRUE,FALSE, "type-1") ?>
                      </div>            
                    </div>
                    <div class="col-md-4">
                      <div class="form-group" id="category-holder-1">
                        <label for="category-1" class="label-block"><?php echo e("Category") ?></label>
                        <p class="help-block"><?php echo e("Please choose the most appropriate category.") ?></p>
                          <select name="category" id="category-1" data-active="video">
                            <?php $categories = $this->db->get("category",array("type"=>"video", "parentid" => "0")) ?>
                            <?php foreach ($categories as $category): ?>                  
                              <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                              <?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
                              <?php foreach ($child as $ch): ?>
                                <option value="<?php echo $ch->id ?>">&nbsp;&nbsp;|_&nbsp;<?php echo $ch->name ?></option>
                              <?php endforeach ?>
                            <?php endforeach ?> 
                          </select>   
                          <div class="music-1 hide">
                            <?php $categories = $this->db->get("category",array("type"=>"music", "parentid" => "0")) ?>
                            <?php foreach ($categories as $category): ?>
                              <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                              <?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
                              <?php foreach ($child as $ch): ?>
                                <option value="<?php echo $ch->id ?>">&nbsp;&nbsp;|_&nbsp;<?php echo $ch->name ?></option>
                              <?php endforeach ?>                 
                            <?php endforeach ?>                   
                          </div>  
                          <div class="vine-1 hide">
                            <?php $categories = $this->db->get("category",array("type"=>"vine", "parentid" => "0")) ?>
                            <?php foreach ($categories as $category): ?>
                              <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                              <?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
                              <?php foreach ($child as $ch): ?>
                                <option value="<?php echo $ch->id ?>">&nbsp;&nbsp;|_&nbsp;<?php echo $ch->name ?></option>
                              <?php endforeach ?>                 
                            <?php endforeach ?>                   
                          </div>  
                          <div class="picture-1 hide">
                            <?php $categories = $this->db->get("category",array("type"=>"picture", "parentid" => "0")) ?>
                            <?php foreach ($categories as $category): ?>
                              <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
                              <?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
                              <?php foreach ($child as $ch): ?>
                                <option value="<?php echo $ch->id ?>">&nbsp;&nbsp;|_&nbsp;<?php echo $ch->name ?></option>
                              <?php endforeach ?>                 
                            <?php endforeach ?>                   
                          </div>                                    
                      </div>                
                    </div> 
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="nsfw" class="label-block"><?php echo e("Not Safe For Work (NSFW)") ?></label>
                        <p class="help-block"><?php echo e("Not safe for work media will require registered users.") ?></p>
                        <select name="nsfw" id="nsfw">
                          <option value="0"><?php echo e("No") ?></option>
                          <option value="1"><?php echo e("Yes") ?></option>
                        </select>
                      </div>            
                    </div>                         
                  </div>                    
                  <hr>
                  <div class="form-group">
                    <label for="thumb" class="control-label"><?php echo e("Thumbnail") ?></label>
                    <input type="file" class="form-control" id="thumb" name="thumb" value="">
                    <p class="help-block"><?php echo e("Recommended dimensions: ") ?> <strong>320x180px</strong></p>
                  </div>  
                  <div class="form-group">
                    <label for="upload" class="control-label"><?php echo e("Main File") ?></label>
                    <input type="file" class="form-control" id="upload" name="upload" value="">
                    <p class="help-block"><?php echo e("You can upload a media file of the following format:") ?> <?php echo formats(NULL, TRUE) ?> (max <?php echo max_size() ?> MB)</p>
                  </div>  
                  <?php echo Main::csrf_token(TRUE) ?>                                                           
                  <button type="submit" class="btn btn-primary"><?php echo e("Submit") ?></button>                 
                </form>              
              </div>
            <?php endif; ?>
            <!-- /#media -->
          </div>
        </div>
      </div>
      <div class="col-md-4 sidebar">
        <?php $this->sidebar(array("featured" => array("limit" => 5),"topusers" => array("limit" => 10))) ?>
      </div>
    </div>
  </div>
</section>