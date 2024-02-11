<?php
if (isset($_GET['fwzUserID'])){ $viewUID = clean($_GET['fwzUserID']);} else { $viewUID = 0;}
if (isset($_GET['fmode'])) { $mode = clean($_GET['fmode']);} else { $mode = null;}
if (!isset($Title)){
  $Title = $whzdom;
}
?>
<!doctype html>
<html class='pgHTM' lang="en">
<head>
  <meta charset="utf-8"/>
  <?php
  if (isset($_SERVER['QUERY_STRING'])){$qry = "?".$_SERVER['QUERY_STRING'];}else {$qry="";}
  $furl = $_SERVER['SCRIPT_NAME'].$qry;
  setHeaderTags($furl);
  if (isset($title)){$Title = $title;}
  ?>
  <meta charset="utf-8"/>
  <title><?php echo $mPgTitle;?></title>
  <meta name="keywords" content="<?php echo $mKeywords;?>"/>
  <meta name="description" content="<?php echo $mPgDesc;?>"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta property="og:title" content="<?php echo $mPgTitle;?>"/>
  <meta property="og:url" content="https://www.bitmonky.com?furl=<?php echo urlencode($furl);?>"/>
  <meta property="og:image" content="<?php echo $mPgImage;?>"/>
  <meta property="og:description" content="<?php echo $mPgDesc;?>" />
  <?php if ($sessISMOBILE){
    echo '<link rel="stylesheet" href="/whzon/mblp/mobile.css?v=1.0"/>';
  }
  else {
    echo '<link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>';
  }?>
  <script src="/whzon/sessmgrJS.php"></script>
  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script src='/whzon/wzToolboxJS.php?v=2'></script>
  <script>
  function pcInit() {
    <?php
    if ($thisIsME){
      echo "showMyWork();";
    }
    if (isset($giftsWaiting)){
	  if ($giftsWaiting !=0){
	     echo "showMyGifts();";
	  }
	}
    if (isset($pgiftsWaiting)){
	  if ($pgiftsWaiting !=0){
	     echo "showMyPGifts();";
	  }
	}
    ?>
    wzInitJS(1);
	doPageEmotes();
    if(typeof wzOnLoad == 'function')
     { wzOnLoad(); }
  }
  function doPageEmotes(){
    var ustatus = document.getElementById('mbrStatusTxt'); 
	if (ustatus){
	  ustatus.innerHTML = parent.doEmotes(ustatus.innerHTML);
	}
  }
  function showStatusCtr(){
  }
  function hideStatusCtrl(){
  }
  function getStatusForm(){
  }
  function getYOffset(id){
    var el = document.getElementById(id); 
	return getOffset(el).top;
  }
  function wzAPI_getOffset( el ) {
    var _x = 0;
    var _y = 0;
    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop;
        el = el.offsetParent;
    }
    return { top: _y, left: _x };
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
  openTipSpot = null;
  function hideActivityTip(ID){
    openTipSpot = null;
    var spot   = document.getElementById('likeTipSpot'+ID);
    if (spot){
      spot.innerHTML = '';
      spot.style.display = 'none';
    }
  }
  function sendLikeTip(ID){
    var conf = confirm('Send Tip To The Owner Of This Post?');
    if (!conf){
      return;
    }
    var xml = parent.getHttpConnection();
    var currentTime = new Date();
    var ranTime = currentTime.getMilliseconds();
    var spot   = document.getElementById('likeTipSpot'+ID);
    var amt    = document.getElementById('tipAmt').value;
    var com    = document.getElementById('tipComment').value;
    
    var url = '/whzon/mbr/sendLikeTip.php?wzID=<?php echo $sKey;?>&tipAmt='+amt+'&tipComment='+encodeURIComponent(com)+'&acID=' + ID + '&xr=' + ranTime;
    xml.timeout   = 20*1000;
    xml.open("GET", url, true);
    xml.onreadystatechange = function(){
      if (xml.readyState == 4){
        if(xml.status  == 200){
          var j = null;
          j = xml.responseText;
          try {j = JSON.parse(xml.responseText); }
          catch(err) {
            alert('pars json failed'+xml.responseText,err);
            hideActivityTip(ID);
            return;
          }
          if (j.message){
            spot.innerHTML = j.message;
            if (j.result){
              parent.doRefreshLikes(ID);
            }
            var timeo = setTimeout( ()=>{
              hideActivityTip(ID);
            },2*1000);
          }
          return;
        }
      }
    };
    xml.send(null);
  }
  function activityTip(ID){
    if(openTipSpot){
      hideActivityTip(openTipSpot);
    }
    openTipSpot = ID;

    var xml = parent.getHttpConnection();
    var currentTime = new Date();
    var ranTime = currentTime.getMilliseconds();
    var spot   = document.getElementById('likeTipSpot'+ID);

    var url = '/whzon/mbr/popLikeTip.php?wzID=<?php echo $sKey;?>&acID=' + ID + '&xr=' + ranTime;
    xml.timeout   = 20*1000;
    xml.open("GET", url, true);
    xml.onreadystatechange = function(){
      if (xml.readyState == 4){
        if(xml.status  == 200){
          var j = null;
          j = xml.responseText;
          spot.style.display = 'block';
          spot.innerHTML = j;
        }
      }
    };
    xml.send(null);
  }  
  function activityBoost(ID){
    var elID = 'Boost' + ID;
    yoff = getYOffset(elID) - 200;
    parent.wzAPI_showFrame('/whzon/mbr/popBoostLike.php?wzID=<?php echo $sKey;?>&acID=' + ID,350,350,500,yoff);
  }
  function gotoMiniBLOG(){
    parent.wzGetPage('/whzon/mbr/blog/mbrMBLOG.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $viewUID;?>');
  }
  function deleteActivity(ID){
    var elID = 'mbrACT' + ID;
	yoff = getYOffset(elID);
	parent.wzAPI_showFrame('/whzon/mbr/popDeleteActivity.php?wzID=<?php echo $sKey;?>&acID=' + ID,350,250,550,yoff);
  }
  function popBannerLoader(){
	parent.wzAPI_showFrame('/whzon/mbr/popLoadBGImg.php?wzID=<?php echo $sKey;?>',350,550,550,100);
  }
  function popStoreBannerLoader(){
	parent.wzAPI_showFrame('/whzon/store/popLoadBGImg.php?wzID=<?php echo $sKey;?>&fstoreID=<?php if (isset($storeID)){ echo $storeID; }?>',350,550,550,100);
  }


  function deleteQuestion(ID){
    var elID = 'qaElm' + ID;
	yoff = getYOffset(elID);
	parent.wzAPI_showFrame('/whzon/mbr/popQADeleteFrm.php?wzID=<?php echo $sKey;?>&fqaID=' + ID,350,250,550,yoff);
  }
  function replyQuestion(ID){
    var elID = 'qaElm' + ID;
	yoff = getYOffset(elID);
	parent.wzAPI_showFrame('/whzon/mbr/popQAReplyFrm.php?wzID=<?php echo $sKey;?>&fqaID=' + ID, 550,450,400,yoff);
  }

  var newlikes  = null;
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

  var conRU=null;
  var conNF=null;
  var conUF=null;
  var conAU=null;
  var conCU=null;
  var conMT=null;
  
  var divRU=null;
  var divNF=null;
  var divUF=null;
  var divAU=null;
  var divCU=null;
  var divMT=null;
  
  var chanTimer=null;
  var feedTimer=null;
  var acFeedTime = null;
  var chanPg=null;
  var chanQry=null;
  
  var giftXML     = parent.getHttpConnection();
  var giftPXML    = parent.getHttpConnection();
  
  var imgRotXML   = parent.getHttpConnection();
  var imgRotPXML  = parent.getHttpConnection();
  var saveBGIXML  = parent.getHttpConnection();
  var saveMPPXML  = parent.getHttpConnection();
  var workXML     = parent.getHttpConnection();
