<script>
function wzPopScrollJoin(mbrID){
        parent.wzQuickReg();
}
</script>
   <p>
   <div class='InfoCardClear'>
   <img style='float:left;margin-right:15px;' src='//image.bitmonky.com/img/smallcart.png'/>
   <b>BitMonky Store Fronts</b><p/>Scope : <?php echo $scopeDisplay ?> - <b>See Also</b>
   <?php drawMyMenu($myMode,$modes);?>
   </div>
<p>
<table style='margin-top:50px;width:100%'><tr valign='top'>
<td>
  
<table class='docTable' style='width:100%'>
  <tr valign='top'>
    <td style='padding-right:60px';>
<?php 

$SQL = "SELECT tblwzOnline.wzUserID as online,storeID, storeDesc,storeTitle,storeUID,status, ";
$SQL .= " tblCity.name as city, date(tblwzUser.lastOnline)lastOnline,tblStore.Status,coverage,storeCityID  ";
$SQL .= " from tblStore  inner join tblCity  on cityID=storeCityID ";
$SQL .= " inner join tblStoreCoverage  on CoverageID = storeCoverageID ";
$SQL .= " inner join tblwzUser  on storeUID = tblwzUser.wzUserID ";
$SQL .= " left join tblwzOnline  on storeUID = tblwzOnline.wzUserID ";
$SQL .= " where ".$storeSearch." ";
$SQL .= " order by activated desc, nProducts desc, lastOnline desc ";


$winX = 200;
$Xinc = 120;

if (isset($_GET['newPg'])){$pg = clean($_GET['newPg']);} else {$pg = 0;}
$nextPage = $pg;

echo "<p><table>";
$i = 0;
$nRows = 15;

$link = $linkRoot;
$appName = "myTown.php";

$nTop = $pg + $nRows;

$selTop = "limit ".$nTop." ";

$SQL .= $selTop;

$tRec = null;
$result = mkyMsqry($SQL) or die($SQL);
$tRec = mkyMsFetch($result);

$cpage = 0;
while($tRec && $cpage < $nextPage) {
  $tRec = mkyMsFetch($result);
  $cpage = $cpage + 1;
}

$frIconStyle = "";
$onIconStyle = "border-radius:50%;";
$frIconStyle = "border-radius:.35em;";
if ($mobile){
  $frIconStyle = "border-radius:.25em;height:30px;width:36px;vertical-align:middle;";
  $onIconStyle = "border-radius:.25em;height:26px;width:160px;margin-top:15px;";
}

While ($tRec && $i < $nRows){
  $storeID = $tRec['storeID'];
  $SQL = "select count(*)as nRec from ICDimages.tblStoreImg where iStoreID=".$storeID;
  $mresult = mkyMyqry($SQL);
  $mRec    = mkyMyFetch($mresult);
  $hasImage = $mRec['nRec'];

  $SQL = "Select tblCountry.name as country from tblCity  ";
  $SQL .= "inner join tblCountry  on tblCountry.countryID=tblCity.countryID ";
  $SQL .= "where tblCity.cityID = ".$tRec['storeCityID'];
  
  $cRec = null;
  $cresult = mkyMsqry($SQL) or die($SQL);
  $cRec = mkyMsFetch($cresult);

  $profile_A = "<a style='font-size:14px;' href='/whzon/store/storeProfile.php?wzID=".$sKey."&fstoreID=".$tRec['storeID']."'>";

  $winX = $winX + $Xinc;

 ?>
  <tr valign='top'>
    <td style='padding-top:14px;width:88px;'> 
    <?php 
      echo $profile_A; 
      echo "<img style='border-radius:0.5em;margin:0px;border: 0px solid #888888;width:72px;height:90px;' ";
      if ($hasImage){
        echo " src='https://image.bitmonky.com/getMbrStoreImg.php?id=".$tRec['storeID']."'></center></a>";
      }
      else {
        echo " src='https://image.bitmonky.com/getMbrImg.php?id=".$tRec['storeUID']."'></center></a>";
      }
    ?>
    </td>
    <td ID='tag<?php echo $tRec['storeUID'] ?>' style='padding-top:10px;'>
      
      <?php echo $profile_A ?><?php echo $tRec['storeTitle'] ?></a><span style='font-size:13px;'>
      <br/><b>Shop Status - </b> <?php echo $tRec['status'] ?>,
      <p/><b>Location - </b> <?php echo $tRec['city'] ?>,
      <?php echo $cRec['country'] ?> </span><br style='clear:right;'>

      <?php if ($tRec['online']){ ?>
         <img title='The Owner Is Online' style='border-radius:50%;<?php echo $onIconStyle ?>' src='https://image.bitmonky.com/img/onlineIcon.png'></a>
      <?php } else { ?>
         <b>Owner Last Online:</b> <?php echo $tRec['lastOnline'] ?>
      <?php } ?>
      <P> 
      <?php  if ( $tRec['storeDesc' ] != "" ) { ?>
       
         <b>Description:</b> <?php echo left($tRec['storeDesc'],180) ?>  <?php echo $profile_A ?>...visit store</a><br>
      <?php } ?>
      <br><br>
    </td>
  </tr>
<?php 
  $i = $i + 1;
  $tRec = mkyMsFetch($result);
}
echo "</table>";


echo "<div style='margin-top:2em;'>";
if ($i > 0){
  echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage + $nRows)."\");'>Next</a>";
}  
if ($nextPage > 0) {
  echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage - $nRows)."\");'>Back</a>";
}
echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=0\");'>Top</a>";
//echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&spin=spin&newPg=0\");'>Spin Again</a>";
echo "</div>";

if (isset($_GET['ferror'])){
  if (clean($_GET['ferror']) == 1 ) {
    echo "<p><span class='errorMsg'></span>";
  }
}
?>
    <br><br>
  </td>
  <td style='padding:0px;text-align:right;'>
  </td>
</tr>
</table>
<?php
if (!$sessISMOBILE){
  echo "</td><td style='width:360px;padding-left:1.5em;'>";
}
else {
  echo "<p/>";
}
getBigCubeAds('0px',2);
?>
</td></tr></table>  
  
