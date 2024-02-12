<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../utility/acHash.php");
include_once("../objPreIndexShares.php");

if ($userID!=0){
  $title = safeGET('fname');
  $guide = left(safeGET('fdesc'),250);
  $hTag  = safeGET('fhash');
  $callBck = safeGET('fcallbck');

  if ($hTag == ""){
    header("Location: ".$callBck."?wzID=".$sKey."&ferror=nohash");
    exit("");
  }
  if (hashFound($hTag)){
    header("Location: ".$callBck."?wzID=".$sKey."&ferror=hash");
    exit("");
  }

  if (isset($_GET['fpriv'])) {$isPrivate=safeGET('fpriv');} else {$isPrivate=null;}
  $spoken=clean($_GET['fspoken']);
  if ($title=="" || $title==null) {
    header("Location: ".$callBck."?wzID=".$sKey."&ferror=1");
    exit("");
  }

  $SQL = "select * from tblChatChannel where ownerID=".$userID." and name='".$title."'";
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  $pvChan = 'null';
  $img = 'null';

  if (!$tRec){
    if ($isPrivate=="on"){
      $pvChan = 1;
    }
    $SQL = "INSERT INTO tblChatChannel (ownerID,name,guide,spoken,img,privateChan) ";
    $SQL .= "VALUES (".$userID.",'".$title."','".$guide."','".$spoken."',null,".$pvChan.")";
    $result = mkyMsqry($SQL);
  
    $SQL = "select * from tblChatChannel where ownerID=".$userID." and name='".$title."'";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $newChanID=$tRec['channelID'];
    $acID = acNewChannel($userID,$newChanID,22);
    doPreIndexShare('chan',$acID,$newChanID,$userID);
    $acWord = mkyTrim(preg_replace('/\s+/', '', $hTag));
    $tag = '';

    if ($acWord != ''){
      $tag = "#".$acWord;
      $tag = mkyStrReplace(' ','',$tag);
      $tag = mkyStrReplace(',,','',$tag);
      $tag = setHashTag($tag,$acID);

      $SQL = "update tblActivityFeed set tags = '$tag' where activityID = ".$acID;
      $result = mkyMsqry($SQL);

      $SQL = "update tblChatChannel set chanType='HTag',chanHcoID = ".getChanHcoID($tag,$newChanID,$userID).",chanACID=".$acID." where channelID = ".$newChanID;
      $result = mkyMsqry($SQL);
    }
  }
}


function getChanHcoID($tag,$chanID,$userID){
  $tag = mkyStrReplace('#','',$tag);
  $SQL = "select hcoID from tblHashChanOwner where hcoHash = '".$tag."'";
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  if (!$tRec){
    $SQL = "insert into tblHashChanOwner (hcoHash,hcoDate,hcoChatChanID,hcoUID) ";
    $SQL .= "values('".$tag."',now(),".$chanID.",".$userID.")";
    $result = mkyMsqry($SQL);

    $SQL = "select hcoID from tblHashChanOwner where hcoChatChanID=".$chanID." and hcoUID=".$userID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    if (!$tRec){
      return 'null';
    }
  }
  return $tRec['hcoID'];
}
//------------------------------------------------------------------------------
function acNewChannel($inUserID,$acItemID,$acID){
//------------------------------------------------------------------------------
    $SQL = "select activityID from tblActivityFeed where acCode=".$acID." and acItemID=".$acItemID." and wzUserID=".$inUserID;
    $result = mkyMsqry($SQL);
    $tRec   = mkyMsFetch($result);
    if ($tRec){
      return $tRec['activityID'];
    }

    $SQL    = "select cityID from tblwzUser where wzUserID=".$inUserID;
    $result = mkyMsqry($SQL);
    $tRec   = mkyMsFetch($result);
    $cityID = $tRec['cityID'];
    $dstamp = mkySQLDstamp();

    $acUKey = getChannelKey();
    if ($acUKey == null){
      $SQL = "Insert into tblActivityFeed  (websiteID,wzUserID,acCode,acLink,acItemID,acCityID,acDate,acChanID) ";
      $SQL .= "Values (0,".$inUserID.",".$acID.",'',".$acItemID.",".$cityID.",'".$dstamp."',".$acItemID.")";
    }
    else{
      $SQL = "Insert into tblActivityFeed  (websiteID,wzUserID,acCode,acLink,acItemID,acCityID,acDate,acUKey,acChanID) ";
      $SQL .= "Values (0,".$inUserID.",".$acID.",'',".$acItemID.",".$cityID.",'".$dstamp."','".$acUKey."',".$acItemID.")";
    }
    $result = mkyMsqry($SQL);

    $SQL = "SELECT activityID FROM tblActivityFeed  Where wzUserID=".$inUserID." and acdate='".$dstamp."'";
    $result = mkyMsqry($SQL);
    $tRec   = mkyMsFetch($result);

    $acNewID = $tRec['activityID'];
    $SQL = "update tblChatChannel set chanACID=".$acNewID." WHERE channelID=".$acItemID;
    $result = mkyMsqry($SQL);
    return $acNewID;
}
?>
<!doctype html>
<html class="pgHTM" lang="en">
<head>
  <meta charset="utf-8">
<script>
  function cleanup(){
    //return;
    parent.wzGetNewChannel(<?php echo $newChanID;?>,'<?php echo mkyStrReplace('#','',$tag);?>');
    parent.wzAPI_closeWin();
  }
</script>
</head>
<body class='pgBody' onload='cleanup();'>
<A HREF=# onclick='javascript:parent.wzAPI_closeWin();'>[Done]</a>
</body>
</html>


