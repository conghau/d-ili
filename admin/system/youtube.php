<?php if(!defined("APP")) die(); // Protect this page ?>

<?php if (!isset($_GET["q"]) || empty($_GET["q"])): ?>
  <div id="showmore" class="hide-callback panel panel-body panel-dark">
    <p>Please note that by using this tool some fields such as title, description and tags will auto-populate itself with their respective youtube data. You might need to edit each one to fix them as youtube data might contain unwanted characters or wordings.</p>
  </div><!--/#showmore-->     
  <div class="panel panel-default">
    <div class="panel-heading">Youtube Mass Importer <a href="" data-target="#showmore" class="toggle btn btn-xs btn-primary pull-right">Help</a></div>     
      <form method="get" action="<?php echo Main::ahref("media/youtube")?>" class="form">
        <div class="panel-body">              
          <?php if(empty($this->config["yt_api"])): ?>
              <p>This script is powered by Youtube's Data API version 3.0. So before installation, you will need to register with Google for an API key.</p>
                <h3>Google API Key</h3>
                <ol>
                  <li>Login with your Google Account at <a href="https://console.developers.google.com">https://console.developers.google.com</a></li>
                  <li>Fill the credetials page</li>
                  <li>Follow images below</li>
                  <li>Then paste your API code in the admin panel > settings > API settings</li>
                </ol>

                <img src="../static/Instructions 1.png" alt="">
                <img src="../static/Instructions 2.png" alt="">
                <img src="../static/Instructions 3.png" alt="">
                <img src="../static/Instructions 4.png" alt="">
 
          <?php else: ?>
          <div class="form-group">
            <label for="q">Search using Keywords or a Profile</label>
            <input type="text" name="q" id="q" class="form-control" title="Type a keyword or a profile name and then select the appropriate option" />           
          </div>      
          <hr>   
          <div class="row">
            <div class="col-md-3">
              <label for="type">Media Type</label>
              <select name="type" id="type">
                <option value="video">Video</option>              
                <option value="music">Music</option>
                <option value="vine">Vine</option>
              </select>
            </div>            
            <div class="col-md-3">
              <label for="h">Search using</label>
              <select name="h" id="h">
                <option value="keyword">Keyword</option>              
                <option value="profile">User Profile</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="i">Show</label>
              <select name="i" id="i">
                <option value="10">10 items</option>              
                <option value="25">25 items</option>              
                <option value="50">50 items</option>
              </select>       
            </div>
            <div class="col-md-3">
              <label for="o">Order by</label>
              <select name="o" id="o">
                <option value="viewCount">Views</option>
                <option value="title">Title</option>
                <option value="rating">Rating</option>
                <option value="date">Date</option>
                <option value="relevance">Relevance</option>
              </select>
            </div>          
          </div>
          <br class="clear">
          <input type="hidden" name="s">
          <button type="submit" class="btn btn-primary">Show me the results</button>   
          <?php endif ?>           
        </div>     
      </form>      
  </div><!--/.box--> 
<?php else: ?>
  <div class="row">
    <div class="col-md-9">
      <div class="panel panel-default">
        <div class="panel-heading">
          Search results for "<?php echo Main::clean($_GET['q'],3,TRUE); ?>" <a href="<?php echo Main::ahref("media/youtube")?>" class="btn btn-xs btn-primary pull-right">Back to Importer</a>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-4"><strong><?php echo $data["pageInfo"]["totalResults"]; ?> Items</strong></div>
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
                  foreach ($data["items"] as $key):
                    $val[] = $key["id"]["videoId"];
                ?>    
                  <tr id='yt-<?php echo $n?>' class='mediainfo'>
                    <td width='12%'>
                      <a href="" class='modal-trigger' title='<?php echo Main::truncate($key["snippet"]["title"],40)?>' data-content="<iframe style='border:0;width: 100%;height:400px;' src='//www.youtube.com/embed/<?php echo $key["id"]["videoId"]?>' frameborder='0' allowfullscreen></iframe>"><img src="<?php echo $key["snippet"]["thumbnails"]["default"]["url"]?>" width='100'/><br /><b><?php echo Main::truncate($key["snippet"]["channelTitle"],12)?></b></a>
                    </td>
                    <td>
                      <input type="checkbox" class="this-import data-delete-check" name="media[]" value="<?php echo $key["id"]["videoId"] ?>">&nbsp;&nbsp;&nbsp;<a href="http://www.youtube.com/watch?v=<?php echo $key["id"]["videoId"]?>"  class="medialink" target="blank" title='<?php echo Main::truncate($key["snippet"]["title"],100)?>'><?php echo Main::truncate($key["snippet"]["title"],60)?> <small class="pull-right"><?php echo Main::timeago($key["snippet"]["publishedAt"])?></small></a>                      
                      <p class="description"><?php echo Main::truncate($key["snippet"]["description"],100)?></p>
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
                            <a href="#import-video" id="button-<?php echo $key["id"]["videoId"]?>" data-id="<?php echo $key["id"]["videoId"]?>" data-type="<?php echo $type ?>" data-n="<?php echo $n ?>" class="btn btn-primary import-this">Import </a>
                            <a href="<?php echo Main::ahref("media/import&url=".urlencode("http://www.youtube.com/watch?v={$key["id"]["videoId"]}"),"media/import?url=".urlencode("http://www.youtube.com/watch?v={$key["id"]["videoId"]}")) ?>" class="btn btn-success">Customize</a>
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
            <?php if(isset($data["prevPageToken"])):?>
              <a href="?<?php echo $action ?>q=<?php echo $q?>&amp;h=<?php echo $h?>&amp;i=<?php echo $i?>&amp;type=<?php echo $type?>&amp;o=<?php echo $o?>&amp;s=<?php echo $_GET['s']-$_GET['i'];?>&page=<?php echo $data["prevPageToken"] ?>" class="btn btn-primary">Previous Page
              </a>
            <?php endif;?>
            <a href="?<?php echo $action ?>q=<?php echo $q?>&amp;type=<?php echo $type?>&amp;i=<?php echo $i?>&amp;h=<?php echo $h?>&amp;o=<?php echo $o?>&amp;s=<?php echo $_GET['s']+$_GET['i'];?>&page=<?php echo $data["nextPageToken"] ?>" class="btn btn-primary">
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
          <form method="get" action="<?php echo Main::ahref("media/youtube") ?>">
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
            <input type="hidden" name="s" />
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