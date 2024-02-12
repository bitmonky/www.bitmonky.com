<?php
header('Content-Type: application/json');
$pvc = new stdClass;

include_once("../mkysess.php");
//ini_set('display_errors',1);
//error_reporting(E_ALL);

/*
*******************************
 start check other member status
*******************************
*/

$mbrID=clean($_GET['fmbrID']);
$bothUseChrome = null;

$hutOwnID  = getUserID(safeGET('hutID'));
$chathut = ' mchaDigiHutOID is null ';
if ($hutOwnID){
  $chathut = 'mchaDigiHutOID = '.$hutOwnID.' ';
}

$SQL = "select TIMESTAMPDIFF(second,lastAction,now()) as lastAction,wzWRTCon, tblwzUser.firstname ";
$SQL .= "from tblwzUser  left join tblwzOnline  on tblwzOnline.wzUserID = tblwzUser.wzUserID ";
$SQL .= "where tblwzUser.wzUserID = ".$mbrID;

$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
if (!$tRec){
  $lastAction = null;
  $otherName  = null;
}
else {
  $lastAction = $tRec['lastAction'];
  $otherName  = $tRec['firstname'];
  if ($tRec['wzWRTCon'] && $wzWRTCon){
    $bothUseChrome = 1;
  }
}
$style = " style='color:brown;'";
if ($lastAction){
  $style='';
}
$html = "<br>Last Action: <b ".$style.">".strLastAction($lastAction)."</b>";

if ($bothUseChrome || $userID == 17621) {
  if ($lastAction < 45 && $lastAction){
    $status = "<a style='color:orange;' href='javascript:startWRTCChat();'>Start Video Chat With - ".$otherName."</a>";
  }
  else {
    $status = $otherName." Not Available for Video Chat.";
  }
}
else { 
  if (!$wzWRTCon) { 
    $status = "<span style='padding:3px;background:none;border-radius: .5em;'><a style='color:orange;' href='//www.google.com/chrome/' target='_new' >Download Chrome And Enjoy Free MonkyTalk Video Chat!</a></span>";
  } 
  else { 
    $status = "sorry ".$otherName." Is not using chrome!";
  }
}
$pvc->mbrstat = new stdClass;
$pvc->mbrstat->html = $html;
$pvc->mbrstat->status = $status;

$mode = safeGET('fmode');
if ($mode == 'all'){
  /*
  ******************************
  begin getChatOldMsg.php
  ******************************
  */
  $WRTCtalkToID = null;

  if (isset($_GET['fWRTC'])){
    $SQL = "SELECT wzWRTCtalkToID  From tblwzUser where wzUserID=".$userID;
    $result = mkyMsqry($SQL) or die($SQL);
    $tRec = mkyMsFetch($result);
    $WRTCtalkToID = $tRec['wzWRTCtalkToID'];
  }
  if (!$userID==0 ){

    $SQL = "SELECT * From ICDchat.tblMbrChat where mread = 1 and ".$chathut." and (( msgUserID=".$userID." and msgMbrID=".$mbrID.") or ";
    $SQL .= "(msgUserID=".$mbrID." and msgMbrID=".$userID.")) ORDER BY msgID desc limit 60";

    $myresult = mkyMyqry($SQL);
    $mRec = mkyMyFetch($myresult);

    if (!$mRec){
      $jObj = '{"myMsgs":[]}';
    }
    else {
      $jObj = '{"myMsgs" : [';
      $n = 1;
      $j = new stdClass;

      while ($mRec){
        if ($n == 1){$coma = '';} else {$coma = ',';}
        $WRTCsender = $mRec['WRTCid'];
        if ($userID != $WRTCsender){
          $msg=mkyStrReplace(";",":",$mRec['msg']);
          $name="namefiller";
          if ($mRec['sentBy'] != $WRTCtalkToID) {
            $n = 2;
            $j->msgID   = $mRec['msgID'];
            $j->guserID = $mRec['sentBy'];
            $j->gname   = $name;
            $j->htm     = utf8_encode($msg);

            $jObj .= $coma.(json_encode($j));
          }
        }
        $mRec = mkyMyFetch($myresult);
      }
      $jObj .= ']}';
    }
  }
}
else {
  /*
  ******************************
  begin getChatMsg.php
  ******************************
  */
  //$mbrID=clean($_GET['fmbrID']);

  $WRTCtalkToID = null;

  if (isset($_GET['fWRTC'])){
    $SQL = "SELECT wzWRTCtalkToID  From tblwzUser  where wzUserID=".$userID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $WRTCtalkToID = $tRec['wzWRTCtalkToID'];
  }
  if (!$userID==0){

    $countryID = 0;
    $IPcountryID = 0;

    $SQL = "select firstname,countryID,IPcountryID from tblwzUser  where wzUserID=".$mbrID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if ($tRec){
      $countryID=$tRec['countryID'];
      $IPcountryID=$tRec['IPcountryID'];
    }

    $nBlock = 0;
    if ($IPcountryID){
      $SQL = "SELECT count(*) as nBlock from tblwzUserBlockList  ";
      $SQL .= "WHERE wzUserID=".$userID." and ( BLKcountryID=".$countryID." or BLKcountryID=".$IPcountryID.");";
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      $nBlock = $tRec['nBlock'];
    }

    if ($nBlock == 0){
      $SQL = "select count(*) as nblock from tblwzUserBlockList  where wzUserID=".$userID." and blockUserID=".$mbrID;
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      $nBlock=$tRec['nblock'];
    }



    if ($nBlock==0){
      $SQL = "SELECT * From ICDchat.tblMbrChat where mread is null and ".$chathut." and msgMbrID=".$userID." and sentBy=".$mbrID." ORDER BY msgID";
      $myresult = mkyMyqry($SQL);
      $mRec = mkyMyFetch($myresult);

      if (!$mRec){
        $jObj = '{"myMsgs":[]}';
      }
      else {
        $jObj = '{"myMsgs" : [';
        $n = 1;
        $j = new stdClass;
        while ($mRec){
          if ($n == 1){$coma = ''; $n = 2;} else {$coma = ',';}
          $msg=mkyStrReplace(";",":",$mRec['msg']);
          $msgID = $mRec['msgID'];
          mkyMyqry("update ICDchat.tblMbrChat set mread=1 where msgID=".$mRec['msgID']);

          $j->msgID = $msgID;
          $j->htm   =  utf8_encode($msg);

          $jObj .= $coma.(json_encode($j));
          $mRec = mkyMyFetch($myresult);
        }
        $jObj .= ']}';
      }
    }
  }
}
$getMsgs = json_decode($jObj);
$pvc->newMsg = $getMsgs; 

