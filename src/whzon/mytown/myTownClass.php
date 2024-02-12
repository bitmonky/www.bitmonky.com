<?php
include_once('newsHLObjs.php');
$fsq = safeGET('fsq');
$mkey = new mkySess($sKey,$userID);
$webItem = new mkyActivityCard($mkey,$cityID,$scope,'class','Classified Ads',$fsq);
$fsqry = null;
$fsqry = $webItem->getQry();
if (!$sessISMOBILE){
  $mdir = null;
}
else {
  $mdir = "mblp/";
}

?>   <p>
<div class='infoCardClear'>
<B><span class='wzBold' style='color:darkKhaki;'>Classifieds Scope - <?php echo $scopeDisplay ?></span> - See Also </b>
<?php drawMyMenu($myMode,$modes);?>
  <div style='margin-top:.5em;'>
  <form method='GET' action=''>
  <input type='hidden' name='wzID'      value='<?php echo $sKey;?>'/>
  <input ID='accQryStr' class='srchBox' type='text'  onkeydown="return (event.keyCode!=13);"
    name='fsq'   onfocus='this.value=""'  value='<?php echo $fsq;?>'
    placeholder='Member Classified Ads' style='font-size:larger;font-weight:bold;width:55%;'/>
  <input type='hidden' name='catID'     value='<?php echo $catID;?>'/>
  <input type='hidden' name='fscope'    value='<?php echo $scope;?>'/>
  <input type='hidden' name='franCID'   value='<?php echo $cityID;?>'/>
  <input type='hidden' name='fmyMode'   value='class'/>
  <input type='hidden' name='fwzUserID' value='<?php echo $userID;?>'/>
  <input class='srchButton' type='button' onclick='doAccQry(0,true,"class")' style='padding:.65em;vertical-align:top;' value=' Search '/>
  </form>
  </div>
</div> 
<p>
<?php if ( $userID == 0 ) { ?>
   <input onclick='parent.wzQuickReg();' type='button' style='padding:.6em;' value=' Post An Add ' />
<?php } else { ?>
   <input onclick='parent.wzGetPage("/whzon/mbr/mbrPostClassified.php?wzID=<?php echo $sKey.'&itemID=post';?>");' style='padding:.6em;' type='button' value=' Post An Ad '/>
<?php } ?>
<p>
<div ID='accQrySpot'></div>

<table width='100%' class='myTown'><tr valign='top'>
<td>

<?php 
if (isset($_GET['fcatID'])){$catID = clean($_GET['fcatID']);} else {$catID = "";}
$catStr = "";
if ( $catID != "" && $catID != '0' ) {
  $catStr = " and tblClassifieds.classKey='".$catID."' ";
}

$fsqry = mkyStrReplace('tblClassifieds.item','tblObjPreIndex.objpWord',$fsqry);

if ( $searchFlg == "" ) {
  $SQL = "SELECT itemStoreID,adID,tblClassifieds.imgFlg,Item, tblClassifieds.wzUserID, tblCity.name as cityName, tblCity.countryCD as countryName,tblClassifieds.cityID,";
  $SQL  .= " tblClassifieds.adBody FROM tblClassifieds  inner join tblCity on tblCity.cityId=tblClassifieds.cityID ";
  if ($fsq){
    $SQL .= "inner join ( select count(*)nRes, objpItemID ";
    $SQL .= "from tblObjPreIndex  ";
    $SQL .= "inner join tblpreIndexCWords  on prcwWord = objpWord ";
    $SQL .= "where tblObjPreIndex.objpName='class' ".$fsqry;
    $SQL .= "group by  objpItemID ";
    $SQL .= ")R on R.objpItemID = adID ";
  }
  $SQL  .= " inner join tblwzUser  on tblClassifieds.wzUserID=tblwzUser.wzUserID "; 
  $SQL  .= " where itemRetired is null and postStatus is null and sandBox is null and spamflg<>1 ";
  $SQL  .= "and ".$classSearchStr.$catStr." ";
  $SQL  .= "order by ";
  if ($fsq){
    $SQL .= "tblClassifieds.ImgFlg desc, R.nRes desc ";
  }
  else {
    $SQL  .= "tblClassifieds.ImgFlg desc, postDate desc, sortOrder";
  }
} 
else {
  $SQL = "SELECT itemStoreID, adID,imgFlg,Item, tblClassifieds.wzUserID, tblCity.name as cityName, tblCity.countryCD as countryName, tblClassifieds.adBody,tblClassifieds.cityID "; 
  $SQL .= " FROM tblClassifieds  inner join tblCityGroup on gCityID=tblClassifieds.CityID "; 
  $SQL .= " inner join tblwzUser  on tblClassifieds.wzUserID=tblwzUser.wzUserID "; 
  $SQL .= " inner join tblCity  on tblCity.cityId=tblClassifieds.cityID where itemRetired is null postStatus is null and sandBox is null and spamFlg<>1 and tblCityGroup.cityId=".$myCityID;
  $SQL .= " order by tblClassifieds.ImgFlg desc, postDate desc,sortOrder";
}
if ($userID == 17621){
  //echo $SQL;
}
if (isset($_GET['newPg'])){$pg = clean($_GET['newPg']);} else {$pg = 0;}
$nextPage = $pg;

