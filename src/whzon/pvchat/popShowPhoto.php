<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
$ID=safeGET('id');
$CK=safeGET('ck');
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
</HEAD>
<BODY style='background:black;'>
<center>
<img src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getPVPhoto.php?id=<?php echo $ID;?>&ck=<?php echo $CK;?>'/>
</center>
</body>
</html>

