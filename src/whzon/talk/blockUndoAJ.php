<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

$blkUserID = clean($_GET['fwzUserID']);
$confirm   = clean($_GET['fconfirm']);
$email="";

if ($userID!=0 && $confirm=="yes"){

  $SQL = "delete tblwzUserBlockList where sysblock is null and wzUserID=".$userID." and blockUserID=".$blkUserID;
  $result = mkyMsqry($SQL);
  echo "Unblock : ";
}

?>
done
<script>
if (parent.reloadBlocks){ 
  parent.reloadBlocks();
  parent.location.reload();
}
else{
  parent.readBlockList();
  parent.wzAPI_closeWin();
}
</script>

