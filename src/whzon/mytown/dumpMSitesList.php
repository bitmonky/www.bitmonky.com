<?php
include_once('../mkysess.php');
include_once('newsHLObjs.php');

ini_set('display_errors',1);
  error_reporting(E_ALL);
$pgSQry = "?".safeSRV('QUERY_STRING');
$catStr = null;
$curCat = "<p/>";
$cityID = null;
$scope  = null;
if(isset($_GET['fcatID'])){$catID = clean($_GET['fcatID']);} else {$catID = "";}
if ( $catID != "" && $catID != 0 ) {
  $SQL = "select name from Category2 where categoryID = ".$catID;
  $cres = mkyMsqry($SQL);
  $crec = mkyMsFetch($cres);
  if ($crec){
    $curCat = "<div class='infoCardClear' style='font-size:larger;background:#151515;color:darkKhaki;'>Category: ".$crec['name']."</div>";
  }
  $catStr = " and oldcategoryID=".$catID." ";
}
$fsq = safeGET('fsq');
$spin = safeGET('spin');
if ($spin){
  //$fsq = null;
}
$mkey = new mkySess($sKey,$userID);
$webItem = new mkyActivityCard($mkey,$cityID,$scope,'web','Websites',$fsq);
$fsqry = null;
$fsqry = $webItem->getQry();
?>
<table style='margin-top:3.5em;width:100%'><tr valign='top'>
<?php $sessISMOBILE=true;
if ($sessISMOBILE){
  echo "<td>";
}
else {
  echo "<td style='width:60%;'>";
}

if (isset($_GET['newPg'])){$pg = clean($_GET['newPg']);} else {$pg = 0;}
$nextPage = $pg;
$n        = $pg + 1;

$cpage = 0;
$i = 0;
$nRows = 10;

$limit = " limit ".$nRows;
if ($pg > 0){
  $limit = " limit ".($pg + $nRows);
}
$wzUserID =573446;
$isFranOwner = null;
$searchFlg = null;
$cityID = 110;
$weblWSID = null;
$scope = 'myCity';
$mtFranCID = $cityID;
$digID = null;
if ($scope == 'myCity'){
  $searchStr = " (ndxwCityID = ".$cityID." or weblCityID = ".$cityID.") ";
  $weblWSID = 'weblCityID,';
}
if ( $searchFlg == "" ) {
  $ndxwhere  = " where ndxwDeleted is null ";
  $ndxwhere .= "and  ".$searchStr;

  $SQL  = "SELECT null mWebFlg, 1 wsImgFlg, ndxwCategory name,ndxwUID wzUserID,ndxwWebsiteID websiteID,ndxwCity cityName,ndxwURL URL,ndxwProt,ndxwTitle Title,ndxwCategoryID oldCategoryID,ndxwDesc description, ";
  $SQL .= "ndxwCityID,date(ndxwRespDate)respDate,timestampdiff(day,ndxwLastUpdate,now())lastUpdate,ndxwRating wsRatingID ";
  $SQL .= "FROM ndxWeb.ndxWebsites ";

  if ($fsq){
    $SQL .= "inner join ( select sum(prcwZeroWT * power(prcwLen,2))nRes, objpItemID wsID ";
    $SQL .= "from tblObjPreIndex  ";
    $SQL .= "inner join tblpreIndexCWords  on prcwWord = objpWord ";
    $SQL .= "where tblObjPreIndex.objpName='web' ".$fsqry;
    $SQL .= "group by  objpItemID ";
    $SQL .= ")R on wsID = ndxwWebsiteID ";
  }
  if ($scope == 'myCity'){
    $SQL .= "left join tblWebsiteLoc  on ndxwWebsiteID = weblWSID ";
  }
  $SQL .= $ndxwhere.mkyStrReplace('oldcategoryID','ndxwCategoryID',$catStr)." ";
  $SQL .= "order by ";
  if ($fsq){
    $SQL .= "R.nRes desc ";
  }
  else {
    $SQL  .= "ndxwWebsiteID desc";
  }
}
else {
  $ndxwhere  = " where not ndxwLastContact is null and ndxwDeleted is null and ";
  // $where .= " mWebFlg=0 and tblCityGroup.cityID=".$myCityID;

  $SQL  = "SELECT 1 wsImgFlg,ndxwCategory name,ndxwUID wzUserID,ndxwWebsiteID websiteID,ndxwCity cityName,ndxwURL URL,ndxwProt,ndxwTitle Title,ndxwCategoryID oldCategoryID,ndxwDesc description, ";
  $SQL .= "ndxwCityID,date(ndxwRespDate)respDate,timestampdiff(day,ndxwLastUpdate,now())lastUpdate,ndxwRating wsRatingID ";
  $SQL .= "FROM ndxWeb.ndxWebsites ";

  $SQL .= $ndxwhere.mkyStrReplace('oldcategoryID','ndxwCategoryID',$catStr)." ";
  $SQL .= "order by ndxwWebsiteID desc";
}
if ($spin){
  $SQL = mkyStrIReplace('wsImgFlg desc,','',$SQL);
  $SQL = mkyStrIReplace('order by ','order by rand(), ',$SQL);
}