/*
*********************************
  Mosh Pit Info Update
*********************************
*/

    $SQL = "SELECT tblMoshUsers.moshPitID, Title, tblMoshArtist.name  From tblMoshUsers  ";
    $SQL .= "inner join tblMoshPit  on  tblMoshUsers.moshPitID=tblMoshPit.moshPitID ";
    $SQL .= "inner join tblMoshPerformance  on gigID=moshPerformanceID ";
    $SQL .= "inner join tblmoshSong  on tblmoshSong.songID=tblMoshPerformance.songID ";
    $SQL .= "inner join tblMoshArtist  on tblmoshSong.artistID=tblMoshArtist.moshArtistID ";
    $SQL .= "where tblMoshUsers.wzUserID=".$mbrID;

    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $mosh = null;
    if ($tRec){
      $moshPit = $tRec['moshPitID'];
      $title   = $tRec['Title'];
      $artist  = $tRec['name'];


     $mosh  = '<b>Listening To</b><img style="border: 0px none; vertical-align: middle; margin-top: 0px; margin-left: 2px; height: 14px; width: 11px;" ';
     $mosh .= 'src="'.$GLOBALS['MKYC_imgsrv'].'/img/musicIcon.png"> - `'.$title.'` By '.$artist;
     $mosh .= "<a style='font-size:12px;' href='javascript:parent.OpenGigBox(".$moshPit.");'>Join Them</a>";
    }

$pvc->mosh = $mosh;
/*
***************************************
Begin UserAlerts 
***************************************
*/

if (!$userID==0){
  $SQL  = "Select firstname, tblwzUserFriends.friendUserID as wzUserID ";
  $SQL .= "from tblwzUserFriends  inner join tblwzOnline  on friendUserID=tblwzOnline.wzUserID ";
  $SQL .= "where status=1 and tblwzUserFriends.wzUserID=".$userID." ";
  $SQL .= "Group by firstname,tblwzUserFriends.friendUserID limit 10";

  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);

  if (!$tRec) {
    $uAlerts =   " - ";
  }
  else {
    $uAlerts = null;
    while ($tRec){
      $id=$tRec['wzUserID'];
      $name=$tRec['firstname'];
      $ankor= "<a href='/whzon/pvchat/pvchatApp.php?wzID=".$sKey."&fmbrID=".$id."' Title='".$name." is online'  alt='".$name." is online'>";

      $uAlerts .= $ankor."<img style='height:24px;width:18px;vertical-align:middle;border-radius:50%;border:0px solid #aaaaaa;margin:1px;' ";
      $uAlerts .= "src='".$GLOBALS['MKYC_imgsrv']."/getMbrTnyImg.php?id=".$id."'></a>";

      $tRec = mkyMsFetch($result);
    }//wend
  }
}
$pvc->uAlerts = $uAlerts;

/*
**********************************************
  Begin Get New Contact Notifications
**********************************************
*/

