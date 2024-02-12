<?php
include_once("../mkysess.php");
ini_set('display_errors',1);
error_reporting(E_ALL);

$cityID = safeGetINT('cityID');
$scope  = safeGET('fscope');
$acOnly = safeGET('actOnly');
$mkey = new mkySess($sKey,$userID);


makeActivtyMenu($mkey,$cityID,$scope);
if ($acOnly){
  exit('');
}

echo "<div class='gridContain'>";
$nCard = new mkyNewsHLCard($mkey,1,$cityID);
$nCard = new mkyNewsHLCard($mkey,3,$cityID);
$nCard = new mkyNewsHLCard($mkey,4,$cityID);
$nCard = new mkyNewsHLCard($mkey,2,$cityID);
$nCard = new mkyNewsHLCard($mkey,6,$cityID);

echo "</div><p/>";

function makeActivtyMenu($mkey,$cityID,$scope){
  $nItem = new mkyActivityCard($mkey,$cityID,$scope,'mBlog','Blogs');
  $nItem = new mkyActivityCard($mkey,$cityID,$scope,'mosh','MoshBox Music');
  $nItem = new mkyActivityCard($mkey,$cityID,$scope,'class','Classifieds');
  $nItem = new mkyActivityCard($mkey,$cityID,$scope,'photo','Pictures');
  $nItem = new mkyActivityCard($mkey,$cityID,$scope,'web','Websites');
  $nItem = new mkyActivityCard($mkey,$cityID,$scope,'mbrs','People');
}
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
class mkyActivityCard {
   private $nr    = null;
   private $scope = null;
   private $name  = null;
   private $mode  = null;
   private $obLoc = null;
   private $sess  = null;
   private $link  = null;
   private $searchStr      = null;
   private $userSearch     = null;
   private $eventSearch    = null;
   private $classSearchStr = null;
   private $storeSearch    = null;

