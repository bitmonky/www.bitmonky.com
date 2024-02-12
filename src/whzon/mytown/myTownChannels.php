<?php
include_once('newsHLObjs.php');
$fsq = safeGET('fsq');
$mkey = new mkySess($sKey,$userID);
$webItem = new mkyActivityCard($mkey,$cityID,$scope,'chan','Member Channels',$fsq);
$fsqry = null;
$fsqry = $webItem->getQry();
$spin = safeGET('spin');
?>
<script>
  function swapFailedmshareImg(id){
    var img = document.getElementById('mshare'+id);
    if (img){
      img.style.display  = 'none';
    }
  }
<?php 
if ($sessISMOBILE){
  ?>
  function wzPopADDCh(){
    parent.wzAPI_showFrame("/whzon/talk/frmAddChannel.php?wzID=" + parent.sID,parent.maxw,1850,0,0);
    parent.window.scrollTo(0,0);
  }
  <?php
}
else {
  ?>
  function wzPopADDCh(){
    parent.wzAPI_showFrame("/whzon/talk/frmAddChannel.php?wzID=" + parent.sID,400,300,50,100);
    parent.window.scrollTo(0,0);
  }
  <?php
}
?>
function searchByOwnerName(){
  var sval = document.getElementById('chSVal');
  if (sval){
    owner = sval.value;
    document.location.href = 'myTown.php?wzID=<?php echo $sKey."&fwzUserID=".$userID;?>&myMode=chan&fscope=myWorld&oName=' + encodeURIComponent(owner);
  }
}

</script>
<p>
<div class='infoCardClear'>
<B><span class='wzBold' style='color:darkKhaki;'>Member Channels Scope - <?php echo $scopeDisplay ?></span> - See Also </b>
<?php 
drawMyMenu($myMode,$modes);
$hcoName = safeGET('oName');
$hcoUID = safeGetINT('ownID');
if ($hcoName){
  $SQL = "select wzUserID from tblwzUser  where firstname = '".$hcoName."'";
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  if ($tRec){
    $hcoUID = $tRec['wzUserID'];
  }
}
?>
<div style='margin-top:.5em;'>
<form ID='chanSearchFrm' method='GET' action=''>
<input type='hidden' name='wzID'      value='<?php echo $sKey;?>'/>
<input ID='accQryStr' class='srchBox' type='text'  onkeydown="return (event.keyCode!=13);"
   name='fsq'   onfocus='this.value=""'  value='<?php echo $fsq;?>'
   placeholder='Member Channels' style='font-size:larger;font-weight:bold;width:55%;'/>

<input type='hidden' name='ownID'     value='<?php echo $hcoUID;?>'/>
<input type='hidden' name='fscope'    value='<?php echo $scope;?>'/>
<input type='hidden' name='franCID'   value='<?php echo $cityID;?>'/>
<input type='hidden' name='fmyMode'   value='chan'/>
<input type='hidden' name='fwzUserID' value='<?php echo $userID;?>'/>
<input class='srchButton' type='button' onclick='doAccQry(0,true,"chan")' style='padding:.65em;vertical-align:top;' value=' Search '/>
<input type='button' value=' By Owner' style='padding:.65em;vertical-align:top;' onclick='searchByOwnerName()'>
<input class='srchButton' type='submit' style='padding:.65em;vertical-align:top;' name='spin' value=' Spin '/>
<?php
$bval = 'Create New Channel';
if ($sessISMOBILE){
  $bval = 'Create';
}
if ($userID != 0 && $sessISMOBILE){
  echo "<input type='button' value=' ".$bval." ' onclick='wzPopADDCh();'/>";
}
?>
</form>
</div>
</div> 
<p>
<div ID='accQrySpot'></div>

<table width='100%' class='myTown'><tr valign='top'>
<td>

<?php 
if ($hcoUID){
  $userSearch .= " and tblwzUser.wzUserID = ".$hcoUID." ";
}
if ( $searchFlg == "" ) {
  $SQL  = "Select hcoSalePrice,hcoSaleStatus,TIMESTAMPDIFF(day,date(tblwzUser.lastOnline),date(now()))nDays,";
  $SQL .= "sandbox,tblwzUser.wzUserID,firstname,storeBanID,tblChatChannel.channelID,tblwzUser.cityID,";
  $SQL .= "tblChatChannel.name,guide,hcoHash ";
  $SQL .= "from tblChatChannel  ";
  if ($fsq){
    $SQL .= "inner join ( select count(*)nRes, objpItemID ";
    $SQL .= "from tblObjPreIndex  ";
    $SQL .= "inner join tblpreIndexCWords  on prcwWord = objpWord ";
    $SQL .= "where tblObjPreIndex.objpName='chan' ".$fsqry;
    $SQL .= "group by  objpItemID ";
    $SQL .= ")R on R.objpItemID = tblChatChannel.channelID ";
  }
  $SQL .= "inner join tblHashChanOwner  on tblChatChannel.channelID = hcoChatChanID ";
  $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID = tblChatChannel.ownerID ";
  $SQL .= "left join tblCity  on tblCity.cityID = tblwzUser.cityID ";
  $SQL .= "where not storeBanID is null and ".$userSearch." ";
  $SQL .= "order by ";
  if ($fsq){
    $SQL .= "R.nRes desc,tblChatChannel.name; ";
  }
  else {
    $SQL  .= "tblChatChannel.name;";
  }
} 
if ($spin){
  $SQL = mkyStrIReplace('order by ','order by rand(), ',$SQL);
}
//if ($userID == 17621){echo $SQL;}
if (isset($_GET['newPg'])){$pg = clean($_GET['newPg']);} else {$pg = 0;}
$nextPage = $pg;

