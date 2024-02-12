<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
$mobile=null;

if ($userID == 0) {
    echo "SESSExpired";
}
else {
    $hwzUserID=$userID;

    $SQL = "select moshPitID from tblMoshUsers  where wzUserID=".$hwzUserID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if ($tRec) {?>
      <a href='javascript:OpenGigBox(<?php echo $tRec['moshPitID']?>);'>
      <img title='Go To Your Open MoshBox' alt='Go To Your Open MoshBox' src='//image.bitmonky.com/mImg/mMOSH.png' style='margin-right:5px;height:27px;width:36px;vertical-align:middle;border:0px solid #222222;margin:0px;'  ></a>
    <?php }

    $SQL = "select lastOnline from tblwzUser  where wzUserID=".$hwzUserID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $lastAction=$tRec['lastOnline'];

    $SQL = "select privateChat,lastOnline from tblwzUser  where not privateChat is Null and wzUserID=".$hwzUserID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    if ($tRec) {?>
      <a href='javascript:wzAPI_focusPVC();'>
      <img title='Return To Your Private Chat' alt='Return To Your Private Chat' src='//image.bitmonky.com/img/pvChatIcon.png' style='margin-right:5px;height:27px;width:36px;vertical-align:middle;border:0px solid #222222;margin:0px;'  ></a>
    <?php }

    $pvchat=$tRec['privateChat'];

    if (is_null($pvchat)){
      $pvchat=0;
    }

    if ($pvchat<2) {

      $SQL= "Select wzUserID from ICDchat.tblvCalls where pending=0 and wzCallerID=".$userID; 
      $myresult = mkyMyqry($SQL);
      $mRec = mkyMyFetch($myresult);

      if ($mRec) {
        $id=$mRec['wzUserID'];
 
        $allowAlert=0;

        if ($pvchat==1) {
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

        if ($allowAlert==1 ){
          $SQL= "Select firstname,countryID,IPCountryID from tblwzUser  where  sandBox is null and tblwzUser.wzUserID=".$id."";
          $result = mkyMsqry($SQL);
          $tRec = mkyMsFetch($result);
 
          if ($tRec) {
            $name=$tRec['firstname'];
            $countryID=$tRec['countryID'];
            $IPcountryID=$tRec['IPCountryID'];
            if (!$countryID) {$countryID = 0;}
            if (!$IPcountryID) {$IPcountryID = 0;}
            $SQL = "Select count(*) as nBlock from tblwzUserBlockList  where  wzUserID=".$userID." and ( blockUserID=".$id." or ";
            $SQL .= "BLKcountryID=".$countryID." or BLKcountryID=".$IPcountryID.");";
            $result = mkyMsqry($SQL);
            $tRec = mkyMsFetch($result);
            $nblock=$tRec['nBlock'];         

            if ($nblock==0){
              echo " - ";
              $ankory = "<a href=javascript:wzStartVChat(".$id.",'".mkyUrlEncode($userName)."');>";
              $ankorn= "<a href='javascript:wzAPI_declineChat();'>";
              echo "<img  Title='".$name." is calling'  alt='".$name." is calling' style='margin-right:5px;height:24px;width:24px;border-radius:50%vertical-align:middle;border:0px solid #aaaaaa;margin:1px;' src='//image.bitmonky.com/getMbrTnyImg.php?id=".$id."'>";
              echo " <b style='color:red;'>Video Call - </b>".$ankory."Accept</a> | ".$ankorn."Decline</a>";
            }
          }
        }
color:lighGrey;      }
       
    }


    $SQL = "Select top 10 firstname, tblwzUserFriends.friendUserID as wzUserID ";
    $SQL .= "from tblwzUserFriends  inner join tblwzOnline  on friendUserID=tblwzOnline.wzUserID ";
    $SQL .= "where status=1 and tblwzUserFriends.wzUserID=".$userID." ";
    $SQL .= "Group by firstname,tblwzUserFriends.friendUserID";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if ($tRec){
      echo  " - ";
    }

    while ($tRec){
      $id=$tRec['wzUserID'];
      $name=$tRec['firstname'];
      $ankor= "<a href='javascript:wzGetPage(\"/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$id."\");' Title='".$name." is online'  alt='".$name." is online'>";

      if ($mobile) {
        echo $ankor."<img style='height:44px;width:34px;vertical-align:middle;border-radius:2px;border:0px solid #aaaaaa;margin:1px;' src='//image.bitmonky.com/getMbrTmn.php?id=".$id."'></a>";
      }
      else{
        echo $ankor."<img style='height:24px;width:21px;vertical-align:middle;border-radius:50%;border:0px solid #aaaaaa;margin:1px;' src='//image.bitmonky.com/getMbrTnyImg.php?id=".$id."'></a>";
      }
      $tRec = mkyMsFetch($result);
    }//wend

    //**********************
    // Regular Mail Alerts
    //**********************
    $SQL = "SELECT count(*) as mbrID FROM  tblVisitorMsg  where isMkdMail is null and wzUserID = ".$hwzUserID." and mbrID>0 and msgRead=0;";  
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $nMail=$tRec['mbrID'];

    if ($nMail!=0 ) {
      if ($mobile) {
        $emailIcon = "<img alt='New Mail' title='New Mail' style='margin:0px 2px 2px 8px;height:2.5em;border:0px solid #e0e0e0;border-radius:.3em;";
        $emailIcon .= "vertical-align:bottom;height:26px;width:46px;' src='//image.bitmonky.com/img/emailIcon.png'>";
      }
      else{
        $emailIcon  = "<img alt='New Mail' title='New Mail' style='margin:0px 2px 2px 8px;height:2.5em;border:0px solid #83b364;border-radius:.3em;";
        $emailIcon .= "vertical-align:bottom;' src='//image.bitmonky.com/img/emailIcon.png'>";
      }
      echo " <a style='color:lightGrey;font-size:smaller;' ";
      echo "href='javascript:wzGetPage(\"/whzon/mbr/mail/inbox.php?wzID=".$sKey."&fmbrID=1&fwebsiteID=0&fcontactID=0\");'>".$emailIcon."+".$nMail."</a>";
    }
    //**********************
    // Dating Mail Alerts
    //**********************
    
    $SQL = "SELECT count(*) as mbrID FROM  tblVisitorMsg  where NOT isMkdMail is null and wzUserID = ".$hwzUserID." and mbrID>0 and msgRead=0;";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $nMail=$tRec['mbrID'];

    if ($nMail!=0 ) {
      if ($sessISMOBILE) {
        $emailIcon = "<img alt='New Mail' title='New Mail' style='margin:0px 0px 0px 8px;border:0px;";
        $emailIcon .= "vertical-align:bottom;height:2em;width:2em;' src='//image.bitmonky.com/img/emailIcon.png'>";
        echo " <a style='color:lightGrey;font-size:smaller;' ";
        echo "href='javascript:wzGetPage(\"/whzon/apps/dating/mMail/inbox.php?wzID=".$sKey."&fmbrID=1&fwebsiteID=0&fcontactID=0\");'>".$emailIcon."+".$nMail."</a>";
      }
      else{
        $emailIcon  = "<img alt='New Mail' title='New Mail' style='margin:0px 4px 3.5px 8px;border:0px;";
        $emailIcon .= "vertical-align:bottom;height:2.27em;width:2.27em;;' src='//image.bitmonky.com/img/loveIcon.png'>";
        echo " <a style='color:lightGrey;font-size:smaller;' ";
        echo "href='javascript:wzGetPage(\"/whzon/apps/dating/mail/inbox.php?wzID=".$sKey."&fmbrID=1&fwebsiteID=0&fcontactID=0\");'>".$emailIcon."+".$nMail."</a>";
      }
    }
    
    //*******************************
    // Friend Alerts
    //********************************
    $SQL = "Select count(*) as nFAlert from tblwzUserFriends  left join tblwzUser  on tblwzUserFriends.wzuserID=tblwzUser.wzUserID where tblwzUserFriends.status=0 and tblwzUserFriends.friendUserID=".$userID;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $nFAlert=$tRec['nFAlert'];
    
    if ($nFAlert!=0) {
      $newFriends  = "<img alt='New Friends' title='New Friends' style='height:2.2em;width:2.2em;margin-bottom:3px;vertical-align:middle;'";
      $newFriends .= "src='//image.bitmonky.com/img/friendIcon.png'>";
      echo " <a style='color:lightGrey;font-size:smaller;' href='javascript:wzGetPage(\"/whzon/mbr/mail/mailSetting.php?wzID=".$sKey."\");'> ".$newFriends."+".$nFAlert."</a>";
      }
    }
     
    $SQL = "Select count(*) as nQA from tblwzQA  where ( seen is null and wzUserID = ".$userID.") or (qUserID = ".$userID." and not seen is null and aSeen is null)";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $nQAlert=$tRec['nQA'];
    
    if ($nQAlert!=0 && !$mobile) {
      $SQL = "Select Top 1 wzUserID,qUserID from tblwzQA  where ( seen is null and wzUserID = ".$userID.") or (qUserID = ".$userID." and not seen is null and aSeen is null)";
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      
      $aUserID = $tRec['wzUserID'];
      $qUserID = $tRec['qUserID'];
      
      if ($userID == $aUserID){
        $newQAImg = "<img alt='New Questions Waiting' title='New Questions Waiting' style='margin-bottom:4px;height:2.08em;border:0px;vertical-align:middle;' src='//image.bitmonky.com/img/questionIcon.png'>";
        echo " <a style='color:lightGrey;font-size:smaller;' href='javascript:wzGetPage(\"/whzon/mbr/mbrProfile.php?fmode=qa&wzID=".$sKey."&fwzUserID=".$userID."\");'>".$newQAImg."+".$nQAlert."</a>";
        }
      else{
        $newQAImg = "<img alt='Response To Your Question' title='Response To Your Question' style='margin-bottom:4px;height:2.08em;border:0px;vertical-align:middle;' src='//image.bitmonky.com/img/questionIcon.png'>";
        echo " <a style='color:lightGrey;font-size:smaller;' href='javascript:wzGetPage(\"/whzon/mbr/mbrProfile.php?fmode=qa&wzID=".$sKey."&fwzUserID=".$aUserID."\");'>".$newQAImg."+".$nQAlert."</a>";
      }
    }

    // *******************
    // Group Chat Alerts
    // *******************

    $isGroupAlert = null;
    
    if (1==1) {

      $SQL = "SELECT sandBox,privateChat from tblwzUser  WHERE wzUserID=".$userID;
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      $pvchat=$tRec['privateChat'];
      $sandBox=$tRec['sandBox'];
	

      if (is_null($pvchat))
        $pvchat=0;

      if (!$sandBox){
        $SQL = "SELECT  * from ICDchat.tblChatGroupMbrs ";
        $SQL .= "inner join ICDchat.tblChatGroup on groupID = chatGroupID ";
        $SQL .= "where groupMbrID = ".$userID;

        $myresult = mkyMyqry($SQL) or die($SQL);
        $mRec = mkyMyFetch($myresult);
   	    
        while ($mRec && !$isGroupAlert){
          $groupID       = $mRec['groupID'];
		  $groupOwnerID  = $mRec['groupOwnerID'];
		  $lastMsgID     = $mRec['gLastMsgID'];
		  if (!$lastMsgID) {$lastMsgID = 1;}
		  $gntoRead      = checkntoRead($groupID,$userID,$lastMsgID);
		  if ($gntoRead > 0) {
		    $isGroupAlert = 1;
            groupChatAlert($groupOwnerID,$sKey,$groupID,$mobile);
		  }
		  $mRec = mkyMyFetch($myresult);
        } //wend
      }
    }
    
    // *******************
    // Private Chat Alerts
    // *******************
    
    if ($userID!=0 && $pvchat<2 && !$isGroupAlert){

    $SQL = "SELECT sentBy From ICDchat.tblMbrChat where mread is null and msgMbrID=".$userID." order by modFlg desc";
    $myresult = mkyMyqry($SQL);
    $mRec = mkyMyFetch($myresult);
    $gotIt=False;
    while ($mRec && !$gotIt){

      $SQL = "SELECT countryID, IPcountryID,firstname  From tblwzUser  where  banned <> 1 and wzUserID=".$userID; 
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      $sentBy=$mRec['sentBy'];

      if ($tRec){

        $block=0;
        $friends=0;

        if ($pvchat==1){
      
          $SQL = "select count(*) as nBlock from tblwzUserFriends  where wzUserID=".$userID." and frienduserID=".$sentBy." and Status=1";
          $result = mkyMsqry($SQL);
          $tRec = mkyMsFetch($result);
          if ($tRec['nBlock']==1){
            $friends=1;
          }
        }

        $sandBox=False;

        $SQL = "SELECT sandBox, countryID,IPcountryID  from tblwzUser  WHERE wzUserID=".$sentBy;
        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);
        if ($tRec){
          $countryID=$tRec['countryID'];
          $IPcountryID=$tRec['IPcountryID'];

          if (is_null($IPcountryID)){
            $IPcountryID=0;
          }

          if (is_null($countryID)){
            $countryID=0;
          }

          if ($tRec['sandBox']!=1) {
            $SQL = "SELECT sandBox from tblwzUser  WHERE wzUserID=".$userID;
            $result = mkyMsqry($SQL);
            $tRec = mkyMsFetch($result);
            if  ($tRec['sandBox']==1) {
              $sandBox=True;
            }
          }
          else{
            $sandBox=True;
          }


          $SQL = "SELECT count(*) as nBlock from tblwzUserBlockList  WHERE wzUserID=".$userID." and ( BLKcountryID=".$countryID." or BLKcountryID=".$IPcountryID.");";
          $result = mkyMsqry($SQL);
          $tRec = mkyMsFetch($result);
          $block = $tRec['nBlock'];

          if ($block == 0){
            $SQL = "select count(*) as nBlock from tblwzUserBlockList  where wzUserID=".$userID." and blockUserID=".$sentBy;
            $result = mkyMsqry($SQL);
            $tRec = mkyMsFetch($result);
            $block = $tRec['nBlock'];
          }

          if ($sandBox) {
            $block=1;
          }

          if ($pvchat==1) {
            if ($block==0  && $friends==1) {
              privateChatAlert($sentBy,$mobile,$sKey);  
              $gotIt=True;
            }
          }
          else{
            if ($block==0) {
              privateChatAlert($sentBy,$mobile,$sKey);  
              $gotIt=True;
            }
          }
        }
      }
      $mRec = mkyMyFetch($myresult);
    }//wend
}
    // *******************
    // Private Dating Chat Alerts
    // *******************
    $pvdchat = 0;

    if ($userID!=0 && $pvdchat < 1 && $mkdID && !$isGroupAlert){

      $SQL = "SELECT sentBy From ICDchat.tblMkdChat where mread is null and msgMbrID=".$mkdID." order by qDate desc";
      $myresult = mkyMyqry($SQL);
      $mRec = mkyMyFetch($myresult);
      $gotIt=False;

      while ($mRec && !$gotIt){
        $sentBy = $mRec['sentBy'];
        $SQL = "select mkdUID from tblMkyDating where mkdID = ".$sentBy;
        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);
        $sentByUID = 0;
        if ($tRec){
          $sentByUID = $tRec['mkdUID'];
        }
        

        $SQL = "SELECT countryID, IPcountryID,firstname  From tblwzUser  where  banned <> 1 and wzUserID=".$userID;
        $result = mkyMsqry($SQL);
        $tRec = mkyMsFetch($result);
        $sentBy=$mRec['sentBy'];

        if ($tRec){
          $block=0;
          $friends=0;

          $sandBox=False;

          $SQL = "SELECT sandBox, countryID,IPcountryID  from tblwzUser  WHERE wzUserID=".$sentByUID;
          $result = mkyMsqry($SQL);
          $tRec = mkyMsFetch($result);
          mkyDBug('->'.$SQL);
          if ($tRec){
            $countryID=$tRec['countryID'];
            $IPcountryID=$tRec['IPcountryID'];

            if (is_null($IPcountryID)){
              $IPcountryID=0;
            }

            if (is_null($countryID)){
              $countryID=0;
            }

            if ($tRec['sandBox']!=1) {
              $SQL = "SELECT sandBox from tblwzUser  WHERE wzUserID=".$userID;
              $result = mkyMsqry($SQL);
              $tRec = mkyMsFetch($result);
              if  ($tRec['sandBox']==1) {
                $sandBox=True;
              }
            }
            else{
              $sandBox=True;
            }


            $SQL = "SELECT count(*) as nBlock from tblwzUserBlockList  WHERE wzUserID=".$userID." and ( BLKcountryID=".$countryID." or BLKcountryID=".$IPcountryID.");";
            $result = mkyMsqry($SQL);
            $tRec = mkyMsFetch($result);
            $block = $tRec['nBlock'];

            if ($block == 0){
              $SQL = "select count(*) as nBlock from tblwzUserBlockList  where wzUserID=".$userID." and blockUserID=".$sentByUID;
              $result = mkyMsqry($SQL);
              $tRec = mkyMsFetch($result);
              $block = $tRec['nBlock'];
            }

            if ($sandBox) {
              $SQL = "SELECT count(*) as nBlock from tblwzUserBlockList  WHERE wzUserID=".$userID." and ( BLKcountryID=".$countryID." or BLKcountryID=".$IPcountryID.");";
              $result = mkyMsqry($SQL);
              $tRec = mkyMsFetch($result);
              $block = $tRec['nBlock'];

              if ($block == 0){
                $SQL = "select count(*) as nBlock from tblwzUserBlockList  where wzUserID=".$sendByUID." and blockUserID=".$userID;
                $result = mkyMsqry($SQL);
                $tRec = mkyMsFetch($result);
                $block = $tRec['nBlock'];
              }

              if ($sandBox) {
                $block=1;
              }
            }
            mkyDBug('I amd here '.$sentBy.$sKey.$mobile);
            if ($pvchat==1) {
              if ($block==0) {
                privateDChatAlert($sentBy,$mobile,$sKey);
                $gotIt=True;
              }
            }
            else{
              if ($block==0) {
                privateDChatAlert($sentBy,$mobile,$sKey);
                $gotIt=True;
              }
            }
          }
        }
        $mRec = mkyMyFetch($myresult);
      }//wend
    }


