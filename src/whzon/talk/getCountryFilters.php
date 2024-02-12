<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");


if ($userID!=0) {
    $SQL = "SELECT BLKcountryID from tblwzUserBlockList  where wzUserID=".$userID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    if (!$tRec){
      $json = '{"myRFilters" : []}';
    }
	else {
	  $comma = null;
      $json = '{"myFilters" : [';
      while ($tRec){
        $json .=  $comma.$tRec['BLKcountryID'];
        $tRec = mkyMsFetch($result);
	    $comma = ",";
      }//wend
	  $json .= "]}";
    }
    echo $json;
}	

?>

