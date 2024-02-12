<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");


if (!$userID==0){

    $SQL = "SELECT sandBox,privateChat from tblwzUser WHERE wzUserID=".$userID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $pvchat=$tRec['privateChat'];
    $sandBox=$tRec['sandBox'];
	

    if (is_null($pvchat))
      $pvchat=0;

    if (!$sandBox){
          
      
      $SQL = "SELECT  * from ICDchat.tblChatGroupMbrs ";
	  $SQL .= "inner join ICDchat.tblChatGroup on groupID = chatGroupID ";
      $SQL .= "where groupMbrID = ".$userID;

      $myresult = mkyMyqry($SQL);
	  $jObj = '{"myGroups" : ['; 
	  $mRec = mkyMyFetch($myresult);
      while ($mRec){
        $groupID       = $mRec['groupID'];
        $chatGroupName = $mRec['chatGroupName'];
		$groupOwnerID  = $mRec['groupOwnerID'];
		$lastMsgID     = $mRec['gLastMsgID'];
		$lastRead      = $mRec['gLastRead'];
		if (!$lastMsgID) {$lastMsgID = 1;}
		if (!$lastRead)  {$lastRead  = '-';}
		$gntoRead      = checkntoRead($groupID,$userID,$lastMsgID);
		if (!$gntoRead)  {$gntoRead  = 0;}
		$jObj .= '{ "gname" : "'.$chatGroupName.'", "groupID" : "'.$groupID.'", "ownerID" : "'.$groupOwnerID.'", "lastMsgID" : "'.$lastMsgID.'", "lastRead" : "'.$lastRead.'", "gntoRead" : "'.$gntoRead.'" }';
		$mRec = mkyMyFetch($myresult);
		if ($mRec) {$jObj .= ',';}
    } //wend
	$jObj .= ']}';
	echo $jObj;
    
  }
}


function checkntoRead($groupID,$userID,$lastMsgID){
  $SQL = "select count(*) as nRec from ICDchat.tblMbrChat where groupID = ".$groupID." and (NOT sentBy = ".$userID.") and msgID > ".$lastMsgID;
  $myresult = mkyMyqry($SQL) or die($SQL);
  $rRec = mkyMyFetch($myresult);
  return $rRec['nRec'];
}
?>