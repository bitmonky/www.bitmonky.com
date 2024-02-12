<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

if (isset($_GET['ferror'])){$ferror=clean($_GET['ferror']);} else {$ferror=null;}
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
<!-- SiteLOGz Code For: "Talk About Add Chan Form" only!  -->
<script src='/whzon/snapLTp.php?ID=17621&wsID=5&pgID=582148'></script>
<!-- End of SiteLOGz Code -->
</head>
<body style='background:white;margin:0px;'>
<table style='width:100%;'>
  <tr valign='bottom'>
    <td colspan='2' style='background:#222222;padding-left:15px;padding-bottom:5px;'>
      <div style='white-space:nowrap; margin:0px;padding:0px;'>
        <font style='FONT-FAMILY: Impact;font-size:20px;font-weight:normal;color:#8ec634;'> Bit<font style='color:#ffffff;'>Monky</font><font style='color:#eb9b52;'></font></font>
        </font><font style='font-size:14px;font-weight:normal;color:#cb0051;'>Private Chat
        </font>
		  <div ID='myAlerts'></div>
	  </div>
    </td>
  </tr>
</table>
<div style='padding:20px;padding-bottom:0px;'>
	<a href='javascript:window.history.back();'>[Cancel]</a>
</div>	
<?php
   if ($ferror==1){
     echo "<span style='color:red;'>You Must Give Your Group A Name!</span>";
  }

?>

   <form style='margin:15px;' method="GET" action="groupAddGroup.php?">
     <input type='hidden' name='wzID' value='<?php echo $sKey;?>'>
 
      <p><b>BitMonk | Create A Private Chat Group</b> 
      <p>
      <table>
        <tr>
          <td>Group Name</td>
          <td><input name="fgname" size="40" maxlength="40"></td>
        </tr>
        <tr>
          <td>Channel Description</td>
          <td><input name="fgdesc" size="40" maxlength="240"></td>
        </tr>
      </table>

      <p><input name="fsubmit" type="submit"
      value="Add Group"> </p>
    </form>

</body>
</html>

