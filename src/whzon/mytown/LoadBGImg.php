<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

$newBannerID = null;

if ($userID != 0){
  $ferror=null;
  $wzUserID  = getSess('wzMyTownUserID');
  $scope     = getSess('wzMyTownScope');
  $fsmCID    = getSess('wzFsmCID');
  
  $SQL = "select  tblCity.cityID,tblCity.StateID,tblCity.countryID,tblCity.metroID from tblwzUser ";
  $SQL .= "inner join tblCity on tblCity.cityID = tblwzUser.cityID ";
  $SQL .= "where wzUserID = ".$wzUserID;

  if ($fsmCID != ''){
    $SQL = "select cityID,stateID,countryID,metroID from tblCity where cityID=".$fsmCID;
  }
  
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  
  $cityID    = $tRec['cityID'];
  $metroID   = $tRec['metroID'];
  $stateID   = $tRec['stateID'];
  $countryID = $tRec['countryID'];
  
  $lockKey = "lb:fieldname.fieldValue";
  scopeSQL($lockKey,$cityID,$stateID,$countryID,$metroID);
  if (!wzAppLock($lockKey)){
    header('Location: popLoadBGImg.php?ferror=locked&wzID='.$sKey.'&fwzUserID='.$wzUserID.'&fscope='.$scope);
    
    
    exit('');
  }
  
  if ($wzUserID == ""){
    exit("invalid myTownUserID");
  }
  
  if (!$userID==0){
    if(isset($_POST['shareIt']) || 1==1) {

      $filename = $_FILES['imgshare']['name'];
      $tmpname  = $_FILES['imgshare']['tmp_name'];
      $imgsize  = $_FILES['imgshare']['size'];
      $imgtype  = $_FILES['imgshare']['type'];
      $status   = $_FILES['imgshare']['error'];
	  
      $permitted = array('image/gif', 'image/jpeg', 'image/pjpeg','image/png','image/jpg');

      $datestamp = mkySQLDstamp();
      $logID = 0;
	  
      $SQL = "insert into tblUploadLog(name,tname,imgsize,imgtype,status,dstamp,device) ";
      $SQL .= "values ('".$filename."','".$tmpname."','".$imgsize."','".$imgtype."','".$status."','".$datestamp."','*pc')";
      $result = mkyMsqry($SQL);
	  
      $SQL = "select upLoadID from tblUploadLog where dstamp = '".$datestamp."' and name = '".$filename."'";
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      if ($tRec) {
        $logID = $tRec['upLoadID'];
      }


      //begin upload
      if($imgsize > 0 && $imgsize < 28000000 && $status==0) {
        $handle = fopen($tmpname, "r");
        $content = fread($handle, filesize($tmpname));

        $image = imagecreatefromstring($content);
        $w = ImageSX($image); 
        $h = ImageSY($image); 
        
        $width = 1100;
        
        if ($w < $width || $h < 240) {
          header('Location: popLoadBGImg.php?ferror=tosmall&wzID='.$sKey.'&fwzUserID='.$wzUserID.'&fscope='.$scope);
          wzAppUnLock($lockKey);
          
          
          exit('');
        }   
        
        $iw = $w;

        if ($w > $width){
          $iw = $width;
        }

        $scale = $w/$iw;
        $ih    = intval($h / $scale);

        $new = imagecreatetruecolor($iw, $ih);

        imagecopyresampled($new, $image, 0, 0, 0, 0, $iw, $ih, $w, $h);
        ob_start();
        imagepng($new);
        $fullImg = ob_get_contents();
        $imageCrp = imagecreatefromstring($fullImg);
        $fullImg = addslashes($fullImg);
        ob_end_clean();
        imagedestroy($new); 
        
        $new = imagecreatetruecolor($width, 220);
        imagecopy($new, $imageCrp, 0, 0, 0, 0, $width, 220);
        
        ob_start();
        imagepng($new);
        $cropImg = addslashes(ob_get_contents());
        ob_end_clean();
        imagedestroy($new); 

        $new = imagecreatetruecolor(100, 65);

        imagecopyresampled($new, $image, 0, 0, 0, 0, 100, 65, $w, $h);
        ob_start();
        imagepng($new);
        $thmb = addslashes(ob_get_contents());
        ob_end_clean();
        imagedestroy($new); 

        $imgMd5ID = md5($fullImg);
        $SQL = "Select count(*) as nrec from ICDimages.tblStoreBanner where fieldname = fieldValue and imgMd5ID = '".$imgMd5ID."'";
        scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID);
        $mRec = null;
        $myresult = mkyMyqry($SQL);
        if ($myresult){$mRec = mkyMyFetch($myresult);}
		
	$nrec = $mRec['nrec'];
		
	if ($nrec == 0 ) {
          $SQL = "update ICDimages.tblStoreBanner set udefault = null where fieldname = fieldValue";
          scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID);
          $myresult = mkyMyqry($SQL) or die('Bad Query - '.$SQL);

          $SQL = "insert into ICDimages.tblStoreBanner (bannerUID,fieldname,height,width,imgMd5ID,img) ";
          $SQL .= " values(".$userID.",fieldValue,".$ih.",".$width.",'".$imgMd5ID."','".$fullImg."')";
          scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID);
          $myresult = mkyMyqry($SQL) or die('Bad Query - on insert'.$SQL);

          $SQL = "update ICDimages.tblStoreBanner set cropImg = '".$cropImg."' where fieldname = fieldValue and imgMd5ID = '".$imgMd5ID."'";
          scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID);
          $myresult = mkyMyqry($SQL) or die('Bad Query - '.$SQL);

          $SQL = "update ICDimages.tblStoreBanner set uploadStatus = 1,udefault = 1, thmb = '".$thmb."' where fieldname = fieldValue and imgMd5ID = '".$imgMd5ID."'";
          scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID);
          $myresult = mkyMyqry($SQL) or die('Bad Query - on insert'.$SQL);

          $SQL = "select bannerID from ICDimages.tblStoreBanner where fieldname = fieldValue and imgMd5ID = '".$imgMd5ID."'";
          scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID);
          $mRec = null;
          $myresult = mkyMyqry($SQL) or die('Bad Query - image to '.$SQL);
          if ($myresult){$mRec = mkyMyFetch($myresult);}
          $newBannerID = $mRec['bannerID'];

          $SQL = "insert into tblCopyRightCredits (cprAcCode,cprAcItemID,cprText,cprURL) ";
          $SQL .= "values (20,".$newBannerID.",null,null)";
          $result = mkyMsqry($SQL) or die($SQL);
          
	  $SQL = "update tablename set bannerID = ".$newBannerID." where tablekey = fieldValue";
          scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID);
          $result = mkyMsqry($SQL) or die($SQL);
		  
	  if ($scope == "myCity"){
	    $SQL = "update tblTaskCities set taskUID = ".$userID.", taskBannerID = ".$newBannerID." where taskCityID = ".$cityID;
            $result = mkyMsqry($SQL);
	  }

          if ($scope == "myState"){
	    $SQL = "update tblTaskCities set taskUID = ".$userID.", taskBannerID = ".$newBannerID." where taskStateID = ".$stateID;
            $result = mkyMsqry($SQL);
	  }

          $SQL = "insert into tblwzUploadLog(oLogID,geoBanner) values (".$logID.",".$newBannerID.")";
          $result = mkyMsqry($SQL);
        
	}
	else {
          header('Location: popLoadBGImg.php?wzID='.$sKey.'&ferror=3&fwzUserID='.$wzUserID.'&fscope='.$scope);
          wzAppUnLock($lockKey);
          
          
          exit('');
        }
	    
        
      }
      else {
        header('Location: popLoadBGImg.php?wzID='.$sKey.'&ferror=2&fwzUserID='.$wzUserID.'&fscope='.$scope);
        wzAppUnLock($lockKey);
        
	exit('');
      }
    }
    else{
      header('Location: popLoadBGImg.php?wzID='.$sKey.'&ferror=2&fwzUserID='.$wzUserID.'&fscope='.$scope);
      wzAppUnLock($lockKey);
      
      exit('');
    }
  }
}
wzAppUnLock($lockKey);