<?php if ($thisIsME){?>
//**********************************
// Show Work Assignments
//**********************************

function showMyWork(){
   var url="/whzon/mbr/getMyWorkAssign.php?wzID=" + parent.sID;
   wzAPI_prepUrl(url,workXML);
   workXML.onreadystatechange = DoShowMyWork;
   workXML.send(null);
}

function DoShowMyWork(){
  if (workXML.readyState == 4){
    if (workXML.status = 200){
      var srcHTML = workXML.responseText;
      var wzoutput = document.getElementById("workAssignSpot");
      wzoutput.innerHTML = srcHTML;
      parent.window.scrollTo(0,0);
    }
  }
}
<?php }?>
//************************
// Gift Request functions
//************************

function checkForGiftShopperProfile(giftID){
  var imgELID = parent.document.getElementById('wzBrowser');
  var xoff =  parent.wzAPI_getOffset(imgELID).left;
  parent.wzAPI_showFrame('/whzon/store/getShopperGiftSizeProfile.php?wzID=<?php echo $sKey;?>&fgiftID=' + giftID,540,600,xoff,parent.layersViewTop);
  parent.window.scrollTo(0,0);
}
function getGiftItemColor(giftID){
  var imgELID = parent.document.getElementById('wzBrowser');
  var xoff =  parent.wzAPI_getOffset(imgELID).left;
  parent.wzAPI_showFrame('/whzon/store/getGiftItemColor.php?wzID=<?php echo $sKey;?>&fgiftID=' + giftID,540,600,xoff,parent.layersViewTop);
  parent.window.scrollTo(0,0);
}
function acceptGift(giftID,reqProf,reqColor){
  var imgELID = parent.document.getElementById('wzBrowser');
  var xoff =  parent.wzAPI_getOffset(imgELID).left;
  parent.wzAPI_showFrame('/whzon/store/frmViewGiftForAccept.php?wzID=<?php echo $sKey;?>&fgiftID=' + giftID,540,600,xoff,parent.layersViewTop);
  parent.window.scrollTo(0,0);
}
function showMyGifts(){
   var url="/whzon/store/myGiftFeed.php?wzID=" + parent.sID;
   wzAPI_prepUrl(url,giftXML);
   giftXML.onreadystatechange = DoShowMyGifts;
   giftXML.send(null);
}

