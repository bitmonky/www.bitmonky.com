<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
$imgID = clean($_GET['fimgID']);
$mbrID = clean($_GET['fmbrID']);
?>
<!doctype html>
<html class='pgHTM' lang="en">
<head>
  <meta charset="utf-8">
  <title>BitMonk</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0">
  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
<title>SiteLogz - Add Chat Channel</title>
</head>
<body class='pgStyle' style='margin:15px;'>

       <h3 style='font-size:14px;'>Report This Photo And Block This Member</h3>
     
       <font color='red'><p>Warnning making false reports will result in your own account being deleted! </font>
       <FORM  ACTION="reportImg.php" METHOD="GET">
        Confirm Report And Block This Member<br>
          <input type='hidden' name = 'wzID'   value='<?php echo $sKey;?>'/>
          <input type='hidden' name = 'fimgID' value='<?php echo $imgID;?>'/>
          <input type='hidden' name = 'fmbrID' value='<?php echo $mbrID;?>'/>
          <input name="fconfirm" value="yes" checked="checked" type="radio"> yes 
          <input name="fconfirm" value="no" type="radio"> no 
          <input name="faction" value="Continue" type="submit">
       <div ID='wzLoading'></div>

       </FORM>


</body>
</html>
