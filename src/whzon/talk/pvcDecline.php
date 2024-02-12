<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");

if ($userID!=0){
    $chatterID= clean($_GET['fmbrID']);

    $SQL = "delete from ICDchat.tblMbrChat where groupID is null and msgUserID=".$chatterID." and msgMbrID=".$userID;
    $myresult = mkyMyqry($SQL);
}

?>
ok
