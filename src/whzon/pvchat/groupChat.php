<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
$mobile=null;
$userIsMod=null;
$userIsAdmin=null;
include_once("../mkysess.php");

    


  $groupID = clean($_GET['fgroupID']);
  
  $groupName    = null;
  $groupOwnerID = null;
  
  $SQL = "select chatGroupName,groupOwnerID from ICDchat.tblChatGroup where chatGroupID=".$groupID;
  $myresult = mkyMyqry($SQL);
  $mRec = mkyMyFetch($myresult);
  
  if($mRec){ 
    $groupName    = $mRec['chatGroupName'];
    $groupOwnerID = $mRec['groupOwnerID'];
  }
  
  $SQL = "update tblwzOnline set inGroupPChatID =".$groupID." where wzUserID=".$userID;
  $result = mkyMsqry($SQL) or die($SQL);
    
  if ($mobile==True){
    
    header("mblChatApp.php?fmbrID=".$groupOwnerID);
    exit();
  }
?>
<!doctype html>
<html lang="en">
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

var refresh=4;
var isThere=false;
var userID=<?php echo $userID;?>;
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

function openPShareGroup(){
  wzAPI_showFrame('groupSharePhotoFrm.php?wzID=<?php echo $sKey;?>&fgroupID=<?php echo $groupID;?>',350,250,200,200);
}
function popReportImg(imgID){
  wzAPI_showFrame('popReportImg.php?fimgID=' + imgID + '&wzID=<?php echo $sKey;?>&fmbrID=<?php echo $groupOwnerID;?>',350,250,200,200);
}

function pshare(id,ck){
  wzAPI_showFrame('popShowPhoto.php?wzID=<?php echo $sKey;?>&id=' + id + '&ck=' + ck ,640,440,25,130);
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

function tAGetMissingInfo(){
   wzAPI_setRefreshPg(1);
   wzAPI_showFrame("/whzon/signup/fastJoin.php?mode=v&wzID=<?php echo $sKey;?>",400,450,50,100);
}
var VCname;
var VCuserID;
var VCmbrID;

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
</script>
</HEAD>
<?php
    $isMod=null;
    if ($userIsMod || $userIsAdmin) 
       $isMod=True;

    $IAmBLOCKED=False;

    $SQL = "SELECT privateChat from tblwzUser WHERE wzUserID=".$userID;
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

    $SQL = "SELECT count(*) as nRecs from tblwzUserBlockList WHERE wzUserID=".$userID." and blockUserID=".$groupOwnerID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    
    if ($tRec['nRecs']>0)
      $IAmBLOCKED=True;

    $SQL = "SELECT  franchise, privateChat, verified,cityID,banned,imgFlg, firstname, timezone,city,prov,country,paidMember,nfans,sex,age,IP,moderator, ";
    $SQL .= "profileText From tblwzUser where wzUserID=".$groupOwnerID;
      
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $missingInfo=False;
	$franchise = null;
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
       $IP=$tRec['IP'];
       $bio =left($tRec['profileText'],180)."...";
       $dIP=$IP;
	   $franchise = $tRec['franchise'];
	   
       if (!is_null($IP)) {
         $resultIPC=ipToCountryCD($IP);
         $IPLocation="IP Location - ".$resultIPC;
       }
       if (!is_null($sex)) {
         if ($sex==True)
           $sex = "f - ";
         else
           $sex = "m - ";
       }
   
    }

    $SQL = "SELECT  suspect, verified,cityID,banned,imgFlg,TIMESTAMPDIFF(day,date(creatDate),date(now())) as nDays From tblwzUser where wzUserID=".$userID;  
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $missingInfo=False;
    if ($tRec){
      if ($tRec['banned'])
         echo "<script>window.location='getlost.php';</script>";
       else{
         $vCityID=$tRec['cityID'];
         $verified=$tRec['verified'];
         $nDays=$tRec['nDays'];
         $suspect=$tRec['suspect'];

         if ($userID < 402959) $verified=1;
         if (is_null($verified)) $verified=0;
         if ($verified==0 && !is_null($suspect)) $missingInfo=True;
         if (is_null($nDays)) $nDays=99999;
         $verified=1;



         $vImgFlg=$tRec['imgFlg'];
         if ($vCityID==0 || $vImgFlg==0 || $verified==0)
           $missingInfo=True; 
      }
    }

    if ($groupOwnerID==17621) $missingInfo=False;

