<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
header('Content-Type: application/json');
$irc = new stdClass;

include_once("../mkysess.php");
if ($userID!=0){

    $SQL = "select privateChat,lastOnline from tblwzUser  where not privateChat is Null and wzUserID=".$userID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $pvchat=$tRec['privateChat'];

    if (is_null($pvchat)){
      $pvchat=0;
    }

    if ($pvchat<2) {
      $SQL = "SELECT sentBy From ICDchat.tblMbrChat where NOT sentBy = 0 and mread is null and msgMbrID=".$userID." order by modFlg desc";
      $myresult = mkyMyqry($SQL);
      $mRec = mkyMyFetch($myresult);

      $gotIt=false;

      while ($mRec && $gotIt==false){
 
        $SQL = "SELECT countryID, IPcountryID,firstname  From tblwzUser  where  banned <> 1 and wzUserID=".$userID; 
        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);

        if ($tRec){
          $irc->sentBy=$mRec['sentBy'];
 
          $block=0;
          $friends=0;

          if ($pvchat==1){
            $SQL = "select count(*) as nBlock from tblwzUserFriends  where wzUserID=".$userID." and frienduserID=".$irc->sentBy." and Status=1";
            $xresult = mkyMsqry($SQL);
            $xRec = mkyMsFetch($xresult);
            if ($xRec['nBlock']==1){
              $friends=1;
            }
          }

          $sandBox=False;

          $SQL = "SELECT sandBox, countryID,IPcountryID  from tblwzUser  WHERE wzUserID=".$irc->sentBy;
          $xresult = mkyMsqry($SQL);
          $xRec = mkyMsFetch($xresult);

          $countryID=$xRec['countryID'];
          $IPcountryID=$xRec['IPcountryID'];

          if (is_null($IPcountryID)){
            $IPcountryID=0;
          }

          if (is_null($countryID)){
            $countryID=0;
          }

          if  ($xRec['sandBox']!=1){
            $SQL = "SELECT CAST(sandBox as integer)as sandBox from tblwzUser  WHERE wzUserID=".$userID;
            $xresult = mkyMsqry($SQL);
            $xRec = mkyMsFetch($xresult);
            if  ($xRec['sandBox']==1){
              $sandBox=True;
            }
          }
          else {
            $sandBox=True;
          }

          $SQL  = "SELECT count(*) as nBlock from tblwzUserBlockList  ";
          $SQL .= "WHERE wzUserID=".$userID." and ( BLKcountryID=".$countryID." or BLKcountryID=".$IPcountryID.");";
          $xresult = mkyMsqry($SQL);
          $xRec = mkyMsFetch($xresult);
          $block= $xRec['nBlock'];

          if ($block==0){
            $SQL = "select count(*) as nBlock from tblwzUserBlockList  where wzUserID=".$userID." and blockUserID=".$irc->sentBy;
            $xresult = mkyMsqry($SQL);
            $xRec = mkyMsFetch($xresult);
            $block= $xRec['nBlock'];
          }

          if ($sandBox){
            $block=1;
          }

          if ($pvchat==1){
            if ($block==0 && $friends==1){
              exit (mkyJEncode($irc));  
              $gotIt=True;
           }
          }
          else {
            if ($block==0){
              exit (mkyJEncode($irc));  
              $gotIt=True;
            }
          }
        }
        $mRec = mkyMyFetch($myresult);
      }//wend
    }
  }

$irc->sentBy = 0;
echo mkyJEncode($irc);
?>
