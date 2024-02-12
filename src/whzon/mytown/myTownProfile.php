<?php
include_once("../mkysess.php");
include_once("../mbr/mbrData.php");
include_once("../gold/goldInc.php");
include_once("../franMgr/franMgrObj.php");

if ($sessISMOBILE){
  $qry = $_SERVER['QUERY_STRING'];
  $nmURL = "/whzon/mblp/mytown/myTownProfile.php?".$qry;
  header('Location: '.$nmURL);
  exit('');
}

if ($userID == 17621){
  ini_set('display_errors',1); 
  error_reporting(E_ALL);
}

$title="";
$mKeywords="";
$mDesc="";
$mobile = null;

include_once("myTownInc.php");
$tycoonID = null;
$cpCity = '&cpCity='.safeGetINT('cpCity');
$gscope = $scope; //safeGET('gscope');
if ($gscope){
  $gscope = '&gscope='.$gscope;
}
$guideID = safeGetINT('gID');
if ($guideID){
  $guideLink = '&gID='.$guideID;
}
else {
  $guideLink = '&gID=999';
}
//$guideLink .= $gscope;

if ($scope == "myWorld"){
  header('Location: /whzon/public/homepg.php?wzID='.$sKey.$guideLink);
  exit('');
}
$SQL  = "select concat(replace(lower(tblCity.name),' ','-') , '.', replace(lower(tblState.name),' ','-'), '.', lower(tblCountry.countryCD)) as cityHash ";
$SQL .= "from tblCity  ";
$SQL .= "left join tblState  on tblState.stateID = tblCity.StateID ";
$SQL .= "left join tblCountry  on tblCountry.countryID = tblCity.countryID ";
$SQL .= "where cityID = ".$cityID;
$ccityID = $cityID;

if ($scope == "myCountry" || ($scope == "myState" && $myState == "-")){
  //$SQL = "select replace(lower(tblCountry.name),' ','-') as cityHash ";
  $SQL  = "select concat ('&facCountry=',tblCity.countryID) as cityHash ";
  $SQL .= "from tblCity  ";
  $SQL .= "left join tblCountry  on tblCountry.countryID = tblCity.countryID ";
  $SQL .= "where cityID = ".$cityID;
}
else if ($scope == "myState"){
  $SQL  = "select concat('&facState=',tblCity.StateID) as cityHash ";  
  $SQL .= "from tblCity  ";
  $SQL .= "left join tblState  on tblState.stateID = tblCity.StateID ";
  $SQL .= "left join tblCountry  on tblCountry.countryID = tblCity.countryID ";
  $SQL .= "where cityID = ".$cityID;
}
else {
  $cpCity = '&cpCity='.$cityID;
  $tSQL = "Select ownerID from tblCity  where cityID = ".$cityID;
  $coresultCR = mkyMsqry($tSQL);
  $coRec = mkyMsFetch($coresultCR);
  if ($coRec){
    $tycoonID = $coRec['ownerID'];
	if ($wzUserID != $tycoonID){
	  //header('Location: myTownProfile.php?franCID=&wzID='.$sKey.'&fscope=myCity&fcatID=&fwzUserID='.$tycoonID);
	  //exit('');
	}
  }
}
$cRec = null;
$resultCR = mkyMsqry($SQL);
$cRec = mkyMsFetch($resultCR);

$cityHash = safeQuotes($cRec['cityHash']);

$photoCredit = null;
if ($cprText){
  $photoCredit = '<div align="right">';
  $photoCredit .= '<span style="font-size:smaller;">photo credit -</span> ';
  $photoCredit .= '<a target="_new" style="font-size:smaller;" href="'.$cprURL.'">'.$cprText.'</a>';
  $photoCredit .= '</div>';
}
if (isset($_SERVER['QUERY_STRING'])){$qry = "?".$_SERVER['QUERY_STRING'];}else {$qry="";}
$furl = $_SERVER['SCRIPT_NAME'].$qry;
setHeaderTags($furl);

if ($sessISMOBILE){
  include("../mblp/mblTemplate.php");
  echo "<div style='padding:.5em;'>";
}
else {
  ?>
  <!doctype html>
  <html class='pgHTM' lang="en">
  <head>
  <meta charset="utf-8"/>
  <link rel="canonical" href='<?php echo $GLOBALS['MKYC_canon'].'/whzon/wzApp.php?furl='.urlencode($furl);?>'/>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>
  <script src="/whzon/sessmgrJS.php"></script>
  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script src='../wzToolboxJS.php'></script>
  <title><?php echo $mPgTitle;?></title>
  <meta name="keywords" content="<?php echo $mKeywords;?>"/>
  <meta name="description" content="<?php echo $mPgDesc;?>"/>
  <meta property="og:title" content="<?php echo $mPgTitle;?>"/>
  <meta property="og:url" content="https://www.bitmonky.com?furl=<?php echo urlencode($furl);?>"/>
  <meta property="og:image" content="<?php echo $mPgImage;?>"/>
  <meta property="og:description" content="<?php echo $mPgDesc;?>" />

  <script>
  function hideImageID(id){
    var img = document.getElementById(id);
    if (img){
      img.style.display = 'none';
    }
  }
  <?php
  include_once("../frameHandlerJS.php");
  ?>
  </script>
  </head>
  <body class='pgBody' style='background:#222222;' onload='frameInit();'>
  <?php
}
?>
<script>
  var feedTimer=null;
  var acFeedTime = null;

  function jumpThisClaim(franID){
     var conf = confirm('Take This City From This Owner And Start Your Own Free Trial As This Cities Tycoon?');
     if (conf){
       var jmpCon = parent.getHttpConnection();
       var currentTime = new Date();
       var ranTime = currentTime.getMilliseconds();
       var url = '/whzon/franMgr/jumpFranchiseClaim.php?wzID=' + parent.sID + '&franID=' + franID + '&xm=' + ranTime ;
       jmpCon.open("GET", url,true);
       jmpCon.onreadystatechange = function(){
         if (jmpCon.readyState == 4){
           if(jmpCon.status  == 200){ 
             var res = mkyTrim(jmpCon.responseText);
             alert(res);
             document.location.reload();
           }
         }
       };
       jmpCon.send(null);
     }
  }
  function hideVisualSearch(){
    var div = document.getElementById('visualSearchDisplay');
    if (div){
      div.style.display = 'none';
    }
  }
  function showVisualSearch(){
    var div = document.getElementById('visualSearchDisplay');
    if (div){
      div.style.display = 'block';
    }
  }
  function hideHeadLines(){
    var div = document.getElementById('newsHeadLines');
    if (div){
      div.style.display = 'none';
    }
  }
  function showHeadLines(){
    var div = document.getElementById('newsHeadLines');
    if (div){
      div.style.display = 'block';
    }
  }
  function swapFailedmshareImg(id){
    var img = document.getElementById('mshare'+id);
    if (img){
      img.style.display  = 'none';
    }
  }
  function goToCountryChat(){
     var edUrl = '/whzon/mytown/fetchCountryChat.php?wzID=' + parent.sID + '&countryID=<?php echo $countryID;?>';
     var edDiv = 'cityChatRoomSpot';
     parent.updateDivJSON(edUrl,edDiv);
  }
  function goToStateChat(){
     var edUrl = '/whzon/mytown/fetchStateChat.php?wzID=' + parent.sID + '&stateID=<?php echo $stateID;?>';
     var edDiv = 'cityChatRoomSpot';
     parent.updateDivJSON(edUrl,edDiv);
  }
  function goToCityChat(){
     var edUrl = '/whzon/mytown/fetchCityChat.php?wzID=' + parent.sID + '&cityID=<?php echo $cityID;?>';
     var edDiv = 'cityChatRoomSpot';
     parent.updateDivJSON(edUrl,edDiv);
  }
  function jsonHandler(j){
    if (j.chanID){
      var conf = 1;//confirm('Confirm Change Current Channel?');
      if (conf){
        parent.wzChangeChannel(j.chanID);
      }
    }
  }