$n = 0;
$i = 0;
$nRows = 12;

$nTop = $pg + $nRows;

$selTop = "Select Top ".$nTop." ";

$SQL = mkyStrReplace("SELECT",$selTop,$SQL);

$acRec = null;
$result = mkyMsqry($SQL);
$acRec = mkyMsFetch($result);


$p = 0;
while ($acRec && $p < $nextPage) {
  $acRec = mkyMsFetch($result);
  $p = $p + 1;
}
echo "<p/>";
$mblp = null;
if ($sessISMOBILE){
  $mblp = 'mblp/';
}
$link = $linkRoot."&fsq=".mkyUrlEncode($fsq);
if ($hcoUID){
  $link .= "&ownID=".$hcoUID;
  if ($acRec){
    echo "<div class='infoCardClear'>";
    echo "<h3>Viewing All Channels Owned By ".$acRec['firstname']."</h3>";
    echo "</div>";
  }
}
$appName = "myTown.php";
if ($digID){
  showDigListing($digID);
}
while ($acRec && $i < $nRows){
  $name       = $acRec['firstname'];
  $storeBanID = $acRec['storeBanID'];
  $hashstr    = mkyStrReplace('#','',$acRec['hcoHash']);
  $ownID      = $acRec['wzUserID'];
  $lol        = $acRec['nDays'];
  $inbox      = $acRec['sandbox'];
  $claimStatus = null;
  if ($lol > $GLOBALS['MKYC_chanExpire'] || $inbox){
    $claimStatus = "TagMiner Claim: <span style='color:fireBrick;'>Expired</span></br>";
  }
  else {
    $claimStatus = "TagMiner Claim: <span style='color:darkKhaki;'>VALID</span></br>";
  }

  $img = "//image.bitmonky.com/img/monkyTalkfbCard.png";
  $img = "https://image.bitmonky.com/getStoreBGTmn.php?id=".$storeBanID;

  $newsImgStr = "";
  $newsImgStr = "<img ID='mshare".$storeBanID."'onerror='swapFailedmshareImg(".$storeBanID.")' ";
  $newsImgStr .= "style='float:right;border-radius:.5em;margin:0em 0em 1.5em 1.5em 0em;width:180px;' ";
  $newsImgStr .= "src='".$img."'>";
  if ($storeBanID == null || $img == ''){
    $newsImgStr = null;
  }


  $title = $acRec['name'];
  $story = left($acRec['guide'],500);

  echo "<div class='infoCardClear' style='background:#333333;border-radius:.5em .5em 0em 0em;margin-bottom:0em;'>";
  echo "<h2>".$title."</h2>";
  echo "<a href='javascript:wzLink(\"/whzon/public/homepg.php?noNews=on&wzID=".$sKey."&fhQry=".$hashstr."\");'>";
  echo $newsImgStr."</a>";
  echo "<b>Owner: <span style='color:darkKhaki;'>".$name." </span></b><p>".$story."<br>";
  
  echo "<a href='javascript:wzLink(\"/whzon/public/homepg.php?noNews=on&wzID=".$sKey."&fhQry=".$hashstr."\");'>";
  echo "Visit The Channel Here...</a>";
  echo "<br clear='right'/>";
  echo "</div>";
  if (!$hcoUID){
    echo "<div class='infoCardClear' align='right' style='border-radius:0em 0em;background:black;margin:bottom:0em;'>";
    echo $claimStatus;
    echo "<a href='javascript:wzLink(\"".$appName."?wzID=".$sKey."&fwzUserID=".$userID."&myMode=chan&fscope=myWorld&ownID=".$ownID."\");'/>";
    echo "Sea All Channels By ".$name."</a></div>";
  }
  echo "</div>";
 
  $acRec = mkyMsFetch($result);
  $i = $i + 1;
  $n = $n  + 1;
}

echo "<div style='margin-top:2em;'>";
if ($i > 0) {
  echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage + $nRows)."\");'>Next</a>";
}
if ($nextPage > 0) {
  echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage - $nRows)."\");'>Back</a>";
}
echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=0\");'>Top</a>";
echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&spin=spin&newPg=0\");'>Spin Again</a>";
echo "</div>";

