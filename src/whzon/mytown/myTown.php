<?php
if (isset($_GET['fmyMode'])){
  $myMode = $_GET['fmyMode'];
  if ($myMode == "wNews" ){
    exit('');
  }
}
$searchFlg = "";
$scope = null;
include_once("../mkysess.php");
include_once("myMbrData.php");
include_once("../utility/acHash.php");

if ($userID == 17621){
  ini_set('display_errors',1);
  error_reporting(E_ALL);
}
$title="";
$mKeywords="";
$mDesc="";
$mobile = null;
$fsq = safeGET('fsq');
$fsqPg = null;
if ($fsq){
  $fsqPg = "&fsq=".mkyUrlEncode($fsq);
}
include_once("myTownInc.php");
$digID = safeGetINT('digID');

$photoCredit = null;
if ($cprText){
  $photoCredit = '<div align="right" style="padding-right:.5em;">';
  $photoCredit .= '<span style="font-size:smaller;">photo credit -</span> ';
  $photoCredit .= '<a target="_new" style="font-size:smaller;" href="'.$cprURL.'">'.$cprText.'</a>';
  $photoCredit .= '</div>';
}
if (isset($_SERVER['QUERY_STRING'])){$qry = "?".$_SERVER['QUERY_STRING'];}else {$qry="";}
$furl = $_SERVER['SCRIPT_NAME'].$qry;

if ($sessISMOBILE){
  include_once("../mblp/mblTemplate.php");
}
else {
  ?>
  <!doctype html>
  <html class='pgHTM' lang="en">
  <head>
  <link rel="canonical" href='<?php echo $GLOBALS['MKYC_canon'].'/whzon/wzApp.php?furl='.urlencode($furl);?>'/>
  <style>
  .scrollBut {
    padding:.6em;
    font-weight:normal;
    border-radius: .25em;
    background-color:#74a02a;
    color:#ffffff;
    border:0px solid #aaaaaa;
    cursor:pointer;
    -webkit-border-radius:0.25em;
    margin-right:1em;
  }
  </style>

  <meta charset="utf-8"/>
  <link rel="stylesheet" href="/whzon/pcCSS.php?v=1.0"/>
<!-- Google tag (gtag.js) -->
  <?php $MKYC_ggtrack = 'on';?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-L9BBF7ES7Z"></script>
  <script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-L9BBF7ES7Z');
  </script>

  <script src="/whzon/sessmgrJS.php"></script>
  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script src='../wzToolboxJS.php'></script>
  <Title><?php echo $primContact; ?>'s Local Guide To | <?php echo $myCityname;?>, <?php echo $myState;?>, <?php echo $myCountry;?></title>
  <meta name="description" content="<?php echo left($profileText,200);?>">
  <meta name="keywords" content="BitMonky, websites, local news, photos,pictures,Blogs, Classifieds,Music,events">
  <script>
  <?php
  include_once("../frameHandlerJS.php");
  ?>
  function hideImageID(id){
    var img = document.getElementById(id);
    if (img){
      img.style.display = 'none';
    }
  }
  </script>
  </head>
  <body class='pgBody' onload='frameInit();'>
  <div style='background:#222222;'>
  <?php
}
?>
<script>
  var feedTimer=null;
  var acFeedTime = null;