function privateDChatAlert($sentBy,$mobile,$sKey){

   if ($mobile){
     $chatAImg="<img alt='Accept Love Chat Msg' title='Love Chat' style='margin-left:8px;margin-bottom:2px;border:0px;vertical-align:bottom;height:30px;width:30px;' src='//image.bitmonky.com/img/iconDating.png'>";
   }
   else{
     $chatAImg="<img alt='Accept Love Chat Msg' title='Love Chat' style='margin-left:2px;border:0px;vertical-align:top;height:2.3em;width:2.3em;' src='//image.bitmonky.com/img/iconDating.png'>";
   }
   echo "<a href=\"javascript:wzGetPage('/whzon/apps/dating/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$sentBy."');\">";
   echo "<img  Title='Private Caller: click to view their profile... Click The Heart To Open Chat'  alt='".$sentBy." is calling - click to view their profile' style='margin-left:8px;height:24px;width:24px;vertical-align:middle;border:0px solid orange;border-radius:50%;' src='//image.bitmonky.com/getDProfThm.php?id=".$sentBy."'></a>";
   echo "<a href='javascript:wzPopDChat(".$sentBy.");'>".$chatAImg."</a>";
}
function groupChatAlert($sentBy,$sKey,$groupID,$mobile){

   if ($mobile){
     $chatAImg="<img alt='Private Chat' title='Private Chat' style='margin-left:8px;margin-bottom:2px;border:0px;vertical-align:bottom;height:30px;width:30px;' src='//image.bitmonky.com/img/chatAlertBG.png'>";
   }
   else{
     $chatAImg="<img <img alt='Accept Private Chat' title='Accept Priviat Chat' style='margin-left:2px;margin-bottom:2px;border:0px;vertical-align:top;' src='//image.bitmonky.com/img/chatAlertBG.png'>";
   }
   echo "<a href=\"javascript:wzGetPage('/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$sentBy."');\">";
   echo "<img  Title='New message(s) in ".$sentBy."`s group chat - click to view their profile'  alt='New message(s) in ".$sentBy."`s grouop chat - click to go there' style='margin-left:8px;height:24px;width:24px;vertical-align:middle;border:0px solid orange;border-radius:50%;' src='//image.bitmonky.com/getMbrTnyImg.php?id=".$sentBy."'></a>";
   echo " <a href='javascript:wzPopGroupChat(".$groupID.");'>".$chatAImg."</a>";

}

