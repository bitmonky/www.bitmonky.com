<?php
if (isset($_GET['fwzUserID'])){ $viewUID  = safeGET('fwzUserID');} else { $viewID = 0;}
if (isset($_GET['fmode']))    { $mode     = safeGET('fmode');}            else { $mode = null;}
if (isset($_GET['fitemID']))  { $inItemID = safeGET('fitemID');}          else { $inItemID = null;}
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
    <?php
	if ($nBanners  < $maxBanners ){
	   echo "parent.wzAPI_showFrame('/whzon/mytown/popLoadBGImg.php?wzID=".$sKey."&fwzUserID=".$wzUserID."&fscope=".$scope."&fsmCID=".$fsmCID."',500,550,444,getYOffset('menuMark')+225);";
    }
	else {
	  echo "alert('A Banner For This Task Is Already Loaded');";
	}
	?>
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
