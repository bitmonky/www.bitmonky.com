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
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</HEAD>
<body class='pgBody' style='margin:20px;'>


<h1 style='font-size:14px;'>Blocking Users:</h1>

You can now block users in public chat as well.  To block a user that is bothering you in public chat... Click on the users picture and then click on the block 
user link near the top middle of the page.

<b>See Also:</b> <a href='blockCountryFrm.php?wzID=<?php echo $sKey;?>'> Blocking  Countries</a>


<h2 style='font-size:12px;'>Users You Have Blocked</h2>


<?php


   $SQL = "select blockUserID, firstname from tblwzUserBlockList  inner join tblwzUser  on tblwzUser.wzUserID=blockUserID ";
   $SQL .= "where tblwzUserBlockList.wzUserID=".$userID." order by bdate desc";

   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);

   if (!$tRec){
     echo " - you are not blocking any users.";
   }

   while ($tRec){

?>
     <a href="javascript:parent.wzGetPage('/whzon/mbr/mbrProfile.php?wzId=<?php echo $sKey;?>&fwzUserID=<?php echo $tRec['blockUserID'];?>');">
     <img style='float:left;border:0px;margin-right:8px;margin-bottom:5px;' src='//image.bitmonky.com/getMbrTnyImg.php?id=<?php echo $tRec['blockUserID'];?>'> <?php echo $tRec['firstname'];?></a>
     | <a href='blockUndoFrm.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $tRec['blockUserID'];?>'> Unblock User</a><br clear='left'>
<?php
     $tRec = mkyMsFetch($result);
    }

?>

</BODY>
</HTML>
