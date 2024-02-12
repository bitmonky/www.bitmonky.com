<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../mbr/mbrData.php");
if ($sessISMOBILE){
//  $qry = $_SERVER['QUERY_STRING'];
//  $nmURL = "/whzon/mblp/mbr/mbrProfile.php?".$qry;
//  header('Location: '.$nmURL);
//  exit('');
}
include_once("myTownInc.php");


$title="";
$mKeywords="";
$mDesc="";

$mRec = null;
$bgImgID = 0;


if (isset($_GET['fwzUserID'])){ $viewUID  = safeGET('fwzUserID');} else { $viewID = 0;}
if (isset($_GET['fmode']))    { $mode     = safeGET('fmode'];}            else { $mode = null;}
if (isset($_GET['fitemID']))  { $inItemID = safeGET('fitemID'];}          else { $inItemID = null;}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title><?php echo $Title;?></title>
  <meta name="keywords" content="<?php echo $mKeywords;?>"/>
  <meta name="description" content="<?php echo $mDesc;?>"/>
  <?php 
  //if ($sessISMOBILE){
  //  echo '<link rel="stylesheet" href="/whzon/mblp/mobile.css?v=1.0"/>';
  // }
  // else {
    echo '<link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>';
  //}
  ?>
  <script src="/whzon/sessmgrJS.php"></script>
  <script src='../wzToolboxJS.php'></script>
  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script src='../wzToolboxJS.php?v=2'></script>
  <script>
  var storePg = null;  
  function pcInit() {
    storePg = 0;
    wzInitJS(1);
  }
  function changePageTo(pg){
    storePg = pg;
    fetchUF();	
    parent.window.scrollTo(0,0);
  }
  function getYOffset(id){
    var el = document.getElementById(id);
	return getOffset(el).top;
  }
  function getOffset( el ) {
    var _x = 0;
    var _y = 0;
    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop;
        el = el.offsetParent;
    }
    return { top: _y, left: _x };
  }
  function deleteActivity(ID){
    var elID = 'mbrACT' + ID;
	yoff = getYOffset(elID);
	parent.wzAPI_showFrame('/whzon/mbr/popDeleteActivity.php?wzID=<?php echo $sKey;?>&acID=' + ID,350,250,550,yoff);
  }
  function popStoreBannerLoader(){
	parent.wzAPI_showFrame('/whzon/mytown/popLoadBGImg.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $wzUserID.'&fscope='.$scope.'&fsmCID='.$fsmCID;?>',500,550,444,getYOffset('menuMark')+225);
  }
  function popCopyRight(banID){
	parent.wzAPI_showFrame('/whzon/mytown/getImgCredits.php?bannerID=' + banID + '&wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $wzUserID.'&fscope='.$scope;?>',500,550,444,getYOffset('menuMark')+225);
  }

  function deleteQuestion(ID){
	yoff = getYOffset(elID);
	parent.wzAPI_showFrame('/whzon/mbr/popQADeleteFrm.php?wzID=<?php echo $sKey;?>&fqaID=' + ID,350,250,550,yoff);
  }
  function replyQuestion(ID){
    var elID = 'qaElm' + ID;
	yoff = getYOffset(elID);
	parent.wzAPI_showFrame('/whzon/mbr/popQAReplyFrm.php?wzID=<?php echo $sKey;?>&fqaID=' + ID, 550,450,400,yoff);
  }
  var popsCont = null;
  var likesConn = null;
  var mainPicControl = null;
  var mainPicTimer   = null;
  var mainPicHolder  = null;
  var bannerControl  = null;
  var bannerTimer    = null;
  var bannerHolder   = null;
  var tmnailControl  = null;
  var tmnailTimer    = null;
  var pTextControl   = null;
  var pTextTimer     = null;
  var saveBGIControl = null;
  var saveMPPControl = null;
  var bgOffset       = null;
  var mppXoff        = null;
  var mppYoff        = null;
  var bgImgID        = <?php echo $bgImgID;?>;
  var bgMode         = <?php echo $bgMode;?>;

  var divRU=null;
  var divNF=null;
  var divUF=null;
  var divAU=null;
  var divCU=null
  var divMT=null;
  
  var chanTimer=null;
  var feedTimer=null;
  var chanPg=null;
  var chanQry=null;
  
  var imgRotXML   = parent.getHttpConnection();
  var saveBGIXML  = parent.getHttpConnection();
  var saveMPPXML  = parent.getHttpConnection();
  var itemViewXML = parent.getHttpConnection();