function checkntoRead($groupID,$userID,$lastMsgID){
  $SQL = "select count(*) as nRec from ICDchat.tblMbrChat where groupID = ".$groupID." and (NOT sentBy = ".$userID.") and msgID > ".$lastMsgID;
  $myresult = mkyMyqry($SQL) or die($SQL);
  $rRec = mkyMyFetch($myresult);
  return $rRec['nRec'];
}

function privateChatAlert($sentBy,$mobile,$sKey){

   if ($mobile){
     $chatAImg="<img alt='Private Chat' title='Private Chat' style='margin-left:8px;margin-bottom:2px;border:0px;vertical-align:bottom;height:30px;width:30px;' src='//image.bitmonky.com/img/chatAlertBG.png'>";
   }
   else{
     $chatAImg="<img <img alt='Accept Private Chat' title='Accept Priviat Chat' style='margin-left:2px;margin-bottom:2px;border:0px;vertical-align:top;' src='//image.bitmonky.com/img/chatAlertBG.png'>";
   }
   echo "<a href=\"javascript:wzGetPage('/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$sentBy."');\">";
   echo "<img  Title='".$sentBy." is calling - click to view their profile'  alt='".$sentBy." is calling - click to view their profile' style='margin-left:8px;height:24px;width:24px;vertical-align:middle;border:0px solid orange;border-radius:50%;' src='//image.bitmonky.com/getMbrTnyImg.php?id=".$sentBy."'></a>";
   echo " <a href='javascript:wzPopChat(".$sentBy.");'>".$chatAImg."</a>";

}

?>

