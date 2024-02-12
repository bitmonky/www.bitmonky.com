<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
if (isset($_GET['ferror'])){$ferror=clean($_GET['ferror']);} else {$ferror=null;}
?>
<!doctype html>
<html class='pgHTM' lang="en">
<head>
  <meta charset="utf-8">
  <title>Whzon.com</title>
  <?php
  if ($sessISMOBILE){
    echo '<link rel="stylesheet" href="/whzon/mblp/mobile.css?v=1.0">';
  }
  else {
    echo '<link rel="stylesheet" href="/whzon/pc.css?v=1.0">';
  }
  ?> 
  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
<title>SiteLogz - Add Chat Channel</title>
</head>
<?php
 
    $SQL = "SELECT msg from tblChatterBox where msgID=".safeGET('fchatID');
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if ($tRec) {
      $msg = $tRec['msg'];
    }
    

?>
<body class='pgBody' style='margin:1em;font-size:larger;'>
<div class='infoCardClear'>
<b>Delete This Chat Msg :</b>

<form method="GET" action="deleteChat.php">
<input type="hidden" name="fchatID" value="<?php echo safeGET('fchatID');?>">
<input type="hidden" name="wzID" value="<?php echo $sKey;?>">

<table class='docTable'>
<tr><td> Confirm Delete this Message</td><td>
   <input type="radio" name="fconfirm" value="yes" checked> yes 
   <input type="radio" name="fconfirm" value="no" > no 
<input name="faction" type="submit" value="Delete" ></td></tr>
</table>    
</div>

</body></html>


