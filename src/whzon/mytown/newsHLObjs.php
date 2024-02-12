<?php
class mkySess{
   public $sKey;
   public $UID;
   public $isMob;
   function __construct($inKey,$inUserID){
      global $sessISMOBILE;
      $this->sKey = $inKey;
      $this->UID  = $inUserID; 
      $this->isMob = $sessISMOBILE;
   }
};
class mkyImage{
  private $imgType   = null;
  private $imgID     = null;
  private $imgLink   = null;
  private $imgLinkTo = null;

  function __construct($type,$ID,$linkTo=null){
    $this->imgType = $type;
    $this->imgID   = $ID;
    $this->imgLink = null;
    $this->imgLinkTo = $linkTo;
  }
  public function draw($ID,$h,$w,$linkTo=null){
    if ($this->imgID != $ID){
      $this->imgID = $ID;
    }
    if ($this->imgLinkTo != $linkTo){
      $this->imgLinkTo = $linkTo;
    }
    $result = self::getImg();
    if (!$result){
      return;
    }
    if ($this->imgType == 'share'){
      $a = null;
      if ($this->imgLinkTo){
        echo "<a href='javascript:wzLink(\"".$this->imgLinkTo."\");'>";
        $a = '</a>';
      }
      echo "<img onerror='this.style.display=\"none\";' src='".$this->imgLink."' ID='img".$this->imgType.$ID."' style='".$w.$h."'/> ".$a;
    }
  }
  private function getImg(){
    if ($this->imgType == 'share'){
      $SQL = "select urlImgLink,urlImgFlg from newsDirectory.tblUrlShares where urlShareID = ".$this->imgID;
      $vresult = mkyMyqry($SQL);
      $tRec = mkyMyFetch($vresult);
      if (!$tRec){
        return null;
      }
      $this->imgLink = $tRec['urlImgLink'];
      if (mkyStrpos($this->imgLink,'http:') === 0){
        $this->imgLink = "//image.bitmonky.com/getNShareImg.php?id=".$this->imgID;
      }
      return true;
    }
    return null;
  }
};
class mkyScope{
   public  $cur            = null;
   private $mode           = null;
   private $obLoc          = null;
   private $searchStr      = null;
   private $qry            = null;
   private $userSearch     = null;
   private $eventSearch    = null;
   private $classSearchStr = null;
   private $storeSearch    = null;
   private $scope          = null;
   private $scopeID        = null;

   function __construct($inScope,$mode,$cityID){
     $this->cur = $inScope;
     $this->mode = $mode;
     $this->obLoc = new mkyCityLocation($cityID);
     self::setScopeSearch();
   }
   public function getSearch($mode,$cityID){
     $this->obLoc->setLocTo($cityID);
     if ($this->mode !== $mode){
       $this->mode = $mode;
       self::setScopeSearch();
     }
     if ($mode == 'class'){
       return $this->classSearchStr;
     }
     if ($mode == 'web'){
       return $this->searchStr;
     }
     if ($mode == 'store'){
       return $this->storeSearch;
     }
     return $this->eventSearch;
   }
   public function putScopeLinks($sKey){
     echo "Search Location: ";
     if ($this->cur != 'myCity' && $this->cur != 'myState' && $this->cur != 'myCountry'){
       $jCityAnk = "<a href='javascript:wzLink(\"/whzon/mytown/myTownProfile.php?wzID=".$sKey."\");'>";
       echo $jCityAnk."World Wide</a>";
       return;
     }
     if ($this->cur == 'myCity'){
       $jCityAnk = "<a href='javascript:wzLink(\"/whzon/mytown/myTownChangeTo.php?wzID=".$sKey."&fcityID=".$this->obLoc->cityID."\");'>";
       echo $jCityAnk.$this->obLoc->city."</a>";
       $jCityAnk = "<a href='javascript:wzLink(\"/whzon/mytown/myTownChangeTo.php?wzID=".$sKey."&fprovID=".$this->obLoc->stateID."\");'>";
       echo ", ".$jCityAnk.$this->obLoc->state."</a>, ";
     }

     if ($this->cur == 'myState'){
       $jCityAnk = "<a href='javascript:wzLink(\"/whzon/mytown/myTownChangeTo.php?wzID=".$sKey."&fprovID=".$this->obLoc->stateID."\");'>";
       echo $jCityAnk.$this->obLoc->state."</a>, ";
     }

     $jCityAnk = "<a href='javascript:wzLink(\"/whzon/mytown/myTownChangeTo.php?wzID=".$sKey."&fcountryID=".$this->obLoc->countryID."\");'>";
     echo $jCityAnk.$this->obLoc->country."</a>";
   }
   private function setScopeSearch(){
     if ($this->cur == "myCity"){
       $this->scope          = 'city';
       $this->scopeID        = $this->obLoc->cityID;    
       $this->searchStr      = " tblWebsites.cityID=".$this->obLoc->cityID." ";
       $this->userSearch     = " tblwzUserJoins.cityID=".$this->obLoc->cityID." ";
       $this->eventSearch    = " tblCity.cityID=".$this->obLoc->cityID." ";
       $this->classSearchStr = " tblClassifieds.cityID=".$this->obLoc->cityID." ";
       $this->storeSearch    = " tblStore.storeCityID=".$this->obLoc->cityID;
       return;
     }

     if ($this->cur == "myState" ) {
       $this->scope          = 'state';
       $this->scopeID        = $this->obLoc->stateID;
       $this->searchStr      = " tblWebsites.stateID=".$this->obLoc->stateID." ";
       $this->classSearchStr = " tblClassifieds.stateID=".$this->obLoc->stateID." ";
       $this->userSearch     = " tblwzUserJoins.stateID=".$this->obLoc->stateID." ";
       $this->eventSearch    = " tblCity.StateID=".$this->obLoc->stateID." ";
       $this->storeSearch    = " storeStateID=".$this->obLoc->stateID." ";
       return;
     }

     if ($this->cur == "myCountry"  || $this->cur == "" ) {
       $this->scope          = 'country';
       $this->scopeID        = $this->obLoc->countryID;
       $this->searchStr = " tblWebsites.countryID=".$this->obLoc->countryID." ";
       $this->classSearchStr = " tblClassifieds.countryID=".$this->obLoc->countryID." ";
       $this->userSearch = " tblwzUserJoins.countryID=".$this->obLoc->countryID." ";
       $this->eventSearch = " tblCity.countryID=".$this->obLoc->countryID." ";
       $this->storeSearch = " storeCountryID=".$this->obLoc->countryID." ";
       return;
     }
     $this->searchStr      = " 1=1 ";
     $this->classSearchStr = " 1=1 ";
     $this->eventSearch    = " 1=1 ";
     $this->userSearch     = " 1=1 ";
     $this->storeSearch    = " 1=1 ";
  }
};
class mkyActivityCard {
   private $nr    = null;
   private $scope = null;
   private $name  = null;
   private $mode  = null;
   private $obLoc = null;
   private $sess  = null;
   private $link  = null;
   private $hlink = null;
   private $onclick = null;
 
   private $searchStr      = null;
   private $qry            = null;
   private $userSearch     = null;
   private $eventSearch    = null;
   private $classSearchStr = null;
   private $storeSearch    = null;
   private $srcScope       = null;
   private $srcScopeID     = null;

