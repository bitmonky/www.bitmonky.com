<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

  if (isset($_GET['fcityID'])){$cityID = clean($_GET['fcityID']);} else {$cityID = "";}
  if (isset($_GET['fcountryID'])){$countryID = clean($_GET['fcountryID']);} else {$countryID = "";}
  if (isset($_GET['fprovID'])){$provID = clean($_GET['fprovID']);} else {$provID = "";}
  $isTycAd = safeGET('tycc');

  if ($cityID != "" ) {
    changeCity($cityID,$sKey);
    if (!safeCOOK("myTownStart") && $isTycAd){
      putYearCookie("myTownStart",$cityID);
      
    }
  }

  if ( $provID != "" ) {
    changeProv($provID,$sKey);
  }

  if ( $countryID != "" ) {
    changeCountry($countryID,$sKey);
  }

function changeCountry($countryID,$sKey){

  $SQL = "select wzUserID from tblwzUser where countryID=".$countryID." and sandBox is null and imgflg=1 order by lastOnline desc limit 1";
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

  header("Location: /whzon/mytown/myTownProfile.php?wzID=".$sKey."&myCountryID=".$countryID."&fscope=myCountry&fwzUserID=".$wzUserID);
}
function changeProv($provID,$sKey){

  $SQL = "select tblCity.cityID,wzUserID from tblCity  ";
  $SQL .= "inner join tblState  on tblCity.StateID=tblState.stateID ";
  $SQL .= "inner join tblwzUser  on tblwzUser.stateID = tblCity.StateID ";
  $SQL .= "where tblCity.DopeFlg=0 and sandBox is null and tblCity.StateID=".$provID."  limit 1";
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

  header("Location: /whzon/mytown/myTownProfile.php?fsmCID=".$cityID."&wzID=".$sKey."&myStateID=".$provID."&fscope=myState&fwzUserID=".$wzUserID);
}


function changeCity($cityID,$sKey){

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

  header("Location: /whzon/mytown/myTownProfile.php?wzID=".$sKey."&fscope=myCity&fwzUserID=".$wzUserID."&myCityID=".$cityID);
}
?>
