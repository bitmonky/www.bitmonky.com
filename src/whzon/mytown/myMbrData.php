<?php
$mbrDataIncluded = true;
$vprivacy=null;
$wzUserID=null;
if (isset($setMbrTo)){
  $wzUserID = $setMbrTo;
}
else {
  if (isset($_GET['fwzUserID'])){$wzUserID = safeGetINT('fwzUserID');} 
  if (isset($_GET['fwzUserId'])){$wzUserID = safeGetINT('fwzUserId');} 
}
if (!$wzUserID || $wzUserID == 0 || $wzUserID == '') {
  $wzUserID=17621;
}
$SQL = "Select cityID from tblwzUser  where wzUserID=".$wzUserID;
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);
if ($tRec){
  $cityID = $tRec['cityID'];
}
else {
  $SQL = "Select cityID from tblwzUser  where wzUserID=17621";
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  $cityID = $tRec['cityID'];
  $wzUserID = 17621;
}

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
function formatTxt($str){
  $str = mkyStrReplace("\n","<p>",$str);
  $str = mkyStrReplace("[","<b>",$str);
  $str = mkyStrReplace("]","</b>",$str);
  $str = mkyStrReplace("�","`",$str);
  return $str;
}
function TalkAboutLink($URL,$text) {
  global $whzdom;
  $op = "<table style='vertical-align:top;display:inline;border-collapse:collapse;border:0px;margin:1px;'><tr valign='top'>";
  $op .=  "<td style='line-height:normal;height:15px;font-size:9px;white-space:nowrap;border:0px;padding:0px;'>";
  $op .= "<a style='font-size:9px;line-height:normal;' title='".$text."' href='".$URL."'>";
  $op .= "<img style='display:inline;vertical-align:middle;border:0px;margin:0px;padding:0px;margin-right:3px;' ";
  $op .= "src='//image.bitmonky.com/talkAbout.png'></a></td><td style='line-height:normal;height:15px;font-size:9px;white-space:nowrap;";
  $op .= "border:0px;padding:0px;'><img style='display:inline;vertical-align:middle;margin-0px;border:0px;padding:0px;' ";
  $op .= "src='//image.bitmonky.com/followLS.png'></td><td style='line-height:normal;height:15px;font-size:9px;white-space:nowrap;";
  $op .= "border:0px;padding:0px;'><table style='height:15px;display:inline;vertical-align:middle;border-collapse:collapse;border:0px;margin:0px;'>";
  $op .= "<tr><td style=line-height:normal;height:15px;border:0px;white-space:nowrap;padding:0px;background-image:";
  $op .= "url('//image.bitmonky.com/folAmtBK.jpg');font-size:9px;><a style='line-height:normal;font-size:9px;color:#777777;text-decoration:none;' ";
  $op .= "title='".$text."' href='".$URL."'>".$text."</a></td></tr></table></td>";
  $op .= "<td style='line-height:normal;height:15px;font-size:9px;white-space:nowrap;border:0px;padding:0px;'>";
  $op .= "<img style='display:inline;vertical-align:middle;border:0px;margin:0px;padding:0px;' src='//image.bitmonky.com/followRS.png'></td></tr></table>";
  echo $op;
}
  if (isset($_GET['fwebsiteID'])){$websiteID = clean($_GET['fwebsiteID']);} else {$websiteID = "";}

    $SQL = "select count(*) as isOn From tblwzOnline  where wzUserID=".$wzUserID;  
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $thisUserON = $tRec['isOn'];
    $kingQueenImg = '//image.bitmonky.com/img/kingProf.png';
    
    $SQL = "SELECT curentStatus,franchise,cityID,partyMbrID,partyLeader, mute,sandBox, online,IP,sex,age,nfans,paidMember,certifiedStatus, ";
    $SQL .= "firstname,lastname,email,pTextImgFlg,country,prov,city, timezone, profileText From tblwzUser  where wzUserID=".$wzUserID;
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
		   $kingQueenImg = '//image.'.$whzdom.'/img/jungleQueenS.png';
		 }
         else {
           $sex = "m - ";
		   $kingQueenImg = '//image.bitmonky.com/img/kingProf.png';
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

   function writeUserFolLink($linkTxt,$nfans,$wzUserID,$userID,$primContact){ 
   
     $folAmt = $nfans;
     if (is_null($folAmt)){
      $folAmt = 0;
     }

     if ($userID != 0) {
      $folJS = "href=javascript:wzAPI_showFrame('fanRequestFrm.asp?fanID=".$wzUserID."',300,180,600,200);";
      }
    else{
      $folJS = "href=javascript:wzOpenFanReq();";
    }

    $folLink  = "<a title='Visit ".$primContact."`s Fan Room' style='font-size:9px;color:#777777;text-decoration:none;font-weight:normal;' href='https://".$whzdom."/whozon/mbrfanRoom.asp?fwzUserID=".$wzUserID."'>".$folAmt."</a>";

    $followLink = "<a style='font-size:9px;' ".$folJS.">";
    $followLink .= "<img title='Follow ".$primContact."' style='margin-right:2px;border:0px solid #dddddd;vertical-align:middle;' src='//image.".$whzdom."/img/folowIcon_lt.jpg'></a>"; 
    $followLink .= "<img style='display:inline;vertical-align:middle;' src='//image.".$whzdom."/img/folAmtLFT.jpg'>"; 
    $followLink .= "<table style='display:inline;vertical-align:middle;border-collapse:collapse;border:0px;margin:0px;'><tr>";
    $followLink .= "<td style=height:15px;font-size:9px;white-space:nowrap;border:0px;padding:0px;background-image:url('https://".$whzdom."/folAmtBK.jpg');>".$folLink."";
    $followLink .= "</td></tr></table><img style='display:inline;vertical-align:middle;' src='//image.".$whzdom."/img/folAmtRT.jpg'>";
    $followLink .= " <a  "  & folJS.">".$linkTxt."</a>"; 
        
    echo $followLink;

  }
?>
