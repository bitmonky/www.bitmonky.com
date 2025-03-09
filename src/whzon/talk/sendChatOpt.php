<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../gold/goldInc.php");

if (!$userID==0 && !is_null($inChatChanID)){
  $shout = clean($_GET['fshout']);
  $digiHut = safeGET('digiHut');
  $hutOID = 'null';
  if ($digiHut){
    $hutOID = getUserID($digiHut);
  }
  $postTo = safeGetINT('pchan');

  if ($postTo){
    $channel = $postTo;
  }
  else {
    $channel=$inChatChanID;
  }
  $SQL = "select ownerID from tblChatChannel  where channelID=".$channel;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  $ownerID=$tRec['ownerID'];

  $SQL = "select count(*) as isBlock from tblwzUserBlockList  where wzUserID=".$ownerID." and blockUserID=".$userID;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  $isBlock=$tRec['isBlock'];
  if (is_null($isBlock)){
    $isBlock=0;
  }

  if ($isBlock==0){

    $xmute=0;

    $SQL = "select sandBox, mute, TIMESTAMPDIFF(minute,DATE_ADD(mute,INTERVAL muteTerm minute),now()) as muteTerm, online  ";
    $SQL .= "from tblwzUser  where wzUserID=".$userID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if ($tRec) {
      $mute=$tRec['mute'];
      $online=$tRec['online'];
      $muteTerm=$tRec['muteTerm'];
      $xsbox=$tRec['sandBox'];

      if (!is_null($muteTerm) && $xsbox!=1) {
        if ($muteTerm > 0 ) {
          $SQL = "UPDATE tblwzUser SET mute=null, muteTerm=null where wzUserID=".$userID;
          $result = mkyMsqry($SQL);
          $xmute=0;
          $mute=null;
        }
      }
    }

    if ($mute || $xsbox) {
      $shout="";
      $xmute=1;
    }


    if ($online==0){
      $SQL = "update tblwzUser set online=1 where wzUserID=".$userID;
      $result = mkyMsqry($SQL);
    }



    if ($shout!="") {
      echo "msgi is4: -".$shout;
      $cshout=cleanChat($shout);
      echo "msgi becomes: -sfdd: ".$cshout;
      $cshout=left($cshout,850);

      $preMsg=null;
      $SQL = "select top 1 msg from tblChatterBox  where wzUserID=".$userID." order by msgID desc";
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      if ($tRec) {
        $preMsg=$tRec['msg'];
      }

      if (!is_null($preMsg)) {
        if ($cshout==$preMsg) {
          $cshout="";
        }
      }

      if ($cshout!=""){
        $toUID = chatGetCallerID($cshout);    
        if ($toUID != 63555){
	  $toUID = 'null';
	}
	$timestamp=mkySQLDstamp();
        $SQL = "insert into tblChatterBox (wzUserID,msg,channel,cDate,callToUID,cboxHutOID)";
	$SQL .= " values(".$userID.",N'".$cshout."',".$channel.",'".$timestamp."',".$toUID.",".$hutOID.")";
        $result = mkyMsqry($SQL);

        $SQL = "select msgID from tblChatterBox  where wzUserID=".$userID." and channel=".$channel." and cDate='".$timestamp."'";
        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);
        if ($tRec) {
          $msgID=$tRec['msgID'];
        }
        else {
          $msgID=0;
        }

        $SQL = "update tblChatChannel set rdate='".$timestamp."' where channelID=".$channel;
        $result = mkyMsqry($SQL);

        $SQL = "update tblChanHistory set nChats = nChats +1 where chChanID=".$channel." and chUID=".$userID;
        $result = mkyMsqry($SQL);

        $SQL = "insert into tblChatterBuf (wzUserID,msg,channel,realID,cboxHutOID)";
        $SQL .= " values(".$userID.",N'".$cshout."',".$channel.",".$msgID.",".$hutOID.")";
        $result = mkyMsqry($SQL);
		
	if ($channel != 1 ){
          $SQL = "update tblChatChannel set  nPosts = 0 where nPosts is null and channelID=".$channel;
          $result = mkyMsqry($SQL);

          $SQL = "update tblChatChannel set  last24hrs = 0 where last24hrs is null and channelID=".$channel;
          $result = mkyMsqry($SQL);
        }
		
        $SQL = "update tblChatChannel set lastpost = now(), nPosts = nPosts + 1, last24hrs = last24hrs + 1 where channelID=".$channel;
        $result = mkyMsqry($SQL);
	//if ($channel != 1) {
	  sendNotifications($channel,$userID,$ownerID,$cshout,$msgID);
	//}
        logReferralActivity(2,$userID); 
      }
    }
  }
}