function popPurchaseCity(cityID) {
   parent.window.scrollTo(1, 0);
   parent.window.document.body.style.zoom = "100%";
   parent.wzAPI_showFrame("/whzon/franMgr/popPurchaseCity.php?wzID=<?php echo $sKey;?>&fcityID=" + cityID, 500, 670, 30, 80);
}
function acListenToSong(url,id){
	if (feedTimer) { clearTimeout(feedTimer);}
    feedTimer = setTimeout('fetchUF()',10*60*1000);
    var output = document.getElementById('acMoshViewer' + id);
    if (output){
      output.innerHTML = '<iframe width=" 100%" height="250" src="' + url + '?autoplay=1" frameborder="0" allowfullscreen=""></iframe><div align="right"><a href="javascript:acCloseSong(' + id + ');">close[x]</a></div>';
    }
  }
  function acCloseSong(id){
	if (feedTimer) { clearTimeout(feedTimer);}
    feedTimer = setTimeout('fetchUF()',acFeedTime);
    var output = document.getElementById('acMoshViewer' + id);
    if (output){
      output.innerHTML = '';
    }
  }
function pcInit(){
  if (typeof wzOnLoad == 'function') {wzOnLoad();}
}
function wzOpenNewLOG(URL){
  location=URL;
}

function wzPopChat(fID){
  if (!fID) fID=<?php echo $wzUserID;?>; 
  var URL="https://www.bitmonky.com/whzon/public/OSchatreq.php?fuID=<?php echo $userID; ?>&fmbrID=" + fID;
  window.location.href=URL;
}
</script>
<?php 
if ($bgImg != ''){
  ?>
  <img ID="bannerHolder" style="width:100%;height:200px;" src="<?php echo $bgImg;?>"/>
  <?php
}
?>
<div id='cityChatRoomSpot' style='display:none;'></div>
<!--    
    <div id='franBannerInfo' style='background: rgba(255, 255, 255, 0.75);position:fixed;top:0px;left:0px;width:100%;'>
    <b style='font-size:larger;color:#222222;'> 
    <a style='font-size:larger;' href='javascript:wzLink("/whzon/mbr/mbrProfile.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $wzUserID?>");'>
    <img alt="View Profile" style="float:left;width:30;height:38;margin:3px;margin-right:6px;" src="//image.bitmonky.com/getMbrTmn.php?id=<?php echo $wzUserID?>">
    Welcome To <?php echo $primContact; ?>'s</a> <?php echo $scopeDisplay;?></b>
    
    | <a  href='javascript:wzLink("myTownProfile.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fscope=myCity&fcatID=<?php echo $catID;?>&fwzUserID=<?php echo $wzUserID?>");'><?php echo $myCityname?></a>,
      <a  href='javascript:wzLink("myTownProfile.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fscope=myState&fcatID=<?php echo $catID;?>&fwzUserID=<?php echo $wzUserID; ?>");'><?php echo $myState?></a>, 
	  <a  href='javascript:wzLink("myTownProfile.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fscope=myCountry&fcatID=<?php echo $catID; ?>&fwzUserID=<?php echo $wzUserID; ?>");'><?php echo $myCountry; ?></a>
   <?php if ($scope != "myWorld" ){?>
	  Return To: <a  href='javascript:wzLink("myTownProfile.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fscope=myWorld&fcatID=<?php echo $catID; ?>&fwzUserID=<?php echo $wzUserID; ?>");'>World View</a>
   <?php }?>
   <br>
   <a href='javascript:wzLink("/whzon/mbr/mbrProfile.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $wzUserID; ?>");'>Return To <?php echo $primContact; ?>'s Profile</a> 
   </div>
-->
<div style='padding:0em .5em 0em .5em;'>
<table style='width:100%;margin-top:0px;'><tr valign='top'>
<td>
<?php

echo $photoCredit;

if ($myMode != "qry")  {
  ?>
  <div style=''>
  <?php
   if (!$sessISMOBILE){
   echo "<B><span class=wzBold>Recent Members</span></b><p>";

   $searchFlg = "";

   if ($userSearch == " 1=1 ") {

     $SQL = "SELECT wzUserID,firstname, imgFlg FROM tblwzUserRecent order by lastOnline desc limit 12;";
   }
   else
   {
     if (strrpos($userSearch, "metroID") === false){
       $SQL = "SELECT tblwzUser.wzUserID,tblwzUser.firstname, tblwzUser.imgFlg ";
       $SQL .= " from tblwzUser  ";
       $SQL .= " left join tblwzOnline  on tblwzUser.wzUserID = tblwzOnline.wzUserID ";
       $SQL .= " where ".$userSearch." and  tblwzUser.sandBox is null ";
       $SQL .= " order by  tblwzUser.imgFlg desc,online desc,lastOnline desc limit 12;";
	   }
	   else {
       $SQL = "SELECT tblwzUser.wzUserID,tblwzUser.firstname, tblwzUser.imgFlg ";
       $SQL .= " from tblwzUser  ";
       $SQL .= " left join tblwzOnline  on tblwzUser.wzUserID = tblwzOnline.wzUserID ";
	     $SQL .= " inner join tblCity on tblCity.cityID = tblwzUser.cityID "; 
       $SQL .= " where tblCity.metroID = ".$myMetroID." and  tblwzUser.sandBox is null ";
       $SQL .= " order by  tblwzUser.imgFlg desc,online desc,lastOnline desc limit 12;";
	   }
   }
   
   $tRec = null;
   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);
   $i = 0;
   $scrWidth = "width:100%;";
   if ($mobile) {
     $nRows = 9;
   }
   else
   {
     $nRows = 12;
   }

   echo "<p><table style='border-collapse: collapse; padding: 0px; border: 0px solid;width:100%' >";


   echo "<tr  valign='top'>";

   While ($tRec && $i < $nRows)
   {
      $wsImgStr="";
      $wsAnkor= " <a  href='javascript:wzLink(\"/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."\");'>";
      $wsImgStr= $wsAnkor."<img style='margin-right:4px;border-radius:50%;width:60px;height:80px;' src='//image.bitmonky.com/getMbrImg.php?id=".$tRec['wzUserID']."'></a>";
      echo "<td style='padding:0px;color:white;font-size:10px;border: 0px solid #777777;'>".$wsImgStr;
      echo "</td>";
      $i = $i + 1;
      $tRec = mkyMsFetch($result);
   }
   echo "</tr></table>";
  }
  ?>
  <p/>
  <style>.AH1{font-size:1em;font-weight:bold;color:darkKhaki}</style>
  <div class='infoCardClear'>
  <div align='right'>
  <b ID='searchBox' class='a.Title'>Change:</b>
  <a class='a.Title' href='javascript:wzAPI_showFrame("/whzon/mytown/selCountryAutoT.php?wzID=<?php echo $sKey;?>",300,400,100,100,"searchBox");'>Country</a>
  | <a class='a.Title' href='javascript:wzAPI_showFrame("/whzon/mytown/selCityAutoT.php?wzID=<?php echo $sKey;?>",300,400,100,100,"searchBox");'>City</a>
  | <a class='a.Title' href='javascript:wzLink("myTownProfile.php?wzID=<?php echo $sKey;?>&fscope=myWorld&fcatID=<?php echo $catID?>&fwzUserID=<?php echo $wzUserID?>");'>Go World Wide</a>
  </div>
  <h1><b>Location:</b>
