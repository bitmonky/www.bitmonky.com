<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
$mobile=null;
$userIsMod=null;
$userIsAdmin=null;
include_once("../mkysess.php");

$mode = 'pvc';
include_once("../spamStatInc.php");

  $mbrID=clean($_GET['fmbrID']);
  $bothUseChrome = null;
  $otherName     = null;
  $pvChatSetting = null;
  
  if ($mbrID == 17621){$isADMIN = True;} else {$isADMIN = null;}
  $SQL = "select wzWRTCon,firstname,privateChat  from tblwzUser  where wzUserID=".$mbrID;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  if($tRec){ 
    $otherName = $tRec['firstname'];
	$pvChatSetting = $tRec['privateChat'];
    if ($tRec['wzWRTCon'] && $wzWRTCon){
      $bothUseChrome = 1;
	}
  }
  $weAREFRIENDS = null;
//  if ($pvChatSetting == 1){
    $SQL = "select count(*) as nRec from tblwzUserFriends  ";
	$SQL .= "where status = 1 and ((wzUserID=".$mbrID." and friendUserID=".$userID.") or (wzUserID=".$userID." and friendUserID=".$mbrID."))";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
	if ($tRec['nRec'] > 0){
	  $weAREFRIENDS = 1;
	}  
//  } 
  $callerBLOCKED=False;

  $SQL = "SELECT count(*) as nRecs from tblwzUserBlockList  WHERE wzUserID=".$mbrID." and blockUserID=".$userID;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
    
  if ($tRec['nRecs']>0){
    $callerBLOCKED=True;
 }
  
  $SQL = "update tblwzOnline set privateChat =".$mbrID." where wzUserID=".$userID;
  $result = mkyMsqry($SQL);
    
  if ($mobile==True){
    
    header("mblChatApp.php?fmbrID=".$mbrID);
    exit();
  }
?>
<!doctype html>
<html class='pgHTM' lang="en">
<head>
  <meta charset="utf-8"/>
  <title></title>
  <meta name="keywords" content=""/>
  <meta name="description" content=""/>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>
  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

<script>
var dbugt=new Date();
var dbgT=dbugt.getTime();
var zbugs=0;
var scrwidth = Math.round(screen.width * 0.28);
var formatDiv = "<div class='infoCardClear' style='background:#111111;padding:1em;border-radius:0.25em;width:calc(100% - 2.2em);margin-top:10px;'>";


var refresh=30;
var isThere=false;
var vChatStarted=false;
var userID=<?php echo $userID;?>;
window.onfocus = keepInView;

var onePVCT    = null;
var pvcPMode   = 'all';
function doOnePoll() {
   if (onePVCT) {clearTimeout(onePVCT);}
   var onexml = parent.getHttpConnection();
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url = '/whzon/pvchat/pvcPollOnce.php?wzID=' + parent.sID +  '&fmode=' + pvcPMode +'&fmbrID=<?php echo $mbrID;?>&xr=' + ranTime;
   onexml.timeout   = 20*1000;
   onexml.ontimeout = doOnePoll;
   onexml.onerror   = function (){
     onePVCT = setTimeout('doOnePoll()',15*1000);
   }
   onexml.open("GET", url, true);
   onexml.onreadystatechange = function(){
     if (onexml.readyState == 4){
       if (onexml.status  == 200){
         var jdata = mkyTrim(onexml.responseText);
         var j = null;
         try {j = JSON.parse(jdata); }
         catch(err) {
           dbug("JSON fail: PVConePoll fail -> " + urldecode(jdata));
           j = null;
         }

         if (j){
           console.log('pvc onePoll ',j);
           excWriteLastAction(j);
           excWriteMosh(j);
           excWriteMyAlerts(j);
           excWriteMyGroups(j);
           excWriteMsg(j);
           excWriteContacts(j);
           excWriteAllContacts(j);
         }
         onePVCT = setTimeout('doOnePoll()',15*1000);
       }
     }
   };
   onexml.send(null);
}
function keepInView(){
  if (parent.winWRTCChat){
    showWRTCwindow();
  }
}


function showWRTCwindow(){
  parent.winWRTCChat.focus();
}
function deleteCallRecord(){
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='/whzon/wzWRTC/deleteCallRecord.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>&xm=' + ranTime ;
   vChatxml.open("GET", url,true);
   vChatxml.onreadystatechange = function() {};
   vChatxml.send(null);
}

function declineCall(msgID=null){
   if (msgID){
     removeNotice(msgID);
   }
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='/whzon/wzWRTC/declineCall.php?wzID=<?php echo $sKey;?>&fcallerID=<?php echo $mbrID;?>&xm=' + ranTime ;
   vChatxml.open("GET", url,true);
   vChatxml.onreadystatechange = function() {if (readxml.readyState == 4){document.location.reload();}};
   vChatxml.send(null);
   
}

function WRTChangUp(){
   deleteCallRecord();
   alert('video call has ended');
   document.location.reload();
}
function startWRTCChat(){
  var callTo = <?php echo $mbrID;?>;
  if (callTo == 63555){
    alert('Sorry `Agent SiteMonkey AI` Can Not Video Chat');
    return;
  }
  parent.openVideoChatFrame("/whzon/wzWRTC/videoCall.php?fcallToID=<?php echo $mbrID."&wzID=".$sKey;?>","480px","750px");
  document.location.reload();
}
function removeNotice(id){
   if (ifrm){
     var div = ifrm.document.getElementById(id);
     if (div){
       div.style.display = 'none';
     }
   }
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='/whzon/pvchat/deleteVChatNotice.php?wzID=<?php echo $sKey;?>&msgID=' + id + '&xm=' + ranTime ;
   vChatxml.open("GET", url,true);
   vChatxml.onreadystatechange = function() {};
   vChatxml.send(null);
}
function testAcceptVChat(url,msgID=null){
  if (msgID){
    removeNotice(msgID);
  }
  var isme = 'fcallFromID=<?php echo $userID;?>';
  var findit = url.search(isme);

  if ( findit == -1 && !parent.winWRTCChat){
    parent.openVideoChatFrame(url,"480px","750px");
  }
  document.location.reload();
}
function reloadBlocks(){
  parent.readBlockList();
}

    function doClick(e)
    {
       var key;

         if(window.event)
           key = window.event.keyCode;     //IE
         else
           key = e.which;     //firefox
    
       if (key == 13){
          sendMsg();
          return false;
      }
    }

function openPShare(){
  wzAPI_showFrame('/whzon/pvchat/popfrmSharePhoto.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>',350,250,200,200,'wzAppsContainer');
}
function popReportImg(imgID){
  wzAPI_showFrame('/whzon/pvchat/popReportImg.php?fimgID=' + imgID + '&wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>',350,250,200,200,'wzAppsContainer');
}

function pshare(id,ck){
  wzAPI_showFrame('/whzon/pvchat/popShowPhoto.php?wzID=<?php echo $sKey;?>&id=' + id + '&ck=' + ck ,640,440,25,130,'wzAppsContainer');
  }

  function redirectToAd(url){
  parent.location.href = url;
  }

  function wzGotoPage(pUrl){
  if (opener && !opener.closed) {
    opener.parent.location.href=pUrl;
    opener.parent.focus();
    window.close();
    }
  else
    window.location.href=pUrl;
}
function wzGoToMyMailBox(){
}
function wzGoToMyFriends(){
}


