<?php
include_once('newsHLObjs.php');
$fsq = safeGET('fsq');
$mkey = new mkySess($sKey,$userID);
$webItem = new mkyActivityCard($mkey,$cityID,$scope,'mshare','Member Shares',$fsq);
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
<B><span class=wzBold style='color:darkKhaki;'>Member Shares Scope - <?php echo $scopeDisplay ?></span> - See Also </b>
<?php 
drawMyMenu($myMode,$modes);
$hcoUID = safeGetINT('ownID');
$spin = safeGET('spin');
?>
<form method='GET' action=''>
<input type='hidden' name='wzID'      value='<?php echo $sKey;?>'/>
<input ID='accQryStr' class='srchBox' type='text'  onkeydown="return (event.keyCode!=13);"
   name='fsq'   onfocus='this.value=""'  value='<?php echo $fsq;?>'
   placeholder='Member Shares' style='font-size:larger;font-weight:bold;width:55%;'/>

<input type='hidden' name='ownID'     value='<?php echo $hcoUID;?>'/>
<input type='hidden' name='fscope'    value='<?php echo $scope;?>'/>
<input type='hidden' name='franCID'   value='<?php echo $cityID;?>'/>
<input type='hidden' name='fmyMode'   value='mshare'/>
<input type='hidden' name='fwzUserID' value='<?php echo $userID;?>'/>
<input class='srchButton' type='button'  onclick='doAccQry(0,true,"mshare")' style='padding:.65em;vertical-align:top;' value=' Search '/>
<input class='srchButton' type='submit' style='padding:.65em;vertical-align:top;' name='spin' value=' Spin '/>
</form>

</div> 
<p>
<div ID='accQrySpot'></div>

<table width='100%' class='myTown'><tr valign='top'>
<td>

<?php 


if ($hcoUID) {
  $userSearch .= " and tblwzUser.wzUserID=".$hcoUID." ";
}

if ( $searchFlg == "" ) {
  $SQL = "SELECT country,activityID, acLink,acItemID, tblwzUser.wzUserID,firstname ";
  $SQL .= "from tblActivityFeed  ";
  if ($fsq){
    $SQL .= "inner join ( select count(*)nRes, objpItemID ";
    $SQL .= "from tblObjPreIndex  ";
    $SQL .= "inner join tblpreIndexCWords  on prcwWord = objpWord ";
    $SQL .= "where tblObjPreIndex.objpName='mshare' ".$fsqry;
    $SQL .= "group by  objpItemID ";
    $SQL .= ")R on R.objpItemID = acItemID ";
  }
  $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID=tblActivityFeed.wzUserID ";
  $SQL .= "inner join tblCity  on tblCity.cityId=tblwzUser.cityID ";
  $SQL .= "where tblActivityFeed.acCode = 18 and ".$userSearch." ";
  $SQL .= "order by ";
  if ($fsq){
    $SQL .= "R.nRes desc,acDate desc";
  }
  else {
    $SQL  .= "acDate desc";
  }
} 
else {
  $SQL = "SELECT country,activityID, acLink,acItemID, tblwzUser.wzUserID,firstname ";
  $SQL .= "from tblActivityFeed  ";
  $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID=tblActivityFeed.wzUserID ";
  $SQL .= "inner join tblCity  on tblCity.cityId=tblwzUser.cityID ";
  $SQL .= "where tblActivityFeed.acCode = 18 and ".$userSearch." ";
  $SQL .= "order by acDate desc";
}
if ($spin){
  $SQL = mkyStrIReplace('order by ','order by rand(), ',$SQL);
}
//$SQL = mkyStrReplace('tblObjPreIndex','ndxMShares.ndxObjPreIndex',$SQL);

if (isset($_GET['newPg'])){$pg = clean($_GET['newPg']);} else {$pg = 0;}
$nextPage = $pg;

$n = 0;
$i = 0;
$nRows = 12;

$nTop = $pg + $nRows;

$selTop = "Select Top ".$nTop." ";

