<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");


$imgID    = clean($_GET['fimgID']);
$mbrID    = clean($_GET['fmbrID']);
$fconfirm = clean($_GET['fconfirm']);

if ($fconfirm == 'yes'){
  $SQL = "update ICDchat.tblChatSharedPic set repDate = now(),reportedBy=".$userID." Where sharePicID=".$imgID;
  $myresult = mkyMyqry($SQL);

  $SQL = "select email from tblwzUser  where wzUserID =".$mbrID;
  $result   = mkyMsqry($SQL);
  $tRec     = mkyMsFetch($result);
  $mbrEmail = $tRec['email'];

  $SQL = "select count(*) as nblocks from tblwzUserBlockList  where wzUserID = ".$userID." and blockUserID = ".$mbrID;
  $result   = mkyMsqry($SQL);
  $tRec     = mkyMsFetch($result);
  $nblocks  = $tRec['nblocks'];

  if ($nblocks == 0){
    $SQL = "insert into tblwzUserBlockList (wzUserID,blockUserID,email,reasonCD) ";
    $SQL .= "values (".$userID.",".$mbrID.",'".$mbrEmail."',6)";
    $result = mkyMsqry($SQL);
  }

  
  
}
?>
<html>
<head>
<script>
  function closePop(){
    parent.parent.wzAPI_closeWin();
}
</script>		
</head>
<body onload="setTimeout('closePop()',3*1000)">
  <?php if ($fconfirm == 'yes'){?>
	<h2>User Image Share Reported</h2>
	This user has been blocked and reported!
  <?php } else {?>
	<h2>Report Canceled...</h2>
  <?php }?>
</body>
</htm>
