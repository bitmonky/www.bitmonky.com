<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");

if (isset($_GET['fgroupID'])){ $groupID = safeGET('fgroupID');} else {$groupID = null;}



if (!$userID==0){
        
    

    $SQL = "Delete From ICDchat.tblChatGroupMbrs where groupID = ".$groupID." and groupMbrID = ".$userID;
    $myresult = mkyMyqry($SQL);

    $SQL = "Delete From ICDchat.tblMbrChat where groupID = ".$groupID." and sentBy = ".$userID;
    $myresult = mkyMyqry($SQL);

    
}
header('Location: pvChatEnd.php?fcpg=pv&wzID='.$sKey);
?>