   function __construct($inSess,$inCityID,$scope,$mode,$name){
     $this->scope  = $scope;
     $this->mode   = $mode;
     $this->name   = $name;
     $this->sess   = $inSess;
     $this->nr     = 4;
   
     if ($this->sess->isMob){
        $this->mbld = "mblp/";
     }
     $this->obLoc = new mkyCityLocation($inCityID);
     $this->link  = self::getLink();
     self::setScopeSearch();
     self::draw();
   }
   private function getLink($cityID=null){
     $this->link  = '<a style="color:#dcdcdc;" href="';
     $this->link .= '/whzon/mytown/myTown.php?wzID='.$this->sess->sKey;
     $this->link .= '&fmyMode='.$this->mode;
     $this->link .= '&fscope='.$this->scope;
     $this->link .= '&fwUserId='.$this->sess->UID;
     if (!$cityID){
       $this->link .= '&franCID='.$this->obLoc->cityID;
     }
     else {
       $this->link .= '&franCID='.$cityID;
     }
     $this->link .= '">';
     return $this->link;
   }
   public function draw(){
     echo $this->link;
     echo "<div class='infoCardClear'>";
     echo $this->name;
     echo "<div style='margin-top:.5em;'>";
     self::drawImages();
     echo "</div></div></a>";
   }
   private function setScopeSearch(){
     if ($this->scope == "myCity"){
       $this->searchStr      = " tblWebsites.cityID=".$this->obLoc->cityID." ";
       $this->userSearch     = " tblwzUser.cityID=".$this->obLoc->cityID." ";
       $this->eventSearch    = " tblCity.cityID=".$this->obLoc->cityID." ";
       $this->classSearchStr = " tblClassifieds.cityID=".$this->obLoc->cityID." ";
       $this->storeSearch    = " tblStore.storeCityID=".$this->obLoc->cityID;
       return;
     }

     if ($this->scope == "myState" ) {
       $this->searchStr      = " tblWebsites.stateID=".$this->obLoc->stateID." ";
       $this->classSearchStr = " tblClassifieds.stateID=".$this->obLoc->stateID." ";
       $this->userSearch     = " tblwzUser.stateID=".$this->obLoc->stateID." ";
       $this->eventSearch    = " tblCity.StateID=".$this->obLoc->stateID." ";
       $this->storeSearch    = " storeStateID=".$this->obLoc->stateID." ";
       return;
     }

     if ($this->scope == "myCountry"  || $this->scope == "" ) {
       $this->searchStr = " tblWebsites.countryID=".$this->obLoc->countryID." ";
       $this->classSearchStr = " tblClassifieds.countryID=".$this->obLoc->countryID." ";
       $this->userSearch = " tblwzUser.countryID=".$this->obLoc->countryID." ";
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
   private function drawImages(){
     if ($this->mode == 'mosh'){
       $SQL  = "SELECT  top ".($this->nr * 5)." tblMoshPit.moshPitID,tblCity.cityID, tblwzUser.wzUserID, firstname,venuName,description, ";
       $SQL .= "health, tblMoshPit.nViews, gigID FROM tblMoshPit  ";
       $SQL .= "inner join tblwzUser  on tblMoshPit.wzUserId=tblwzUser.wzUserID ";
       $SQL .= "inner join tblCity  on tblCity.cityID = tblwzUser.cityID ";
       $SQL .= "inner join tblMoshPerformance  on tblMoshPit.moshPitID=tblMoshPerformance.moshPitID ";
       $SQL .= "where ".$this->userSearch;
       $SQL .= "group by tblMoshPit.moshPitID, tblwzUser.wzuserID, firstname,venuName,description, ";
       $SQL .= "health, tblMoshPit.nViews, gigID,tblCity.cityID ";
       $SQL .= " order by rand()";
       $res = mkyMsqry($SQL);
       $rec = mkyMsFetch($res);
       while($rec){
         self::getLink($rec['cityID']);
         $SQL = "select  tblmoshSong.songID,artistID, uTubeCD, tblMoshArtist.name, title,tblMoshArtist.img from tblMoshPerformance  ";
         $SQL .= "inner join tblmoshSong  on tblmoshSong.songID=tblMoshPerformance.songID ";
         $SQL .= "inner join tblMoshArtist  on tblmoshSong.ArtistID=tblMoshArtist.moshArtistID where moshPerformanceID=".$rec['gigID'];

         $gRec = null;
         $gresult = mkyMsqry($SQL) or die($SQL);
         $gRec = mkyMsFetch($gresult);
         $uImg = getSongIMG($gRec['uTubeCD']);
         if ($uImg != '/default.jpg'){ 
           echo mkyStrReplace('">','',$this->link).'&digID='.$rec['moshPitID'].'&songID='.$gRec['songID'].'">';
           echo "<img style='height:4.5em;margin:.15em;border-radius:.25em;' src='".$uImg."'/></a>";
         }
         $rec = mkyMsFetch($res);
       }
     }
     if ($this->mode == 'mbrs'){
       $SQL  = "Select top ".($this->nr * 5)." wzUserID,cityID from tblwzUser  ";
       $SQL .= "where imgFlg=1 and ".$this->userSearch;
       $SQL .= " order by rand()";
       $res = mkyMsqry($SQL);
       $rec = mkyMsFetch($res);
       while($rec){
         self::getLink($rec['cityID']);
         echo mkyStrReplace('">','',$this->link).'&digID='.$rec['wzUserID'].'">';
         echo "<img style='height:4.5em;margin:.15em;border-radius:.25em;' src='//image.bitmonky.com/getMbrImg.php?id=".$rec['wzUserID']."'/></a>";
         $rec = mkyMsFetch($res);
       }
     }
     if ($this->mode == 'photo'){
       $SQL  = "Select top ".($this->nr * 3)." photoID,tblCity.cityID from tblwzPhoto  ";
       $SQL .= "inner join tblwzPhotoAlbum  on tblwzPhotoAlbum.wzPhotoAlbumID = tblwzPhoto.wzPhotoAlbumID ";
       $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID = tblwzPhoto.wzUserID ";
       $SQL .= "inner join tblCity  on tblCity.cityID = tblwzUser.cityID ";
       $SQL .= "where privacy < 2 and isMkdDating is null and ".$this->eventSearch;
       $SQL .= " order by rand()";
       $res = mkyMsqry($SQL);
       $rec = mkyMsFetch($res);
       while($rec){
         self::getLink($rec['cityID']);
         echo mkyStrReplace('">','',$this->link).'&digID='.$rec['photoID'].'">';
         echo "<img style='height:6em;margin:.15em;border-radius:.25em;' src='//image.bitmonky.com/getPhotoTmn.php?id=".$rec['photoID']."'/></a>";
         $rec = mkyMsFetch($res);
       }
     }
     if ($this->mode == 'class'){
       $SQL = "Select top ".$this->nr." adID,cityID from tblClassifieds  ";
       $SQL .= "where postStatus is null and itemStoreID = 0 and imgFlg = 1 and ".$this->classSearchStr;
       $SQL .= " order by rand()";
       $res = mkyMsqry($SQL);
       $rec = mkyMsFetch($res);
       while($rec){
         self::getLink($rec['cityID']);
         echo mkyStrReplace('">','',$this->link).'&digID='.$rec['adID'].'">';
         echo "<img style='height:4.5em;margin:.15em;border-radius:.25em;' src='//image.bitmonky.com/getClassTmn.php?id=".$rec['adID']."'/></a>";
         $rec = mkyMsFetch($res);
       }
     }
     if ($this->mode == 'web'){
       $SQL = "Select top ".($this->nr *3)." websiteID,tblWebsites.cityID from tblWebsites  "; 
       $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID = tblWebsites.wzUserID ";
       $SQL .= "where mrkDelete is null and not tblWebsites.wzUserID = 63555 and oldcategoryID <> 2192 and reviewed > 1 and responseCD = '200' ";
       $SQL .= "and wsImgFlg = 1 and mWebFlg=0 and ".$this->searchStr;
       $SQL .= " order by rand()";
       //$SQL .= " order by wsImgFlg desc, websiteID desc";
       $res = mkyMsqry($SQL);
       $rec = mkyMsFetch($res);
       while($rec){
         self::getLink($rec['cityID']);
         echo mkyStrReplace('">','',$this->link).'&digID='.$rec['websiteID'].'">';
         echo "<img style='height:4.5em;margin:.15em;border-radius:.25em;' src='//image.bitmonky.com/getWsImg.php?id=".$rec['websiteID']."'/></a>";
         $rec = mkyMsFetch($res);
       }
     }
     if ($this->mode == 'mBlog'){
       $SQL = "SELECT top ".($this->nr * 3)." mBlogEntryID,tblwzUser.cityID  From tblmBlogTopic  ";
       $SQL .= "inner join  tblwzUser  ON tblmBlogTopic.wzUserID = tblwzUser.wzUserID ";
       $SQL .= "inner join tblMBlogEntry  on tblmBlogTopic.mBlogTopicID = tblMBlogEntry.mBlogTopicID ";
       $SQL .= "inner join tblCity  on tblCity.cityID=tblwzUser.cityID ";
       $SQL .= "where tblMBlogEntry.imgFlg = 1 and mbStatus is null and sandBox is null and privacy is null and adultContent<>1 and spamFlg<>1  ";
       $SQL .= "and ".$this->eventSearch;
       $SQL .= " order by rand()";
       $res = mkyMsqry($SQL) or die($SQL);
       $rec = mkyMsFetch($res);
       while($rec){
         self::getLink($rec['cityID']);
         echo mkyStrReplace('">','',$this->link).'&digID='.$rec['mBlogEntryID'].'">';
         echo "<img style='height:5.5em;margin:.15em;border-radius:.25em;' src='//image.bitmonky.com/getmBlogTmn.php?id=".$rec['mBlogEntryID']."'/></a>";
         $rec = mkyMsFetch($res);
       }
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
   private $jbOwner = null;
   private $mbld    = null;
   function __construct($inSess,$inJobTID,$inCityID){
     $nHL = 3;
     $this->sess   = $inSess;
     if ($this->sess->isMob){
        $this->mbld = "mblp/";
     }
     $this->obLoc        = new mkyCityLocation($inCityID);
     $this->jbType = $inJobTID;

     $SQL = "select top ".$nHL." acCode,acItemID,headLine,acdate,tblActivityFeed.wzUserID "; 
     $SQL .= "from tblTycJobDesc  "; 
     $SQL .= "inner join tblActivityFeed  on acJobID = tycJobID ";
     $SQL .= "inner join tblCity  on cityID = acCityID ";
     $SQL .= "left  join tblState  on tblCity.StateID = tblState.stateID ";
     $SQL .= "inner join tblCountry  on tblCity.countryID = tblCountry.countryID ";
     $SQL .= "where  cityID = ".$inCityID." and tycJobID = ".$inJobTID;
     $SQL .= self::setScopeWhere();
     $SQL .= "order by acDate desc";

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
   }
   private function putCardHeader($rec){
     echo "<div class='newsHLCard'>";
     echo "<h2>".$rec['headLine']."</h2>";
   }
   private function getOBJLink(){
     if ($this->acCode == 7){
       $ank = "<a style='' href=\"/whzon/".$this->mbld."mbr/mbrViewPhotos.php?wzID=".$this->sess->sKey."&fwzUserID=".$this->jbOwner."&vPhotoID=".$this->jbItem."\">";
       return $ank;
     }
     return null;
   }
   private function getOBJRec(){
     if ($this->acCode == 7){
       $SQL = "SELECT top 1 photoID,height,width, title, phototxt from tblwzPhoto  ";
       $SQL .= "where photoID = ".$this->jbItem;
       return $SQL;
     }
     return null;
   }
   private function putHeadLine(){
     $SQL = self::getOBJRec();
     $result = mkyMsqry($SQL);
     $tRec   = mkyMsFetch($result);
     $title  = $tRec['title'];
     $desc   = left($tRec['phototxt'],200);

     $jCityAnk = "<a href='/whzon/".$this->mbld."mytown/myTownProfile.php?wzID=".$this->sess->sKey."&fwzUserID=".$this->jbOwner."&fscope=myCity&fmyMode=mbrs'>";
     $ank = self::getOBJLink();
     
     echo "<p><b style='font-size:larger;'>".$title."</b><br/><br/>";
     echo $ank;
     echo "<img style='float:left;margin:0 1em 1em 0em;width:134px;height:90px;border-radius:.5em;' ";
     echo "src='//image.bitmonky.com/getPhotoImg.php?id=".$this->jbItem."&fpv=".$this->jbOwner."'/>";
     echo "</a><span style='font-size:smaller;'>".$desc."<br/>".$ank."Read Full Story</a></span><br clear='left'/>";
     
   }
   private function putCardFooter(){
     $jCityAnk = "<a href='/whzon/".$this->mbld."mytown/myTownProfile.php?wzID=".$this->sess->sKey."&fwzUserID=".$this->jbOwner."&gID=".$this->jbType."'>";
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
   private $city      = null;
   private $metro     = null;
   private $cRegion   = null;
   private $state     = null;
   private $country   = null;
   private $wRegion   = null;
   private $viewScope = null;

   function __construct($inCID){
     $this->cityID = $inCID;
     $SQL = "select tblCity.ownerID,tblCity.metroID,tblCity.StateID,tblCity.countryID,tblCity.regionID, tblCountry.worldRegionID, ";
     $SQL .= "tblCity.name city,tblMetro.name metro,tblState.name state,tblCountry.name country,";
     $SQL .= "tblRegion.name cRegion, tblWorldRegion.name wRegion ";
     $SQL .= "from tblCity  ";
     $SQL .= "inner join tblState  on tblState.stateID = tblCity.StateID ";
     $SQL .= "inner join tblCountry  on tblCountry.countryID = tblCity.countryID ";
     $SQL .= "inner join tblWorldRegion  on tblWorldRegion.worldRegionID = tblCountry.WorldRegionID ";
     $SQL .= "left  join tblRegion  on tblRegion.regionID = tblCity.regionID ";
     $SQL .= "left join tblMetro  on tblMetro.metroID = tblCity.metroID ";
     $SQL .= "where tblCity.cityID = ".$this->cityID;

     $res = mkyMsqry($SQL);
     $rec = mkyMsFetch($res);
    
     $this->ownerID   = $rec['ownerID'];
     $this->metroID   = $rec['metroID'];
     $this->stateID   = $rec['stateID'];
     $this->countryID = $rec['countryID'];
     $this->regionID  = $rec['regionID'];
     $this->wRegionID = $rec['worldRegionID'];
     $this->city      = $rec['city'];
     $this->metro     = $rec['metro'];
     $this->cRegion   = $rec['cRegion'];
     $this->state     = $rec['state'];
     $this->country   = $rec['country'];
     $this->wRegion   = $rec['wRegion'];
     //$this->viewScope = $rec['viewScope'];
   }
};
?>

