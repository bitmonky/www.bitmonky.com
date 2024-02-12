<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once('../sessMgrWC.php');

$ircCache = new stdClass;
$chanID = cacheGetPINT('fchanID');

$irc = new ircCacheOBJ($ircCache,$chanID,$cuserID);

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
   private $userID;

   function __construct($c,$chanID,$userID,$rt=60){
     $this->refresh = $rt;
     $this->cache   = $c;
     $this->fcache  = $_SERVER['DOCUMENT_ROOT'].'/wzAdmin/icr_'.$chanID.'_cache.tmp';
     $this->chanID  = $chanID;
     $this->userID  = $userID;
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
   public function writeCache($c,$chanID){
     if (!$this->chanID || $this->userID === null){
       //return;
     }
     $this->fcache  = $_SERVER['DOCUMENT_ROOT'].'/wzAdmin/icr_'.$chanID.'_cache.tmp';
     $this->chanID = $chanID;
     $this->cache = $c;
     $txt = json_encode($this->cache);
     $myfile = fopen($this->fcache, "w");
     if ($myfile){
       if (flock($myfile, LOCK_EX)) {
         fwrite($myfile, $txt);
         flock($myfile,LOCK_UN);
       }
       fclose($myfile);
     }
   }
   private function stripEmotes($s){
     return $s;
     return preg_replace("/[^a-zA-Z0-9]+/", "", $s);
   }
   private function echoDProfThmPad($mkdID,$h,$pad,$r=0.85,$name=''){
     $w = $r * $h;
     echo "<img title='".$name."' alt='".$name."' style='background:white;width:".$w."em;height:".$h."em;border-radius:50%;margin-right:".$pad."em;' ";
     echo "src='https://image.bitmonky.com/getDProfThm.php?id=".$mkdID."'/>";
   }
   public function sendFromCache(){
     echo $this->cache->inWhat;
     foreach ($this->cache->dbrec  as $stdo){
       foreach($stdo as $key => $value){
        $tRec[$key] =  $value;
       }
       // html output data here;
       if ($this->userID != $tRec['wzUserID']){
         if ($this->chanID == $this->cache->dateChanID){
           ?>
           <a href="javascript:wzGetPage('/whzon/apps/dating/mbrProfile.php?wzID=' + sID + '&fwzUserID=<?php echo $tRec['wzUserID'];?>');">
           <?php
           self::echoDProfThmPad($tRec['wzUserID'],2.8,.2,.85,$tRec['firstname']);
           echo "</a>";
         }
         else {
           ?>
	   <a href="javascript:sayToMember('<?php echo self::stripEmotes($tRec['firstname']);?>',null);">
           <img alt='Right Click To View <?php echo $tRec['firstname'];?>`s Profile' title='Right Click To View <?php echo $tRec['firstname'];?>`s Profile'
           style='background:#444444;width:23px;height:28px;border-radius:50%;border: 0px solid #aaaaaa;margin-right:2px;'
	   oncontextmenu="wzGetPage('/whzon/mbr/mbrProfile.php?wzID=' + sID + '&fwzUserID=<?php echo $tRec['wzUserID'];?>');return false;"
           src='//image.bitmonky.com/getMbrTmn.php?id=<?php echo $tRec['wzUserID'];?>' /></a>
           <?php
         }
       }
     }
     echo $this->cache->foot;
   }
};
if ($irc->checkCache()){
  $irc->sendFromCache();
  exit();
}

include_once("../mkysess.php");
include_once("../gold/goldInc.php");
include_once("../apps/dating/dateInc.php");

