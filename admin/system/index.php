<?php if(!defined("APP")) die(); // Protect this page ?>
<div class="row stats">
  <div class="col-md-6">
    <div class="row">
      <div class="col-md-4 col-sm-6">
        <div class="panel panel-default panel-red">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $this->db->count("user") ?></span> Users</p>
            <p>+ <?php echo $this->db->count("user","date>=curdate()") ?> Today</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 col-sm-6">
        <div class="panel panel-default panel-green">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $this->db->count("comment") ?></span> Comments</p>
            <p>+ <?php echo $this->db->count("comment","date>=curdate()") ?> Today</p>
          </div>
        </div>      
      </div>      
      <div class="col-md-4 col-sm-12">
        <div class="panel panel-default panel-blue">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $this->config["count_media"] ?></span> Media</p>
            <p>+ <?php echo $this->db->count("media","date>=curdate()") ?> Today</p>
          </div>
        </div>      
      </div>      
    </div>
  </div>
  <div class="col-md-6 col-sm-12">
    <div class="row">
      <div class="col-md-3 col-sm-6">
        <div class="panel panel-default panel-dark">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $this->db->count("media","type='video'") ?></span></p>
            <p>Videos</p>
          </div>
        </div>        
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="panel panel-default panel-dark">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $this->db->count("media","type='music'") ?></span></p>
            <p>Music</p>
          </div>
        </div>        
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="panel panel-default panel-dark">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $this->db->count("media","type='vine'") ?></span></p>
            <p>Vines</p>
          </div>
        </div>         
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="panel panel-default panel-dark">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $this->db->count("media","type='picture'") ?></span></p>
            <p>Pictures</p>
          </div>
        </div>         
      </div>
    </div>
  </div>  
</div><!--/.stats-->
<?php if(isset($videos_moderate) && $videos_moderate): ?>
  <p class="alert alert-info">
    <strong>Notice</strong> You have <strong><?php echo $videos_moderate ?></strong> media waiting to be moderated.
    <a href="<?php echo Main::ahref("media/moderate") ?>" class="btn btn-xs btn-primary pull-right">Moderate</a>
  </p>
<?php endif ?>
<div class="panel panel-default panel-dark hidden-xs">
  <div class="panel-heading">
    Summary Chart
    <div class="btn-group btn-group-xs pull-right">
      <a href="?filter=daily" class="btn btn-primary">Daily</a>
      <a href="?filter=monthly" class="btn btn-primary">Monthly</a>
      <a href="?filter=yearly" class="btn btn-primary">Yearly</a>
    </div>
  </div> 
  <div class="panel-body">
    <div id="user-chart" class='chart'></div>  
  </div>
</div>    
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">Top Media</div>      
      <div class="panel-body nopadding">
			  <ul class="medialist static">
			    <?php foreach ($top_videos as $media):?>      
			      <li>
              <?php
                if($this->config["local_thumbs"] || empty($media->ext_thumb)){
                  if(empty($media->thumb)){
                    $media->thumb = $media->ext_thumb;
                  }else{
                    $media->thumb = "{$this->config["url"]}/content/thumbs/{$media->thumb}";
                  }
                }else{
                  $media->thumb = $media->ext_thumb;
                }     
              ?> 
              <img src="<?php echo $media->thumb ?>">
			        <a class="overlay" href="<?php echo Main::href("view/{$media->url}") ?>" target="_blank">
			          <span><?php echo Main::truncate($media->title,25)?></span>
			          <span>Views: <?php echo $media->views?></span>
			          <span>Likes / Dislikes: <?php echo $media->likes?> / <?php echo $media->dislikes ?></span>
			          <center><strong>Click to view this video</strong></center>
			        </a>
			        <div class="titles"><?php echo ucfirst($media->type)?></div>                       
			        <div class="options">
			          <a href="<?php echo Main::ahref("media/edit/{$media->id}");?>" title="Edit" class="edit btn btn-xs btn-primary">Edit</a>
			          <a href="<?php echo Main::ahref("media/delete/{$media->id}").Main::nonce("delete_media-{$media->id}");?>" title="Delete this video" class="delete btn btn-xs btn-danger">Delete</a>
			        </div>         
			      </li>
			    <?php endforeach; ?> 
			  </ul>       
      </div>
    </div>
  </div>  
