<?php if(!defined("APP")) die(); // Protect this page ?>
<div class="row">
	<div class="col-md-9">
		<div class="panel panel-default">
			<div class="panel-heading">Add Media <a href="<?php echo Main::ahref("media/import") ?>" class="btn btn-xs btn-primary pull-right">Import</a></div>
			<form action="<?php echo Main::ahref("media/add") ?>" method="post" class="panel-body" enctype="multipart/form-data">
				<div class="form-group">
					<label for="title">Title</label>
					<p class="help-block">A good title will attract more people so make it unique.</p>
					<input type="text" class="form-control" name="title" id="title" placeholder="The title of your video. Please be as descriptive as possible.">
				</div>
				<div class="form-group hide-callback">
					<label for="slug" class="label-block">Media Permalink <span class="label label-success pull-right">Max 60 characters</span></label>
					<p class="help-block">The following field is used to link to the video so make it clean for SEO. If you title contains utf-8 chars leave the slug empty.</p>
					<input type="text" class="form-control" name="slug" id="slug" placeholder="<?php echo Main::href("view/") ?>">					
				</div>							
				<div class="form-group">
					<label for="description" class="label-block">Description</label>
					<p class="help-block">You can use the following html markup in the description: &lt;a&gt;&lt;b&gt;&lt;i&gt;&lt;strong&gt;&lt;p&gt; </p>
					<textarea class="form-control" name="description" id="description" placeholder="The description of your video. HTML Markup Allowed: <a><b><i><strong><p>"></textarea>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="duration" class="label-block">Media Duration (in seconds)</label>
							<span class="help-block">Videos only. E.g: 10:35 or 1:25:30</span>
							<input type="text" class="form-control" name="duration" id="duration" value="0">					
						</div>					
					</div>						
					<div class="col-md-3">
						<div class="form-group">
							<label for="type" class="label-block">Media Type</label>
							<p class="help-block">Please choose the right media type.</p>
							<?php echo types(NULL, TRUE) ?>
						</div>						
					</div>
					<div class="col-md-3">
						<div class="form-group" id="category-holder">
							<label for="category" class="label-block">Categories</label>
							<p class="help-block">You can add a category from <a href="<?php echo Main::ahref("categories") ?>" target="_blank">here</a>.</p>
							<select name="category" id="category" data-active="video">
								<?php $categories = $this->db->get("category",array("type"=>"video", "parentid" => "0")) ?>
								<?php foreach ($categories as $category): ?>									
									<option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
									<?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
									<?php foreach ($child as $ch): ?>
										<option value="<?php echo $ch->id ?>">&nbsp;&nbsp;&nbsp;|_<?php echo $ch->name ?></option>
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
					<div class="col-md-3">
						<div class="form-group">
							<label for="featured" class="label-block">Featured</label>
							<p class="help-block">Featured media will always be prioritized.</p>
							<select name="featured" id="featured">
								<option value="0">No</option>
								<option value="1">Yes</option>
							</select>
						</div>						
					</div>					
				</div>
				<div class="form-group">
					<label for="tags" class="label-block">Tags</label>
					<p class="help-block">Separate tags with a comma. Minimum 3 chars per tag. Duplicate not allowed.</p>
					<input type="text" name="tags" class="form-control" id="tags" />							
				</div>								
				<hr>
					<div class="row">
						<div class="col-md-6">
							<label for="nsfw" class="label-block">Not Safe For Work</label>
							<select name="nsfw" id="nsfw">
								<option value="0">Safe For Work</option>								
								<option value="1">Not Safe For Work</option>
							</select>				
						</div>
						<div class="col-md-6">
							<label for="subscribe" class="label-block">Subscribe to Unlock</label>
							<select name="subscribe" id="subscribe">
								<option value="0">No</option>
								<option value="1">Yes</option>
							</select>				
						</div>
					</div>					
				<hr>				
				<ul class="form_opt" data-id="media_switch" data-callback="media_switch" style="margin-bottom:0;">
					<li><a href="" class="last" data-value="2">Link</a></li>
					<li><a href="" data-value="1">Embed</a></li>
					<li><a href="" class="first current" data-value="0">Upload</a></li>					
				</ul>
				<input type="hidden" name="media_switch" id="media_switch" value="0">

			
				<div id="upload-holder" class="form-group">
					<label for="uploader_v" class="label-block">Upload Media</label>
					<p class="help-block">You can manually upload a media file (video or image) of the following format: <?php echo formats(NULL, TRUE) ?>. Please note that videos are not converted but used in the format uploaded and used with a HTML5/Flash player. The upload size limit on your server is currently <strong><?php echo $this->config["max_size"] ?> MB</strong>.</p>
					<input type="file" name="media_file" class="form-control" id="uploader_v" />							
				</div>									
		
				<div id="embed-holder" class="hide-callback form-group">
					<label class="label-block">Embed Code</label>
					<p class="help-block">You can paste your custom embed code below. Make sure to clean the embed code.</p>
					<textarea rows="5" name="media_code" class="form-control"></textarea>							
				</div>				
		
				<div id="link-holder" class="hide-callback form-group">
					<label for="uploader_l" class="label-block">Link to Media File</label>
					<p class="help-block">You can directly link to a media file of the following format: <?php echo formats(NULL, TRUE) ?>. These files can be hosted externally however they need to be publicly accessible (i.e. no key or auth required). Please note that these files are not copied to your server but simply linked to. <strong>If you have enabled "store pictures/thumbnails locally" then when you link to pictures, these will be copied to the server.</strong></p>
					<input type="text" name="media_source" class="form-control" id="uploader_l" />							
				</div>					

				<ul class="form_opt" data-id="thumb_switch" data-callback="thumb_switch" style="margin-bottom:0;">
					<li><a href="" class="last" data-value="1">Link</a></li>
					<li><a href="" class="first current" data-value="0">Upload</a></li>					
				</ul>
				<input type="hidden" name="thumb_switch" id="thumb_switch" value="0">

				<div id="thumb-upload-holder" class="form-group">
					<label for="thumb_u" class="label-block">Upload Thumbnail</label>
					<p class="help-block">You can upload the thumbnail of the following format: .jpg and .png. The minimum thumbnail size must be 100x100 and the maximum is 600x600. <strong>For pictures and posts (image only), leave this empty to auto-generate from the main file.</strong></p>
					<input type="file" name="thumb" class="form-control" id="thumb_u" />					
				</div>	
				<div id="thumb-link-holder" class="form-group hide-callback">
					<label for="thumb_l" class="label-block">Link to Thumbnail</label>
					<p class="help-block">You can link to an external thumbnail by adding the <strong>direct</strong> link below (either a jpg or png). Please note that these files are not copied to your server but simply linked to.</p>
					<input type="text" name="thumb_link" class="form-control" id="thumb_l" />					
				</div>	

				<?php echo Main::csrf_token(TRUE) ?>
				<button type="submit" class="btn btn-primary">Add Media</button>			
			</form>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-dark">
			<div class="panel-heading">Quick Help</div>
			<div class="panel-body">
        <ul class="cleanlist">
					<li>Make sure to optimize the title and the description for SEO.</li>
					<li>Optimize the slug by removing useless character or text.</li>
					<li>Tags are used to display relevant videos.</li>
					<li><span class="label label-success">Featuring a video</span> will give you the ability to style the video page differently using CSS.</li>
					<li>You can upload media in three different ways: Upload a video/picture, link to it or paste the embed code.</li>
					<li>If you are adding a "post" media type then the make sure to upload a featured image via the upload media file.</li>
					<li>If you link to the media or the thumbnail, they <span class="label label-danger">will not be uploaded</span>.</li>
					<li>If you embed a code, make sure to remove any unecessary elements suchs as links.</li>
					<li>If you have enabled the setting <span class="label label-success">Store Thumbnails Locally</span> and you link to an image file, the file will be copied to the server.</li>
					<li>If you are submitting a <strong>picture</strong> (either by uploading or linking), you can leave the thumbnail field empty to auto-generated a thumbnail. Make sure to select picture type.</li>
        </ul> 				
			</div>
		</div>		
	</div>
</div>