$SQL .= ' limit '.$nTop;

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
  $acID     = $acRec['activityID'];

  $SQL  = "SELECT  urlLink, urlImgLink,urlTitle,urlDesc  FROM newsDirectory.tblUrlShares ";
  $SQL .= "where urlShareID = ".$acItemID;

  $myresult = mkyMyqry($SQL); //,$dbConNews);
  if ($myresult){$tRec = mkyMyFetch($myresult);} else { $tRec=null;}

  if ($tRec){
    $oimg = $tRec['urlImgLink'];
    if ($oimg == 'https://i.ytimg.com/vi//hqdefault.jpg'){
      $oimg = '0.jpg';
    }
    $margin  = "margin: 12px 20px 12px 0px;";
    $iwidth = 135;
    $URL = $tRec['urlLink'];
    $img = fixUTubeImg($URL,$oimg);
    if ($oimg == '0.jpg'){
      $SQL  = "update newsDirectory.tblUrlShares set  urlImgLink ='".$img."' ";
      $SQL .= "where urlShareID = ".$acItemID;
      $myres = mkyMyqry($SQL);
    }

    $imgW = '185px;';
    $imgM = '0em 1.5em 1.5em 0em;';
    if($sessISMOBILE){
      $imgW = '100%;';
      $imgM = '0em 0em 1em 0em;';
    }
    $newsImgStr = "";
    $newsImgStr = "<img ID='mshare".$acItemID."'onerror='swapFailedmshareImg(".$acItemID.")' ";
    $newsImgStr .= "style='float:left;border-radius:.5em;margin:".$imgM."width:".$imgW."' ";
    $newsImgStr .= "src='".$img."'>";
    if ($img == null || $img == ''){
      $newsImgStr = null;
    }


    $title = $tRec['urlTitle'];

    $story = shortenTextTo($tRec['urlDesc'],500);
    formatHashTags($story,$acID);

    echo "<div class='infoCardClear' style=''>";
    echo "<a href='javascript:wzLink(\"/whzon/".$mblp."mbr/mbrViewWNewsShare.php?wzID=".$sKey."&newsID=".$acItemID."\");'>";
    echo $newsImgStr."</a>";
    if ($sessISMOBILE){
      echo "<br clear='left'>";
    }
    echo "<b>".$name." </b>Has shared a web link<p/>".$title."<p>".$story."<br>";
  
    echo " <a href='javascript:wzLink(\"/whzon/".$mblp."mbr/mbrViewWNewsShare.php?wzID=".$sKey."&newsID=".$acItemID."\");'>";
    echo "View Full Story Here...</a><br/>";
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
echo getBigCubeAds('25px',2);

echo "</td></tr></table>";
echo "<p/>";



function showDigListing($digID){
    global $sKey,$mblp;
    global $userID;
    global $whzdom;
    global $i,$n;

    $SQL  = "SELECT  urlShareUID,urlLink, urlImgLink,urlTitle,urlDesc  FROM newsDirectory.tblUrlShares ";
    $SQL .= "where urlShareID = ".$digID;

    $myresult = mkyMyqry($SQL); //,$dbConNews);
    if ($myresult){$tRec = mkyMyFetch($myresult);} else { $tRec=null;}

    if ($tRec){
      $SQL = "select firstname from tblwzUser  where wzUserID = ".$tRec['urlShareUID'];
      $result = mkyMsqry($SQL);
      $acRec = mkyMsFetch($result);
      $name  = $acRec['firstname'];

      $oimg = $tRec['urlImgLink'];
      if ($oimg == 'https://i.ytimg.com/vi//hqdefault.jpg'){
        $oimg = '0.jpg';
      }
      $margin  = "margin: 12px 20px 12px 0px;";
      $iwidth = 135;
      $URL = $tRec['urlLink'];
      $img = fixUTubeImg($URL,$oimg);
      if ($oimg == '0.jpg'){
        $SQL  = "update newsDirectory.tblUrlShares set  urlImgLink ='".$img."' ";
        $SQL .= "where urlShareID = ".$acItemID;
        $myres = mkyMyqry($SQL);
      }

      $newsImgStr = "";
      $newsImgStr = "<img style='float:left;border-radius:.5em;margin:0em 0em 1em 0em;width:100%;' ";
      $newsImgStr .= " ID='mshare".$digID."'onerror='swapFailedmshareImg(".$digID.")' ";
      $newsImgStr .= "src='".$img."'>";
      if ($img == null || $img == ''){
        $newsImgStr = null;
      }


      $title = $tRec['urlTitle'];
      $story = left($tRec['urlDesc'],500);

      echo "<div class='infoCardClear' style=''>";
      echo "<a href='javascript:wzLink(\"/whzon/".$mblp."mbr/mbrViewWNewsShare.php?wzID=".$sKey."&newsID=".$digID."\");'>";
      echo $newsImgStr."</a>";
      echo "<b clear='left';>".$name." </b>Has shared a web link<p/>".$title."<p>".$story."<br>";
      echo " <a href='javascript:wzLink(\"/whzon/".$mblp."mbr/mbrViewWNewsShare.php?wzID=".$sKey."&newsID=".$digID."\");'>";
      echo "View Full Story Here...</a><br/>";
      echo "<br clear='left'/>";
      echo "</div>";
    }

    $i = $i + 1;
}
?> 
