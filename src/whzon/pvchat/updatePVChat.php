<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

$pvChat = clean($_GET['fpvc']);

if ($pvChat == "on"){
  $pvChat = "null";
}

if ($pvChat == "friends"){
  $pvChat = "1";
}

if ($pvChat == "off"){
  $pvChat = "2";
}
$SQL = "update tblwzUser set privateChat = ".$pvChat." Where wzUserID=".$userID;
$result = mkyMsqry($SQL);
?>