   function __construct($inSess,$inCityID,$scope,$mode,$name,$qry=null){
     $qry = urldecode(fxnToStr($qry));
     $this->scope  = $scope;
     $this->mode   = $mode;
     $this->name   = $name;
     $this->sess   = $inSess;
     $this->nr     = 3;
     $this->qry    = mkyStrReplace('&hQry=','',$qry);
     $this->qry    = mkyStrReplace('+',' ',$this->qry);
     $wzCIDn = mkyStrpos($this->qry,'&wzCID=');
     if ($wzCIDn !== false){
       $this->qry = left($this->qry,$wzCIDn);
     }
   
     if ($this->sess->isMob){
        $this->mbld = "mblp/";
     }
     $this->obLoc = new mkyCityLocation($inCityID);

     $this->link  = self::getLink();
     self::setScopeSearch();
     //self::draw();
   }
   private function getLink($cityID=null){
     $this->link  = '<a style="font-size:12px;" href="';
     $this->link .= 'javascript:wzLink(\'/whzon/mytown/myTown.php?wzID='.$this->sess->sKey;
     $this->link .= '&spin=spin&fmyMode='.$this->mode;
     $this->link .= '&fscope='.$this->scope;
     $this->link .= '&fwUserId='.$this->sess->UID;
     if ($this->qry){
       $this->link .= '&fsq='.mkyUrlEncode($this->qry);
     }
     if (!$cityID){
       $this->link .= '&franCID='.$this->obLoc->cityID;
     }
     else {
       $this->link .= '&franCID='.$cityID;
     }
     $this->link .= '\');">';
     $this->hlink = mkyStrReplace('font-size:12px;','color:darkKhaki;',$this->link);
     $this->onclick = mkyStrReplace('<a style="font-size:12px;" href="','',$this->link);
     $this->onclick = mkyStrReplace('javascript:',' onmouseover="this.style.cursor=\'pointer\';" onclick="',$this->onclick);
     $this->onclick = mkyStrReplace('>','',$this->onclick);
     //if ($GLOBALS['userID'] == 17621){echo $this->onclick;} 
     return $this->link;
   }
   public function draw(){
     echo "<div class='infoCardClear' style=''>";
     echo "<div class='newsHLCardBG' style='padding:.75em;'>".$this->hlink;
     echo $this->name;
     echo "</a></div>";
     echo "<div style='margin-top:.5em;'>";
     self::drawImages();
     echo "</div></div>";
   }
   private function setScopeSearch(){
     if ($this->scope == "myCity"){  
       $this->searchStr      = " tblWebsites.cityID=".$this->obLoc->cityID." ";
       $this->userSearch     = " tblwzUserJoins.cityID=".$this->obLoc->cityID." ";
       $this->eventSearch    = " tblCity.cityID=".$this->obLoc->cityID." ";
       $this->classSearchStr = " tblClassifieds.cityID=".$this->obLoc->cityID." ";
       $this->storeSearch    = " tblStore.storeCityID=".$this->obLoc->cityID;
       $this->srcScope = 'city';
       $this->srcScopeID = $this->obLoc->cityID;
       return;
     }

     if ($this->scope == "myState" ) {
       $this->searchStr      = " tblWebsites.stateID=".$this->obLoc->stateID." ";
       $this->classSearchStr = " tblClassifieds.stateID=".$this->obLoc->stateID." ";
       $this->userSearch     = " tblwzUserJoins.stateID=".$this->obLoc->stateID." ";
       $this->eventSearch    = " tblCity.StateID=".$this->obLoc->stateID." ";
       $this->storeSearch    = " storeStateID=".$this->obLoc->stateID." ";
       $this->srcScope = 'state';
       $this->srcScopeID = $this->obLoc->stateID;
       return;
     }

     if ($this->scope == "myCountry"  || $this->scope == "" ) {
       $this->searchStr = " tblWebsites.countryID=".$this->obLoc->countryID." ";
       $this->classSearchStr = " tblClassifieds.countryID=".$this->obLoc->countryID." ";
       $this->userSearch = " tblwzUserJoins.countryID=".$this->obLoc->countryID." ";
       $this->eventSearch = " tblCity.countryID=".$this->obLoc->countryID." ";
       $this->storeSearch = " storeCountryID=".$this->obLoc->countryID." ";
       $this->srcScope = 'country';
       $this->srcScopeID = $this->obLoc->countryID;
       return;
     }
     $this->searchStr      = " 1=1 ";
     $this->classSearchStr = " 1=1 ";
     $this->eventSearch    = " 1=1 ";
     $this->userSearch     = " 1=1 ";
     $this->storeSearch    = " 1=1 ";
   }
   public function getQry(){
      if (!$this->qry){
        return null;
      }
      if ($this->mode == 'mosh'){
        if (strtoupper($this->qry) == "MUSIC"){
          return null;
        }
        return  " and ( tblmoshSong.title like '%".$this->qry."%' or tblMoshArtist.name like '%".$this->qry."%') ";
      }
      if ($this->mode == 'mbrs'){
        return " and (".self::andWords('tblObjPreIndex.objpWord').") ";
      }
      if ($this->mode == 'class'){
        return " and (".self::andWords('tblClassifieds.item').") ";
      }
      if ($this->mode == 'web'){
        return " and  (".self::andWords('tblObjPreIndex.objpWord').") ";
      }
      if ($this->mode == 'photo'){
        return  " and (".self::andWords(' tblwzPhoto.title').") ";
      }
      if ($this->mode == 'mBlog'){
        return " and (".self::andWords('tblMBlogEntry.title').") ";
      }
      if ($this->mode == 'mshare'){
        return " and (".self::andWords('tblObjPreIndex.objpWord').") ";
      }
      if ($this->mode == 'video'){
        return " and (".self::andWords('tblObjPreIndex.objpWord').") ";
      }
      if ($this->mode == 'chan'){
        return " and (".self::andWords('tblObjPreIndex.objpWord').") ";
      }
      return null;
   }
   private function andWords($index){
      $words = explode(' ',$this->qry);
      $and = null;
      $srch = null;
      foreach($words as $word){
        $wlen = mb_strlen($word);
        if($wlen > 2){
          if ($this->mode=='mshare' || $this->mode == 'mbrs' || $this->mode == 'video' || $this->mode == 'web'){
            if($wlen > 3){
              $srch .= $and.$index." like '%".$word."%' ";
            }
            else {      
              $srch .= $and.$index." =  '".$word."' ";
            }
          }
          else {
            $srch .= $and.$index." like '%".$word."%' ";
          }
          if (!$and){
            $and = " or ";
          }
        }
      }
      if (!$srch) {$srch = " 1 = 2 ";}
      return $srch;   
   }
   private function drawScript(){
      ?>
      <script>
      function swapFailed<?php echo $this->mode;?>Img(id){
        var img = document.location.getElementById('<?php echo $this->mode;?>'+id);
        if (img){
          img.style.display  = 'none';
        }
      }
      </script>
      <?php
   }
   private function drawImages(){
     global $userID;
     //self::drawScript();
     $offsetWidth = safeGET('avSpace');
     if (!$offsetWidth || $offsetWidth == ""){
       $offsetWidth = 0;
     }
     //echo 'offset:',$offsetWidth; 
     $npr = 4;
     $unpr = 6;
     $padL = 0;
     if ($offsetWidth  < 400 || $GLOBALS['sessISMOBILE']){
       $npr = 2;
       $unpr = 3;
       if (!$GLOBALS['sessISMOBILE']){
         $padL = 25;
       }
       else {
         $padL = 20;
       }
     }
     $mar = 1.95*2*$npr;
     $avSpace = $offsetWidth -$padL -(2*8) -(2*8) -$mar;
     $pw  = $avSpace / $npr;

     $mar = 1.8*2*$unpr;
     $avSpace = $offsetWidth -$padL -(2*8) -(2*8) -$mar;
     $upw  = $avSpace / $unpr;
     //if ($userID == 17621) {echo $this->srcScope.':'.$this->srcScopeID;}
 
     if ($this->mode == 'video'){
       $tmpQCache = makeTmpName(hash('sha256',$this->userSearch.$this->qry.'video limit 8'));
       $tmpCFile  = '/var/www/www.bitmonky.com/wzAdmin/tmpQry'.$tmpQCache.'.txt';
       $res = checkCache($tmpCFile);
       if ($res == 'Empty'){
	 return;
       } 
       if ($res){
	 echo $res;
	 return;
       }
       $res = null;
       $SQL = "SELECT activityID,acItemID,tblCity.cityID ";
       $SQL .= "from tblActivityFeed  ";
       $SQL .= "inner join tblwzUserJoins  on tblwzUserJoins.wzUserID = tblActivityFeed.wzUserID ";
       $SQL .= "inner join tblwzVideo  on tblwzVideo.wzVideoID = acItemID ";
       $SQL .= "inner join tblCity on tblCity.cityID = tblwzUserJoins.cityID ";
       $SQL .= "where tblActivityFeed.acCode = 17 and ".$this->userSearch.self::getQry();
       $SQL .= "order by rand() limit 8 "; //.($this->nr * 3)." ";
       if ($this->qry){
         $SQL = "SELECT sum(prcwZeroWT * power(prcwLen,2))nRes,activityID, acItemID,tblCity.cityID ";
         $SQL .= "from tblObjPreIndex  ";
         $SQL .= "inner join tblpreIndexCWords on prcwWord = objpWord ";
         $SQL .= "inner join tblActivityFeed  on objpACID = activityID ";
         $SQL .= "inner join tblwzVideo  on tblwzVideo.wzVideoID = acItemID ";
         $SQL .= "inner join tblwzUserJoins  on tblwzUserJoins.wzUserID = tblActivityFeed.wzUserID ";
         $SQL .= "inner join tblCity on tblCity.cityID = tblwzUserJoins.cityID ";
         $SQL .= "where tblObjPreIndex.objpName='video' and ".$this->userSearch.self::getQry();
         $SQL .= "group by activityID, acItemID,tblCity.cityID ";
         $SQL .= "order by nRes desc  limit 8 "; //.($this->nr * 3)." ";
         $res = getQry('video', $this->qry,$this->srcScope,$this->srcScopeID,' limit 8');//$SQL = mkyStrReplace('%','',$SQL);
       }
       //if ($userID == 17621) {echo $SQL;}
       if (!$res){
	 $res = mkyMsqry($SQL);
       }        
       $rec = mkyMsFetch($res);
       $cache = null;
       while($rec){
         $acItemID = $rec['acItemID'];
         $img = getVideoImg($acItemID);
         self::getLink($rec['cityID']);
         $cache .=  mkyStrReplace('\');">','',$this->link).'&digID='.$acItemID.'\');">';
         $cache .= "<img ID='video".$acItemID."' ".$this->onclick." onerror='swapFailed".$this->mode."Img(".$acItemID.")' ";
         $cache .= "style='height:".($pw/1.6)."px;width:".$pw."px;margin:.0em .15em 0em .15em;border-radius:.25em;' src='".$img."'/>";
         $rec = mkyMsFetch($res);
       }
       writeCache($tmpCFile,$cache);
     }
     if ($this->mode == 'mosh'){
       $tmpQCache = makeTmpName(hash('sha256',$this->userSearch.$this->qry.'mosh limit 8'));
       $tmpCFile  = '/var/www/www.bitmonky.com/wzAdmin/tmpQry'.$tmpQCache.'.txt';
       $res = checkCache($tmpCFile);
       if ($res == 'Empty'){
         return;
       }
       if ($res){
         echo $res;
         return;
       }
       $SQL  = "SELECT tblMoshPit.moshPitID,tblCity.cityID, tblwzUserJoins.wzUserID, firstname,venuName,description, ";
       $SQL .= "health, tblMoshPit.nViews, gigID FROM tblMoshPit  ";
       $SQL .= "inner join tblwzUserJoins  on tblMoshPit.wzUserId=tblwzUserJoins.wzUserID ";
       $SQL .= "inner join tblCity  on tblCity.cityID = tblwzUserJoins.cityID ";
       $SQL .= "inner join tblMoshPerformance  on tblMoshPit.gigID=tblMoshPerformance.moshPerformanceID ";
       $SQL .= "inner join tblmoshSong  on tblmoshSong.songID=tblMoshPerformance.songID ";
       $SQL .= "inner join tblMoshArtist  on tblmoshSong.artistID = tblMoshArtist.moshArtistID ";
       $SQL .= "where ".$this->userSearch.self::getQry();
       $SQL .= "group by tblMoshPit.moshPitID, tblwzUserJoins.wzuserID, firstname,venuName,description, ";
       $SQL .= "health, tblMoshPit.nViews, gigID,tblCity.cityID ";
       $SQL .= "order by rand() limit 16 "; //.($this->nr * 5)." ";
       $res = mkyMsqry($SQL);
       $rec = mkyMsFetch($res);
     
       $cache = null;
       while($rec){
         self::getLink($rec['cityID']);
         $SQL = "select  tblmoshSong.songID,artistID, uTubeCD, tblMoshArtist.name, title,tblMoshArtist.img from tblMoshPerformance  ";
         $SQL .= "inner join tblmoshSong  on tblmoshSong.songID=tblMoshPerformance.songID ";
         $SQL .= "inner join tblMoshArtist on tblmoshSong.ArtistID=tblMoshArtist.moshArtistID where moshPerformanceID=".$rec['gigID'];

         $gRec = null;
         $gresult = mkyMsqry($SQL) or die($SQL);
         $gRec = mkyMsFetch($gresult);
         $uImg = getSongIMG($gRec['uTubeCD']);
         if ($uImg != '/default.jpg'){ 
           $cache .= mkyStrReplace('\');">','',$this->link).'&digID='.$rec['moshPitID'].'&songID='.$gRec['songID'].'\');">';
           $cache .= "<img style='height:".($pw/1.6)."px;width:".$pw."px;margin:.15em;border-radius:.25em;' src='".$uImg."'/></a>";
         }
         $rec = mkyMsFetch($res);
       }
       writeCache($tmpCFile,$cache);
     }
     if ($this->mode == 'chan'){
       $tmpQCache = makeTmpName(hash('sha256', $this->userSearch.$this->qry.'chan limit 8'));
       $tmpCFile  = '/var/www/www.bitmonky.com/wzAdmin/tmpQry'.$tmpQCache.'.txt';
       $res = checkCache($tmpCFile);
       if ($res == 'Empty'){
         return;
       }
       if ($res){
         echo $res;
         return;
       }
       $SQL  = "Select storeBanID,tblChatChannel.channelID,tblwzUserJoins.cityID from tblChatChannel  ";
       $SQL .= "inner join tblHashChanOwner  on tblChatChannel.channelID = hcoChatChanID "; 
       $SQL .= "inner join tblwzUserJoins  on tblwzUserJoins.wzUserID = tblChatChannel.ownerID ";
       $SQL .= "inner join tblCity on tblCity.cityID = tblwzUserJoins.cityID ";
       $SQL .= "where not storeBanID is null and ".$this->userSearch.self::getQry();
       $SQL .= "group by tblChatChannel.channelID,storeBanID,tblwzUserJoins.cityID "; 
       $SQL .= "order by rand() limit 4 "; //.($this->nr * 2)." ";
       if ($this->qry){
         $SQL = "SELECT sum(prcwZeroWT * power(prcwLen,2))nRes, tblwzUserJoins.wzUserID,acItemID channelID,storeBanID,tblCity.cityID ";
         $SQL .= "from tblObjPreIndex  ";
         $SQL .= "inner join tblpreIndexCWords on prcwWord = objpWord ";
         $SQL .= "inner join tblActivityFeed  on objpACID = activityID ";
         $SQL .= "inner join tblHashChanOwner  on tblActivityFeed.acItemID = hcoChatChanID ";
         $SQL .= "inner join tblwzUserJoins  on tblwzUserJoins.wzUserID = hcoUID ";
         $SQL .= "inner join tblCity on tblCity.cityID = tblwzUserJoins.cityID ";
         $SQL .= "where tblObjPreIndex.objpName='chan' and ".$this->userSearch.self::getQry();
         $SQL .= "group by acItemID,storeBanID,tblCity.cityID,tblwzUserJoins.wzUserID ";
         $SQL .= "order by nRes desc limit 4 "; //.($this->nr * 5)." ;";
       }
       //if ($GLOBALS['userID'] == 17621){echo $SQL;}
       $res = mkyMsqry($SQL);
       $rec = mkyMsFetch($res);
       $cache = null;
       while($rec){
         self::getLink($rec['cityID']);
         $cache .= mkyStrReplace('\');">','',$this->link).'&digID='.$rec['channelID'].'\');">';
	 $cache .= "<img style='height:".(($pw/1.6)*0.7)."px;width:".$pw."px;margin:.15em;border-radius:.25em;' ";
	 $cache .= "onerror='this.style.display=\"none\";' src='//image.bitmonky.com/getStoreBGTmn.php?id=".$rec['storeBanID']."'/></a>";
         $rec = mkyMsFetch($res);
       }
       writeCache($tmpCFile,$cache);
     }
     if ($this->mode == 'mbrs'){
       $tmpQCache = makeTmpName(hash('sha256',$this->userSearch.$this->qry.'mbrs limit 8'));
       $tmpCFile  = '/var/www/www.bitmonky.com/wzAdmin/tmpQry'.$tmpQCache.'.txt';
       $res = checkCache($tmpCFile);
       if ($res == 'Empty'){
         return;
       }
       if ($res){
         echo $res;
         return;
       }
       $SQL  = "Select wzUserID,cityID from tblwzUserJoins  ";
       $SQL .= "where imgFlg=1 and ".$this->userSearch.self::getQry();
       $SQL .= "order by rand() limit 12 "; //.($this->nr * 5)." ";
       if ($this->qry){
         $SQL = "SELECT sum(prcwZeroWT * power(prcwLen,2))nRes,activityID, tblwzUserJoins.wzUserID,acItemID,tblCity.cityID ";
         $SQL .= "from tblObjPreIndex  ";
         $SQL .= "inner join tblpreIndexCWords on prcwWord = objpWord ";
         $SQL .= "inner join tblActivityFeed  on objpACID = activityID ";
         $SQL .= "inner join tblwzUserJoins  on tblwzUserJoins.wzUserID = tblActivityFeed.wzUserID ";
         $SQL .= "inner join tblCity on tblCity.cityID = tblwzUserJoins.cityID ";
         $SQL .= "where tblObjPreIndex.objpName='mbrs' and ".$this->userSearch.self::getQry();
         $SQL .= "group by activityID, acItemID,tblCity.cityID,tblwzUserJoins.wzUserID ";
         $SQL .= "order by nRes desc limit ".($this->nr * 5)." ;";
       }
       $res = mkyMsqry($SQL);
       $rec = mkyMsFetch($res);
       $cache = null;
       while($rec){
         self::getLink($rec['cityID']);
         $cache .= mkyStrReplace('\');">','',$this->link).'&digID='.$rec['wzUserID'].'\');">';
	 $cache .= "<img style='height:".($upw*1.25)."px;width:".$upw."px;margin:.15em;border-radius:.25em;' ";
	 $cache .= "src='//image.bitmonky.com/getMbrImg.php?id=".$rec['wzUserID']."'/></a>";
         $rec = mkyMsFetch($res);
       }
       writeCache($tmpCFile,$cache);

     }
     if ($this->mode == 'photo'){
       $tmpQCache = makeTmpName(hash('sha256',$this->userSearch.$this->qry.'photo limit 8'));
       $tmpCFile  = '/var/www/www.bitmonky.com/wzAdmin/tmpQry'.$tmpQCache.'.txt';
       $res = checkCache($tmpCFile);
       if ($res == 'Empty'){
         return;
       }
       if ($res){
         echo $res;
         return;
       }
       $SQL  = "Select photoID,tblCity.cityID from tblwzPhoto  ";
       $SQL .= "inner join tblwzPhotoAlbum  on tblwzPhotoAlbum.wzPhotoAlbumID = tblwzPhoto.wzPhotoAlbumID ";
       $SQL .= "inner join tblwzUserJoins  on tblwzUserJoins.wzUserID = tblwzPhoto.wzUserID ";
       $SQL .= "inner join tblCity  on tblCity.cityID = tblwzUserJoins.cityID ";
       $SQL .= "where privacy < 2 and isMkdDating is null and ".$this->eventSearch.self::getQry();
       $SQL .= "order by rand() limit 6 "; //.($this->nr * 3)." ";
       $res = mkyMsqry($SQL);
       $rec = mkyMsFetch($res);
       $cache = null;
       while($rec){
         self::getLink($rec['cityID']);
         $cache .= mkyStrReplace('\');">','',$this->link).'&digID='.$rec['photoID'].'\');">';
	 $cache .= "<img style='height:".($upw*1.25)."px;width:".$upw."px;margin:.15em;border-radius:.25em;' ";
	 $cache .= "src='//image.bitmonky.com/getPhotoTmn.php?id=".$rec['photoID']."'/></a>";
         $rec = mkyMsFetch($res);
       }
       writeCache($tmpCFile,$cache);

     }
     if ($this->mode == 'class'){
       $tmpQCache = makeTmpName(hash('sha256',$this->userSearch.$this->qry.'class limit 8'));
       $tmpCFile  = '/var/www/www.bitmonky.com/wzAdmin/tmpQry'.$tmpQCache.'.txt';
       $res = checkCache($tmpCFile);
       if ($res == 'Empty'){
         return;
       }
       if ($res){
         echo $res;
         return;
       }
       $SQL = "Select adID,cityID from tblClassifieds  ";
       $SQL .= "where postStatus is null and itemStoreID = 0 and imgFlg = 1 and ".$this->classSearchStr.self::getQry();
       $SQL .= "order by rand() limit 4 "; //.($this->nr)." ";
       $res = mkyMsqry($SQL);
       $rec = mkyMsFetch($res);
       $cache = null;
       while($rec){
         self::getLink($rec['cityID']);
         $cache .= mkyStrReplace('\');">','',$this->link).'&digID='.$rec['adID'].'\');">';
         $cache .= "<img style='max-width:calc(100% - .3em);height:".($pw/1.6)."px;width:".$pw."px;margin:.15em;border-radius:.25em;' ";
         $cache .= "src='//image.bitmonky.com/getClassTmn.php?id=".$rec['adID']."'/></a>";
         $rec = mkyMsFetch($res);
       }
       writeCache($tmpCFile,$cache);
     }
     if ($this->mode == 'web'){
       $tmpQCache = makeTmpName(hash('sha256',$this->userSearch.$this->qry.'web limit 8'));
       $tmpCFile  = '/var/www/www.bitmonky.com/wzAdmin/tmpQry'.$tmpQCache.'.txt';
       $res = checkCache($tmpCFile);
       if ($res == 'Empty'){
         return;
       }
       if ($res){
         echo $res;
         return;
       }
       $res = null;    
       $SQL = "Select ndxwWebsiteID acItemID,ndxwCityID cityID from ndxWeb.ndxWebsites  ";
       $SQL .= "left join Category2  on ndxwCategoryID = categoryID ";
       $SQL .= "inner join tblCity on tblCity.cityID = ndxwCityID ";
       $SQL .= "where ndxwDeleted is null ";
       $SQL .= "and ".$this->searchStr.self::getQry();
       $SQL .= "order by rand() limit 8 "; //.($this->nr * 3)." ";
       if ($this->qry){
         $SQL = "SELECT count(*)nRes,ndxwWebsiteID acItemID, activityID, ndxwCityID cityID ";
         $SQL .= "from tblObjPreIndex  ";
         $SQL .= "inner join tblpreIndexCWords on prcwWord = objpWord ";
         $SQL .= "left join tblActivityFeed  on objpACID = activityID ";
         $SQL .= "inner join ndxWeb.ndxWebsites on ndxwWebsiteID = objpItemID ";
         $SQL .= "inner join tblCity on tblCity.cityID = ndxwCityID ";
         $SQL .= "where tblObjPreIndex.objpName='web' and ".$this->searchStr.self::getQry();
         $SQL .= "group by ndxwWebsiteID,ndxwCityID ";
         $SQL .= "order by nRes desc limit 8 "; //.($this->nr * 3)." ";
	 $res = getQry('web', $this->qry,$this->srcScope,$this->srcScopeID,' limit 8');
       }

       $SQL = mkyStrReplace('tblWebsites.stateID','ndxwStateID',$SQL);
       $SQL = mkyStrReplace('tblWebsites.countryID','ndxwCountryID',$SQL);
       $SQL = mkyStrReplace('tblWebsites.cityID','ndxwCityID',$SQL);
       $SQL = mkyStrReplace('tblObjPreIndex','ndxWeb.ndxObjPreIndex',$SQL);

       if (!$res){
         $res = mkyMsqry($SQL);
       }
       else {echo 'PeerTree Results';}       
       $rec = mkyMsFetch($res);
       $cache = null;
       while($rec){
	 self::getLink($rec['cityID']);
         $websiteID = $rec['acItemID'];
         $cache .= mkyStrReplace('\');">','',$this->link).'&digID='.$websiteID.'\');">';
         $cache .= "<img style='max-width:calc(100% - .3em);height:".($pw/1.6)."px;width:".$pw."px;margin:.15em;border-radius:.25em;' ";
         $cache .= "onerror='this.style.display=\"none\";' src='//image.bitmonky.com/getWsImg.php?id=".$websiteID."'/></a>";
         $rec = mkyMsFetch($res);
       }
       writeCache($tmpCFile,$cache);
     }
     if ($this->mode == 'mBlog'){
       $tmpQCache = makeTmpName(hash('sha256',$this->userSearch.$this->qry.'mBlog limit 8'));
       $tmpCFile  = '/var/www/www.bitmonky.com/wzAdmin/tmpQry'.$tmpQCache.'.txt';
       $res = checkCache($tmpCFile);
       if ($res == 'Empty'){
         return;
       }
       if ($res){
         echo $res;
         return;
       }
       $SQL = "SELECT mBlogEntryID,tblwzUserJoins.cityID  From tblmBlogTopic  ";
       $SQL .= "inner join  tblwzUserJoins  ON tblmBlogTopic.wzUserID = tblwzUserJoins.wzUserID ";
       $SQL .= "inner join tblMBlogEntry  on tblmBlogTopic.mBlogTopicID = tblMBlogEntry.mBlogTopicID ";
       $SQL .= "inner join tblCity  on tblCity.cityID=tblwzUserJoins.cityID ";
       $SQL .= "where tblMBlogEntry.imgFlg = 1 and mbStatus is null and sandBox is null and privacy is null and adultContent<>1 and spamFlg<>1  ";
       $SQL .= "and ".$this->eventSearch.self::getQry();
       $SQL .= "order by rand() limit 8 "; //.($this->nr * 3)." ";
       $res = mkyMsqry($SQL) or die($SQL);
       $rec = mkyMsFetch($res);
       $cache = null;
       while($rec){
         self::getLink($rec['cityID']);
         $cache .= mkyStrReplace('\');">','',$this->link).'&digID='.$rec['mBlogEntryID'].'\');">';
         $cache .= "<img style='max-width:calc(100% - .3em);height:".($pw/1.6)."px;width:".$pw."px;margin:.15em;border-radius:.25em;' ";
         $cache .= "src='//image.bitmonky.com/getmBlogTmn.php?id=".$rec['mBlogEntryID']."'/></a>";
         $rec = mkyMsFetch($res);
       }
       writeCache($tmpCFile,$cache);
     }
  
     if ($this->mode == 'mshare'){
       $tmpQCache = makeTmpName(hash('sha256',$this->userSearch.$this->qry.'mshare limit 8'));
       $tmpCFile  = '/var/www/www.bitmonky.com/wzAdmin/tmpQry'.$tmpQCache.'.txt';
       $res = checkCache($tmpCFile);
       if ($res == 'Empty'){
         return;
       }
       if ($res){
         echo $res;
         return;
       }

       $SQL = "SELECT activityID,acItemID,tblCity.cityID ";
       $SQL .= "from tblActivityFeed  ";
       $SQL .= "inner join tblwzUserJoins  on tblwzUserJoins.wzUserID = tblActivityFeed.wzUserID ";
       $SQL .= "inner join tblCity on tblCity.cityID = tblwzUserJoins.cityID ";
       $SQL .= "where tblActivityFeed.acCode = 18 and ".$this->userSearch.self::getQry();
       $SQL .= "order by rand() limit 8 "; //.($this->nr * 3)." ";
       if ($this->qry){
         $SQL = "SELECT count(*)nRes,activityID, acItemID,tblCity.cityID ";
         $SQL .= "from tblObjPreIndex  ";
         $SQL .= "inner join tblpreIndexCWords on prcwWord = objpWord ";
         $SQL .= "inner join tblActivityFeed  on objpACID = activityID ";
         $SQL .= "inner join tblwzUserJoins  on tblwzUserJoins.wzUserID = tblActivityFeed.wzUserID ";
         $SQL .= "inner join tblCity on tblCity.cityID = tblwzUserJoins.cityID ";
         $SQL .= "where tblObjPreIndex.objpName='mshare' and ".$this->userSearch.self::getQry();
         $SQL .= "group by activityID, acItemID,tblCity.cityID ";
         $SQL .= "order by nRes desc limit 8 "; //.($this->nr * 3)." ;";
       }
       //$SQL = mkyStrReplace('tblObjPreIndex','ndxMShares.ndxObjPreIndex',$SQL);

       //if ($userID == 17621) {echo $SQL;}
       $res = mkyMsqry($SQL);
       $rec = mkyMsFetch($res);
       $cache = null;
       while($rec){
         $acItemID = $rec['acItemID'];
         $acID     = $rec['activityID'];
         $SQL  = "SELECT  urlLink,urlImgLink  FROM newsDirectory.tblUrlShares ";
         $SQL .= "where urlShareID = ".$acItemID;
         $myresult = mkyMyqry($SQL); 
         if ($myresult){$tRec = mkyMyFetch($myresult);} else { $tRec=null;}

         if ($tRec){
           $oimg = $tRec['urlImgLink'];
           if ($oimg == 'https://i.ytimg.com/vi//hqdefault.jpg'){
             $oimg = '0.jpg';
           }
           $URL = $tRec['urlLink'];
           $img = fixUTubeImg($URL,$oimg);
           if ($oimg == '0.jpg'){
             $SQL  = "update newsDirectory.tblUrlShares set  urlImgLink ='".$img."' ";
             $SQL .= "where urlShareID = ".$acItemID;
             $myres = mkyMyqry($SQL);
           }
           $img = "//image.bitmonky.com/getNShareImg.php?id=".$acItemID;

           self::getLink($rec['cityID']);
           $cache .= mkyStrReplace('\');">','',$this->link).'&digID='.$rec['acItemID'].'\');">';
           $cache .= "<img ID='mshare".$acItemID."' onerror='swapFailed".$this->mode."Img(".$acItemID.")' ";
	   $cache .= "style='max-width:calc(100% - .3em);height:".($pw/1.6)."px;width:".$pw."px;margin:.15em;border-radius:.25em;' ";
	   $cache .= "onerror='this.style.display=\"none\";' src='".$img."'/></a>";
         }
         $rec = mkyMsFetch($res);
       }
       writeCache($tmpCFile,$cache);
     }
   }
};
class mkyNewsHLCard {
   private $sess    = null;
   private $obLoc   = null;
   private $jbType  = null;
   private $acCode  = null;
   private $jbTitle = null;
   private $jbDesc  = null;
   private $jbLink  = null;
   private $jbImg   = null;
   private $jbImgID = null;
   private $jbOwner = null;
   private $mbld    = null;
   private $scope   = null;
   private $nHL     = null;
   private $imo     = null;
   function __construct($inSess,$inJobTID,$inCityID,$inScope=null){
     $this->nHL = 3;
     $this->sess   = $inSess;
     if ($this->sess->isMob){
        $this->mbld = "mblp/";
     }
     $this->obLoc        = new mkyCityLocation($inCityID);
    
     if (!$inScope){
       $inScope = "myCity";
     }
     $this->scope        = new mkyScope($inScope,'city',$inCityID); 
     $this->jbType = $inJobTID;
   }
   public function draw(){
     $SQL = "select acCode,acItemID,headLine,acdate,tblActivityFeed.wzUserID ";
     $SQL .= "from tblTycJobDesc  ";
     $SQL .= "inner join tblActivityFeed  on acJobID = tycJobID ";
     $SQL .= "inner join tblCity  on cityID = acCityID ";
     $SQL .= "left  join tblState  on tblCity.StateID = tblState.stateID ";
     $SQL .= "inner join tblCountry  on tblCity.countryID = tblCountry.countryID ";
     //$SQL .= "where  cityID = ".$inCityID." and tycJobID = ".$this->jbType;
     $SQL .= self::setScopeWhere();
     $SQL .= " order by acDate desc limit ".$this->nHL." ";

     $res = mkyMsqry($SQL);
     $rec = mkyMsFetch($res);
     if (!$rec){
       self::showJobOpen();
       return;
     }
     self::putCardHeader($rec);
     while($rec){
       $this->acCode  = $rec['acCode'];
       $this->jbItem  = $rec['acItemID'];
       $this->jbOwner = $rec['wzUserID'];
       self::putHeadline();
       $rec = mkyMsFetch($res);
     }
     self::PutCardFooter();
   }   
   private function setScopeWhere(){
     if (!$this->scope){ 
       return "where  cityID = ".$this->obLoc->cityID." and tycJobID = ".$this->jbType." ";
     }
     return "where ".$this->scope->getSearch('city',$this->obLoc->cityID). " and tycJobID = ".$this->jbType." ";
   }
   private function putScopeLinks(){
     echo "Location: ";
     if ($this->scope->cur != 'myCity' && $this->scope->cur != 'myState' && $this->scope->cur != 'myCountry'){
       $jCityAnk = "<a href='javascript:wzLink(\"/whzon/mytown/myTownProfile.php?wzID=".$this->sess->sKey."\");'>";
       echo $jCityAnk."World Wide</a>";
       return;
     }
     if ($this->scope->cur == 'myCity'){
       $jCityAnk = "<a href='javascript:wzLink(\"/whzon/mytown/myTownChangeTo.php?wzID=".$this->sess->sKey."&fcityID=".$this->obLoc->cityID."\");'>";
       echo $jCityAnk.$this->obLoc->city."</a>";
       $jCityAnk = "<a href='javascript:wzLink(\"/whzon/mytown/myTownChangeTo.php?wzID=".$this->sess->sKey."&fprovID=".$this->obLoc->stateID."\");'>";
       echo ", ".$jCityAnk.$this->obLoc->state."</a>, ";
     }

     if ($this->scope->cur == 'myState'){
       $jCityAnk = "<a href='javascript:wzLink(\"/whzon/mytown/myTownChangeTo.php?wzID=".$this->sess->sKey."&fprovID=".$this->obLoc->stateID."\");'>";
       echo $jCityAnk.$this->obLoc->state."</a>, ";
     }
 
     $jCityAnk = "<a href='javascript:wzLink(\"/whzon/mytown/myTownChangeTo.php?wzID=".$this->sess->sKey."&fcountryID=".$this->obLoc->countryID."\");'>";
     echo $jCityAnk.$this->obLoc->country."</a>";
   }
   public function drawGuideHeaderAll($wzUserID=null){
     echo "<div class='newsHLCard' style=''>";
     echo "<h3>Current Selection - <span style='color:darkKhaki;'>All Posts</span></h3>";

     $SQL = "select tycJobID,headLine from tblTycJobDesc  ";
     $SQL .= "where not headLine is null ";

     $res = mkyMsqry($SQL);
     $rec = mkyMsFetch($res);
     if ($rec){
       echo "<div class='infoCardClear' style=''>";
       echo "<h3>Focused Posts - ".$this->obLoc->city."</h3>";
       while ($rec){
         $jCityAnk = "<a href='javascript:wzLink(\"/whzon/".$this->mbld."mytown/myTownProfile.php?wzID=".$this->sess->sKey;
         $jCityAnk .= "&cpCity=".$this->obLoc->cityID."&franCID=".$this->obLoc->cityID."&gscope=".$this->scope->cur."&fwzUserID=".$wzUserID."&gID=".$rec['tycJobID']."\");'>";
         echo $jCityAnk." ".$rec['headLine']." </a> | ";
         $rec = mkyMsFetch($res);
       }
     }
     echo "</div></div>";
   }
   public function drawGuideHeader($wzUserID=null){
     if ($this->jbType == 999){
       self::drawGuideHeaderAll($wzUserID);
       return;
     } 
     $SQL = "select headLine from tblTycJobDesc  ";
     $SQL .= "where tycJobID = ".$this->jbType;

     $res = mkyMsqry($SQL);
     $rec = mkyMsFetch($res);

     echo "<div class='newsHLCard' style=''>";
     echo "<h3>Current Selection - <span style='color:darkKhaki;'>".$rec['headLine']."</span></h3>";

     $SQL = "select tycJobID,headLine from tblTycJobDesc  ";
     $SQL .= "where not tycJobID=".$this->jbType." and not headLine is null ";

     $res = mkyMsqry($SQL);
     $rec = mkyMsFetch($res);
     if ($rec){
       echo "<div class='infoCardClear' style=''>";
       echo "<h3>Other Focused Posts</h3>";
       while ($rec){
         $jCityAnk = "<a href='javascript:wzLink(\"/whzon/".$this->mbld."mytown/myTownProfile.php?wzID=".$this->sess->sKey;
         $jCityAnk .= "&cpCity=".$this->obLoc->cityID."&franCID=".$this->obLoc->cityID."&gscope=".$this->scope->cur."&fwzUserID=".$wzUserID."&gID=".$rec['tycJobID']."\");'>";
         echo $jCityAnk." ".$rec['headLine']." </a> | ";
         $rec = mkyMsFetch($res);
       }
     }
     echo "</div></div>";
  }
   private function putCardHeader($rec){
     echo "<div class='newsHLCard' style=''>";
     echo "<h2>".$rec['headLine']."</h2>";
     self::putScopeLinks();
   }
   private function getOBJLink(){
     if ($this->acCode == 7){
       $ank = "<a style='' href='javascript:wzLink(\"/whzon/".$this->mbld."mbr/mbrViewPhotos.php?wzID=".$this->sess->sKey."&fwzUserID=".$this->jbOwner."&vPhotoID=".$this->jbItem."\");'>";
       return $ank;
     }
     if ($this->acCode == 18){
       $ank = "<a style='' href='javascript:wzLink(\"/whzon/".$this->mbld."mbr/mbrViewWNewsShare.php?wzID=".$this->sess->sKey."&newsID=".$this->jbItem."\");'>";
       return $ank;
     }
     return null;
   }
   private function getOBJRec(){
     if ($this->acCode == 7){
       $SQL = "SELECT photoID,height,width, title, phototxt from tblwzPhoto  ";
       $SQL .= "where photoID = ".$this->jbItem." limit 1";
       $result = mkyMsqry($SQL);
       $tRec   = mkyMsFetch($result);
       $this->jbImg = "//image.bitmonky.com/getPhotoImg.php?id=".$this->jbItem."&fpv=".$this->jbOwner;
       $this->jbImgID = 'jbImgAc07'.$this->jbItem;
       return $tRec;
     }
     if ($this->acCode == 18){
       $SQL = "SELECT urlImgLink, urlTitle title, urlDesc phototxt, urlNotes from newsDirectory.tblUrlShares ";
       $SQL .= "where urlShareID = ".$this->jbItem;
       $result = mkyMyqry($SQL);
       $tRec   = mkyMyFetch($result);
       $this->jbImg = "//image.bitmonky.com/getNShareImg.php?id=".$this->jbItem; //Rec['urlShareImgLink'];
       $this->jbImgID = 'jbImgAc18'.$this->jbItem;
       return $tRec;
     }
     return null;
   }
   private function putHeadLine(){
     $tRec   = self::getOBJRec();
     $title  = $tRec['title'];
     $desc   = left($tRec['phototxt'],200);
     if ((!$desc || $desc == '') && $this->acCode == 18){
       $desc = left($tRec['urlNotes'],200);
     }

     $jCityAnk = "<a href='javascript:wzLink(\"/whzon/".$this->mbld."mytown/myTownProfile.php?wzID=".$this->sess->sKey."&fwzUserID=".$this->jbOwner."&fscope=myCity&fmyMode=mbrs\");'>";
     $ank = self::getOBJLink();
     
     echo "<p><b style='font-size:larger;'>".$title."</b><br/><br/>";
     echo $ank;
     echo "<img style='float:left;margin:0 1em 1em 0em;width:134px;height:90px;border-radius:.5em;' ";
     echo "src='".$this->jbImg."' ID='".$this->jbImgID."' onerror='hideImageID(\"".$this->jbImgID."\");'/>";
     echo "</a><span style='font-size:smaller;'>".$desc."<br/>".$ank."Read Full Story</a></span><br clear='left'/>";
     
   }
   private function putCardFooter(){
     $jCityAnk = "<a href='javascript:wzLink(\"/whzon/".$this->mbld."mytown/myTownProfile.php?wzID=".$this->sess->sKey;
     $jCityAnk .= "&cpCity=".$this->obLoc->cityID."&franCID=".$this->obLoc->cityID."&gscope=".$this->scope->cur."&fwzUserID=".$this->jbOwner."&gID=".$this->jbType."\");'>";
     echo "<div align='right'>".$jCityAnk." View More </a></div>";
     echo "</div>";
   }
   private function showJobOpen(){
     return;
     
     $SQL  = "SELECT tycJobID,tycJobTitle,tycJobDesc,tjobMaxDayPosts,tjobRate,tjobMaxEmp from tblTycJobDesc  ";
     $SQL .= "left join tblTycEmployee  on tycEmpType = tycJobID and TycCityID=".$this->obLoc->cityID." ";
     $SQL .= "where tycEmpID is null and tycEmpType = ".$this->jbType;

     $cRec = null;
     $cresult = mkyMsqry($SQL) or die($SQL);
     $cRec = mkyMsFetch($cresult);

     if ($cRec){

       $jobID = $cRec['tycJobID'];
       $SQL = "Select count(*) as nRec from tblTycJobApplications  ";
       $SQL .= "where tjaUID=".$this->sess->UID." and tjaCityID=".$this->obLoc->cityID." and tjaJobID = ".$cRec['tycJobID'];
       $jresult = mkyMsqry($SQL);
       $jRec = mkyMsFetch($jresult);
       $appSent = $jRec['nRec'];

       echo "<div ID='jobApp".$jobID."' class='infoCardClear' style='margin:0px 0px 25px; 0px;'>";
       if ($appSent < 1){
         echo "<div align='right' style='margin-bottom:2px;'><a style='font-size:smaller;' href='javascript:applyToJob(".$cRec['tycJobID'].");'>";
         echo "Apply For This Job</a></div>";
       }
       else {
         echo "<div align='right' style='font-size:smaller;margin-bottom:2px;'><b>".getTRxt('Your Application For This Job is:')."</b> ".getTRxt('Pending')."</div>";
       }
       echo "<b>".getTRxt('Position:')."</b> ".getTRxt($cRec['tycJobTitle']);
       if ($jobID == 5){
         echo "<p/><b>".getTRxt('Pay:')."</b> ".getTRxt('Monthly Percentage Of Tycoon Tax (to be decided)')." ";
       }
       else {
         echo "<p/><b>".getTRxt('Pay:')."</b>  ".mkyNumFormat($cRec['tjobRate'],0)." gp Per Post | <b>Post Per Day:</b> ".$cRec['tjobMaxDayPosts'];
       }
       echo "<br/><b>".getTRxt('Positions Available:')."</b>  ".$cRec['tjobMaxEmp'];
       echo "<p><b>".getTRxt('Job Description:')."</b><br/><br/>";
       echo getTRxt($cRec['tycJobDesc']);
       echo "<p/></div>";
    }
  }
};
class mkyCityLocation {
   private $ownerID  = null;
   public  $cityID   = null;
   public  $metroID  = null;
   public  $stateID  = null;
   public  $countryID = null;
   public  $regionID  = null;
   public  $city      = null;
   private $metro     = null;
   private $cRegion   = null;
   public  $state     = null;
   public  $country   = null;
   private $wRegion   = null;
   public  $viewScope = null;

