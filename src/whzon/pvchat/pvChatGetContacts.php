<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
$mbrID=clean($_GET['fmbrID']);

if (!$userID==0){

    $SQL = "SELECT sandBox,privateChat from tblwzUser  WHERE wzUserID=".$userID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $pvchat=$tRec['privateChat'];
    $sandBox=$tRec['sandBox'];

    if (is_null($pvchat))
      $pvchat=0;

    if (!$sandBox){
      $SQL = "SELECT  sentBy, count(*) as nMsg From ICDchat.tblMbrChat ";
      $SQL .= "where mread is null and msgMbrID=".$userID;
      $SQL .= " group by sentBy"; 

      $myresult = mkyMyqry($SQL);
      $mRec = mkyMyFetch($myresult);	
	  if (!$mRec){
	    exit('{"myMsgs":[]}');
	  }
	  
  	  $jObj = '{"myMsgs" : ['; 
	  $n = 1;
      while ($mRec){
        $sentBy    = $mRec['sentBy'];
        $nmsg      = $mRec['nMsg'];
        $block=0;
        $friends=0;
		if ($n == 1){$coma = '';} else {$coma = ',';}

        $SQL = "select firstname,countryID,IPcountryID from tblwzUser  where sandBox is null and wzUserID=".$sentBy;

        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);
        if ($tRec) {

          $firstname = $tRec['firstname'];
          $countryID=$tRec['countryID'];
          $IPcountryID=$tRec['IPcountryID'];
		  if (!$countryID) {$countryID = 0;}
		  if (!$IPcountryID) {$IPcountryID = 0;}

          if ($pvchat==1) {
            $SQL = "select count(*) as nBlock from tblwzUserFriends  where wzUserID=".$userID." and frienduserID=".$sentBy." and Status=1";
            $result = mkyMsqry($SQL);
            $tRec = mkyMsFetch($result);
            if ($tRec['nBlock']==1)
              $friends=1;
          }

          $SQL = "SELECT count(*) as nBlock from tblwzUserBlockList  WHERE wzUserID=".$userID." and ( BLKcountryID=".$countryID." or BLKcountryID=".$IPcountryID.");";
          $result = mkyMsqry($SQL);
          $tRec = mkyMsFetch($result);
          $block = $tRec['nBlock'];

          if ($block == 0){
            $SQL = "select count(*) as nBlock from tblwzUserBlockList  where wzUserID=".$userID." and blockUserID=".$sentBy;
            $result = mkyMsqry($SQL);
            $tRec = mkyMsFetch($result);
            $block= $tRec['nBlock'];
          }

          if ($pvchat==1){
            if ($block==0 && $friends==1){
              $jObj .= $coma.'{"sentBy":'.$sentBy.',"htm":"'.getHTML($sentBy,$firstname,$nmsg,$userID,$sKey).'"}';
			  $n = $n + 1;
            }
          }
          else{
            if ($block==0) {
              $jObj .= $coma.'{"sentBy":'.$sentBy.',"htm":"'.getHTML($sentBy,$firstname,$nmsg,$userID,$sKey).'"}';
			  $n = $n + 1;
            }
          }  
        }
        $mRec = mkyMyFetch($myresult);	
      } //wend
      
	  $jObj .= ']}';
      echo $jObj;
  }
}


function getHTML($sentBy,$firstname,$nmsg,$userID,$sKey){
     $firstname = clean(mkyStrReplace("!","*",$firstname));
     return "<table><tr><td><a href='pvchatApp.php?wzID=".$sKey."&fmbrID=".$sentBy."'><img style='border-radius:50%;border: 0px solid #e0e0e0;height:2em;width:23m;margin-bottom:2px;float:left;margin-right:8px;' src='//image.bitmonky.com/getMbrTnyImg.php?id=".$sentBy."'> ".$firstname."</a> - ".$nmsg."</td></tr></table>";
}
?>
