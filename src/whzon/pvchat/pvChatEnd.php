<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
if (isset($_GET['fmbrID'])){$mbrID = clean($_GET['fmbrID']);} else {$mbrID = 0;}
if (isset($_GET['fcpg'])){$callPg = clean($_GET['fcpg']);} else {$callPg = null;}


    $SQL = "Delete from ICDchat.tblMbrChat where groupID  is null and msgMbrID=".$userID." and msgUserID = ".$mbrID;
    $myresult = mkyMyqry($SQL);

    $SQL = "Delete from ICDchat.tblMbrChat where groupID is null and msgMbrID=".$mbrID." and msgUserID = ".$userID;
    $myresult = mkyMyqry($SQL);

    $doit = true;
    
    $SQL = "Select count(*) as nRec from ICDchat.tblMbrChat where msgMbrID=".$userID;
    $myresult = mkyMyqry($SQL);
    $mRec = mkyMyFetch($myresult);
    
    $maxn = $mRec['nRec'];
    $n = 0;
    if ($maxn == 0){
      $sentBy = 0;
    }
    while ($doit && $n < $maxn){
      $n = $n + 1;
      $SQL = "Select msgUserID from ICDchat.tblMbrChat where msgMbrID=".$userID." Limit 1";
      $myresult = mkyMyqry($SQL);
      $mRec = mkyMyFetch($myresult);
    
      if (!$mRec){
        $doit   = null;
        $sentBy = 0;
        $SQL = "update tblwzOnline set privateChat = null where wzUserID=".$userID;
        $result = mkyMsqry($SQL);
      }
      else {
        $sentBy = $mRec['msgUserID'];
        $SQL = "select count(*) as nRec from tblwzUser  where sandBox is null and wzUserID = ".$sentBy;
        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);
        if ($tRec['nRec'] == 0){
          $SQL = "delete from ICDchat.tblMbrChat where msgUserID = ".$sentBy." or msgMbrID = ".$sentBy;     
          $myresult = mkyMyqry($SQL);
          $sentBy = 0;
        }
      }
    }
    
    $sentBy=0;


if ($callPg=="pv"){?>
  <HTML>
  <script>
  <?php if ($sentBy==0) {?>
    parent.wzAPI_closePVC();
  <?php } else { ?>
    window.location.href='pvchatApp.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $sentBy;?>';
  <?php } ?>
  </script>
  </HTML>

<?php }?>

