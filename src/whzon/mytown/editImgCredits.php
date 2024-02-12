<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

if (isset($_GET['ferror'])){ $ferror = safeGET('ferror');} else { $ferror = null;}
if (isset($_GET['bannerID'])){$newBannerID = safeGET('bannerID');} else {$newBannerID=null;}

$wzUserID = clean($_GET['fwzUserID']);
$scope    = clean($_GET['fscope']);
?>
<!doctype html>
<html lang="en">
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
function refreshEditor(){
  parent.wzGetPage('/whzon/mytown/myTownEditor.php?mode=edit&wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $wzUserID.'&fscope='.$scope;?>');
  parent.showBannerControls();
}
function resetEditor(){
  parent.wzGetPage('/whzon/mytown/myTownEditor.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $wzUserID.'&fscope='.$scope;?>');
  parent.showBannerControls();
}
</script>
</head>
<body style='background:white;margin:15px;' <?php if=(isset($_GET['fmoder'])) {resetEditor();} else {echo "onload='refreshEditor();}'"?>>
	<div style="margin-top:10px;padding:10px;padding-left:5px;background:#f9f9f9;border-radius: .5em;border:0px solid #efefef;">
    <?php
	if (isset($_GET['fmoder'])){
	  echo "<h2>Thank You Task Complete!</h2>";
	  echo "your bitmonky.com account has been updated...";
	}
	else {
	?>
	<h3>Step 2.</h3>
	Place your mouse over the image and use the arrow keys to scroll the image up or down in the background.
	until your happy with the results... Then click save.
	</div>
	<p/>
	<div style="margin-top:10px;padding:10px;padding-left:5px;background:#f9f9f9;border-radius: .5em;border:0px solid #efefef;">
	<h3>Step 3.</h3>Enter Copy Right Credtis For This Image:
	<p/>
	<form ID='wzPLoadFrm' enctype="multipart/form-data" action="updateCopyRight.php" method="GET">
    <input type="hidden" name="fwzUserID" value="<?php echo $wzUserID;?>"/>
    <input type="hidden" name="fscope"    value="<?php echo $scope;?>"/>
    <input type="hidden" name="bannerID"  value="<?php echo $newBannerID;?>"/>
	<input type="hidden" name="wzID"      value="<?php echo $sKey;?>"/>

		<INPUT style="width:98%;border-radius:0.4em;" TYPE="text" NAME="cprText" maxlength="80"  placeholder="Credit This Photo To?"/><br/>
    <INPUT style="width:98%;border-radius:0.4em;" TYPE="text" NAME="cprURL"  maxlength="250" placeholder="link to source https://?"/><br/>
    <span id='uploadBut'><INPUT style='margin-top:15px;' TYPE="button" name="shareIt" VALUE="Finish This Task" onclick='startPhotoUpload();'/></span>
    </FORM>
	
    <div ID='wzLoading'>
	<?php if ( $ferror == 'noText' ){?>
    <p><font style='color:red;font-weight:bold;'>You must give a credit for this image.. if it is your credit your self</font>
    <?php } ?>

    <?php if ( $ferror == 'badURL' ){?>
      <p><font style='color:red;font-weight:bold;'>That URL is not valid... check and try again!</font>
    <?php }
    }
?>

  </div>
</div>


</body>
</html>
<?php


?>
