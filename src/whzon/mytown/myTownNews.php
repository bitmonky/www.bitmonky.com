
   <p>
   <B><span class=wzBold>MiniNEWs In My <?php echo $scopeDisplay ?></span> See Also</b> 
   <?php drawMyMenu($myMode,$modes);?>

   <p>

<table width='95%' class='myTown'><tr valign='top'>
<td>
<?php 
if (isset($_GET['fcatID'])){$catID = clean($_GET['fcatID']);} else {$catID = "";}
if ( $catID != "" ) {
  $catStr = " and oldcategoryID=".$catID." ";
}
if ( $myMetroID == 0 ) {
  $SQL = "SELECT date(pDate) fpDate, tblMiniNews.* From tblMiniNews INNER JOIN   tblwzUser ON tblMiniNews.wzUserID = tblwzUser.wzUserID where sandBox is null and ".$userSearch." and adultContent<>1 and spamFlg<>1 order by pDate desc;";
} 
else {
  $SQL = "SELECT date(pDate) fpDate, tblMiniNews.* From tblMiniNews INNER JOIN   tblwzUser ON tblMiniNews.wzUserID = tblwzUser.wzUserID inner join tblCity on tblCity.cityID=tblwzUser.cityID where sandBox is null and ".$userSearch." and adultContent<>1 and spamFlg<>1  order by pDate desc;";
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
echo "<p><table class='myTown' width=95% >";
   ?><tr valign=top>
  <td style="background-image: url('');" colspan=1><font color='white'><b>Owner</b></font></td>
  <td style="background-image: url('');" colspan=1><font color='white'><b>Headline</b></font></td>
  <td style="background-image: url('');" width='70' align='right'><font color='white'><b>Post Date</b></font></td>
  </tr>
<script>
<!--
function openFanReq(fanID,wsID){
      URL="https://bitmonky.com/wzUsers/mbrProfiles/fanRequestFrm.asp?fanID=" + fanID + "&wsID=" + wsID;
      winName = "wzReq" + fanID;
      var onfanReq = window.open(URL,winName,"target=new,width=450,height=130,resizable=no,scrollbars=no");
      onfanReq.focus();
}

//-->
</SCRIPT>
<?php 
$i = 0;
$nRows = 10;
$link = "?wzID".$sKey."&fwzUserID=".$wzUserID."&fcatID=".$catID;
$appName = "myTown.php";

While ($tRec && $i < $nRows){ 

  $articleID = $tRec['articleID'];
  $newsID = $tRec['miniNewsID'];
  $story = "";
  $img = "";
  if ( $articleID ) {
    $SQL = "select imgFlg, body from tblminiNewsArticle where NewsArticleID=".$articleID;
    $nRec = null;
    $nresult = mkyMsqry($SQL);
    $nRec = mkyMsFetch($nresult);

    $story = left($nRec['body'],250)."...";

    if ( $nRec['imgFlg'] == 1 ) {
      $img = "<img src='https://bitmonky.com/wzUsers/mbrProfiles/miniNewsGetImgTN.asp?farticleID=".$articleID."' style='float:right;border:0px solid #777777;margin-right:8px;'>";
    }
  } 
  else {
    $story = left($tRec['linkDesc'],250)."...";
    if ( $tRec['linkImgFlg'] == 1 ) {
      $img = "<img src='https://bitmonky.com/wzUsers/mbrProfiles/miniNewsGetImgTN.asp?flinkID=".$newsID."' style='float:right;border:0px solid #777777;margin-right:8px;'>";
    }
  }

  $websiteID = $tRec['websiteID'];
  if ( $websiteID == 0 ) {
    $readerApp = "https://bitmonky.com/whozon/MbrMiniNews.asp?fwzUserID=".$tRec['wzUserID'];
    $websiteID = $wzUserID;
  } 
  else {
    $readerApp = "https://bitmonky.com/whozon/MiniNews.asp?fwebsiteID=".$tRec['websiteID'];
  }
  $newsLink = "<a href='".$readerApp."&fnewsID=".$newsID."'>";


  echo "<tr  valign=top>";
  echo "<td width='36' align='left'>".$newsLink." <img style='border:0px solid #777777;' src='https://image.bitmonky.com/getMbrTmn.php?id=".$tRec['wzUserID'] ."'></a></td>";
  echo "<td>".$img."<b>".$tRec['newsTxt']."</b><br>".$story.$newsLink."Full Story<a></td>";
  echo "<td align='right'>".$tRec['fpDate']."</td>";
  echo "</tr>";
  $i = $i + 1;
  $n = $n + 1;
  $tRec = mkyMsFetch($result);
}
echo "</table>";
if (i > 0 ) {
  echo "<p><a href='".$appName.$link."&newPg=".($nextPage + $nRows)."'>Next</a>";
}
if ($nextPage > 0 ) {
  echo " | <a href='".$appName.$link."&newPg=".($nextPage - $nRows)."'>Back</a>";
}
echo " | <a href='".$appName.$link."&newPg=0'>Top</a>";

?>
    </td>
  </tr>
</table> 

