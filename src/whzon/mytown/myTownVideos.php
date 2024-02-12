<?php
include_once('newsHLObjs.php');
$fsq = safeGET('fsq');
$mkey = new mkySess($sKey,$userID);
$webItem = new mkyActivityCard($mkey,$cityID,$scope,'video','Video Shares',$fsq);
$fsqry = null;
$fsqry = $webItem->getQry();
?>
<script>
  function swapFailedmshareImg(id){
    var img = document.getElementById('mshare'+id);
    if (img){
      img.style.display  = 'none';
    }
  }
</script>
<p>
<div class='infoCardClear'>
<B><span class='wzBold' style='color:darkKhaki;'>Video Shares Scope - <?php echo $scopeDisplay ?></span> - See Also </b>
<?php 
drawMyMenu($myMode,$modes);
$spin = safeGET('spin');
?>
<div style='margin-top:.5em;'>
<form method='GET' action=''>
<input type='hidden' name='wzID'      value='<?php echo $sKey;?>'/>
 <input ID='accQryStr' class='srchBox' type='text'  onkeydown="return (event.keyCode!=13);"
  name='fsq'       value='<?php echo $fsq;?>' 
  placeholder='Video Shares' style='font-size:larger;font-weight:bold;width:55%;'/>
<input type='hidden' name='catID'     value='<?php echo $catID;?>'/>
<input type='hidden' name='fscope'    value='<?php echo $scope;?>'/>
<input type='hidden' name='franCID'   value='<?php echo $cityID;?>'/>
<input type='hidden' name='fmyMode'   value='video'/>
<input type='hidden' name='fwzUserID' value='<?php echo $userID;?>'/>
<input class='srchButton' type='button' style='padding:.65em;vertical-align:top;'  onclick='doAccQry(0,true,"video")'  value=' Search '/>
<input class='srchButton' type='submit' style='padding:.65em;vertical-align:top;' name='spin' value=' Spin '/>
</form>
</div>
</div> 
<p>
<div ID='accQrySpot'></div>

<table width='100%' class='myTown'><tr valign='top'>
<td>

<?php 
if (isset($_GET['fcatID'])){$catID = clean($_GET['fcatID']);} else {$catID = "";}
$catStr = "";
if ( $catID != "" ) {
  $catStr = " and tblClassifieds.classKey='".$catID."' ";
}

if ( $searchFlg == "" ) {
  $SQL = "SELECT country,activityID, acLink,acItemID, tblwzUser.wzUserID,firstname ";
  $SQL .= "from tblActivityFeed ";
  if ($fsq){
    $SQL .= "inner join ( select count(*)nRes, objpItemID ";
    $SQL .= "from tblObjPreIndex ";
    $SQL .= "inner join tblpreIndexCWords on prcwWord = objpWord ";
    $SQL .= "where tblObjPreIndex.objpName='video' ".$fsqry;
    $SQL .= "group by  objpItemID ";
    $SQL .= ")R on R.objpItemID = acItemID ";
  }
  $SQL .= "inner join tblwzVideo  on tblwzVideo.wzVideoID = acItemID ";
  $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID=tblActivityFeed.wzUserID ";
  $SQL .= "inner join tblCity  on tblCity.cityId=tblwzUser.cityID ";
  $SQL .= "where tblActivityFeed.acCode = 17 and ".$userSearch." ";
  $SQL .= "order by ";
  if ($fsq){
    $SQL .= "R.nRes desc,acDate desc; ";
  }
  else {
    $SQL  .= "acDate desc;";
  }
} 
else {
  $SQL = "SELECT country,activityID, acLink,acItemID, tblwzUser.wzUserID,firstname ";
  $SQL .= "from tblActivityFeed  ";
  $SQL .= "inner join tblwzVideo on tblwzVideo.wzVideoID = acItemID ";
  $SQL .= "inner join tblwzUser on tblwzUser.wzUserID=tblActivityFeed.wzUserID ";
  $SQL .= "inner join tblCity  on tblCity.cityId=tblwzUser.cityID ";
  $SQL .= "where tblActivityFeed.acCode = 17 and ".$userSearch." ";
  $SQL .= "order by acDate desc;";
}
if ($spin){
  $SQL = mkyStrIReplace('order by ','order by rand(), ',$SQL);
}

if (isset($_GET['newPg'])){$pg = clean($_GET['newPg']);} else {$pg = 0;}
$nextPage = $pg;

$n = 0;
$i = 0;
$nRows = 12;

$nTop = $pg + $nRows;

