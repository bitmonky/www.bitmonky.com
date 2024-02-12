<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
$callID=clean($_GET['fcallID']);

$allowAlert=0;

if ($userID!=0){
    $SQL="select privateChat from tblwzUser  where wzUserID=".$userID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    
    $pvchat=$tRec['privateChat'];

    if (is_null($pvchat)){
      $pvchat=0;
    }

    if ($pvchat<2){

      $SQL= "Select ICDchat.tblvCalls.wzUserID from ICDchat.tblvCalls where pending=0 and ICDchat.tblvCalls.wzCallerID=".$userID;

      $myresult = mkyMyqry($SQL);
      $mRec = mkyMyFetch($myresult);
      if ($mRec){
        $id=$mRec['wzUserID'];

        if ($pvchat==1){
          $SQL = "select count(*) as nfrnd from tblwzUserFriends  where wzUserID=".$userID." and frienduserID=".$id." and Status=1";
          $result = mkyMsqry($SQL);
          $tRec = mkyMsFetch($result);
          if ($tRec['nfrnd']==1){
            $allowAlert=1;
          }
        }
        else{
          $allowAlert=1;
        }

        if ($allowAlert==1){
          $SQL= "Select firstname,countryID,IPCountryID from tblwzUser  where  sandBox is null and tblwzUser.wzUserID=".$id;
          $result = mkyMsqry($SQL);
          $tRec = mkyMsFetch($result);
 
          if ($tRec){
            $name=$tRec['firstname'];
            $countryID=$tRec['countryID'];
            $IPcountryID=$tRec['IPcountryID'];

            $SQL = "Select count(*) as nBlock from tblwzUserBlockList  where  wzUserID=".$userID." and ( blockUserID=".$id." or ";
            $SQL .= "BLKcountryID=".$countryID." or BLKcountryID=".$IPcountryID.");";
            $result = mkyMsqry($SQL);
            $tRec = mkyMsFetch($result);
            $nblock=$tRec['nBlock'];         

            if ($nblock==0){
              $allowAlert=$id;
            }
          }
        }
       
      }

    }
    
}


if ($allowAlert==0){
  echo "NC";
  }
else{
  echo $allowAlert;
}
?>
