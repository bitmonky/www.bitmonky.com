<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");

$groupID   = safeGetINT('fgroupID');
$lastMsgID = safeGetINT('flastMsgID');
if (!$lastMsgID){
  $lastMsgID = 0;
}


if (!$userID==0 ){

      if ($lastMsgID == 1){
        $SQL = "SELECT qdate,msg,msgID,sentBy From ICDchat.tblMbrChat where msgID > ".$lastMsgID." and groupID=".$groupID." ORDER BY msgID";
      }
      else {
        $SQL  = "SELECT qdate,msg,msgID,sentBy From ICDchat.tblMbrChat where msgID > ".$lastMsgID." and groupID=".$groupID;
        $SQL .= " and NOT sentBy = ".$userID." ORDER BY msgID";
      }
      $myresult = mkyMyqry($SQL);
      $mRec = mkyMyFetch($myresult);
      if (!$mRec){
        exit('{"myMsgs":[]}');
      }
      $j = new stdClass;	  
      $j->myMsgs = [];
      $jObj = '{"myMsgs" : ['; 
      $n = 1;
      $i = 0;
      while ($mRec){
        if ($n == 1){$coma = '';} else {$coma = ',';}
        $msg  = mkyStrReplace(";",":",$mRec['msg']);
        $msg  = mkyStrReplace("\n","<br/>",$msg);
        $SQL = "select firstname from tblwzUser  where wzUserID = ".$mRec['sentBy'];
        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);
        $lastMsgID = $mRec['msgID'];
        $name = clean($tRec['firstname']);
        $n = 2;
        $jObj .= $coma.'{"msgID":'.$mRec['msgID'].',';
        $jObj .= '"guserID":'.$mRec['sentBy'].',';
        $jObj .= '"gname":"'. $name.'",';
        $jObj .= '"htm":"'.mkyStrReplace('"',"'",$msg).'"}';

        $j->myMsgs[$i]->msgId   = $mRec['msgID'];
        $j->myMsgs[$i]->guserID = $mRec['sentBy'];
        $j->myMsgs[$i]->gname   = $name;
        $j->myMsgs[$i]->htm     = mkyStrReplace('"',"'",$msg);
        $j->myMsgs[$i]->date    = $mRec['qdate'];
        $i = $i+1;

        $mRec = mkyMyFetch($myresult);
        if (!$mRec){
          $datestamp = mkySQLDstamp();
          $SQL = "Update ICDchat.tblChatGroupMbrs set gLastMsgID = ".$lastMsgID.",gLastRead = '".$datestamp."', gntoRead = 0 where groupMbrID=".$userID;
          $myresult = mkyMyqry($SQL);
        }
      }
      $jObj .= ']}';
      echo json_encode($j);
}
?>
