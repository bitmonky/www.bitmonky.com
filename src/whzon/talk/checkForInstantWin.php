<?php
$sessionMode="lite";
include_once("../mkysess.php");
include_once("../gold/goldInc.php");

    // **********************************************
    // Module Check For instant win record  
    // ***********************************************
    $winRecID = 0;
    $SQL  = "select isWinID from tblGoldInstantWinClaims ";
    $SQL .= "where TIMESTAMPDIFF(second,iswDate,now()) < ".$gracePeriod." and iswNotified is null and iswUID = ".$userID;
    $iresult = mkyMsqry($SQL);
    $iRec    = mkyMsFetch($iresult);

    if ($iRec){
      $winRecID = $iRec['isWinID'];
      $SQL  = "Update tblGoldInstantWinClaims set iswNotified = 1, iswTriger = 'chatJOBJ' where isWinID = ".$winRecID;
      $iresult = mkyMsqry($SQL) or die('dead -> '.$SQL);
      echo $winRecID;
    }
    else {
      echo 0;
    }
	  
    
?>