function afOpenGigBox(boxID){
  winName = "wzGigBox";
  var win2 = window.open("//whzon.com/mosh/moshBox.asp?fboxID=" + boxID ,winName,"target=new,width=506,height=500,resizable=no,scrollbars=no");
  win2.focus();
}
function init(){
   wzMblReDrawPage();
   parent.pvcCurUID = <?php echo $mbrID;?>;
   console.log('pvc current  UID',parent.pvcCurUID);
   <?php
   if ($userID != 17621){
     if ($pvChatSetting == 2 || $callerBLOCKED || ($pvChatSetting == 1 && !$weAREFRIENDS)){
       echo "alert('This Members Private Chat Is Not Available');";
	   echo "parent.wzAPI_closePVC();";
	   echo "return null;";
     }
   }
   ?> 
   doOnePoll();
}
function checkChatStatus(){
   <?php if (!$isADMIN){?>
   var pvchat = xmGetRadioValue("fpvc");
   if (pvchat == 'off'){
     alert('your  chat is : `' + pvchat + '` Change your private chat settings to `ON` or `Friends Only` to chat');
   }
   if (pvchat == 'friends'){
   <?php
   if (!$weAREFRIENDS){
     echo "alert('your  chat is set to `Friends Only` This person is not on your friends list. You need add them as a friend or change your chat settings to ON');";
   } 
   ?>
   }	
   <?php }?>
}
function tAGetMissingInfo(){
   init();
   wzAPI_setRefreshPg(1);
   wzAPI_showFrame("//bitmonky.com/whzon/signup/fastJoin.php?mode=v&wzID=<?php echo $sKey;?>",400,450,50,100);
}
var VCname;
var VCuserID;
var VCmbrID;

function VCenterChat(roomNbr){

  vChatStarted=true;
  parent.wzEnterVC(roomNbr,VCname);
  window.location.reload();
}

function reqVChat(){

  vChatStarted=true;
  VCname='<?php echo $userName;?>';
  VCuserID=<?php echo $userID;?>;
  VCmbrID=<?php echo $mbrID;?>;

  wzAPI_setRefreshPg(1);
  wzAPI_showFrame("/whzon/vchat/vChatWaitfor.php?wzID=<?php echo $sKey;?>&fname=" + VCname + "&wzUserID=<?php echo $userID;?>&wzCallBackID=" + VCmbrID,350,200,150,250);
}


function wzAcceptVChat(cBckID){
  return;
  if (parent.wzUserID == 17621){
    var URL="/whozon/vChat/vPhonet.asp?fname=<?php echo mkyUrlEncode($userName);?>&wzUserID=" + <?php echo $userID;?>;
    parent.openVideoChatFrame(URL,"480px","750px");
    vChatStarted=true;
  }
  else {
    vChatStarted=true;
    var URL="/whozon/vChat/vPhonet.asp?fname=<?php echo mkyUrlEncode($userName);?>&wzUserID=" + <?php echo $userID;?>;

    var winName = "wzPopVChatReq";

    var vChatWin= window.open(URL,winName,"target=new,width=1000,height=700,resizable=no,scrollbars=vertical");

    vChatWin.focus();
  }
}
function xmUpdatePVChat(){

   var xm = new Date();
   var pvchat = xmGetRadioValue("fpvc");
   var url='/whzon/pvchat/updatePVChat.php?wzID=<?php echo $sKey;?>&fpvc=' + pvchat + '&' + xm.getMilliseconds();
   wzPVxml.open("GET", url,true);
   wzPVxml.onreadystatechange = xmDoUpdatePVC;
   wzPVxml.send(null);
}

function xmDoUpdatePVC(){
  if (wzPVxml.readyState == 4){
    if(wzPVxml.status  == 200){ 
      var alertID=wzPVxml.responseText;
      window.location.reload();
    }
  }
}

function xmGetRadioValue(theRadioGroup)
{
    for (var i = 0; i < document.getElementsByName(theRadioGroup).length; i++)
    {
        if (document.getElementsByName(theRadioGroup)[i].checked)
        {
                return document.getElementsByName(theRadioGroup)[i].value;
        }
    }
}
function removeAll(){
  var c = confirm("Warning This Will Delete All Your Private Conversations!");
  if (c == true) {
    document.location.href = "removeAll.php?wzID=<?php echo $sKey;?>";
  } 
}
function updatePvcFocus(){
  parent.pvcInFocus = true;
  console.log('pvc must be infocuse ',parent.pvcInFocus);
}
<?php include_once('urlAutoShareJS.php');?>
</script>
</HEAD>
<?php
$isMod=null;
if ($userIsMod || $userIsAdmin){ 
  $isMod=True;
}
$IAmBLOCKED=False;

$SQL = "SELECT privateChat from tblwzUser  WHERE wzUserID=".$userID;
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
    
$pvChat = $tRec['privateChat'];
$pvON = '';
$pvFR = '';
$pvOF = '';

if (is_null($pvChat)){
  $pvON = "checked='checked'";
}

if ($pvChat == 1){
  $pvFR = "checked='checked'";
}

if ($pvChat == 2) {
  $pvOF = "checked='checked'";
}

$SQL = "SELECT count(*) as nRecs from tblwzUserBlockList  WHERE wzUserID=".$userID." and blockUserID=".$mbrID;
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
    
if ($tRec['nRecs']>0){
  $IAmBLOCKED=True;
}  

$SQL = "SELECT  tblwzUser.franchise, tblwzUser.privateChat, tblwzUser.verified,tblwzUser.cityID,tblwzUser.banned,tblwzUser.imgFlg, tblwzUser.firstname, tblwzUser.timezone,tblwzUser.city,tblwzUser.prov,tblwzUser.country,tblwzUser.rCode,";
$SQL .= "TIMESTAMPDIFF(second,lastAction,now()) as lastAction, tblwzUser.paidMember,tblwzUser.nfans,tblwzUser.sex,tblwzUser.age,currentIP,tblwzUser.moderator, ";
$SQL .= "tblwzUser.profileText From tblwzUser  left join tblwzOnline  on tblwzOnline.wzUserID = tblwzUser.wzUserID ";
$SQL .= "where tblwzUser.wzUserID=".$mbrID;
      
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
$missingInfo=False;
$franchise = null;
$kingQueenImg = $GLOBALS['MKYC_imgsrv'].'/img/kingProf.png';
$userDeleted = null;

if ($tRec){
  $mbrName=$tRec['firstname'];
  $timezone=$tRec['timezone'];
  $hCity=$tRec['city'];
  $hProv=$tRec['prov'];
  $hCountry=$tRec['country'];
  $paidMember=$tRec['paidMember'];
  $nfans=$tRec['nfans'];
  $sex=$tRec['sex'];
  $age=$tRec['age'];
  $IP=$tRec['currentIP'];
  $bio =left($tRec['profileText'],180)."...";
  $dIP=$IP;
  $franchise = $tRec['franchise'];
  $mbrLastAction = $tRec['lastAction'];
  $mbrRCode      = $tRec['rCode'];

  if (!is_null($IP)) {
    $resultIPC=ipToCountryCD($IP);
    $IPLocation="IP Location - ".$resultIPC;
  }
  $sexHold = $sex;
  if (!is_null($sex)) {
    if ($sex==True) {
      $sex = "f - ";
      $kingQueenImg = $GLOBALS['MKYC_imgsrv'].'/img/jungleQueenS.png';
    }
    else {
      $sex = "m - ";
      $kingQueenImg = $GLOBALS['MKYC_imgsrv'].'/img/kingProf.png';
    }
  }
  if ($sexHold == 3){
    $sex = 'YouTuber';
    $tribStatus = $sex." Hut Owner";
    $age = '';
  }
  if ($sexHold == 4){
    $sex = 'Business Account';
    $tribStatus = $sex." Hut Owner";
    $age = '';
  }
  if ($mbrRCode == 'mbvirtualP'){
    $sex = 'Virtual Assistant';
    $tribStatus = $sex." Hut Owner";
    $age = '';
  }

} 
else {
  $userDeleted = 1;
}

