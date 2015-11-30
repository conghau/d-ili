<?php if(!defined("APP")) die(); // Protect this page ?>
<div class="panel panel-default">
  <div class="panel-heading">Reports</div>      
  <div class="panel-body">
    <div class="table-responsive">
      <?php if($reports): ?>
        <table class="table table-striped">
          <thead>
            <tr>           
              <th>Report ID</th>              
              <th>Report Type</th>
              <th>Reported ID</th>
              <th>Comment</th>
              <th>Date</th>
              <th>Options</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reports as $report): ?>
              <?php $data = json_decode($report->data) ?>
              <?php if($report->type == "media_report"): ?>
                <tr>
                  <td><?php echo $report->id ?></td>
                  <td>Media Report</td>
                  <td><a href="<?php echo Main::ahref("media/edit/{$data->id}") ?>" target="_blank" class="btn btn-primary btn-xs">Media ID: <?php echo $data->id ?></a></td>
                  <td><?php if(isset($data->comment)) echo $data->comment ?></td>
                  <td><?php echo date("Y-m-d" ,strtotime($report->date)) ?></td>
                  <td>
                    <a href="<?php echo Main::ahref("users/edit/{$data->user}") ?>" class="btn btn-success btn-xs">View Reporter</a>
                    <a href="<?php echo Main::ahref("reports/delete/{$report->id}") ?>" class="btn btn-danger btn-xs delete">Delete Report</a>
                  </td>
                </tr>
              <?php elseif($report->type == "user_report"): ?>           
                <tr>
                  <td><?php echo $report->id ?></td>
                  <td>User Report</td>
                  <td><a href="<?php echo Main::ahref("users/edit/{$data->id}") ?>" target="_blank" class="btn btn-primary btn-xs">User ID: <?php echo $data->id ?></a></td>
                  <td><?php if(isset($data->comment)) echo $data->comment ?></td>
                  <td><?php echo date("Y-m-d" ,strtotime($report->date)) ?></td>
                  <td>
                    <a href="<?php echo Main::ahref("users/edit/{$data->user}") ?>" class="btn btn-success btn-xs">View Reporter</a>
                    <a href="<?php echo Main::ahref("reports/delete/{$report->id}") ?>" class="btn btn-danger btn-xs delete">Delete Report</a>
                  </td>
                </tr>
              <?php elseif($report->type == "comment_report"): ?>           
                <tr>
                  <td><?php echo $report->id ?></td>
                  <td>Comment Report</td>
                  <td><a href="<?php echo Main::ahref("comments/edit/{$data->id}") ?>" target="_blank" class="btn btn-primary btn-xs">Comment ID: <?php echo $data->id ?></a></td>
                  <td><?php if(isset($data->comment)) echo $data->comment ?></td>
                  <td><?php echo date("Y-m-d" ,strtotime($report->date)) ?></td>
                  <td>
                    <a href="<?php echo Main::ahref("users/edit/{$data->user}") ?>" class="btn btn-success btn-xs">View Reporter</a>
                    <a href="<?php echo Main::ahref("reports/delete/{$report->id}") ?>" class="btn btn-danger btn-xs delete">Delete Report</a>
                  </td>
                </tr>                                 
              <?php endif; ?>
            <?php endforeach ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class='text-center'><strong>Nothing to report</strong></p>
      <?php endif ?>
    </div>    
  </div>
</div>