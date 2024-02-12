<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");

$ferror=null;
$dstamp=mkySQLDstamp();

if (!$userID==0){
  if(isset($_POST['shareIt']) || 1==1) {

    $filename = $_FILES['imgshare']['name'];
    $tmpname  = $_FILES['imgshare']['tmp_name'];
    $imgsize  = $_FILES['imgshare']['size'];
    $imgtype  = $_FILES['imgshare']['type'];
    $status   = $_FILES['imgshare']['error'];

    $permitted = array('image/gif', 'image/jpeg', 'image/pjpeg','image/png','image/jpg');


    //begin upload
    if($imgsize > 0 && $imgsize < 28000000 && $status==0) {
      $handle = fopen($tmpname, "r");
      $content = fread($handle, filesize($tmpname));
      $md5ID = md5($content);

      $image = imagecreatefromstring($content);

      $w = ImageSX($image); 
      $h = ImageSY($image); 
      if ($w > 600){$iw=600;} else {$iw=$w;}
      $scale = $w/$iw;
      $ih    = (int)$h / $scale;

      $new = imagecreatetruecolor($iw, $ih);
      $tbn = imagecreatetruecolor(100, 65);

      imagecopyresampled($new, $image, 0, 0, 0, 0, $iw, $ih, $w, $h);
      ob_start();
      imagepng($new);
      $fullImg = addslashes(ob_get_contents());
      ob_end_clean();
      imagedestroy($new); 
           
      imagecopyresampled($tbn, $image, 0, 0, 0, 0, 100, 65, $w, $h);
      ob_start();
      imagepng($tbn);
      $thmnail = addslashes(ob_get_contents());
      ob_end_clean();
      imagedestroy($tbn); 
      
      $SQL = "select count(*) as nRec from ICDimages.tblpornMd5log where md5ID = '".$md5ID."'";
      $mRec = null;
      $myresult = mkyMyqry($SQL) or die($SQL);
      $mRec = mkyMyFetch($myresult);

      if ($mRec['nRec'] == 0){
        $SQL = "select sdate from ICDchat.tblChatSharedPic where md5ID = '".$md5ID."' and wzUserID=".$userID." and shareID=".$inPVCwithID;
        $mRec = null;
        $myresult = mkyMyqry($SQL) or die($SQL);
        $mRec = mkyMyFetch($myresult);
     
        if ($mRec) {
          $dstamp = $mRec['sdate'];
        }
        else {
          $SQL = "insert into ICDchat.tblChatSharedPic(wzUserID,shareID,img,sdate,thmnail,md5ID)";
          $SQL .= " values(".$userID.",".$inPVCwithID.",'".$fullImg."','".$dstamp."','".$thmnail."','".$md5ID."')";
          $myresult = mkyMyqry($SQL);

          $SQL = "select sharePicID from ICDchat.tblChatSharedPic where md5ID = '".$md5ID."' and wzUserID=".$userID." and shareID=".$inPVCwithID;
          $mRec = null;
          $myresult = mkyMyqry($SQL) or die($SQL);
          $mRec = mkyMyFetch($myresult);
          $newPicID = $mRec['sharePicID'];
          
          $SQL = "insert into tblwzUploadLog(pshareID,upMd5ID) values (".$newPicID.",'".$md5ID."')";
          $result = mkyMsqry($SQL);
        }
      }
      else {
        $ferror = "Image Share Refused";
      }
    }
    else {
      $ferror= "Not An Image File!".$imgsize.":".$imgtype.":".$status.":".$filename;
    }
  }
  else {
    $ferror="Improper use of script";
  }
}
else {
  $ferror="No Session";
}

if ($ferror){
  
  
  header("Location: popfrmSharePhoto.php?wzID=".$sKey."&ferror=".mkyUrlEncode($ferror));
}
 
$result=reportShare($userID,$inPVCwithID,$dstamp);



function reportShare($userID,$shareID,$dstamp){
   $SQL = "Select sharePicID from ICDchat.tblChatSharedPic where wzUserID=".$userID." and sdate='".$dstamp."'";
   $myresult = mkyMyqry($SQL);
   $mRec = mkyMyFetch($myresult);
   $photoID=$mRec['sharePicID'];

   $sharetxt= "<table style=\"".mkyUrlEncode('width:100%;')."\"><tr valign=\"top\"><td>has shared this photo with you<br></td><td style=\"padding-left:1.5em;\" width=\"87\">";
   $sharetxt .= "<a href=javascript:parent.pshare(".$photoID.",\"".mkyUrlEncode($dstamp)."\")>";
   $sharetxt .= "<img style=\"border-radius:0.5em;\" ";
   $sharetxt .= "src=\"//image.bitmonky.com/getPVPhotoTmn.php?id=".$photoID."&ck=".mkyUrlEncode($dstamp)."\"></a>";
   $sharetxt .= "<br><a title=\"Block User And Report This Photo!\" href=\"javascript:parent.popReportImg(".$photoID.")\">Report This!</a></td></tr></table>"; 
             
   $SQL = "INSERT INTO ICDchat.tblMbrChat (msg,msgUserID,msgMbrID,sentBY) values('".left($sharetxt,600)."',".$userID.",".$shareID.",".$userID.")";
   $myresult = mkyMyqry($SQL);

   $sharetxt= "<table style=\"".mkyUrlEncode('width:100%;')."\"><tr valign=\"top\"><td>has shared this photo with you<br></td><td style=\"padding-left:1.5em;\" width=\"87\">";
   $sharetxt .= "<a href=javascript:parent.pshare(".$photoID.",\"".mkyUrlEncode($dstamp)."\")>";
   $sharetxt .= "<img style=\"border-radius:.5em;\" ";
   $sharetxt .= "src=\"//image.bitmonky.com/getPVPhotoTmn.php?id=".$photoID."&ck=".mkyUrlEncode($dstamp)."\"></a>";
   $sharetxt .= "</td></tr></table>"; 
 
   return $sharetxt;
}
?>
<html><body>
<script>

    parent.displayShare('<?php echo $result;?>');
    parent.wzAPI_closeWin('wzAppsContainer');
</script>
</body></html>