   function __construct($inCID){
     self::setLocTo($inCID);
   }
   public function setLocTo($inCID){
     if ($this->cityID == $inCID){
       return;
     }
     $this->cityID = $inCID;
     $SQL = "select tblCity.ownerID,tblCity.metroID,tblCity.StateID,tblCity.countryID,tblCity.regionID, tblCountry.worldRegionID, ";
     $SQL .= "tblCity.name city,tblMetro.name metro,tblState.name state,tblCountry.name country,";
     $SQL .= "tblRegion.name cRegion, tblWorldRegion.name wRegion ";
     $SQL .= "from tblCity  ";
     $SQL .= "left join tblState  on tblState.stateID = tblCity.StateID ";
     $SQL .= "left join tblCountry  on tblCountry.countryID = tblCity.countryID ";
     $SQL .= "left join tblWorldRegion  on tblWorldRegion.worldRegionID = tblCountry.WorldRegionID ";
     $SQL .= "left join tblRegion  on tblRegion.regionID = tblCity.regionID ";
     $SQL .= "left join tblMetro  on tblMetro.metroID = tblCity.metroID ";
     $SQL .= "where tblCity.cityID = ".$this->cityID;

     $res = mkyMsqry($SQL);
     $rec = mkyMsFetch($res);
    
     $this->ownerID   = $rec['ownerID'];
     $this->metroID   = $rec['metroID'];
     $this->stateID   = $rec['StateID'];
     $this->countryID = $rec['countryID'];
     $this->regionID  = $rec['regionID'];
     $this->wRegionID = $rec['worldRegionID'];
     $this->city      = $rec['city'];

     $this->metro = null;
     if ($rec['metro']){
       $this->metro = $rec['metro'];
     }
     $this->cRegion = null;
     if ($rec['cRegion']){
       $this->cRegion = $rec['cRegion'];
     }
     $this->state = '-';
     if ($rec['state']){
       $this->state = $rec['state'];
     }
     $this->country   = $rec['country'];
     $this->wRegion   = $rec['wRegion'];
     //$this->viewScope = $rec['viewScope'];
   }
};
function writeCache($fcache,$cache){
  if ($cache){
    echo $cache;
  }
  else {
    $cache = 'Empty';
  }
  file_put_contents($fcache,str_replace('wzID='.$GLOBALS['sKey'],'wzID=@@',$cache));
}	
function checkCache($cache,$refresh=60){
  //return null;    
  if (file_exists($cache)) {
    $ct = microtime(true) - filemtime($cache);
    if ($refresh < $ct) {
      unlink($cache);	    
      return null;
    }
    $myfile = fopen($cache, "r");
    $contents = fread($myfile,filesize($cache));
    fclose($myfile);
    if ($contents === false){
      return null;
    }
    return str_replace('wzID=@@','wzID='.$GLOBALS['sKey'],$contents);
  }
  return null;
}  
function getQry($type, $qrystr,$scope,$scopeID,$limit){
  $qry = left(prepWords($qrystr),500);
  //echo $qry;
  $SQL = "select pmacPMemOwner from ICDirectSQL.tblPeerMemoryAcc";
  $res = mkyMyqry($SQL);
  $rec = mkyMyFetch($res);
  $mbrMUID = $rec['pmacPMemOwner'];

  $j = ptreeSearchMem($mbrMUID,$qry,$type,$scope,$scopeID,$limit);
  $j = mkyStrReplace('"{','{',$j);
  $j = mkyStrReplace('}"','}',$j);
  $j = mkyStrReplace('\\"','"',$j);
  $j = mkyStrReplace('NULL','',$j);
  $r = json_decode($j); //,JSON_INVAL

  if ($r->result){
    $nrec = sizeof($r->data) -1;
    if ($nrec > 0){
      return null;
    }
    $n=1;
    $ACIDs = [];

    $fstr = '';
    forEach($r->data as $rec){
      $fstr .= $rec->pmcMemObjID."\t";
      $fstr .= $rec->pmcMemObjNWords."\t";
      $fstr .= $rec->nMatches."\t";
      $fstr .= $rec->score."'\r\n";
      $n=$n+1;
      if ($n > 20){
        break;
      }
    }
    if ($fstr == ''){
      return null;
    }  
    $tmpTable = makeTmpName(hash('sha256', $qry.$type.$limit.time()));
    $tmpFile  = '/var/www/www.bitmonky.com/wzAdmin/tmpQry'.$tmpTable.'.txt';

    if (!file_put_contents($tmpFile,$fstr)){
      echo 'fail to write query result file.'.$fstr.':'.$tmpFile;
      return null;
    }
    if (!mkyStartTransaction()){
      echo 'fail to start database transaction';
      return null;
    }

    $SQL  = "CREATE TABLE ICDirectSQL._tmp".$tmpTable." (";
    $SQL .= "  `pmcMemObjID` varchar(64) NOT NULL,";
    $SQL .= "  `pmcMemObjNWords` int(10) unsigned DEFAULT NULL,";
    $SQL .= "  `nMatches` int(10) unsigned DEFAULT NULL,";
    $SQL .= "  `score` decimal(12,9) DEFAULT NULL,";
    $SQL .= "  PRIMARY KEY (`pmcMemObjID`),";
    $SQL .= "  KEY `ndxPmcMemObjID` (`pmcMemObjID`),";
    $SQL .= "  KEY `score` (`score`)";
    $SQL .= ")ENGINE=InnoDB;";

    //$SQL .= "TRUNCATE TABLE ICDirectSQL.tmpPeerQry; ";
    $qres = mkyMyqry($SQL);

    $SQL  = "LOAD DATA LOCAL INFILE '".$tmpFile."' INTO TABLE ICDirectSQL._tmp".$tmpTable.";";
    $qres = mkyMyqry($SQL);

    unlink($tmpFile); // Delete temp text file.

    $SQL = "select firstname,mbrMUID,tags,contentOwnerID,activityID,websiteID, acCode, acLink,acItemID, ";
    $SQL .= "tblwzUserJoins.wzUserID,firstname,age,sex ";
    $SQL .= "from ICDirectSQL.tblActivityFeed ";
    $SQL .= "left join ICDirectSQL.tblActivityMemories on acmeACID = activityID ";
    $SQL .= "inner join ICDirectSQL._tmp".$tmpTable." on acmeMemHash = pmcMemObjID ";
    $SQL .= "left join tblwzUserJoins on tblwzUserJoins.wzUserID = tblActivityFeed.wzUserID ";
    $SQL .= "where suppress is null ";
    $SQL .= "group by activityID,tags,contentOwnerID,websiteID, acCode, acLink,acItemID,tblwzUserJoins.wzUserID,firstname,age,sex ";
    $SQL .= "order by score desc ";
    $qres = mkyMyqry($SQL);

    $SQL = "drop table ICDirectSQL._tmp".$tmpTable.";";
    mkyMyqry($SQL);
    mkyCommit();

    $lrec = mkyMyFetch($qres);
    return $lrec;
  }
}  
function makeTmpName($publickey){
  $step1=hexStringToByteString($publickey);
  $step2=hash("sha256",$step1);
  $step3=hash('ripemd160',hexStringToByteString($step2));
  $step4="00".$step3;
  $step5=hash("sha256",hexStringToByteString($step4));
  $step6=hash("sha256",hexStringToByteString($step5));
  $checksum=substr($step6,0,8);
  $step8=$step4.$checksum;
  // base conversion is from hex to base58 via decimal.
  // Leading hex zero converts to 1 in base58 but it is dropped
  // in the intermediate decimal stage.  Simply added back manually.

  $step9="1".bc_base58_encode(bc_hexdec($step8));
  return $step9;
}
function hexStringToByteString($hexString){
  $len=strlen($hexString);

  $byteString="";
  for ($i=0;$i<$len;$i=$i+2){
    $charnum=hexdec(substr($hexString,$i,2));
    $byteString.=chr($charnum);
  }

  return $byteString;
}
function bc_base58_encode($num) {
    return bc_arb_encode($num, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
}
function bc_hexdec($num) {
    return bc_arb_decode(strtolower($num), '0123456789abcdef');
}
function bc_arb_encode($num, $basestr) {
    if( ! function_exists('bcadd') ) {
        Throw new Exception('You need the BCmath extension.');
    }

    $base = strlen($basestr);
    $rep = '';

    while( true ){
        if( strlen($num) < 2 ) {
            if( intval($num) <= 0 ) {
                break;
            }
        }
        $rem = bcmod($num, $base);
        $rep = $basestr[intval($rem)] . $rep;
        $num = bcdiv(bcsub($num, $rem), $base);
    }
    return $rep;
}
function bc_arb_decode($num, $basestr) {
    if( ! function_exists('bcadd') ) {
        Throw new Exception('You need the BCmath extension.');
    }

    $base = strlen($basestr);
    $dec = '0';

    $num_arr = str_split((string)$num);
    $cnt = strlen($num);
    for($i=0; $i < $cnt; $i++) {
        $pos = strpos($basestr, $num_arr[$i]);
        if( $pos === false ) {
            Throw new Exception(sprintf('Unknown character %s at offset %d', $num_arr[$i], $i));
        }
        $dec = bcadd(bcmul($dec, $base), $pos);
    }
    return $dec;
}
?>