?>

<BODY class='pgBody' style='background:#2A2A2A;' <?php if ($missingInfo) {?> onload='tAGetMissingInfo();'<?php } ?> >
<?php if ($mobile) {?>

<table style='width:100%;'>
<tr valign='bottom'>
<td style='padding-left:15px;padding-bottom:5px;background:black;'>
<div style='white-space:nowrap; margin:0px;padding:0px;'>
<span style='FONT-FAMILY: Impact;font-size:38px;font-weight:normal;color:#8ec634;'> Bit<span style='color:#ffffff;'>Monky</span></span>
<span style='font-size:20px;font-weight:normal;color:#cb0051;'>Mobile </span>
</div>
<form style='margin:0px;padding:0px;margin-top:4px;' method='GET' action='/whozon/myTown.asp'>
<input type='hidden' name='fmyMode' value='qry'>
<input type='hidden' name='fwzUserID' value='<?php echo $hUserID;?>'>
<span style='color:#ffffff;white-space:nowrap;'>
Search<input style='margin-left:4px;vertical-align:middle;' size='15' name='fqry' value=''> 
<input style='vertical-align:middle;' type='submit' value='Go'></span> <a style='color:#8ec634;' href=javascript:wzGotoPage('//whzon.com');>Home</a>  
<?php if ($userID==0) {?>
  | <a style='color:#8ec634;' href='javascript:wzQuickReg();'>Join Here</a> 
<?php }else { ?>
  | <a style='color:#8ec634;' href=javascript:wzGotoPage('/wzUsers/trackerLT/viewMbrAccount.asp');>Go To My Account</a> 
<?php } ?>
</Form>
</td>
</tr>
</table>

<?php } else { ?>

<table style='width:100%;'>
  <tr valign='bottom'>
    <td colspan='2' style='padding-left:15px;padding-bottom:5px;background:black;'>
      <div style='white-space:nowrap; margin:0px;padding:0px;'>
        <font style='FONT-FAMILY: Impact;font-size:20px;font-weight:normal;color:#8ec634;'> Bit<font style='color:#ffffff;'>Monky</font><font style='color:#eb9b52;'></font></font>
        </font><font style='font-size:14px;font-weight:normal;color:#cb0051;'>Private Group Chat!
        </font>
		  <div ID='myAlerts'></div>
	  </div>
    </td>		
  </tr>
</table>

<?php }?>

<table style='border:0px solid #777777;margin:0px; width:100%'><tr valign='top'><td style='padding:15px;'>

<div class='infoCardClear' style='background:#222222;'>
<div align='right'>
<form id="frmPVChat" method="get">
Set Chat:
<input style='background: #ffffff;width:13px;height:13px;border:0px;' type='radio' name='fpvc' value='on'      <?php echo $pvON;?>  
onclick='javascript:xmUpdatePVChat();'> On
<input style='background: #ffffff;width:13px;height:13px;border:0px;' type='radio' name='fpvc' value='off'     <?php echo $pvOF;?>  
onclick='javascript:xmUpdatePVChat();'> Off
<input style='background: #ffffff;width:13px;height:13px;border:0px;' type='radio' name='fpvc' value='friends' <?php echo $pvFR;?>  
onclick='javascript:xmUpdatePVChat();'> Friends only

| <a href='pvChatEnd.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $groupOwnerID;?>&fcpg=pv'>End Chat</a>
<?php 
if ($userID != $groupOwnerID){
  ?>
  | <a href='groupRemoveFrm.php?wzID=<?php echo $sKey;?>&fgroupID=<?php echo $groupID;?>&fcpg=pv'>Remove Me</a>
  <?php 
}
?>
</form>
</div>
  
