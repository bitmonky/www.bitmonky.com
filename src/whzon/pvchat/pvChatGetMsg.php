<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
$mbrID=clean($_GET['fmbrID']);

include_once("../JSON.php");


$WRTCtalkToID = null;
$hutOwnID  = getUserID(safeGET('hutID'));
$chathut = null;
$chathut = ' mchaDigiHutOID is null and ';

if ($hutOwnID){
  $chathut = ' mchaDigiHutOID = '.$hutOwnID.' and ';
}

if (isset($_GET['fWRTC'])){
    $SQL = "SELECT wzWRTCtalkToID  From tblwzUser  where wzUserID=".$userID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $WRTCtalkToID = $tRec['wzWRTCtalkToID'];
}
if (!$userID==0){
    
    $countryID = 0;
    $IPcountryID = 0;

    $SQL = "select firstname,countryID,IPcountryID from tblwzUser  where wzUserID=".$mbrID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if ($tRec){
      $countryID=$tRec['countryID'];
      $IPcountryID=$tRec['IPcountryID'];
    }

    $nBlock = 0;
    if ($IPcountryID){
      $SQL = "SELECT count(*) as nBlock from tblwzUserBlockList  ";
      $SQL .= "WHERE wzUserID=".$userID." and ( BLKcountryID=".$countryID." or BLKcountryID=".$IPcountryID.");";
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      $nBlock = $tRec['nBlock'];
    }

    if ($nBlock == 0){
      $SQL = "select count(*) as nblock from tblwzUserBlockList  where wzUserID=".$userID." and blockUserID=".$mbrID;
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      $nBlock=$tRec['nblock'];
    }
        
    
    
    if ($nBlock==0){
      $SQL = "SELECT * From ICDchat.tblMbrChat where ".$chathut." mread is null and msgMbrID=".$userID." and sentBy=".$mbrID." ORDER BY msgID";
      $myresult = mkyMyqry($SQL);
      $mRec = mkyMyFetch($myresult);
	  
	  if (!$mRec){
	    exit('{"myMsgs":[]}');
	  }
	  
  	  $jObj = '{"myMsgs" : ['; 
	  $n = 1;
      $j = new stdClass;
      while ($mRec){
		if ($n == 1){$coma = ''; $n = 2;} else {$coma = ',';}
        $msg=mkyStrReplace(";",":",$mRec['msg']);
        $msgID = $mRec['msgID'];
        mkyMyqry("update ICDchat.tblMbrChat set mread=1 where msgID=".$mRec['msgID']);

        $j->msgID = $msgID;
        $j->htm   = $msg;
     
        $jObj .= $coma.(json_encode($j));      
        $mRec = mkyMyFetch($myresult);
      }
	  $jObj .= ']}';
      echo $jObj;
    
    }
    
}


    
