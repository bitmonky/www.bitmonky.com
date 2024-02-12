<?php
include_once("../mkysess.php");
ini_set('display_errors',1);
error_reporting(E_ALL);

$cityID = safeGetINT('cityID');

$mkey = new mkySess($sKey,$userID);
echo "<div class='gridContain'>";
$nCard = new mkyNewsHLCard($mkey,1,$cityID);
$nCard = new mkyNewsHLCard($mkey,3,$cityID);
$nCard = new mkyNewsHLCard($mkey,4,$cityID);
$nCard = new mkyNewsHLCard($mkey,2,$cityID);
$nCard = new mkyNewsHLCard($mkey,6,$cityID);

echo "</div><p/>";

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
class mkyBanner{
  private $bannerID = null;
  private $banType = null;
  private $width   = null;
  private $height  = null;
  private $upLStat = null;
  private $udef    = null;
  private $useSBID = null;
  private $img     = null;
  private $thmb    = null;
  private $cropImg = null;
  private $bgImg   = null;
  private $bgOffset = null;
  private $margTop  = null;

  function __construct($bannerID){
    $this->bannerID = $bannerID;
    if ($bannerID){
      self::getData();
      self::selectImg();
    }
  }
  private function selectImg(){
    if ($this->useSBID){
      $SQL = "select bgName,yOffset from tblDefBackground where bgID = ".$this->useSBID;
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      $this->bgImg    = '//image.bitmonky.com/img/'.$tRec['bgName'];
      $this->bgOffset = $tRec['yOffset'];
    }
    else {
      $this->bgImg    = "//image.bitmonky.com/getStoreBGImg.php?mode=view&wzID=".$this->sess->sKey."&id=".$this->bannerID;
      $this->bgOffset = 0;
    }
    $this->margTop = '210px';
  }
  private fuction getData(){
    $SQL = "select bannerID,yoffset,udefault,useSysBanID,width,height,uploadStatus ";
    $SQL .= "from tblStoryBanner ";
    $SQL .= "where bannerID = ".$this->bannerID;
    $res = mkyMyqry($SQL);
    $rec = mkyMyFetch($SQL);
    if ($rec){
      $this->yoff    = $rec['yoffset'];
      $this->height  = $rec['height'];
      $this->width   = $rec['width'];
      $this->udef    = $rec['udefault'];
      $this->useSBID = $rec['useSysBanID'];
      $this->upLStat = $rec['uploadStatus'];
    }
  }  
};
class mkyOrg{
  private $sess = null;
  private $orgLoc = null;
  private $orgID = null;
  private $orgName = null;
  private $orgID   = null;
  
  function __construct($inSess,$inOrgID){
    $this->sess = $inSess;
    $this->orgID = inOrgID;
    self::fetchData();
  }
  private function fetchData();
    $SQL = "select mkOrgID,mkOrgOwnID,mkOrgName,mkOrgDate,mkOrgCityID ";
    $SQL .= "from tblmkyOrg  ";
    $SQL .= "where mkOrgID = ".$this->orgID;
    $res = mkyMsqry($SQL);
    $rec = mkyMsFetch($SQL);
    if ($rec){
      $this->name    = $rec['mkOrgName'];
      $this->ownID   = $rec['mkOrgOwnID'];
      $this->orgLoc  = new mkyCityLocation($rec['mkOrgCityID']; 
      $this->ordDate = $rec['mkOrdDate'];
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
     $SQL .= "inner join tblCity on cityID = acCityID ";
     $SQL .= "left  join tblState on tblCity.StateID = tblState.stateID ";
     $SQL .= "inner join tblCountry on tblCity.countryID = tblCountry.countryID ";
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
   private $metroID  = null;
   private $stateID  = null;
   private $countryID = null;
   private $regionID  = null;
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
     $SQL .= "left join tblMetro on tblMetro.metroID = tblCity.metroID ";
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

