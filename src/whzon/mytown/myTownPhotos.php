<?php
include_once('newsHLObjs.php');
$fsq = safeGET('fsq');
$rdot = null;
if ($fsq){
  $rdot = ",R.nRes ";
}
$mkey = new mkySess($sKey,$userID);
$webItem = new mkyActivityCard($mkey,$cityID,$scope,'mshare','Member Shares',$fsq);
$fsqry = null;
$fsqry = $webItem->getQry();
?> 
<p>
<div class='infoCardClear'>
<B><span style='color:darkKhaki;'>Photo Albums Scope - <?php echo $scopeDisplay ?></span> - See Also</b>
<?php drawMyMenu($myMode,$modes);?>
  <div style='margin-top:.5em;'>
  <form method='GET' action=''>
  <input type='hidden' name='wzID'      value='<?php echo $sKey;?>'/>
  <input ID='accQryStr' class='srchBox' type='text'  onkeydown="return (event.keyCode!=13);"
   name='fsq'   onfocus='this.value=""'  value='<?php echo $fsq;?>'
   placeholder='Member Photo Albums' style='font-size:larger;font-weight:bold;width:55%;'/>
  <input type='hidden' name='catID'     value='<?php echo $catID;?>'/>
  <input type='hidden' name='fscope'    value='<?php echo $scope;?>'/>
  <input type='hidden' name='franCID'   value='<?php echo $cityID;?>'/>
  <input type='hidden' name='fmyMode'   value='photo'/>
  <input type='hidden' name='fwzUserID' value='<?php echo $userID;?>'/>
  <input class='srchButton' type='button' onclick='doAccQry(0,true,"photo")' style='padding:.65em;vertical-align:top;' value=' Search '/>
  </form>
  </div>
</div>
<p>
<div ID='accQrySpot'></div>
<table width='100%' class='myTown'><tr valign='top'>
<td>

<?php 
if ( $userID == 0 ) {
  $phPrivacey = " privacy < 1 ";
} 
else {
  $phPrivacey = " privacy < 2 ";
}

$phPrivacey  = $phPrivacey." and sandBox is null and isMkdDating is null ";
  $fsqry = mkyStrReplace('tblwzPhoto.title','tblObjPreIndex.objpWord',$fsqry);


  $SQL = "SELECT tblwzPhotoAlbum.wzPhotoAlbumID,tblwzPhotoAlbum.wzUserID,tblwzPhotoAlbum.rdate,tblwzPhotoAlbum.description,tblwzPhotoAlbum.name, ";
  $SQL .= "imgFlg, tblCity.name as cityName, tblCity.countryCD as countryName FROM ";
  $SQL .= "tblwzPhotoAlbum  ";
  $SQL .= "inner join tblwzPhoto  on tblwzPhotoAlbum.wzPhotoAlbumID = tblwzPhoto.wzPhotoAlbumID ";
  if ($fsq){
    $SQL .= "inner join ( select count(*)nRes, objpItemID ";
    $SQL .= "from tblObjPreIndex  ";
    $SQL .= "inner join tblpreIndexCWords  on prcwWord = objpWord ";
    $SQL .= "where tblObjPreIndex.objpName='photo' ".$fsqry;
    $SQL .= "group by  objpItemID ";
    $SQL .= ")R on R.objpItemID = photoID ";
  }
  $SQL .= "inner join tblwzUser on tblwzUser.wzUserID=tblwzPhotoAlbum.wzUserID ";
  $SQL .= "inner join tblCity  on tblCity.cityID=tblwzUser.cityID  where ".$phPrivacey." and ".$userSearch; 
  $SQL .= "group by tblwzPhotoAlbum.wzPhotoAlbumID,tblwzPhotoAlbum.wzUserID".$rdot.",tblwzPhotoAlbum.rdate,tblwzPhotoAlbum.description, ";
  $SQL .= "tblwzPhotoAlbum.name, imgFlg, tblCity.name, tblCity.countryCD, lastOnline ";
  if ($fsq){
    $SQL .= "order by R.nRes desc ";
  }
  else {
    $SQL .= "order by rand()";
  }
 

$tRec = null;
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);


if (isset($_GET['newPg'])){$pg = clean($_GET['newPg']);} else {$pg = 0;}
$nextPage = $pg;
$n        = $pg + 1;

$cpage = 0;
while($tRec && $cpage < $nextPage) {
  $tRec = mkyMsFetch($result);
  $cpage = $cpage + 1;
}