$jObj = '{"myMsgs":[]}';
if (!$userID==0){
  $SQL = "SELECT sandBox,privateChat from tblwzUser  WHERE wzUserID=".$userID;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  $pvchat=$tRec['privateChat'];
  $sandBox=$tRec['sandBox'];

  if (is_null($pvchat)){
    $pvchat=0;
  }

  if (!$sandBox){
    $SQL = "SELECT  sentBy, count(*) as nMsg From ICDchat.tblMbrChat ";
    $SQL .= "where mread is null and ".$chathut." and msgMbrID=".$userID;
    $SQL .= " group by sentBy";

    $myresult = mkyMyqry($SQL);
    $mRec = mkyMyFetch($myresult);
    if (!$mRec){
      $jObj = '{"myMsgs":[]}';
    }
    else {
      $jObj = '{"myMsgs" : [';
      $n = 1;
      while ($mRec){
        $sentBy    = $mRec['sentBy'];
        $nmsg      = $mRec['nMsg'];
        $block=0;
        $friends=0;
        if ($n == 1){$coma = '';} else {$coma = ',';}

        $SQL = "select firstname,countryID,IPcountryID from tblwzUser  where sandBox is null and wzUserID=".$sentBy;

        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);
        if ($tRec) {
          $firstname = $tRec['firstname'];
          $countryID=$tRec['countryID'];
          $IPcountryID=$tRec['IPcountryID'];
          if (!$countryID) {$countryID = 0;}
          if (!$IPcountryID) {$IPcountryID = 0;}

          if ($pvchat==1) {
            $SQL = "select count(*) as nBlock from tblwzUserFriends  where wzUserID=".$userID." and frienduserID=".$sentBy." and Status=1";
            $result = mkyMsqry($SQL);
            $tRec = mkyMsFetch($result);
            if ($tRec['nBlock']==1){
              $friends=1;
            }
          }

          $SQL  = "SELECT count(*) as nBlock from tblwzUserBlockList  ";
          $SQL .= "WHERE wzUserID=".$userID." and ( BLKcountryID=".$countryID." or BLKcountryID=".$IPcountryID.")";
          $result = mkyMsqry($SQL);
          $tRec = mkyMsFetch($result);
          $block = $tRec['nBlock'];

          if ($block == 0){
            $SQL = "select count(*) as nBlock from tblwzUserBlockList  where wzUserID=".$userID." and blockUserID=".$sentBy;
            $result = mkyMsqry($SQL);
            $tRec = mkyMsFetch($result);
            $block= $tRec['nBlock'];
          }

          if ($pvchat==1){
            if ($block==0 && $friends==1){
              $jObj .= $coma.'{"sentBy":'.$sentBy.',"htm":"'.getHTML($sentBy,$firstname,$nmsg,$userID,$sKey).'"}';
              $n = $n + 1;
            }
          }
          else {
            if ($block==0) {
              $jObj .= $coma.'{"sentBy":'.$sentBy.',"htm":"'.getHTML($sentBy,$firstname,$nmsg,$userID,$sKey).'"}';
              $n = $n + 1;
            }
          }
        }
        $mRec = mkyMyFetch($myresult);
      } //wend

      $jObj .= ']}';
    }
  }
}

$cMsgs = json_decode($jObj);
$pvc->cMsgs = $cMsgs;

/*
**********************************************
  Begin Get Group Chat List
**********************************************
*/

$jObj = '{"myMsgs":[]}';

if (!$userID==0){

  $SQL = "SELECT sandBox,privateChat from tblwzUser WHERE wzUserID=".$userID;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  $pvchat=$tRec['privateChat'];
  $sandBox=$tRec['sandBox'];

  if (is_null($pvchat)){
    $pvchat=0;
  }
  if (!$sandBox){
    $SQL = "SELECT  * from ICDchat.tblChatGroupMbrs ";
    $SQL .= "inner join ICDchat.tblChatGroup on groupID = chatGroupID ";
    $SQL .= "where groupMbrID = ".$userID;

    $myresult = mkyMyqry($SQL);
    $jObj = '{"myGroups" : [';
    $mRec = mkyMyFetch($myresult);
    while ($mRec){
      $groupID       = $mRec['groupID'];
      $chatGroupName = $mRec['chatGroupName'];
      $groupOwnerID  = $mRec['groupOwnerID'];
      $lastMsgID     = $mRec['gLastMsgID'];
      $lastRead      = $mRec['gLastRead'];
      if (!$lastMsgID) {$lastMsgID = 1;}
      if (!$lastRead)  {$lastRead  = '-';}
      $gntoRead      = checkntoRead($groupID,$userID,$lastMsgID,$chathut);
      if (!$gntoRead)  {$gntoRead  = 0;}
      $jObj .= '{ "gname" : "'.$chatGroupName.'", "groupID" : "'.$groupID.'", "ownerID" : "'.$groupOwnerID.'", "lastMsgID" : "'.$lastMsgID.'", "lastRead" : "'.$lastRead.'", "gntoRead" : "'.$gntoRead.'" }';
      $mRec = mkyMyFetch($myresult);
      if ($mRec) {$jObj .= ',';}
    } //wend
    $jObj .= ']}';
  }
}
$grpList = json_decode($jObj);
$pvc->grpList = $grpList;

