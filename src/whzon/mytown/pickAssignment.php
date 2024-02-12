<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

//  $SQL = "select top 1 name, countryID from tblCountry where bannerID is null and dopeFlg=0 ORDER BY rand()";

  $SQL = "SELECT top 1 taskCityID from tblTaskCities where not taskCityID is null and taskUID is null ";
  $SQL .= "ORDER BY rand()";

  $cRec = null;
  $resultCR = mkyMsqry($SQL);
  $cRec = mkyMsFetch($resultCR);
  
  if ($cRec) {
    $cityID = $cRec['taskCityID'];
    changeCity($cityID,$sKey);
	exit('');
  }
  $SQL = "SELECT top 1 taskStateID from tblTaskCities where not taskStateID is null and taskBannerID is null ";
  $SQL .= "ORDER BY rand()";

  $cRec = null;
  $resultCR = mkyMsqry($SQL);
  $cRec = mkyMsFetch($resultCR);
  
  if ($cRec) {
    $stateID = $cRec['taskStateID'];
    changeState($stateID,$sKey);
	exit('');
  }
  header("Location: /whzon/public/homepg.php?wzID=".$sKey);
  exit('');
  
  
function changeState($stateID,$sKey){

  $SQL = "select TOP 1 wzUserID from tblwzUser where stateID=".$stateID." order by lastOnline desc";
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);

  if ( $tRec ) {
    $wzUserID = $tRec['wzUserID'];
  } 
  else {
    $SQL = "select top 1 cityID from tblCity where dopeFlg=0 and stateID=".$stateID." ORDER BY rand()"; 
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

  header("Location: /whzon/mytown/myTownEditor.php?wzID=".$sKey."&fscope=myState&fwzUserID=".$wzUserID);
}
  
function changeCountry($countryID,$sKey){

  $SQL = "select TOP 1 wzUserID from tblwzUser where countryID=".$countryID." and sandBox is null and imgflg=1 order by lastOnline desc";
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);

  if ( $tRec ) {
    $wzUserID = $tRec['wzUserID'];
  } 
  else {
    $SQL = "select top 1 cityID from tblCity where dopeFlg=0 and CountryID=".$countryID." ORDER BY rand()"; 
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

  header("Location: /whzon/mytown/myTownEditor.php?wzID=".$sKey."&fscope=myCountry&fwzUserID=".$wzUserID);
}


function changeCity($cityID,$sKey){

  $SQL = "select TOP 1 wzUserID from tblwzUser where cityID=".$cityID;
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);

  if ($tRec) {
    $wzUserID = $tRec['wzUserID'];
  } 
  else {
    $wzUserID = "63555&fsmCID=".$cityID;
  }

  header("Location: /whzon/mytown/myTownEditor.php?wzID=".$sKey."&fscope=myCity&fwzUserID=".$wzUserID);
}
?>
