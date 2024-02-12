<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../JSON.php");

$countryID = safeGetINT('countryID');

$SQL = "select name, countryChanID from tblCountry  ";
$SQL .= "where countryID = ".$countryID;

$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
if($tRec){
  $chanID = $tRec['countryChanID'];
  $ownID  = 63555;
  $countryName = $tRec['name'];
  if (!$ownID){
    $ownID = 63555;
  }

  if (!$chanID) {
    //********* create new channel
    $title = $countryName;
    $guide = 'Country Channel';
    $isPrivate = null;
    $spoken = 'All';

    $SQL = "select * from tblChatChannel where chanCityID=".$countryID." and chanType='Country'";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $pvChan = 'null';
    $img = 'null';

    if (!$tRec){
      if ($isPrivate=="on"){
        $pvChan = 1;
      }
      $SQL = "INSERT INTO tblChatChannel (chanType,ownerID,chanCityID,name,guide,spoken,img,privateChan) ";
      $SQL .= "VALUES ('Country',".$ownID.",".$countryID.",'".$title."','".$guide."','".$spoken."',null,".$pvChan.")";
      $result = mkyMsqry($SQL);
    }

    $SQL = "select channelID from tblChatChannel where chanCityID=".$countryID." and chanType='Country'";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $newChanID=$tRec['channelID'];

    $SQL = "update tblCountry set countryChanID = ".$newChanID." where countryID = ".$countryID;
    $result = mkyMsqry($SQL);
    doJson($newChanID);
  }
  else {
    doJson($chanID);
  }
  doJson(null);
}
doJson(0);



function doJson($chanID){
   $jHtm = '';
   if ($chanID === null || $chanID === 0){
     $jHtm = 'Sorry... Channel Not Available';
   }

   
   $j = new stdClass;

   $j->chanID     = $chanID;
   $j->htm        = mkyUrlEncode($jHtm);
   $jObj = json_encode($j);

   echo $jObj;
   exit('');
}
?>
