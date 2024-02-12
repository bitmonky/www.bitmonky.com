<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../mbr/mbrData.php");
include_once("myTownInc.php");

if (isset($_GET['ferror'])){ $ferror = safeGET('ferror');} else { $ferror = null;}
if (isset($_GET['fsmCID'])){ $fsmCID = safeGET('fsmCID');} else { $fsmCID = '';}
$wzUserID = clean($_GET['fwzUserID']);
$scope    = clean($_GET['fscope']);

$SQL = "Select bannerID from ICDimages.tblStoreBanner where fieldname = fieldValue";
scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID,$scope);
$mRec = null;
$myresult = mkyMyqry($SQL);
if ($myresult){$mRec = mkyMyFetch($myresult);}
if ($mRec){
  $newBannerID = $mRec['bannerID'];
  $url = "getImgCredits.php?wzID=".$sKey."&fwzUserID=".$wzUserID."&fscope=".$scope."&fsmCID=".$fsmCID."&bannerID=".$newBannerID;
//  header('Location: '.$url);
//  exit('');
}

$SQL = "Select taskBulletinID,tskShortCd from tblTaskBulletins ";
$SQL .= "inner join tblTaskCodes on tbTaskCode = tskCodeID ";
$SQL .= "left join tblTaskBulletinRead on tbrBulletinID = taskBulletinID ";
$SQL .= "where tskShortCd = 'geoBan' and tbrReadUID = ".$userID;
$tRec = null;
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
 
if (!$tRec){
  $qstr = "?wzID=".$sKey."&fwzUserID=".$wzUserID."&fscope=".$scope."&fsmCID=".$fsmCID;
  header('Location: /whzon/mbr/tasks/bulletins/geoBan-1.php'.$qstr);
  exit('');
}

  putSession("wzSKey",$sKey);
  putSession("wzMyTownUserID",$wzUserID);
  putSession("wzMyTownScope",$scope);
  putSession("wzFsmCID",$fsmCID);
?>
<!doctype html>
<html class='pgHTM' lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Whzon.com</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>
  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
<title><?php echo $sitename;?> Load Background Picture</title>
<script>
function  startPhotoUpload(){
   var wzoutput = document.getElementById("wzLoading");
   wzoutput.innerHTML="<img onload='executeUploadImg();' style='width:35px;height;35px;' src='https://image.bitmonky.com/img/imgLoading.gif'> Loading Please Wait...";
}
function executeUploadImg(){
   document.getElementById("wzPLoadFrm").submit();
   var button = document.getElementById('uploadBut');
   button.innerHTML = "";
}
</script>
</head>
<body class='pgBody' style='margin:15px;'>
<div class='infoCardClear' style="margin-top:10px;">
<h3>Step 1.</h3>Load A Background Image For This Location From Your Computer.
<form ID='wzPLoadFrm' enctype="multipart/form-data" action="/whzon/franMgr/LoadBGImg.php" method="POST">
 
<INPUT TYPE="FILE" NAME="imgshare" style="width:95%;"><br/>
<span id='uploadBut'><INPUT style='margin-top:15px;' TYPE="button" name="shareIt" VALUE="Upload" onclick='startPhotoUpload();'/></span>
</FORM>

<div ID='wzLoading'>

<?php if ( $ferror == 'locked' ){?>
<p><font style='color:red;font-weight:bold;'>Record Locked By Another User... Try again latter.</font>
<?php } ?>

<?php if ( $ferror == 'tosmall' ){?>
  <p><font style='color:red;font-weight:bold;'>Sorry that image is to small... Backgronds must be at least 1200 x 400 pixles in size!</font>
<?php } ?>

<?php if ( $ferror == 1 ){?>
  <p><font style='color:red;font-weight:bold;'>No image file selected... Click  the 'Browse' or 'Located File' button first!</font>
<?php } ?>

<?php if ( $ferror == 2 ){?>
   <p><font style='color:red;font-weight:bold;'>The file you loaded is not a picture... Try another picture.</font>
<?php } ?>
	   
<?php if ( $ferror == 3 ){?>
   <p><font style='color:red;font-weight:bold;'>This picture is already loaded... Try another picture.</font>
<?php } ?>
</div>
<h3>What To Do</h3>
Find on the Internet or use a photo image you have created that will be a good match for the
geographic location. Then save the image on your computer.
<p/>
If you get the photo off the Internet be sure to get the information required to give credit
to the owner of the photo and a link to where the photo came from.
<p/>
If the photo is your own original work then you will give credit to yourself.
<p/>
<b>*Note</b> - The photo must be at least 1200 x 400 pixels in size.
<h3 style='color:brown;'>Your work will be reviewed! poorly selected photos will be subject to fines!</h3>
</div>
<!--
<h3>Uploaded Backgrounds</h3>
<?php
   $SQL = "select bannerID from ICDimages.tblStoreBanner where 1 = 2 and udefault is null and useSysBanID is null";

   $mRec = null;
   $myresult = mkyMyqry($SQL);
   if ($myresult){$mRec = mkyMyFetch($myresult);}
   
   if (!$mRec){
     echo "you have not uploaded any background photos...";
   }
   while ($mRec){
     echo "<a href='selectBGImg.php?wzID=".$sKey."&iType=user&id=".$mRec['bannerID']."&fstoreID=".$storeID."'>";
     echo "<img style='margin:3px;border:0px solid #777777;height:65;width:100;' src='/whzon/store/getBGTmn.php?id=".$mRec['bannerID']."'></a>";
     $mRec = mkyMyFetch($myresult);
   }
?>
<h3>bitmonky.com Backgrounds</h3>	
<?php	   
   $SQL = "select bgID,bgName from tblStoreDefBck";
   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);
   
   if (!$tRec){
     echo "no backgrounds found...";
   }
   while ($tRec){
     echo "<a href='selectBGImg.php?wzID=".$sKey."&iType=whzon&id=".$tRec['bgID']."'>";
     echo "<img style='margin:3px;border:0px solid #777777;height:65px;width:250px;' src='https://bitmonky.com/".$tRec['bgName']."'></a><br>";
     $tRec = mkyMsFetch($result);
   }
?>
-->
</body>
</html>
<?php


?>
