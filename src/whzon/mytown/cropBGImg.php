!<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

   $imgID = clean($_GET['id']);
   $yOff  = clean($_GET['yoff']);
            
   $SQL = "Select img as nrec, width from ICDimages.tblStoreBanner where bannerID=".$imgID ;
   $mRec = null;
   $myresult = mkyMyqry($SQL);
   if ($myresult){$mRec = mkyMyFetch($myresult);}
   $width = 1100;
   if ($mRec['width']){
     $width = $mRec['width'];
   }
   $img = imagecreatefromstring($mRec['nrec']);
   $dest = imagecreatetruecolor($width, 220);
   imagecopy($dest, $img, 0, 0, 0, $yOff, $width, 220);
  
   ob_start();
   imagepng($dest);
   $fullImg = addslashes(ob_get_contents());
   ob_end_clean();
   imagedestroy($dest); 

   $SQL = "update ICDimages.tblStoreBanner set uploadStatus=1,cropMd5ID = null, cropImg = '".$fullImg."', yoffset= ".$yOff." where bannerID=".$imgID;
   $myresult = mkyMyqry($SQL);

   
   
?>
OK
