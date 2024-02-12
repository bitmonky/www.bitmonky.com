<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../JSON.php");

$stateID = safeGetINT('stateID');

$SQL = "select tblState.name,tblCountry.name country,stateChanID from tblState  ";
$SQL .= "inner join tblCountry  on tblCountry.countryID = tblState.countryID ";
$SQL .= "where stateID = ".$stateID;

$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
if($tRec){
  $chanID = $tRec['stateChanID'];
  $ownID  = 63555;
  $stateName = $tRec['name'].', '.$tRec['country'];
  if (!$ownID){
    $ownID = 63555;
  }

  if (!$chanID) {
    //********* create new channel
    $title = $stateName;
    $guide = 'State/Province Channel';
    $isPrivate = null;
    $spoken = 'All';

    $SQL = "select * from tblChatChannel where chanCityID=".$stateID." and chanType='State'";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $pvChan = 'null';
    $img = 'null';

    if (!$tRec){
      if ($isPrivate=="on"){
        $pvChan = 1;
      }
      $SQL = "INSERT INTO tblChatChannel (chanType,ownerID,chanCityID,name,guide,spoken,img,privateChan) ";
      $SQL .= "VALUES ('State',".$ownID.",".$stateID.",'".$title."','".$guide."','".$spoken."',null,".$pvChan.")";
      $result = mkyMsqry($SQL);
    }

    $SQL = "select channelID from tblChatChannel where chanCityID=".$stateID." and chanType='State'";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $newChanID=$tRec['channelID'];

    $SQL = "update tblState set stateChanID = ".$newChanID." where stateID = ".$stateID;
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
