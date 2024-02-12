<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
$blkUserID = clean($_GET['fmbrID']);
$reasonCD  = clean($_GET['fblockCD']);
$confirm   = clean($_GET['fconfirm']);
$email="";
$IsMod=null;


if ($userID!=0 && $confirm=="yes"){

  $SQL="Select email, moderator from tblwzUser  where wzUserID=".$blkUserID;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  if ($tRec) {
    $email=$tRec['email'];
    $IsMod=$tRec['moderator'];
  }
  
  if (is_null($IsMod)){
    $IsMod=0;
  }

  if ($IsMod < 2){

     $SQL="select count(*) as nRec from tblwzUserBlockList  where wzUserId=".$userID." and blockUserID=".$blkUserID;
     $result = mkyMsqry($SQL);
     $tRec = mkyMsFetch($result);
     $nBlocked=$tRec['nRec'];

     if ($nBlocked==0){
       $SQL = "insert into tblwzUserBlockList (wzUserID,blockUserID,email,reasonCD)  values (".$userID.",".$blkUserID.",'".$email."',".$reasonCD.")";
       $result = mkyMsqry($SQL);
       echo "block : ";
     }
     echo "done";
   }
   else{
     echo "You Can't Block A System Admin!";
   }
}
else{
  echo "done";
}

?>
<br>done
<script>
parent.reloadBlocks();
parent.location.reload();
</script>