<table style='width:100%'><tr valign='top'><td style='width:76px;'>
<a href='javascript:popProfile()'>
<img style='border-radius:50%;margin-top:0px;margin-right:5px;float:left; height:85px;width:70px;border:0px solid #999999;' 
src='//image.bitmonky.com/getMbrImg.php?id=<?php echo $groupOwnerID;?>'><a></a>
</td><td style='padding:0px;'>
<?php 
if ($franchise){
  echo '<a title="Certified BitMonky city franchise owner... Click for more info." ';
  echo 'href="javascript:parent.wzGetPage(\'/whzon/franMgr/franMgr.php?wzID='.$sKey.'\')">';
  echo '<img src="//image.bitmonky.com/img/kingProf.png" style="border-radius:.5em;float:right;width:90px;height:63px;border:0px;">'; 
}
?> 
<a href='javascript:popProfile()'>
<?php echo 'Welcome To ', $mbrName;?>'s </a><b >Group Chat - "<?php echo $groupName;?>"</b><br>
<?php if ($franchise){echo "<b style:font-size:smaller;>BitMonky Franchise Owner</b>";}?>
<div ID='showMoshPit'></div>
<p>
<?php echo $bio;?>
</p>
</td></tr></table>
</div>  

<iFRAME width=100% height='250' ID="chatFrame"  style='background:#333333;' SCROLLING="vertical" FRAMEBORDER="NO" BORDER="0"></iFrame>
    
<form style='margin-top:8px;' ID='chTxtBox' method=get  onSubmit="return sendMsg();">
<input type='hidden' name='fmbrID' value='<?php echo $groupOwnerID;?>'>
<TEXTAREA style='width:75%;' onkeypress='return doClick(event);' ID='typeBox' NAME="fmsg" WRAP=VIRTUAL ROWS=3 COLS=40></TEXTAREA><br>
<input onclick='sendMsg(); return false; 'style="margin-top: 3px; border-radius: 0.5em 0.5em 0.5em 0.5em;" value="Say It" type="submit">
<?php 
if (!$inSandBox) {
  ?>
  <input onclick='openPShareGroup(); 'style="margin-top: 3px; border-radius: 0.5em 0.5em 0.5em 0.5em;" value="Share Photo" type="button">
  <?php 
}
?>
</form>

</td>
<td style='border:0px;width:160px;padding:15px;'>

<div class='infoCardClear' style='background:#222222;'>
<b>Group Chats</b><a href='groupAddGroupFrm.php?wzID=<?php echo $sKey;?>'> [+]</a>
<div ID='myGroups'></div>
<p/><b>New Messages:</b>
<div ID='wzContacts'></div>
<b>All Conversations:</b>
<div ID='wzAllContacts'></div>
</div>
</td></tr>
<tr><td colspan='2'>

<div style='margin-top:10px;' ID='wzAdSpace'>
<?php if ($paidMember < 2) {?>
<!-- BEGIN AdMgr CODE -->
<div ID="wzMainAd" style="width:100%"></div>
<!-- END Burst AdMgr CODE -->
<?php }?>
</div>

</td></tr></table>

<div ID='wzAppsContainer' style='display:none;border:8px solid #777777;border-radius: 0.5em;position:absolute;'></div>
<script src='/whozon/apps/wzAPI.js'></script>

<script>
  var cptr="";
  var lastMsgID = '1';
 
  var ifrm=document.getElementById("chatFrame");
  var typeBox=document.getElementById("typeBox");
  typeBox.focus();

  ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument;

  var myxml          = getUrlCom();
  var readxml        = getUrlCom();
  var groupsxml      = getUrlCom();
  var contactsxml    = getUrlCom();
  var contactsAllxml = getUrlCom();
  var sendmgsxml     = getUrlCom();
  var moshxml        = getUrlCom();
  var blockxml       = getUrlCom();
  var alertxml       = getUrlCom();
  var adspotxml      = getUrlCom();
  var pgTrackConn    = getUrlCom();
  var wzPVxml        = getUrlCom();
  
  var isFocused=1;
  var isIExplorer=1;
  var blockList=new Object();

  ifrm.document.open();
  ifrm.document.write(getUrl('pvChatframe.htm'));
  ifrm.document.close();

  zbug("Begin!");

  ReadContacts();
  ReadMsg();
  ReadMosh();
  ReadMyAlerts();
  rotateAdSpot();
  trackPage();
  ReadMyGroups();
  