$n = 0;
$i = 0;
$nRows = 12;

$nTop = $pg + $nRows;

$selTop = " limit ".$nTop." ";

$SQL .= $selTop;

$tRec = null;
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);

$p = 0;
while ($tRec && $p < $nextPage) {
  $tRec = mkyMsFetch($result);
  $p = $p + 1;
}

echo "<p><table class='myTown' width=100% >";

$link = $linkRoot."&fsq=".$fsq;

$appName = "myTown.php";
if ($digID){
  showDigListing($digID);
}
while ($tRec and $i < $nRows){
  $storeID = $tRec['itemStoreID'];
  $adID    = $tRec['adID'];
  $mtFranCID = $tRec['cityID'];

  if ($storeID) {
    $gotoURL = "/whzon/store/storeProfile.php?wzID=".$sKey."&fstoreID=".$storeID."&fitemID=".$adID;
    $imgsrc  = "getStoreItemImg.php?ad=".$adID."&fs=".$storeID;
  }
  else {
    $gotoURL = "/whzon/".$mdir."mbr/mbrViewClassified.php?franCID=".$mtFranCID."&wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."&itemID=".$adID;
    $imgsrc  = "getClassImg.php?id=".$tRec['adID']."&fs=".$tRec['wzUserID']; 
  }
  ?>
  <tr valign='top'>
  <td style='padding:8px;'>
  <div class='infoCardClear' style='background:#151515;'>
  <?php  if ( $tRec['imgFlg'] == 1 ) { ?>
    <a href='javascript:wzLink("<?php echo $gotoURL;?>");'>
    <img align='left' style='width:135px;margin:0em 1em 1em 0em;border-radius:.5em;' 
    src='https://image.bitmonky.com/<?php echo $imgsrc;?>'></a>
  <?php } ?>
  <span class='wzInstruction'>   <?php echo mkyStrReplace('/',' ',$tRec['Item']);?></span> | 
  <b><?php echo $tRec['cityName'] ?>,<?php echo $tRec['countryName'];?></b>
  <font color='#aaaaaa'><?php echo mkyStrReplace('/',' ',left($tRec['adBody'],250));?>...
  </font><a href='javascript:wzLink("<?php echo $gotoURL;?>");'>more</a>
  <br clear='left'/></div>
  </td>
  </tr>
  <?php 
  $i = $i + 1;
  $n = $n + 1;
  $tRec = mkyMsFetch($result);
}
echo "</table>"; 

echo "<div style='margin-top:2em;'>";
echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage + $nRows)."\");'>Next</a>";
if ($nextPage > 0) {
  echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage - $nRows)."\");'>Back</a>";
}
echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=0\");'>Top</a>";
//echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&spin=spin&newPg=0\");'>Spin Again</a>";
echo "</div>";

