<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

include_once('../sessMgrWC.php');
$ircCache = new stdClass;
$ircCache->cuser = $cuserID;
$chanID = cacheGetPINT('fchanID');
$wzID   = cacheSafeGET('wzID');
$lmsgID = cacheGetPINT('flmsgID');
//$cuser  = cacheGetPINT('fcu');
$irc = new ircCacheOBJ($ircCache,$chanID,$lmsgID,$wzID,$cuserID,$cmkdID);

function cacheSafeGET($fld){
  if (!isset($_GET[$fld])){
    return null;
  }
  $id = $_GET[$fld];
  //if(preg_match("/^[0-9]+$/", $id)) {
    return addslashes($id);
  //}
  //else {
  //  return null;
  //}
}

function cacheGetPINT($fld){
  if (!isset($_GET[$fld])){
    return null;
  }
  $id = $_GET[$fld];
  if(preg_match("/^[0-9]+$/", $id)) {
    return addslashes($id);
  }
  else {
    return null;
  }
}

class ircCacheOBJ {
   private $fcache;   // local cache file filename
   private $cache;    // local cache buffer
   private $source;   // database or cache;
   private $refresh;  // max time in seconds before reloading cache
   private $chanID;
   private $lmsgID;  
   private $wzID;
   private $userID; 
   private $mkdID;
  

   function __construct($c,$chanID,$lmsgID,$wzID,$cuser,$cmkdID,$rt=20){
     $this->refresh = $rt;
     $this->cache   = $c;
     $this->fcache  = $_SERVER['DOCUMENT_ROOT'].'wzAdmin/mbrChat_'.$chanID.'_cache.tmp';
     $this->chanID  = $chanID;
     $this->wzID    = $wzID;
     $this->lmsgID  = $lmsgID;
     $this->userID  = $cuser;
     $this->mkdID   = $cmkdID;
     $this->dateChan = 302057;
   }

   public function checkCache(){
     if (!$this->chanID || $this->userID === null){
       return null;
     }
     if (file_exists($this->fcache)) {
       $ct = microtime(true) - filemtime($this->fcache);
       if ($this->refresh < $ct) {
         return null;
       }
       $myfile = fopen($this->fcache, "r");
       if (filesize($this->fcache) < 1){
         fclose($myfile);
         return null;
       }
       $contents = fread($myfile,filesize($this->fcache));
       fclose($myfile);
       if ($contents === false){
         return null;
       }
       $this->cache = json_decode($contents);
       $try = 0;
       while($this->cache === null || json_last_error() !== JSON_ERROR_NONE) {
         if ($try > 3){
           return null;
         }
         $try = $try + 1;
         usleep(1000000);
         $myfile = fopen($this->fcache, "r");
         $contents = fread($myfile,filesize($this->fcache));
         fclose($myfile);
         $this->cache = json_decode($contents);
       }
       return $ct;
     }
     return null;
   }
   public function writeCache($c){
     if (!$this->chanID || $this->userID === null){
       //return;
     }
     $this->cache = $c;
     $txt = $this->cache;
     $myfile = fopen($this->fcache, "w");
     if ($myfile){
       if (flock($myfile, LOCK_EX)) {
         fwrite($myfile, $txt);
         flock($myfile,LOCK_UN);
       }
       fclose($myfile);
     }
   }
   public function sendFromCache($decode=null){
     if($decode){
       $this->cache = json_decode($this->cache);
     }
     header('Content-Type: application/json');
     echo '{"myMsgs" : ['; 
     //var_dump($this->cache);
     $n = 1;
     $coma = null;
     $UID = $this->userID;
     if ($this->chanID == $this->dateChan){
       $UID = $this->mkdID;
     }
     if($this->cache){
       foreach ($this->cache->myMsgs as $stdo){
         if(($stdo->msgID > $this->lmsgID && !($UID == $stdo->crUserID && $stdo->isShare === null)) || !$this->lmsgID){
           echo $coma.mkyStrReplace('@@mkyRepSkey@@',$this->wzID,json_encode($stdo));
           $n = $n + 1;
           $coma = ',';
         }
       }
     }
     echo ']}';
   }
};
if ($irc->checkCache()){
  $irc->sendFromCache();
  exit();
}

$sessionMode="lite";
include_once("../mkysess.php");
include_once("../JSON.php");
include_once("../utility/acHash.php");
include_once("../gold/goldInc.php");
include_once("../apps/dating/dateInc.php");