$SQL = "SELECT  suspect, verified,cityID,banned,imgFlg,TIMESTAMPDIFF(day,date(creatDate),date(now())) as nDays From tblwzUser  where wzUserID=".$userID;  
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
$missingInfo=False;
if ($tRec){
  if ($tRec['banned']){
    echo "<script>window.location='getlost.php';</script>";
  }
  else {
    $vCityID=$tRec['cityID'];
    $verified=$tRec['verified'];
    $nDays=$tRec['nDays'];
    $suspect=$tRec['suspect'];

    if ($userID < 402959){
      $verified=1;
    }
    if (is_null($verified)) {
      $verified=0;
    }
    if ($verified==0 && !is_null($suspect)) {
      $missingInfo=True;
    }
    if (is_null($nDays)) {
      $nDays=99999;
    }
    $verified=1;

    $vImgFlg=$tRec['imgFlg'];
    if ($vCityID==0 || $vImgFlg==0 || $verified==0){
      $missingInfo=True; 
    }
  }
}

if ($mbrID==17621){
  $missingInfo=False;
}
?>

<body class='pvcBody' style='background:#444444;' <?php if ($missingInfo) {?> 
onload='tAGetMissingInfo();'<?php } else {echo "onload='init();'";} ?> 
onclick="updatePvcFocus();"
>

<table style='border:0px solid black;width:100%;background:LightSeaGreen;'>
<tr valign='top'>
<td style='background:LightSeaGreen;color:white;padding:8px 0px 5px 5px;'>
<div>
<form style='margin:5px;' ID='chTxtBox' method=get  onSubmit="return sendMsg();">
<input type='hidden' name='fmbrID' value='<?php echo $mbrID;?>'>
<!--
<img style='border-radius: 0.5em;margin-bottom:20px;margin-right:5px;float:left; height:65px;width:50px;border:0px solid #777777;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrImg.php?id=<?php echo $userID;?>'>
-->
<img alt='Start Video Chat' title='Start Video Chat' style='border:0px;width:20px;height:15px;margin-bottom:2px;border-radius:.25em;vertical-align:middle;' 
src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/vChatIcon.png'>
<span ID='vChatAlert'>
<?php
if ($bothUseChrome || $userID == 17621) {
  if ($mbrLastAction < 45 && $mbrLastAction){
    echo "<a style='color:orange;' href='javascript:startWRTCChat();'>".getTRxt('Start Video Chat With')." - ".$mbrName."</a>";
  }
  else {
    echo $mbrName." ".getTRxt('Not Available for Video Chat');
  }
}
else {
  if (!$wzWRTCon) {
    echo "<span style='padding:3px;background:none;border-radius: .5em;'><a style='color:orange;' href='//www.google.com/chrome/' target='_new' >";
    echo getTRxt("Download Chrome And Enjoy Free monkytalk Video Chat!")."</a></span>";
  }
  else {
    echo getTRxt("Sorry")." ".$otherName." ".getTRxt('Is not using chrome!');
  }
}
?>
</span><br>
<TEXTAREA style='width:98%;background:LemonChiffon;' 
onclick='checkChatStatus();' onkeypress='return doClick(event);' ID='typeBox' NAME="fmsg" WRAP=VIRTUAL ROWS=3></TEXTAREA><br>
<input onclick='sendMsg(); return false; 'style="margin-top: 3px; border-radius: 0.5em 0.5em 0.5em 0.5em;" value=" <?php sayTxt('Say It');?>" type="submit">
<?php 
if (!$inSandBox) {
  ?>
  <input onclick='openPShare(); 'style="margin-top: 3px; border-radius: 0.5em 0.5em 0.5em 0.5em;" value=" <?php sayTxt('Share Photo');?>" type="button">
  <?php 
}
?>
</form>
</div>
</td>
<td style='background:LightSeaGreen;color:white;padding:8px 10px 5px 5px;'>
<div align='right' style='white-space:nowrap; margin:0px;padding:0px;'>

<?php getLogo();?> <?php sayTxt('Private Chat');?>

<div ID='myAlerts'></div>
</div
</td>
</tr>
</table>

<table style='border:0px solid #777777;margin:0px; width:100%'><tr valign='top'><td style='padding:15px 10px 10px 5px;'>
<div style='width:calc(100% - 16px);margin:0px 0px 5px 0px;padding:8px;padding-left:8px;background:#333333;border-radius: .5em;'>
<div align='right'>
<?php
if (!$userDeleted){
  ?>
  <form id="frmPVChat" method="get">
  <?php sayTxt('Set Chat');?>:
  <input style='background: #ffffff;width:13px;height:13px;border:0px;' type='radio' name='fpvc' value='on'      <?php echo $pvON;?>  
  onclick='javascript:xmUpdatePVChat();'> <?php sayTxt('On');?>
  <input style='background: #ffffff;width:13px;height:13px;border:0px;' type='radio' name='fpvc' value='off'     <?php echo $pvOF;?>  
  onclick='javascript:xmUpdatePVChat();'> <?php sayTxt('Off');?>
  <input style='background: #ffffff;width:13px;height:13px;border:0px;' type='radio' name='fpvc' value='friends' <?php echo $pvFR;?>  
  onclick='javascript:xmUpdatePVChat();'> <?php sayTxt('Friends only');?>
  | 
  <?php 
  if ($userID==17621 || $userID==50491){
    ?>
    IP: <a href='//whzon.com/whozAdmin/inspectProf.asp?fmbrID=<?php echo $mbrID;?>'><?php echo $dIP;?></a>
    <a href=javascript:testVChat();>Dbug Off</a> | <a href='javascript:setzbug(1);'>Dbug On</a>
    <?php 
  }
  if (!$IAmBLOCKED){
    ?>
    <a href="javascript:wzAPI_showFrame('blockUserFrm.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $mbrID;?>',350,250,200,200,'wzAppsContainer');">
    <?php sayTxt('Block This Mbr');?></a>
    <?php
  }
  else {
    ?>
    <a href="javascript:wzAPI_showFrame('/whzon/talk/blockUndoFrm.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $mbrID;?>',300,180,200,200,'wzAppsContainer');">
    <?php 
    sayTxt('Unblock This Mbr');
    ?>
    </a>
    <?php 
  } 
  ?>
  | <a href='pvChatEnd.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>&fcpg=pv'><?php sayTxt('End Chat');?></a>
  </form>
  </div>
  
  <table style='width:100%'><tr valign='top'><td style='width:76px;'>
  <a href='javascript:popProfile()'>
  <img style='border-radius:50%;margin-top:0px;margin-right:5px;float:left; height:85px;width:70px;' 
  src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrImg.php?id=<?php echo $mbrID;?>'><a></a>
  </td><td style='padding:0x;'>
  <?php 
  if ($franchise){
     echo '<a title="'.getTRxt('Certified monkytalk city franchise owner... Click for more info.').'" ';
     echo 'href="javascript:parent.wzGetPage(\'/whzon/franMgr/franMgr.php?wzID='.$sKey.'\')">';
     echo '<img src="'.$kingQueenImg.'" style="float:right;width:90px;height:63px;border-radius:50%;">'; 
  }
  ?> 
  <a href='javascript:popProfile()'>
  <?php 
  echo $mbrName;?></a><b > <?php echo $sex;?> <?php echo $age;?> From <?php echo $hProv;?>,<?php echo $hCountry;?></b><br>
  <?php 
  if ($franchise){
    echo "<b style:font-size:smaller;>".$whzdom." Franchise Owner</b>";
  }
  $style = "style='color:brown;'";
  if ($mbrLastAction){
    $style='';
  }
  echo "<span ID='wzLastActive' style='font-size:smaller;'><br>Last Active: <b ".$style.">".strLastAction($mbrLastAction)."</b></span>";
  ?>
  <div ID='showMoshPit'></div>
  <p>
  <?php echo $bio;?>
  </p>
  </td></tr></table>
  </div>
  <iFRAME  width=100% height='250' ID="chatFrame"  SCROLLING="vertical" FRAMEBORDER="NO" BORDER="0"></iFrame>

  <?php
}
getSmallBannerAd('5px;');?>
  <div style='margin-top:10px;' ID='wzAdSpace'>
  <div ID="wzMainAd" style="width:100%"></div>
  </div>
	   
  </td>
  <td style='border:0px;width:160px;padding:5px;'>
  <div class='pvcRSPanel' style='margin-top:10px;padding:8px;padding-left:8px;'>
