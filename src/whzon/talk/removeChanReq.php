<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

$chanAID = safeGetINT('chanAID');
     
$SQL = "delete from  tblChanAllowList  where chanOwnerID=".$userID." and chanAllowID=".$chanAID;
$result = mkyMsqry($SQL);


header('Location: myCAllowList.php?wzID='.$sKey);
?>