if (!$sessISMOBILE){
  echo "</td>";
  echo "<td style='width:350px;padding-left:25px;'>";
}
else {
  echo "<p/>";
}
if ($userID > 0){
$SQL  = "Select tblwzUser.wzUserID,firstname,rCode,storeBanID,tblChatChannel.channelID,tblwzUser.cityID,tblChatChannel.name,guide,hcoHash ";
$SQL .= "from tblHashChanOwner  ";
$SQL .= "left join tblChatChannel  on tblChatChannel.channelID = hcoChatChanID ";
$SQL .= "inner join tblwzUser  on tblwzUser.wzUserID = hcoUID ";
$SQL .= "left join tblCity  on tblCity.cityID = tblwzUser.cityID ";
$SQL .= "where hcoUID = ".$userID." ";
$SQL .= "order by storeBanID desc, tblChatChannel.name";

$result = mkyMsqry($SQL);
$acRec = mkyMsFetch($result);

$rCode     = $acRec['rCode'];

if ($rCode == 'virtualP' || $userID==17621){
  showMyChans();
}

echo "<div class='infoCardClear'>";
if (!$sessISMOBILE){
  if ($userID != 0){
   echo "<div align='right'>";	  
   echo "<input type='button' value=' ".$bval." ' onclick='wzPopADDCh();'/>";
   echo "</div>";
  }
}  
echo "<h3>All Channels Owned By Me</h3>";
if (!$acRec){
  echo "You have not created any other channels ...";
}
$nRows = 100;
$i = 0;
while ($acRec && $i < $nRows){
  $name       = $acRec['firstname'];
  $storeBanID = $acRec['storeBanID'];
  $hashstr    = mkyStrReplace('#','',$acRec['hcoHash']);
  $ownID      = $acRec['wzUserID'];

  $img = "//image.bitmonky.com/img/monkyTalkfbCard.png";
  $img = "https://image.bitmonky.com/getStoreBGTmn.php?id=".$storeBanID;

  $newsImgStr = "";
  $newsImgStr = "<img ID='mshare".$storeBanID."'onerror='swapFailedmshareImg(".$storeBanID.")' ";
  $newsImgStr .= "style='float:left;border-radius:.5em;margin:0em 1.5em 1.5em 0em;width:60px;' ";
  $newsImgStr .= "src='".$img."'>";
  if ($storeBanID == null || $img == ''){
    $newsImgStr = null;
  }


  $title = $acRec['name'];
  $story = left($acRec['guide'],500);
  if (!$title){
    $title = "#".$acRec['hcoHash'];
  }
  echo "<div class='infoCardClear' style='background:#222222;'>";
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
}
echo getBigCubeAds('25px',2);
echo "</td></tr></table>";
echo "<p/>";


function showMyChans(){
  global $sKey;    
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

function showDigListing($digID){
    global $sKey,$mblp;
    global $userID;
    global $whzdom;
    global $i,$n;

    $SQL  = "Select firstname,storeBanID,tblChatChannel.channelID,tblwzUser.cityID,tblChatChannel.name,guide,hcoHash ";
    $SQL .= "from tblChatChannel  ";
    $SQL .= "inner join tblHashChanOwner  on tblChatChannel.channelID = hcoChatChanID ";
    $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID = tblChatChannel.ownerID ";
    $SQL .= "where tblChatChannel.channelID = ".$digID;
  
    $result = mkyMsqry($SQL);
    $acRec = mkyMsFetch($result);

    if ($acRec){
      $name       = $acRec['firstname'];
      $storeBanID = $acRec['storeBanID'];
      $hashstr    = mkyStrReplace('#','',$acRec['hcoHash']);

      $img = "//image.bitmonky.com/img/monkyTalkfbCard.png";
      $img = "https://image.bitmonky.com/getStoreBGTmn.php?id=".$storeBanID;

      $newsImgStr = "";
      $newsImgStr = "<img ID='mshare".$storeBanID."'onerror='swapFailedmshareImg(".$storeBanID.")' ";
      $newsImgStr .= "style='float:left;border-radius:.5em;margin:0em 1.5em 1.5em 0em;width:135px;' ";
      $newsImgStr .= "src='".$img."'>";
      if ($storeBanID == null || $img == ''){
        $newsImgStr = null;
      }

      $title = $acRec['name'];
      $story = left($acRec['guide'],500);

      echo "<div class='infoCardClear' style=''>";
      echo "<a href='javascript:wzLink(\"/whzon/public/homepg.php?noNews=on&wzID=".$sKey."&fhQry=".$hashstr."\");'>";
      echo $newsImgStr."</a>";
      echo "<b>".$name." </b>Created This Channel<p/>".$title."<p>".$story."<br>";

      echo "<a href='javascript:wzLink(\"/whzon/public/homepg.php?noNews=on&wzID=".$sKey."&fhQry=".$hashstr."\");'>";
      echo "Visit The Channel Here...</a><br/>";
      echo "<br clear='left'/>";
      echo "</div>";
    }
    $i = $i + 1;
}
?> 
