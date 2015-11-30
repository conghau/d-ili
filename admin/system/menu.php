<?php if(!defined("APP")) die(); // Protect this page ?>
<div class="row">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">Customize Menu</div>
			<div class="panel-body">
				<form class="form" action="#" method="post" id="add_to_menu">				
					<p class="help-block">
						Please note that by using this feature, the default and dynamic menu will be replaced by a static menu defined by you. This means that the menu will not be affected by things like translation and toggle type settings. You can use this to add to the existing menu or overwrite completely the menu.
					</p>
					<div class="form-group">
						<label for="title">Name</label>
						<input class="form-control" type="text" name="title" id="title" value="" placeholder="e.g. Contact"  required>
					</div>
											
					<div class="form-group">
						<label for="url">Link to the page</label>
						<input class="form-control" type="text" name="url" id="url" value="" placeholder="http://" required>						
					</div>

					<div class="form-group">
						<label for="fa">Font Awesome Class Name (optional)</label>
						<p class="help-block">You can choose to add an icon from the Font Awesome library using this <a target="_blank" href="http://fortawesome.github.io/Font-Awesome/cheatsheet/">cheatsheet</a>. You only need to add the part after fa-. e.g. if the class is fa-home only add <strong>home</strong>.</p>
						<input class="form-control" type="text" name="fa" id="fa" value="" placeholder="home">						
					</div>						

					<?php echo Main::csrf_token(TRUE) ?>	
					<input type="submit" class="btn btn-primary" value="Add to Menu">
				</form>	
			</div>						
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">Link to a Category</div>
			<div class="panel-body">
				<p class="help-block">
					Once you click "Add", the menu will imported to the form above where you will be able to choose the icon.
				</p>
				<form action="#" class="add_custom">
					<select name="cates">
						<?php foreach ($categories as $category): ?>
							<option value='<?php echo $category->name?>||<?php echo Main::href("channel/{$category->type}/{$category->slug}") ?>'><?php echo ucfirst($category->type) ?> / <?php echo $category->name ?></option>
						<?php endforeach ?>							
					</select>				
					<input type="submit" class="btn btn-primary" value="Add">					
				</form>	
			</div>						
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">Link to a Page</div>
			<div class="panel-body">
				<p class="help-block">
					Once you click "Add", the menu will imported to the form above where you will be able to choose the icon.
				</p>
				<form action="#" class="add_custom">
					<select name="cates">
						<?php foreach ($pages as $page): ?>
							<option value='<?php echo $page->name ?>||<?php echo Main::href("page/{$page->slug}") ?>'><?php echo $page->name ?></option>
						<?php endforeach ?>							
					</select>				
					<input type="submit" class="btn btn-primary" value="Add">
				</form>	
			</div>						
		</div>					
	</div>
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">Current Menu 
				<a href="#" class="btn btn-primary btn-xs pull-right" id="save_menu">Save Menu</a>
			</div>
			<form action="<?php echo Main::ahref("server/menu/save")?>" id="current_menu">
				<ul id="sortable">
					<?php $i = 0; ?>
					<?php foreach ($menus as $menu): ?>
	          <li>
	          	<div class="input-group">
			           <span class="input-group-addon"><i class="fa fa-<?php echo $menu["icon"]?>"></i></span>
		            <a href="#<?php echo $menu["href"] ?>"><?php echo $menu["text"] ?>
		            	<span class="menu-delete btn btn-danger btn-xs pull-right">Delete</span>
		            </a>
		            <input type="hidden" name="menu[]" value='{"href":"<?php echo $menu["href"] ?>","text":"<?php echo $menu["text"] ?>","icon":"<?php echo $menu["icon"] ?>"}'>
		          </div>
		        </li>		
	        	<?php if(isset($menu["child"])): ?>
	        		<?php foreach ($menu["child"] as $child): ?>
			          <li class="second-level">
			          	<div class="input-group">
					           <span class="input-group-addon"><i class="fa fa-<?php echo $child["icon"]?>"></i></span>
				            <a href="#<?php echo $child["href"] ?>"><?php echo $child["text"] ?>
				            	<span class="menu-delete btn btn-danger btn-xs pull-right">Delete</span>
				            </a>
				            <input type="hidden" name="menu[]" value='{"href":"<?php echo $child["href"] ?>","text":"<?php echo $child["text"] ?>","icon":"<?php echo $child["icon"] ?>"}'>
				          </div>
				        </li>			        			
	        		<?php endforeach ?>
		        <?php endif; ?>
		        <?php $i++; ?>				
					<?php endforeach ?>
				</ul>
			</form>
		</div>
    <div class="panel panel-dark panel-body sticky">
      <strong>Tips</strong>
      <ul class="cleanlist">
        <li>To reset the menu, delete everything then save an empty menu.</li>
        <li>If you want to move one child from a parent to another parent directly, first make sure to turn that child into a parent then make it a child.</li>
        <li>To edit the menu, delete it then recreate it.</li>
        <li>You might notice some bugs with this feature and it is normal as this is experimental.</li>
      </ul>        
    </div>		
	</div>
</div>
<script>
	  // $("#sortable").sortable({
   //    placeholder: "input_placeholder",
   //    axis: 'y'
   //  });
$("#sortable").sortable({
  connectWith: "#sortable",
  placeholder: "placeholder",
  update: function(event, ui) {
	},
  start: function(event, ui) {
      if(ui.helper.hasClass('second-level')){
          ui.placeholder.removeClass('placeholder');
          ui.placeholder.addClass('placeholder-sub');
      }
      else{ 
          ui.placeholder.removeClass('placeholder-sub');
          ui.placeholder.addClass('placeholder');
      }
  },
	sort: function(event, ui) {
        var pos;
        if(ui.helper.hasClass('second-level')){
            pos = ui.position.left+20; 
            $('#cursor').text(ui.position.left+20);
        }
        else{
            pos = ui.position.left; 
            $('#cursor').text(ui.position.left);    
        }
        if(pos >= 32 && !ui.helper.hasClass('second-level')){
            ui.placeholder.removeClass('placeholder');
            ui.placeholder.addClass('placeholder-sub');
            ui.helper.addClass('second-level');
            var i = ui.item.prevAll("li:not(.second-level)").index();
            ui.helper.find("input").attr("name","menu[child-"+i+"][]");
        }
        else if(pos < 25 && ui.helper.hasClass('second-level')){
            ui.placeholder.removeClass('placeholder-sub');
            ui.placeholder.addClass('placeholder');
            ui.helper.removeClass('second-level');
            ui.helper.find("input").attr("name","menu[]");
        }
  }
});
$("#sortable li.second-level").each(function(){
    var i = $(this).prevAll("li:not(.second-level)").index();
		$(this).find("input").attr("name","menu[child-"+i+"][]");
})
</script>