function DoShowMyGifts(){
  if (giftXML.readyState == 4){

    var srcHTML = giftXML.responseText;
    var wzoutput = document.getElementById("giftFeed");
    wzoutput.innerHTML = srcHTML;
	parent.window.scrollTo(0,0);
  }
}
function hideGiftViewer(){
    var wzoutput = document.getElementById("giftFeed");
    wzoutput.innerHTML = "";
}
//***************************
//Gifts Purchased For Me
//***************************
function showMyPGifts(){
   var url="/whzon/store/myGiftsPurchasedFeed.php?wzID=" + parent.sID;
   wzAPI_prepUrl(url,giftPXML);
   giftPXML.onreadystatechange = DoShowMyPGifts;
   giftPXML.send(null);
}

function DoShowMyPGifts(){
  if (giftXML.readyState == 4){

    var srcHTML = giftPXML.responseText;
    var wzoutput = document.getElementById("giftsPurchasedFeed");
    wzoutput.innerHTML = srcHTML;
	parent.window.scrollTo(0,0);
  }
}
//************************
//Alerts
//************************
function doAlertFriends(){
  var wzoutput = document.getElementById("falertwait");
  wzoutput.innerHTML="<br><div style='display:inline;height:28px;color:#777777;' ><div class='mkyloader'></div>Sending Please Wait...</div><img onload='executefAlerts();' style='display:none;width:35px;height;35px;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/imgLoading.gif'>";
}
function executefAlerts(){
  document.getElementById("friends").submit();
}
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
  wzoutput.innerHTML = "<br><img onload='executeAlerts();' style='display:none;width:35px;height;35px;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/imgLoading.gif'><div style='display:inline;height:28px;color:#777777;' ><div class='mkyloader'></div>Sending Please Wait...</div>";
}
function executeAlerts() {
  document.getElementById("pquestion").submit();
}
function rotatePLargeImg(rDirection){
   var url="/whzon/mbr/rotatePLargeImg.php?wzID=" + parent.sID + "&fd=" + rDirection;
   wzAPI_prepUrl(url,imgRotXML);
   imgRotXML.onreadystatechange = DoRotatePLargeImg;
   imgRotXML.send(null);
}

