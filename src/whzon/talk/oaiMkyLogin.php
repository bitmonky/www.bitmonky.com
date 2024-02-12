<?php
$isMbrLogin = true;
include_once("../mkysess.php");
ini_set('display_errors',1);
error_reporting(E_ALL);
include_once("../gold/goldInc.php");
include_once("../utility/mbrUtility.php");
include_once("../JSON.php");

$inEmail = 'peter@whozontop.com';
$autoLogin = true;

$SQL = "delete from tblTempPass where tmpEmail = '".$inEmail."'";   
$presult = mkyMsqry($SQL);

$datestamp = mkySQLDstamp();
$IP   = left($_SERVER['REMOTE_ADDR'],84);
$BRST = left($_SERVER['HTTP_USER_AGENT'],150);
$login = null;

$SQL = "select muidAccLock,wzUserID,TIMESTAMPDIFF(minute,lastOnline,now()) TLOL from tblwzUser ";
$SQL .= "left join ICDirectSQL.tblwzMUID on muidWzUserID = wzUserID ";
$SQL .= "where email like '".$inEmail."' or loginKey = '".$inEmail."'";
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
$wzUID = 0;
$tLOL = null;

if($tRec){
      
  $wzUID     = $tRec['wzUserID'];
  $tLOL      = $tRec['TLOL'];
  $accLock   = $tRec['muidAccLock'];
  if ($accLock && $autoLogin && !$peerMUID){
    exit('locked');	
    $autoLogin = false;
    $inEmail   = "fail";
  } 
  $keygen =  randomKeygenString(25);
  $pubKey = md5($wzUID.$keygen);
  putSession('mkySafe',$keygen);   

  $SQL = "delete from tblwzOnline where wzUserID=".$wzUID;
  $result = mkyMsqry($SQL);

  $SQL =  "insert into tblwzOnline (wzUserID,lastAction,firstname,age,sex,country,moderator,profileText,doNotList,adultContent,";
  $SQL .= "cityID,paidMember, mute,privateChat,verified,inChatChanID,banned,imgFlg,inRoomID,loginIP) "; 
  $SQL .= " select wzUserID, now() as lastAction, firstname,age,sex,country,moderator,left(profileText,350),doNotList,adultContent,";
  $SQL .= "cityID,paidMember, mute,privateChat,verified,inChatChanID,banned,imgFlg,defaultRoom,'".$IP."' from tblwzUser where wzUserID = ".$wzUID;
  $result = mkyMsqry($SQL);

  $SQL = "select wzOnlineID from tblwzOnline  where wzUserID = ".$wzUID;
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  if ($tRec){
    $newu = new mkyUser($wzUID);
    $newu->genSToken();

    $newKey =  scrambleKey2($tRec['wzOnlineID'],$key);
 
    $SQL = "update tblwzOnline set sKey = '".$newKey."',keygen = '".$keygen."',appPubKey='".$pubKey."' where wzUserID = ".$wzUID;
    $result = mkyMsqry($SQL);
    $SQL = "update tblwzUser set online=1, lastOnline=now() where wzUserID = ".$wzUID;
    $result = mkyMsqry($SQL);
  }
}
?>