function ReadMyGroups(){
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='groupChatList.php?wzID=<?php echo $sKey;?>&xm=' + ranTime ;
   groupsxml.open("GET", url,true);
   groupsxml.onreadystatechange = writeMyGroups;
   groupsxml.send(null);

}

function writeMyGroups(){
 
  if (groupsxml.readyState == 4){
    setTimeout("ReadMyGroups()", 30*1000);
    if (groupsxml.status == 200){
	  console.log('group:checklist');
	  var wzoutput = document.getElementById('myGroups');
      wzoutput.innerHTML = '';	  
      var j = JSON.parse(groupsxml.responseText);
	  var myGroups = j.myGroups;
	  console.log('group:test: ' + j.myGroups[0].gname);
      for (var i in myGroups) {
	    console.log ('group:looping');
        gdiv = ifrm.document.createElement('DIV');
		var htm = "<a href='groupChat.php?wzID=<?php echo $sKey;?>&fgroupID=" + myGroups[i].groupID + "'>";
		htm  = htm + "<img style='float:left;width:18px;height:24px;border-radius: .25em;border: 0px solid #74a02a;margin-right:2px;margin-bottom:2px;' ";
	    htm  = htm + "src='//image.bitmonky.com/getMbrTnyImg.php?id=" + myGroups[i].ownerID + "'/> " + Left(myGroups[i].gname,10) + "</a>";
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
  var url = "/whzon/track/trackLT.php?ID=17621&wsID=5&pgID=584085&htRefer=none";
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

function rotateAdSpot(){
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='/whzon/adMgr/srvAd.php?wzID=<?php echo $sKey;?>&fh=90&fw=&fqry=&xm=' + ranTime ;
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
  var URL = '/whzon/mbr/mbrProfile.php?wzID=' + parent.sID + '&fwzUserID=<?php echo $groupOwnerID;?>';
  parent.wzGetPage(URL);
}

function popMyProfile(){
  var URL = '/whzon/mbr/mbrProfile.php?wzID=' + parent.sID + '&fwzUserID=<?php echo $userID;?>';
  parent.wzGetPage(URL);
}



function sendVChatReq(){

//          var wzoutput = document.getElementById("vChatAlert");
//          wzoutput.innerHTML= "<img alt='Start Video Chat' title='Start Video Chat' style='border:0px;width:30px;height:21px;margin-bottom:2px;vertical-align:middle;' src='//image.bitmonky.com/img/vChatIconOn.png'>Video Started";
//          reqVChat();

}

function acceptVChatReq(){

//          var wzoutput = document.getElementById("vChatAlert");
//          wzoutput.innerHTML= "<img alt='Start Video Chat' title='Start Video Chat' style='border:0px;width:30px;height:21px;margin-bottom:2px;vertical-align:middle;' src='//image.bitmonky.com/img/vChatIconOn.png'>Video Started";

//        wzAcceptVChat(<?php echo $groupOwnerID;?>);

}

function wzEndVChat(){
        vChatStarted=false;
        var currentTime = new Date();
        var ranTime = currentTime.getMilliseconds();
        var url='/whozon/vChat/declineVchat.php?fwzUserID=<?php echo $userID;?>&xm=' + ranTime;
        var donothing=getUrl(url);
        var wzoutput = document.getElementById("vChatAlert");
//        wzoutput.innerHTML= "<a href='javascript:sendVChatReq();'><img alt='Start Video Chat' title='Start Video Chat' style='border:0px;width:30px;height:21px;margin-bottom:2px;vertical-align:middle;' src='//image.bitmonky.com/img/vChatIcon.png'>Start Video Chat with</a> - <?php echo $mbrName;?>";


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
        msg=doEmotes(msg);
        ifrm.scrollTo(0,0);
        var wzoutput = ifrm.document.getElementById("chatDiv");
        var wzAdspace = document.getElementById("wzAdSpace");

        gdiv = ifrm.document.createElement('DIV');
        gdiv.innerHTML="<table><tr valign='top'><td style='padding:0px;padding-top:4px;width:18px;'><a href='javascript:parent.popMyProfile();'><img style='margin-bottom:5px;margin-right:8px;border:0px solid #777777;float:left;' src='//image.bitmonky.com/getMbrTnyImg.php?id=<?php echo $userID;?>'></a></td><td style='padding-top:0px;'><a href='javascript:parent.popMyProfile();'><?php echo $userName;?> </a> Says:<br>" + msg + "</td></tr></table>";

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


function sendMsg(){
     var msg  = document.getElementById("chTxtBox").elements["fmsg"].value;
     msg = mkyTrim(msg);
     if (msg!='') {
 	   var currentTime = new Date();
	   var ranTime = currentTime.getMilliseconds();
	   var url='groupChatSayMsg.php?wzID=<?php echo $sKey;?>&fgroupID=<?php echo $groupID;?>&fmsg=' + escape(msg) + "&xm=" + ranTime;
       doSendMsgToServer(url);
       msg=doEmotes(msg);
       ifrm.scrollTo(0,0);
       var wzoutput = ifrm.document.getElementById("chatDiv");
       var wzAdspace = document.getElementById("wzAdSpace");

       gdiv = ifrm.document.createElement('DIV');
       gdiv.innerHTML="<table><tr valign='top'><td style='padding:0px;padding-top:4px;width:18px;'><a href='javascript:parent.popMyProfile();'><img style='margin-bottom:5px;margin-right:8px;border:0px solid #777777;float:left;' src='//image.bitmonky.com/getMbrTnyImg.php?id=<?php echo $userID;?>'></a></td><td style='padding-top:0px;'><a href='javascript:parent.popMyProfile();'><?php echo $userName;?> </a> Says:<br>" + msg + "</td></tr></table>";

       if (wzoutput.childNodes.length==0)
         wzoutput.appendChild(gdiv);
       else
         wzoutput.insertBefore(gdiv,wzoutput.firstChild);  
    
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

      var url='pvChatEnd.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $groupOwnerID;?>&xm=' + ranTime ;
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
   
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='pvChatGetContacts.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $groupOwnerID;?>&xm=' + ranTime ;
   contactsxml.open("GET", url,true);
   contactsxml.onreadystatechange = writeContacts;
   contactsxml.send(null);

}

function ReadAllContacts(){
   zbug("ReadAllContacts"); 
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='pvChatGetAllContacts.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $groupOwnerID;?>&xm=' + ranTime ;
   contactsAllxml.open("GET", url,true);
   contactsAllxml.onreadystatechange = writeAllContacts;
   contactsAllxml.send(null);

}

function writeAllContacts(){
 
  if (contactsAllxml.readyState == 4){
    if(contactsAllxml.status  == 200){ 
	  var jdata = mkyTrim(contactsAllxml.responseText);
	  if (jdata == '') {jdata = '{"myMsgs":[]}';}
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

function writeContacts(){
 
    if (contactsxml.readyState == 4){
    setTimeout("ReadContacts()",8*1000);

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
        parent.pvchatControl.innerHTML = "<span class='mpgTab' style='padding:1px;padding-top:0px;padding-left:3px;padding-right:3px;border-radius: .5em;'><b>Private Chat:</b><a href='javascript:wzAPI_hidePVC();'>[-]</a><a style='color:orange;font-weight:bold;' href='javascript:wzAPI_focusPVC();'>[+] new! " + pvMsgs + "</a></span>";
        var snd = new Audio("/sounds/wzNotify.mp3");
        snd.play();
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
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='pvChatUserAlerts.php?wzID=<?php echo $sKey;?>&xm=' + ranTime ;
   alertxml.open("GET", url,true);
   alertxml.onreadystatechange = writeMyAlerts;
   alertxml.send(null);

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
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='pvChatGetMoshBox.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $groupOwnerID;?>&xm=' + ranTime ;
   moshxml.open("GET", url,true);
   moshxml.onreadystatechange = writeMosh;
   moshxml.send(null);

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
//   var url='vChatCheckCalls.php?wzID=<?php echo $sKey;?>&fcallID=<?php echo $groupOwnerID;?>&xm=' + ranTime ;
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
//        wzoutput.innerHTML= "<img style='width:18px;height:24px;margin-left:8px;vertical-align:bottom;border:0px solid #777777;' src='//image.bitmonky.com/getMbrTnyImg.php?id=" + msgs + "'> <b style='color:brown;'> Video Call</b> <a href='javascript:acceptVChatReq()'>Accept</a> | <a href='javascript: wzEndVChat();'>Decline</a>";
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


function ReadMsg(){
   zbug("ReadMsg()");
   var vmode = '';
   if (parent.winWRTCChat){
     vmode = '&fWRTC=on';
   } 
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url='groupChatGetMsg.php?wzID=<?php echo $sKey;?>&fgroupID=<?php echo $groupID;?>&flastMsgID=' + lastMsgID + vmode + '&xm=' + ranTime ;
   readxml.open("GET", url,true);
   readxml.onreadystatechange = writeMsg;
   readxml.send(null);

}

function writeMsg(){
 
  if (readxml.readyState == 4){
    setTimeout("ReadMsg()",refresh*1000);
    if (readxml.status == 200){
	  var jdata = mkyTrim(readxml.responseText);
	  if (jdata == '') {jdata = '{"myMsgs":[]}';}
      try {
        var j = JSON.parse(jdata);
      }
      catch (err) {
        console.log ('Group Chaon JSON error',jdata);
      }
      var msgs = j.myMsgs;
      var wzoutput = ifrm.document.getElementById("chatDiv");

      for (var msg of j.myMsgs) {
        var msgID   = msg.msgID;
        var htm     = msg.htm;
        var sendID  = msg.guserID;
        var sname   = msg.gname;
        zbug("WriteMsg()");

        isThere=true;
	lastMsgID  = msgID;

        htm=doEmotes(unescape(htm));

        divflg=ifrm.document.getElementById(msgID);

        //if (divflg==null) {
          gdiv = ifrm.document.createElement('DIV');
          gdiv.id=msgID;
          var html ="<div class='infoCardClear' style='margin:.5em;'> <a href='javascript:parent.popProfile(sendID);'>";
          html += "<img style='margin-bottom:5px;margin-right:8px;border-radius:50%;float:left;' ";
          html += "src='//image.bitmonky.com/getMbrTnyImg.php?id=" + sendID + "'></a>";
          html += "<a href='javascript:parent.popMyProfile(sendID);'>" + sname + " </a><br>" + htm;
          html += "<div align='right' style='font-size:smaller;color:lightSeaGreen;'>"+msg.date+"</div></div>";
          gdiv.innerHTML = html;

          if (wzoutput.childNodes.length==0)
            wzoutput.appendChild(gdiv);
          else
            wzoutput.insertBefore(gdiv,wzoutput.firstChild); 

          var wzVDIV = document.getElementById("vChatAlert");
          if (isThere==true) {
            // if(! vChatStarted) {
            //   wzVDIV.innerHTML= "<a href='javascript:sendVChatReq();'><img alt='Start Video Chat' title='Start Video Chat' style='border:0px;width:30px;height:21px;margin-bottom:2px;vertical-align:middle;' src='//image.bitmonky.com/img/vChatIcon.png'>Start Video Chat with - <?php echo $mbrName;?></a>";
            // }  
          }

          if (isFocused==0) {
            var snd = new Audio("/sounds/wzNotify.mp3");
            snd.play();
          } 
        //}
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
<?php

$SQL = "select * from tblChatEmotes";
$emotes = mkyMsqry($SQL);
while ($eRec = mkyMsFetch($emotes)){?>
  msg=msg.replace('<?php echo $eRec['imgText'];?>','wzIRTx<?php echo $eRec['imgName']?>>');
  while (msg.indexOf("<?php echo $eRec['imgText'];?>")>-1) {
    msg=msg.replace('<?php echo $eRec['imgText']?>','');
  }

  <?php
}?>
  while (msg.indexOf("wzIRTx")>-1) {
    msg=msg.replace('wzIRTx','<img src=//image.bitmonky.com/vChat/emoticons/');
  }
  return msg;
}
</script>
</body>
</html>
<?php


function ipToCountryCD($ipStr){

  $fresult = "Not Found";

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
  
    $SQL = "select name,countryCD2 from IpToCountry  where LOWERip < ".$IPc." and upperIP > ".$IPc;
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