//ini_set('display_errors',1);
//error_reporting(E_ALL);


if (isset($_GET['adNbr'])){$adCnt  = safeGetINT('adNbr');} else {$adCnt = 0;}
$wzID = $sKey;
$sKey = '@@mkyRepSkey@@';
    
    $wzUserID = $userID;
    if($userID != 0){
      $channel = $inChatChanID;
    }
    
    $inChannel = safeGetINT('fchanID');
    if ($inChannel != "" && $inChannel != "none") {
      $channel = $inChannel;
    }
    else {
      $channel = 1;
    }
    
    if (!$channel){
      $channel = 1;
    }

    if (isSet($_GET['flmsgID'])){$lastMsgID = safeGetINT('flmsgID');} else {$lastMsgID = 0;}
    if (isSet($_GET['fnrec']))  {$nDispRec  = safeGetINT('fnrec');}   else {$nDispRec = 35;}
    if (isSet($_GET['fimg']))   {$imgSize   = clean($_GET['fimg']);}   else {$imgSize='normal';}
    
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
 
    if ($lastMsgID=="") {
      $lastMsgID=0;
    }

    if (!$channel){ 
      $channel = 1;
    }
	
    $isStore = null;
    $SQL = "SELECT ownerID,chanStoreID from tblChatChannel  where channelID=".$channel;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $isOwner=False;
    if ($tRec){
      $isStore = $tRec['chanStoreID'];
      $chanOwnerID=$tRec['ownerID'];
      if (is_null($chanOwnerID)) {
        $chanOwnerID=1;
      }
      if ($chanOwnerID == $wzUserID) {
        $isOwner=True;
      }
    }

    if (fmod($adCnt,3) == 0 && !$isStore ) {
      header('Location: /whzon/adMgr/srvAdInc.php?wzID='.$wzID.'&fwzUserID=0');
    }

    $SQL = "SELECT count(*) as nRecs from tblChatterBuf  where channel=0 or channel=".$channel;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $nrecs= $tRec['nRecs'];

    //if ($nrecs < $nDispRec )
    $cTable = "tblChatterBox";
    //else
    //$cTable= "tblChatterBuf";

    $lastMsgID = 0;

    if ($lastMsgID==0){
      $SQL = "SELECT  * from ".$cTable."  where channel=0 or channel=".$channel." order by cDate desc limit ".($nDispRec +1);

      $result = mkyMsqry($SQL);
      while($tRec = mkyMsFetch($result)){
        if ($cTable=="tblChatterBox"){
          $lastMsgID=$tRec['msgID'];
        }
        else{
          $lastMsgID=$tRec['realID'];
        }
      }
      if ($channel == $dateAppChan){
        $SQL = "SELECT urlShareImg,urlShareLink,videoID,video,isVshare, moshPitID as inMosh,tblwzUser.inChatChanID, wzOnlineID, msgID, ";
        $SQL .= "TIMESTAMPDIFF(second,cDate,now())elt,tblwzUser.moderator, tblwzUser.sex, tblwzUser.age, wzOnlineID as online, ";
        $SQL .= "mkdID as wzUserID, tblwzUser.country, tblCity.countryID, mkdNic as firstname, msg,tblCity.name,cast(isShare as integer)isShare ";
        $SQL .= "from tblChatterBox  ";
        $SQL .= "inner join tblMkyDating  on mkdUID = tblChatterBox.wzUserID ";
        $SQL .= "left Join tblMoshUsers  on tblMoshUsers.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "left Join tblwzOnline  on tblwzOnline.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "inner Join tblwzUser  on tblwzUser.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "left Join tblCity  on tblCity.cityID = tblwzUser.cityID ";
        $SQL .= "where sandBox is null and msgID >= ".$lastMsgID." and ((channel=0 and TIMESTAMPDIFF(minute,cDate,now()) < 5) or channel=".$channel.") order by cDate";
      }
      else {
        $SQL = "SELECT urlShareImg,urlShareLink,videoID,video,isVshare, moshPitID as inMosh,tblwzUser.inChatChanID, wzOnlineID, msgID, ";
        $SQL .= "TIMESTAMPDIFF(second,cDate,now())elt,tblwzUser.moderator, tblwzUser.sex, tblwzUser.age, 1 as online, ";
        $SQL .= "tblChatterBox.wzUserID, tblwzUser.country, tblCity.countryID, tblwzUser.firstname, msg,tblCity.name,cast(isShare as integer)isShare ";
        $SQL .= "from tblChatterBox  ";
        $SQL .= "left Join tblMoshUsers  on tblMoshUsers.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "left Join tblwzOnline  on tblwzOnline.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "inner Join tblwzUser  on tblwzUser.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "left Join tblCity  on tblCity.cityID = tblwzOnline.cityID ";
        $SQL .= "where msgID >= ".$lastMsgID." and ((channel=0 and TIMESTAMPDIFF(minute,cDate,now()) < 5) or channel=".$channel.") order by cDate";
      }
    }
    else{
      if ($channel == $dateAppChan){
        $SQL = "SELECT urlShareImg,urlShareLink,videoID,video,isVshare, moshPitID as inMosh, tblwzUser.inChatChanID, wzOnlineID, msgID, ";
        $SQL .= "TIMESTAMPDIFF(second,cDate,now())elt,tblwzUser.moderator, tblwzUser.sex, tblwzUser.age, 1 as online, ";
        $SQL .= "mkdID as wzUserID, tblwzUser.country,tblCity.countryID,mkdNic as firstname, msg,tblCity.name from tblChatterBox  ";
        $SQL .= "inner join tblMkyDating  on mkdUID = tblChatterBox.wzUserID ";
        $SQL .= "left Join tblMoshUsers  on tblMoshUsers.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "inner Join tblwzUser  on tblwzUser.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "left Join tblwzOnline  on tblwzOnline.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "left Join tblCity  on tblCity.cityID = tblwzUser.cityID ";
        $SQL .= "where sandBox is null and ((not tblChatterBox.wzUserID=".$wzUserID." )  or Not isShare is null or ";
        $SQL .= "Not cbxStoreItemID is null) and msgID > ".$lastMsgID." and ((channel=0 and TIMESTAMPDIFF(minute,cDate,now()) < 5) or channel=".$channel.") order by cDate";
      }
      else { 
        $SQL = "SELECT urlShareImg,urlShareLink,videoID,video,isVshare, moshPitID as inMosh, tblwzUser.inChatChanID, wzOnlineID, msgID, ";
        $SQL .= "TIMESTAMPDIFF(second,cDate,now())elt,tblwzUser.moderator, tblwzUser.sex, tblwzUser.age, 1 as online, ";
        $SQL .= "tblChatterBox.wzUserID, tblwzUser.country,tblCity.countryID,tblwzUser.firstname, msg,tblCity.name from tblChatterBox  ";
        $SQL .= "left Join tblMoshUsers  on tblMoshUsers.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "left Join tblwzOnline  on tblwzOnline.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "inner Join tblwzUser  on tblwzUser.wzUserID=tblChatterBox.wzUserID ";
        $SQL .= "left Join tblCity  on tblCity.cityID = tblwzOnline.cityID ";
        $SQL .= "where ((not tblChatterBox.wzUserID=".$wzUserID." )  or Not isShare is null or ";
        $SQL .= "Not cbxStoreItemID is null) and msgID > ".$lastMsgID." and ((channel=0 and TIMESTAMPDIFF(minute,cDate,now()) < 5) or channel=".$channel.") order by cDate";
      }
    } 
    