<?php
if ($scopeDisplay == 'Town'){
  ?>
  <a class='AH1' href='javascript:wzLink("myTownChangeTo.php?wzID=<?php echo $sKey."&fcityID=".$cityID;?>");'><?php echo $myCityname?></a>,
  <a class='AH1' href='javascript:wzLink("myTownChangeTo.php?wzID=<?php echo $sKey."&fprovID=".$stateID;?>");'><?php echo $myState?></a>,
  <a class='AH1'  href='javascript:wzLink("myTownChangeTo.php?wzID=<?php echo $sKey."&fcountryID=".$countryID;?>");'><?php echo $myCountry; ?></a>
  <?php
}
else if ($scopeDisplay == 'Region'){
  ?>
  <a class='AH1' href='javascript:wzLink("myTownChangeTo.php?wzID=<?php echo $sKey."&fprovID=".$stateID;?>");'><?php echo $myState?></a>,
  <a class='AH1'  href='javascript:wzLink("myTownChangeTo.php?wzID=<?php echo $sKey."&fcountryID=".$countryID;?>");'><?php echo $myCountry; ?></a>
  <?php
}
else if ($scopeDisplay == 'Country'){
  ?>
  <a class='AH1'  href='javascript:wzLink("myTownChangeTo.php?wzID=<?php echo $sKey."&fcountryID=".$countryID;?>");'><?php echo $myCountry; ?></a>
  <?php
}
else {
  echo "<a class='AH1' href='javascript:wzLink(\"myTownProfile.php?wzID=0&fscope=myWorld&fcatID=0&fwzUserID=949098\");'>World Wide</a>";
}
?>
  - Scope <span style='color:darkKhaki;'><?php echo mkyStrReplace('My ','',$scopeDisplay);?></span>
  </h1>
  <?php
}
else {
  echo "<div class='infoCardClear'>";
}
if ($userID == 17621 || $userID == 63555){
   //echo "<a class='a.Title' href='javascript:wzLink(\"myTownEditor.php?wzID=".$sKey."&fscope=".$scope."&fcatID=".$catID."&fwzUserID=".$wzUserID."\");'>Edit</a>";
}
   
if (isset($_GET['newPg'])){$pg = clean($_GET['newPg']);} else {$pg = 0;}
$nextPage = $pg;
?>
<script>
var feedConn = null;
var likesConn = null;
var newsConn = null;

