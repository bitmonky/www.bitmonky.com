<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
$blkUserID=clean($_GET['fwzUserID']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>BitMonk</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0">
  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</HEAD>
<body style='background:white;'>


<h1 style='font-size:14px;'>Confirm Block User:<h1>



<form method="GET" action="blockUserAJ.php">
<input type="hidden" name="fmbrID" value="<?php echo $blkUserID;?>">
<input type="hidden" name="wzID"  value="<?php echo $sKey;?>">
<table border=0>
<tr valign='top'><td><img style='border:0px;margin-right:5px;' src='//image.bitmonky.com/getMbrTmn.php?id=<?php echo $blkUserID;?>'></td><td> Confirm block this user</td><td>
   <input type="radio" name="fconfirm" value="yes" checked> yes 
   <input type="radio" name="fconfirm" value="no" > no <br>
   Reason : <select name='fblockCD'>
<?php

   $SQL = "select blockCodeID, reason from tblwzUserBlockCode  order by priority";
   $result = mkyMsqry($SQL);
   while ($tRec = mkyMsFetch($result)){

?>
   <option value='<?php echo $tRec['blockCodeID'];?>'><?php echo $tRec['reason'];?>
<?php
    }
  
?>
   </select>

<input name="faction" type="submit" value="Block" ></td></tr>
</table>    

</BODY>
</HTML>
