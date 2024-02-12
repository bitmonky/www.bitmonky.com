<?php
session_start(); 
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

$groupID = clean($_GET['fgroupID']);


?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>BitMonk</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>
  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</HEAD>
<body style='margin:15px;background:white;'>
<b>Remove Me From This Chat Group</b>

<form method="GET" action="groupRemoveMe.php">
<input type="hidden" name="fgroupID" value="<?php echo $groupID;?>">
<input type="hidden" name="wzID" value="<?php echo $sKey;?>">

Confirm Remove From Chat Group<br/>
   <input type="radio" name="fconfirm" value="yes" checked> yes 
   <input type="radio" name="fconfirm" value="no" > no<br/> 
<input name="faction" type="submit" value="Remove Me" > 
<a href="groupChat.php?wzID=<?php echo $sKey;?>&fgroupID=<?php echo $groupID?>">[Cancel]</a>

</body></html>


