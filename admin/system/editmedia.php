<?php if(!defined("APP")) die(); // Protect this page ?>
<div class="row">
	<div class="col-md-4">
		<div class="panel panel-body panel-dark">
			<p class="main-stats"><span><?php echo number_format($media->views) ?> </span> Views</p>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-body panel-red">
			<p class="main-stats"><span><?php echo number_format($media->likes) ?> </span> Likes (<?php echo number_format($media->votes) ?> votes)</p>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-body panel-green">
			<p class="main-stats"><span><?php echo number_format($media->comments) ?> </span> Comments</p>
		</div>
	</div>	
</div>
<form action="<?php echo Main::ahref("media/edit/{$media->id}") ?>" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-heading">Edit Media</div>
				<div class="panel-body">				
					<div class="form-group">
						<label for="title">Title</label>
						<input type="text" class="form-control" name="title" id="title" placeholder="The title of your video. Please be as descriptive as possible." value="<?php echo $media->title ?>">
					</div>
					<div class="form-group">
						<label for="slug" class="label-block">Media Permalink <span class="label label-success pull-right">Max 60 characters</span></label>
						<p class="help-block">The following field is used to link to the video so make it clean for SEO. Don't change this too much!</p>
						<input type="text" class="form-control" name="slug" id="slug" placeholder="<?php echo Main::href("view/") ?>" value="<?php echo $media->url ?>">					
					</div>							
					<div class="form-group">
						<label for="description" class="label-block">Description</label>
						<p class="help-block small">Keep it clean. You can use the following html markup in the description: &lt;a&gt;&lt;b&gt;&lt;i&gt;&lt;strong&gt;&lt;p&gt; </p>
						<textarea class="form-control" name="description" id="description" rows="8" placeholder="The description of your video. HTML Markup Allowed: <a><b><i><strong><p>"><?php echo $media->description ?></textarea>
					</div>		
					<hr>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="duration" class="label-block">Media Duration (in seconds)</label>
								<span class="help-block">Videos only. E.g: 10:35 or 1:25:30</span>
								<input type="text" class="form-control" name="duration" id="duration" value="<?php echo $media->duration ?>">					
							</div>					
						</div>				
						<div class="col-md-3">
							<div class="form-group">
								<label for="type" class="label-block">Media Type</label>
								<p class="help-block">Please choose the right media type.</p>
								<?php echo types($media->type, TRUE) ?>
							</div>						
						</div>
						<div class="col-md-3">
							<div class="form-group" id="category-holder">
								<label for="category" class="label-block">Categories</label>
								<p class="help-block">You can add a category from <a href="<?php echo Main::ahref("categories") ?>">here</a>.</p>
								<select name="category" id="category" data-active = "<?php echo $media->type ?>">
									<?php $categories = $this->db->get("category",array("type"=> $media->type, "parentid" => "0")) ?>
									<?php foreach ($categories as $category): ?>
										<option value="<?php echo $category->id ?>" <?php if($media->catid == $category->id) echo "selected" ?>><?php echo $category->name ?></option>
											<?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
											<?php foreach ($child as $ch): ?>
												<option value="<?php echo $ch->id ?>" <?php if($media->catid == $ch->id) echo "selected" ?>>&nbsp;&nbsp;&nbsp;|_<?php echo $ch->name ?></option>
											<?php endforeach ?>									
									<?php endforeach ?>	
								</select>	
								<?php $lists = types(); unset($lists[$media->type]); ?>							
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
						<div class="col-md-3">
							<div class="form-group">
								<label for="featured" class="label-block">Featured</label>
								<p class="help-block">Featured media will always be prioritized.</p>
								<select name="featured" id="featured">
									<option value="0" <?php if(!$media->featured) echo "selected" ?>>No</option>
									<option value="1" <?php if($media->featured) echo "selected" ?>>Yes</option>
								</select>
							</div>						
						</div>					
					</div>
					<div class="form-group">
						<label for="tags" class="label-block">Tags</label>
						<p class="help-block">Separate tags with comma. Minimum 3 chars per tag. Duplicate not allowed.</p>
						<input type="text" name="tags" class="form-control" id="tags" value="<?php echo $media->tags ?>" />							
					</div>
					<hr>
					<?php if($media->type == "post"): ?>
					<div class="row">
						<div class="col-md-2">
							<?php if (!empty($media->file)): ?>
								<img src="<?php echo $this->config["url"] ?>/content/media/<?php echo $media->file ?>" alt="" width="100%">
							<?php endif ?>
						</div>
						<div class="col-md-10">
							<label for="file" class="label-block">Featured Image</label>
							<p class="help-block">You can upload the featured image of the following format: .jpg and .png. The minimum featured image size must be 500x100.</p>
							<input type="file" name="file" class="form-control" id="file" />					
						</div>
					</div>
					<hr>					
					<?php endif ?>
					<div class="row">
						<div class="col-md-2">
							<img src="<?php echo $media->thumb ?>" alt="" width="100%">
						</div>
						<div class="col-md-10">
							<label for="thumb_u" class="label-block">Thumbnail</label>
							<p class="help-block">You can upload the thumbnail of the following format: .jpg and .png. The minimum thumbnail size must be 100x100 and the maximum is 600x600.</p>
							<input type="file" name="thumb" class="form-control" id="thumb_u" />					
						</div>
					</div>	
					<?php if(!empty($media->embed)): ?>
						<hr>
						<div class="form-group">
							<label for="embed" class="label-block">Embed Code</label>
							<textarea class="form-control" name="embed" id="embed"><?php echo $media->embed ?></textarea>
							<p class="help-block">The media embed code for <strong>imported media</strong>. Please note that if you use VideoJS, changing Youtube's embed code will have no effect.</p>
						</div>				
					<?php endif; ?>			
					<hr>		
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="views" class="label-block">Media Views</label>
								<input type="text" class="form-control" name="views" id="views" value="<?php echo $media->views ?>">					
							</div>					
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="likes" class="label-block">Media Likes</label>
								<input type="text" class="form-control" name="likes" id="likes" value="<?php echo $media->likes ?>">					
							</div>						
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="dislikes" class="label-block">Media Dislikes</label>
								<input type="text" class="form-control" name="dislikes" id="dislikes" value="<?php echo $media->dislikes ?>">					
							</div>						
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="votes" class="label-block">Media Votes (Likes + Dislikes)</label>
								<input type="text" class="form-control" name="votes" id="votes" value="<?php echo $media->votes ?>">					
							</div>						
						</div>
					</div>	
					<hr>
					<div class="form-group">
						<label for="source" class="label-block">Media Source</label>
						<input type="text" class="form-control" name="source" id="source" value="<?php echo $media->source ?>">					
						<p class="help-block">The media source for <strong>imported media</strong> is the link to the <strong>actual media page</strong>. If the media is imported, you should <strong>not</strong> empty this field! For uploaded content, you can use this field to link to the original creator.</p>
					</div>
					<?php echo Main::csrf_token(TRUE) ?>
					<p>&nbsp;</p>
					<button type="submit" class="btn btn-primary">Edit Media</button>			
					<div class="pull-right">
						<a href="<?php echo Main::ahref("media/delete/{$media->id}").Main::nonce("delete_media-{$media->id}") ?>" class="btn btn-danger delete">Delete Media</a>							
						<a href="<?php echo Main::href("view/{$media->url}/{$media->uniqueid}")?>" class="btn btn-success" target="_blank">View Media</a>							
					</div>	
				</div>					
			</div>			
		</div>
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading">Media Options</div>
				<div class="panel-body">					
						<div class="form-holder">
							<label for="approved" class="label-block">Media Status</label>
							<p class="help-block small">You can publish or unpublish media. Unpublishing a media will put it back in the unapproved list.</p>
							<select name="approved" id="approved">
								<option value="1" <?php if($media->approved) echo "selected" ?>>Published</option>
								<option value="0" <?php if(!$media->approved) echo "selected" ?>>Unpublished</option>
							</select>				
						</div>
						<hr>
						<div class="form-holder">
							<label for="nsfw" class="label-block">Not Safe For Work</label>
							<p class="help-block small">NSFW will require users to register and enable NSFW option.</p>
							<select name="nsfw" id="nsfw">
								<option value="1" <?php if($media->nsfw) echo "selected" ?>>Not Safe For Work</option>
								<option value="0" <?php if(!$media->nsfw) echo "selected" ?>>Safe For Work</option>
							</select>				
						</div>
						<hr>
						<div class="form-holder">
							<label for="subscribe" class="label-block">Subscribe to Unlock</label>
							<p class="help-block small">This option will force a user to subscribe before unlocking a media. Enabling this will prevent users from embedding.</p>
							<select name="subscribe" id="subscribe">
								<option value="1" <?php if($media->subscribe) echo "selected" ?>>Yes</option>
								<option value="0" <?php if(!$media->subscribe) echo "selected" ?>>No</option>
							</select>				
						</div>		
						<hr>						
						<div class="form-holder">
							<label for="userid" class="label-block">User</label>
							<p class="help-block small">You can change the owner of this media any time. Don't change this often though.</p>
							<select name='userid' id="userid">
		            <?php $users = $this->db->get("user") ?>
		            <?php foreach ($users as $user): ?>
		              <option value='<?php echo $user->id ?>' <?php if($user->id == $media->userid) echo "selected" ?>><?php echo ucfirst($user->username) ?></option>
		            <?php endforeach ?>
							</select>
						</div>							
				</div>
			</div>
		</div>
	</div>
</form>