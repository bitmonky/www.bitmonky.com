<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../gold/goldInc.php");

if ($userID == 17621){
  //exit('incct: '.$inChatChanID);
}

if ($franchise){
  if ($mFeeOutStanding > 5.0){
    if (!$isAdmin){
     // showInfoVideoLink(35985);
     // exit('');
    }
  }
}
$isAdmin=False;
$isOwner=False;
$isMod=False;

if ($userID==17621) {
  $isAdmin=True;
}
  
$fheight=425;

if (!$inChatChanID){
  $channel=1;
}
else{
  $channel=$inChatChanID;
}

if (isset($_GET['fchanID'])){
  $changeToChan = clean($_GET['fchanID']);
  $channel=$changeToChan;
  if (is_null($channel) || $channel=="" || $channel == 'undefined'){
    $channel=1;
  }
}
if ($userID != 0 && $defaultRm !=1 && $channel == 1){
  if ($defaultRm == -1){
    $channel = 271924;
  }
  else {
    exit('wzAcceptTerms:');
  }
}
  
$SQL = "select name,monthRate from tblCity  where ownerID is null and cityID = ".$yourCity;
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
if ($tRec){
  $cityName = $tRec['name'];
  $cityPrice = $tRec['monthRate'];
} 
else {
  $cityName = null;
}
if ($userID == 17621){
  //exit('incc: '.$channel);
}  
$SQL = "update tblwzUser set inChatChanID=".$channel." where wzUserID=".$userID;
$result = mkyMsqry($SQL);
	
$SQL = "update tblwzOnline set inChatChanID=".$channel.", inRoomID=".$channel." where wzUserID=".$userID;
$result = mkyMsqry($SQL);

$inChatChanID = $channel;
$wzChanName   = inChanName($channel);
logChanHistory($userID,$channel);

$cnlUserID=$userID;

if (!$cnlUserID) {
  $cnlUserID=0;
}
?>
<div style='padding:15px;padding-top:0px;'>
<script>parent.wzChanName = '<?php echo $wzChanName;?>';</script>
<STYLE>
INPUT {
	MARGIN: 0px; 
        BORDER: 1px solid;
	PADDING: 1px 2px 0px 2px; 
	FONT-FAMILY:  ariel,sans-serif, tahoma;
	FONT-SIZE: 10px; 
	COLOR: #444444; 
	BACKGROUND-COLOR: #efefef;
}
</STYLE>
<?php

$warned=0;

$vUserID=$userID;
$missingInfo=False;


$SQL = "SELECT moderator, suspect, banned, cityID, verified, imgFlg,TIMESTAMPDIFF(day,date(creatDate),date(now())) as nDays From tblwzUser  ";
$SQL .= "where wzUserID=".$vUserID;
	
$result = mkyMsqry($SQL);
if ($row = mkyMsFetch($result)){
  if (!is_null($row['moderator'])) {
    $isMOD=True;
  }
  if (!is_null($row['banned'])){
    //        $wzlogOut();
    echo "<script>window.location.reload();</script>";
  }
  else{
    $vCityID=$row['cityID'];
    $verified=$row['verified'];
    $nDays=$row['nDays'];
    $suspect=$row['suspect'];
    if (is_null($verified)) {
      $verified=0;
    }
    if ($verified==0 && !is_null($suspect)) {
      $missingInfo=True;
    }

    if (is_null($nDays)) {
      $nDays=99999;
    }

    if ($nDays < 300) {
      $verified=1;
    }

    if ($vUserID < 137510) {
      $verified=1;
    }

    $vImgFlg=$row['imgFlg'];
    if (!$vImgFlg){$vImgFlg==0;}
    if ($vCityID==0 || $vImgFlg==0 || $verified==0){
      $missingInfo=True; 
    }
  }
}

$isBlock=0;

