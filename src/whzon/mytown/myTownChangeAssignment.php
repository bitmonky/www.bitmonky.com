<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

  if (isset($_GET['fcityID'])){$cityID = clean($_GET['fcityID']);} else {$cityID = "";}
  if (isset($_GET['fcountryID'])){$countryID = clean($_GET['fcountryID']);} else {$countryID = "";}
  if (isset($_GET['fstateID'])){$stateID = clean($_GET['fstateID']);} else {$stateID = "";}

  if ($cityID != "" ) {
    changeCity($cityID,$sKey);
  }

  if ( $countryID != "" ) {
    changeCountry($countryID,$sKey);
  }
  
  if ($stateID != ""){
    changeState($stateID,$sKey);
  }
  
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

  $SQL = "select TOP 1 wzUserID from tblwzUser where cityID=".$cityID." and verified= 1 and imgflg=1 order by lastOnline desc";
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
