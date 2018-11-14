<!-- REST Connection Start -->
<?php

  require_once('executeREST.php');

  function getAccessToken()
  {
    $params = array(
         'grant_type' => 'password',
         'scope' => '*',
         'client_id' => 'GMFGBYKHBIWCSQIVVRFEOLVNQCBAKRUQ',
         'client_secret' => '1584600615bbfdaf3f2eca7067866477',
         'username' => 'admin',
         'password' => 'admin'
    );
    $url = 'https://sales-poc.processmaker.net/nasa/oauth2/token';
    $data = executeREST( $url, 'POST', $params );
    return $data['access_token'];
  }

  session_start();

  $_SESSION['access_token'] = getAccessToken();
  $url = 'https://sales-poc.processmaker.net/api/1.0/nasa/cases/unassigned';
//  $url = 'https://sco-2088.processmaker.net/api/1.0/workflow/cases';
  $case_list = executeREST( $url, 'GET', array(), $_SESSION['access_token'] );

?>
<!-- REST Connection End -->
<!-- Page Content Start-->
<?php include_once("header.html"); ?>
        <div>
			 <table class="table table-hover table-bordered">
			   <thead><tr class="bg-primary">
				<th>Request #</th>
				<th>Initiator</th>
				<th>Task</th>
				<th>Due Date</th>
				<th>Status</th>
				<th></th></tr></thead>
				<?php
					$i = 0;
					foreach($case_list as $case){
					$i++;
					echo "<tr>
						<td>".$case['app_number']."</td>
						<td>".$case['app_del_previous_user']."</td>
						<td>".$case['app_tas_title']."</td>
						<td>".$case['del_task_due_date']."</td>
						<td>".$case['app_status']."</td>
						<td><a href='dynaform.php?proj=".$case['pro_uid']."&task=".$case['tas_uid']."&app=".$case['app_uid']."' class='btn btn-default btn-xs'>Open</a></td></tr>";
					}
				?>
			 </table>
		</div>
<!-- Page Content End-->
<?php include_once("footer.html"); ?>