$SQL = "Select chanMkapID,chanHcoID,spoken,firstname, privateChan,chanType,name,guide,ownerID, websiteID, URL,link, smNewsID,smNewsLinksID, mNewsID, ";
$SQL .="photoID, videoID, mBlogEID, tblChatChannel.img from tblChatChannel  left join tblwzUser  on wzUserID=ownerID ";
$SQL .=" left join tblWebsites on websiteID=chanWSID  ";
$SQL .="where tblChatChannel.channelID=".$channel;
$result = mkyMsqry($SQL);
if (!$row = mkyMsFetch($result)){
  echo "<div class='infoCardClear'><b style='font-size:13px;color:#6699ff;'>";
  sayTxt('No Chat Room Available For This Topic');
  echo "</b></div>";
  exit('');
}	
else {
  $tklink=$row['link'];
  $chanType=$row['chanType'];
  $URL=$row['URL'];
  $chanWSID=$row['websiteID'];
  $smNewsID=$row['smNewsID'];
  $smNewsLinksID=$row['smNewsLinksID'];
  $photoID=$row['photoID'];
  $mBlogEID=$row['mBlogEID'];
  $ownerID = $row['ownerID'];
  $ownerName=$row['firstname'];
  $mNewsID = $row['mNewsID'];
  $privateChan=$row['privateChan'];
  $spoken=$row['spoken'];
  $videoID = $row['videoID'];
  $hcoID   = $row['chanHcoID'];
  $mkapID  = $row['chanMkapID'];

  $chanOwner=False;
  $chanOwner2=False;
  $privateAccess=0; 
  if (is_null($ownerID)){ 
    $ownerID=0;
  }

  $img="<img alt='".$ownerName."'`s Profile Image' src='//image.".$whzdom."/getMbrImg.php?id=".$row['ownerID']."' style=''>";
  $imgAon="";
  $imgAoff="";
  
  if (!is_null($privateChan) && $userID!=0){
    $SQL = "SELECT count(*) as nRec from tblChanAllowList  where status=1 and chanID=".$channel." and allowUserID=".$userID;
    $result = mkyMsqry($SQL);
    $pRec = mkyMsFetch($result);
        
    $privateAccess=$pRec['nRec'];

    if ($privateAccess==0 && 2 == 1){
      $SQL = "SELECT count(*) as nRec from tblwzUserFriends  where status=1 and ((wzUserID=".$ownerID." and friendUserID=".$userID.")";
      $SQL .= " or (wzUserID=".$userID." and friendUserID=".$ownerID."))"; 
      $result = mkyMsqry($SQL);
      $pRec = mkyMsFetch($result);
      $privateAccess=$pRec['nRec'];
    }

    if ($ownerID==$userID) {
      $privateAccess=1;
      $chanOwner=True;
    }
  }


  if ($userID!=0){
    $SQL = "SELECT count(*) as nRec from tblChatChannel  where ownerID=".$userID." and channelID=".$channel;
    $result = mkyMsqry($SQL);
    $pRec = mkyMsFetch($result);
    $nowRec=$pRec['nRec'];

    $SQL = "SELECT warned from tblwzUser  where wzUserID=".$userID;
    $result = mkyMsqry($SQL);
    $pRec = mkyMsFetch($result);
    $warned=$pRec['warned'];

    if (is_null($warned)){
      $warned=0;
    }
    if ($nowRec>0 ) {
      $chanOwner2=True;
    }
  }
  else {
    $warned=0;
  }

  if (!is_null($chanWSID)) {
    $SQL = "SELECT wsImgFlg as imgFLg from tblWebsites  where websiteID=".$chanWSID;
    $result = mkyMsqry($SQL);
    $pRec = mkyMsFetch($result);
    $imgFlg=$pRec['imgFlg'];

    if ($imgFlg=1) {
      $img="<img src='//image.bitmonky.com/getWsImg.php?id=".$chanWSID."' style=''>";
    }
  }


  if (!is_null($row['photoID'])) {
    $img="<img src='//image.".$whzdom."/getPhotoTmn.php?id=".$photoID."' style=''>";
  }
  if ($videoID) {
    $SQL = "SELECT vidURL from tblwzVideo  where wzVideoID=".$videoID;
    $xresult = mkyMsqry($SQL);
    $xRec = mkyMsFetch($xresult);
    $vidURL = "//".replace("/v/","/vi",$xRec['vidURL'])."/default.jpg";
    $vidURL = mkyStrReplace("youtube.","i2.ytimg.",$vidURL);
    $img = "<img alt='video' src='".$vidURL."' style='border:0px solid #777777;float:right;margin-left:5px;'>";
  }

  if (!is_null($mBlogEID)){
    $SQL = "SELECT imgFlg as nRec from tblMBlogEntry  where mBlogEntryID=".$mBlogEID; 
    $result = mkyMsqry($SQL);
    $pRec = mkyMsFetch($result);
    $hasPic=False;
    if ($pRec) {
      $hasPic=$pRec['nRec'];
    }
    if ($hasPic) {
      $img="<img src='//image.".$whzdom."/getmBlogTmn.php?id=".$mBlogEID."' style=''>";
    }
  }
  $say = safeGET('fshout');

  if (!is_null($mNewsID)) {
    $SQL = "SELECT tblminiNews.wzUserID,tblminiNews.websiteID,articleID,imgFlg,linkImgFlg from tblminiNews  "; 
    $SQL .="left join tblminiNewsArticle  on newsArticleID=articleID where miniNewsID=".$mNewsID;

    $result = mkyMsqry($SQL);
    $pRec = mkyMsFetch($result);
    $articleID= $pRec['articleID'];
    $mnWSID= $pRec['websiteID'];
    $imgFlg= $pRec['imgFlg'];
    $linkImgFlg= $pRec['linkImgFlg'];
    if ($imgFlg==True) {
      $img="<img src='/wzUsers/mbrProfiles/miniNewsGetImgTN.asp?farticleID=".$articleID."' style=''>";
    }
    if ($linkImgFlg==1){
      $img="<img src='/wzUsers/mbrProfiles/miniNewsGetImgTN.asp?flinkID=".$mNewsID."' style=''>";
    }
  }



  if (!is_null($smNewsLinksID)) {
    $SQL = "SELECT img from tblwsNewsImg where newsLinkID=".$smNewsLinksID;
    $result = mkyMyqry($SQL);
    $pRec = mkyMyFetch($result);
    if ($pRec) {
      $imgData=$pRec['img'];
    }
    else{
      $imgData=null;
    }
    if (!is_null(imgData)) {
      $img="<img src='/getWsNewsImg.php?id=".$smNewsLinksID."' style=''>";
    }
  }

  echo '<div ID="wzMainAd" style="width:100%"></div>';

  if ($userID == $userID){
    echo "<div class='infoCardClear' style='margin-top:0px;margin-bottom:8px;background:#222322;color:#fefefe;padding:6px 12px 4px 12px;'>";
  }
  else {
    echo "<div style='border:0px solid #efefef;border-radius: .5em;355px;margin-top:0px;margin-bottom:8px;background:#fefefe;padding:6px 12px 4px 12px;'>";
  }
  ?>
  <div align='right'>
  <span ID='wxpulldwn' style='font-size:12px;'>   
  <div style="display: inline-block;position:relative;color:lightSeaGreen;padding-right:.0em;">
  <?php 
  if ($channel!=1 ) { 
    ?>
    <img alt='BitMonky Chat - More Channels Icon'  onmouseout="mclosetime();" onmouseover="pullDwnChannels();" 
    src='//image.bitmonky.com/img/gearOrange.png' 
    style='margin-right:3px;height:17px;width:17px;vertical-align:middle;border:0px;'/><b><?php sayTxt('Return To');?></b> 
    | <span style='background:lightSeaGreen;border-radius: .25em;FONT-FAMILY:padding:1px;padding-left:3px;padding-right:3px;'>
    <a  style='font-size:smaller;color:white;' href="javascript:wzChangeChannel(1);"><?php sayTxt('Lounge');?></a></span> 
    | <span style='background:lightSeaGreen;border-radius: .25em;FONT-FAMILY:padding:1px;padding-left:3px;padding-right:3px;'>
    <a  style='font-size:smaller;color:white;' href="javascript:wzChangeChannel(293174);"><?php sayTxt('Chat');?></a></span>       
    <?php
  } 
  else {
    ?>
    <img  onmouseout="mclosetime();" onmouseover="pullDwnChannels();" src='//image.bitmonky.com/img/gearOrange.png' 
    style='margin-right:3px;height:17px;width:17px;vertical-align:middle;border:0px;'/><b>MonkyTalk <?php sayTxt('Lounge');echo ' '.$lngEmoj; ?> </b>
    | <span style='background:lightSeaGreen;border-radius: .25em;FONT-FAMILY:padding:1px;padding-left:3px;padding-right:3px;'>
    <a  style='font-size:smaller;color:white;' href="javascript:wzChangeChannel(293174);"><?php sayTxt('Chat Only');?></a></span>       
    <?php
  } 
  echo "</span>";

  if ($userID!=0) { 
    ?>
    | <span style='background:lightSeaGreen;border-radius: .25em;FONT-FAMILY:padding:1px;padding-left:3px;padding-right:3px;'>
    <a style='font-size:smaller;color:white;' alt='Info on Blocking or Un-Blocking People' title='Info on Blocking or Un-Blocking People'  
    href='javascript:myBlockList();'><?php sayTxt('Block');?></a></span> 
    <?php 
  } 
  ?>
  </div></DIV>
  <div ID="wzTopicDiv" style="">
  <div ID="chanTop">
  <?php
  $source = getTRxt('A Channel Started By')." -  <a style='color:darkKhaki' 
    href=\"javascript:wzGetPage('/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$ownerID."');\">".$ownerName."</a>";
  if (is_null($ownerID) || $ownerID=="") {
    $ownerID=0;
  }

  $SQL = "SELECT count(*) as isOn from tblwzUser  where online=1 and wzUserID=".$ownerID;
  $result = mkyMsqry($SQL);
  $pRec = mkyMsFetch($result);
  $ownerOn=$pRec['isOn'];

  if ($userID!=0 && $channel!=1) {
    $SQL = "SELECT count(*) as isBlock from tblwzUserBlockList  where wzUserID=".$ownerID." and blockUserID=".$userID;
    $result = mkyMsqry($SQL);
    $pRec = mkyMsFetch($result);
    $isBlock=$pRec['isBlock'];
  }


  $ownerOnImg="<br> * Offline";
  if ($ownerOn==1) {
    $ownerOnImg="<br><img style='border:0px;margin-left:0px;' src='//image.bitmonky.com/img/siteLOG_onlineff.jpg'>";
  } 

  $imgAon="<a data='View ".$ownerName."`s Profile' alt='View ".$ownerName."`s Profile' 
    href=\"javascript:sayToMember('".stripEmotes($ownerName)."',".$ownerID.");\">";
  $imgAoff = "</a>";

  if (!is_null($tklink)) {
    if ($chanType="mBlog") { 
      $source = "<a href=\"javascript:wzGetPage('//".$whzdom."".$tklink."');\"> Read Blog Entry</a>";
      $imgAon="";
      $imgAoff="";
    }     
    if ($chanType="mNews"){
      $source = "<a href=\"javascript:wzGetPage('//".$whzdom."".$tklink."');\"> Read miniNews</a>";
      $imgAon="";
      $imgAoff="";
    }
  }
  if (!is_null($URL)){
    if (!is_null($smNewsID)){
      $source= "<a href=\"javascript:wzGetPage('//".$whzdom."/whzon/mbr/wzViewWSNews.php?fwebsiteID=".$chanWSID."&fnewsID=".$smNewsID."');\">".$URL."</a>";
      $imgAon="";
      $imgAoff="";
    }
    else{
      $source= "<a href=\"javascript:wzGetPage('//".$whzdom."/whzon/mbr/wzViewSite.asp?fwebsiteID=".$chanWSID."'>".$URL."');\"</a>";
      $imgAon="";
      $imgAoff="";
    }
  }

  if ($hcoID){
    $SQL = "select hcoHash from tblHashChanOwner  where hcoID=".$hcoID;
    $hcresult = mkyMsqry($SQL);
    $hcRec = mkyMsFetch($hcresult);
    if ($hcRec){
      $hQry=$hcRec['hcoHash'];
      $source= "<a style='color:gold' href=\"javascript:wzGetPage('/whzon/public/homepg.php?wzID=".$sKey."&wzCID="
        .$inChatChanID."&fhQry=".mkyUrlEncode($hQry)."');\">View Channel Feed Here</a>";
      $imgAon="";
      $imgAoff="";
    }
  }

  if ($mkapID){
    $SQL = "select mkapKey  from tblmkyApp  where mkapID=".$mkapID;
    $hcresult = mkyMsqry($SQL);
    $hcRec = mkyMsFetch($hcresult);
    if ($hcRec){
      $appKey  = $hcRec['mkapKey'];
      $source  = "<a style='color:gold' href=\"";
      $source .= "javascript:parent.wzGetPage('api/appProfile.php?appKey=".$appKey."');";
      $source .= "\">View The App Here</a>";
      $imgAon="";
      $imgAoff="";
    }
  }
  if (!is_null($photoID)){
    $SQL = "select wzUserID from tblwzPhoto  where PhotoID=".$photoID;
    $result = mkyMsqry($SQL);
    $pRec = mkyMsFetch($result);
    if ($pRec){
      $phUserID=$pRec['wzUserID'];
    }
    else{
      $phUserID=0;
    }
    $source= "<a href=\"javascript:wzGetPage('//".$whzdom."/whzon/mbr/mbrViewPhotos.php?wzID=".$sKey."&fwzUserID="
     .$phUserID."&vPhotoID=".$photoID."');\">View photo Here</a>";
    $imgAon="";
    $imgAoff="";
  }

  if ($imgAon == ""){
    $imgAon="<a data='View ".$ownerName."`s Profile' alt='View "
     .$ownerName."`s Profile' href=\"javascript:wzGetPage('/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$ownerID."');\">";
  }
  $imgAoff = "</a>";
}
if ($inChatChanID == $dateAppChan){
  $img = "<img src='//image.bitmonky.com/img/iconDating.png' style=''/>";
  $source= "<a style='color:darkKhaki;font-size:larger;font-weight:bold;' 
    title='Open Dating App' href=\"javascript:wzGetPage('/whzon/apps/dating/appDating.php?wzID=".$sKey."');\">".getTRxt('MonkyTalk Dating App')."</a>";
  $imgAon="<a data='Open Dating App' href=\"javascript:wzGetPage('/whzon/apps/dating/appDating.php?wzID=".$sKey."');\">";
  $imgAoff="</a>";
}
$guidetxt='Not Found';
$tkname = 'Not Found';

