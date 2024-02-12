<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");

if (isset($_GET['fgroupID'])){ $groupID = safeGET('fgroupID');} else {$groupID = null;}



if (!$userID==0){
    $modFlg = 'null';
    if ($isMod) {$modFlg = 1;}
	if ($isAdmin) {$modFlg = 2;}
	
        
    
    $msg = clean($_GET['fmsg']);

    $SQL = "Insert into ICDchat.tblMbrChat (msg,groupID,msgUserID,msgMbrID,sentBy,modFlg) values('".left($msg,250)."',".$groupID.",".$userID.",0,".$userID.",".$modFlg.")"; 
    $myresult = mkyMyqry($SQL);

    

    echo "OK";
}
?>