if ($userID == 17621){
  //echo $SQL;
}
   
    $SQL= mkyStrReplace("tblChatterBox",$cTable,$SQL);
    if ($cTable== "tblChatterBuf"){
      $SQL=mkyStrReplace("msgID,","realID as msgID,",$SQL);
      $SQL=mkyStrReplace("and msgID","and realID",$SQL);
      $SQL=mkyStrReplace("where msgID","where realID",$SQL);
      $SQL=mkyStrReplace("where ","where NOT photoIDApproved is null and ",$SQL);
    }
//    if ($userID == 17621){
//      $SQL=mkyStrReplace("where ","where NOT photoIDApproved is null and ",$SQL);
//      echo $SQL;
//    }
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

	if (!$tRec){
	   exit('norecs{"myMsgs":[]}');
	}
  
    $n = 1;
    $j = new stdClass;
    $j->myMsgs = [];

     
    while($tRec){
      if ($n == 1){$coma = '';} else {$coma = ',';}
      $n = $n +1;
    
      $wzOnlineID = $tRec['wzOnlineID'];
      $crUserID   = $tRec['wzUserID'];
      $inChan     = $tRec['inChatChanID'];
      $inMosh     = $tRec['inMosh'];
      $isVshare   = $tRec['isVshare'];
      $video      = $tRec['video'];
      $msgID      = $tRec['msgID'];
      $videoID    = $tRec['videoID'];
      $cityName   = $tRec['name'];
      $urlImg     = $tRec['urlShareImg'];
      $urlLink    = $tRec['urlShareLink'];
      $firstname  = $tRec['firstname'];
      $elapsTime  = $tRec['elt'];
      $isShare    = $tRec['isShare'];
  	  
      if ($channel == $dateAppChan){
        $SQL = "select claimStatus,null as mbrAmgImage,null as mbrAmgTxtCol,totalRCADs,storeFront,curentStatus,mbrTitle,photoIDApproved,";
        $SQL .= "TIMESTAMPDIFF(hour,creatDate,now()) newb ";
        $SQL .= "from tblwzUser  ";
        $SQL .= "inner join tblMkyDating  on mkdUID = wzUserID ";
        $SQL .= "where mkdID = ".$crUserID;
      }
      else {
        $SQL = "select claimStatus,mbrAmgImage,mbrAmgTxtCol,totalRCADs,storeFront,curentStatus,mbrTitle,photoIDApproved,";
        $SQL .= "TIMESTAMPDIFF(hour,creatDate,now()) newb ";
        $SQL .= "from tblwzUser  where wzUserID = ".$crUserID;
      }
      $uresult = mkyMsqry($SQL);
      $uRec = mkyMsFetch($uresult);
      $storeFront = null;
      $rCADs = null;
      $spamStatus = null;
      if ($uRec){
        $mbrAmgTxt  = $uRec['mbrAmgTxtCol'];
        $mbrAmgImg  = $uRec['mbrAmgImage'];
        $rCADs      = $uRec['totalRCADs'];
        $mTitle     = $uRec['mbrTitle'];
        $storeFront = $uRec['storeFront'];
        $ustatus    = $uRec['curentStatus'];
        $spamStatus = $uRec['photoIDApproved'];
        $newb       = $uRec['newb'];
        if ($spamStatus){
          $spamStatus = "<b>Mbr Status:</b> Verified";
        }
        else {
          if ($newb < 24){
           $spamStatus = "<b>Mbr Status:</b> NEW ";
          }
          else {
           $spamStatus = "<b>Mbr Status:</b> Unverified";
           if ($uRec['claimStatus']){
             $spamStatus = "<b>Mbr Status:</b> Recommended";
           }
          }
        }
        if ($ustatus == ''){
          $ustatus = null;
        }
      }
      else {
        $mTitle  = null;
        $ustatus = null;
      }
      $gotoChan="";

      $deleteMsg="";
      if ($isOwner){
        $deleteMsg=" - <a href=\"javascript:parent.ownerRemoveChatMsg(".$msgID.",'".$sKey."');\">Remove</a>";
      }
      $gotoMosh="";
      if(!is_null($inMosh)){ 
        $gotoMosh  = "<img title='Listening to music! Click to join them' alt='Listening to music! Click to join them' ";
        $gotoMosh .= "onclick='OpenGigBox(".$inMosh.");' ";
        $gotoMosh .= "style='border-radius:50%;vertical-align:middle;margin-top:1.5px;margin-left:5px;height:20px;width:20px;'";
        $gotoMosh .= " src='//image.bitmonky.com/img/inMoshBox.png'>";
      }
      if (is_null($inChan)){ 
        if (!is_null($wzOnlineID)) {
          $gotoChan=" <span style='font-size:11px;'>- (stepped out)</span>";
          $gotoChan="";
        }
      }
      else{
        if ($inChan!=$channel) {
          $SQL= "select  left(name,30) as name from tblChatChannel  where isMod is null and channelID=".$inChan;
          $resultCR = mkyMsqry($SQL);
          $crRec = mkyMsFetch($resultCR);
          If ($crRec){
            $goToChan  = "<div style='margin-top:2px;font-size:11px;color:brown;'>Moved To Room --> ";
	    $goToChan .= "<a alt='Change to channel - ". mkyTrim($crRec['name'])."style='font-size:11px;' ";
	    $goToChan .= "target='_top' href='/whozon/talk/changeTalkChannel.asp?fqry=&newPg=0&fchan=".$inChan."'>";
            $goToChan .= mkyTrim($crRec['name'])."</a></div>";
          }
        }
      }

      $crRec = null;
      if ($inChatChanID == $dateAppChan){
        $SQL  = "select  sandBox,franchise, moderator, sex, age, mkdNic as firstname, country,countryID, crown,city from tblwzUser  ";
        $SQL .= "inner join tblMkyDating on mkdUID = wzUserID ";
        $SQL .= "where mkdID = ".$crUserID;
      }
      else {
        $SQL= "select  sandBox,franchise, moderator, sex, age, firstname, country,countryID, crown,city from tblwzUser  where wzUserID=".$crUserID;
      }
      $resultCR = mkyMsqry($SQL);
      $crRec = mkyMsFetch($resultCR);
      $inJail = $crRec['sandBox'];

      if (!$inJail){
        $mbrIsFranchise = $crRec['franchise']; 
        $crown = $crRec['crown'];
        $wzMod = 0;
        $firstname = null;
        if (is_null($wzOnlineID)){
          $online=0;
          if ($crRec){
            $firstname = stripToAlphaNum($crRec['firstname']);
            $age       = $crRec['age'];
            $sex       = $crRec['sex'];
            $wzMod     = $crRec['moderator'];
	    $country   = "<a alt='View ".$crRec['country']." Guide' href=javascript:parent.wzGetPage('/whzon/mytown/myTown.php?wzID=".$sKey;
	    $country  .= "&fwzUserID=".$crUserID."&fscope=myCountry&fmyMode=mbrs');>".$crRec['country']."</a>";
	    $city      = "<a  alt='View ".$crRec['city']." Guide' ";
	    $city     .= "href=javascript:parent.wzGetPage('/whzon/mytown/myTownProfile.php?wzID=".$sKey;
	    $city     .= "&fwzUserID=".$crUserID."&fscope=myCity&fmyMode=mbrs');>".$crRec['city']."</a>";
            $crown     = $crRec['crown'];
            $countryID = $crRec['countryID'];
          }
        }  
        else{
          $online=1;
          $firstname = stripToAlphaNum($crRec['firstname']);
          $age = $crRec['age'];
          $sex = $crRec['sex'];
          $wzMod=$crRec['moderator'];
	  $country   = "<a  alt='View ".$crRec['country']." Guide' ";
	  $country  .= "href=javascript:parent.wzGetPage('/whzon/mytown/myTownProfile.php?wzID=".$sKey;
	  $country  .= "&fwzUserID=".$crUserID."&fscope=myCountry&fmyMode=mbrs');>".$crRec['country']."</a>";
	  $city      = "<a alt='View ".$crRec['city']." Guide' ";
	  $city     .= "href=javascript:parent.wzGetPage('/whzon/mytown/myTownProfile.php?wzID=".$sKey;
	  $city     .= "&fwzUserID=".$crUserID."&fscope=myCity&fmyMode=mbrs');>".$cityName."</a>";
          $countryID = $crRec['countryID'];
        }

        if (!is_null($sex)){
          if ($sex == 3){
            $sex = "<span style='font-size:small;'> YouTuber </span>";
            $age = "";
          }
	  else if ($sex == 3){
            $sex = "<span style='font-size:small;'> Business Account </span>";
            $age = "";
          }
          else if ($sex == 1 ){
            $sex = " f - ";
          }
          else{
            $sex = " m - ";
          }
        }   
        if (is_null($wzMod)){
          $wzMod=0;
        }
      
        $admin="";
        $msg=$tRec['msg'];
        $rawmsg = $msg;
        //$msg = urldecode($msg);
        $msg = mkyStrReplace('image.monkytalk','image.bitmonky',$msg);
        $msg = mkyStrReplace('image.bitmonky','image.bitmonky',$msg);
        $msg = mkyStrReplace('border:0px solid #777777;','',$msg);    
        $msg = mkyStrIReplace('//image.bitmonky.com','//image.bitmonky.com',$msg);
        if (mkyStripos($msg,'//image.bitmonky.com') === false){
 	  formatHashTagsNUPS($msg,1);
        }
        //$msg = urldecode($msg);
        $msg=mkyStrReplace("broadIcon.jpg","broadcast.jpg",$msg);
        if ($isVshare == 1 || $isVshare == 2 ){
          $img = $video;
          $img = preg_replace('/.*youtube.com/i','youtube.com',$img);
          $img = mkyStrIReplace("youtube.com/v","https://i2.ytimg.com/vi",$img);
          $img = $img."/default.jpg";
          $sharetxt = $msg;
          $shareApp = "vshare('".$video."');";
          if ($isVshare == 2) {
            $shareApp = "videoShare(".$videoID.");";
          }
          $msg = "<a href=javascript:parent.".$shareApp.">";
          $msg .= "<img 'alt='Video share ".$rawmsg."' style='float:right;margin-left:6px;width:110px;height:80px;border-radius:0.4em;' ";
          $msg .= "title='View Video' src='".$img."'></a>";
          $msg .= "<b>has shared this video</b><br>".$sharetxt."";
        }
        if ($isVshare == 3){
          $img  = '//image.bitmonky.com/getmBlogTmn.php?id='.$videoID;
          $sharetxt = $msg;
          $msg = "<div style='width:99%;'><a href=javascript:parent.wzGetPage('/whzon/mbr/blog/mbrMBLOG.php?wzID=".$sKey."&fwzUserID=".$crUserID."&fTopicID=".$video."#mb".$videoID."');>";
          $msg .= "<img style='vertical-align:top;float:right;margin-left:6px;width:110px;height:80px;border-radius:0.4em; border:0px solid #777777;' ";
          $msg .= "src='".$img."'></a>";
          $msg .= "<b>has shared this blog entry</b><br>".$sharetxt."</div>";
          $msg = mkyStrReplace("'","\"",$msg);
        }
        if ($isVshare == 4){
          $img = "//image.bitmonky.com/getNShareImg.php?id=".$videoID;
          $sharetxt = $msg;
          $msg = "<div style='width:99%;'><a href=javascript:parent.wzGetPage('/whzon/mbr/mbrViewWNewsShare.php?wzID=".$sKey."&newsID=".$videoID."');>";
          $msg .= "<img style='vertical-align:top;float:right;margin-left:6px;width:110px;height:80px;border-radius:0.4em; border:0px solid #777777;' ";
          $msg .= "src='".$img."'></a>";
          $msg .= "<b>has shared this Link</b><br>".$sharetxt."</div>";
          $msg = mkyStrReplace("'","\"",$msg);
        }
        if ($isVshare == 5 || $isVshare == 6 || $isVshare == 7){
          $img = getLSImage($videoID);
          $msgHed = "Has Started A Live Stream";
          if ($isVshare == 6) {$msgHed = "Has Started A Live Stream Comment ";}
          if ($isVshare == 7) {$msgHed = "Has Started A Live Stream Reply ";}
          $sharetxt = $msg;
          $msg = "<div style='width:99%;'><a href=javascript:parent.wzGetVideoPage('/whzon/live/chan/chanLiveStreams.php?wzID=".$sKey."&videoID=".$videoID."');>";
          $msg .= "<img style='vertical-align:top;float:right;margin-left:6px;width:110px;height:80px;border-radius:0.4em; border:0px solid #777777;' ";
          $msg .= "src='".$img."'></a>";
          $msg .= "<b/>".$msgHed."</b><br>".$sharetxt."</div>";
          $msg = mkyStrReplace("'","\"",$msg);
        }
        $orgmsg = $msg;
        if (!$isVshare){
          $msg = "<div style='margin:0.5em;border-radius:1em;background:#333333;padding:1em;font-size:larger;color:white;'>".$msg."<br clear='right'/></div>";
        }
        if ($mbrAmgImg && !$mbrMCBckOff){
          $msg = "<div style='margin:0.5em;border-radius:1em;background-color:rgba(0, 0, 0, 0.7);padding:1em;font-size:larger;color:white;'>".$orgmsg."<br clear='right'/></div>";
        }

        //$msg=mkyStrReplace("^"," ",$msg);

        if ($wzMod==2){
          $admin="<img src='//image.bitmonky.com/img/goldStar.png' height='15' width='15' style='vertical-align:middle; margin:0px;border:0px;' title='System Admin' alt='System Admin'>";
        }
        if ($mbrIsFranchise != null) {
          $crown = ""; // get rid of for now
          $admin="<a href=javascript:parent.wzGetPage('/whzon/franMgr/franMgr.php?mode=reRead&wzID=".$sKey."');><img src='//image.bitmonky.com/img/tycoonIcon".$crown.".png' height='22' width='22' style='border-radius: .4em;vertical-align:middle; margin:3px 3px 0px 3px;' title='Media Tycoon' alt='Media Tycoon'/></a>";
        }

	mb_detect_encoding($msg, mb_detect_order(), true) === 'UTF-8' ? $msg : mb_convert_encoding($msg, 'UTF-8');
        //$msg = urldec      
        //mb_convert_encoding($value, 'UCS-2LE', mb_detect_encoding($value, mb_detect_order(), true)); 
      
        if ($channel == $dateAppChan){
          $crown = "";
          $admin = "";
          $gotoMosh = "";
          $ustatus = null;
          $rCADs   = null;
          $mTitle  = null;
          $storeFront = null;
        }
        $amgImg = getAmgImg($mbrAmgImg); //'/img/'.$mbrAmgImg;
        $amg = null;
        $amgend = null;
        if ($mbrAmgImg && !$mbrMCBckOff){ //$crUserID==17621){
          $amg = "<div id='animate-area' style='padding:0em;background-image: url(".$amgImg.");'><div class='amgFilter' style='padding:0em;'>";
          $amgend = "</div><div>";
          if ($mbrAmgTxt){
            $mbrAmgTxt = 'color:'.$mbrAmgTxt.';';
          }
	}
	$mAdj = 'gsoftADJ';
        $jmsg  = "<div class='chatCard' style='".$mbrAmgTxt."width:calc(100%".$mAdj.");margin-top:10px;padding:6px;border-radius:.5em;'>";
        $jmsg .= $amg."<table style='width:100%;' ID='cmsgEL".$msgID."' ";
	$jmsg .= "oncontextmenu=\"javascript:wzGetPage('/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$crUserID."');return false;\" ";
        $jmsg .= "onmouseover='highlightChatMsg(".$msgID.");' onmouseout='normalizeChatMsg();' style='width:calc(100% - 0px);";
        $jmsg .= "border-collapse:collapse;border:0px solid;'><tr valign='top'><td style='padding:0em;'>";
        $jmsg .= "<div style='border-radius:.5em .5em 0em 0em;width:100%;background-color: rgba(10,10,10,.70);'>";
	$jmsg .= "<a data='View ".$firstname."`s Profile' href=javascript:sayToMember('".stripEmotes($firstname)."',".$msgID.");>";
        if ($channel == $dateAppChan){
          $jmsg .= getDProfThm($crUserID,3);
        }
        else {
	  $jmsg .= "<img title='Right Click To View ".$firstname."`s Profile' style='float:left;width:".$iw.";height:".$ih;
  	  $jmsg .= ";margin:3px;margin-right:6px;border-radius:50%;";
          $jmsg .= "border: 0px solid #dddddd;' src='//image.bitmonky.com/".$imgfile."?id=".$crUserID."'>";
        }
        $jmsg .= "</a>";
        $jmsg .= "";
        $jmsg .= "<a style='font-size:12px;' data='View ".$firstname."`s Profile' alt='View Profile'  ";
        $jmsg .= "href=javascript:parent.wzGetPage('/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$crUserID."');>";
        $jmsg .= left(mkyTrim($firstname),30)."</a>".$admin."  ".$gotoMosh;

        if ($userID!=0){
          if ($online==1 &&  $userID!=$crUserID) {
            $jmsg .= "<a href=javascript:parent.wzPopChat('".$crUserID."');>";
	    $jmsg .= "<img alt='Start Private Chat Icon' title='Start Private Chat with ".$firstname."' ";
	    $jmsg .= "style='border:0px;margin-top:2px;width:20px;height:13px' ";
	    $jmsg .= "src='//image.bitmonky.com/img/chatAlertBG.png'></a>";
          }
	  else {
            if ($userID!=$crUserID) {
              $jmsg .= "<b style='font-size:11px;'>(Offline)";
	      $jmsg .= "<a data='Send ".$firstname." an email' ";
	      $jmsg .= "href=\"javascript:wzGetPage('/whzon/mbr/mail/sendMailForm.php?wzID=".$sKey."&fwzUserID=".$crUserID."');\">";
	      $jmsg .= "<img alt='User Email Icon' style='margin-left:.5em;vertical-align:middle;border:0px;height:23px;width:23px;' ";
	      $jmsg .= "src='//image.bitmonky.com/img/iconEmail.png'></a></b>";
            }
          }
        }
        $rpPhoto = 'getPhotoImg.php?fpv='.$crUserID.'&id=';
        $msg = mkyStrReplace('getPhotoTmn.php?id=',$rpPhoto,$msg);
        $msg = mkyStrIReplace(' src=', ' ID=\'shareImg'.$msgID.'\' onerror=\'adImgFail("shareImg'.$msgID.'",1);\' src=',$msg); 
        
        $jmsg .= "<a href='javascript:parent.zoomChannelConversation(".$crUserID.",".$channel.");'><img ID='zimgEL".$msgID."' ";
        $jmsg .= "alt='zoom in icon' Title='zoom in' src='//image.bitmonky.com/img/iconZoom.png' ";
        $jmsg .= "style='border-radius:50%;float:right;margin:1px;height:24px;width:24px;visibility:hidden;'></a>";
        $jmsg .= $sex." ".$age."<br/>".$city.", ".$country;
        $jmsg .= "<br clear='right'/><br clear='left'/></div><div style='margin-top:9px;width:100%;'>".$msg.$gotoChan.$deleteMsg."<br clear='right'/></div>";
        $jmsg .= "<div style='margin-top:1em;padding:.5em;;background-color: rgba(10,10,10,.70);'>"; //background:#333333;'>";
      
        if ($ustatus){
          $jmsg .= "<b>Status: </b>".makeClickableLinksSmall($ustatus);
        }
        //if ($rCADs && $crUserID != 63555){
	//  $jmsg .= "<br/><b>Successfull Withdraws: </b>$".mkyNumFormat($rCADs,2)." CAD";
        //}
        if ($mTitle){
	  $jmsg .= "<div align='right' style='margin-top:12px;font-size:smaller;'><b>Title: </b>".$mTitle."</div>";
        }
        if ($spamStatus){
	  	
	  $mtop = 1;
          if(!$mTitle){
            $mtop = 12;
          }
          $jmsg .= "<div align='right' style='margin-top:".$mtop."px;font-size:smaller;'>".$spamStatus."</div>";
        }
        if ($crUserID == 63555 || $crUserID == 471104){
          $jmsg .= "<div align='right' style='margin-top:6px;font-size:smaller;'><a style='font-size:smaller;' ";
          $jmsg .= "href='javascript:wzGetPage(\"/whzon/monkytube/frmSubmitVideo.php?wzID=".$sKey."\");'>Submit Videos Here</a> | ";
          $jmsg .= "<a  style='font-size:smaller;' href='https://youtube.com/channel/UCS2_0BMYStZlyyBMKEm4EoA'>Subscribe</a> | ";
	  $jmsg .= "<a  style='font-size:smaller;' ";
          $jmsg .= "href='javascript:wzGetPage(\"/whzon/mbr/vidView/viewVideoPg.php?wzID=".$sKey."&videoID=".getMkyTubeVotePg()."\");'>Vote</a> </div>";
        }
        if ($storeFront){
          $jmsg .= "<div align='right' style='margin-top:6px;font-size:smaller;'> ";
          $jmsg .= "<a  style='font-size:smaller;' ";
          $jmsg .= "href='javascript:wzGetPage(\"/whzon/store/storeProfile.php?wzID=".$sKey."&fstoreID=".$storeFront."\");'>Visit My Online Store</a> </div>";
        }
	$jmsg .= "</div></td></tr></table>".$amgend."</div>";

        if ($channel == $dateAppChan){
          $jmsg = mkyStrReplace('.videoShare(','.mkdVideoShare(',$jmsg);
          $jmsg = mkyStrReplace('/mbr/','/apps/dating/',$jmsg);
          $jmsg = mkyStrReplace('wzPopChat','wzPopDChat',$jmsg);
        }
	$myMsgs = new stdClass;

	$myMsgs->msgID    = $msgID;
        $myMsgs->crUserID = $crUserID;
        $myMsgs->country  = $countryID;
        $myMsgs->isWinID  = 0;
        $myMsgs->msg      = mkyStrIReplace('image.whzon.com','image.bitmonky.com',$jmsg);
        $myMsgs->msg      = mkyStrIReplace('image.monkytalk.com','image.bitmonky.com',$jmsg);
        $myMsgs->chanID   = $channel;
        $myMsgs->elapsT   = $elapsTime;
        $myMsgs->callID   = chatGetCallerID($rawmsg);
        $myMsgs->rawmsg   = $rawmsg;
        $myMsgs->isShare  = $isShare;
	array_push($j->myMsgs,$myMsgs);  //$j->myMsgs[$n-1] = $myMsgs;
      }//inJail
      $tRec = mkyMsFetch($result);
    }
    $cachEncoded = json_encode($j,JSON_INVALID_UTF8_SUBSTITUTE);  
    //exit(json_last_error_msg()); 
    $irc->writeCache($cachEncoded);
    $decode=true;
    $irc->sendFromCache($decode);


?>

