<?php
$tycDataIncluded = true;
$vprivacy=null;
$wzUserID=$tycoonID;

$mKeywords = null;
$mDesc = null;

if (is_null($vprivacy) && $userID != 0){
  if ($wzUserID == $userID){
    $vprivacy=99;
    }
  else {
    $SQL = "SELECT trustlevel From tblwzUserFriends  where status=1 and wzUserID=".$userID." and friendUserID=".$wzUserID;  
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if ($tRec) {
      $vprivacy=2;
    }
  }
}
if (isset($_GET['fwebsiteID'])){$websiteID = clean($_GET['fwebsiteID']);} else {$websiteID = "";}

    $SQL = "select count(*) as isOn From tblwzOnline  where wzUserID=".$wzUserID;  
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $thisUserON = $tRec['isOn'];
    $kingQueenImg = 'https://image.bitmonky.com/img/kingProf.png';
    
    $SQL = "SELECT curentStatus,franchise,cityID,partyMbrID,partyLeader, mute,sandBox, online,IP,";
    $SQL .= "sex,age,nfans,paidMember,certifiedStatus, firstname,lastname,email, ";
    $SQL .= "pTextImgFlg,country,prov,city, timezone, profileText From tblwzUser  where wzUserID=".$wzUserID;  
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if ($tRec) {
       $pTextImgFlg  = $tRec['pTextImgFlg'];
       $primContact  = $tRec['firstname'];
       $profileText  = $tRec['profileText'];
       $timezone     = $tRec['timezone'];
       $hCity        = $tRec['city'];
       $cityID       = $tRec['cityID'];
       $hprov        = $tRec['prov'];
       $hCountry     = $tRec['country'];
       $paidMember   = $tRec['paidMember'];
       $nfans        = $tRec['nfans'];
       $sex          = $tRec['sex'];
       $age          = $tRec['age'];
       $IP           = $tRec['IP'];
       $online       = $tRec['online'];
       $isMute       = $tRec['mute'];
       $isSandBoxed  = $tRec['sandBox'];
       $isLeader     = $tRec['partyLeader'];
       $partyID      = $tRec['partyMbrID'];
	     $isFranOwner  = $tRec['franchise'];
	     $ustatus      = $tRec['curentStatus'];
       if (empty($ustatus)){
	     $ustatus = null;
	   }
              
       if (!is_null($sex)) {
         if ($sex==True) {
           $sex = "f - ";
		   $kingQueenImg = 'https://image.'.$whzdom.'/img/jungleQueenS.png';
		 }
         else {
           $sex = "m - ";
		   $kingQueenImg = 'https://image.bitmonky.com/img/kingProf.png';
		 }
       }
   
       $certified = $tRec['certifiedStatus'];
       if (is_null($certified)) { 
         $certified = 0;
       }
     }
     else{
       header('Location: /whzon/mblp/wzMbl.php?noUser');
       
       exit("");
    }

    $weAreFriends = False;
    $thisIsME     = False;
    $iAmBLOCKED   = False;
    $imAFan       = False;

    if ($userID !=0){

      if ($userID == $wzUserID){
        $thisIsME=True;
      }

      $SQL = "select count(*) as nFound from tblwzUserFriends  where status=1 and wzUserID=".$wzUserID." And friendUserID=".$userID;
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      if ($tRec['nFound'] > 0){
        $weAreFriends = True;
      }

      $SQL = "SELECT count(*) as nRecs from tblwzUserBlockList  WHERE wzUserID=".$userID." and blockUserID=".$wzUserID;
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      if ($tRec['nRecs'] > 0){
        $iAmBLOCKED=TRUE;
      }

      $SQL = "SELECT count(*) as nRecs from tblwzfan  WHERE block=0 and wzUserID=".$wzUserID." and fanUserID=".$userID;
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      if ($tRec['nRecs'] > 0){
        $imAFan = True;
      }
    }

?>