<!--
  <b><?php sayTxt('Group Chats');?></b><a href='groupAddGroupFrm.php?wzID=<?php echo $sKey;?>'> [+]</a>
-->
  <div ID='myGroups'></div>
  <p/><b><?php sayTxt('New Messages');?>:</b>
  <div ID='wzContacts'></div>
  <b>All Conversations:</b>
  <div align='right'><a href='javascript:removeAll();'>End All</a></div>
  <div ID='wzAllContacts'></div>
  </div>
  </td></tr>
  <tr colspan='2'>
  <td>
  <?php 
  $noClose = true;
  include_once("../mblp/mblFooter.php");
  ?>
  </td>
</tr>
</table>

<div ID='wzAppsContainer' style='display:none;border:8px solid #777777;border-radius: 0.5em;position:absolute;background:#ffffff;'></div>
<script src='/whzon/mblp/mblToolboxJS.php'></script>

<script>
  var cptr="";
 
  var ifrm=document.getElementById("chatFrame");
  var typeBox=document.getElementById("typeBox");
  typeBox.focus();

  ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument;

  var myxml          = getUrlCom();
  var readxml        = getUrlCom();
  var vChatxml       = getUrlCom();
  var contactsxml    = getUrlCom();
  var contactsAllxml = getUrlCom();
  var sendmgsxml     = getUrlCom();
  var moshxml        = getUrlCom();
  var blockxml       = getUrlCom();
  var alertxml       = getUrlCom();
  var adspotxml      = getUrlCom();
  var pgTrackConn    = getUrlCom();
  var wzPVxml        = getUrlCom();
  var groupsxml      = getUrlCom();
  var lastactxml     = getUrlCom();
  
  var isFocused=1;
  var isIExplorer=1;
  var blockList=new Object();

  ifrm.document.open();
  ifrm.document.write(getUrl('pvChatframe.htm'));
  ifrm.document.close();

  zbug("Begin!");

  ReadContacts();
  ReadOldMsg();
  ReadMosh();
  ReadMyAlerts();
  rotateAdSpot();
  trackPage();
  ReadMyGroups();
  setInterval('checkLastAction()',14*1000);
  
  function wzMblReDrawPage(){
    var mblFooter = document.getElementById("mblFooter");
    if (mblFooter){
	  var pgViewFrame = parent.document.getElementById('pvchatWin');
	  var fheight = getOffset( mblFooter ).top;
	  parent.pgViewer.style.height = fheight + 48 + 'px';
	  pgViewFrame.style.height = fheight + 318 + 'px';
	  pgViewFrame.style.border = '0px solid #efefef';
	  pgViewFrame.style.borderTopWidth = '0px';
	  pgViewFrame.style.borderBottomLeftRadius = '0.5em';
	  pgViewFrame.style.borderBottomRightRadius = '0.5em';
	  
    }
  }
  function getYOffset(id){
    var el = document.getElementById(id);
	return getOffset(el).top;
  }
  function getYoffsetBottom(id){
    var el = document.getElementById(id);
    return getOffset(el).bottom;
  }
  function getOffset( el ) {
    var _x = 0;
    var _y = 0;
	var _b = 0;
    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) && !isNaN(el.offsetHeight) ) {
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop;
		_b += el.offsetHeight;
        el = el.offsetParent;
    }
    return { top: _y, left: _x, bottom: _b };
  }

function ReadMyGroups(){
   return;
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='groupChatList.php?wzID=<?php echo $sKey;?>&xm=' + ranTime ;
   groupsxml.open("GET", url,true);
   groupsxml.onreadystatechange = writeMyGroups;
   groupsxml.send(null);
}

var prevMyGrps = null;
var myGrpsTimer = null;
function excWriteMyGroups(j){
   var wzoutput = document.getElementById('myGroups');
   restxt = JSON.stringify(j.grpList.myGroups);
   if (prevMyGrps == restxt){
     return;
   }
   prevMyGrps = restxt;
   var myGroups = j.grpList.myGroups;
   for (var i in myGroups) {
     gdiv = ifrm.document.createElement('DIV');
     var htm = "<a href='groupChat.php?wzID=<?php echo $sKey;?>&fgroupID=" + myGroups[i].groupID + "'>";
     htm  = htm + "<img style='float:left;width:18px;height:24px;border-radius: .25em;border-radius:50%;margin-right:2px;margin-bottom:2px;' ";
     htm  = htm + "src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrTnyImg.php?id=" + myGroups[i].ownerID + "'/> " + Left(myGroups[i].gname,10) + "</a>";
     if (myGroups[i].ownerID == <?php echo $userID?>){
       htm = htm + " <a href='groupSelMbrAutoT.php?wzID=<?php echo $sKey;?>&fgroupID=" + myGroups[i].groupID + "'>[+]</a>";
     }
     htm = htm + " new - " + myGroups[i].gntoRead;
     htm = htm + "<br clear='left'/>";
     gdiv.innerHTML = htm;
     if (wzoutput.childNodes.length==0)
       wzoutput.appendChild(gdiv);
     else
       wzoutput.insertBefore(gdiv,wzoutput.firstChild);
   }
}
function writeMyGroups(){
 
  if (groupsxml.readyState == 4){
    if (myGrpsTimer) {clearTimeout(myGrpsTimer);}
    myGrpsTimer = setTimeout("ReadMyGroups()", 30*1000);
    if (groupsxml.status == 200){
	  
      var wzoutput = document.getElementById('myGroups');
      if (prevMyGrps == groupsxml.responseText){
        return;
      }
      prevMyGrps = groupsxml.responseText;
      var j = JSON.parse(groupsxml.responseText);
      var myGroups = j.myGroups;
      for (var i in myGroups) {
        gdiv = ifrm.document.createElement('DIV');
	var htm = "<a href='groupChat.php?wzID=<?php echo $sKey;?>&fgroupID=" + myGroups[i].groupID + "'>";
	htm  = htm + "<img style='float:left;width:18px;height:24px;border-radius: .25em;border-radius:50%;margin-right:2px;margin-bottom:2px;' ";
        htm  = htm + "src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrTnyImg.php?id=" + myGroups[i].ownerID + "'/> " + Left(myGroups[i].gname,10) + "</a>";
        if (myGroups[i].ownerID == <?php echo $userID?>){
	  htm = htm + " <a href='groupSelMbrAutoT.php?wzID=<?php echo $sKey;?>&fgroupID=" + myGroups[i].groupID + "'>[+]</a>";
	}
	htm = htm + " new - " + myGroups[i].gntoRead;
	htm = htm + "<br clear='left'/>";
        gdiv.innerHTML = htm;
        if (wzoutput.childNodes.length==0)
          wzoutput.appendChild(gdiv);
        else
          wzoutput.insertBefore(gdiv,wzoutput.firstChild); 
      }
    }
  }
}

