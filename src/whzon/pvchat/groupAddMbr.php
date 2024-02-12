<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");

if (isset($_GET['fgroupID'])){ $groupID = safeGET('fgroupID');} else {$groupID = null;}
if (isset($_GET['fmbrID']))  { $mbrID   = safeGET('fmbrID');} else {$mbrID = null;}



if (!$userID==0){
        
    

    $countryID   = 0;
    $IPcountryID = 0;
    $sandBox     = null;
	
    $SQL = "select firstname,countryID,IPcountryID,sandBox from tblwzUser  where wzUserID=".$mbrID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if ($tRec){
      $countryID=$tRec['countryID'];
      $IPcountryID=$tRec['IPcountryID'];
	  $sandBox = $tRec['sandBox'];
    }

    $SQL = "SELECT count(*) as nBlock from tblwzUserBlockList  WHERE wzUserID=".$userID." and ( BLKcountryID=".$countryID." ";
	$SQL .= "or BLKcountryID=".$IPcountryID.");";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $nBlock = $tRec['nBlock'];
    
    if ($nBlock == 0){
      $SQL = "select count(*) as nblock from tblwzUserBlockList  where wzUserID=".$userID." and blockUserID=".$mbrID;
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      $nBlock=$tRec['nblock'];
    }
    if ($nBlock == 0 && !$sandBox){

      $SQL = "Select count(*) as nRec from ICDchat.tblChatGroupMbrs where groupID = ".$groupID." and groupMbrID = ".$mbrID;
      $myresult = mkyMyqry($SQL);
  	  $mRec = mkyMyFetch($myresult);
	  if ($mRec['nRec'] == 0 ){
        $SQL = "Insert into ICDchat.tblChatGroupMbrs (groupID,groupMbrID,mbrStatus) values(".$groupID.",".$mbrID.",1)"; 
        $myresult = mkyMyqry($SQL);
	  }
	}  
    
}
header('Location: groupSelMbrAutoT.php?wzID='.$sKey.'&fgroupID='.$groupID);
?>
