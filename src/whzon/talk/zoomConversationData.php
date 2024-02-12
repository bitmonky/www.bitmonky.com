<!doctype html>
<html class='pgHTM' style='background:#333333;' lang="en">
<head>
  <meta charset="utf-8">
  <title>Whzon.com</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>
	  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
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
<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../apps/dating/dateInc.php");

ini_set('display_errors',1);
error_reporting(E_ALL);
?>
<script>
	function highlightChatMsg(id){
	}
	function normalizeChatMsg(){
	}
	function pshare(photoID,mbrID){
	  parent.pshare(photoID,mbrID);
	}
</script>	
</head>

<body class='pgBody' style='background:#333333;margin:0px;' onload='scrollTo(0,0);parent.scrollTo(0,0);'>
<table class='docTable' style='width:100%;'><tr valign='top'><td>
<div class='infoCardClear'>
<?php
  $mbrID = safeGetINT('fmbrID');
  $mbrUID = getMkdUID($mbrID);
  
    $channel="none";
    if (isSet($_GET['flmsgID'])){$lastMsgID=clean($_GET['flmsgID']);} else {$lastMsgID = 0;}
    if (isSet($_GET['fnrec']))  {$nDispRec=clean($_GET['fnrec']);} else {$nDispRec = 65;}
    if (isSet($_GET['fimg']))   {$imgSize=clean($_GET['fimg']);}   else {$imgSize='normal';}
    if (isset($_GET['fchanID'])) { $chanID = clean($_GET['fchanID']);} else {$chanID = null;}
	
	if ($chanID){
	  $channel = $chanID;
	}
    
    if ($imgSize == 'normal'){
      $imgfile = 'getMbrTmn.php';
      $ih = '44px';
      $iw = '34px';
    }

    if ($imgSize == 'small'){
      $imgfile = 'getMbrTnyImg.php';
      $ih = '24px';
      $iw = '18px';
    }
    if ($channel == $dateAppChan){
      $imgfile = 'getDProfThm.php'; 
    } 
    if ($channel=="") 
      $channel=1;
    else{
      if ($channel=="none"){
        if ($inChatChanID){
          $channel=$inChatChanID;
          }
        else
         $channel=1;
      }
    }
$isOwner = null;
$mpath = 'mbr';
if ($channel == $dateAppChan){
  exit('Dating History Coming Soon...');
  $mpath = 'apps/dating';

  $SQL = "SELECT top 500 videoID,video,isVshare, isShare, moshPitID as inMosh,inChatChanID, null as wzOnlineID, msgID, moderator, sex, age, 0 as online, ";
  $SQL .= "mkdID as wzUserID, country,mkdNic as firstname, msg, cDate from tblChatterBox  ";
  $SQL .= "left Join tblMoshUsers  on tblMoshUsers.wzUserID=tblChatterBox.wzUserID ";
  $SQL .= "left Join tblwzUser  on tblwzUser.wzUserID=tblChatterBox.wzUserID ";
  $SQL .= "inner join tblMkyDating on mkdUID = tblChatterBox.wzUserID ";
  $SQL .= "where (tblChatterBox.wzUserID = ".$mbrUID.") and channel = ".$channel." order by msgID desc";
}
else {
  $SQL = "SELECT top 500 videoID,video,isVshare, isShare, moshPitID as inMosh,inChatChanID, wzOnlineID, msgID, moderator, sex, age, 1 as online, ";
  $SQL .= "tblChatterBox.wzUserID, country,firstname, msg, cDate from tblChatterBox  ";
  $SQL .= "left Join tblMoshUsers  on tblMoshUsers.wzUserID=tblChatterBox.wzUserID ";
  $SQL .= "left Join tblwzOnline  on tblwzOnline.wzUserID=tblChatterBox.wzUserID ";
  $SQL .= "where (tblChatterBox.wzUserID = ".$mbrID.") and channel = ".$channel." order by msgID desc";
}

$result = mkyMsqry($SQL) or die($SQL);
$tRec = mkyMsFetch($result);

