<?php if(!defined("APP")) die(); // Protect this page ?>
<div id="media-holder">
	<h4>
		<?php echo $count ?> Media to Moderate
		<a href="<?php echo Main::ahref("media/moderate/delete").Main::nonce("delete_media-all") ?>" class="btn btn-xs btn-danger pull-right delete">Delete all</a>
	</h4>
	<ul class="medialist">		
	  <?php foreach ($videos as $media):?>      
	    <li>
	      <?php if($this->config["local_thumbs"]): ?>
	        <img src="<?php echo $this->config["url"];?>/content/thumbs/<?php echo $media->thumb;?>" />
	      <?php else: ?>
	      	<img src="<?php echo $media->ext_thumb ?>" />
	      <?php endif; ?>
	      <a class="overlay" href="<?php echo Main::href("view/{$media->url}/{$media->uniqueid}") ?>" target="_blank">
	        <span><?php echo Main::truncate($media->title,25)?></span>
	        <center><strong>Click to view this video</strong></center>
	      </a>
				<div class="buttons" style="padding:2px;">
					<a href="<?php echo Main::ahref("media/approve/{$media->id}");?>" title="Approve and Edit" class="edit btn btn-xs btn-primary">Edit</a>
        	<a href="<?php echo Main::ahref("media/delete/{$media->id}").Main::nonce("delete_media-{$media->id}");?>" data-media="delete" title="Delete this video" class="doajax delete btn btn-xs btn-danger">Delete</a>      
				</div>
				<a href="<?php echo Main::ahref("media/approve/{$media->id}");?>" title="Approve and Edit" data-media="approve" class="doajax btn btn-xs btn-success btn-block">Approve</a>
	    </li>
	  <?php endforeach; ?> 
	</ul> 	
	<?php echo $pagination ?>
</div>