header('Location:  getImgCredits.php?wzID='.$sKey.'&fwzUserID='.$wzUserID.'&fscope='.$scope.'&bannerID='.$newBannerID);

function scopeSQL(&$SQL,$cityID,$stateID,$countryID,$metroID){
    global $scope;
    global $metroID;
    if ($scope == "myCity"){
      $SQL = str_replace ("fieldname","bnCityID",$SQL);
      $SQL = str_replace ("fieldValue",$cityID,$SQL);
      $SQL = str_replace ("tablename","tblCity",$SQL);
      $SQL = str_replace ("tablekey","cityID",$SQL);
    }

//    if ($metroID != 0){
//      $SQL = str_replace ("fieldname","bnMetroID",$SQL);
//      $SQL = str_replace ("fieldValue",$metroID,$SQL);
//    }

    if ($scope == "myState" ) {
      $SQL = str_replace ("fieldname","bnStateID",$SQL);
      $SQL = str_replace ("fieldValue",$stateID,$SQL);
      $SQL = str_replace ("tablename","tblState",$SQL);
      $SQL = str_replace ("tablekey","stateID",$SQL);
    }

    if ($scope == "myCountry"  || $scope == "" ) {
      $SQL = str_replace ("fieldname","bnCountryID",$SQL);
      $SQL = str_replace ("fieldValue",$countryID,$SQL);
      $SQL = str_replace ("tablename","tblCountry",$SQL);
      $SQL = str_replace ("tablekey","countryID",$SQL);
    }
    if ($scope == "myWorld"  || $scope == "" ) {
      $SQL = str_replace ("fieldname","bnWorld",$SQL);
      $SQL = str_replace ("fieldValue",0,$SQL);
    }

    if ($scope == "myWorld" ) {
    }
}
?>