function redirectToAd(url){
  window.open(url,'refWebsite');
}
function doAccQry(pg=0,qry=true,qmode='web',spin=null,goTop=null){
  scrollTo(0,0);
  if (spin){
    spin = '&spin=on';
  }else {spin = '';}
  
  var ispot = document.getElementById('accQrySpot');
  var xml = getHttpConnection();
  var currentTime = new Date();
  var ranTime = currentTime.getMilliseconds();
  var mode = '';
  <?php
  if (!$sessISMOBILE){
    echo "mode = '&mode=PC';";
  }
  ?>
  var app = 'getHutList.php';
  var oqry = null;
  if (qry){
    qry  = document.getElementById('accQryStr').value;
    oqry = '&search='+encodeURIComponent(qry);
    qry  = '&search='+encodeURIComponent(qry)+'&qmode='+qmode;
    app  = 'doPeerAccQry.php';
  } else qry = '';

  ispot.style.display = 'block';
  ispot.innerHTML = parent.getSpinner('Reading Memory Banks...');

  var data = '?wzID=<?php echo $sKey;?>&newPg='+pg+'&xr=' + ranTime + mode + qry + spin;
  var url = '/whzon/mytown/' + app + data;
  xml.timeout   = 20*1000;
  xml.open("GET", url, true);
  xml.onreadystatechange = function(){
    if (xml.readyState == 4){
      if(xml.status  == 200){
        const htm  = xml.responseText;
        if (htm.indexOf('BrainFreeze:Try All') > 0){
          ispot.innerHTML = parent.getSpinner('No Services Found... Searching More Memories!');
          doSiteWideSearch(pg,oqry,ispot,spin);
        }
        else {
          ispot.style.display = 'block';
          ispot.innerHTML = htm;
        }
        return;
      }
    }
  };
  xml.send(null);
}
function markWebForDelete(id){
    var conf = confirm("Confirm Delete");
    if (!conf){
      return;
    }

    var xml = parent.getHttpConnection();
    var currentTime = new Date();
    var ranTime = currentTime.getMilliseconds();

    var url = '/whzon/mytown/markWebsiteDeleted.php?wzID=<?php echo $sKey;?>&wsID=' + id + '&xr=' + ranTime;
    xml.timeout   = 20*1000;
    xml.open("GET", url, true);
    xml.onreadystatechange = function(){
      if (xml.readyState == 4){
        if(xml.status  == 200){
          var j = null;
          try {j = JSON.parse(xml.responseText); }
          catch(err) {
            alert('pars json failed'+xml.responseText,err);
            document.location.reload();
            return;
          }
          document.location.reload();
          return;
        }
      }
    };
    xml.send(null);
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
</script>

<?php
if (1==2){ //$bgImg != '' && !isset($_COOKIE['wzDSMode'])){
  ?>
  <img ID="bannerHolder" style="width:100%;height:200px;" src="<?php echo $bgImg;?>"/>
  <?php
}
echo "<!--";
?>
   
    <div id='franBannerInfo' style='background: rgba(255, 255, 255, 0.75);position:fixed;top:0px;left:0px;width:100%;'>
    <b style='font-size:larger;color:#222222;'> 
    <a style='font-size:larger;' href='javascript:wzLink("/whzon/mbr/mbrProfile.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $wzUserID?>");'>
    <img alt="View Profile" style="float:left;width:30;height:38;margin:3px;margin-right:6px;border:0px solid #777777;" src="//image.bitmonky.com/getMbrTmn.php?id=<?php echo $wzUserID?>">
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
<?php
echo "-->";
?>

<div style='padding:0em .5em 0em .5em;'>
<table style='width:100%;<?php if ($sessISMOBILE){ echo "margin-top:15px;";}?>'><tr valign='top'>
<td>
<?php
if (!$sessISMOBILE){
  echo "<div style=background:#222222;padding-right:.5em;'>";
}
if ($bgImg != '' && !isset($_COOKIE['wzDSMode'])){
  echo $photoCredit;
}
if (!$sessISMOBILE){

if ($myMode != "qry")  {
  ?>
  </div>
  <div style=''>
  <div style='background:#222222;padding:.5em;'>
  <div class='infoCardPost' style='margin-bottom:0px;'>
  <!--
  <B><span class=wzBold>Recent Members</span></b> 
  <p>
  <?php
  /***
  $searchFlg = "";

  if ($userSearch == " 1=1 ") {

     $SQL = "SELECT wzUserID,firstname, imgFlg FROM tblwzUserRecent  order by lastOnline desc limit 12;";
  }
  else {
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
      $SQL .= " inner join tblCity  on tblCity.cityID = tblwzUser.cityID "; 
      $SQL .= " where tblCity.metroID = ".$myMetroID." and  tblwzUser.sandBox is null ";
      $SQL .= " order by  tblwzUser.imgFlg desc,online desc,lastOnline desc limit 12;";
    }
  }
  //if ($userID == 17621){echo $SQL;}   
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  $i = 0;
  $scrWidth = "width:100%;";
  if ($mobile) {
    $nRows = 9;
  }
  else {
    $nRows = 12;
  }

  echo "<p><table style='border-collapse: collapse; padding: 0px; border: 0px solid; width:100%' >";
  echo "<tr  valign='top'>";

  While ($tRec && $i < $nRows) {
    $wsImgStr="";
    $wsAnkor= " <a  href='javascript:wzLink(\"/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."\");'>";
    $wsImgStr= $wsAnkor."<img style='margin: 0px;margin-right:4px;border-radius:50%;width:60px;height:80px;' src='//image.bitmonky.com/getMbrImg.php?id=".$tRec['wzUserID']."'></a>";
    echo "<td style='padding:0px;color:white;font-size:10px;border: 0px solid #777777;'>".$wsImgStr;
    echo "</td>";
    $i = $i + 1;
    $tRec = mkyMsFetch($result);
  }
  echo "</tr></table>";
  echo "</div></div>";
*/
  echo "-->";
}

echo "<p/>";

}
else {
  echo "<div style='padding:.5em;'>";
}

  ?>

<div class='infoCardClear'>

   <?php
   //echo "<a class='a.Title' href='javascript:wzLink(\"myTownProfile.php?franCID=".$mtFranCID."&wzID=".$sKey;
   //echo "&fscope=".$scope."&fcatID=".$catID."&fwzUserID=".$wzUserID."\");'>";
   ?>
   <!--
   <img alt="View Profile" style="float:right;width:30;height:38;margin:3px;margin-left:6px;border-radius:50%;"
   src="//image.bitmonky.com/img/homeIcon.png"></a>
   -->
  <div align='right'>
  <b ID='searchBox' class='a.Title' style=''>Change Location:</b>
  <a class='a.Title'
  href='javascript:wzAPI_showFrame("/whzon/mytown/selCountryAutoT.php?wzID=<?php echo $sKey.$fsqPg."&fcatID=".$catID;?>",300,400,100,100,"searchBox");'>Country</a>
  | <a class='a.Title'
  href='javascript:wzAPI_showFrame("/whzon/mytown/selCityAutoT.php?wzID=<?php echo $sKey.$fsqPg."&fcatID=".$catID;?>",300,400,100,100,"searchBox");'>City</a>
  | <a class='a.Title'
  href='javascript:wzLink("myTown.php?wzID=<?php echo $sKey.$fsqPg;?>&fscope=myWorld&fcatID=<?php echo $catID?>&fwzUserID=<?php echo $wzUserID?>");'>Go World Wide</a>
  </div>
<?php 
if ($myMode == ""){
  $myMode = "mshare";
}  
echo "<h1><b>Member ".$modes[$myMode]." : </b>";

if ($scopeDisplay == 'Town'){
  ?>
  <a class='AH1' 
  href='javascript:wzLink("myTownChangeULoc.php?wzID=<?php echo $sKey.$fsqPg."&fcatID=".$catID."&fcityID=".$cityID;?>");'><?php echo $myCityname?></a>,
  <a  class='AH1'
  href='javascript:wzLink("myTownChangeULoc.php?wzID=<?php echo $sKey.$fsqPg."&fcatID=".$catID."&fprovID=".$stateID;?>");'><?php echo $myState?></a>,
  <a class='AH1'
  href='javascript:wzLink("myTownChangeULoc.php?wzID=<?php echo $sKey.$fsqPg."&fcatID=".$catID."&fcountryID=".$countryID;?>");'><?php echo $myCountry; ?></a>
  <?php
}
else if ($scopeDisplay == 'Region'){
  ?>
  <a  class='AH1'
  href='javascript:wzLink("myTownChangeULoc.php?wzID=<?php echo $sKey.$fsqPg."&fcatID=".$catID."&fprovID=".$stateID;?>");'><?php echo $myState?></a>,
  <a class='AH1'
  href='javascript:wzLink("myTownChangeULoc.php?wzID=<?php echo $sKey.$fsqPg."&fcatID=".$catID."&fcountryID=".$countryID;?>");'><?php echo $myCountry; ?></a>
  <?php
}
else if ($scopeDisplay == 'Country'){
  ?>
  <a class='AH1'
  href='javascript:wzLink("myTownChangeULoc.php?wzID=<?php echo $sKey.$fsqPg."&fcatID=".$catID."&fcountryID=".$countryID;?>");'><?php echo $myCountry; ?></a>
  <?php
}
else {
  echo "<a class='AH1' href='javascript:wzLink(\"myTownProfile.php?wzID=0&fscope=myWorld&fcatID=0&fwzUserID=949098\");'>World Wide</a>";
}

echo "<br/>Scope - ".$scopeDisplay."</h1>";
 


if ($userID == 17621 || $userID == 63555){
   echo "| <a class='a.Title' href='javascript:wzLink(\"myTown.php?fmyMode=store&wzID=".$sKey."&fscope=".$scope."&fcatID=".$catID."&fwzUserID=".$wzUserID."\");'>Shop</a>";
   echo "| <a class='a.Title' href='javascript:wzLink(\"myTownEditor.php?wzID=".$sKey."&fscope=".$scope."&fcatID=".$catID."&fwzUserID=".$wzUserID."\");'>Edit</a>";
}
?>
<br clear='right'/></div>
<script>
function scrollToDig(){
  console.log('scrold to dig');
  parent.window.scrollTo(0,300);
  scrollTo(0,250);
}
function wzOnLoad(){
   <?php if ($digID){ echo "setTimeout('scrollToDig()',1000);";}?>
   parent.uloc.setScope('<?php echo $scope;?>');
   parent.uloc.setAction('<?php echo $myMode;?>');
   parent.uloc.setBanner('<?php echo $bgImg;?>');
   parent.uloc.setLink('<?php echo $_SERVER['REQUEST_URI'];?>');
   parent.uloc.setNames('<?php echo $myCountry."','".$myState."','".$myCityname;?>');
   parent.uloc.setLoc(<?php echo $wdRegionID.",".$countryID.",".$stateID.",0,0,".$cityID;?>);
   parent.mkyPushDest(parent.uloc);
   parent.showRecentDest();
   if (typeof wzFeedLoad == 'function') {wzFeedLoad();}
   console.log('location is now:',parent.uloc);
}
</script>
<?php


if ($myMode == "photo") {  
   include_once("myTownPhotos.php");
}

if ($myMode == "event") {
   include_once("myTownEvents.php");
}

if ($myMode == "class") {
  include_once("myTownClass.php");
}

if ($myMode == "store") {
  include_once("myTownStores.php");
}

if ($myMode == "web") {
  include_once("myTownWSites.php");
}

if ($myMode == "favWS") {
   include_once("myFavWSites.php");
}

if ($myMode == "mBlog")  {
  include_once("myTownBlog.php");
}
if ($myMode == "chan")  {
  include_once("myTownChannels.php");
}

if ($myMode == "news") {
//  include_once("myTownNews.php");
  $myMode = 'mFeed';
}

if ($myMode == "mbrs") {
  include_once("myTownMbrs.php");
}

if ($myMode == "mosh") {
  include_once("myTownMosh.php");
}

if ($myMode == "qry") {
  include_once("myTownQry.php");
}

if ($myMode == "gov") {
  include_once("myTownGov.php");
}

if ($myMode == "mshare" || $myMode == "") {
  include_once("myTownShares.php");
}

if ($myMode == "video") {
  include_once("myTownVideos.php");
}

if ($myMode == "wNews"){
  include_once("myTownWNews.php");
}

if ($myMode == "mFeed"){
  include_once("myTownFeed.php");
}

if ($myMode == "boost"){
  include_once("myTownBoosts.php");
}

echo "</td>";

echo "</tr></table>";
echo "</div>";
include_once("folFooter.php");
if (!$sessISMOBILE){
  
  echo "<br/></div>";
  include_once("folFooter.php");
  echo "<div ID='frameFooter' style='margin-bottom:1em;'></div>";
  echo "</body></html>";
}
else {
  echo "</div>";
  include_once("../mblp/mblFooter.php");
}
?>
