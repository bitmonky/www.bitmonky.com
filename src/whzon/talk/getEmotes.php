<!doctype html>
<html class='pgHTM' lang="en">
<head>
  <meta charset="utf-8">
  <title>Whzon.com</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>
	  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <!-- SiteLOGz Code For: "chat zoom" only!  -->
  <script src='/whzon/snapLTp.php?ID=17621&wsID=5&pgID=583931'></script>
  <!-- End of SiteLOGz Code -->
<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
?>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
</head>

<body class='pgBody' style='margin:20px;'>
<div class='infoCardClear'>
<?php
$SQL = "SELECT varName,imgName,imgText,height,width ";
$SQL .= "from tblChatEmotes  ";
$SQL .= "where imgText like '~%'";

$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);

if ($tRec) {
  $pg=0;
    if ( isset($_GET['newPg'])){
      $pg = clean($_GET['newPg']);
    }
	else {
	  $pg = 0;
	}

    $nextPage = $pg; 
    $n        = $pg + 1;
    $pgcount  = 0;
    while ($tRec && $pgcount<$nextPage) {
      $pgcount = $pgcount + 1;
      $tRec = mkyMsFetch($result);
    }

    $i=0;
    $nRows=10;
    $appName="getEmotes.php";

    if ( !$tRec ) {
      echo "<h3>No More Emotes To List...</h3>";
    }

    echo "<table style='width:100%;margin:0px;margin-bottom:8px;border-collapse:collapse;border:0px solid;'>";

    while ($tRec && $i<$nRows){

      $varName  = $tRec['varName'];
      $imgName  = $tRec['imgName'];
      $imgText  = strtoupper($tRec['imgText']);
      $height   = $tRec['height'];
      $width    = $tRec['width'];
      
?>
      <tr valign='top'>
      <td style='width:100px;padding-left:0px;'>
      <img alt='<?php echo $varName;?>' style='width:<?php echo $width."px;height".$height;?>px;margin:3px;margin-right:6px;border:0px;' 
      src='//image.bitmonky.com/vChat/emoticons/<?php echo $imgName;?>'/>
      </td><td style='padding: 0 0 0 15px;'>
      <?php echo $varName;?>
      </td>
      <td style='padding: 0 0 0 15px;'>
      <?php echo mkyStrReplace('~',':',$imgText);?>
      </td>
      </tr>
<?php

      $i=$i+1;
      $n=$n+1;
      $tRec = mkyMsFetch($result);
    }//wend
    echo "</table>";

    echo "<p/><a href='".$appName."?wzID=".$sKey."&newPg=".($nextPage + $nRows)."'>Next</a>";
    if($nextPage > 0) {
      echo " | <a href='".$appName."?wzID=".$sKey."&newPg=".($nextPage - $nRows)."'>Back</a>";
    }
    echo " | <a href='".$appName."?wzID=".$sKey."&newPg=0'>Top</a>";
  }

?>
</div>
</body>
</html>
