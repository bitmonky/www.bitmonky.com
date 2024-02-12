<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
$blkUserID=clean($_GET['fwzUserID']);


?>
<!doctype html>
<html class="pgHTM" lang="en">
<head>
  <meta charset="utf-8">
  <title>Whzon.com</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0">
  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</HEAD>
<body class='pgBody'>


<h1 style='font-size:14px;'>Confirm Unblock User:<h1>


<form method="GET" action="blockUndoAJ.php">
<input type="hidden" name="fwzUserID" value="<?php echo $blkUserID;?>">
<input type="hidden" name="wzID"  value="<?php echo $sKey;?>">
<table border=0>
<tr valign='top'><td><img style='border:0px;margin:5px;' src='//image.bitmonky.com/getMbrTmn.php?id=<?php echo $blkUserID;?>'></td><td> Confirm unBlock this user</td><td>
   <input type="radio" name="fconfirm" value="yes" checked> yes 
   <input type="radio" name="fconfirm" value="no" > no 
<input name="faction" type="submit" value="Unblock" ></td></tr>
</table>    

</BODY>
</HTML>
