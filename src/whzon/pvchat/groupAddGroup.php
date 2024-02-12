<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");

if (isset($_GET['fgname'])){ $gname = safeGET('fgname');} else {$gname = null;}
if (isset($_GET['fgdesc'])){ $gdesc = safeGET('fgdesc');} else {$gdesc = 'none';}


$groupID = 0;

if (!$gname || $gname == ''){
  header('Location: groupAddGroupFrm.php?ferror=1&wzID='.$sKey);
  exit('');
}

if (!$userID==0){
        
    

    $SQL = "Select chatGroupID as groupID from ICDchat.tblChatGroup where groupOwnerID = ".$userID." and chatGroupName = '".$gname."'";
    $myresult = mkyMyqry($SQL);
	$mRec = mkyMyFetch($myresult);
	
	if (!$mRec){
      $SQL = "Insert into ICDchat.tblChatGroup (groupOwnerID,chatGroupName,chatGroupDesc) values(".$userID.",'".$gname."','".$gdesc."')"; 
      $myresult = mkyMyqry($SQL) or die($SQL);
	  
      $SQL = "Select chatGroupID as groupID from ICDchat.tblChatGroup where groupOwnerID = ".$userID." and chatGroupName = '".$gname."'";
      $myresult = mkyMyqry($SQL);
	  $mRec = mkyMyFetch($myresult) or die($SQL);
	  $groupID = $mRec['groupID'];

      $SQL = "Insert into ICDchat.tblChatGroupMbrs (groupID,groupMbrID,mbrStatus) values(".$groupID.",".$userID.",1)"; 
      $myresult = mkyMyqry($SQL) or die($SQL);
	}  
	else {
	  $groupID = $mRec['groupID'];
	}
    
}
header('Location: groupSelMbrAutoT.php?wzID='.$sKey.'&fgroupID='.$groupID);
?>
