<?php if(!defined("APP")) die(); // Protect this page ?>
<div class="row">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">Add Category</div>
			<div class="panel-body">
				<form class="form" action="<?php echo Main::ahref("categories/add")?>" method="post">				
					<div class="form-group">
						<label for="title">Name</label>
						<p class="help-block">Don't append the media type after the name as this will be done automatically. For example <strong>Funny videos</strong> is not good.</p>
						<input class="form-control" type="text" name="title" id="title" value="" placeholder="The name of the category."  required>
					</div>
											
					<div class="form-group">
						<label for="slug">Slug (optional)</label>
						<p class="help-block">A slug is a short alias used to identify this category. Excellent for SEO. Leave it empty to generate one using the name.</p>
						<input class="form-control" type="text" name="slug" id="slug" value="" placeholder="The slug of the category. e.g. funny-and-cute">						
					</div>
					
					<div class="form-group">
						<label for="type">Type</label>		
						<p class="help-block">Choose the category type to group this in.</p>	
						<?php echo types(NULL, TRUE) ?>							
					</div>

					<div class="form-group">
						<label for="parent">Parent Category</label>		
						<p class="help-block">You can make this a sub-category by selecting a parent category.</p>	
						<?php $cat = $this->db->get("category", array("parentid" => "0"), array("order" => "type")) ?>
						<select name="parent" id="parent">
							<option value="0">None</option>
							<?php foreach ($cat as $c): ?>
									<option value="<?php echo $c->id ?>"><?php echo ucfirst($c->type) ?> / <?php echo $c->name ?></option>
							<?php endforeach ?>
						</select>
					</div>					

					<div class="form-group">
						<label for="description">Description (optional)</label>
						<textarea name="description" id="description" class="form-control" cols="30" rows="5" placeholder="This will override the auto meta description."></textarea>						
					</div>

					<?php echo Main::csrf_token(TRUE) ?>	
					<input type="submit" class="btn btn-primary" value="Add Category">
				</form>	
			</div>				
		</div>
	</div>
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Type</th>
							<th>Name</th>
							<th>Slug</th>
							<th>Options</th>
						</tr>
					</thead>	
					<tbody>
						<?php foreach ($categories as $category): ?>
							<tr>
								<td><?php echo ucfirst($category->type) ?></td>
								<td><?php echo $category->name ?></td>
								<td><?php echo $category->slug ?></td>
								<td>
									<a href="<?php echo Main::href("channel/{$category->type}/{$category->slug}") ?>" class="btn btn-success btn-xs" target="_blank">View</a>
									<a href="<?php echo Main::ahref("categories/edit/{$category->id}") ?>" class="btn btn-primary btn-xs">Edit</a>
									<?php if($category->id !== "1"): ?>
										<a href="<?php echo Main::ahref("categories/delete/{$category->id}").Main::nonce("delete-category-{$category->id}") ?>" class="btn btn-danger delete btn-xs">Delete</a>									
									<?php endif; ?>
								</td>
							</tr>
							<?php if($cat = $this->db->get("category", array("parentid" => $category->id))): ?>
								<?php foreach ($cat as $c): ?>
									<tr class="alt">
										<td>&nbsp;</td>
										<td><?php echo $c->name ?></td>
										<td><?php echo $c->slug ?></td>
										<td>
											<a href="<?php echo Main::href("channel/{$c->type}/{$c->slug}") ?>" class="btn btn-success btn-xs" target="_blank">View</a>
											<a href="<?php echo Main::ahref("categories/edit/{$c->id}") ?>" class="btn btn-primary btn-xs">Edit</a>
											<?php if($c->id !== "1"): ?>
												<a href="<?php echo Main::ahref("categories/delete/{$c->id}").Main::nonce("delete-category-{$c->id}") ?>" class="btn btn-danger delete btn-xs">Delete</a>									
											<?php endif; ?>
										</td>									
									</tr>									
								<?php endforeach ?>
							<?php endif ?>
						<?php endforeach ?>
					</tbody>			
				</table>
			</div>
			<?php echo $pagination ?>
		</div>
	</div>
</div>