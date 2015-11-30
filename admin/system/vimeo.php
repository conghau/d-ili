<?php if(!defined("APP")) die(); // Protect this page ?>

<?php if (!isset($_GET["q"]) || empty($_GET["q"])): ?>
  <div id="showmore" class="hide-callback panel panel-body panel-dark">
    <p>Please note that by using this tool some fields such as title, description and tags will auto-populate itself with their respective vimeo data. You might need to edit each one to fix them as youtube data might contain unwanted characters or wordings.</p>
  </div><!--/#showmore-->     
  <div class="panel panel-default">
    <div class="panel-heading">Vimeo Mass Importer <a href="" data-target="#showmore" class="toggle btn btn-xs btn-primary pull-right">Help</a></div>     
      <form method="get" action="<?php echo Main::ahref("media/vimeo")?>" class="form">
        <div class="panel-body">              
          <div class="form-group">
            <label for="q">Search using Keywords</label>
            <input type="text" name="q" id="q" class="form-control" title="Type a keyword or a profile name and then select the appropriate option" />           
          </div>      
          <hr>   
          <div class="row">
            <div class="col-md-4">
              <label for="type">Media Type</label>
              <select name="type" id="type">
                <option value="video">Video</option>              
                <option value="music">Music</option>
                <option value="vine">Vine</option>
              </select>
            </div>            
            <div class="col-md-4">
              <label for="i">Show</label>
              <select name="i" id="i">
                <option value="10">10 items</option>              
                <option value="25">25 items</option>              
                <option value="50">50 items</option>
              </select>       
            </div>
            <div class="col-md-4">
              <label for="o">Order by</label>
              <select name="o" id="o">
                <option value="plays">Views</option>
                <option value="likes">Rating</option>
                <option value="date">Date</option>
                <option value="relevant">Relevance</option>
              </select>
            </div>          
          </div>
          <br class="clear">
          <input type="hidden" name="s">
          <button type="submit" class="btn btn-primary">Show me the results</button>       
        </div>     
      </form>      
  </div><!--/.box--> 
