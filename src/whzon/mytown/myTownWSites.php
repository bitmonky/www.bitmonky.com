<?php
include_once('newsHLObjs.php');
$pgSQry = "?".safeSRV('QUERY_STRING');
$catStr = null;
$curCat = "<p/>";

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
<p>
<div class='infoCardClear'>
<B><span class=wzBold style='color:darkKhaki;'>Websites In This <span style='background:#151515;padding:.25em;border-radius:.25em;'><?php echo $scopeDisplay;?></span></span> - See Also</b> 
<?php 
drawMyMenu($myMode,$modes);
?>
  <div style='margin-top:.5em;'>
  <form method='GET' action=''>
  <input type='hidden' name='wzID'      value='<?php echo $sKey;?>'/>
  <input ID='accQryStr' class='srchBox' type='text'  onkeydown="return (event.keyCode!=13);"
   name='fsq'   onfocus='this.value=""'  value='<?php echo $fsq;?>' 
   placeholder='Search Websites' style='font-size:larger;font-weight:bold;width:55%;'/>
  <input type='hidden' name='catID'     value='<?php echo $catID;?>'/>
  <input type='hidden' name='fscope'    value='<?php echo $scope;?>'/>
  <input type='hidden' name='franCID'   value='<?php echo $cityID;?>'/>
  <input type='hidden' name='fmyMode'   value='web'/>
  <input type='hidden' name='fwzUserID' value='<?php echo $userID;?>'/>
  <input class='srchButton' type='button' style='padding:.65em;vertical-align:top;' onclick='doAccQry(0,true,"web")' value=' Search '/>
  <input class='srchButton' type='submit' style='padding:.65em;vertical-align:top;' name='spin' value=' Spin '/>
  </form>
</div>
</div>
<div ID='accQrySpot'></div>
<?php echo $curCat;?>

<table style='margin-top:3.5em;width:100%'><tr valign='top'>

<?php 
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
$weblWSID = null;
if ($scope == 'myCity'){
  $searchStr = " (ndxwCityID = ".$cityID." or weblCityID = ".$cityID.") ";
  $weblWSID = 'weblCityID,';
}
/*
if ( $searchFlg == "" ) {
  
  $where  = " where mrkDelete is null and responseCD = '200' ";
  $where .= "and  ".$searchStr;

  $SQL = "SELECT ".$weblWSID."Category2.name, wzUserID,websiteID,tblCity.name cityName,mWebFlg,date(respDate)respDate, ";
  $SQL .= "timestampdiff(day,wsLastContact,now())lastUpdate,nComments,URL,Title,oldCategoryID,averageHits, wsRatingID, ";
  $SQL .= " wsImgFlg,approvalRating, description  ";
  $SQL .= "FROM tblWebsites  ";
  if ($fsq){
    $SQL .= "inner join ( select sum(prcwZeroWT * power(prcwLen,2))nRes, objpItemID wsID ";
    $SQL .= "from tblObjPreIndex  ";
    $SQL .= "inner join tblpreIndexCWords  on prcwWord = objpWord ";
    $SQL .= "where tblObjPreIndex.objpName='web' ".$fsqry;
    $SQL .= "group by  objpItemID ";
    $SQL .= ")R on wsID = websiteID ";
  }
  if ($scope == 'myCity'){
    $SQL .= "left join tblWebsiteLoc  on ndxwWebsiteID = weblWSID ";
  }
  $SQL .= "left join tblCity  on tblCity.cityID=tblWebsites.cityID ";
  $SQL .= "inner join Category2  on categoryID = oldCategoryID ";
  $SQL .= $where.$catStr." ";
  $SQL .= "order by ";
  if ($fsq){
    $SQL .= "wsImgFlg desc, R.nRes desc ";
  }
  else {
    $SQL  .= "wsImgFlg desc, websiteID desc";
  }
} 
else {
  $where  = " where not wsLastContact is null and mrkDelete is null and responseCD = '200' and ";
  $where .= " mWebFlg=0 and tblCityGroup.cityID=".$myCityID;

  $SQL = "SELECT wzUserID,websiteID,tblCity.name cityName,mWebFlg, nComments,URL,Title,oldCategoryID,averageHits, wsRatingID,date(respDate)respDate, ";
  $SQL .= "timestampdiff(day,wsLastContact,now())lastUpdate,wsImgFlg,approvalRating, description FROM tblWebsites  inner join tblCityGroup  on gCityId=tblWebsites.cityID ";
  $SQL .= "inner join Category2  on oldCategoryID = categoryID ";
  $SQL .= $where.$catStr." ";
  $SQL .= "order by wsImgFlg desc, websiteID desc";
}
if ($spin){
  $SQL = mkyStrIReplace('wsImgFlg desc,','',$SQL);
  $SQL = mkyStrIReplace('order by ','order by wsImgFlg desc, rand(), ',$SQL);
//  $SQL = mkyStrIReplace('where ','where  not wsLastContact is null and ',$SQL);
//  $SQL = mkyStrIReplace('where mrkD','where NOT (wzUserID = 0 or wzUserID = 63555) and mrkD',$SQL);
}
*/
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

  $SQL  = "SELECT 1 wsImgFlg,ndxwCategory name,ndxwUID wzUserID,ndxwWebsiteID websiteID,ndxwCity cityName,ndxwURL URL,ndxwProt,ndxwTitle ";
  $SQL .= "Title,ndxwCategoryID oldCategoryID,ndxwDesc description, ";
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

