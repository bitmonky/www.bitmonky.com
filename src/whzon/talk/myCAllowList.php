<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

?>
<!doctype html>
<html class="pgHTM" lang="en">
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
function removeUser(id){
   var conf = confirm('Remove This Member From The Channel?');
   if (conf){
     document.location.href='removeChanReq.php?wzID=<?php echo $sKey;?>&chanAID=' + id;
   }
} 
function allowUser(id){
   var conf = confirm('Allow This Member Access To Your Channel?');
   if (conf){
     document.location.href='approveChanReq.php?wzID=<?php echo $sKey;?>&chanAID=' + id;
   }
}
</script>
<div class='infoCardClear'>
<h1 style='font-size:14px;'>This Channels Allow List</h1>

<?php
   $channel = $inChatChanID;

   echo "<h2 style='font-size:12px;'>Pending Requests To Join Your Channel</h2>";

   $SQL = "SELECT chanAllowID,allowUserID,firstname from tblChanAllowList  ";
   $SQL .= "inner join tblwzUser on wzUserID = allowUserID ";
   $SQL .= "where status is null and chanID=".$channel." and chanOwnerID=".$userID;
   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);

   if (!$tRec){
     echo " - no pending requests to join.";
   }

   while ($tRec){
     ?>
     <a href="javascript:parent.wzGetPage('/whzon/mbr/mbrProfile.php?wzId=<?php echo $sKey;?>&fwzUserID=<?php echo $tRec['allowUserID'];?>');">
     <img style='float:left;border-radius:50%;margin-right:8px;margin-bottom:5px;'
     src='//image.bitmonky.com/getMbrTnyImg.php?id=<?php echo $tRec['allowUserID'];?>'> <?php echo $tRec['firstname'];?></a>
     | <a href='javascript:removeUser(<?php echo $tRec['chanAllowID'];?>);'>Remove User[-]</a>
     | <a href='javascript:allowUser(<?php echo $tRec['chanAllowID'];?>);'>Allow[+]</a><br clear='left'>
     <?php
     $tRec = mkyMsFetch($result);
   }

   echo "<h2 style='font-size:12px;'>Members With Permission To Access Your Channel</h2>";

   $SQL = "SELECT chanAllowID,allowUserID,firstname from tblChanAllowList  ";
   $SQL .= "inner join tblwzUser on wzUserID = allowUserID ";
   $SQL .= "where status = 1 and chanID=".$channel." and chanOwnerID=".$userID;
   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);

   if (!$tRec){
     echo " - you are not allowing any users.";
   }

   while ($tRec){
     ?>
     <a href="javascript:parent.wzGetPage('/whzon/mbr/mbrProfile.php?wzId=<?php echo $sKey;?>&fwzUserID=<?php echo $tRec['allowUserID'];?>');">
     <img style='float:left;border-radius:50%;margin-right:8px;margin-bottom:5px;' 
     src='//image.bitmonky.com/getMbrTnyImg.php?id=<?php echo $tRec['allowUserID'];?>'> <?php echo $tRec['firstname'];?></a>
     | <a href='javascript:removeUser(<?php echo $tRec['chanAllowID'];?>);'>Remove User[-]</a><br clear='left'>
     <?php
     $tRec = mkyMsFetch($result);
   }

   
?>
  </div>
</BODY>
</HTML>
