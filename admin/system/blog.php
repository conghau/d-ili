<?php if(!defined("APP")) die(); // Protect this page ?>
<?php if(!$this->config["type"]["blog"]): ?>
  <div class="panel panel-red panel-body">
    Please note that the blog module is disabled. You can enabled it via settings > application settings.
  </div>
<?php endif ?>
<div class="panel panel-default">
  <div class="panel-heading">
    Blog Posts (<?php echo $count ?>)
    <a href="<?php echo Main::ahref("blog/add") ?>" class="pull-right btn btn-primary btn-xs">Add Post</a>
  </div>
  <div class="panel-body">
    <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Name</th>
              <th>Permalink</th>
              <th>Date</th>
              <th>Published</th>
              <th>Options</th>
            </tr>
          </thead>
          <tbody>          
            <?php foreach ($posts as $post): ?>
              <tr data-id="<?php echo $post->id ?>">
                <td><?php echo Main::truncate($post->name,20) ?></td>
                <td><a href="<?php echo Main::href("blog/{$post->slug}") ?>" class='btn btn-success btn-xs' target='_blank'><?php echo $post->slug ?></a></td>
                <td><?php echo date("d-m-Y", strtotime($post->date)) ?></td>         
                <td><?php echo $post->publish ? "Yes" : "No" ?></td>         
                <td>
                  <a href="<?php echo Main::ahref("blog/edit/{$post->id}") ?>" class="btn btn-primary btn-xs">Edit</a>
                  <a href="<?php echo Main::ahref("blog/delete/{$post->id}").Main::nonce("delete_post-{$post->id}") ?>" class="btn btn-danger btn-xs delete">Delete</a>
                </td>
              </tr>      
            <?php endforeach ?>
          </tbody>
        </table> 
    </div>
  </div>
</div>