function DoRotatePLargeImg(){
  if (imgRotXML.readyState == 4){
    if(imgRotXML.status  == 200){ 
//      var srcHTML=imgRotXML.responseText;
//      var wzoutput = document.getElementById("profTextImg");
//      var imgHTML=wzoutput.innerHTML;
      window.location.reload();
    }
  }
}
function rotateThmnImg(rDirection){
   var url="/whzon/mbr/rotatePTmnImg.php?wzID=" + parent.sID + "&fd=" + rDirection;
   wzAPI_prepUrl(url,imgRotPXML);
   imgRotPXML.onreadystatechange = DoRotateThmnImg;
   imgRotPXML.send(null);
}

function DoRotateThmnImg(){
  if (imgRotPXML.readyState == 4){
    if(imgRotPXML.status  == 200){ 
//      var srcHTML=imgRotPXML.responseText;
//      var wzoutput = document.getElementById("profTextImg");
//      var imgHTML=wzoutput.innerHTML;
      window.location.reload();
    }
  }
}
  
  function doFollowMbrACT(id,mbrID){
    var el = document.getElementById(id);
    var xOff=wzAPI_getOffset(el).left;
    var yOff=wzAPI_getOffset(el).top;
    var ofrName = window.frameElement.id;
    wzAPI_showFrame('/whzon/mbr/fanRequestFrm.php?wzID=<?php echo $sKey;?>&fanID=' + mbrID + '&ofrname=' + ofrName ,340,180,xOff,yOff,popsCont);
  }

  function wzInitJS($vstatus){
    wzStartApp();
	acFeedTime = 300*1000;
	mainPicControl = document.getElementById('mainPicControl');
	mainPicHolder  = document.getElementById('mainPicHolder');
	bannerControl  = document.getElementById('bannerControl');
	bannerHolder   = document.getElementById('bannerHolder');
	tmnailControl  = document.getElementById('tmnailControl');
	pTextControl   = document.getElementById('pTextControl');
	saveBGIControl = document.getElementById('saveBGIControl');
	saveMPPControl = document.getElementById('saveMPPControl');
	
	likesConn = getHttpConnection();
    divUF = document.getElementById('wzUserACTFeed');
    popsCont = 'wzPopContainer';

    fetchUF();
  }
  // connection loaders
  function fetchUF(){
    <?php if (isset($storeID)) {?>
      reloadElement(conUF,'/whzon/store/storeItemFeed.php?wzID=<?php echo $sKey;?>&fstoreID=<?php  if (isset($storeID)) {echo $storeID;}?>',divUF,acFeedTime,cbk_UF);
    <?php } else { 
      if ($mode == 'qa') {?>
         reloadElement(conUF,'/whzon/mbr/mbrQAFeed.php?wzID=<?php echo $sKey;?>&viewUID=<?php echo $viewUID;?>',divUF,acFeedTime,cbk_UF);
      <?php } else { 
	    if ($mode == 'shout'){?>
	      reloadElement(conUF,'/whzon/mbr/friendxmlData.php?wzID=<?php echo $sKey;?>',divUF,acFeedTime,cbk_UF);
	    <?php } else {?>
	      reloadElement(conUF,'/whzon/mbr/mbrActivityFeed.php?wzID=<?php echo $sKey;?>&viewUID=<?php echo $viewUID;?>&facMode=<?php if(isset($facMode)){echo $facMode;}?>',divUF,acFeedTime,cbk_UF);
        <?php }
      }
	}?>
  }
  function acListenToSong(url,id){
    if (feedTimer) { clearTimeout(feedTimer);}
    feedTimer = setTimeout('fetchUF()',10*60*1000);
    var output = document.getElementById('acMoshViewer' + id);
    if (output){
      output.innerHTML = '<iframe width=" 100%" height="250" src="' + url.replace('http:','') + '?autoplay=1" frameborder="0" allowfullscreen=""></iframe><div align="right"><a href="javascript:acCloseSong(' + id + ');">close[x]</a></div>';
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
    function activityVoteTxt(vote,acID){
     var liketxt = document.getElementById('frmlike' + acID).fliketxt.value;
	 if (liketxt == '') {
	   alert('comment on why you ' + vote + ' this!');
	 }
	 else {
       var waiting = document.getElementById('newLikeSpot' + acID);
	   if (waiting) {
	     waiting.innerHTML = '<img style="display:none;width:35px;height;35px;" src="<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/imgLoading.gif"/><div style="display:inline;height:28px;color:#777777;" ><div class="mkyloader"></div>Please Wait...</div>';
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
	    clearTimeout(feedTimer);
	    fetchUF();
      }
    }
  }

  //connection callbacks
  function cbk_UF(con,src,id,result,timer){
    id.innerHTML=result;
    feedTimer = setTimeout('fetchUF()',timer);
    fhReDrawFrame();
} 
  function wzClearTimer(wztimer){
    clearTimeout(wztimer);
	wztimer = null;
  }
  
  function showMainPicControls(){
    wzClearTimer(mainPicTimer);
    mainPicControl.style.visibility = "visible";
  }
  function hideMainPicControls(){
    mainPicControl.style.visibility = "hidden";
    wzClearTimer(mainPicTimer);
  }
  function hideTmnailControls(){
    tmnailControl.style.display = "none";
    wzClearTimer(tmnailTimer);
  }
  function showTmnailControls(){
    wzClearTimer(tmnailTimer);
    tmnailControl.style.display = "block";
    hidePTextControls();
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
  function hidePTextControls(){
    pTextControl.style.display = "none";
    wzClearTimer(pTextTimer);
  }
  function showPTextControls(){
    wzClearTimer(pTextTimer);
    pTextControl.style.display = "block";
    hideTmnailControls();
  }
  
  function hideSaveBGIControls(){
    saveBGIControl.style.visibility = "hidden";
  }
  function showSaveBGIControls(){
    if (bgMode){
      saveBGIControl.style.visibility = "visible";
	}
  }
  function hideSaveMPPControls(){
    saveMPPControl.style.visibility = "hidden";
  }
  function showSaveMPPControls(){
    saveMPPControl.style.visibility = "visible";
  }

 function replaceAll(find, replace, str) {
	return str.replace(new RegExp(find, 'g'), replace);
  }

 function mainPicScroll(amtx,amty){
   var p = mainPicHolder.style.backgroundPosition;
   p = replaceAll('px','',p);
   psplit = p.split(" ");
   mppXoff = parseInt(psplit[0]) + amtx;
   mppYoff = parseInt(psplit[1]) + amty;
   var pnew = mppXoff + 'px ' + mppYoff + 'px';
   mainPicHolder.style.backgroundPosition = pnew;
   showSaveMPPControls();
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
   var url="/whzon/mbr/cropBGImg.php?wzID=<?php echo $sKey;?>&yoff=" + bgOffset + '&id=' + bgImgID;
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
   var url="/whzon/store/cropBGImg.php?wzID=<?php echo $sKey;?>&yoff=" + bgOffset + '&id=' + bgImgID;
   wzAPI_prepUrl(url,saveBGIXML);
   saveBGIXML.onreadystatechange = DoSaveStoreBGImgPos;
   saveBGIXML.send(null);
 }

 function DoSaveStoreBGImgPos(){
   if ( saveBGIXML.readyState == 4){
     hideSaveBGIControls()
   }
 }

 function saveMainPicPos(){
   var url="/whzon/mbr/savePTextPos.php?yoff=" + mppYoff + '&xoff=' + mppXoff + '&id=<?php echo $userID;?>&wzID=<?php echo $sKey;?>';
   wzAPI_prepUrl(url,saveMPPXML);
   saveMPPXML.onreadystatechange = DoSaveMainPicPos;
   saveMPPXML.send(null);
 }

 function DoSaveMainPicPos(){
   if ( saveMPPXML.readyState == 4){
     hideSaveMPPControls()
   }
 }
<?php
include_once("frameHandlerJS.php");
?>
  </SCRIPT>
</head>
<body onload='frameInit()' class='pgBody' style='margin:0px;' />
<script>
  const observer = new ResizeObserver(entries => {
  console.log('MKFentries',entries);
  for (let entry of entries) {
    const height = entry.contentRect.height;
    parent.onPageFrameResize(height);
    console.log('MKFHeight changed:', height);
  }
});
function doWatchDiv(){
  var obsvdiv = null;
  try {obsvdiv = document.querySelector('.watchDiv');}
  catch(e){
    console.log('MKFerr:',e);
  }
  if (obsvdiv){
    observer.observe(obsvdiv);
  }
}
</script>
<div style='padding:0px;'>
