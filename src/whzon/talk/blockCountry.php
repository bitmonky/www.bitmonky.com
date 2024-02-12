<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

$blkUserID    = 0;
$blkCountryID = clean($_GET['fcountryID']);
$email="na";

if ($blkCountryID=="") {$blkCountryID=0;}
if ($userID!=0){

  $SQL="select count(*) as nRec from tblwzUserBlockList where  wzUserId=".$userID." and BLKCountryID=".$blkCountryID;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  $nBlocked=$tRec['nRec'];

  if ($nBlocked==0){
    $SQL = "insert into tblwzUserBlockList (wzUserID,blockUserID,email,reasonCD,BLKCountryID)  values (".$userID.",".$blkUserID.",'".$email."',1,".$blkCountryID.")";
    $result = mkyMsqry($SQL);
  }
}

header("Location: blockCountryFrm.php?mode=update&wzID=".$sKey);
?>