function trackPage(){
  var currentTime = new Date();
  var ranTime = currentTime.getMilliseconds();
  var BID = "&BID=" + hash(navigator.appName + navigator.appVersion + navigator.cpuClass + navigator.platform + navigator.userAgent + screen.width );
  var url = "/whzon/track/trackLT.php?ID=17621&wsID=5&pgID=580428&htRefer=none";
  url = url + BID + '&xm=' + ranTime;
  pgTrackConn.open("GET", url,true);
  pgTrackConn.onreadystatechange = doNothingMsg;
  pgTrackConn.send(null);
}
function hash(str){
    var num=0;
    n=1;
    hstr="";

    for (var i=0;i<str.length;i++){
       num=num + str.charCodeAt(i);
       if (n>str.length/4){
         hstr=hstr + num;
         n=1;
         }
       else
         n=n+1;
    }
    return (num + "H" + hstr);
}
function checkLastAction(){
   return;
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='/whzon/pvchat/checkLastAction.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>&xm=' + ranTime ;
   lastactxml.open("GET", url,true);
   lastactxml.onreadystatechange = doWriteLastAction;
   lastactxml.send(null);
}
function excWriteLastAction(j){
   var jobj = j.mbrstat;
   var wzoutput = document.getElementById("wzLastActive");
   wzoutput.innerHTML=jobj.html;
   wzoutput = document.getElementById("vChatAlert");
   wzoutput.innerHTML = jobj.status;
}
function doWriteLastAction(){
    if (lastactxml.readyState == 4){
      if(lastactxml.status  == 200){ 
        var jobj = JSON.parse(mkyTrim(lastactxml.responseText));
        var wzoutput = document.getElementById("wzLastActive");
        wzoutput.innerHTML=jobj.html;
	    wzoutput = document.getElementById("vChatAlert");
		wzoutput.innerHTML = jobj.status;
      }
    }
}

function rotateAdSpot(){
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='/whzon/adMgr/srvGPVAd.php?wzID=<?php echo $sKey;?>&fh=90&fw=&fqry=&xm=' + ranTime ;
   adspotxml.open("GET", url,true);
   adspotxml.onreadystatechange = doRotateAdSpot;
   adspotxml.send(null);

}

function doRotateAdSpot(){
 
    if (adspotxml.readyState == 4){
      if(adspotxml.status  == 200){ 
        var rhtml=mkyTrim(adspotxml.responseText);
        var wzoutput = document.getElementById("wzMainAd");
        wzoutput.innerHTML="";
        wzoutput.innerHTML=rhtml;
      }
    }

}

function onBlur() {
  isFocused=0;
}

function onFocus(){
  isFocused=1;
}

if (/*@cc_on!@*/false) { // check for Internet Explorer
  document.onfocusin = onFocus;
  document.onfocusout = onBlur;
  isIExplorer=1;
  }
else {
  window.onfocus = onFocus;
  window.onblur = onBlur;
  isIExplorer=0;
}

function popProfileID(id){
  var URL = '/whzon/mbr/mbrProfile.php?wzID=' + parent.sID + '&fwzUserID=' + id;
  parent.wzGetPage(URL);
}
function wzGetPage(url){
  parent.wzGetPage(url);
}
function popProfile(){
  var URL = '/whzon/mbr/mbrProfile.php?wzID=' + parent.sID + '&fwzUserID=<?php echo $mbrID;?>';
  parent.wzGetPage(URL);
}

function popMyProfile(){
  var URL = '/whzon/mbr/mbrProfile.php?wzID=' + parent.sID + '&fwzUserID=<?php echo $userID;?>';
  parent.wzGetPage(URL);
}



function sendVChatReq(){

//          var wzoutput = document.getElementById("vChatAlert");
//          wzoutput.innerHTML= "<img alt='Start Video Chat' title='Start Video Chat' style='border:0px;width:30px;height:21px;margin-bottom:2px;vertical-align:middle;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/vChatIconOn.png'>Video Started";
//          reqVChat();

}

function acceptVChatReq(){

//          var wzoutput = document.getElementById("vChatAlert");
//          wzoutput.innerHTML= "<img alt='Start Video Chat' title='Start Video Chat' style='border:0px;width:30px;height:21px;margin-bottom:2px;vertical-align:middle;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/vChatIconOn.png'>Video Started";

//        wzAcceptVChat(<?php echo $mbrID;?>);

}

function wzEndVChat(){
        vChatStarted=false;
        var currentTime = new Date();
        var ranTime = currentTime.getMilliseconds();
        var url='/whozon/vChat/declineVchat.php?fwzUserID=<?php echo $userID;?>&xm=' + ranTime;
        var donothing=getUrl(url);
        var wzoutput = document.getElementById("vChatAlert");
//        wzoutput.innerHTML= "<a href='javascript:sendVChatReq();'><img alt='Start Video Chat' title='Start Video Chat' style='border:0px;width:30px;height:21px;margin-bottom:2px;vertical-align:middle;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/vChatIcon.png'>Start Video Chat with</a> - <?php echo $mbrName;?>";


}

function doSendMsgToServer(url){
   sendmgsxml.open("GET", url,true);
   sendmgsxml.onreadystatechange = doNothingMsg;
   sendmgsxml.send(null);
   parent.doUserActionLog();
   rotateAdSpot();
   trackPage();
}

function doNothingMsg(){
}

function setzbug(setting){
  zbugs=setting;
}

function zbug(str){
    if (zbugs==1) {
      var currentTime = new Date();
      var ranTime = currentTime.getTime();
      var msg = -1*(dbgT - ranTime) + " : " + mkyTrim(str);
      dbgT=ranTime;

      ifrm.scrollTo(0,0);
      var wzoutput = ifrm.document.getElementById("chatDiv");

      gdiv = ifrm.document.createElement('DIV');
      gdiv.innerHTML="<table><tr valign='top'><td style='padding:0px;padding-top:4px;width:18px;'></td><td style='padding-top:0px;'>debug: " + msg + "</td></tr></table>";

      if (wzoutput.childNodes.length==0)
        wzoutput.appendChild(gdiv);
      else
        wzoutput.insertBefore(gdiv,wzoutput.firstChild);  

    }    
    return false;
}
function displayShare(txt){
      var msg  = decodeURIComponent(txt);
      if (msg!='') {
        //msg=doEmotes(msg);
        ifrm.scrollTo(0,0);
        var wzoutput = ifrm.document.getElementById("chatDiv");
        var wzAdspace = document.getElementById("wzAdSpace");

        gdiv = ifrm.document.createElement('DIV');
        gdiv.innerHTML = formatDiv + "<table><tr valign='top'><td style='padding:0px;padding-top:4px;width:18px;'><a href='javascript:parent.popMyProfile();'><img style='margin-bottom:5px;margin-right:8px;height:24px;width:18px;border-radius:50%;float:left;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrTnyImg.php?id=<?php echo $userID;?>'></a></td><td style='padding-top:0px;'><a href='javascript:parent.popMyProfile();'><?php echo $userName;?> </a> Says:<br>" + msg + "</td></tr></table></div>";

		if (wzoutput.childNodes.length==0)
		  wzoutput.appendChild(gdiv);
		else
		  wzoutput.insertBefore(gdiv,wzoutput.firstChild);

		var adHTML=wzAdspace.innerHTML;
		wzAdspace.innerHTML="";
		wzAdspace.innerHTML=adHTML;
	  }
	  return false;
}