$SQL = mkyStrReplace('tblWebsites.stateID','ndxwStateID',$SQL);
$SQL = mkyStrReplace('tblWebsites.countryID','ndxwCountryID',$SQL);
$SQL = mkyStrReplace('tblObjPreIndex','ndxWeb.ndxObjPreIndex',$SQL);
$SQL .= $limit;

//  echo $SQL;

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

$i = 0;
$nRows = 10;
$linkRoot=null;
$link = $linkRoot;
if ($catID){
  $link .= "&fcatID=".$catID;
}
if ($fsq){
  $link .= "&fsq=".mkyUrlEncode($fsq);
}
$appName = "myTown.php";
if ($digID){
  showDigListing($digID);
}
While ($tRec && $i < $nRows){
    $localService = null;
    if ($scope == 'myCity'){
      if ($tRec['ndxwCityID']){
        $localService = ' (Local Service Available) ';
      }
    }

    $strJobs  = "<a  href=# onclick=openListComments('/whozon/frmViewJobs.asp?fwebsiteID=".$tRec['websiteID']."'," .$tRec['websiteID'].")>Job Openings</a>";
    $strClass = "<a href=# onclick=openListComments('/whozon/frmViewClassif (ieds.asp?fwebsiteID=".$tRec['websiteID']."'," .$tRec['websiteID'].")>Items For Sale</a>";
    $strContact  = "<a href=# onclick=openListComments('/whozon/frmVisitorContact.asp?fwebsiteID=".$tRec['websiteID']."',";
    $strContact .= $tRec['websiteID'].")><img style='border:0px;' src='/mbrContactFF.jpg'></a>";
    $strsiteNews  = "<a href=# onclick=openListComments('/whozon/frmLinkSpaces.asp?fwebsiteID=".$tRec['websiteID']."',";
    $strsiteNews .= $tRec['websiteID'].")><img style='border:0px;' src='/linkTrade.jpg'></a>";
    $strVote = "<a href=# onclick=openListComments('/whozon/frmEvaluateSite.asp?fwebsiteID=".$tRec['websiteID']."'," .$tRec['websiteID'].")>Vote</a>";

    $SQL = "select rating, minAge from tblwsRatings  where wsRatingID=".$tRec['wsRatingID'];
    $wsRec = null;
    $wsresult = mkyMsqry($SQL);
    $wsRec = mkyMsFetch($wsresult);
   
    $strViewRating = "Not Rated";
    if ($wsRec) {
      $strViewRating = $wsRec['rating']." ".$wsRec['minAge']."+";
    }

    /*
    if ( $tRec['averageHits'] == 0 ) {
      $showAverage = "*****";
    } 
    else {
      $showAverage = mkyNumFormat($tRec['averageHits'],2);
    }
    */

    $wsImgStr = "";
    $imgFile = "//image.bitmonky.com/img/monkyTalkfbCard.png";
    if ( $tRec['wsImgFlg'] == 1 ) {
      $imgFile = "//image.bitmonky.com/getWsImg.php?id=".$tRec['websiteID'];
    } 
    $imgW = '185px;';
    if ($sessISMOBILE){
      $imgW = '100%;';
    }
    $wsAnkor= " <a target='_blank' href='".fetchWebsiteURL($tRec['websiteID'],true)."' rel='nofollow'>";
    $wsImgStr = $wsAnkor."<img style='float: left; width:".$imgW."margin-bottom: 1em; margin-top: 0em;";
    $wsImgStr .= "border-radius:.5em; margin-right: 8px; vertical-align: top;' src='".$imgFile."'></a>";
    
    $URL = $tRec['URL'];

    if ( $tRec['mWebFlg'] == 1 ) {
      $URL = "bitmonky.com/".$URL;
    }

  echo "<div class='infoCardClear' style='margin-bottom:1em;'>";
  if (!$tRec['respDate']){
    $tRec['respDate'] = 'Never';
  }
  $lastUp = $tRec['lastUpdate'];
  echo "<div align='right' class='infoCardClear' style='background:#222222;color:darkKhaki;font-size:smaller'>Last Crawled - ".$tRec['respDate'];
  if ($lastUp !== null){
    if ($lastUp == 0){$lastUp="Last Updated: Today";}
    else {$lastUp = "Last Updated: ".$lastUp." Days";}
    echo "<br/>".$lastUp;
  }
  if ($userID == 17621){
    echo "<br/><a href='javascript:markWebForDelete(".$tRec['websiteID'].");'>Mark Deleted</a>";
  }
  echo "</div>";
  echo "<table style='width:100%;'><tr valign='top'>";
  echo "<td style='width:135px' align='left'>".$wsImgStr."";
  if (!$sessISMOBILE){
    echo "</td><td style='padding-left:1em;'>";
  }
  else {echo "<br clear='left'/>";}
  echo "<a style='font-size:larger;' target='_blank' oncontextmenu='xxfetchPics(".$tRec['websiteID'].")' ";
  echo "href='".fetchWebsiteURL($tRec['websiteID'],true)."' rel='nofollow' >".splitLWordsNoTag($tRec['Title'])."</a>";
  echo "<br>".splitLWords($tRec['description']); 
  echo "<p/><b><span style='color:darkKhaki'>Location:</span></b> ".$tRec['cityName'].$localService;
  echo "<br><b><span style='color:darkKhaki'>Category:</span></b> ";
  echo " <a ID='wzBold_A' href='javascript:wzLink(\"myTown.php?fmyMode=web&wzID=".$sKey."&fscope=".$scope."&franCID=".$mtFranCID;
  echo "&fwzUserID=".$wzUserID."&fcatID=".$tRec['oldCategoryID']."\");'>".$tRec['name']."</a>";
  echo " | <b><font color='darkKhaki'>Audience </font>: </b>".$strViewRating;
  echo "<br></font> ";

  echo "</td></tr></table>";
  if ($scope == 'myCity' && ($isFranOwner && $userID == $wzUserID)){
    echo "<div align='right'>";
    echo "<a onclick  = 'parent.scrollTo(0,0);' ";
    echo "href='/whzon/franMgr/wsRevListings.php?wzID=".$sKey."&fcityID=".$cityID."&wsID=".$tRec['websiteID']."&pgSQ=".mkyUrlEncode($pgSQry)."'/>Review Listing</a>";
    echo "</div>";
  }
  echo "</div>";
  $i = $i + 1;
  $n = $n + 1;
  $tRec = mkyMsFetch($result);
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
if (!$sessISMOBILE){
  echo "<a class='scrollBut' href='/whzon/adMgr/frmAdMgrAddWebsite.php?wzID=".$sKey."'>Advertise Your Website</a>";
}
echo "</div>";
if (!$sessISMOBILE){
  echo "</td><td style='width:30%;padding-left:1.4em;'>";
}
else {
  echo "<p/>";
}
function showDigListing($digID){
    global $sKey,$scope,$wzUserID;
    global $userID,$mtFranCID,$cityID;

    $weblWSID = null;
    if ($scope == 'myCity'){
      $searchStr = " (tblWebsites.cityID = ".$cityID." or weblCityID = ".$cityID.") ";
      $weblWSID = 'weblCityID,';
    }
    $SQL = "SELECT ".$weblWSID."Category2.name, wzUserID,websiteID,cityName,mWebFlg, nComments,URL,date(respDate)respDate,";
    $SQL .= "timestampdiff(day,wsLastContact,now())lastUpdate,Title,oldCategoryID,averageHits, wsRatingID, ";
    $SQL .= "wsImgFlg,approvalRating, description  FROM tblWebsites  ";
    $SQL .= "inner join tblCity  on tblCity.cityID=tblWebsites.cityID ";
    if ($scope == 'myCity'){
      $SQL .= "left join tblWebsiteLoc  on websiteID = weblWSID ";
    }
    $SQL .= "inner join Category2  on categoryID = oldCategoryID ";
    $SQL .= "where websiteID = ".$digID;

    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $SQL = "select rating, minAge from tblwsRatings  where wsRatingID=".$tRec['wsRatingID'];
    $wsRec = null;
    $wsresult = mkyMsqry($SQL);
    $wsRec = mkyMsFetch($wsresult);

    $localService = null;
    if ($scope == 'myCity'){
      if ($tRec['weblCityID']){
        $localService = ' (Local Service Available) ';
      }
    }

    $strViewRating = "Not Rated";
    if ($wsRec) {
      $strViewRating = $wsRec['rating']." ".$wsRec['minAge']."+";
    }

    $wsImgStr = "";
    if ( $tRec['wsImgFlg'] == 1 ) {
      $wsAnkor= " <a target='_blank' href='".fetchWebsiteURL($tRec['websiteID'],true)."'  rel='nofollow'>";
      $wsImgStr = $wsAnkor."<img style='float: left; width:100%;margin-bottom: 1emp; margin-top: 0em;";
      $wsImgStr .= "border-radius:.5em; margin-right: 8px; vertical-align: top;' src='https://image.bitmonky.com/getWsImg.php?id=".$tRec['websiteID']."'></a>";
    }

    $URL = $tRec['URL'];

    //if ( $tRec['mWebFlg'] == 1 ) {
    //  $URL = "bitmonky.com/".$URL;
    //}

    echo "<div class='infoCardClear' style='background:#151515;margin-bottom:1em;'>";
    if (!$tRec['respDate']){
      $tRec['respDate'] = 'Never';
    }
    $lastUp = $tRec['lastUpdate'];

   echo "<div align='right' class='infoCardClear' style='background:#222222;color:darkKhaki;font-size:smaller'>Last Crawled - ".$tRec['respDate'];
   if ($lastUp !== null){
     if ($lastUp == 0){$lastUp="Last Updated: Today";}
     else {$lastUp = "Last Updated: ".$lastUp." Days";}
    echo "<br/>".$lastUp;
   }
   if ($userID == 17621){
     echo "<br/><a href='javascript:markWebForDelete(".$tRec['websiteID'].");'>Mark Deleted</a>";
   }
   echo "</div>";
    echo "<table style='width:100%;'><tr valign='top'>";
    echo "<td style='' align='left'>".$wsImgStr."";
    echo "<br clear='left'/>";
    echo "<p/><a style='font-size:larger;' target='_blank' oncontextmenu='xxfetchPics(".$tRec['websiteID'].")' ";
    echo "href='".fetchWebsiteURL($tRec['websiteID'],true)."'  rel='nofollow'>".splitLWordsNoTag($tRec['Title'])."</a>";
    echo "<br>".splitLWords($tRec['description']);
    echo "<p/><b><span style='color:darkKhaki'>Location:</span></b> ".$tRec['cityName'].$localService;
    echo "<br><b><span style='color:darkKhaki'>Category:</span></b> ";
    echo " <a ID='wzBold_A' href='myTown.php?fmyMode=web&wzID=".$sKey."&fscope=".$scope."&franCID=".$mtFranCID;
    echo "&fwzUserID=".$wzUserID."&fcatID=".$tRec['oldCategoryID']."'>".$tRec['name']."</a>";
    echo " | <b><font color='darkKhaki'>Audience </font>: </b>".$strViewRating;
    echo "<br></font> ";
    echo "</td></tr></table>";
    echo "</div>";
}
?>
    </TD>
  </tr>
</table> 
</div>
<script>
function fetchPics(id){
  window.scrollTo(0,0);
  parent.window.scrollTo(0,0);
  document.location.href='/wzAdmin/testpars.php?wzID=<?php echo $sKey;?>&wsID=' + id;
}
</script>