echo "<p><table class='docTable' style='width:100%;' >";
?>
<tr valign=top>
  <td width='38' style="background-image: url('');" colspan=1><font color='white'><b>Owner</b></font></td>
  <td style="background-image: url('');" colspan="2"><font color='white'><b>Ablum</b></font></td>
  <td style="background-image: url('');" width='70' align='right'><font color='white'><b>Post Date</b></font></td>
  <td style="background-image: url('');" width='70' align='right'><font color='white'><b>Location</b></font></td>
  </tr>

<script>
<!--
function openComments(URL,wsID){
  winName = "wzCom" + wsID;
  //var win2 = window.open(URL,winName,"target=new,width=740,height=600,resizable=yes,scrollbars=yes'];
}
//-->
</SCRIPT>
<?php 

$i = 0;
$n = 0;
$nRows = 10;
$link = $linkRoot."&fsq=".$fsq;
$appName = "myTown.php";
if ($digID){
  showDigListing($digID);
}
while ($tRec && $n < $nRows){ 
  $sAnkor = "<a href='javascript:wzLink(\"/whzon/mbr/mbrProfile.php?franCID=".$mtFranCID."&wzID=".$sKey."&fwzUserID=".$tRec['wzUserID'];
  $sAnkor .= "&feventID=".$tRec['wzPhotoAlbumID']."\");');>"; 

  $wsImgStr = "";
  if ($tRec['imgFlg'] == 1 ) {
    $wsAnkor = "";
    $wsImgStr  = $wsAnkor."<img style='border-radius:50%;float: left;margin-bottom: 3px; margin-top: 3px; ";
    $wsImgStr .= "margin-right: 8px; vertical-align: top; border: 0px solid #777777' src='https://image.bitmonky.com/getMbrTmn.php?id=".$tRec['wzUserID']."'>";
  }
  ?>
  <tr valign='top'>
  <td><?php echo $sAnkor ?><?php echo $wsImgStr ?></a></td>
  <td colspan='2'>
  <span class='wzInstruction'><?php echo $tRec['name'] ?></span><br>
  <font color='#aaaaaa'><?php echo $tRec['description'] ?></font><br clear='left'>
        
  <a 
  href='javascript:wzLink("/whzon/mbr/mbrViewPhotos.php?wzID=<?php echo $sKey."&fwzUserID=".$tRec['wzUserID']."&fcurAlbumID=".$tRec['wzPhotoAlbumID'];?>");');>
  View Album</a>
  </td>
  <td><?php echo $tRec['rdate'];?></td>
  <td><?php echo $tRec['cityName'] ?></td>
  </tr>
  <tr><td></td><td colspan='6'>

  <?php 
  $SQL  = "Select PhotoID from tblwzPhoto  ";
  if ($fsq){
    $SQL .= "inner join ( select count(*)nRes, objpItemID ";
    $SQL .= "from tblObjPreIndex  ";
    $SQL .= "inner join tblpreIndexCWords  on prcwWord = objpWord ";
    $SQL .= "where tblObjPreIndex.objpName='photo' ".$fsqry;
    $SQL .= "group by  objpItemID ";
    $SQL .= ")R on R.objpItemID = photoID ";
  }
  $SQL .= "where wzPhotoAlbumID=".$tRec['wzPhotoAlbumID']." ";
  $SQL .= "limit 6";
  $pRec = null;
  $presult = mkyMsqry($SQL);
  $pRec = mkyMsFetch($presult);
  while ($pRec){ ?>
    <a href='javascript:wzLink("/whzon/mbr/mbrViewPhotos.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php 
    echo $tRec['wzUserID'] ?>&vPhotoID=<?php echo $pRec['PhotoID'] ?>");');> 
    <img style='border-radius:0.5em;margin-right:4px;' 
    src='https://image.bitmonky.com/getPhotoTmn.php?id=<?php echo $pRec['PhotoID'] ?>'/></a>
    <?php 
    $pRec = mkyMsFetch($presult);
  }
  echo "</td></tr'>";
  $n = $n + 1;
  $tRec = mkyMsFetch($result);
}
echo "</table>";

echo "<div style='margin-top:2em;'>";
if ($n > 0) {
  echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage + $nRows)."\");'>Next</a>";
}
if ($nextPage > 0) {
  echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage - $nRows)."\");'>Back</a>";
}
echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=0\");'>Top</a>";
//echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&spin=spin&newPg=0\");'>Spin Again</a>";
echo "</div>";