var sMsgID = 1;
function sendMsg(){
     var msg  = document.getElementById("chTxtBox").elements["fmsg"].value;
     msg = mkyTrim(msg);
     if (msg!='') {
 	   var currentTime = new Date();
	   var ranTime = currentTime.getMilliseconds();
	   var url='pvChatSayMsg.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>&fmsg=' + escape(msg) + "&xm=" + ranTime;
       doSendMsgToServer(url);
       msg=doEmotes(msg);
       ifrm.scrollTo(0,0);
       var wzoutput = ifrm.document.getElementById("chatDiv");
       var wzAdspace = document.getElementById("wzAdSpace");

       gdiv = ifrm.document.createElement('DIV');
       var h =formatDiv + "<table><tr valign='top'><td style='padding:0px;padding-top:4px;width:18px;'><a href='javascript:parent.popMyProfile();'>";
       h = h + "<img style='height:24px;width:18px;margin-bottom:5px;margin-right:8px;border-radius:50%;float:left;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrTnyImg.php?id=<?php echo $userID;?>'></a>";
       h = h + "</td><td style='padding-top:0px;'><a href='javascript:parent.popMyProfile();'><?php echo $userName;?> </a> Says:<br>" + msg + "</td></tr></table>";
       h = h + "<div ID='linkSpotS" + sMsgID + "' style='display:none'></div></div>";
       gdiv.innerHTML = h;

       if (wzoutput.childNodes.length==0)
         wzoutput.appendChild(gdiv);
       else
         wzoutput.insertBefore(gdiv,wzoutput.firstChild);  
    
       console.log('pvcURL:' + msg,sMsgID);
       scanInput(msg,'S' + sMsgID);
       sMsgID = sMsgID + 1;

       var adHTML=wzAdspace.innerHTML;
       wzAdspace.innerHTML="";
       wzAdspace.innerHTML=adHTML;
     }
     document.getElementById("chTxtBox").elements["fmsg"].value = '';

     return false;
}

function endChat(){

      var currentTime = new Date();
      var ranTime = currentTime.getMilliseconds();

      var url='pvChatEnd.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>&xm=' + ranTime ;
	  alert(url);
      var xmresult=mkyTrim(getUrl(url));
}


function BlockUser(ID){
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='blockUserAJ.asp?fwzUserID=<?php echo $userID;?>&fanID=' + ID +'&xm=' + ranTime ;
   var msg=getUrl(url);
   if ( msg="done") {
     blockList[ID]=1;
     window.close();
   }
}

function ReadContacts(){
   return;   
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='pvChatGetContacts.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>&xm=' + ranTime ;
   contactsxml.open("GET", url,true);
   contactsxml.onreadystatechange = writeContacts;
   contactsxml.send(null);

}

function ReadAllContacts(){
   return;
   zbug("ReadAllContacts"); 
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='pvChatGetAllContacts.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>&xm=' + ranTime ;
   contactsAllxml.open("GET", url,true);
   contactsAllxml.onreadystatechange = writeAllContacts;
   contactsAllxml.send(null);

}

var prevAConMsgs = null;
function excWriteAllContacts(j){
   var jdata = JSON.stringify(j.allContacts);
   if (prevAConMsgs == jdata){
     return;
   }
   prevAConMsgs = jdata;
   var msgs = j.allContacts.myMsgs;
   var wzoutput = document.getElementById("wzAllContacts");
   wzoutput.innerHTML = "";
   for (var i in msgs) {
     var msgID = msgs[i].sentBy;
     var msg   = msgs[i].htm;

     divflg=document.getElementById('cta_'+msgID);

     if (divflg==null) {
       gdiv = ifrm.document.createElement('DIV');
       gdiv.id='cta_'+msgID;
       gdiv.innerHTML=msg;
       if (wzoutput.childNodes.length==0)
         wzoutput.appendChild(gdiv);
       else
         wzoutput.insertBefore(gdiv,wzoutput.firstChild);
     }
   }
}
function writeAllContacts(){
 
  if (contactsAllxml.readyState == 4){
    if(contactsAllxml.status  == 200){ 
      var jdata = mkyTrim(contactsAllxml.responseText);
      if (jdata == '') {jdata = '{"myMsgs":[]}';}
      if (prevAConMsgs == jdata){
        return;
      }
      prevAConMsgs = jdata;     
      var j = JSON.parse(jdata);
      var msgs = j.myMsgs;
      var wzoutput = document.getElementById("wzAllContacts");
      wzoutput.innerHTML = "";
      for (var i in msgs) {
        var msgID = msgs[i].sentBy;
        var msg   = msgs[i].htm;

        divflg=document.getElementById('cta_'+msgID);

        if (divflg==null) {
          gdiv = ifrm.document.createElement('DIV');
          gdiv.id='cta_'+msgID;
          gdiv.innerHTML=msg;
          if (wzoutput.childNodes.length==0)
            wzoutput.appendChild(gdiv);
          else
            wzoutput.insertBefore(gdiv,wzoutput.firstChild); 
        }
      }
    }
  }
}
function excWriteContacts(j){
   var msgs = j.cMsgs.myMsgs;
   var wzoutput = document.getElementById("wzContacts");
   wzoutput.innerHTML = "";
   for (var i in msgs) {
     var msgID = msgs[i].sentBy;
     var msg   = msgs[i].htm;
     var pvMsgs = 0;

     divflg=document.getElementById('ct_'+msgID);

     if (divflg==null) {
       gdiv = ifrm.document.createElement('DIV');
       gdiv.id='ct_'+msgID;
       gdiv.innerHTML=msg;
       if (wzoutput.childNodes.length==0)
         wzoutput.appendChild(gdiv);
       else {
         wzoutput.insertBefore(gdiv,wzoutput.firstChild);
         pvMsgs = pvMsgs + 1;
       }
     }
     if (!parent.pvcInFocus && pvMsgs > 0){
       parent.pvchatControl.innerHTML = "<span class='mpgTab' style='padding:1px;padding-top:0px;padding-left:3px;padding-right:3px;border-radius: .5em;'><b>Private Chat:</b><a href='javascript:wzAPI_hidePVC();'>[-]</a><a style='font-weight:bold;color:orange;' href='javascript:wzAPI_focusPVC();'>[+] new! " + pvMsgs + "</a></span>";
       var snd = new Audio("/sounds/wzNotify.mp3");
       if (!snd.isPlaying){
         snd.play();
       }
     }
   }
}
function writeContacts(){
 
  if (contactsxml.readyState == 4){
    setTimeout("ReadContacts()",60*1000);

    if (contactsxml.status == 200){
      var jdata = mkyTrim(contactsxml.responseText);
      if (jdata == '') {jdata = '{"myMsgs":[]}';}
      var j = JSON.parse(jdata);
      var msgs = j.myMsgs;
      var wzoutput = document.getElementById("wzContacts");
      wzoutput.innerHTML = "";
      for (var i in msgs) {
        var msgID = msgs[i].sentBy;
        var msg   = msgs[i].htm;
        var pvMsgs = 0;

        divflg=document.getElementById('ct_'+msgID);

        if (divflg==null) {
          gdiv = ifrm.document.createElement('DIV');
          gdiv.id='ct_'+msgID;
          gdiv.innerHTML=msg;
          if (wzoutput.childNodes.length==0)
            wzoutput.appendChild(gdiv);
          else
            wzoutput.insertBefore(gdiv,wzoutput.firstChild); 
          pvMsgs = pvMsgs + 1;
        }
      }
      if (!parent.pvcInFocus && pvMsgs > 0){
        parent.pvchatControl.innerHTML = "<span class='mpgTab' style='padding:1px;padding-top:0px;padding-left:3px;padding-right:3px;border-radius: .5em;'><b>Private Chat:</b><a href='javascript:wzAPI_hidePVC();'>[-]</a><a style='font-weight:bold;color:orange;' href='javascript:wzAPI_focusPVC();'>[+] new! " + pvMsgs + "</a></span>";
        var snd = new Audio("/sounds/wzNotify.mp3");
        if (!snd.isPlaying){
          snd.play();
        }
      } 
    }
    else {
      var wzoutput = document.getElementById("wzContacts");
      wzoutput.innerHTML=" - None";
    }
    ReadAllContacts();
  }
}

