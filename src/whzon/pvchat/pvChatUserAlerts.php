<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

$mobile=null;

if (!$userID==0){

    $SQL  = "Select firstname, tblwzUserFriends.friendUserID as wzUserID ";
    $SQL .= "from tblwzUserFriends  inner join tblwzOnline  on friendUserID=tblwzOnline.wzUserID ";
    $SQL .= "where status=1 and tblwzUserFriends.wzUserID=".$userID." ";
    $SQL .= "Group by firstname,tblwzUserFriends.friendUserID limit 10";
    
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    if (!$tRec) {
      echo " - ";
    }

    while ($tRec){
      $id=$tRec['wzUserID'];
      $name=$tRec['firstname'];
      $ankor= "<a href='/whzon/pvchat/pvchatApp.php?wzID=".$sKey."&fmbrID=".$id."' Title='".$name." is online'  alt='".$name." is online'>";

      if ($mobile){
        echo $ankor."<img style='height:44px;width:34px;vertical-align:middle;border-radius:50%;border:0px solid #aaaaaa;margin:1px;' src='//image.bitmonky.com/getMbrTmn.php?id=".$id."'></a>";
        }
      else{
        echo $ankor."<img style='height:24px;width:18px;vertical-align:middle;border-radius:50%;border:0px solid #aaaaaa;margin:1px;' src='//image.bitmonky.com/getMbrTnyImg.php?id=".$id."'></a>";
      }
      $tRec = mkyMsFetch($result);
    }//wend
}

?>
