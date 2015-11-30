<?php if(!defined("APP")) die(); // Protect this page ?>
<div class="row">
	<div class="col-md-8">
		<?php if (!isset($media) || empty($media)): ?>
			<div class="panel panel-default">
				<div class="panel-heading">Import a Single Video<a href="<?php echo Main::ahref("media/add")?>" class="btn btn-primary btn-xs pull-right">Upload/Embed</a></div>
				<div class="panel-body">
					<form id="form" method="get" action="<?php echo Main::ahref("media/import") ?>" class="form">
						<div class="form-group">				
							<label for="url">URL to the video (including http://)</label>
							<input type="text" class="form-control" id="url" name="url" value="" placeholder="" title="Remember to remove any extra parameters. For Youtube keep only ?v=ID">
						</div><!-- /.inner -->						
						<button type="submit" class="btn btn-primary">Get Video</button>
					</form>
				</div>
			</div>
			<div class="panel panel-dark">
				<div class="panel-heading">List of supported providers</div>
				<div class="panel-body">
					<p>The importer currently supports these providers: <?php echo providers() ?>. Please check out the expected URL format and note that not all videos can be imported.</p>
					<ol class="cleanlist">
						<li>Break.com <span class="listblock">http://www.break.com/video/[VIDEO SLUG]-<strong>[VIDEO ID]</strong></span></li>
						<li>Collegehumor.com <span class="listblock">http://www.collegehumor.com/embed/<strong>[VIDEO ID]</strong>/[VIDEO SLUG]</span></li>
						<li>Dailymotion.com <span class="listblock">http://www.dailymotion.com/video/<strong>[VIDEO ID]</strong>_[VIDEO SLUG]</span></li>
						<li>FunnyorDie.com <span class="listblock">http://www.funnyordie.com/videos/<strong>[VIDEO ID]</strong>/[VIDEO SLUG]</span></li>
						<li>Metacafe.com <span class="listblock">http://www.metacafe.com/watch/<strong>[VIDEO ID]</strong>/[VIDEO SLUG]</span></li>
						<li>SoundCloud.com <span class="listblock">https://soundcloud.com/<strong>[SINGER]</strong>/<strong>[SONG SLUG]</strong></span></li>
						<li>Twitch.com <span class="listblock">https://twitch.com/<strong>[USERNAME]</strong>/video/<strong>[VIDEO ID]</strong></span></li>
						<li>Vine.co <span class="listblock">https://vine.co/v/<strong>[VIDEO ID]</strong></span></li>						
						<li>Vimeo.com <span class="listblock">https://vimeo.com/<strong>[VIDEO ID]</strong></span></li>						
						<li>Youtube.com <span class="listblock">https://www.youtube.com/watch?v=<strong>[VIDEO ID]</strong></span><span class="listblock">http://youtu.be/<strong>[VIDEO ID]</strong></span></li>
					</ol>
				</div>
			</div>
		<?php else: ?>
		<div class="panel panel-default">
			<div class="panel-heading">Import Media <a href="<?php echo Main::ahref("media/import") ?>" class="btn btn-xs btn-primary pull-right">Back</a></div>
			<div class="panel-body">
				<form method="post" action="<?php echo Main::ahref("media/add") ?>" class="form">					
					<div class="form-group">
						<label for="title">Title</label>
						<input type="text" class="form-control" name="title" id="title" value="<?php echo $media->title ?>" placeholder="The title of your video. Please be as descriptive as possible.">
					</div>
					<div class="form-group">
						<label for="slug" class="label-block">Media Permalink <span class="label label-success pull-right">Max 60 characters</span></label>
						<input type="text" class="form-control" name="slug" id="slug" value="<?php echo $media->slug ?>" placeholder="<?php echo Main::href("view/") ?>">
					</div>							
					<div class="form-group">
						<label for="description" class="label-block">Description</label>
						<p class="help-block small">You can use the following html markup in the description: &lt;a&gt;&lt;b&gt;&lt;i&gt;&lt;strong&gt;&lt;p&gt; </p>
						<textarea class="form-control" name="description" id="description" rows="5" placeholder="The description of your video. HTML Markup Allowed: <a><b><i><strong><p>"><?php echo $media->desc ?></textarea>
					</div>				
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="duration" class="label-block">Media Duration (seconds)</label>
								<p class="help-block">E.g. 10:35 = 10x*60+35 = 635s</p>
								<input type="text" class="form-control" id="duration" name="duration" value="<?php echo $media->duration ?>">
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
								<p class="help-block">You can add a category from <a href="<?php echo Main::ahref("categories") ?>">here</a>.</p>
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
								<div class="music hide">
									<?php $categories = $this->db->get("category",array("type"=>"music", "parentid" => "0")) ?>
									<?php foreach ($categories as $category): ?>
										<option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
										<?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
										<?php foreach ($child as $ch): ?>
											<option value="<?php echo $ch->id ?>">&nbsp;&nbsp;&nbsp;|_<?php echo $ch->name ?></option>
										<?php endforeach ?>									
									<?php endforeach ?>										
								</div>	
								<div class="vine hide">
									<?php $categories = $this->db->get("category",array("type"=>"vine", "parentid" => "0")) ?>
									<?php foreach ($categories as $category): ?>
										<option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
										<?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
										<?php foreach ($child as $ch): ?>
											<option value="<?php echo $ch->id ?>">&nbsp;&nbsp;&nbsp;|_<?php echo $ch->name ?></option>
										<?php endforeach ?>									
									<?php endforeach ?>										
								</div>	
								<div class="picture hide">
									<?php $categories = $this->db->get("category",array("type"=>"picture", "parentid" => "0")) ?>
									<?php foreach ($categories as $category): ?>
										<option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
										<?php $child = $this->db->get("category",array("parentid" => $category->id)); ?>
										<?php foreach ($child as $ch): ?>
											<option value="<?php echo $ch->id ?>">&nbsp;&nbsp;&nbsp;|_<?php echo $ch->name ?></option>
										<?php endforeach ?>									
									<?php endforeach ?>										
								</div>																	
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
						<label for="tag" class="label-block">Tags</label>
						<input type="text" name="tags" class="form-control" id="tags" value="<?php echo $media->tag ?>" />							
					</div>								
					<hr>
						<ul class="form_opt" data-id="nsfw">
							<li class="text-label">Not Safe For Work <small>Enable this option will hide contents that are not safe for work (requires 18+).</small></li>
							<li><a href="" data-value="1">NSFW</a></li>
							<li><a href="" class="first current" data-value="0">SFW</a></li>					
						</ul>
						<input type="hidden" name="nsfw" id="nsfw" value="0">	
					<hr>					
					<div id="embed-holder">
						<div class="form-group">
							<label>Video Preview</label>
							<?php echo $media->code ?>					
						</div>					
						<div class="form-group">
							<label class="label-block">Embed Code</label>
							<p class="help-block">You can paste your custom embed code below. Make sure to clean the embed code of any links to the provider's site.</p>
							<textarea  rows="5" name="media_code" class="form-control"><?php echo $media->code ?></textarea>							
						</div>				
					</div>		
					<hr>						
					<div class="form-group">
						<label class="label-block">Video Thumbnail <?php echo $media->size ?></label>
						<img src="<?php echo $media->thumb ?>" width="180" />
						<input type="hidden" name="thumb" value="<?php echo $media->thumb ?>" class="form-control" />			
					</div>					
					<input type="hidden" name="source" value="<?php echo $_GET["url"] ?>">					
					<input type="hidden" name="media_switch" value="1">			
					<input type="hidden" name="thumb_switch" value="3">			
					<?php echo Main::csrf_token(TRUE) ?>
					<button type="submit" class="btn btn-primary">Import Media</button>		
				</form>	
			</div>
		</div>		
		<?php endif ?>
	</div>
	<div class="col-md-4">
		<?php Main::plug("admin_import_sidebar") ?>
		<div class="panel panel-default">
			<div class="panel-heading">Youtube Mass Import</div>
			<div class="panel-body">		
				<p>If you want to import many videos at once from Youtube, you can use the mass video importer tool. It is pretty awesome.</p>
				<p><a href="<?php echo Main::ahref("media/youtube")?>" class="btn btn-success btn-block">Mass Import from Youtube</a></p>			
			</div>
		</div><!--/.box-->
		<div class="panel panel-default">
			<div class="panel-heading">Bookmarklet</div>
			<div class="panel-body">		
				<p>You can easily import videos you are watching using the bookmarklet tool. Simply drag &amp; drop the bookmarklet below to your bookmarks bar or add it manually. Next time you want to import a video you are watching, just click the bookmarklet. Please note that you will need to be logged in as admin for this to work.</p>
				<p><a href="javascript:(function(){ window.location='<?php echo Main::ahref("media/import&url=","media/import?url=") ?>'+encodeURIComponent(document.URL); })();" class="btn btn-primary btn-block">Bookmark this Video</a></p>			
			</div>
		</div><!--/.box-->	
	</div>
</div>