if($row){
  $guidetxt=$row['guide'];
  $tkname = realTimeNic($row['name']);
}

if ($userID == $userID){
  $chatBoxBG = '#111111'; //'LemonChiffon';
  echo "<div style='border:0px;border-radius: .5em;width:100%;margin-top:8px;margin-bottom:0px;background:#333333;padding:4px;'>";
}
else {
  $chatBoxBG = '#eeeeee';
  echo "<div style='border:0px;border-radius: .5em;width:99%;margin-top:8px;margin-bottom:0px;background:#fefefe;padding:4px;'>";
}
if (!isset($img)){
  $img ='';
  $imgAon='';
  $imgAoff='';
}  
$img = mkyStrReplace("style=''","style='border-radius:50%;width:47px;height:47px;float:right;margin-bottom:5px;'",$img);
echo $imgAon.$img.$imgAoff;
?>
<h2 style='margin:0em;font-size:12px;'><?php sayTxt('Topic');?> - 
<?php 
echo left($tkname,35);
if(!isset($source)){$source=null;}
?>
</h2><span style=''><b style=''><?php sayTxt('source')?>:</b> - <?php echo $source;?></span>
<?php
if (!isset($chanOwner2)){$chanOwner2=null;}
if ($chanOwner2 ) {
  ?>
  <br /><A style='font-size:11px;' href=javascript:wzOwnerEditChan(<?php echo $channel;?>);><?php sayTxt('Edit Your Channel');?></a>
  <?php 
} 
?>
<br clear='right'/>  
<div ID='liveStream' style='width:100%;'>
<?php
if ($userID != 0){
  echo "<button type='button' class='mkyold-button' onclick='chanGoLive()'>View Live Stream</button>";
  //}
  //if ($userID == 17621){
  echo " <button type='button' class='mkyold-button' onclick='chanChooseBroadTime()'>Broadcast</button>";
}
?>
</div>
<div align='right' ID='liveSCtrls' style='display:none'>
<button type='button' class='mkyold-button' onclick='chanCloseStream()'> Close Stream </button>
<button type='button' class='mkyold-button' onclick='chanMoreStreams()'> View More </button>
<button type='button' class='mkyold-button' onclick='chanChooseBroadTime()'> Broadcast </button>
<!--input type='button' style='background:orange;color:white;' onclick='chanPostLSVComment(".$cstrID.");' value=' Post Reply ' /-->
</div>
<div align='right' ID='liveBCtrls' style='display:none'>
<button type='button'  class='mkyold-button'  onclick='chanEndBroadcast()'> End Broadcast </button>
</div>
</div>
</div>
</div>	
<?php 
if ($userID==0) {
  ?>
  <form ID='mkyChatBox' name='frmTalkAI' style='margin-top:0px;padding-top:3px' action="javascript:wzQuickReg();">
  <input type='hidden' NAME='flogin' value='yes'>
  <?php 
} 
else { 
  ?>
  <form style='margin-top:0px;padding-top:3px;' ID='chTxtBox' method='post'  onSubmit="return sendMsg();">
  <?php 
} 
?>
<input type='hidden' NAME='fqry' value='!#=qry#!'>
<?php 
if (safeGET('flogin')=="yes" && $userID==0 ){
  ?>
  <a style='color:red;' href='javascript:wzQuickReg();'>Please Login or [Click Here to Join].</a>
  <?php
} 
if ($userID!=0){
  $action = "doClick(event);";
}
else{
  $action = "doChatClick(event);";
}
if(!isset($privateChan)){$privateChan = null;}    
if ($isBlock==1 || (!is_null($privateChan) && $privateAccess==0)) {
  echo "<b>This Channel Is Private:</b>";
  if ($userID!=0 && $isBlock==0 ) { 
    ?>
    <a style='color:darkKhaki' href='javascript:tRequestChanPass();'><?php sayTxt('Request Access To Room');?></a>
    <?php 
  } 
  ?>
      
  <p><?php sayTxt('Return To');?>:
  <span style='background:lightSeaGreen;border-radius: .25em;FONT-FAMILY:padding:px;padding-left:3px;padding-right:3px;'>
  <a style='color:white' href='javascript:wzChangeChannel(1);'><?php sayTxt('Lounge');?></a></span>
  <span style='background:lightSeaGreen;border-radius: .25em;FONT-FAMILY:padding:px;padding-left:3px;padding-right:3px;'>
  <a style='color:white' href='javascript:wzChangeChannel(293174);'><?php sayTxt('Chat');?></a></span>
  </p>

  <?php
} 
else { 
  $class = " style='color:#777777;background:".$chatBoxBG.";";
  $class .= "border-radius: .5em;FONT-FAMILY: tahoma,sans-serif;font-size:13px;font-weight:bold;
    padding:2px;width:calc(100%);height:50px;margin:5px 0px 0px 0px;'";
  $class .= "  WRAP='VIRTUAL' scrollbars='no'";
  if ($userID == 0){
    $place   = "Right Click On Member Photos To Call Them";
    $tAction = " onclick='wzQuickReg();' onkeypress='wzQuickReg();'"; 
  }
  else{
    $place = "Right Click On Member Photos To Call Them";
    //if ($missingPInfo && $channel != 284267 ){ 
    //  $tAction = "onclick='finishRegister();'";
    //}
    //else {
    $tAction = "onkeypress='return ".$action."'"; 
    //}
  }
  if ($inChatChanID == $dateAppChan && !$mkdID){
    $tAction = "onclick='parent.dateAppAlert();'";
  }
  $txArea = getTextArea('typeBox','fmsg',$palmEmoj." ...",$place,$tAction,$class);
  echo $txArea;
  ?>
  <div ID='siteMonkDisclaimer' class='infoCardClear' style='display:none'>
  <div align='right'><a href='javascript:hideMkyDisclaimer();'>Hide[x]</a>
  </div>
  Disclaimer - Agent SiteMonkey AI is a conversational chatbot only . It may say things 
  that are not factual. This is particularly true when asked specific questions about 
  the bitmonky website.
  </div>
<!--
  <textArea ID='typeBox' 
  style='background:<?php echo $chatBoxBG;?>;
  border-radius: .5em;FONT-FAMILY: tahoma,sans-serif;font-size:13px;font-weight:bold;padding:2px;width:calc(100% - 24px);height:50px;margin-top:5px;'

  <?php 
  if ($userID==0 ) { 
    ?> 
    style='font-weight:bold;font-size:13px;' placeholder='<?php echo $palmEmoj;?> ...<?php sayTxt('Type Here To Begin Chatting');?>!' 
    onclick='wzQuickReg();' onkeypress='wzQuickReg();'
    <?php 
  } 
  else { 
    if ($missingPInfo && $channel != 284267 ){
      echo 'onclick=finishRegister();';
    } 
    else {
      ?>
      onkeypress='return <?php echo $action;?>'  
      placeholder='<?php echo $palmEmoj;?> ...<?php sayTxt('Type Here To Talk About This Topic');?>!' 
      <?php 
    }
  }
  ?>  NAME="fmsg"   WRAP='VIRTUAL' scrollbars='no'><?php echo $say;?></textArea>
 -->
  <br>
  <style>
    .unicode-button {
         display: inline-block;
         white-space: nowrap;
          width: auto;
         margin: 0.5em 0;
         font-size: 1em;
         color: white;
         padding:1px 4px 1px 3px;
         background-color:#74a02a;
         border-radius: .25em;
         -webkit-border-radius: .25em;
         -moz-border-radius: .25em;
         transition: all 300ms ease;
         border:0;
         box-shadow:none;
         text-shadow:none;
    }

    button.unicode-button {
         cursor: pointer;
         overflow: visible;
    }

    .unicode-button:before {
         content: "\27A5";
         padding-right: 10px;
    }

    .unicode-button:focus {
          outline: 0;
    }
  </style>
  <span ID='showShareSpot' class='infoCardClear' style='background:#151515;margin-top:.5em'>
  <?php 
  if ($channel==1 ) {
    ?>
    <button type="button" value=" Send " onclick="sendMsg();" class="unicode-button" style="padding:.23em;"/> Send </button>
    <?php
  } 
  else { 
    ?>
    <button type="button" value=" Send " onclick="sendMsg();" class="unicode-button"  style="padding:.23em;"/> Send </button>
    <?php
  } 
  ?>
  <button alt='Share Photos With The Room' style='padding:.27em;font-size:1em;margin-top:6.5px;margin-bottom:6.5px;border-radius: .25em;' 
  type="button" class="mkyold-button" onclick='startPShare();'> <?php sayTxt('Share Photo');?></button> 
  <button ID='chanRestore' style='display:none;vertical-align:top;margin-top:.85em;' 
  type='button'  class="mkyold-button" onclick='chanRestorMinimize()'/> + </button>
  <button ID='chanMin' style='display:inline-block;vertical-align:top;margin-top:.85em;padding:.15em' 
  type='button' class="mkyold-button" onclick='chanMinimize()'/> ^ </button>
  </span>
  <?php 
} 
if(!isset($spoken)){$spoken = null;}
if (!is_null($spoken)) { 
  ?>
  <span style='font-size:smaller;'>
  <b style='margin-left:1em;'><?php sayTxt('Language');?>:</b> <?php echo $spoken;?>
<!--
  <?php 
  if ($mumbles){
    $nic = mkyStrReplace(' ','.',$userName);
    $nic = mkyStrReplace('@','',$userName);
    $mlink = "<a href='mumble://".mkyUrlEncode($nic)."@wzdev.<?php echo $whzdom;?>/?version=1.2.0' title='Click The Lips To Join The Mumble Room!'>";
    ?>
    |<span style='white-space:nowrap;'><?php echo $mlink;?>Run Mumble</a>
    <?php echo $mlink;?>
    <img style='width:40px;height:20px;border:0px;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/wzMumbleImg.png'></img>
    </a></span>
    <?php 
  } 
  else {
    ?>
    |<span style='white-space:nowrap;'><a href="javascript:wzGetPage('/whzon/mumble/howToMumble.php?wzID=<?php echo $sKey;?>');">Try Mumble</a>
    <a data="<?php echo $whzdom;?> new voice chat channel... Try It!" 
    href="javascript:wzGetPage('/whzon/mumble/howToMumble.php?wzID=<?php echo $sKey;?>');">
    <img style='width:40px;height:20px;border:0px;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/wzMumbleImg.png'></img></a></span>
    <?php
  }
  ?>
 -->
  </span>
  </form>
  <?php 
}   
if(!isset($chanOwner)){$chanOwner = null;}
if ($chanOwner ) { 
  ?>
  <?php sayTxt('This Is Your Private Channel');?>:
  <a style='color:darkKhaki' href='javascript:myAllowList();'><?php sayTxt('View Your Allow List');?></a> 
  <br><a style='color:darkKhaki' href='javascript:callToChat();'> <?php sayTxt('Call Your Group To Meeting');?><a>
  <?php
} 
$franText = "Sorry ".$userName.", you missed the chance to buy your city... See what other cities are still available!.";
if ($cityName){
  $franText = "Hey ".$userName." The City Franchise For '".$cityName."' Is Available Buy It For Only $".mkyNumFormat($cityPrice,2)."/mth!";
}
$franText = "<a style='color:brown;' href=\"javascript:clickAdSpot2('/whzon/franMgr/franMgr.php?wzID='+sID);\">".$franText;
if ($franchise || 1 == 1) { $franText = '';}
$boost = checkBoost($userID,$sKey);
if ($boost) {$franText = $boost;}
if ($userID!=0 || 1==1) { 
  ?>
  <div ID="inThisRoomNow"></div>
  </div>
  <span ID="wzSysMsgDiv">
  <?php 
  if ($franText != ""){
    $boostBGC = '#fefefe';
    if ($userID == $userID){
      $boostBGC = 'fireBrick';
    }
    $franText = mkyStrIReplace('<a',"<a style='color:LemonChiffon;' ",$franText);
    echo "<div style='height:65px;background:".$boostBGC.";border-radius: .5em;padding:1em;'>";
    echo $franText."</a></div>";
  }  
  ?>
  </span>
  <?php
} 
else {
  echo "</div>";
}
?>
<table style="width:100%;"><tr><td><div ID="pvChatAlertOld"></div><div ID="pvDChatAlertSpot"></div></td></tr></table>