$selTop = " desc limit ".$nTop." ";
$SQL = mkyStrReplace(" desc;",$selTop,$SQL);
//echo $SQL;
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
$appName = "myTown.php";
if ($digID){
  showDigListing($digID);
}
while ($acRec && $i < $nRows){
  $acItemID = $acRec['acItemID'];
  $name     = $acRec['firstname'];

  $SQL = "SELECT wzUserID,vTitle, vDesc, vidURL from tblwzVideo where wzVideoID=".$acItemID;
  $vresult = mkyMsqry($SQL);
  $tRec = mkyMsFetch($vresult);
  $fresult = 0;
  $img = "//image.bitmonky.com/img/monkyTalkfbCard.png";
  $pUID = $tRec['wzUserID'];
  if ($tRec){
    $img = $tRec['vidURL'];
    $img = preg_replace('/.*youtube.com/i','youtube.com',$img);
    $img = mkyStrIReplace("www.youtube.com/v","//i2.ytimg.com/vi",$img);
    $img = mkyStrIReplace("youtube.com/v","//i2.ytimg.com/vi",$img);
    $img = $img."/hqdefault.jpg";

    $newsImgStr = "";
    $newsImgStr = "<img ID='mshare".$acItemID."'onerror='swapFailedmshareImg(".$acItemID.")' ";
    $newsImgStr .= "style='float:left;border-radius:.5em;margin:0em 1.5em 1.5em 0em;width:135px;' ";
    $newsImgStr .= "src='".$img."'>";
    if ($img == null || $img == ''){
      $newsImgStr = null;
    }

    $title = utf8str($tRec['vTitle']);
    //$title = stripslashes($tRec['vTitle']);
    $story = splitLWords(left(utf8str($tRec['vDesc']),250));

    echo "<div class='infoCardClear' style=''>";
    echo "<a href='javascript:wzLink(\"/whzon/".$mblp."mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$pUID."\");'>";
    echo "<img style='float:right;border-radius:50%;margin-left:1.5em;height:4em;' ";
    echo "src='".$GLOBALS['MKYC_imgsrv']."/getMbrImg.php?id=".$pUID."'/></a>";
    echo "<a href=\"javascript:parent.wzGetVideoPage('/whzon/mbr/vidView/viewVideoPg.php?wzID=".$sKey."&videoID=".$acItemID."');\">";
    echo $newsImgStr."</a>";
    echo "<b>".$name." </b>Has shared YouTube Video<p/>".$title."<p>".$story."<br>";
  
    echo "<a href=\"javascript:parent.wzGetVideoPage('/whzon/mbr/vidView/viewVideoPg.php?wzID=".$sKey."&videoID=".$acItemID."');\">";
    echo "Watch Video Here...</a><br/>";
    echo "<br clear='left'/>";
    echo "</div>";
  } 
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
//echo getBigCubeAds('25px',2);

echo "</td></tr></table>";
echo "<p/>";



function showDigListing($digID){
    global $sKey,$mblp;
    global $userID;
    global $whzdom;
    global $i,$n;

    $acItemID = $digID;
    $SQL = "SELECT vTitle, firstname,vDesc, vidURL from tblwzVideo ";
    $SQL .= "inner join tblwzUser on tblwzUser.wzUserID = tblwzVideo.wzUserID ";
    $SQL .= "where wzVideoID=".$acItemID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $fresult = 0;
    $img = "//image.bitmonky.com/img/monkyTalkfbCard.png";
    if ($tRec){
      $name = $tRec['firstname'];
      $img = $tRec['vidURL'];
      $img = preg_replace('/.*youtube.com/i','youtube.com',$img);
      $img = mkyStrIReplace("www.youtube.com/v","//i2.ytimg.com/vi",$img);
      $img = mkyStrIReplace("youtube.com/v","//i2.ytimg.com/vi",$img);
      $img = $img."/hqdefault.jpg";

      $newsImgStr = "";
      $newsImgStr = "<img ID='mshare".$acItemID."'onerror='swapFailedmshareImg(".$acItemID.")' ";
      $newsImgStr .= "style='float:left;border-radius:.5em;margin:0em 1.5em 1.5em 0em;width:100%;' ";
      $newsImgStr .= "src='".$img."'>";
      if ($img == null || $img == ''){
        $newsImgStr = null;
      }


      $title = $tRec['vTitle'];
      $story = splitLWords(left($tRec['vDesc'],250));

      echo "<div class='infoCardClear' style=''>";
      echo "<a href=\"javascript:parent.wzGetVideoPage('/whzon/mbr/vidView/viewVideoPg.php?wzID=".$sKey."&videoID=".$acItemID."');\">";
      echo $newsImgStr."</a>";
      echo "<b>".$name." </b>Has shared YouTube Video<p/>".$title."<p>".$story."<br>";

      echo "<a href=\"javascript:parent.wzGetVideoPage('/whzon/mbr/vidView/viewVideoPg.php?wzID=".$sKey."&videoID=".$acItemID."');\">";
      echo "Watch Video Here...</a><br/>";
      echo "<br clear='left'/>";
      echo "</div>";
    }
    $i = $i + 1;
}
?> 
