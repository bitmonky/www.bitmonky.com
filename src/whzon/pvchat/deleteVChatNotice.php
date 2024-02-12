<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
if ($userID != 0){
   $msgID = safeGetINT('msgID');
   $SQL = "Delete from ICDchat.tblMbrChat where msgID = ".$msgID." and (msgMbrID=".$userID." or  msgUserID = ".$userID.")";
   $myresult = mkyMyqry($SQL);
   echo $SQL;
}
?>

