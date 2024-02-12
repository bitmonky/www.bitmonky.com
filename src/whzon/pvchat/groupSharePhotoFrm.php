<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");

setcookie( "wzSKey", $sKey, 0, "/", ".".$whzdom );
if (isset($_GET['ferror'])){$ferror = safeGET('ferror');}else{$ferror = null;}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>BitMonk</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0">
  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
<title>SiteLogz - Add Chat Channel</title>
<script>
function  startPhotoUpload(){
   var wzoutput = document.getElementById("wzLoading");
   wzoutput.innerHTML="<img onload='executeUploadImg();' style='width:35px;height;35px;' src='//image.bitmonky.com/img/imgLoading.gif'> Loading Please Wait...";
}
function executeUploadImg(){
   document.getElementById("wzPLoadFrm").submit();
}
</script>
</head>
<body style='background:white;margin:15px;'>



       <h1 style='font-size:14px;'>Choose Photo To Share With This Group</h1>
     
       <font color='red'><p>Warnning the  people your sharing with can copy and save this photo!</font>
       <FORM ID='wzPLoadFrm' ENCTYPE="multipart/form-data" ACTION="groupLoadSharePhoto.php" METHOD="POST">
       <INPUT TYPE="FILE" NAME="imgshare">
       <INPUT TYPE="button" name="shareIt"  VALUE="Upload" onclick='startPhotoUpload();'> <a href='javascript:parent.wzAPI_closeWin();'>Cancel</a>

       <?php if ($ferror){?>
         <p><font style='color:red;font-size:12px;font-weight:bold;'><?php echo $ferror;?>... Try another picture.</font>
       <?php }?>
       <div ID='wzLoading'></div>

       </FORM>


</body>
</html>