</div>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">Latest Media
        <a href="<?php echo Main::ahref("media/video") ?>" class="btn btn-primary btn-xs pull-right">View More</a>
      </div>      
      <div class="panel-body nopadding">
			  <ul class="medialist static">
			    <?php foreach ($videos as $media):?>      
			      <li>
			        <?php
                if($this->config["local_thumbs"] || empty($media->ext_thumb)){
                  if(empty($media->thumb)){
                    $media->thumb = $media->ext_thumb;
                  }else{
                    $media->thumb = "{$this->config["url"]}/content/thumbs/{$media->thumb}";
                  }
                }else{
                  $media->thumb = $media->ext_thumb;
                }     
              ?> 
              <img src="<?php echo $media->thumb ?>">
			        <a class="overlay" href="<?php echo Main::href("view/{$media->url}") ?>" target="_blank">
			          <span><?php echo Main::truncate($media->title,25)?></span>
			          <span>Views: <?php echo $media->views?></span>
			          <span>Likes / Dislikes: <?php echo $media->likes?> / <?php echo $media->dislikes ?></span>
			          <center><strong>Click to view this video</strong></center>
			        </a>
			        <div class="titles"><?php echo ucfirst($media->type)?></div>                       
			        <div class="options">
			          <a href="<?php echo Main::ahref("media/edit/{$media->id}");?>" title="Edit" class="edit btn btn-xs btn-primary">Edit</a>
			          <a href="<?php echo Main::ahref("media/delete/{$media->id}").Main::nonce("delete_media-{$media->id}");?>" title="Delete this video" class="delete btn btn-xs btn-danger">Delete</a>
			        </div>         
			      </li>
			    <?php endforeach; ?> 
			  </ul>     
      </div>
    </div>
  </div>      
</div>
<div class="row">
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Latest Users <a href="<?php echo Main::ahref("users") ?>" class="btn btn-primary btn-xs pull-right">View More</a></div>      
      <ul class="media-list media-list-new">
        <?php foreach ($users as $user): ?>
          <li class="media">
            <a class="pull-left" href="<?php echo Main::ahref("users/edit/{$user->id}") ?>">
              <img class="media-object" src="<?php echo $this->avatar($user) ?>" width="48">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><?php echo ucfirst($user->username) ?> <small><?php echo Main::timeago($user->date); ?></small></h4>
              <?php if($user->admin) echo "<span class='label label-primary'>Admin</span>" ?> <?php echo ($user->active?"<span class='label label-success'>Active</span>":"<span class='label label-danger'>Inactive</span>") ?>
              <div class="pull-right">
                <a href="<?php echo Main::ahref("users/edit/{$user->id}") ?>" class="btn btn-primary btn-xs">Edit</a>
                <a href="<?php echo Main::ahref("users/delete/{$user->id}").Main::nonce("delete_user-{$user->id}") ?>" class="btn btn-danger btn-xs delete" title="Delete user only">Delete</a>                  
              </div>
            </div>
          </li>
        <?php endforeach ?>  
      </ul>              
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Latest Comments <a href="<?php echo Main::ahref("comments") ?>" class="btn btn-primary btn-xs pull-right">View More</a></div>      
      <ul class="media-list media-list-new">
        <?php foreach ($comments as $comment): ?>
          <li class="media">
            <a class="pull-left" href="<?php echo Main::ahref("users/edit/{$comment->userid}") ?>">
              <img class="media-object" src="<?php echo $this->avatar($comment) ?>" width="48">
            </a>            
            <div class="media-body">
              <h4 class="media-heading">
                <a href="<?php echo Main::ahref("users/edit/{$comment->userid}") ?>"><?php echo $comment->author ?></a>
                <div class="pull-right">
                  <a href="<?php echo Main::ahref("comments/edit/{$comment->id}") ?>" class="btn btn-primary btn-xs">Edit</a> 
                  <a href="<?php echo Main::ahref("comments/delete/{$comment->id}") ?>" class="btn btn-danger btn-xs">Delete</a>
                </div>
              </h4>
              <?php echo Main::truncate($comment->body,250) ?>
            </div>
          </li>                
        <?php endforeach ?>
      </ul>   
    </div>    
  </div>   
  <div class="col-md-4">
    <div class="panel panel-default panel-dark hidden-xs">
      <div class="panel-heading">Latest News</div>      
      <ul class="media-list media-list-new media-list-dark" id="latestnews">               
      </ul>   
    </div>       
  </div>    