if ($tRec) {
  $pg=0;
    if ( isset($_GET['newPg'])){
      $pg = clean($_GET['newPg']);
    }
	else {
	  $pg = 0;
	}

    $nextPage = $pg; 
    $n        = $pg + 1;
    $pgcount  = 0;
    while ($tRec && $pgcount<$nextPage) {
      $pgcount = $pgcount + 1;
      $tRec = mkyMsFetch($result);
    }

    $i=0;
    $nRows=5;
    $appName="zoomConversationData.php";

    if ( !$tRec ) {
      echo "! No Conversation To List...";
    }

    while ($tRec && $i<$nRows){

      $wzOnlineID = $tRec['wzOnlineID'];
      $crUserID   = $tRec['wzUserID'];
      $inChan     = $tRec['inChatChanID'];
      $inMosh     = $tRec['inMosh'];
      $isShare    = $tRec['isShare'];
      $isVshare   = $tRec['isVshare'];
      $video      = $tRec['video'];
      $msgID      = $tRec['msgID'];
      $videoID    = $tRec['videoID'];
	  $cDate      = $tRec['cDate'];

      $gotoChan="";

      if (is_null($isShare)) 
        $isShare="False";
      else
        $isShare="True";

      $deleteMsg="";
      if ($isOwner)
        $deleteMsg=" - <a href=\"javascript:parent.ownerRemoveChatMsg(".$msgID.",'".$sKey."');\">Remove</a>";

      $gotoMosh="";

      if(!is_null($inMosh)) 
        $gotoMosh="<a data='Listening to music! Click to join them' href='javascript:parent.OpenGigBox(".$inMosh.");'><img style='border:0px;vertical-align:top;margin-top:3px;margin-left:5px;height:10px;width:13px;' src='//image.bitmonky.com/img/musicIcon.png'></a>";

      if (is_null($inChan)){ 
        if (!is_null($wzOnlineID)) {
          $gotoChan=" <span style='font-size:11px;'>- (stepped out)</span>";
          $gotoChan="";
        }
      }
      else{
        if ($inChan!=$channel) {
          $SQL= "select  left(name,30) as name from tblChatChannel where isMod is null and channelID=".$inChan;
          $resultCR = mkyMsqry($SQL);
          $crRec = mkyMsFetch($resultCR);
          If ($crRec)
            $goToChan="<div style='margin-top:2px;font-size:11px;color:brown;'>Moved To Room --> <a style='font-size:11px;'  href='javascript.parent.wzChangeChannel(".$inChan.");'> ".mkyTrim($crRec['name'])."</a></div>";
        }
      }


      if (is_null($wzOnlineID)){
        if ($channel == $dateAppChan){
          $SQL = "select  moderator, sex, age, mkdNic as firstname, country from tblwzUser  ";
          $SQL .= "inner join tblMkyDating on mkdUID = wzUserID ";
          $SQL .= "where mkdID=".$crUserID;
        }
        else {
          $SQL= "select  moderator, sex, age, firstname, country from tblwzUser  where wzUserID=".$crUserID;
        }
        $resultCR = mkyMsqry($SQL);
        $crRec = mkyMsFetch($resultCR);

        $online=0;
        if ($crRec){
          $firstname = $crRec['firstname'];
          $age       = $crRec['age'];
          $sex       = $crRec['sex'];
          $wzMod     = $crRec['moderator'];
          $country   = $crRec['country'];
        }
      }  
      else{
        $online=1;
        $firstname = $tRec['firstname'];
        $age = $tRec['age'];
        $sex = $tRec['sex'];
        $wzMod=$tRec['moderator'];
        $country   = $tRec['country'];
      }

      if (!is_null($sex)){
        if ($sex==True ){
          $sex = "f - ";
          }
        else{
          $sex = "m - ";
        }
      }   
      if (is_null($wzMod)){
        $wzMod=0;
      }
      
      $admin="";
      $msg = hexToString($tRec['msg']);
      $msg=mkyStrReplace("broadIcon.jpg","broadcast.jpg",$msg);
      if ($isVshare == 1 || $isVshare == 2 ){
        $img = $video;
        $img = mkyStrReplace("www.youtube.com/v","i2.ytimg.com/vi",$img);
        $img = mkyStrReplace("youtube.com/v","i2.ytimg.com/vi",$img);
        $img = $img."/default.jpg";
        $sharetxt = $msg;
        $shareApp = "vshare('".$video."');";
        if ($isVshare == 2) {
          $shareApp = "videoShare(".$videoID.");";
        }
        $msg = "<div style='width:300px;'><a href=javascript:parent.".$shareApp.">";
        $msg .= "<img style='vertical-align:top;float:right;margin-left:1em;width:135px;height:80px; border-radius:.5em;' ";
        $msg .= "src='".$img."'></a>";
        $msg .= "<b>has shared this video</b><br>".$sharetxt."</div>";
      }
      $msg=mkyStrReplace("^"," ",$msg);

      if ($wzMod==2)
        $admin="<img src='//image.bitmonky.com/img/adminStar.jpg' height='10' width='15' style='vertical-align:middle;border-radius:50%; margin:0px;border:0px;' title='System Admin' alt='System Admin'>";

      if ($wzMod==1) {
        $admin="<img src='//image.bitmonky.com/img/crownicon.png' height='10' width='10' style='vertical-align:middle;border-radius:50%; margin:0px;border:0px;margin-left:3px;margin-right:3px;margin-bottom:3px;' title='Moderator' alt='Moderator'>";
        $admin="";
      }

?>
    <table ID='cmsgEL<?php echo $msgID;?>' onmouseover='highlightChatMsg(<?php echo $msgID;?>);' onmouseout='normalizeChatMsg();' 
    style='width:100%;margin:0px;margin-bottom:8px;border-collapse:collapse;border:0px solid;'>
    <tr valign='top'>
    <td style='width:50px;padding-left:0px;'>
    <a data='View <?php echo $firstname;?>`s Profile' href=javascript:parent.wzGetPage('/whzon/<?php echo $mpath;?>/mbrProfile.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $crUserID;?>',<?php echo $crUserID;?>);>
    <img alt='View Profile' style='width:<?php echo $iw;?>;height:<?php echo $ih;?>;border-radius:50%;margin:3px;margin-right:6px;border:0px solid #777777;' 
    src='//image.bitmonky.com/<?php echo $imgfile;?>?id=<?php echo $crUserID;?>'></a>
    </td><td>
    <a style='font-size:12px;' title='View <?php echo $firstname;?>`s Profile' alt='View Profile'  
    href=javascript:parent.wzGetPage('/whzon/<?php echo $mpath;?>/mbrProfile.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $crUserID;?>',<?php echo $crUserID;?>);>
    <?php echo left(mkyTrim($firstname),30);?></a><?php echo $admin;?>  <?php echo $gotoMosh;?>
    <?php if ($userID!=0){
       if ($online==1 &&  $userID!=$crUserID) {?>
          <a href=javascript:parent.wzPopChat('<?php echo $crUserID;?>');>
          <img title='Start Private Chat' style='vertical-align:middle;border:0px;height:2.5em' src='//image.bitmonky.com/img/chatBubIcon.png'></a>
       <?php }else{?>
         <?php if ($userID!=$crUserID) {?>
            <b style='font-size:11px;'>(Offline)
            <a data='Send <?php echo $firstname;?> an email' href="javascript:parent.wzGetPage('/whzon/mbr/mail/sendMailForm.php?wzID=<?php echo $sKey."&fwzUserID=".$crUserID;?>');">
            <img style='vertical-align:middle;border:0px;height:2.5em;' src='//image.bitmonky.com/img/emailIcon.png'></a></b>
         <?php }
       }
    }?>
    <font style=''><?php echo $sex?> <?php echo $age;?> <b style='color:#888888;'> <?php echo $country;?> </b>
    <br>
    <font style='color:white;font-size:13px;'><?php echo $msg;?><?php echo $gotoChan;?><?php echo $deleteMsg;?></font></font>
    <?php
    if (!$sessISMOBILE){
      //echo "</td><td style='width:100px;'>";
    }
    echo "<br clear='right'/><div align='right' style='font-size:smaller;'>".$cDate."</div>";
    echo "</td></tr></table>";
    
    $i=$i+1;
    $n=$n+1;
    $tRec = mkyMsFetch($result);
  }//wend

  echo "<div style='margin-top:2em;'>";

  echo "<a class='scrollBut' href='".$appName."?wzID=".$sKey."&newPg=".($nextPage + $nRows)."&fmbrID=".mkyUrlEncode($mbrID).'&fchanID='.$channel."'>Next</a>";
  if($nextPage > 0) {
    echo "<a class='scrollBut' href='".$appName."?wzID=".$sKey."&newPg=".($nextPage - $nRows)."&fmbrID=".mkyUrlEncode($mbrID).'&fchanID='.$channel."'>Back</a>";
  }
  echo "<a class='scrollBut' href='".$appName."?wzID=".$sKey."&newPg=0&fmbrID=".mkyUrlEncode($mbrID).'&fchanID='.$channel."'>Top</a>";
  echo "</div>";
}


echo "</div>";

if ($sessISMOBILE){
  echo "<P/>";
}
else {
  echo "</td><td style='padding:1em;'>";
}
getBigCubeAds('0px',2);	

echo "</td></tr></table>";
echo "</body></html>";
function hexToString($hexString) {
   return $hexString; 
   //return pack("H*" , mkyStrReplace('%', '', $hexString));
}
?>