function getCSCDesc(country,state=null,city=null){
  if (city)
    city = '&city='+encodeURIComponent(city);
  else city = '';
  if (state)
    state = '&state='+encodeURIComponent(state);
  else state = '';
  country = '?country='+encodeURIComponent(country);
  var url = "getCSSDesc.php"+country+state+city;
  var onexml = parent.getHttpConnection();
  var currentTime = new Date();
  var ranTime = currentTime.getMilliseconds();
  var url = "/whzon/mytown/getCSCdesc.php"+country+state+city + '&xr=' + ranTime;
  console.log('cscDesc:',url);
  onexml.timeout   = 60*1000;
  onexml.ontimeout = console.log('CSCDescTimeOut');
  onexml.open("GET", url, true);
  onexml.onreadystatechange = function(){
    if (onexml.readyState == 4){
      if(onexml.status  == 200){
        var jdata = parent.mkyTrim(onexml.responseText);
        var j = null;
        try {j = JSON.parse(jdata); }
        catch(err) {
          dbug("JSON fail: oneAlert fail -> " + jdata);
          j = null;
        }
        if (j.result){
          var spot = document.getElementById('cscDescSpot');
	  if (spot){
            spot.style.color = 'lightGray';
            spot.className   = 'infoCardClear';
	    spot.style.background = '#222222';
            spot.style.fontSize  = 'larger';
	    spot.innerHTML = j.data;
	  }
	}
      }
    }
  }  
  onexml.send(null);

}
function wzOnLoad(){
  //** Initialize Here
   parent.uloc.setScope('<?php echo $scope;?>');
   parent.uloc.setAction('locProf');
   parent.uloc.setBanner('<?php echo $bgImg;?>');
   parent.uloc.setLink('<?php echo $_SERVER['REQUEST_URI'];?>');
   parent.uloc.setNames('<?php echo $myCountry."','".$myState."','".$myCityname;?>');
   parent.uloc.setLoc(<?php echo $wdRegionID.",".$countryID.",".$stateID.",0,0,".$cityID;?>);
   parent.mkyPushDest(parent.uloc);
   parent.showRecentDest();
   console.log('location is now:',parent.ulocs);
   feedConn    = parent.getHttpConnection();
   jobConn     = parent.getHttpConnection();
   likesConn   = parent.getHttpConnection();
   newsConn    = parent.getHttpConnection();
   readFeed();

   var myCity    = "<?php echo $myCityname;?>";
   var myState   = "<?php echo $myState;?>";
   var myCountry = "<?php echo $myCountry;?>";
   <?php
   if ($scope == "myCity"){
     echo "goToCityChat();";
     echo "getCSCDesc(myCountry,myState,myCity);";
     echo "parent.fetchAdvPkg(0);";
   }
   if ($scope == "myState"){
     echo "goToStateChat();";
     echo "getCSCDesc(myCountry,myState);";
   }
   if ($scope == "myCountry"){
     echo "goToCountryChat();";
     echo "getCSCDesc(myCountry);";
   }
   if ($tycoonID) { 
     echo " readTycJobs();";
     if (!$guideID){
       echo " readTycNews();";
     }
   }
   else {
     if (!$guideID){
       echo " readCityActOnly();";
     }
   }
   ?>
}
function applyToJob(id){
  <?php 
  if($qualityScore < .5){
    echo "alert('Sorry You Need A Quality Score Of 0.5 Or Higher To Apply');";
    echo "return;";
  }
  ?>
  var con = confirm('IMPORTANT! ONLY APPLY IF - You Are Currently Residing In This City (in real life)');
  if (con){
    var edUrl = '/whzon/mytown/submitJobApp.php?wzID=' + parent.sID + '&jobID=' + id + '&cityID=<?php echo $cityID.'&tycID='.$tycoonID;?>';
    var edDiv = 'jobApp' + id;
    parent.updateDivHTML2(edUrl,edDiv);
  }
}
function hideJobs(){
  var wzout = document.getElementById('myTownTycJobs');
  if (wzout){
    wzout.innerHTML = "Online Jobs Available: <a href='javascript:readTycJobs();'>Show Jobs</a>";
    fhReDrawFrame();
  }
}
function readTycJobs(){
     var currentTime = new Date();
     var ranTime = currentTime.getMilliseconds();
     <?php
     $app = 'getTycJobOpenings.php';
     ?>
     var url = '/whzon/mytown/<?php echo $app;?>?wzID=<?php echo $sKey;?>&cityID=<?php echo $cityID;?>&tagOwner=off&xm=' + ranTime ;
     jobConn .open("GET", url,true);
     jobConn.onreadystatechange = doWriteTycJobs;
     jobConn.send(null);
}
function doWriteTycJobs(){
 
    if (jobConn.readyState == 4){
      if(jobConn.status  == 200){ 
        var html = mkyTrim(jobConn.responseText);
        var wzout = document.getElementById('myTownTycJobs');
		if (wzout){
		  wzout.innerHTML = html;
		  fhReDrawFrame();
		}
      }
    }

}
function hideNews(){
  var wzout = document.getElementById('myTownTycNews');
  if (wzout){
    wzout.innerHTML = "Local News: <a href='javascript:readTycJobs();'>Show News Headlines</a>";
    fhReDrawFrame();
  }
}
function readCityActOnly(){
     <?php
     if ($guideID && $guideID != 999){
       echo "return;";
     }
     ?>
     var spot = document.getElementById('myTownTycNews');
     var avSpace = spot.offsetWidth;
     var currentTime = new Date();
     var ranTime = currentTime.getMilliseconds();
     var url = '/whzon/mytown/newsHeadLines.php?actOnly=off&wzID=<?php echo $sKey;?>&avSpace='+avSpace+'&cityID=<?php echo $cityID.'&fscope='.$scope;?>&tagOwner=off&xm=' + ranTime ;
     newsConn .open("GET", url,true);
     newsConn.onreadystatechange = doWriteTycNews;
     newsConn.send(null);
}
function readTycNews(){
     <?php
     if ($guideID && $guideID != 999){
       echo "return;";
     }
     ?>
     var currentTime = new Date();
     var ranTime = currentTime.getMilliseconds();
     <?php
     $app = 'getTycJobOpenings.php';
     $app = "newsHeadLines.php";
     ?>
     var spot = document.getElementById('myTownTycNews');
     var avSpace = spot.offsetWidth;
     var url = '/whzon/mytown/<?php echo $app;?>?wzID=<?php echo $sKey;?>&avSpace='+avSpace+'&cityID=<?php echo $cityID.'&fscope='.$scope;?>&tagOwner=off&xm=' + ranTime ;
     newsConn .open("GET", url,true);
     newsConn.onreadystatechange = doWriteTycNews;
     newsConn.send(null);
}
function doWriteTycNews(){

    if (newsConn.readyState == 4){
      if(newsConn.status  == 200){
        var html = mkyTrim(newsConn.responseText);
        var wzout = document.getElementById('myTownTycNews');
        if (wzout){
          wzout.innerHTML = html;
          fhReDrawFrame();
        }
      }
    }

}
function readFeed(){
     var currentTime = new Date();
     var ranTime = currentTime.getMilliseconds();
     var url = '/whzon/public/mbrActivityFeed.php?pg=<?php echo $pg;?>&wzID=<?php echo $sKey.$cpCity.$gscope;?>&hQry=<?php echo $cityHash.$guideLink;?>&tagOwner=off&xm=' + ranTime ;
     feedConn.open("GET", url,true);
     feedConn.onreadystatechange = doWriteFeed;
     feedConn.send(null);
}
function doWriteFeed(){
 
    if (feedConn.readyState == 4){
      if(feedConn.status  == 200){ 
        var html = mkyTrim(feedConn.responseText);
        var wzout = document.getElementById('myTownActivities');
	if (wzout){
	  wzout.innerHTML = html;
	  fhReDrawFrame();
	}
      }
    }

}
  function activityVoteTxt(vote,acID){
     var liketxt = document.getElementById('frmlike' + acID).fliketxt.value;
	 if (liketxt == '') {
	   alert('comment on why you ' + vote + ' this!');
	 }
	 else {
       var waiting = document.getElementById('newLikeSpot' + acID);
	   if (waiting) {
	     waiting.innerHTML = '<img style="width:35px;height;35px;" src="//image.bitmonky.com/img/imgLoading.gif"/> Please Wait...';
		 newlikes = waiting;
	   }
       var currentTime = new Date();
       var ranTime = currentTime.getMilliseconds();
       var url = '/whzon/public/activityLikeUpdate.php?wzID=<?php echo $sKey;?>&fv=' + vote + '&facID=' + acID + '&fliketxt=' + liketxt + '&xm=' + ranTime ;
       likesConn.open("GET", url,true);
       likesConn.onreadystatechange = doActivityVoteTxt;
       likesConn.send(null);
	 }
  }
  function doActivityVoteTxt(){
    if (likesConn.readyState == 4){
      if (likesConn.status  == 200){ 
        parent.checkLikeStatus(likesConn.responseText);
	    if (newlikes) {
	      newlikes.innerHTML = '';
	    }
//	    clearTimeout(feedTimer);
	    readFeed();
      }
    }
  }
function wzPopScrollJoin(mbrID){
        parent.wzQuickReg();
}
function startPShare(){
  <?php if ($userID != 0){
	echo 'parent.startPShare();';
  }
  else {
    echo 'alert("You Must Be A Member To Post... Join Now Or Login.");';
    echo 'parent.wzQuickReg();';
  }?>
}
</script>
<p>
<div class='infoCardClear' style='background:#222222;'>
<B><span class=wzBold>People In My <?php echo $scopeDisplay ?> - See Also:</span></b>
<?php drawMyMenu('all',$modes);?>
</div>
</div>
<p>
<table style='margin-top:50px;width:100%'>
<tr valign='top'>
<td ID='feedContainer' style='width:50%;padding-right:15px;'>
<?php
if ($tycoonID){
  sayTodaysWeather($cityID);
  echo "<div style='' ID='myTownTycNews'>";
  //echo " <img style='width:25px;height;25px;border-radius:50%;' src='//image.bitmonky.com/img/imgLoading.gif'> Search Job Openings...	";
  echo "</div>";
}
else {
  echo "<div style='' ID='myTownTycNews'></div>";
}
?>