function notifyAtNotice($chan,$talkerID,$msg,$msgID,$mkyRes=false){
    
   global $dateAppChan;

   if ($chan == $dateAppChan){
     return;
   }
   $toUID = chatGetCallerID($msg);  
   if ($toUID == 63555){
     respondAsMonkey($chan,$talkerID,$msg,$msgID);
     return;
   }
   $isOnline = null;
   $inCCID   = null;
   if ($toUID){
     $SQL = "select TIMESTAMPDIFF(second,lastaction,now())la, inChatChanID from tblwzOnline where wzUserID = ".$toUID;
     $result = mkyMsqry($SQL);
     $tRec = mkyMsFetch($result);
     if ($tRec){
       $isOnline = $tRec['la'];
       $inCCID   = $tRec['inChatChanID'];
       notifyAtOnline($chan,$talkerID,$msg,$toUID,$isOnline,$inCCID,$msgID,$mkyRes);
       return; 
     }
     //notifyAtENotice($chan,$talkerID,$msg,$toUID);
     sendAtYouENotice($toUID,$chan,$talkerID,$msg);
   }
}
function notifyAtOnline($chan,$talkerID,$msg,$toUID,$isOnline,$inCCID,$msgID,$mkyRes=null){
  if ($mkyRes){
    $talkerID = 63555;
  }    
  $SQL = "select count(*)nRec from tblChatAtNotice where cnotCID = ".$msgID;  
  $result = mkyMsqry($SQL);
  //if ($result === false){
  //  return null;
  //}
  $tRec = mkyMsFetch($result);
  if ($tRec['nRec'] == 0){
    $SQL  = "Insert into tblChatAtNotice (cnotToUID,cnotFromUID,cnotCID,cnotDate) ";
    $SQL .= "values (".$toUID.",".$talkerID.",".$msgID.",now())";
    $result = mkyMsqry($SQL);
  }
}
function notifyAtENotice($chan,$talkerID,$msg,$toUID){
} 
function sendAtYouENotice($toID,$chanID,$fromID,$msg){
 
      if ($toID != 17621){
        //return;
      }
      $SQL = "select name from tblChatChannel  ";
      $SQL .= "where channelID = ".$chanID;
      $main = mkyMsqry($SQL);
      $tRec = mkyMsFetch($main);

      $chan = $tRec['name'];

      $SQL = "select firstname,countryID,sex from tblwzUser  ";
      $SQL .= "where wzUserID = ".$fromID;
      $main = mkyMsqry($SQL);
      $tRec = mkyMsFetch($main);

      $from = $tRec['firstname'];

      $m = "<h2>Message From {".$from."} In Channel {".$chan."} </h2> ";

      $m .= '{'.$msg.'}';

      $m .= "<p/>Login now to respond.";
      $img = "https://image.bitmonky.com/getMbrImg.php?id=".$fromID;
      $sty = " style='border-radius:50%;' ";

      sendUserGNotice($toID,$m,'goToChan.php','?chanID='.$chanID,$img,$sty);

}
function sendNotifications($chan,$talkerID,$ownerID,$msg=null,$msgID,$mkyRes=null){
   notifyAtNotice($chan,$talkerID,$msg,$msgID,$mkyRes);
   $SQL  = "select tblChatterBox.wzUserID from tblChatterBox  ";
   $SQL .= "left join tblwzOnline  on tblwzOnline.wzUserID = tblChatterBox.wzUserID where channel = ".$chan;
   $SQL .= " and (inChatChanID is null or NOT inChatChanID = ".$chan.")";
   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);
   while($tRec){
     $cUID = $tRec['wzUserID'];
     if ($cUID != $talkerID) {
       $SQL = "Select count(*) as nRec from tblwzUserNotifications  where notifyUID = ".$cUID." and fromChanID = ".$chan." and notified is null";   
       $nresult = mkyMsqry($SQL);
       $nRec = mkyMsFetch($nresult);
       if ($nRec['nRec'] == 0){
         $SQL = "insert into tblwzUserNotifications (notifyUID,fromChanID,whoUID) values (".$cUID.",".$chan.",".$talkerID.")";
         $nresult = mkyMsqry($SQL);
       }
     }
     $tRec = mkyMsFetch($result);
   }
   $cUID = $ownerID;
   if ($cUID != $talkerID) {
     $SQL = "Select count(*) as nRec from tblwzUserNotifications  where notifyUID = ".$cUID." and fromChanID = ".$chan." and notified is null";   
     $nresult = mkyMsqry($SQL);
     $nRec = mkyMsFetch($nresult);
     if ($nRec['nRec'] == 0){
       $SQL = "insert into tblwzUserNotifications (notifyUID,fromChanID,whoUID) values (".$cUID.",".$chan.",".$talkerID.")";
       $nresult = mkyMsqry($SQL);
     }
   }
}