function ReadMyAlerts(){
   return;
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='pvChatUserAlerts.php?wzID=<?php echo $sKey;?>&xm=' + ranTime ;
   alertxml.open("GET", url,true);
   alertxml.onreadystatechange = writeMyAlerts;
   alertxml.send(null);

}
function excWriteMyAlerts(j){
   var msgs=mkyTrim(alertxml.responseText);
   var wzoutput = document.getElementById("myAlerts");
   wzoutput.innerHTML= j.uAlerts;
}
function writeMyAlerts(){
 
  if (alertxml.readyState == 4){
    setTimeout("ReadMyAlerts()", 30*1000);
    if (alertxml.status == 200){
      var msgs=mkyTrim(alertxml.responseText);

      var wzoutput = document.getElementById("myAlerts");
      wzoutput.innerHTML= msgs;
    }
  }
}



function ReadMosh(){
   return;
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='pvChatGetMoshBox.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>&xm=' + ranTime ;
   moshxml.open("GET", url,true);
   moshxml.onreadystatechange = writeMosh;
   moshxml.send(null);

}
function excWriteMosh(j){
   var wzoutput = document.getElementById("showMoshPit");
   wzoutput.innerHTML = j.mosh;
}
function writeMosh(){
 
  if (moshxml.readyState == 4){
    setTimeout("ReadMosh()", 30*1000);
    if (moshxml.status == 200){
      var msgs=mkyTrim(moshxml.responseText);

      var wzoutput = document.getElementById("showMoshPit");
      wzoutput.innerHTML= msgs;
    }
  }
}


function ReadVCalls(){
//   var currentTime = new Date();
//   var ranTime = currentTime.getMilliseconds();
//   var url='vChatCheckCalls.php?wzID=<?php echo $sKey;?>&fcallID=<?php echo $mbrID;?>&xm=' + ranTime ;
//   vChatxml.open("GET", url,true);
//   vChatxml.onreadystatechange = alertVChat;
//   vChatxml.send(null);

}

function alertVChat(){
 
//  if (vChatxml.readyState == 4){
//    setTimeout("ReadVCalls()",refresh*1000);
//    if (vChatxml.status == 200){
//      var msgs=mkyTrim(vChatxml.responseText);

//      var wzoutput = document.getElementById("vChatAlert");
//      if ( msgs!="NC") {
//        wzoutput.innerHTML= "<img style='width:18px;height:24px;margin-left:8px;vertical-align:bottom;border:0px solid #777777;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrTnyImg.php?id=" + msgs + "'> <b style='color:brown;'> Video Call</b> <a href='javascript:acceptVChatReq()'>Accept</a> | <a href='javascript: wzEndVChat();'>Decline</a>";
//        if (isFocused==0) {
//          //window.focus();
//          var snd = new Audio("/sounds/wzNotify.mp3");
//          snd.play();
//         } 
//         //typeBox.focus();
//      }
//    
//  }
}

function ReadOldMsg(){
   return;
   zbug("ReadOldMesg()");
   var vmode = '';
   if (parent.winWRTCChat){
     vmode = '&fWRTC=on';
   } 
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='pvChatGetOldMsg.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>' + vmode + '&xm=' + ranTime ;
   readxml.open("GET", url,true);
   readxml.onreadystatechange = writeOldMsg;
   readxml.send(null);
}

function writeOldMsg(){
 
  if (readxml.readyState == 4){
    if (readxml.status == 200){
      var jdata = mkyTrim(readxml.responseText);
      if (jdata == '') {jdata = '{"myMsgs":[]}';}
      var j = JSON.parse(jdata);
      var msgs = j.myMsgs;
      var wzoutput = ifrm.document.getElementById("chatDiv");
      wzoutput.innerHTML = "";

      zbug("writeOldMesg()");

      var myID='<?php echo $userID;?>';
      var themID='<?php echo $mbrID;?>';

      for (let i = msgs.length - 1; i >=0 ; i--){
        var msgID   = msgs[i].msgID;
        var msg   = null;
        try {msg = decodeURIComponent(msgs[i].htm);}
        catch(err) { msg = msgs[i].htm;}
        var guserID = msgs[i].guserID;
        var gname   = msgs[i].gname;
        msg = msg.replace(/em:/g,'em;');
        msg = doEmotes(unescape(msg));
        zbug("writeOldMesg()");

        if (guserID==myID){
          gname='<?php echo $userName;?>';
          }
        else{
          gname='<?php echo $mbrName;?>';
        }
            
        
        divflg=ifrm.document.getElementById(msgID);

        if (divflg==null) {
          gdiv = ifrm.document.createElement('DIV');
          gdiv.id=msgID;
          gdiv.innerHTML = formatDiv + "<table><tr valign='top'><td style='padding:0px;padding-top:4px;width:18px;'><a href='javascript:parent.popProfileID(" + guserID + ");'><img style='height:24px;width:18px;margin-bottom:5px;margin-right:8px;border-radius:50%;float:left;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrTnyImg.php?id=" + guserID + "'></a></td><td style='padding-top:0px;'><a href='javascript:parent.popProfileID(" + guserID + ");'>" + gname + "</a><br>" + msg + "</td></tr></table></div>";

          if (wzoutput.childNodes.length==0)
            wzoutput.appendChild(gdiv);
          else
            wzoutput.insertBefore(gdiv,wzoutput.firstChild); 


          if (isFocused==0) {
            //window.focus();
            var snd = new Audio("/sounds/wzNotify.mp3");
            snd.play();
          } 
          //typeBox.focus();
        }
      }
    }
    ReadMsg();
  }
}