<div style='width:100%;margin-top:px;' ID='myTownActivities'>
<img style='width:25px;height;25px;border-radius:50%;' src='//image.bitmonky.com/img/imgLoading.gif'> Loading Latested Posts...	
</div>
<p/><b>See Also:</b> <a href='javascript:wzLink("myTown.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fmyMode=mbrs&fwzUserId=<?php echo $wzUserID ?>");'>None Tagged Posts</a> 

<?php 
echo getBigCubeAds('25px',2);
if (!$sessISMOBILE){
  echo "</td><td style='width:50%;padding-left:25px;'>";
}
else {
  echo "<p/>";
}

     	 
     //**********************
     // Show Tycoon profile
     //**********************
     if ($scope == "myCity"){
       $SQL = "select population,nUsers from tblCity  where cityID = ".$cityID;
       $cresult = mkyMsqry($SQL);
       $cRec = mkyMsFetch($cresult);
       $cityPop = null;
       if ($cRec){
         $cityPop = '<p/>Population: '.mkyNumFormat($cRec['population'],0);
         $cityPop .= '<br/>Members : '.mkyNumFormat($cRec['nUsers'],0);
       } 
       if (!$tycoonID ){
         echo "<div  class='infoCardClear'>";
         echo "<h2>Welcome To ".$myCityname."</h2>";
         echo "<div ID='cscDescSpot'></div>";
	 echo "<img style='float:right;border-radius:50%;width:85px;height:62px;margin:0em 0em 1.5em 1.5em;' src='//image.bitmonky.com/img/forSale.jpg'/>";
         echo "<h3>".$myCityname." Is For Sale!</h3>";
         echo "BitMonky is a network of legal city franchises.   Franchise owners pay a monthly fee for the rights to "; 
         echo "operate their franchise in their city or town.  <p/>This city is available which means you can purchase ";
         echo "rights to build it into your own online business using BitMonky software and servers. ";
         echo "<p/><div ID='advInvestPkg'></div>";
         echo "<div align='right'><a href='javascript:wzLink(\"/whzon/franMgr/franMgr.php?mode=reRead&wzID=".$sKey."\");'>Read More On Buying Cities</a></div>";

         if ($sessFreeTrial){
           echo "<p/><div class='infoCardClear' style='background:fireBrick;color:white;'>";
           echo "<H3>New!!! Try It Free For ".$freeTrialPeriod." Days</h3>";
           echo "Try out this city for free with no obligation to buy. ";
           echo "If it`s not right for you cancel with no penalty.";
           echo "It's a great way to find out if Tycooning on BitMonky is right for you! ";
           echo "<br/><input type='button' value='Start Free Trial Now' onclick='popPurchaseCity(".$cityID.");'/>";
           echo "</div>";
         }
	 echo "<br><a href='javascript:popPurchaseCity(".$cityID.");'>";
	 echo "<p/><img style='width:105px;height:50px;border:0px;border-radius:.5em;' src='//image.bitmonky.com/img/buynow.jpg'/></a>";
         echo "<br clear='right'>";
         showOtherTycoons();
         echo "<div align='right'><a href='javascript:wzLink(\"/whzon/franMgr/franMgr.php?mode=reRead&wzID=".$sKey."\")'>Read More On Buying Cities<a/></div>";
         echo "</div>";
       }
       else {
         include_once("myTycData.php");
         echo "<div class='infoCardClear'>";

         echo "<div align='right'>";
         franCheckClaimStatus($cityID,$userID);
         echo "</div><p/>";

         echo "<img src='".$kingQueenImg."' style='border-radius:50%;float:right;width:90px;height:63px;'/>";
         echo "<h2>Welcome To ".$myCityname."</h2>";
         echo "<div ID='cscDescSpot'></div>";     
	 echo $cityPop;
         echo "<p/>Owned And Operated By: ";
         include_once("tycMiniProfile.php");
         echo "</div>";
       }
     }
     //**********************
     // Scope Country Show Provinces/States 
     //**********************
     if ($scope == "myCountry"){
       $SQL  = "Select left(tblState.name,17) name,tblState.stateID as stateID,sum(taxableMembers) from tblState  ";
       $SQL .= "inner join tblCity  on tblCity.StateID = tblState.stateID ";
       $SQL .= "where tblState.dopeFlg=0 and tblState.countryID = ".$countryID." group by tblState.stateID,tblState.name ";
       $SQL .= "order by sum(taxableMembers) desc,sum(nUsers) desc limit 60" ;
       $cresult = mkyMsqry($SQL);
       $cRec = mkyMsFetch($cresult);
       if ($cRec){
         echo "<div class='infoCardClear'>";
         echo "<h2>Welcome To ".$myCountry."</h2>";
         echo "<div ID='cscDescSpot'></div>";
	 echo "<h3>Regions of ".$myCountry."</h3>";
         echo "<table class='docTableSmall'><tr valign='top'>";
         while($cRec){
           $linkTo = "<a class='smallListLink' href='javascript:wzLink(\"myTownChangeTo.php?wzID=".$sKey."&fprovID=";
           echo "<td>".$linkTo.$cRec['stateID']."\");'>".$cRec['name']."</a></td>";
           $cRec = mkyMsFetch($cresult);
	   if ($cRec){
	     echo "<td>".$linkTo.$cRec['stateID']."\");'>".$cRec['name']."</a></td>";
             $cRec = mkyMsFetch($cresult);
           }
           if ($cRec){
             echo "<td>".$linkTo.$cRec['stateID']."\");'>".$cRec['name']."</a></td>";
             $cRec = mkyMsFetch($cresult);
	   }
	   echo "</tr>";
	 }
	 echo "</table>";
	 echo "</div>";
       }
     }
	 
     //**********************
     // Scope Prov/State show top cities 
     //**********************
     if ($scope == "myState"){
       $SQL  = "Select name,cityID from tblCity  ";
       $SQL .= "where dopeFlg=0 and stateID = ".$stateID." ";
       $SQL .= "order by taxableMembers desc,nUsers desc limit 130" ;
       $cresult = mkyMsqry($SQL);
       $cRec = mkyMsFetch($cresult);
       if ($cRec){
         echo "<div class='infoCardClear'>";
         echo "<h2>Welcome To ".$myState."</h2>";
         echo "<div ID='cscDescSpot'></div>";
	 echo "<h3>Cities of ".$myState."</h3>";
         echo "<table class='docTableSmall'><tr valign='top'>";
         while($cRec){
           $linkTo = "<a class='smallListLink' href='javascript:wzLink(\"myTownChangeTo.php?wzID=".$sKey."&fcityID=";
           echo "<td>".$linkTo.$cRec['cityID']."\");'>".$cRec['name']."</a></td>";
           $cRec = mkyMsFetch($cresult);
           if ($cRec){
             echo "<td>".$linkTo.$cRec['cityID']."\");'>".$cRec['name']."</a></td>";
             $cRec = mkyMsFetch($cresult);
           }
           if ($cRec){
             echo "<td>".$linkTo.$cRec['cityID']."\");'>".$cRec['name']."</a></td>";
             $cRec = mkyMsFetch($cresult);
           }
           echo "</tr>";
         }
         echo "</table>";
         if ($userID == 17621){
           echo "<p/><form method='GET' action='/whzon/mbr/mods/addNewCity.php'>";
           echo "<input type='hidden' name='wzID' value='".$sKey."'/>";
           echo "<input type='hidden' name='stateID' value='".$stateID."'/>";
           echo "Add New City: <input type='text' name='cname' />";
           echo "<input type='text' name='cpop' value='10000'/>";
           echo "<br/><input type='submit' value='Submit'/>";
           echo "</form>";
         }
         echo "</div>";
       }
     }
	 
     //**************************
     // Display Member Post Options
     //****************************
     $postPhoto = 'javascript:startPShare();';
     $postVideo = '/whzon/mbr/vidView/frmQuickVideos.php?wzID='.$sKey.'&fmode=1';
     $postBlog  = '/whzon/mbr/blog/mbrMBLOG.php?fTopicID=0&wzID='.$sKey.'&fwzUserID='.$userID;
     $postSale  = '/whzon/mbr/mbrPostClassified.php?itemID=post&wzID='.$sKey;
     if ($userID == 0){   
       $postVideo = 'javascript:startPShare();';
       $postBlog  = 'javascript:startPShare();';
       $postSale  = 'javascript:startPShare();';
     }
     ?>

     <div ID='PostIT' style='background:darkKhaki;border-radius: .5em;padding:0px;margin:0px;'>
     <div style='margin:0px;border:0px solid #dddddd;border-bottom:0px;width:100%;padding-top:4px;'>
     <a href='javascript:parent.startPShare();'><img style='border:0px;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/_photoIcon.png' height='24px'/></a>Photo
     <a href='javascript:wzLink("<?php echo $postVideo;?>");'><img style='border:0px;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/_videoIcon.png' 
     height='24px'/></a>Video
     <a href='javascript:wzLink("<?php echo $postBlog;?>");'><img style='border:0px;' 
     src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/_newsIcon.png'  height='24px'/></a>Blog
     <a href='javascript:wzLink("<?php echo $postSale;?>");'><img style='border:0px;' 
     src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/_saleIcon.png'  height='24px'/></a> Sell
     </div>
     <form id="friends" style="margin:0px;padding:0px;width:100%;background:#eeeeee;border:0px solid #cccccc;" method="get" action="">
     <input name="wzID" value="<?php echo $sKey;?>" type="hidden"/>
     <input name="fmbrID" value="<?php echo $userID;?>" type="hidden"/>
     <textarea id="typeBox" style="border:0px;background:#eeeeee;FONT-FAMILY: tahoma,sans-serif;font-size:13px;font-weight:bold;padding:2px;width:98%;height:43px;" 
     onclick="startPShare();" placeholder=" ...Click Here To Share Photo's Or Videos." name="fshout" wrap="VIRTUAL" scrollbars="no"></textarea>
     <div><span ID='falertwait'> <input  style="margin-top:3px;border-radius: .25em;margin-right:15px;margin:3px;" type='button' 
     value='Share It Now' onclick='startPShare();'></span></div>
     </form>
     </div>

     <h3>Members In <?php echo $myCityname;?> </h3>

     <table style='width:100%'>
     <tr valign='top'>
     <td>
     <?php 
     $SQL = "SELECT tblwzUser.sex sex, tblwzUser.age, tblwzOnline.wzUserID as online, ";
     $SQL .= " tblwzUser.verified, tblwzUser.imgFlg,tblwzUser.wzUserID,tblwzUser.firstname, tblwzUser.profileText, ";
     $SQL .= " tblwzUser.country, tblwzUser.city, tblwzUser.lastOnline  ";
     $SQL .= " from tblwzUser  inner join tblCity  on tblCity.cityID=tblwzUser.cityID ";
     $SQL .= " left join tblwzOnline  on tblwzUser.wzUserID = tblwzOnline.wzUserID ";
     $SQL .= " where ".$userSearch." and  tblwzUser.sandBox is null  and tblwzUser.imgFlg=1 ";
     $SQL .= " order by  online desc,lastOnline desc;";

     $winX = 200;
     $Xinc = 120;


     echo "<p><table>";
     $i = 0;
     $nRows = 30;

     $link = "?franCID=".$mtFranCID."&wzID=".$sKey."&fmyMode=mbrs&fwzUserId=".$wzUserID;
     $appName = "myTownProfile.php";

     $nTop = $pg + $nRows;

     $selTop = "limit ".$nTop." ";

     $SQL = mkyStrReplace("Online desc;","Online desc ".$selTop,$SQL);

     $tRec = null;
     $result = mkyMsqry($SQL);
     $tRec = mkyMsFetch($result);

     $cpage = 0;
     while($tRec && $cpage < $nextPage) {
       $tRec = mkyMsFetch($result);
       $cpage = $cpage + 1;
     }

     $frIconStyle = "border-radius:.3em;";
     $onIconStyle = "";

     if ($mobile){
       $frIconStyle = "border-radius:.3em;height:30px;width:36px;vertical-align:middle;";
       $onIconStyle = "height:26px;width:160px;margin-top:15px;";
     }

     While ($tRec && $i < $nRows){
       $age = $tRec['age'];
       $sex = $tRec['sex'];

       if ($sex !== null) {
         if ( $sex == 1) {
           $sex = "f - ";
         } 
         else {
           $sex = "m - ";
         }
       }

       $profile_A = "<a style='font-size:14px;' href='/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."'>";
       $SQL = "select mWebFlg, websiteID, Title, rating, wsImgFlg, tblTopSites.wzUserID From tblTopSites  ";
       $SQL .= " left outer join tblwsRatings  on tblTopSites.wsRatingID=tblwsRatings.wsRatingID ";
       $SQL .= " where wzUserID=".$tRec['wzUserID']." ORDER BY wsImgFlg desc;";

       $wRec = null;
       $wresult = mkyMsqry($SQL) or die($SQL);
       $wRec = mkyMsFetch($wresult);

       if ( $wRec ) {
         $linkTrade_A = "<a href=javascript:openLinkTrade('/wzUsers/trackerLT/confirmTradeLink.asp?fwebsiteID=".$wRec['websiteID']."',".$wRec['websiteID'].")>";
       }

       if ($userID != 0 ) {
         $folJS = "href=javascript:parent.wzAPI_showFrame('/whzon/mbr/fanRequestFrm.php?wzID=".$sKey."&fanID=".$tRec['wzUserID']."',380,230,500,".$winX.");";
       } 
       else {
         $folJS = "href='javascript:parent.xyAquickLogin(500,".($winX-100).");'";
       }

       $winX = $winX + $Xinc;

       ?>
       <tr valign=top>
       <td style='padding-top:14px;width:88px;'> 
       <?php echo $profile_A ?> 
       <img title="View <?php echo $tRec['firstname'] ?>'s profile" 
       style='margin:0px 15px 10px 0px;border-radius:50%;width:72px;height:90px;' 
       src='//image.bitmonky.com/getMbrImg.php?id=<?php echo $tRec['wzUserID'] ?>'></center>
       </a>
       </td>
       <td ID='tag<?php echo $tRec['wzUserID'] ?>' style='padding-top:10px; border: 0px dotted #777777;'>
       <a <?php echo $folJS ?>><img title='Send Friends Request' style='border:0px;vertical-align:middle;<?php echo $frIconStyle ?>' 
       src='//image.bitmonky.com/img/friendsIcon.png'></a>
       <?php echo $profile_A ?><?php echo $tRec['firstname'] ?></a><span style='font-size:13px;'> <b><?php echo $sex ?> <?php echo $age ?></b>
       <b>From</b> <?php echo $tRec['city'] ?>,
       <?php echo $tRec['country'] ?> </span><br style='clear:right;'>

       <?php if ($tRec['online']){ ?>
         <?php if ( $userID != 0) { ?>
           <a href='javascript:parent.wzPopChat(<?php echo $tRec['wzUserID'] ?>);'>
         <?php } else { ?>
           <a href='javascript:parent.wzQuickReg(<?php echo $tRec['wzUserID'] ?>);'>
         <?php } ?>
         <img title='Start Chat With <?php echo $tRec['firstname'] ?>' 
         style='float:right;margin:.5em 1em 1em 1.5em;border-radius:50%;<?php echo $onIconStyle ?>' 
         src='//image.bitmonky.com/img/onlineIcon.png'></a>
       <?php } else { ?>
         <b>Last Online:</b> <?php echo $tRec['lastOnline'] ?>
       <?php } ?>
       <P> 
       <?php  if ( $tRec['profileText' ] != "" ) { ?>
       
       <b>Greeting:</b> <?php echo left($tRec['profileText'],180) ?>  <?php echo $profile_A ?>..more</a><br>
     <?php } ?>
     </td></tr><td colspan='2' style='padding-left:15px;padding-top:10px;'>
     <?php 
     $wc = 0;
     if ($wRec){
       echo "<b>".$tRec['firstname']."`s Websites: </b><p/>";
     }
     while ($wRec && $wc < 5){
       if ( $wRec['mWebFlg'] == 1 ) {
         $webURL = "https://bitmonky.com/whozon/mbrMWeb.asp";
       } 
       else {
         $webURL = "/whzon/mbr/viewWebsite.php";
       }

       if ( $wRec['wsImgFlg']== 1 ) { ?>
         <a href='javascript:wzLink("<?php echo $webURL ?>?fwebsiteID=<?php echo $wRec['websiteID'] ?>&fwzUserID=<?php echo $tRec['wzUserID'] ?>");'>
         <img title='<?php echo left($wRec['Title'],25) ?>..' 
         style='float: left;border-radius:0.25em;margin: 2px 12px 2px 0px;' 
         src='//image.bitmonky.com/getWsMiniImg.php?id=<?php echo $wRec['websiteID'] ?>'>
         <?php echo left($wRec['Title'],25) ?>..
         </A>  <?php echo $wRec['rating'] ?>
         <br style='clear:left;'> 
       <?php } else { ?>
         | <a href=Javascript:parent.wzOpenNewLOG('<?php echo $webURL ?>?fwebsiteID=<?php echo $wRec['websiteID'] ?>&fwzUserID=<?php echo $tRec['wzUserID'] ?>',<?php echo $tRec['wzUserID'] ?>)>
         <?php echo left($wRec['Title'],25) ?>..</A> <?php echo $wRec['rating'] ?><br>
       <?php }
       $wRec = mkyMsFetch($wresult);
       $wc = $wc + 1;
     } ?>
     <br><br>
     </td>
     </tr>
     <?php 
     $i = $i + 1;
     $tRec = mkyMsFetch($result);
   }
   echo "</table>";
   if ($i > 0 ){
     echo "<p><a href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage + $nRows)."\");'>Next</a>";
   }
   if ($nextPage > 0 ) {
     echo " | <a href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage - $nRows)."\");'>Back</a>";
   }
   echo " | <a href='javascript:wzLink(\"".$appName.$link."&newPg=0\");'>Top</a>";

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
   </td>
   </tr>
   </table>
   <div ID='frameFooter'></div>

   <?php
   echo "</td>";
   echo "</tr></table>";
   include_once("folFooter.php");
   echo "</div>";

