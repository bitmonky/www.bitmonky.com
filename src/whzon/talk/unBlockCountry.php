<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

$blkCountryID = clean($_GET['fcountryID']);
if ($blkCountryID=="") {$blkCountryID=0;}

if ($userID!=0){
  $SQL = "delete from tblwzUserBlockList where wzUserID=".$userID." and BLKcountryID=".$blkCountryID;
  $result = mkyMsqry($SQL);
}

header("Location: blockCountryFrm.php?mode=update&wzID=".$sKey);
?>