function popRemoveMyFmsg(msgID) {
  var url = 'frmRemoveMyFmsg.php?wzID=<?php echo $sKey;?>&fmsgID=' + msgID;
  var fmsgwin = window.open(url, 'fmsgWin', 'winName,"target=new,width=350,height=240,resizable=no,scrollbars=no"');
  fmsgwin.focus();
  fmsgwin.moveTo(500, 150);
}
function popReplyMyFmsg(msgID) {
  var url = 'frmReplyMyFmsg.php?wzID=<?php echo $sKey;?>&fmsgID=' + msgID;
  var fmsgwin = window.open(url, 'fmsgWin', 'winName,"target=new,width=350,height=240,resizable=no,scrollbars=no"');
  fmsgwin.focus();
  fmsgwin.moveTo(500, 150);
}
  
function doSendQuestion() {
  var wzoutput = document.getElementById("falertwait");
  wzoutput.innerHTML = "<br><img onload='executeAlerts();' style='width:35px;height;35px;' src='https://image.bitmonky.com/img/imgLoading.gif'> Sending Please Wait...";
}
function executeAlerts() {
  document.getElementById("pquestion").submit();
}
  function wzInitJS($vstatus){
    wzStartApp();
	bannerControl  = document.getElementById('bannerControl');
	bannerHolder   = document.getElementById('bannerHolder');
	saveBGIControl = document.getElementById('saveBGIControl');
	likesConn      = getHttpConnection();
    popsCont       = 'wzPopContainer';
    showBannerControls();
  }
  function wzClearTimer(wztimer){
    clearTimeout(wztimer);
	wztimer = null;
  }
  
  function hideBannerControls(){
    if (bannerControl){
      bannerControl.style.visibility = "hidden";
	}
    wzClearTimer(bannerTimer);
  }
  function showBannerControls(){
    wzClearTimer(bannerTimer);
    if (bannerControl){
	  bannerControl.style.visibility = "visible";
	}
  }
  
  function hideSaveBGIControls(){
    saveBGIControl.style.visibility = "hidden";
  }
  function showSaveBGIControls(){
    if (bgMode){
      saveBGIControl.style.visibility = "visible";
	}
  }
  function replaceAll(find, replace, str) {
	return str.replace(new RegExp(find, 'g'), replace);
  }
  function bannerScroll(amt){
    var y = bannerHolder.style.backgroundPosition;
    y = y.replace('right','');
    y = y.replace('100%','');
    y = y.replace('px','');
    var n  = parseInt(y) + amt;
    if (n > 0){n = 0;}
    if (n < -<?php echo $bgHeight;?>) { n = -<?php echo $bgHeight;?>;}
    bgOffset = -1 * n;
    var p = 'right ' + n + 'px';
    bannerHolder.style.backgroundPosition = p;
    showSaveBGIControls()
 }
 function saveBGImgPos(){
   var url="/whzon/mytown/cropBGImg.php?wzID=<?php echo $sKey;?>&yoff=" + bgOffset + '&id=' + bgImgID;
   wzAPI_prepUrl(url,saveBGIXML);
   saveBGIXML.onreadystatechange = DoSaveBGImgPos;
   saveBGIXML.send(null);
 }

 function DoSaveBGImgPos(){
   if ( saveBGIXML.readyState == 4){
     hideSaveBGIControls()
   }
 }
  function saveStoreBGImgPos(){
   var url="/whzon/mytown/cropBGImg.php?wzID=<?php echo $sKey;?>&yoff=" + bgOffset + '&id=' + bgImgID;
   wzAPI_prepUrl(url,saveBGIXML);
   saveBGIXML.onreadystatechange = DoSaveStoreBGImgPos;
   saveBGIXML.send(null);
 }

 function DoSaveStoreBGImgPos(){
   if ( saveBGIXML.readyState == 4){
     hideSaveBGIControls()
   }
 }

  </SCRIPT>
</head>
<body onload='pcInit()' style='background:white;margin:0px;'/>
<div style='background:white;padding:0px;'>
<style>
BODY {
	font-family:MS Sans Serif, Geneva, sans-serif;
}
</style>
<div
	onmouseover="showBannerControls();" onmouseout="bannerTimer = setTimeout('hideBannerControls()',500);"
	ID="bannerHolder" style="margin:0px;text-align:right;padding:4px;height:200px;background-attachment:fixed;background-position:right <?php echo $bgOffset;?>px;background-image:url('<?php echo $bgImg;?>');">
	<div ID="bannerControl"
	  onmouseover="wzClearTimer(bannerTimer);" 
	  style="white-space:nowrap;padding:2px;position:absolute;right:10px;top:60px;visibility:hidden;border:0px solid #777777;margin:7px;text-align:right;background:white;">
	 </div>
	   