function showOtherTycoons(){
     global $userID;
     global $sKey;
     echo "<h2 style=''>"; 
     sayTxt('Meet Our Top City Tycoons!');
     echo "</h2>";
     sayTxt('Have a look at what they are doing with their cities');
     echo "<p/>";
     $SQL = "select totalRCADs,advInvestPkg,tblwzUser.firstname,wzUserID, tblwzUser.city as name, ";
     $SQL .= "sum(nUsers)nUsers, sum(population)population  from tblCity  ";
     $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID = ownerID ";
     $SQL .= "inner join tblFranchise  on franCityID = tblCity.cityID ";
     $SQL .= "where feeExempted is null and not ownerID is null and sandBox is null ";
     $SQL .= "group by wzUserID,tblwzUser.firstname,tblwzUser.city,totalRCADs,advInvestPkg ";
     $SQL .= "order by advInvestPkg desc limit 4";
     $result = mkyMsqry($SQL);
     $tRec = mkyMsFetch($result);

     while ($tRec){
       //$cityID = $tRec['cityID'];
       echo "<div style='width:100%;padding-bottom:10px;'>";
       echo "<a title='View ".$tRec['firstname']."`s Profile' ";
       echo "href='javascript:wzLink(\"/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."\");'>";
       echo "<img alt='View Profile' style='float:left;width:34px;height:44px;margin:3px;margin-right:6px;margin-bottom:20px;border-radius:50%;' ";
       echo "src='//image.bitmonky.com/getMbrTmn.php?id=".$tRec['wzUserID']."'></a>";
       echo "<b>".$tRec['firstname']."</b><br>";
       echo "City: <a href='javascript:wzLink(\"/whzon/mytown/myTownProfile.php?wzID=".$sKey."&fwzUserID=".$tRec['wzUserID'];
       echo "&fscope=myCity&fmyMode=mbrs\");'>".$tRec['name']."</a>";
       echo "<br/>Monthly Investment: $".mkyNumFormat($tRec['advInvestPkg'],2)." CAD";
       //echo "<br/>Total Withdraws: $".mkyNumFormat($tRec['totalRCADs'],2)." CAD";
       echo "<br/>Members: ".$tRec['nUsers'];
       echo "</div>";
       $tRec = mkyMsFetch($result);
     }
}
function sayTodaysWeather($cityID){
  global $userID;
  $SQL  = "SELECT name,tycEmpID,tycJobTitle,tycJobDesc,firstname,tycEmpUID from tblTycJobDesc  ";
  $SQL .= "inner join tblTycEmployee  on tycEmpType = tycJobID ";
  $SQL .= "inner join tblwzUser  on wzUserID = tycEmpUID ";
  $SQL .= "inner join tblCity  on tblCity.cityID=tycCityID ";
  $SQL .= "where tycJobID = 6 and tycCityID = ".$cityID." ";
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  if (!$tRec){
    return;
  }
  $empID    = $tRec['tycEmpUID'];
  $cityName = $tRec['name'];
  $uID      = $empID;
  global $divWidth;
  $sayNewPhoto = 0;
  $SQL = "SELECT acID,photoID,height,width, title, phototxt from tblwzPhoto  ";
  $SQL .= "where wzUserID=".$empID." and phoJobID = 6 and TIMESTAMPDIFF(day,date(pDate),date(now())) = 0 limit 1 ";

  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);

  if ($tRec){
    $acItemID = $tRec['photoID'];
    $acID     = $tRec['acID'];

    $sImg = "//image.bitmonky.com/getPhotoTmn.php?id=".$acItemID;
    $title = $tRec['title'];
    $story = shortenTextTo($tRec['phototxt'],500);

    formatHashTags($story,$acID);
    $imgID = "fImage".$acItemID;
    $h     = $tRec['height'];
    $w     = $tRec['width'];
    $idim  = ' onload="fixImageSizeMax(\'".$imgID."\',350);" ';
    $maxw  = 400;
    if ($divWidth){
      $maxw = $divWidth;
    }
    if  ($w){
      if ($w > $maxw){
        $scale = $w/$maxw;
        $w = $maxw;
        $h = intval($h / $scale);
      }
      $idim = ' width="'.$w.'" height="'.$h.'" ';
    }
    $ank = "<a href=\"javascript:wzLink('/whzon/mbr/mbrViewPhotos.php?fwzUserID=".$uID."&vPhotoID=".$acItemID."');\">";
    echo "<div class='infoCardClear'>";
    echo "<p/>".getTRxt('Todays Weather Report');
    echo " <b>".$cityName."</b>";
    echo " <a href=\"javascript:wzLink('/whzon/mbr/mbrViewPhotos.php?fwzUserID=".$uID."&vPhotoID=".$acItemID."');\">";
    echo "<br/><center><img ID='".$imgID."' ".$idim." src='//image.bitmonky.com/getPhotoImg.php?id=".$acItemID."&fpv=".$uID."' ";
    echo "style='border-radius: .5em;margin:0px;'></center></br></a>";
    echo "<b>".$cityName."</b><p>".$story."<p/>";
    echo "<p/>";
    echo " <a href=\"javascript:wzLink('/whzon/mbr/mbrViewPhotos.php?fwzUserID=".$uID;
    echo "&vPhotoID=".$acItemID."');\">".getTRxt('View Report Here')."...</a><br>";
    echo "</div>";
  }
  else {
    $sayNewPhoto = 1;
  }
}
function showEmployees($cityID){
  global $sKey;
  showCityMgr($cityID);
  $SQL  = "Select name from tblCity  where cityID = ".$cityID;
  $cresult = mkyMsqry($SQL) or die($SQL);
  $cRec = mkyMsFetch($cresult);
  $myCityname = safeQuotes($cRec['name']);

  $SQL  = "SELECT tycEmpID,tycJobID,tycJobTitle,tycJobDesc,firstname,tycEmpUID from tblTycJobDesc  ";
  $SQL .= "inner join tblTycEmployee  on tycEmpType = tycJobID and TycCityID=".$cityID." "; 
  $SQL .= "inner join tblwzUser  on wzUserID = tycEmpUID ";
  $SQL .= "where NOT tycJobID = 5 and tycCityID = ".$cityID." "; 
  $SQL .= "order by wzUserID ";
   
  $cRec = null;
  $cresult = mkyMsqry($SQL) or die($SQL);
  $cRec = mkyMsFetch($cresult);

  if ($cRec){
    echo "<p/><div style='' class='infoCardClear'>";
    //echo "<img style='float:right;width:45px;height:45;border-radius:.5em;' src='//image.bitmonky.com/img/jobOpen.png'/>";
    echo "<h3>Local City Staff</h3>";
    echo "<br clear='right'>";

    while ($cRec){
      $jobID = $cRec['tycJobID'];
      $name  = $cRec['firstname'];
      $UID   = $cRec['tycEmpUID'];
      $desc  = $cRec['tycJobDesc'];
     
      $a = "<a href='javascript:wzLink(\"/whzon/mytown/myTownProfile.php?wzID=".$sKey."&fwzUserID=".$UID."&gID=".$jobID."\");'>";
      echo "<div style='margin-bottom:15px;'>";
      echo $a."<img style='float:left;margin:0px 15px 10px 0px;border-radius:0.5em;width:50px;height:58px;' ";
      echo "src='//image.bitmonky.com/getMbrImg.php?id=".$UID."'/></a>";
      echo "<b style=''>Position:</b> ".$a."Local ".$cRec['tycJobTitle']."</a><br/><b>Duties:</b> ".$desc;
      echo "<br/><b/>Name:</b> ".$name."";
      echo "<br clear=left></div>";
      $cRec = mkyMsFetch($cresult);
    }
  }
  echo "<p/><div style='margin-bottom:25px;' ID='myTownTycJobs'>";
  echo "</div>";
}
function showCityMgr($cityID){
  global $sKey;
  $SQL  = "Select name from tblCity  where cityID = ".$cityID;
  $cresult = mkyMsqry($SQL) or die($SQL);
  $cRec = mkyMsFetch($cresult);
  $myCityname = safeQuotes($cRec['name']);

  $SQL  = "SELECT tycEmpID,tycJobID,tycJobTitle,tycJobDesc,firstname,tycEmpUID from tblTycJobDesc  ";
  $SQL .= "inner join tblTycEmployee  on tycEmpType = tycJobID and TycCityID=".$cityID." ";
  $SQL .= "inner join tblwzUser  on wzUserID = tycEmpUID ";
  $SQL .= "where tycJobID = 5 and tycCityID = ".$cityID." ";
  $SQL .= "order by wzUserID ";

  $cRec = null;
  $cresult = mkyMsqry($SQL) or die($SQL);
  $cRec = mkyMsFetch($cresult);

  if ($cRec){
    echo "<p/><div style='' class='infoCardClear'>";
    echo "<h3>Local City Manager</h3>";
    echo "<br clear='right'>";

   while ($cRec){
     $jobID = $cRec['tycJobID'];
     $name  = $cRec['firstname'];
     $UID   = $cRec['tycEmpUID'];
     $desc  = $cRec['tycJobDesc'];

     $a = "<a href='javascript:wzLink(\"/whzon/mytown/myTownProfile.php?wzID=".$sKey."&fwzUserID=".$UID."\");'>";
     echo "<div>";
     echo $a."<img style='float:left;margin:0px 15px 10px 0px;border-radius:0.5em;width:50px;height:58px;' ";
     echo "src='//image.bitmonky.com/getMbrImg.php?id=".$UID."'/></a>";
     echo "<b>Duties:</b> ".$a.$desc."</a>";
     echo "<br/><b/>Name:</b> ".$name."";
     echo "<br clear=left></div></a>";
     $cRec = mkyMsFetch($cresult);
   }
   echo "</div>";
 }
}
if ($sessISMOBILE){
  echo "</div>";
  include_once("../mblp/mblFooter.php");
}
else {
  echo "</body></html>";
}
?>

