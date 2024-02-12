<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
$mbrID=clean($_GET['fmbrID']);

include_once("../JSON.php");



$WRTCtalkToID = null;

if (isset($_GET['fWRTC'])){
    $SQL = "SELECT wzWRTCtalkToID  From tblwzUser where wzUserID=".$userID;
    $result = mkyMsqry($SQL) or die($SQL);
    $tRec = mkyMsFetch($result);
    $WRTCtalkToID = $tRec['wzWRTCtalkToID'];
}
if (!$userID==0 ){

      $SQL = "SELECT * From ICDchat.tblMbrChat where mread = 1 and (( msgUserID=".$userID." and msgMbrID=".$mbrID.") or ";
      $SQL .= "(msgUserID=".$mbrID." and msgMbrID=".$userID.")) ORDER BY msgID desc limit 60";

      $myresult = mkyMyqry($SQL);
      $mRec = mkyMyFetch($myresult);
	  
	  if (!$mRec){
	    exit('{"myMsgs":[]}');
	  }
	  
  	  $jObj = '{"myMsgs" : ['; 
	  $n = 1;
      $j = new stdClass;
    
      while ($mRec){
		if ($n == 1){$coma = '';} else {$coma = ',';}
	    $WRTCsender = $mRec['WRTCid'];
		if ($userID != $WRTCsender){
          $msg=mkyStrReplace(";",":",$mRec['msg']);
          $name="namefiller";
          if ($mRec['sentBy'] != $WRTCtalkToID) {
		    $n = 2;
			$j->msgID   = $mRec['msgID'];
			$j->guserID = $mRec['sentBy'];
			$j->gname   = $name;
			$j->htm     = $msg;
     
            $jObj .= $coma.(json_encode($j));
	      }
		}
        $mRec = mkyMyFetch($myresult);
      }
      
	  $jObj .= ']}';
      echo $jObj;
}
?>
