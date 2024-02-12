<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
$oldCount = null;
if (isset($_GET['fcount'])) {$oldCount = safeGetINT('fcount');} 

$SQL = "select reloadCounter from tblChanReload  where reloadChanID = 1";
$result  = mkyMsqry($SQL);
$tRec    = mkyMsFetch($result);

if ($tRec){
  $reload  = $tRec['reloadCounter'];

  if (!$reload){$reload = 1;}

  if($oldCount){
    if ($oldCount != $reload){
      echo '{"reload":yes","counter":'.$reload.'}';
    }
    else {
      echo '{"reload":"no","counter":0}';
    }
  }
  else {
      echo '{"reload":"not","counter":0}';
  }
}
else {
  echo '{"reload":"nofile","counter":0}';
}

?>