if ($userID!=0 || 1==1){
  $SQL = "Select chanStoreID from tblChatChannel  where channelID = ".$inChatChanID;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  $inWhat = 'Room';
  $storeID = $tRec['chanStoreID'];
  $action = "<span  style='padding:3px;background:#333333;border-radius: .25em;'>";
  $action .= "<a alt='Link to dating Section' href=\"javascript:wzGetPage('/whzon/apps/dating/appDating.php?wzID=' ";
  $action .= "+ sID + '&fscope=myWorld&fmyMode=mbrs&fwzUserId=' + wzUserID );\">";
  $action .= getTRxt('Find Love')."</a> <img style='width:1em;height:1em;' src='https://image.bitmonky.com/img/iconDating.png'?></span>";
  if ($storeID){
    $inWhat = 'Store';
    $action = "<span  style='padding:3px;background:#333333;border-radius: .25em;'><a href=\"javascript:wzGetPage('/whzon/store/storeProfile.php?wzID=".$sKey."&fstoreID=".$storeID."');\">".getTRxt('View The Store')."</a></span>";
  }

  $ircCache->inWhat =  "<p/><b style='font-size:smaller;'>".getTRxt("In This ".$inWhat." Now")."</b></p>";
  echo $ircCache->inWhat;
  $chanID=$inChatChanID;
  $isMe  =$userID;
    
  if ($isMe==""){
    $isMe=0;
  }
    
  if (!$chanID){
    $chanID=1;
  }
    
  if ($chanID == $dateAppChan){ 
    $SQL  = "select top 9 mkdID as wzUserID, mkdNic as firstname, tblwzOnline.lastAction ";
    $SQL .= "from tblMkyDating  ";
    $SQL .= "inner join tblwzOnline  on tblwzOnline.wzUserID = mkdUID "; 
    $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID = mkdUID ";
    $SQL .= "where sandBox is null and NOT mkdUID=".$userID." and NOT tblwzUser.sex =".$userSex; //." and tblwzOnline.inChatChanID=".$chanID." ";
    $SQL .= " order by photoIDApproved desc, tblwzOnline.lastAction desc,lastOnline desc ";
  }
  else {
    $SQL = "select top 11 tblwzOnline.wzUserID, tblwzOnline.firstname, tblwzOnline.lastAction from ";
    $SQL .= "tblwzOnline  inner join tblwzUser  on tblwzUser.wzUserID = tblwzOnline.wzUserID ";
    $SQL .= "where sandBox is null and NOT tblwzOnline.wzUserID=".$isMe." and tblwzOnline.inChatChanID=".$chanID." ";
    $SQL .= "group by tblwzUser.imgFlg,tblwzOnline.wzUserID, tblwzOnline.firstname, tblwzOnline.lastAction ";
    $SQL .= "order by tblwzUser.imgFlg desc,tblwzOnline.lastAction desc";
  }
  //if ($wzUserID = 17621) { echo $SQL;}
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
    
  if (!$tRec){
    echo "<span style='font-size:smaller;'> - No active users in the ".strtolower($inWhat)." right now...</a>";
  }
  $n = 0;  
  $ircCache->dbrec = [];
  while ($tRec){
    $ircCache->dbrec[$n] = $tRec;
    if ($chanID == $dateAppChan){
      ?>
      <a href="javascript:wzGetPage('/whzon/apps/dating/mbrProfile.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $tRec['wzUserID'];?>');">
      <?php 
      echoDProfThmPad($tRec['wzUserID'],2.8,.2,.85,$tRec['firstname']);
      echo "</a>";
    }
    else {
      ?>
      <a href="javascript:sayToMember('<?php echo stripEmotes($tRec['firstname']);?>',null);">
      <img alt='Right Click To View <?php echo $tRec['firstname'];?>`s Profile' title='Right Click To View <?php echo $tRec['firstname'];?>`s Profile'
      style='background:#444444;width:23px;height:28px;border-radius:50%;border: 0px solid #aaaaaa;margin-right:2px;'
      oncontextmenu="wzGetPage('/whzon/mbr/mbrProfile.php?wzID=' + sID + '&fwzUserID=<?php echo $tRec['wzUserID'];?>');return false;"
      src='//image.bitmonky.com/getMbrTmn.php?id=<?php echo $tRec['wzUserID'];?>' /></a>
      <?php
    }
    $n = $n +1;
    $tRec = mkyMsFetch($result);
  }
  $ircCache->dateChanID = $dateAppChan;

  $jackpot = getDailyBonusAmt();
  $jnum    = mkyStrReplace(",","",$jackpot);
  $jCAD    = mkyNumFormat($jnum * $goldValue,2);
  //tycoonMsgLounge($cityMinMonthPrice);

  $foot = "<div align='right' style='margin-top:0.4em;padding-bottom:8px;'>";
  $foot .= $action." <span  style='padding:3px;background:#333333;border-radius: .25em;'><a alt='Link To Emoji List' href='javascript:showEmoteInfo();'>";
  $foot .= "Emotes <img alt='Emoji Icon' style='vertical-align:middle;border:0px;' src='//image.bitmonky.com/vChat/emoticons/cool.png'></a></span>";
  $foot .= "</div>";

  $foot .= "<div align='right' style='margin:0em 0em 0.35em 0em;'>"; 
  $foot .= "<input type='button' style='background:orange;color:white;padding:.6em;' ";
  $foot .= "onclick='wzGetPage(\"/whzon/franMgr/franMgr.php?mode=reRead&wzID=\" + sID);' Value=' Claim A City '/>"; 
  //$foot .= " <input type='button' style='animation: mkyFlash 5s  infinite;padding:.6em;' value=' Download A NanoBot ' onclick='popImpLoto();'/>";
  if ($userID != 0){
    $foot .= " <input type='button' style='animation: mkyFlash 5s  infinite;padding:.6em;' ";
    $foot .= "value=' Ask Agent SiteMonkey AI ' onclick='sayToMember(\"Agent.SiteMonkey.AI\");'/>";
  }
  echo $foot;
  $ircCache->foot = $foot;
  $irc->writeCache($ircCache,$chanID);
  /*
    <!--
    Yesterdays Winner: <img alt='<?php echo $tRec['firstname'];?>' title='<?php echo $tRec['firstname'];?>
    won <?php echo mkyNumFormat($tRec['bwinAmount']);?> gp' 
    style='vertical-align:top;width:18px;height:24px;border-radius: .25em;border: 0px solid #aaaaaa;margin-left:4px;' 
    src='//image.bitmonky.com/getMbrTnyImg.php?id=<?php echo $tRec['bwinUID'];?>' /></a>
    -->
    </div>
  */
 
}  

?> 