if (!$sessISMOBILE){
  echo "</td><td style='padding-left:25px;width:350px;'>";
}
else {
  echo "<p/>";
}

getBigCubeAds('0px',2);
echo "</td></tr></table>";

function showDigListing($digID){
    global $sKey;
    global $userID;
    global $whzdom;
    global $phPrivacey;
    global $i,$n,$mtFranCID;

    $SQL = "SELECT tblwzPhotoAlbum.wzPhotoAlbumID,tblwzPhotoAlbum.wzUserID,tblwzPhotoAlbum.rdate,tblwzPhotoAlbum.description,tblwzPhotoAlbum.name, ";
    $SQL .= "imgFlg, tblCity.name as cityName, tblCity.countryCD as countryName ";
    $SQL .= "FROM tblwzPhotoAlbum  ";
    $SQL .= "inner join tblwzPhoto  on tblwzPhotoAlbum.wzPhotoAlbumID = tblwzPhoto.wzPhotoAlbumID ";
    $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID=tblwzPhotoAlbum.wzUserID ";
    $SQL .= " inner join tblCity  on tblCity.cityID=tblwzUser.cityID  ";
    $SQL .= " where ".$phPrivacey." and photoID=".$digID;

    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if (!$tRec){
      return;
    }

    $sAnkor  = "<a href='javascript:wzLink(\"/whzon/mbr/mbrProfile.php?franCID=".$mtFranCID."&wzID=".$sKey."&fwzUserID=".$tRec['wzUserID'];
    $sAnkor .= "&feventID=".$tRec['wzPhotoAlbumID']."\");');>";

    $wsImgStr = "";
    if ($tRec['imgFlg'] == 1 ) {
      $wsAnkor = "";
      $wsImgStr = $wsAnkor."<img style='border-radius:50%;float: left;margin-bottom: 3px; ";
      $wsImgStr .= "margin-top: 3px; margin-right: 8px; vertical-align: top; border: 0px solid #777777' ";
      $wsImgStr .= "src='https://image.bitmonky.com/getMbrTmn.php?id=".$tRec['wzUserID']."'>";
    }
    ?>
    <tr valign='top'>
    <td><?php echo $sAnkor ?><?php echo $wsImgStr ?></a></td>
    <td colspan='2'>
    <span class='wzInstruction'><?php echo $tRec['name'] ?></span><br>
    <font color='#aaaaaa'><?php echo $tRec['description'] ?></font><br clear='left'>

    <a href='javascript:wzLink("/whzon/mbr/mbrViewPhotos.php?wzID=<?php 
    echo $sKey;?>&fwzUserID=<?php echo $tRec['wzUserID'] ?>&fcurAlbumID=<?php echo $tRec['wzPhotoAlbumID'] ?>");');>View Album</a>
    </td>
    <td><?php echo $tRec['rdate'];?></td>
    <td><?php echo $tRec['cityName'] ?></td>
    </tr>
    <tr><td></td><td colspan='6'>
    <?php
    $SQL = "Select PhotoID from tblwzPhoto  where not photoID = ".$digID." and wzPhotoAlbumID=".$tRec['wzPhotoAlbumID']." limit 5";
    $pRec = null;
    $presult = mkyMsqry($SQL);
    $pRec = mkyMsFetch($presult);
    ?>
    <a href='javascript:wzLink("/whzon/mbr/mbrViewPhotos.php?wzID=<?php 
    echo $sKey;?>&fwzUserID=<?php echo $tRec['wzUserID'] ?>&vPhotoID=<?php echo $digID;?>");');>
    <div style='background:black;padding:0px;border-radius:.5em;margin-bottom:.5em;'>
    <center><img style='border-radius:0em;margin:0em;max-width:100%;' src='https://image.bitmonky.com/getPhotoImg.php?fpv=<?php echo $tRec['wzUserID']."&id=".$digID;?>'/>
    </center>
    </div>
    </a>
    <?php
    while ($pRec){ ?>
      <a href='javascript:wzLink("/whzon/mbr/mbrViewPhotos.php?wzID=<?php 
      echo $sKey;?>&fwzUserID=<?php echo $tRec['wzUserID'] ?>&vPhotoID=<?php echo $pRec['PhotoID'] ?>");');>
      <img style='border-radius:0.5em;margin-right:4px;' src='https://image.bitmonky.com/getPhotoTmn.php?id=<?php echo $pRec['PhotoID'] ?>'/></a>
      <?php
      $pRec = mkyMsFetch($presult);
    }
    echo "</td></tr'>";
    $n = $n + 1;
}
?> 
