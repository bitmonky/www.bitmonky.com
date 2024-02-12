<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
if ($userID!=0) {

  $SQL = "SELECT CAST(moderator as integer) as moderator from tblwzUser  where wzUserID=".$userID; 
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);

  $isMOD = null;
  if ($tRec){
    $isMOD = $tRec['moderator'];
  }

  if (is_null($isMOD) || 1 == 1) {    

    $SQL = "SELECT wzUserID,blockUserID from tblwzUserBlockList  where wzUserID=".$userID." or blockUserID=".$userID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    if (!$tRec){
      echo "0;";
    }

    while ($tRec){
      if ($tRec['wzUserID'] == $userID) {
        echo $tRec['blockUserID'].";";
        }
      else{
        echo $tRec['wzUserID'].";";
      }
      $tRec = mkyMsFetch($result);
    }//wend
  }
  else{
    echo "0;";
  }
}  
else{
   echo "0;";
}

?>

