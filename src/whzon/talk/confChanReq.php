<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

?>
<!doctype html>
<html class='pgHTM' lang="en">
<head>
  <meta charset="utf-8">
  <title>Whzon.com</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0">
  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</HEAD>
<body class='pgBody' style='margin:20px;'>
<script>
</script>

<h2 style='font-size:12px;'>Request For Access To This Channel</h2>


<?php
   $channel = $inChatChanID;
   $SQL = "select ownerID from tblChatChannel  ";
   $SQL .= "where channelID = ".$channel;
   $result = mkyMsqry($SQL);
   $tRec   = mkyMsFetch($result);
   
   if ($tRec){
     $ownerID = $tRec['ownerID'];
   
     $SQL = "select allowUserID from tblChanAllowList ";
     $SQL .= "where chanID = ".$channel." and allowUserID = ".$userID;
     $result = mkyMsqry($SQL);
     $tRec   = mkyMsFetch($result);

     if(!$tRec){
       $SQL = "insert into tblChanAllowList (allowUserID,chanOwnerID,chanID) ";
       $SQL .= "values(".$userID.",".$ownerID.",".$channel.")";
       $result = mkyMsqry($SQL);
       echo "<h2>Thank You... Request Has Been Sent</h2>";
     }
     else {
       echo "<h2>Your Request Is Already Been Sent... Your will be notified soon.";
     }
   }
   else {
     echo "<h2>channel owner not found! Try again</h2>";
   }
   
?>
</BODY>
</HTML>