</div>
<div class="row">
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Media Reports</div>      
      <div class="panel-body">
        <div class="table-responsive">
          <?php if($video_reports): ?>
            <table class="table table-striped">
              <thead>
                <tr>           
                  <th>Media ID</th>              
                  <th>Comment</th>
                  <th>Options</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($video_reports as $video_report): ?>
                  <?php $data = json_decode($video_report->data) ?>
                  <tr>
                    <td><a href="<?php echo Main::ahref("media/edit/{$data->id}") ?>" class="btn btn-primary btn-xs"><?php echo $data->id ?></a></td>
                    <td><?php if(isset($data->comment)) echo $data->comment ?></td>
                    <td>
                      <a href="<?php echo Main::ahref("users/edit/{$data->user}") ?>" class="btn btn-success btn-xs">Reporter</a>
                      <a href="<?php echo Main::ahref("reports/delete/{$video_report->id}") ?>" class="btn btn-danger btn-xs delete">Delete</a>
                    </td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class='text-center'><strong>Nothing to report</strong></p>
          <?php endif ?>
        </div>    
      </div>
    </div>          
  </div>   
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Comment Reports</div>      
      <div class="panel-body">
        <div class="table-responsive">
          <?php if($comment_reports): ?>
            <table class="table table-striped">
              <thead>
                <tr>           
                  <th>Comment ID</th>              
                  <th>Comment</th>
                  <th>Options</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($comment_reports as $comment_report): ?>
                  <?php $data = json_decode($comment_report->data) ?>
                  <tr>
                    <td><a href="<?php echo Main::ahref("comments/edit/{$data->id}") ?>" class="btn btn-primary btn-xs"><?php echo $data->id ?></a></td>
                    <td><?php if(isset($data->comment)) echo $data->comment ?></td>
                    <td>
                      <a href="<?php echo Main::ahref("users/edit/{$data->user}") ?>" class="btn btn-success btn-xs">Reporter</a>
                      <a href="<?php echo Main::ahref("reports/delete/{$comment_report->id}") ?>" class="btn btn-danger btn-xs delete">Delete</a>
                    </td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class='text-center'><strong>Nothing to report</strong></p>
          <?php endif ?>
        </div>    
      </div>
    </div>    
  </div>    
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">User Reports</div>      
      <div class="panel-body">
        <div class="table-responsive">
          <?php if ($user_reports): ?>
            <table class="table table-striped">
              <thead>
                <tr>           
                  <th>User ID</th>              
                  <th>Comment</th>
                  <th>Options</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($user_reports as $user_report): ?>
                  <?php $data = json_decode($user_report->data) ?>
                  <tr>
                    <td><a href="<?php echo Main::ahref("users/edit/{$data->id}") ?>" class="btn btn-primary btn-xs"><?php echo $data->id ?></a></td>
                    <td><?php if(isset($data->comment)) echo $data->comment ?></td>
                    <td>
                      <a href="<?php echo Main::ahref("users/edit/{$data->user}") ?>" class="btn btn-success btn-xs">Reporter</a>
                      <a href="<?php echo Main::ahref("reports/delete/{$user_report->id}") ?>" class="btn btn-danger btn-xs delete">Delete</a>
                    </td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>            
          <?php else: ?>
            <p class='text-center'><strong>Nothing to report</strong></p>
          <?php endif ?>
        </div>    
      </div>
    </div>    
  </div>         
</div>
<script>loadnews()</script>