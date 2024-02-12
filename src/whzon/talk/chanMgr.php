<?php
class chanMgr {
  private $chanID;
  private $userID;
  private $isOwner;
  private $sKey;
  private $isMobile;
  private $isDating;
  private $isPrivate;
  
  private $tklink;
  private $chanType;
  private $URL;
  private $chanWSID;
  private $smNewsID;
  private $smNewsLinksID;
  private $photoID;
  private $mBlogEID;
  private $ownerID;
  private $mkdID;
  private $mkdNic;
  private $ownerName;
  private $mNewsID;
  private $privateChan;
  private $spoken;
  private $videoID;
  private $hcoID;
  private $mkapID;
  private $pgURL;
  private $thumb;
  private $SQL;
  private $title;
  private $cityID;
  private $storeID;

  function __construct($inChanID,$inDateChan,$inUID,$inSkey,$inSessType){
    $this->chanID   = $inChanID;
    $this->userID   = $inUID;
    $this->sKey     = $inSkey;
    $this->isMobile = $inSessType;
    $this->isDating = false;
    if ($inChanID == $inDateChan){
      $this->isDating = true;
    }
    $isBlock=0;

    $SQL  = "Select chanCityID,chanMkapID,chanHcoID,spoken,firstname, privateChan,chanType,name,guide,ownerID,mkdID,mkdNic, ";
    $SQL .= "websiteID, URL,link, smNewsID,smNewsLinksID, mNewsID,chanStoreID, ";
    $SQL .= "photoID, videoID, mBlogEID, tblChatChannel.img from tblChatChannel  ";
    $SQL .= "left join tblwzUser  on tblwzUser.wzUserID=ownerID ";
    $SQL .= "left join tblWebsites  on websiteID=chanWSID  ";
    $SQL .= "left join tblMkyDating  on mkdUID = tblwzUser.wzUserID ";
    $SQL .= "where tblChatChannel.channelID=".$this->chanID;
    $this->SQL = null;//$SQL;

    $result = mkyMsqry($SQL);
    if ($row = mkyMsFetch($result)){
      $this->tklink        = $row['link'];
      $this->chanType      = $row['chanType'];

      if ($this->isDating){
        $this->title = realTimeDNic($row['name']);
      }  
      else {
        $this->title = realTimeNic($row['name']);
      }

      $this->URL           = $row['URL'];
      $this->chanWSID      = $row['websiteID'];
      $this->smNewsID      = $row['smNewsID'];
      $this->smNewsLinksID = $row['smNewsLinksID'];
      $this->photoID       = $row['photoID'];
      $this->mBlogEID      = $row['mBlogEID'];
      $this->ownerID       = $row['ownerID'];
      $this->mkdID         = $row['mkdID'];
      $this->mkdNic        = $row['mkdNic'];
      $this->ownerName     = $row['firstname'];
      $this->mNewsID       = $row['mNewsID'];
      $this->privateChan   = $row['privateChan'];
      $this->spoken        = $row['spoken'];
      $this->videoID       = $row['videoID'];
      $this->hcoID         = $row['chanHcoID'];
      $this->mkapID        = $row['chanMkapID'];
      $this->pgURL         = $this->getAppURL();
      $this->thumb         = $this->getThumb();
      $this->isPrivate     = $row['privateChan'];
      $this->storeID       = $row['chanStoreID'];
      $this->cityID        = $row['chanCityID'];
    }
    $this->isOwner = False;
    if ($this->userID == $this->ownerID){
      $this->isOwner = true;
    }

    if (is_null($this->ownerID)){
      $this->ownerID = 0;
    }
  }

  public function getAppURL(){
     if ($this->chanID == 1){
       return '/';
     }
     if ($this->isDating){
       return '/whzon/apps/dating/appDating.php?wzID='.$this->sKey;
     }
     if ($this->chanType == 'mbrProf'){
       if ($this->isMobile){
         return '/whzon/mblp/mbrProfile.php?wzID='.$this->sKey.'&fwzUserID='.$this->ownerID;
       }
       return '/whzon/mbr/mbrProfile.php?wzID='.$this->sKey.'&fwzUserID='.$this->ownerID;
     }
     if ($this->chanType == 'HTag'){
       if ($this->isMobile){
         return '/whzon/mblp/homepg.php?wzID='.$this->sKey.'&wzCID='.$this->chanID.'&fhQry='.mkyStrReplace('#','',$this->title);
       }
       return '/whzon/public/homepg.php?wzID='.$this->sKey.'&wzCID='.$this->chanID.'&fhQry='.mkyStrReplace('#','',$this->title);
     }
     if ($this->chanType == 'Store'){
       if ($this->isMobile){
         return '/';
       }
       return '/whzon/store/storeProfile.php?wzID='.$this->sKey.'&fstoreID='.$this->storeID;
     }
     if ($this->chanType == 'City'){
       if ($this->isMobile){
         return '/whzon/mblp/mytown/myTownChangeTo.php?wzID='.$this->sKey.'&fcityID='.$this->cityID;
       }
       return '/whzon/mytown/myTownChangeTo.php?wzID='.$this->sKey.'&fcityID='.$this->cityID;
     }
     return '/';
  }
  public function getThumb(){

     // *******************************************
     // All other channel types return the owners image;
     // *******************************************
     if ($this->isDating){
        return '//image.bitmonky.com/getDProfThm.php?id='.$this->mkdID;
     }
    
     return '//image.bitmonky.com/getMbrImg.php?id='.$this->ownerID;   
  }
    public function mkyDump() {
        var_dump(get_object_vars($this));
    }
};
?>


















