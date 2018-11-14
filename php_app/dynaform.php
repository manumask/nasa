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
  $url = 'https://sales-poc.processmaker.net/api/1.0/nasa/project/'.$_GET['proj'].'/activity/'.$_GET['task'].'/steps';
  $steps = executeREST( $url, 'GET', array(), $_SESSION['access_token'] );
  
  $url = 'https://sales-poc.processmaker.net/api/1.0/nasa/project/'.$_GET['proj'].'/dynaform/'.$steps[0]['step_uid_obj'];
  $dynaform = executeREST( $url, 'GET', array(), $_SESSION['access_token'] );

  $url = 'https://sales-poc.processmaker.net/api/1.0/nasa/cases/'.$_GET['app'].'/variables';
  $variables = executeREST( $url, 'GET', array(), $_SESSION['access_token'] );
?>
<!-- REST Connection End -->
<?php include_once("header.html"); ?>
<!-- Page Content Start-->
        <div>
             <h3>Dynaform: <?php echo $dynaform['dyn_title'] ?></h3>
             <form action="route_case.php?app=<?php echo $_GET['app'] ?>" method="post">
             <table align="center" class="table" style="background-color:#F2F2F2; width: 90%;">
             <?php $rows = json_decode($dynaform['dyn_content'], true);
                    foreach($rows['items'][0]['items'] as $field) {
                        echo "<tr>";
                  for($i = 0 ; $i<=count($field)-1 ; $i++){
                      if(isset($field[$i]['mode']))
                          $mode = $field[$i]['mode'] == 'view'? " disabled" : "";
                      if(isset($field[$i]['var_name']))
                          $value = array_key_exists($field[$i]['var_name'], $variables)? $variables[$field[$i]['var_name']] : "";
                      switch($field[$i]['type']){
case "title":
    echo "<th style='background-color:#1192d4; color:#FFF; font-size: 17px;' colspan='".(12 / count($field))."'>".$field[$i]['label']."</th>";
    break;
case "subtitle":
    echo "<th style='background-color:#4EAECE; color:#FFF; font-size: 15px;' colspan='".(12 / count($field))."'>".$field[$i]['label']."</th>";
    break;
case "text":
    echo "<td colspan='".(12 / count($field))."'><label>".$field[$i]['label']."</label>
                <input name='".$field[$i]['var_name']."' class='form-control'".$mode." value='".$value."'>
             </td>";
    break;
case "textarea":
    echo "<td colspan='".(12 / count($field))."'><label>".$field[$i]['label']."</label>
                <textarea name='".$field[$i]['var_name']."'  class='form-control' placeholder='".$field[$i]['placeholder']."' rows='3'".$mode.">".$value."</textarea></td>";
    break;
case "dropdown":
    echo "<td colspan='".(12 / count($field))."'><label>".$field[$i]['label']."</label><select name='".$field[$i]['var_name']."' class='form-control'".$mode.">";
    foreach($field[$i]['options'] as $option){
        $selected = $value == $option['value']? "selected": "";
        echo "<option value='".$option['value']."' ".$selected.">".$option['label']."</option>";
    }
    echo    "</select></td>";
    break;
case "radio":
    echo "<td colspan='".(12 / count($field))."'><label>".$field[$i]['label']."</label>";
    foreach($field[$i]['options'] as $option){
        $selected = $value == $option['value']? " checked": "";
        echo "<div class='radio'><label><input name='".$field[$i]['var_name']."' type='radio' value='".$option['value']."'".$mode.$selected.">".
                $option['label']."</label></div>";
    }
    echo    "</td>";
    break;
/*case "submit":
    echo "<td align='center' colspan='".(12 / count($field))."'><input class='btn btn-info' type='submit' value='".$field[$i]['label']."'></td>";
    break;*/
                            }
                        }
                        echo "</tr>";
                    }
             ?>
             </table>
             </form>
        </div>
<!-- Page Content End-->
<?php include_once("footer.html"); ?>