</div>
   <div ID="menuMark" style='text-align:left;padding-bottom:5px;padding-left:5px;'>
   </div>

<table style='width:100%;'><tr valign='top'><td style='width:50%;'>
<div style="width:100%;margin-top:10px;padding:10px;padding-left:5px;background:#f9f9f9;border-radius: .5em;border:0px solid #efefef;">
	<img style="float:right;border:0px;border-radius:0.5em;" src="https://image.bitmonky.com/img/potofgold.png"/>
	<h2>Photo Assignment</h3>
	<h3>This assignment is worth 50 gold coins on completion</h3>
		
	To earn gold for this task you are required to find or create a
	background image for this geographic location.
	<p/>
	The task should take about 10 minutes maximum to complete.
	<p/>
	<?php 
	if ($nCredits < 1){
	  if ($nCredits == 0){
	    if ($userID != $bannerUID){
	      echo '<h3 style="color:brown;"> Another member is working in this task... Please select another assignment.</h3>';
		}
		else {
	      echo '<span style="color:brown;"> This task needs copy right information before we can pay you.</span>';
		  echo '<br/><a href="javascript:popCopyRight('.$bannerID.');">Click Here To Add Them</a>';
	    }
	  }
	  else  {
	    if ($nBanners  < $maxBanners ){
	      echo 'To start this assignment <a href="javascript:popStoreBannerLoader();">Click Here</a>';
		}
		else {
	      echo '<h3 style="color:brown;">Thank You... The Quota For This Task Has Been Filled!</h3>';
		}
		
	  }
	} 
	else {
	   echo '<h3 style="color:brown;">Thank You... The Quota For This Task Has Been Filled!</h3>';
	}
	?>
</div>
</td><td style="padding-left:25px;">
  <div style="width:100%;margin-top:10px;padding:10px;padding-left:5px;background:#f9f9f9;border-radius: .5em;border:0px solid #efefef;">
  <h3>Tips For This  Assignment</h3>
  Google images are a good source since you can see the  images size by placing your mouse over the image and google
  will display the size in pixles. Google also provides links to where the photo was taken from (useful for providing photo credit info).
  <p/>
  Do NOT resize photographs this destroys image quality... Instead select images that are as large or larger then 
  the minimum 1200 x 400 pixels.
  <p style="color:brown;font-wieght:bold">
  Select only quality photos the are GOOD representations of the city.  Make sure your photo credits
  are accurate! DO NOT load more then one per city.</p>
	  
	  
  <p style="color:brown;font-wieght:bold">
  DO NOT credit google as the source... click on the photo in google and get the source website
  that google got it from.</p>

		  <h3>More Assignments Like This</h3>
  <?php
  $SQL = "select cityID,name,bannerID,cprTaskStatus from tblCity ";
  $SQL .= "inner join tbltaskCities on taskCityID=cityID ";
  $SQL .= "left join tblCopyRightCredits on bannerID = cprAcItemID ";
  $SQL .= "where (bannerID is null or cprTaskStatus is null) and taskDone is null ";
  $SQL .= "order by name";

  if ($userID == 17621){
    $SQL = "SELECT top 75 cityID,name,taskBannerID as bannerID,taskDone as cprTaskStatus from tblTaskCities ";
    $SQL .= "inner join tblCity on cityID = taskCityID ";
    $SQL .= "where NOT taskBannerID is null and taskApproved is null ";
    $SQL .= "order by name";
  }
  
  $result = mkyMsqry($SQL) or die($SQL);
  $cRec = null;
  $resultCR = mkyMsqry($SQL);
  $cRec = mkyMsFetch($resultCR);
  if(!$cRec){
     echo "No city tasks left to fill...";
  }
  while ($cRec){
    $status = "";
	if ($cRec['bannerID'] && $cRec['cprTaskStatus']===null){
	  $status = ' - taken';
	}
    echo "<a href='myTownChangeAssignment.php?wzID".$sKey."&fcityID=".$cRec['cityID']."'>".$cRec['name']."</a>".$status."<br>";
    $cRec = mkyMsFetch($resultCR);
  }

  ?>
  </div>
</td></tr></table>
<?php
//include_once("../pcFooter.php");


?>