if (!$sessISMOBILE){
  $mdir = null;
  echo "</td>";
  echo "<td style='width:350px;padding-left:25px;'>";
}
else {
  echo "<p/>";
  $mdir = "mblp/";
}
getBigCubeAd('12px');

   if ( $searchFlg == "" ) {
     $SQL = "SELECT tblClassifieds.classKey as catID, fullname as name, COUNT(*) AS nsites";
     $SQL .= " FROM tblClassifieds  inner join tblClassDirectory on tblClassDirectory.classKey=tblClassifieds.classKey inner join tblCity On tblClassifieds.cityID=tblCity.cityID ";
     $SQL .= " where ".$classSearchStr;
     $SQL .= " GROUP BY tblClassifieds.classkey, fullname ";
     $SQL .= " ORDER BY nsites desc;";
   } 
   else {
     $SQL = "SELECT oldCategoryID as catID, Category as name, COUNT(*) AS nsites";
     $SQL .= " FROM tblTopSites  inner join tblCityGroup on gCityId=tblTopSites.cityID where tblCityGroup.cityID=";
     $SQL .=   $MyCityID;
     $SQL .= " GROUP BY Category, OldCategoryID ";
     $SQL .= " ORDER BY COUNT(*) DESC, category;";
   }
//echo $SQL 

$tRec = null;
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);

echo "<div style='margin-top:15px;background:#333333;border-radius: .5em;font-weight:bold;padding:15px;'>";
?>

<h2>Ad Categories</h2>
<a href='javascript:wzLink("myTown.php?wzID=<?php echo $sKey;?>&fwzUserId=<?php echo $wzUserID ?>");'>Show All</a>
<p/>
<?php 
$i = 0;
$nRows = 40;

$rowColor = "#ddddff";
While ($tRec && $i < $nRows){
  echo "<a href='javascript:wzLink(\"myTown.php?wzID=".$sKey."&fcatID=".$tRec['catID'] ."&fwzUserId=".$wzUserID."\");'>".left($tRec['name'],20)."";
  echo "[".$tRec['nsites']."]</a><br>";

  $i = $i + 1;
  $tRec = mkyMsFetch($result);
}
echo "</div>";
echo "</td></tr></table>";

function showDigListing($digID){
    global $mdir;
    global $sKey;
    global $userID;
    global $whzdom;
    global $i,$n;

    $SQL  = "SELECT itemStoreID,adID,tblClassifieds.imgFlg,Item, tblClassifieds.wzUserID, tblCity.name as cityName, tblCity.countryCD as countryName,";
    $SQL .= " tblClassifieds.cityID,tblClassifieds.adBody FROM tblClassifieds  inner join tblCity on tblCity.cityId=tblClassifieds.cityID";
    $SQL .= " inner join tblwzUser  on tblClassifieds.wzUserID=tblwzUser.wzUserID ";
    $SQL .= " where adID = ".$digID." and itemRetired is null and postStatus is null and sandBox is null ";
    $SQL .= "and spamflg<>1 ";

    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if (!$tRec){
      return;
    }

    $storeID = $tRec['itemStoreID'];
    $adID    = $tRec['adID'];
    $mtFranCID = $tRec['cityID'];

    if ($storeID) {
      $gotoURL = "/whzon/store/storeProfile.php?wzID=".$sKey."&fstoreID=".$storeID."&fitemID=".$adID;
    }
    else {
      $gotoURL = "/whzon/".$mdir."mbr/mbrViewClassified.php?franCID=".$mtFranCID."&wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."&itemID=".$adID;
    }
    ?>
    <tr valign='top'>
    <td style='padding:8px;'>
    <div class='infoCardClear' style='background:#151515;'>
    <?php  if ( $tRec['imgFlg'] == 1 ) { ?>
      <a href='javascript:wzLink("<?php echo $gotoURL;?>");'>
      <img align='left' style='width:100%;margin:0em 1em 1em 0em;border-radius:.5em;' 
      src='https://image.bitmonky.com/getClassImg.php?id=<?php echo $tRec['adID']."&fs=".$tRec['wzUserID']; ?>'></a>
    <?php } ?>
    <span class='wzInstruction'>   <?php echo mkyStrReplace('/',' ',$tRec['Item']);?></span> |
    <b><?php echo $tRec['cityName'] ?>,<?php echo $tRec['countryName'];?></b>
    <font color='#aaaaaa'><?php echo mkyStrReplace('/',' ',left($tRec['adBody'],250));?>...</font><a href='javascript:wzLink("<?php echo $gotoURL;?>");'>more</a>
    </div>
    </td>
    </tr>
    <?php
    $n = $n + 1;
}
?> 
