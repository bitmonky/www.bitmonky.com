<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
$mbrID=clean($_GET['fmbrID']);
$bothUseChrome = null;

$SQL = "select TIMESTAMPDIFF(second,lastAction,now()) as lastAction,wzWRTCon, tblwzUser.firstname ";
$SQL .= "from tblwzUser  left join tblwzOnline  on tblwzOnline.wzUserID = tblwzUser.wzUserID ";
$SQL .= "where tblwzUser.wzUserID = ".$mbrID;

$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
if (!$tRec){
  $lastAction = null;
  $otherName  = null;
}
else {
  $lastAction = $tRec['lastAction'];
  $otherName  = $tRec['firstname'];
  if ($tRec['wzWRTCon'] && $wzWRTCon){
    $bothUseChrome = 1;
  }
}
$style = " style='color:brown;'";
if ($lastAction){
  $style='';
}
$html = "<br>Last Action: <b ".$style.">".strLastAction($lastAction)."</b>";

if ($bothUseChrome || $userID == 17621) {
  if ($lastAction < 45 && $lastAction){
    $status = "<a style='color:orange;' href='javascript:startWRTCChat();'>Start Video Chat With - ".$otherName."</a>";
  }
  else {
    $status = $otherName." Not Available for Video Chat.";
  }
}
else { 
  if (!$wzWRTCon) { 
    $status = "<span style='padding:3px;background:none;border-radius: .5em;'><a style='color:orange;' href='//www.google.com/chrome/' target='_new' >Download Chrome And Enjoy Free MonkyTalk Video Chat!</a></span>";
  } 
  else { 
    $status = "sorry ".$otherName." Is not using chrome!";
  }
}


echo '{ "html" : "'.$html.'", "status" : "'.$status.'"}';
?>