<?php else: ?>
  <div class="row">
    <div class="col-md-9">
      <div class="panel panel-default">
        <div class="panel-heading">
          Search results for "<?php echo Main::clean($_GET['q'],3,TRUE); ?>" <a href="<?php echo Main::ahref("media/vimeo")?>" class="btn btn-xs btn-primary pull-right">Back to Importer</a>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-4"><strong><?php echo $data["total"]; ?> Items.</strong></div>
            <div class="col-md-8">
              <div class="btn-group btn-group-sm pull-right">
                <a href="#toggle" id="check-all-btn" class="btn btn-primary">Toggle Select</a>
                <button class='btn btn-success pull-right' id='import_videos'>Import Selected Videos</button>
              </div>              
            </div>
          </div>         
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th><a href="">Thumbnail</a> </th>
                  <th><a href="">Information</a> </th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $n=1;      
                  $val = array();
                  foreach ($data["data"] as $key):
                    $id = str_replace("/videos/", "" , $key["uri"]);
                    $val[] = $id;
                    if($key["privacy"]["embed"] !== "public") continue;
                ?>    
                  <tr id='yt-<?php echo $n?>' class='mediainfo'>
                    <td width='12%'>
                      <a href="" class='modal-trigger' title='<?php echo Main::truncate($key["name"],40)?>' data-content="<iframe style='border:0;width: 100%;height:400px;' src='//player.vimeo.com/video/<?php echo $id ?>?title=0&byline=0&portrait=0&badge=0&autopause=0&player_id=0' frameborder='0' allowfullscreen></iframe>"><img src="<?php echo $key["pictures"]["sizes"][3]["link"] ?>" width='100'/><br /></a>
                    </td>
                    <td>
                      <input type="checkbox" class="this-import data-delete-check" name="media[]" value="<?php echo $id ?>">&nbsp;&nbsp;&nbsp;<a href="<?php echo "https://vimeo.com/{$id}" ?>"  target="blank" title='<?php echo Main::truncate($key["name"],100)?>' class='medialink'><?php echo Main::truncate($key["name"],60)?> <small class="pull-right"><?php echo Main::timeago($key["created_time"])?></small></a>                      
                      <p class="description"><?php echo Main::truncate($key["description"],100)?></p>
                      <div class="row">
                        <div class="col-md-6">
                          <label>
                            <select name='subcat' class='option'>
                              <?php echo $categories ?>
                            </select>
                          </label>
                          <label>
                            <select name='feature' class='feature'>
                              <option value='1'>Feature</option>
                              <option value='0' selected='selected'>Don't feature</option>
                            </select>                        
                          </label>                                                 
                        </div>
                        <div class="col-md-6" id="import-data">
                          <div class="btn-group btn-group-sm pull-right">
                            <a href="#import-video" id="button-<?php echo $id ?>" data-id="<?php echo $id ?>" data-type="<?php echo $type ?>" data-n="<?php echo $n ?>" class="btn btn-primary import-this">Import </a>
                            <a href="<?php echo Main::ahref("media/import&url=".urlencode("https://vimeo.com/{$id}"),"media/import?url=".urlencode("https://vimeo.com/{$id}")) ?>" class="btn btn-success">Customize</a>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php    
                    $n++;
                 endforeach;
                ?>
              </tbody>
            </table>            
          </div>           
          <hr>
            <?php $action=(!$this->config["mod_rewrite"])?"action=youtube&":"" ?>
            <?php if(isset($_GET["page"])):?>
              <a href="?<?php echo $action ?>q=<?php echo $q?>&amp;h=<?php echo $h?>&amp;i=<?php echo $i?>&amp;type=<?php echo $type?>&amp;o=<?php echo $o?>&amp;<?php echo $pre_p[0] ?>" class="btn btn-primary">Previous Page
              </a>
            <?php endif;?>
            <a href="?<?php echo $action ?>q=<?php echo $q?>&amp;type=<?php echo $type?>&amp;i=<?php echo $i?>&amp;h=<?php echo $h?>&amp;o=<?php echo $o?>&amp;<?php echo $next_p[0] ?>" class="btn btn-primary">
              Next Page
            </a>  
          <hr>                 
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="panel panel-default">
        <div class="panel-heading">Quick Search</div>
        <div class="panel-body">
          <form method="get" action="<?php echo Main::ahref("media/vimeo") ?>">
            <div class="form-group">
              <label>Keyword</label>
              <input type="text" name="q" value="<?php echo $_GET['q']?>" class="form-control" />              
            </div>
            <div class="form-group">
              <label>Type</label>     
              <select name="type" class="selectized" title="Is your video a general video or a music video?">
                <option value="video">Video</option>
                <option value="music">Music</option>
                <option value="vine">Vine</option>
              </select>               
            </div> 
            <input type="hidden" name="h" id="h" value="<?php echo $h ?>" />  
            <input type="hidden" name="o" id="o" value="<?php echo $o ?>" />  
            <input type="hidden" name="i" value="<?php echo $i ?>" />
            <button type="submit" class="btn btn-primary">Search</button>
          </form>          
        </div>
      </div><!--/.panel panel-default-->      
      <div class="panel panel-dark panel-body sticky">
        <strong>Instructions</strong>
        <ul class="cleanlist">
          <li>Click on the <span class="label label-primary">thumbnail</span> to preview the video</li>
          <li>Clicking <span class="label label-primary">import</span> will add the video to your site</li>
          <li>By clicking <span class="label label-success">customize</span> you can edit video information in details</li>
          <li>Green <span class="label label-success">background</span> signifies that the video has been added, while red <span class="label label-danger">background</span> is when the video is already in your database or has the same title/slug.<br /></li>          
        </ul>        
      </div>
    </div>
  </div>
<?php endif; ?>