if ($userID == 17621){
  //echo $SQL;
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

$i = 0;
$nRows = 10;
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
    $profile = null;
    if ($tRec['wzUserID'] != 63555 && $tRec['wzUserID'] != 0){
      $profile_A = "<a style='font-size:14px;' href='javascript:wzLink(\"/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."\");'>";
      $profile  = $profile_A.'<img title="View '.$tRec['wzUserID'].'`s profile" ';
      $profile .= 'style="float:right;border-radius:50%;width:36px;height:45px;margin:0em 0em 1em 1em;" ';
      $profile .= " src='".$GLOBALS['MKYC_imgsrv']."/getMbrImg.php?id=".$tRec['wzUserID']."'></center></a>";
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
  echo $profile;
  echo "<a style='font-size:larger;' target='_blank' oncontextmenu='xxfetchPics(".$tRec['websiteID'].")' ";
  echo "href='".fetchWebsiteURL($tRec['websiteID'],true)."' rel='nofollow' >".splitLWordsNoTag($tRec['Title'])."</a>";
  echo "<br>".splitLWords($tRec['description']); 
  echo "<p/><b><span style='color:darkKhaki'>Location:</span></b> ".$tRec['cityName'].$localService;
  echo "<br><b><span style='color:darkKhaki'>Category:</span></b> ";
  echo " <a ID='wzBold_A' href='myTown.php?fmyMode=web&wzID=".$sKey."&fscope=".$scope."&franCID=".$mtFranCID;
  echo "&fwzUserID=".$wzUserID."&fcatID=".$tRec['oldCategoryID']."'>".$tRec['name']."</a>";
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
  
$SQL = "SELECT count(*) as nSites, ndxwCategoryID as catID,Category2.name from ndxWeb.ndxWebsites  ";
if ($scope == 'myCity'){
  $SQL .= "left join tblWebsiteLoc  on ndxwWebsiteID = weblWSID ";
}
$SQL .= "left join tblCity  on tblCity.cityID=ndxwCityID ";
$SQL .= "inner join Category2  on ndxwCategoryID = categoryID ";
$SQL .= $ndxwhere; // and wsImgFlg=1 ";
$SQL .= "group by ndxwCategoryID,Category2.name ";
$SQL .= "order by nSites desc limit 30";

$SQL = mkyStrReplace('tblWebsites.stateID','ndxwStateID',$SQL);
$SQL = mkyStrReplace('tblWebsites.countryID','ndxwCountryID',$SQL);

if ($userID == 17621){
  //echo $SQL;
}

$tRec = null;
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);

echo "<div class='infoCardClear' style='padding:.8em;'>";
echo "<div align='right'><a href='javascript:wzLink(\"myTown.php?fmyMode=web&wzID=".$sKey."&fwzUserID=".$wzUserID."&franCID=".$mtFranCID."\");'>Show All</a></div>";
echo "<p/><font color='white'><b>Top Categories</b></font>";
echo "<p>";

$i = 0;
$nRows = 40;

While ($tRec && $i < $nRows){
  echo "<div style='margin:.15em;padding:.54em;background:#222222;border-radius:.35em;Font-size:larger;'>";
  echo "<a style='color:darkKhaki;font-size:larger;font-weight:normal' href='javascript:wzLink(\"myTown.php?franCID=".$mtFranCID."&fmyMode=web&wzID=".$sKey;
  echo "&fwzUserID=".$wzUserID."&fcatID=".$tRec['catID']."\");'>".left($tRec['name'],20)."";
  echo "[".$tRec['nSites']."]</a></div>";

  $i = $i + 1;
  $tRec = mkyMsFetch($result);
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
      $wsImgStr .= "border-radius:.5em; margin-right: 8px; vertical-align: top;' src='".$GLOBALS['MKYC_imgsrv']."/getWsImg.php?id=".$tRec['websiteID']."'></a>";
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
