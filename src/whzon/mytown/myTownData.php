<?php
$vprivacy=null;
$wzUserID=null;
if (isset($_GET['fstoreID'])){$storeID = safeGET('fstoreID');} else {$storeID = 1;}

$SQL = "select iSchanID,storeUID, storeTitle,storeDesc,status,coverage, purchaseMethod ,activated,geoCountry,geoProvince,";
$SQL .= "geoCity,displayStreet,unitNbr,streetAd,storePhone,postCD from tblStore  ";
$SQL .= "left join tblStoreCoverage   on storeCoverageID = coverageID ";
$SQL .= "left join tblStorePurMethods  on payMethodID=pmID ";
$SQL .= "where storeID = ".$storeID;

$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);

$wzUserID   = $tRec['storeUID'];
$viewUID    = $wzUserID;
$storeTitle = $tRec['storeTitle'];
$storeDesc  = $tRec['storeDesc'];
$storeChanID  = $tRec['iSchanID'];
$status       = $tRec['status'];
$coverage     = $tRec['coverage'];
$purchaseMeth = $tRec['purchaseMethod'];
$activated    = $tRec['activated'];
$storeOwner   = $wzUserID;
$storeCountry = $tRec['geoCountry'];
$storeProvince = $tRec['geoProvince'];
$storeCity = $tRec['geoCity'];
$displayStreet = $tRec['displayStreet'];
$storeStreet = $tRec['streetAd'];
$storeUnitNumber = $tRec['unitNbr'];
$postCD = $tRec['postCD'];
$storePhone = $tRec['storePhone'];
 
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
  return $str;
}


    if (isset($_GET['fwebsiteID'])){$websiteID = clean($_GET['fwebsiteID']);} else {$websiteID = "";}

    $SQL = "select count(*) as isOn From tblwzOnline  where wzUserID=".$wzUserID;  
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $thisUserON = $tRec['isOn'];
    
    $SQL = "SELECT franchise,cityID,partyMbrID,partyLeader, mute,sandBox, online,IP,";
    $SQL .= "sex,age,nfans,paidMember,certifiedStatus, firstname,lastname,email,";
    $SQL .= "country,prov,city, timezone From tblwzUser  where wzUserID=".$wzUserID;  
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if ($tRec) {
       $primContact  = $tRec['firstname'];
       $profileText  = $storeDesc;
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
              
       if (!is_null($sex)) {
         if ($sex == 1) {
           $sex = "f - ";
           }
         else {
           $sex = "m - ";
         }
       }

       $certified = $tRec['certifiedStatus'];
       if (is_null($certified)) { 
         $certified = 0;
       }
     }
     else{
       header('Location: https://www.bitmonky.com/whzon/mblp/wzMbl.php?noUser');
       
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

    $folLink  = "<a title='Visit ".$primContact."`s Fan Room' style='font-size:9px;color:#777777;text-decoration:none;font-weight:normal;' href='https://bitmonky.com/whozon/mbrfanRoom.asp?fwzUserID=".$wzUserID."'>".$folAmt."</a>";

    $followLink = "<a style='font-size:9px;' ".$folJS.">";
    $followLink .= "<img title='Follow ".$primContact."' style='margin-right:2px;border:0px solid #dddddd;vertical-align:middle;' src='https://image.bitmonky.com/img/folowIcon_lt.jpg'></a>"; 
    $followLink .= "<img style='display:inline;vertical-align:middle;' src='https://image.bitmonky.com/img/folAmtLFT.jpg'>"; 
    $followLink .= "<table style='display:inline;vertical-align:middle;border-collapse:collapse;border:0px;margin:0px;'><tr>";
    $followLink .= "<td style=height:15px;font-size:9px;white-space:nowrap;border:0px;padding:0px;background-image:url('https://bitmonky.com/folAmtBK.jpg');>".$folLink."";
    $followLink .= "</td></tr></table><img style='display:inline;vertical-align:middle;' src='https://image.bitmonky.com/img/folAmtRT.jpg'>";
    $followLink .= " <a  "  & folJS.">".$linkTxt."</a>"; 
        
    echo $followLink;

  }
?>
