<?php
    if (isset($_GET['franCID'])) { $mtFranCID  = safeGetINT('franCID');} else {$mtFranCID=null;}
    if (isset($_GET['fsmCID']))  { $fsmCID     = safeGetINT('fsmCID');}  else {$fsmCID = null;}
    if ($mtFranCID){
      $cityID = $mtFranCID;
    }
    if ($fsmCID){
      $cityID = $fsmCID;
    }
    putSession("myTownCity",$cityID);	

    $SQL = "SELECT tblCity.Name,tblCity.lat,tblCity.`long`,tblCity.StateID,MetroID,worldRegionID,tblCity.CountryID, ";
    $SQL .= "tblCountry.name countryName,tblState.name stateName From tblCity ";
    $SQL .= "Inner Join tblState  on tblCity.StateID=tblState.stateID ";
    $SQL .= "Inner Join tblCountry  on tblCity.countryID=tblCountry.countryID ";
    $SQL .= "where cityID=".$cityID;

    $tRec = null;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $cityNOT = "y";
    if ($tRec){
      $cityNOT     = "n";
      $myCityname  = safeQuotes($tRec['Name']);
      $myLat       = $tRec['lat'];
      $mylong      = $tRec['long'];
      $myCountryID = $tRec['CountryID'];
      $myStateID   = $tRec['StateID'];
      $myCountry   = safeQuotes($tRec['countryName']);
      $myState     = safeQuotes($tRec['stateName']);
      $myMetroID   = $tRec['MetroID'];
      $metroID     = $tRec['MetroID'];
      $stateID     = $tRec['StateID'];
      $countryID   = $tRec['CountryID'];
      $wdRegionID  = $tRec['worldRegionID'];
    }
    else {
      header('Location: /whzon/public/homepg.php?wzID='.$sKey);
      exit('');
    }
//=========================================
// My Town Scope management
//=========================================
    $scopes = array ("myCity","myState","myCountry","myWorld");
    if (mkyStripos($whzdom,'bitmonky.com') !== false){
//    $modes  = array ("chan"=>"Channels","class"=>"Classifieds","web"=>"Websites","mbrs"=>"People","photo"=>"Photos","mBlog"=>"Blogs","mosh"=>"Music","mshare"=>"Shares","video"=>"Videos");
//    if ($userID == 17621 | $userID == 50683){
      $modes  = array ("store"=>"Stores","chan"=>"Channels","class"=>"Classifieds","web"=>"Websites","mbrs"=>"People","photo"=>"Photos","mBlog"=>"Blogs","mosh"=>"Music","mshare"=>"Shares","video"=>"Videos","boost"=>"Boosts");
//    } 
    }
    else {
      $modes  = array ("mosh"=>"Music","video"=>"Videos","class"=>"Classifieds");
    }