function hutRefer(wsID,hutID){
  var wzID = '<?php echo $sKey;?>';
  var url = 'https://www.bitmonky.com/whzon/adMgr/clickHutRefer.php?wzID='+wzID+'&wsID='+wsID+'&hutID='+hutID;
  window.open(url,'bitHutRef');
}
function ReadMsg(){
   return;
   zbug("ReadMsg()");
   var vmode = '';
   if (parent.winWRTCChat){
     vmode = '&fWRTC=on';
   } 
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='pvChatGetMsg.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $mbrID;?>' + vmode + '&xm=' + ranTime ;
   readxml.open("GET", url,true);
   readxml.onreadystatechange = writeMsg;
   readxml.send(null);

}
function excWriteOldMsg(j){
   var msgs = j.newMsg.myMsgs;
   var wzoutput = ifrm.document.getElementById("chatDiv");
   wzoutput.innerHTML = "";

   var myID='<?php echo $userID;?>';
   var themID='<?php echo $mbrID;?>';

   for (let i = msgs.length - 1; i >=0 ; i--){
     var msgID   = msgs[i].msgID;
     var msg   = null;
     try {msg = decodeURIComponent(msgs[i].htm);}
     catch(err) { msg = msgs[i].htm;}
     var guserID = msgs[i].guserID;
     var gname   = msgs[i].gname;
     msg = msg.replace(/em:/g,'em;');
     msg = doEmotes(unescape(msg));

     if (guserID==myID){
       gname='<?php echo $userName;?>';
     }
     else{
       gname='<?php echo $mbrName;?>';
     }

     divflg=ifrm.document.getElementById(msgID);
     if (divflg==null) {
       gdiv = ifrm.document.createElement('DIV');
       gdiv.id=msgID;
       msg = msg.replace(/@@msgID@@/g,msgID.toString());
       var h = formatDiv + "<table><tr valign='top'><td style='padding:0px;padding-top:4px;width:18px;'><a href='javascript:parent.popProfileID(" + guserID + ");'>";
       h = h + "<img style='height:24px;width:18px;margin-bottom:5px;margin-right:8px;border-radius:50%;float:left;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrTnyImg.php?id=" + guserID + "'></a>";
       h = h + "</td><td style='padding-top:0px;'><a href='javascript:parent.popProfileID(" + guserID + ");'>" + gname + "</a><br>" + msg + "</td></tr></table>";
       h = h + "<div ID='linkSpot" + msgID + "' style='display:none'></div></div>";
       gdiv.innerHTML = h; 

       if (wzoutput.childNodes.length==0)
         wzoutput.appendChild(gdiv);
       else
         wzoutput.insertBefore(gdiv,wzoutput.firstChild);

       console.log('pvcURL:' + msg,msgID);
       scanInput(msg,msgID);

       if (i==0) {
         //var snd = new Audio("/sounds/wzNotify.mp3");
         //snd.play();
       }
     }
   }
}
function excWriteMsg(j){
   if (pvcPMode  == 'all'){
     excWriteOldMsg(j);
     pvcPMode = 'new';
     doOnePoll();
     return;
   }
   var msgs = j.newMsg.myMsgs;
   var wzoutput = ifrm.document.getElementById("chatDiv");

   for (var i in msgs) {
     var msgID   = msgs[i].msgID;
     var msg   = null;
     try {msg = decodeURIComponent(msgs[i].htm);}
     catch(err) { msg = msgs[i].htm;}
     msg = msg.replace(/em:/g,'em;');

     msg=doEmotes(unescape(msg));
     divflg=ifrm.document.getElementById(msgID);

     if (divflg==null) {
       gdiv = ifrm.document.createElement('DIV');
       gdiv.id = msgID;
       msg = msg.replace(/@@msgID@@/g,msgID.toString());
       var h = formatDiv + "<table><tr valign='top'><td style='padding:0px;padding-top:4px;width:18px;'><a href='javascript:parent.popProfile();'>";
       h = h + "<img style='height:24px;width:18px;margin-bottom:5px;margin-right:8px;border-radius:50%;float:left;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrTnyImg.php?id=<?php echo $mbrID;?>'></a>";
       h = h + "</td><td style='padding-top:0px;'><a href='javascript:parent.popMyProfile();'><?php echo$mbrName;?> </a><br>" + msg + "</td>";
       h = h + "</tr></table><div ID='linkSpot" + msgID + "' style=''></div></div>";
       gdiv.innerHTML = h;
       if (wzoutput.childNodes.length==0)
         wzoutput.appendChild(gdiv);
       else
         wzoutput.insertBefore(gdiv,wzoutput.firstChild);
     }
     console.log(msg,msgID);
     scanInput(msg,msgID);
     if (i == 0){
       console.log('pvc excWriteMsg',i);
       var snd = new Audio("/sounds/wzNotify.mp3");
       snd.play();
     }
   }
}

function writeMsg(){
 
  if (readxml.readyState == 4){
    setTimeout("ReadMsg()",refresh*1000);
    if (readxml.status == 200){
      var jdata = mkyTrim(readxml.responseText);
      if (jdata == '') {jdata = '{"myMsgs":[]}';}
      var j = JSON.parse(jdata);
      var msgs = j.myMsgs;
      var wzoutput = ifrm.document.getElementById("chatDiv");
      zbug("WriteMsg()");

      for (var i in msgs) {
        var msgID   = msgs[i].msgID;
        var msg   = null;
        try {msg = decodeURIComponent(msgs[i].htm);}
        catch(err) { msg = msgs[i].htm;}
        msg = msg.replace(/em:/g,'em;');
        isThere=true;
        msg=doEmotes(unescape(msg));


        divflg=ifrm.document.getElementById(msgID);

        if (divflg==null) {
          gdiv = ifrm.document.createElement('DIV');
          gdiv.id=msgID;
          gdiv.innerHTML = formatDiv + "<table><tr valign='top'><td style='padding:0px;padding-top:4px;width:18px;'><a href='javascript:parent.popProfile();'><img style='height:24px;width:18px;margin-bottom:5px;margin-right:8px;border:0px solid #777777;float:left;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrTnyImg.php?id=<?php echo $mbrID;?>'></a></td><td style='padding-top:0px;'><a href='javascript:parent.popMyProfile();'><?php echo$mbrName;?> </a><br>" + msg + "</td></tr></table></div>";

          if (wzoutput.childNodes.length==0)
            wzoutput.appendChild(gdiv);
          else
            wzoutput.insertBefore(gdiv,wzoutput.firstChild); 

          var wzVDIV = document.getElementById("vChatAlert");
          if (isThere==true) {
            // if(! vChatStarted) {
            //   wzVDIV.innerHTML= "<a href='javascript:sendVChatReq();'><img alt='Start Video Chat' title='Start Video Chat' style='border:0px;width:30px;height:21px;margin-bottom:2px;vertical-align:middle;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/vChatIcon.png'>Start Video Chat with - <?php echo $mbrName;?></a>";
            // }  
          }

          //if (isFocused==0) {
            //window.focus();
            var snd = new Audio("/sounds/wzNotify.mp3");
            snd.play();
          //} 
          //typeBox.focus();
        }
      }
    }
  }	
}

function Left(str, n){
	if (n <= 0)
	    return "";
	else if (n > String(str).length)
	    return str;
	else
	    return String(str).substring(0,n);
}

function Right(str, n){
    if (n <= 0)
       return "";
    else if (n > String(str).length)
       return str;
    else {
       var iLen = String(str).length;
       return String(str).substring(iLen, iLen - n);
    }
}

function mkyTrim(stringToTrim) {
  return stringToTrim.replace(/^\s+|\s+$/g,"");
}
 

function getUrlCom() {
  var xmlhttp=null;
/*@cc_on @*/
/*@if (@_jscript_version >= 5)
// JScript gives us Conditional compilation, we can cope with old IE versions.
// and security blocked creation of the objects.
 try {
  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
 } catch (e) {
  try {
   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  } catch (E) {
   xmlhttp = false;
  }
 }
@end @*/
if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
	try {
		xmlhttp = new XMLHttpRequest();
	} catch (e) {
		xmlhttp=false;
  }
}
if (!xmlhttp && window.createRequest) {
	try {
		xmlhttp = window.createRequest();
	} catch (e) {
		xmlhttp=false;
	}
  }
return xmlhttp;
}


function getUrl(url){
 myxml.open("GET", url,false);
 myxml.send(null);
 return myxml.responseText;
}

function doEmotes(msg){
  return parent.doEmotes(msg);
  return msg;
}
</script>
</body>
</html>
<?php


function ipToCountryCD($ipStr){
  $fresult = "Not Found";
  return;
  $ends=strrpos($ipStr,".");
  $mults=256*256*256;
  $IPc=0;

  if (!is_null($ends) || $ends==0) {
    while ($ends > 0) { 
      $word=left($ipStr,$ends-1);
      $lens=strlen($ipStr);
      $ipStr=right($ipStr,$lens - $ends); 
      $IPc=$IPc + $word *$mults;
      $mults=$mults/256;
      $ends=strrpos($ipStr,".");
      if ($ends===False)
        $ends=0;
    }

    $IPc=$IPc+$ipStr;
  
    $SQL = "select name,countryCD2 from IpToCountry   where LOWERip < ".$IPc." and upperIP > ".$IPc;
    $ipresult = mkyMsqry($SQL);
    $ipRec = mkyMsFetch($ipresult);
 
    if ($ipRec) {
      $fresult=$ipRec['name'];
      $countryCD=$ipRec['countryCD2'];
    }

  }
  return $fresult;
}
?>