function cleanChat($msg){
 return $msg;
 //return preg_replace("/(..)\\1+/", "$1", $msg);
}
function respondAsMonkey($channel,$talkerID,$msg,$msgID){
  global $hutOID;    
  $SQL = "select nicNoEmo from tblwzUser where wzUserID = ".$talkerID;
  $ures = mkyMyqry($SQL);
  $urec = mkyMyFetch($ures);
  $atname = null;
  $uname  = 'nouser';
  $callToUID = $talkerID;
  if ($urec){
    $uname  = $urec['nicNoEmo'];	  
    $atname = '@'.$urec['nicNoEmo'].' - ';
  }
  $userID = 63555;   
  $digiHut = safeGET('digiHut');
  if ($digiHut){
    $digiHut = '&digiHut='.urlencode($digiHut);
  }

/*
  $ts = checkAITokenSuply($talkerID);
  if ($ts->bal <= 0){
    $mkyresp = "Sorry But You Are Out Of BMGP Tokens... I might work for bananas but they are not free!
    You will have to earn some more or buy some on the GJEX.";
  }
  else {
 */ 
    $data = '?chanID='.$channel.'&UID='.$talkerID.'&msg='.urlencode($msg).'&uname='.urlencode($uname).$digiHut; 
    $ores = tryFetchURL('https://www.bitmonky.com/whzon/talk/oaiSiteMonky.php'.$data);
    $j = json_decode($ores,false,512,JSON_INVALID_UTF8_SUBSTITUTE);
    $mkyresp = null;
    gfbug('ores:'.$ores);
    if ($j){
      if ($j->result){
        $mkyresp = $j->data;
      }
    }
//  }  
  if ($mkyresp){
    $shout = $atname.$mkyresp;    
    gfbug('shout:'.$shout);
    if ($shout!="") {
      $cshout=clean($shout);
      $cshout=left($cshout,850);
     
      $preMsg=null;
      $SQL = "select top 1 msg from tblChatterBox  where wzUserID=".$userID." order by msgID desc";
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      if ($tRec) {
        $preMsg=$tRec['msg'];
      }

      if (!is_null($preMsg)) {
        if ($cshout==$preMsg) {
          $cshout="";
        }
      }

      if ($cshout!=""){
        $timestamp=mkySQLDstamp();
        $SQL = "insert into tblChatterBox (wzUserID,msg,channel,cDate,callToUID,cboxHutOID)";
        $SQL .= " values(".$userID.",'".$cshout."',".$channel.",'".$timestamp."',".$callToUID.",".$hutOID.")";
        gfbug('sql::'.$SQL);
        $result = mkyMsqry($SQL);

        $SQL = "select msgID from tblChatterBox  where wzUserID=".$userID." and channel=".$channel." and cDate='".$timestamp."'";
        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);
        if ($tRec) {
          $msgID=$tRec['msgID'];
        }
        else {
          $msgID=0;
        }
        $SQL = "update tblChatChannel set rdate='".$timestamp."' where channelID=".$channel;
        $result = mkyMsqry($SQL);

        $SQL = "update tblChanHistory set nChats = nChats +1 where chChanID=".$channel." and chUID=".$userID;
        $result = mkyMsqry($SQL);

        $SQL = "insert into tblChatterBuf (wzUserID,msg,channel,realID,cboxHutOID)";
        $SQL .= " values(".$userID.",N'".$cshout."',".$channel.",".$msgID.",".$hutOID.")";
	$result = mkyMsqry($SQL);

        if ($channel != 1 ){
          $SQL = "update tblChatChannel set  nPosts = 0 where nPosts is null and channelID=".$channel;
          $result = mkyMsqry($SQL);

          $SQL = "update tblChatChannel set  last24hrs = 0 where last24hrs is null and channelID=".$channel;
          $result = mkyMsqry($SQL);
        }

        $SQL = "update tblChatChannel set lastpost = now(), nPosts = nPosts + 1, last24hrs = last24hrs + 1 where channelID=".$channel;
        $result = mkyMsqry($SQL);
	sendNotifications($channel,$talkerID,$GLOBALS['ownerID'],$cshout,$msgID,$mkyRes=true);
      }
    }
  }
}
?>