function drawMyMenu($mode,$modes){
   global $sKey,$wzUserID,$sessISMOBILE;
   if ($sessISMOBILE){
     ?>
     <!--
     <a style='font-size:larger;color:white;' 
     href='javascript:wzLink("/whzon/mblp/mytown/myTownProfile.php?wzID=<?php echo $sKey."&fscope=myWorld&fwzUserID=".$wzUserID;?>");'>
     <img alt="View Profile" style="float:right;width:30;height:38;margin:3px;margin-left:6px;border-radius:50%;"
     src="//image.bitmonky.com/img/iconScope.png?id=<?php echo $wzUserID?>"></a>
     -->
     <?php
   }
   foreach ($modes as $mod => $mtx){
     if ($mode !== $mod){
       echo "| <a href='javascript:wzLink(\"".changeModeTo($mod),"\");'>".$mtx."</a> ";
     }
   }
   echo "<br clear='right'/>";
}
 
    $searchStr  = " tblWebsites.cityID =".$cityID." ";
    $userSearch = " tblwzUser.cityID=".$cityID." ";
    if ($myMetroID != 0){
      $searchStr=  " tblCity.metroID = ".$myMetroID." ";
      $userSearch = $searchStr;
    }

    if (isset($_GET['fscope'])){ $scope = clean($_GET['fscope']);} else {$scope='';}
    if (isset($_GET['fcatID'])){ $catID = safeGET('fcatID');} else {$catID='';}
    if ($scope != ""){
      putSession("myTownScope",$scope);
    }
    else {
      $scope = getSess("myTownScope");
    }

    if ($scope == "myCity"){
      $scopeDisplay = "Town";
      $searchStr = " tblWebsites.cityID=".$cityID." ";
      $userSearch = " tblwzUser.cityID=".$cityID." ";
      $eventSearch = " tblwzEvent.cityID=".$cityID." ";
      $classSearchStr = " tblClassifieds.cityID=".$cityID." ";
      $storeSearch    = " tblStore.storeCityID=".$cityID;
      $feedData       = "&facCity=".$cityID;
    }

    if ($myMetroID != 0){
      $searchStr =  " tblCity.metroID = ".$myMetroID." ";
      $userSearch = $searchStr;
      $storeSearch = " storeMetroID = ".$myMetroID." ";
      $feedData       = "&facMetro=".$myMetroID;
    }

    if ($scope == "myState" ) {
      $scopeDisplay = "Region";
      $searchStr = " tblWebsites.stateID=".$myStateID." ";
      $classSearchStr = " tblClassifieds.stateID=".$myStateID." ";
      $userSearch = " tblwzUser.stateID=".$myStateID." ";
      $eventSearch = " tblCity.StateID=".$myStateID." ";
      $storeSearch = " storeStateID=".$myStateID." ";
      $feedData       = "&facState=".$myStateID;
    }

    if ($scope == "myCountry"  ){ //|| $scope == "" ) {
      $scopeDisplay = "Country";
      $searchStr = " tblWebsites.countryID=".$myCountryID." ";
      $classSearchStr = " tblClassifieds.countryID=".$myCountryID." ";
      $userSearch = " tblwzUser.countryID=".$myCountryID." ";
      $eventSearch = " tblCity.countryID=".$myCountryID." ";
      $storeSearch = " storeCountryID=".$myCountryID." ";
      $feedData       = "&facCountry=".$myCountryID;
    }

    if ($scope == "myWorld" || $scope == "" ) {
      $scopeDisplay ="World";
      $searchStr = " 1=1 ";
      $classSearchStr = " 1=1 ";
      $eventSearch = " 1=1 ";
      $userSearch = " 1=1 ";
      $storeSearch = " 1=1 ";
      $feedData    = "";
    }

//-----------------------------------
//End Scope