<p/>
<?php 
if (!is_null($privateChan) && $privateAccess==0 ) {
  ?>
  <?php 
} 
else { 
     
  echo '<div ID="chatLoading" width="100%:height:1.7em;padding-left:.9em;" >';
  echo "<div style='display:inline;height:28px;color:#777777;' ><div class='mkyloader'></div>Loading ...</div>";
  echo '</div><div style="width:355px;border:0px;" ID="chatFrame"></div>'; 
}
?>
  

</div>
<?php
      
function checkBoost($userID,$sKey){
       return null;
       $SQL = "Select top 1 boostCount,gperBoost,nBoosts, boostACID from tblLikeBoosts  "; 
       $SQL .= "left join tblwzUserLikes  on likeActivityID = boostACID and wzUserID = ".$userID." ";
       $SQL .= "where done is null and likeActivityID=null and NOT boostUID = ".$userID." ";
       $SQL .= "order by gperBoost desc";
	
       $result = mkyMsqry($SQL);
       $tRec = mkyMsFetch($result);
	
       if($tRec){
         $acID = $tRec['boostACID'];
         $boostLink = getBoostLink($acID,$sKey);
         if(!$boostLink){
	    return null;
         }
         $htm = $boostLink.'<img style="float:left;height:65px;border:border-radius:.5em;0px;margin-right:9px;" ';
         $htm .= 'src="//image.bitmonky.com/img/bitGoldCoin.webp"/> ';
         $htm .= 'Your Opinion Is Wanted! <img style="vertical-align:middle;" src="//image.bitmonky.com/vChat/emoticons/wow.gif"/>  ';
         $htm .= 'Click Here to like or dislike this post and earn '.$tRec['gperBoost'].' Gold... (note 1 like/dislike only).';
         return $htm;
       }
       return null;
}
function getBoostLink($acID,$sKey){
       $SQL = "Select acCode, acItemID,wzUserID from tblActivityFeed  "; 
       $SQL .= "where activityID = ".$acID;
	
       $result = mkyMsqry($SQL);
       $tRec = mkyMsFetch($result);
       if ($tRec){
         $acCode = $tRec['acCode'];
         $itemID = $tRec['acItemID'];
         $owner  = $tRec['wzUserID'];
	  
	 if ($acCode == 7){
	   return "<a href=\"javascript:clickAdSpot2('/whzon/mbr/mbrViewPhotos.php?wzID=".$sKey."&vPhotoID=".$itemID."&fwzUserID=".$owner."');\">";
	 }	
	 if ($acCode == 17){
	   return "<a href=\"javascript:clickAdSpot2('/whzon/mbr/vidView/viewVideoPg.php?wzID=".$sKey."&videoID=".$itemID."');\">";
	 }	
	 if ($acCode == 18){
	   return "<a href=\"javascript:clickAdSpot2('/whzon/mbr/mbrViewWNewsShare.php?wzID=".$sKey."&newsID=".$itemID."');\">";
	 }	
	 if ($acCode == 19){
	   return "<a href=\"javascript:clickAdSpot2('/whzon/mbr/mbrViewSItemShare.php?wzID=".$sKey."&itemID=".$itemID."');\">";
	 }	
       }
       return null;
}
function getBanWords(){

       $SQL = "SELECT banWord from tblbanWords  where mute=1"; 
       $result = mkyMsqry($SQL);
       while ($bnRec = mkyMsFetch($result)){
         $bstr=$bstr."'".mkyStrReplace(' ','',$bnRec['banWord'])."'";
         $bstr=$bstr.",";
       }
       return left($bstr,strlen($bstr)-1);
}
?>
