<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

  if (isset($_GET['fcityID'])){$cityID = clean($_GET['fcityID']);} else {$cityID = "";}
  if (isset($_GET['fcountryID'])){$countryID = clean($_GET['fcountryID']);} else {$countryID = "";}
  if (isset($_GET['fprovID'])){$provID = clean($_GET['fprovID']);} else {$provID = "";}

  $catID = safeGET('fcatID');
  if ($catID !== null){
    $catID = "&fcatID=".$catID;
  }
  $fsq = safeGET('fsq');
  $fsqPg = null;
  if ($fsq){
    $fsqPg = "&fsq=".mkyUrlEncode($fsq);
    $catID .= $fsqPg;
  }
  
  if ($cityID != "" ) {
    changeCity($cityID,$sKey,$catID);
  }

  if ( $provID != "" ) {
    changeProv($provID,$sKey,$catID);
  }

  if ( $countryID != "" ) {
    changeCountry($countryID,$sKey,$catID);
  }

function changeCountry($countryID,$sKey,$catID){

  $SQL = "select wzUserID from tblwzUser where countryID=".$countryID." and sandbox is null and imgflg=1 order by lastOnline desc limit 1";
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);

  if ( $tRec ) {
    $wzUserID = $tRec['wzUserID'];
  } 
  else {
    $SQL = "select cityID from tblCity where dopeFlg=0 and CountryID=".$countryID." ORDER BY rand() limit 1"; 
    $tRec = null;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
	if($tRec){
	  $city = $tRec['cityID'];
      $wzUserID="63555&fsmCID=".$city;
	}
	else {
      $wzUserID="63555";
	}
  }

  header("Location: /whzon/mytown/myTownProfile.php?newPg=0&wzID=".$sKey.$catID."&fscope=myCountry&fwzUserID=".$wzUserID);
}
function changeProv($provID,$sKey,$catID){

  $SQL = "select tblCity.cityID,wzUserID from tblCity  ";
  $SQL .= "inner join tblState  on tblCity.StateID=tblState.stateID ";
  $SQL .= "inner join tblwzUser  on tblwzUser.stateID = tblCity.StateID ";
  $SQL .= "where tblCity.DopeFlg=0 and sandbox is null and tblCity.StateID=".$provID." limit 1";
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  global $userID;
  if ($userID==17621){
    //exit($SQL);
  }
  if ($tRec) {
    $cityID = $tRec['cityID'];
    $wzUserID = $tRec['wzUserID'];
  } 
  else {
    $cityID = null;
    $wzUserID =  "63555";
  }

  header("Location: /whzon/mytown/myTownProfile.php?newPg=0&fsmCID=".$cityID."&wzID=".$sKey.$catID."&fscope=myState&fwzUserID=".$wzUserID);
}


function changeCity($cityID,$sKey,$catID){

  $SQL = "select wzUserID from tblwzUser where cityID=".$cityID." and verified= 1 and imgflg=1 order by lastOnline desc limit 1";
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);

  if ($tRec) {
    $wzUserID = $tRec['wzUserID'];
  } 
  else {
    $wzUserID = "63555&fsmCID=".$cityID;
  }

  header("Location: /whzon/mytown/myTownProfile.php?newPg=0&wzID=".$sKey.$catID."&fscope=myCity&fwzUserID=".$wzUserID);
}
?>
