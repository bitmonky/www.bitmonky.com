<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
if (isset($_GET['fchatID'])){$chatID = clean($_GET['fchatID']);} else {$chatID=null;}

if ($_GET['fconfirm'] == "yes" && $userID != 0){

   $SQL = "select channel from tblChatterBox where msgID=".$chatID;
   $result  = mkyMsqry($SQL);
   $tRec    = mkyMsFetch($result);
   $chanID  = $tRec['channel'];
   
   $SQL = "delete tblChatterBox where wzUserID=".$userID." and msgID=".$chatID;
   $result = mkyMsqry($SQL);
   $SQL = "delete tblChatterBox from tblChatterBox inner join tblChatChannel on channel=channelID where ownerID=".$userID." and msgID=".$chatID;
   $result = mkyMsqry($SQL) or die($SQL);
   $SQL = "delete tblChatterBuf from tblChatterBuf inner join tblChatChannel on channel=channelID where ownerID=".$userID." and realID=".$chatID;
   $result = mkyMsqry($SQL);
   $SQL = "update tblwzOnline set refresh=1";
   $result = mkyMsqry($SQL);
   
   $SQL = "update tblChanReload set reloadCounter = reloadCounter + 1, reloadTime = now() where reloadChanID = ".$chanID; 
   $result = mkyMsqry($SQL);
}

?>
<html class='pgHTM'>
<head>
<script>
    parent.wzReloadChannel();
    parent.wzAPI_closeWin();
</script>
</head>
<body class='pgBody'>
<A HREF='javascript:window.parent.wzAPI_closeWin();'>[Done]</a>
</body>
</html>

