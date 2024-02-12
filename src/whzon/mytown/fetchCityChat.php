<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../JSON.php");

$cityID = safeGetINT('cityID');

$SQL = "select tblCity.name,tblCountry.name country,cityChanID,ownerID from tblCity  ";
$SQL .= "inner join tblCountry  on tblCountry.countryID = tblCity.countryID ";
$SQL .= "where cityID = ".$cityID;

$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
if($tRec){
  $chanID = $tRec['cityChanID'];
  $ownID  = $tRec['ownerID'];
  $cityName = $tRec['name'].', '.$tRec['country'];
  if (!$ownID){
    $ownID = 63555;
  }

  if (!$chanID) {
    //********* create new channel
    $title = $cityName;
    $guide = 'City Channel';
    $isPrivate = null;
    $spoken = 'All';

    $SQL = "select * from tblChatChannel where chanCityID=".$cityID." and chanType='City'";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $pvChan = 'null';
    $img = 'null';

    if (!$tRec){
      if ($isPrivate=="on"){
        $pvChan = 1;
      }
      $SQL = "INSERT INTO tblChatChannel (chanType,ownerID,chanCityID,name,guide,spoken,img,privateChan) ";
      $SQL .= "VALUES ('City',".$ownID.",".$cityID.",'".$title."','".$guide."','".$spoken."',null,".$pvChan.")";
      $result = mkyMsqry($SQL);
    }

    $SQL = "select channelID from tblChatChannel where chanCityID=".$cityID." and chanType='City'";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $newChanID=$tRec['channelID'];

    $SQL = "update tblCity set cityChanID = ".$newChanID." where cityID = ".$cityID;
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
