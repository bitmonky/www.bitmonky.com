<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
$mobile = null;
$wzUserID = safeGET('fwzUserID');

$mbld = null;
if ($sessISMOBILE){
  $mbld = 'mblp/';
}
$SQL  = "select firstname,rCode from tblwzUser where wzUserID = ".$wzUserID;
$result = mkyMsqry($SQL);
$acRec = mkyMsFetch($result);
$firstname = $acRec['firstname'];
$rCode     = $acRec['rCode'];

if ($rCode == 'virtualP' || $userID==17621){
  showMyChans();
}
$SQL  = "Select tblwzUser.wzUserID,firstname,storeBanID,tblChatChannel.channelID,tblwzUser.cityID,tblChatChannel.name,guide,hcoHash ";
$SQL .= "from tblHashChanOwner  ";
$SQL .= "left join tblChatChannel  on tblChatChannel.channelID = hcoChatChanID ";
$SQL .= "inner join tblwzUser  on tblwzUser.wzUserID = hcoUID ";
$SQL .= "left join tblCity  on tblCity.cityID = tblwzUser.cityID ";
$SQL .= "where hcoUID = ".$wzUserID." ";
$SQL .= "order by storeBanID desc, tblChatChannel.name";

$result = mkyMsqry($SQL);
$acRec = mkyMsFetch($result);

echo "<div>";
if ($userID == $wzUserID){
  echo "<div align='right'>";
  echo "<input type='button' value=' Create Channel ' onclick='wzPopADDCh();'/>";
  echo "</div>";
}
echo "<h3>All Channels Owned By ".$firstname."</h3>";
if (!$acRec){
  echo $firstname." has not created any other channels ...";
}
$pg = safeGET('newPg');
$nextPage = $pg;

$i = 0;
$nRows = 15;

$link = "?wzID=".$sKey;
$appName = "myFriends.php";

$nTop = $pg + $nRows;

$selTop = "Select Top ".$nTop." ";

$SQL = mkyStrReplace("SELECT",$selTop,$SQL);

$tRec = null;
$result = mkyMsqry($SQL);
$acRec = mkyMsFetch($result);

$cpage = 0;
while($acRec && $cpage < $nextPage) {
  $acRec = mkyMsFetch($result);
  $cpage = $cpage + 1;
}

while ($acRec && $i < $nRows){
  $name       = $acRec['firstname'];
  $storeBanID = $acRec['storeBanID'];
  $hashstr    = mkyStrReplace('#','',$acRec['hcoHash']);
  $ownID      = $acRec['wzUserID'];

  $img = "//image.bitmonky.com/img/monkyTalkfbCard.png";
  $img = "https://image.bitmonky.com/getStoreBGTmn.php?id=".$storeBanID;

  $newsImgStr = "";
  $newsImgStr = "<img ID='mshare".$storeBanID."'onerror='swapFailedmshareImg(".$storeBanID.")' ";
  $newsImgStr .= "style='float:left;border-radius:.5em;margin:0em 1.5em 1.5em 0em;width:140px;' ";
  $newsImgStr .= "src='".$img."'>";
  if ($storeBanID == null || $img == ''){
    $newsImgStr = null;
  }


  $title = $acRec['name'];
  $story = left($acRec['guide'],500);
  if (!$title){
    $title = "#".$acRec['hcoHash'];
  }
  echo "<div class='infoCardClear' style=''>";
  echo "<a href='javascript:wzLink(\"/whzon/public/homepg.php?noNews=on&wzID=".$sKey."&fhQry=".$hashstr."\");'>";
  echo $newsImgStr."</a>";
  echo $title."<p>".$story."<br clear='left'/>";
  echo "<div align='right'>";
  echo "<a href='javascript:wzLink(\"/whzon/public/homepg.php?noNews=on&wzID=".$sKey."&fhQry=".$hashstr."\");'>";
  echo "View Channel</a>";
  echo "</div>";
  echo "</div>";

  $acRec = mkyMsFetch($result);
  $i = $i + 1;
}
echo "</div>";
if ($i > 0){
  echo "<p><a href='javascript:getChannelMgr(".($nextPage + $nRows).");'>Next</a>";
}  
if ($nextPage > 0 ) {
  echo " | <a href='javascript:getChannelMgr(".($nextPage - $nRows).");'>Back</a>";
}
echo " | <a href='javascript:getChannelMgr(0);'>Top</a>";

function showMyChans(){
  echo "<h3>Channels I Belong To</h3>";
  $SQL  = "Select tblwzUser.wzUserID,firstname,storeBanID,tblChatChannel.channelID,tblwzUser.cityID,tblChatChannel.name,guide,hcoHash ";
  $SQL .= "from tblHashChanOwner  ";
  $SQL .= "inner join tblChanSubscribe on csubChanID = hcoChatChanID ";
  $SQL .= "left join tblChatChannel  on tblChatChannel.channelID = hcoChatChanID ";
  $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID = hcoUID ";
  $SQL .= "left join tblCity  on tblCity.cityID = tblwzUser.cityID ";
  $SQL .= "where csubUID = ".$GLOBALS['userID']." ";
  $SQL .= "order by storeBanID desc, tblChatChannel.name limit 15";

  $result = mkyMsqry($SQL);
  $acRec = mkyMsFetch($result);
  while ($acRec){
    $name       = $acRec['firstname'];
    $storeBanID = $acRec['storeBanID'];
    $hashstr    = mkyStrReplace('#','',$acRec['hcoHash']);
    $ownID      = $acRec['wzUserID'];

    $img = "//image.bitmonky.com/img/monkyTalkfbCard.png";
    $img = "https://image.bitmonky.com/getStoreBGTmn.php?id=".$storeBanID;

    $newsImgStr = "";
    $newsImgStr = "<img ID='mshare".$storeBanID."'onerror='swapFailedmshareImg(".$storeBanID.")' ";
    $newsImgStr .= "style='float:left;border-radius:.5em;margin:0em 1.5em 1.5em 0em;width:140px;' ";
    $newsImgStr .= "src='".$img."'>";
    if ($storeBanID == null || $img == ''){
      $newsImgStr = null;
    }

    $title = $acRec['name'];
    $story = left($acRec['guide'],500);
    if (!$title){
      $title = "#".$acRec['hcoHash'];
    }
    echo "<div class='infoCardClear' style=''>";
    echo "<a href='javascript:wzLink(\"/whzon/public/homepg.php?noNews=on&wzID=".$sKey."&fhQry=".$hashstr."\");'>";
    echo $newsImgStr."</a>";
    echo $title."<p>".$story."<br clear='left'/>";
    echo "<div align='right'>";
    echo "<a href='javascript:wzLink(\"/whzon/public/homepg.php?noNews=on&wzID=".$sKey."&fhQry=".$hashstr."\");'>";
    echo "View Channel</a>";
    echo "</div>";
    echo "</div>";
    $acRec = mkyMsFetch($result);
  }
}
?>
</div>