if (isset($_GET['fmyMode'])){$myMode = clean($_GET['fmyMode']);} else { $myMode = "";}
if ($myMode != "" ) {
  putSession("myTownMode",$myMode);
}
else {
  $myMode = getSess("myTownMode");
}
	
    $bgImg = '';
    $margTop = '60px';
    $useBGStoreFile = 1;
    $cprText = null;
    $cprURL  = null;
	
    $mRec = null;
    $tryScope = null;
    $ntry = 0;
    while (!$mRec && $ntry < 6){ 
      $SQL = "select bannerID,useSysBanID,yoffset,height,cropMd5ID from ICDimages.tblStoreBanner where useSysBanID is null and uploadStatus=1 and fieldname=fieldValue";
      scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID,$tryScope);
      $myresult = mkyMyqry($SQL);
      if ($myresult){$mRec = mkyMyFetch($myresult);}
      $tryScope = $scope;
      $tryScope = reduceScope($tryScope);
      $ntry = $ntry + 1;
    }
      	
    if (!$mRec){
      $useBGStoreFile = null;	
      $SQL = "select bannerID,useSysBanID,yoffset,height,cropMd5ID from ICDimages.tblwzBannerImg where udefault = 1 and bannerUID=".$wzUserID;
      $myresult = mkyMyqry($SQL);
      if ($myresult){$mRec = mkyMyFetch($myresult);}
    }
    else {
      $crBannerID = $mRec['bannerID'];
      $SQL = "select cprText,cprURL from tblCopyRightCredits  where NOT cprTaskStatus is null and cprAcCode=20 and cprAcItemID=".$crBannerID;
      $resultCR = mkyMsqry($SQL);
      $crRec = mkyMsFetch($resultCR);
      if ($crRec){
        $cprText = $crRec['cprText'];
       $cprURL  = $crRec['cprURL'];
      }
    }
    if($mRec){
      if ($mRec['useSysBanID']){
        $SQL = "select bgName,yOffset from tblDefBackground  where bgID = ".$mRec['useSysBanID'];
        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);
        $bgImg    = $GLOBALS['MKYC_imgsrv'].'/img/'.$tRec['bgName'];
        $bgOffset = $tRec['yOffset'];
      }
      else {
        $bgImg = $GLOBALS['MKYC_imgsrv']."/getStoreBGImg.php?mode=view&wzID=".$sKey."&id=".$mRec['bannerID'];
        if (!$useBGStoreFile){
          $bgImg    = $GLOBALS['MKYC_imgsrv']."/whzon/mbr/getBGImg.php?mode=view&id=".$mRec['bannerID'];
        }
        $bgOffset = 0;
        $bgImgID  = $mRec['bannerID'];
      }
      $margTop = '210px';
    }
    else {
      $SQL = "select count(*) as nRec from tblDefBackground ";
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);

      $nbg = $tRec['nRec'];
      $pick = rand(1,$nbg);

      $SQL = "select bgID,bgName,yOffset from tblDefBackground ";
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);

      $n = 1;
      while ($n < $pick){
        $tRec = mkyMsFetch($result);
        $n = $n + 1;
      }
      $bgImg    = $GLOBALS['MKYC_imgsrv'].'/img/'.$tRec['bgName'];
      $bgOffset = $tRec['yOffset'];
      $margTop = '210px';
    }
$chMode = "&fmyMode=".$myMode;
if ($myMode == ""){
  $chMode = '&fmyMode=all';
}
$linkRoot = "?wzID=".$sKey."&franCID=".$cityID.$chMode."&fscope=".$scope."&fwzUserID=".$wzUserID;
function changeModeTo($mode){
  global $fsqPg;
  global $linkRoot;
  global $chMode;
  $url = mkyStrReplace($chMode,"&fmyMode=".$mode,$linkRoot);
  return "myTown.php".$url.$fsqPg;
}
function reduceScope($scope){
  if ($scope == 'myCity'){
    return "myState";
  }
  if ($scope == "myState") {
    return "myCountry";
  }
  return "myWorld";
}
function scopeSQL(&$SQL,$cityID,$stateID,$countryID,$metroID,$tryScope=null){
    global $scope;
    global $metroID;
    $tscope = $scope;
    if($tryScope){
      $tscope = $tryScope;
    }
    if ($tscope == "myCity"){
      $SQL = str_replace ("fieldname","bnCityID",$SQL);
      $SQL = str_replace ("fieldValue",$cityID,$SQL);
    }

    if ($metroID != 0 && $tscope == "myCity"){
      $SQL = str_replace ("fieldname","bnCityID",$SQL);
      $SQL = str_replace ("fieldValue",$cityID,$SQL);
	  return;
    }

    if ($tscope == "myState" ) {
      $SQL = str_replace ("fieldname","bnStateID",$SQL);
      $SQL = str_replace ("fieldValue",$stateID,$SQL);
    }

    if ($tscope == "myCountry"  || $scope == "" ) {
      $SQL = str_replace ("fieldname","bnCountryID",$SQL);
      $SQL = str_replace ("fieldValue",$countryID,$SQL);
    }

    if ($tscope == "myWorld"  || $scope == "" ) {
      $SQL = str_replace ("fieldname","bnWorld",$SQL);
      $SQL = str_replace ("fieldValue",0,$SQL);
    }

    if ($tscope == "myWorld" ) {
    }
}

?>

