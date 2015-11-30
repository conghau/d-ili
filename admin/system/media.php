<?php if(!defined("APP")) die(); // Protect this page ?>
<?php if(isset($videos_moderate) && $videos_moderate): ?>
  <p class="alert alert-info">
    <strong>Notice</strong> You have <strong><?php echo $videos_moderate ?></strong> media waiting to be moderated.
    <a href="<?php echo Main::ahref("media/moderate") ?>" class="btn btn-xs btn-primary pull-right">Moderate</a>
  </p>
<?php endif ?>
<div class="row">
	<div class="col-md-9">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo $type ?>s (<?php echo $count ?>)
				<a href="<?php echo Main::ahref("media/add") ?>" class="btn btn-xs btn-primary pull-right">Add Media</a>
			</div>
			<div class="panel-body">
				<form action="#ajax" class="form" id="ajax_media_search">
					<div class="form-group">
						<label for="q" class="control-label">Live Search</label>
						<input class="form-control" id="q" name="q" placeholder="Enter keyword and press enter to search for media">
					</div>
				</form>
				<div class="media-sort">
					<hr>					
					<form action="<?php echo Main::ahref("media/{$this->do}") ?>" method="get" class="form" data-type="<?php echo $this->do ?>">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="sort">Sort by</label>
									<select name="sort" id="sort">
										<option value="date" <?php if(Main::is_set("sort","date")) echo "selected" ?>>Date</option>
										<option value="views" <?php if(Main::is_set("sort","views")) echo "selected" ?>>Views</option>
										<option value="likes" <?php if(Main::is_set("sort","likes")) echo "selected" ?>>Likes</option>
									</select>
								</div>							
							</div>
							<div class="col-md-6">
								<label for="order">Order by</label>
								<select name="order" id="order">
									<option value="desc" <?php if(Main::is_set("order","desc")) echo "selected" ?>>High to low</option>
									<option value="asc" <?php if(Main::is_set("order","asc")) echo "selected" ?>>Low to high</option>
								</select>
							</div>
						</div>
						<button type="submit" class="btn btn-xs btn-primary">Sort Media</button>
					</form>				
				</div>
			</div>
		</div>		
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">Media Tools</div>
			<div class="panel-body">
				<a class="btn btn-success btn-block" href="<?php echo Main::ahref("media/import") ?>">Import Media</a>
				<a class="btn btn-success btn-block" href="<?php echo Main::ahref("media/youtube") ?>">Mass Import</a>
			</div>
		</div>
		<div class="panel panel-default panel-body">
			<p>Click on the media type (e.g. Video) to select the media and use the bulk feature below.</p>
			<a class="btn btn-xs btn-success" href="#" id="check-all-btn">Select All</a>
			<a class="btn btn-xs btn-danger" href="#" id="delete-all">Delete Selected</a>			
		</div>
	</div>
</div>
<div id="media-holder">
	<form action="<?php echo Main::ahref("media/delete/all") ?>" id="delete-selected-media" method="post">
		<ul class="medialist">
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
					<img src="<?php echo $media->thumb ?>" />
		      <a class="overlay" href="<?php echo Main::href("view/{$media->url}/{$media->uniqueid}") ?>" target="_blank">
		        <span><?php echo Main::truncate($media->title,25)?></span>
		        <span>Views: <?php echo $media->views?></span>
		        <span>Likes / Dislikes: <?php echo $media->likes?> / <?php echo $media->dislikes ?></span>
		        <center><strong>Click to view this video</strong></center>
		      </a>
		      <div class="titles"><input type="checkbox" class="data-delete-check" name="delete_media[]" value="<?php echo $media->id ?>"></div>                       
		      <div class="options">
		        <a href="<?php echo Main::ahref("media/edit/{$media->id}");?>" title="Edit" class="edit btn btn-xs btn-primary">Edit</a>
		        <a href="<?php echo Main::ahref("media/delete/{$media->id}").Main::nonce("delete_media-{$media->id}");?>" title="Delete this video" class="delete btn btn-xs btn-danger">Delete</a>
		      </div>         
		    </li>
		  <?php endforeach; ?> 
		</ul> 	
	</form>
	<?php echo $pagination ?>
</div>