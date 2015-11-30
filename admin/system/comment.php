<?php if(!defined("APP")) die(); // Protect this page ?>
<div class="panel panel-default">
  <div class="panel-heading">
    <?php echo $count ?> Comments
  </div>
  <div class="panel-body">
    <ul class="media-list">
        <?php foreach ($comments as $comment): ?>
          <li class="media withborder">
            <a class="pull-left" href="<?php echo Main::ahref("users/edit/{$comment->userid}") ?>">
              <img class="media-object" src="<?php echo $this->avatar($comment) ?>" width="48">
            </a>            
            <div class="media-body">
              <h4 class="media-heading">
                <a href="<?php echo Main::ahref("users/edit/{$comment->userid}") ?>"><?php echo $comment->author ?></a> 
                <small>
                  <?php echo ($comment->parentid == "0") ? "commented" : "replied" ?>
                  on <a href="<?php echo Main::href("view/{$comment->url}") ?>" target="_blank"><?php echo $comment->title ?></a> - <?php echo Main::timeago($comment->date) ?>
                </small>
                <div class="pull-right">
                  <a href="<?php echo Main::ahref("comments/edit/{$comment->id}") ?>" class="btn btn-primary btn-xs">Edit</a> 
                  <a href="<?php echo Main::ahref("comments/delete/{$comment->id}").Main::nonce("delete-comment-{$comment->id}") ?>" class="btn btn-danger btn-xs delete">Delete</a>
                </div>
              </h4>
              <?php echo Main::truncate($comment->body,250) ?>
            </div>
          </li>                
        <?php endforeach ?>
    </ul>   
    <?php echo $pagination ?>   
  </div>
</div>