/*
******************************************
 Begin Get All Contacst List
******************************************
*/

$jObj = '{"myMsgs":[]}';

if (!$userID==0){
  $SQL = "SELECT sandBox,privateChat from tblwzUser  WHERE wzUserID=".$userID;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  $pvchat=$tRec['privateChat'];
  $sandBox=$tRec['sandBox'];

  if (is_null($pvchat)){
    $pvchat=0;
  }
  if (!$sandBox){
    $SQL = "SELECT  sentBy, count(*) as nMsg From ICDchat.tblMbrChat ";
    $SQL .= "where msgMbrID=".$userID." and ".$chathut." group by sentBy";

    $myresult = mkyMyqry($SQL);
    $mRec = mkyMyFetch($myresult);
    if (!$mRec){
      $jObj = '{"myMsgs":[]}';
    }
    else {
      $jObj = '{"myMsgs" : [';
      $n = 1;
      while ($mRec){
        $sentBy    = $mRec['sentBy'];
        $nmsg      = $mRec['nMsg'];
        $block=0;
        $friends=0;
        if ($n == 1){$coma = '';} else {$coma = ',';}

        $SQL = "select tblwzUser.firstname,tblwzOnline.wzUserID from tblwzUser  ";
        $SQL .= "left join tblwzOnline  on tblwzUser.wzUserID = tblwzOnline.wzUserID ";
        $SQL .= "where tblwzUser.sandBox is null and tblwzUser.wzUserID=".$sentBy;
        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);

        if ($tRec){
          $firstname = $tRec['firstname'];
          $isOnline  = $tRec['wzUserID'];

          if ($pvchat==1) {
            $SQL = "select count(*) as nBlock from tblwzUserFriends  where wzUserID=".$userID." and frienduserID=".$sentBy." and Status=1";
            $result = mkyMsqry($SQL);
            $tRec = mkyMsFetch($result);
            if ($tRec['nBlock']==1){
              $friends=1;
            }
          }

          $SQL = "select count(*) as nBlock from tblwzUserBlockList  where wzUserID=".$userID." and blockUserID=".$sentBy;
          $result = mkyMsqry($SQL);
          $tRec = mkyMsFetch($result);
          $block= $tRec['nBlock'];


          if ($pvchat==1){
            if ($block==0 && $friends==1){
              $jObj .= $coma.'{"sentBy":'.$sentBy.',"htm":"'.getHTML($sentBy,$firstname,$nmsg,$userID,$sKey,$isOnline).'"}';
              $n = $n + 1;
            }
          }
          else{
            if ($block==0){
              $jObj .= $coma.'{"sentBy":'.$sentBy.',"htm":"'.getHTML($sentBy,$firstname,$nmsg,$userID,$sKey,$isOnline).'"}';
              $n = $n + 1;
            }
          }
        }
        $mRec = mkyMyFetch($myresult);
      } //while
      $jObj .= ']}';
    }
  }
}
$allContacts = json_decode($jObj);
$pvc->allContacts = $allContacts;

echo json_encode($pvc);

function checkntoRead($groupID,$userID,$lastMsgID,$chathut){
  $SQL = "select count(*) as nRec from ICDchat.tblMbrChat where groupID = ".$groupID." 
  and ".$chathut." and (NOT sentBy = ".$userID.") and msgID > ".$lastMsgID;
  $myresult = mkyMyqry($SQL) or die($SQL);
  $rRec = mkyMyFetch($myresult);
  return $rRec['nRec'];
}

function getHTML($sentBy,$firstname,$nmsg,$userID,$sKey,$isOnline=null){
  $firstname = clean(mkyStrReplace("!","*",$firstname));
  if ($isOnline){
    $isOnline = "<img alt='Online' style='border-radius:50%;width:10px;height:10px;' src='".$GLOBALS['MKYC_imgsrv']."/img/pvcOnline.png'> ";
  }
  $res  = "<table><tr><td><a href='pvchatApp.php?wzID=".$sKey."&fmbrID=".$sentBy."'><img style='border-radius:50%;border: 0px solid #e0e0e0;";
  $res .= "height:2em;width:23m;margin-bottom:2px;float:left;margin-right:8px;' src='".$GLOBALS['MKYC_imgsrv']."/getMbrTnyImg.php?id=".$sentBy."'>".$isOnline;
  $res .= " ".$firstname."</a> - ".$nmsg."</td></tr></table>";
  return $res;
}
?>
