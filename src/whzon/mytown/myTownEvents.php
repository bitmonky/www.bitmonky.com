<!-- SiteLOGz Code For: "My Town Events" only!  -->
<script src='/whzon/snapLTp.php?ID=17621&wsID=5&pgID=582013'></script>
<!-- End of SiteLOGz Code -->

   <p>
   <B><span class=wzBold>Local Events</span> - See Also</b>
   | <a href='myTown.php?wzID=<?php echo $sKey;?>&fmyMode=mbrs&fwzUserId=<?php echo $wzUserID ?>'>People</a> 
   | <a href='myTown.php?wzID=<?php echo $sKey;?>&fmyMode=web&fwzUserId=<?php echo $wzUserID ?>'>Websites</a> 
   | <a href='myTown.php?wzID=<?php echo $sKey;?>&fmyMode=class&fwzUserId=<?php echo $wzUserID ?>'>Classifieds</a> 
   | <a href='myTown.php?wzID=<?php echo $sKey;?>&fmyMode=mBlog&fwzUserId=<?php echo $wzUserID ?>'>miniBLOGs</a> 
   | <a href='myTown.php?wzID=<?php echo $sKey;?>&fmyMode=photo&fwzUserId=<?php echo $wzUserID ?>'>Photos</a> 
   | <a href='myTown.php?wzID=<?php echo $sKey;?>&fmyMode=wNews&fwzUserId=<?php echo $wzUserID ?>'>World News</a> 

   <p>
<table class='MyTown' style='width:95%'><tr valign='top'>
<td>
<p>
<?php 
if ( $userID == 0 ) { 
  ?>
  <input type='button' value=' Post Event ' onclick='parent.wzQuickReg();'>
  <?php 
} 
else {
  if ($userID == 17621){
    ?>
    <input type='button' value=' Post Event ' onclick='document.location.href="/whzon/mbr/mbrPostEvent.php?wzID=<?php echo $sKey.'&itemID=post';?>";'>
    <?php
  } 
}
?>
<p>

<?php 
if ( $searchFlg == "" ) {
  $SQL = "SELECT tblwzEvent.*,  tblCity.name as cityName, tblCity.countryCD as countryName FROM tblwzEvent inner join tblwzUser On tblwzUser.wzUserID= tblwzEvent.wzUserID inner join tblCity on tblCity.cityID=tblwzEvent.cityID where spamFlg<>1 and ".$eventSearch ." order by endDate;";
} 
else {
  $SQL = "SELECT tblwzEvent.*,  tblCity.name as cityName, tblCity.countryCD as countryName FROM tblwzEvent inner join tblwzUser On tblwzUser.wzUserID= tblwzEvent.wzUserID  inner join tblCityGroup on gCityID=tblwzEvent.CityID  inner join tblCity on tblCity.cityId=tblwzEvent.cityID where spamflg<>1 and tblCityGroup.cityID=".$cityID. " order by endDate;";
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
echo "<p><table class='myTown' cellpadding=1 width=95% >"
?>
<script>
<!--
function openComments(URL,wsID){
  document.location.href = URL;
}
//-->
</SCRIPT>
<?php 
$i = 0;
$nRows = 10;
$link = "?wzID".$sKey."&fwzUserID=".$wzUserID."&fcatID=".$catID;
$appName = "myTown.php";

While ($tRec && $i < $nRows){ 
  $sAnkor = "<a href='https://bitmonky.com/whozon/mbrViewEvent.asp?fwzUserID=".$tRec['wzUserID']."&feventID=".$tRec['eventID']."';>";

  $wsImgStr = "";
  if ( $tRec['imgFlg'] == 1 ) {
        $wsAnkor = "";
        $wsImgStr = $wsAnkor."<img style='float: left;margin-bottom: 3px; margin-top: 3px; margin-right: 8px; vertical-align: top; border: 0px solid #777777' src='https://bitmonky.com/whozon/getEventMiniImg.asp?feventID=".$tRec['eventID']."'>";
  }
 ?>
      <tr valign='top'>
        <td><?php echo $sAnkor ?><?php echo $wsImgStr ?></a></td>
        <td colspan='2'>
        <span class='wzInstruction'><?php echo $tRec['title'] ?></span><br>
        <font color='#aaaaaa'><?php echo $tRec['shortDesc'] ?></font><br clear='left'>
          <?php echo $sAnkor ?>View Event</a>
        </td>
        <td><?php echo $tRec['startDate'] ?></td>
        <td><?php echo $tRec['cityName'] ?></td>
      </tr>
<?php 
      $n = $n + 1;
      $tRec = mkyMsFetch($result);
    }
 ?>   
    </table> 
<?php 
echo "<p><a href='".$appName.$link."&newPg=".($nextPage + $nRows)."'>Next</a>";
if (($nextPage > 0) ) {
  echo " | <a href='".$appName.$link."&newPg=".($nextPage - $nRows)."'>Back</a>";
}
echo " | <a href='".$appName.$link."&newPg=0'>Top</a>";
echo " | <a target=_new href='javascript:parent.wzQuickReg();'>Join WhzOn.Com To Post Your Stuff</a>";
?>
</td></tr></table>
