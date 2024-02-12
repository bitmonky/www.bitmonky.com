<?php
include_once('newsHLObjs.php');
ini_set('display_errors',1);
error_reporting(E_ALL);

$fsq = safeGET('fsq');
$mkey = new mkySess($sKey,$userID);
//$webItem = new mkyActivityCard($mkey,$cityID,$scope,'boost','Boosted Posts',$fsq);
//$fsqry = null;
//$fsqry = $webItem->getQry();
if (!$sessISMOBILE){
  $mdir = null;
}
else {
  $mdir = "mblp/";
}

?>
<p>
<div ID='myTownBoosts' class='infoCardClear'>
<B><span class='wzBold' style='color:darkKhaki;'>Boosted Items - <?php echo $scopeDisplay ?></span> - See Also </b>
<?php 

drawMyMenu($myMode,$modes);
echo "<p/>";
?>
<div style='background:fireBrick;color:white;border-radius: .5em;padding:8px;'>
<img style='float:right;width:7em;height:7em;margin-left:2em;' src='//image.bitmonky.com/img/stopSign.png'/>
<h2>Read This Before You Post </h2>
<div class='infoCardClear' style='font-size:larger;'>
These are boosted posts. You can earn BGMP paid by the post owner for your opinion. But be warned, 
if you give copied or off topic responses just to get the gold you will be thrown into the 
<a target='mkyJail' style='color:gold;' href='/whzon/wzApp.php?wzID=<?php echo $sKey.'&furl='.urlencode('/whzon/public/viewJailHouse.php?wzID='.$sKey);?>'> BitMonky Jail</a> 
and have to pay a $5.00 CAD fine to get back out. 
</div>
</div>
<p/>
<table width='100%' class='myTown'><tr valign='top'><td>

<?php

$cityName = null;
if (isset($_GET['mode'])) {$mbl = true;} else {$mbl = null;}

$boostID = null;
$boostRef = null;

if ($userID != 0){
  $SQL = "Select boostID,boostCount,gperBoost,nBoosts, boostACID from tblLikeBoosts  "; 
  $SQL .= "inner join tblwzUser  on tblwzUser.wzUserID = boostUID ";
  $SQL .= "left join tblwzUserLikes  on likeActivityID = boostACID and tblwzUserLikes.wzUserID = ".$userID." ";
  $SQL .= "left join tblActivityFeed  on activityID = boostACID ";
  $SQL .= "left join tblBoostSkip  on bskBoostID = boostACID and bskUID = ".$userID." ";
  $SQL .= "where tblLikeBoosts.mrkForDel is null and sandBox is null and NOT doNotList = 1 and done is null and likeActivityID is null ";
  //$SQL .= "and gperBoost > ".$minBoostAmt." ";
  $SQL .= "and NOT activityID is null and bskUID is null and NOT boostUID = ".$userID." ";
  $SQL .= "order by gperBoost desc limit 12";

	
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
	
  if (!$tRec){
    echo "<h3>No More Boosted Posts Found... Try Again Later.</h3>";
  }
  while ($tRec){
    $acID = $tRec['boostACID'];
    $boostAmt = $tRec['gperBoost'];
    $boostID   = $tRec['boostID'];
    $boostLink = getBoostLink($acID,$sKey,$sessISMOBILE);
    $boostRef  = getRefLink($boostLink);

    if($boostLink){
      $boostLink = str_ireplace('href=','style="color:lightGray;"',$boostLink);
      $htm = $boostLink.'<img ID="boostImg'.$acID.'" onerror="hideImageID(\'boostImg'.$acID.'\');" style="float:left;border-radius:0.5em;height:90px;width:115px;border:0px;margin-right:1.5em;" src="';
      $htm .= getBoostImg($acID,$sKey,$boostID).'"/> ';
    //$htm .= '<img style="border-radius:0.5em;float:left;margin-right:13px;" src="//image.bitmonky.com/img/bitGoldCoin.webp"/>  ';
      $htm .= getTRxt('Your Opinion Is Wanted... Earn').' '.mkyNumFormat($tRec['gperBoost']).' BMGP For Your Response';

      echo "<div ID='ccBoost".$boostID."'  class='infoCardClear' style='background:#222222;border-radius: .5em;padding:1em;'>";
      echo $htm;
      echo "</a><br clear='left'/>";
      echo "<div align='right' style='margin-top:3px;'>";
      echo "<form>";
      echo "<input style='' type='button' value= ' ".getTRxt(' Accept Task ')." ' onclick='".$boostRef."'/> ";
      echo "<input type='button' value= ' ".getTRxt(' Skip ' )." ' onclick='parent.doChanSkipBoost(".$boostID.");'/>";
      echo "</form></div>";
      echo "</div>";
    }
    $tRec = mkyMsFetch($result);
  }
}
if (!$sessISMOBILE){
  echo "</td>";
  echo "<td style='width:350px;padding-left:25px;'>";
}
else {
  echo "<p/>";
}
echo getBigCubeAds('0px',2);
echo "</td></tr></table>";

function getRefLink($txt){
   if (!$txt){
     return null;
   }
   $txt = mkyStrIReplace('<a href="','',$txt);
   $txt = mkyStrIReplace("<a href='",'',$txt);
   $txt = mkyStrIReplace("'>",'',$txt);
   $txt = mkyStrIReplace('">','',$txt);
   $txt = mkyStrIReplace("'",'"',$txt);
   $txt = mkyStrIReplace('javascript:','',$txt);
   return $txt;
}

function getBoostLink($acID,$sKey,$mblp){
   global $userID;
   $SQL = "Select acCode, acItemID,wzUserID from tblActivityFeed  "; 
   $SQL .= "where activityID = ".$acID;
	
   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);
   if ($mblp){
     $mblp = "mblp/";
   }  
   if ($tRec){
     $acCode = $tRec['acCode'];
     $itemID = $tRec['acItemID'];
     $owner  = $tRec['wzUserID'];

     if ($userID == 17621 || $userID=82598){
       //return "<a href='javascript:document.location.href=\"http:bitmonky.com\";'>";
     }
	  
     if ($acCode == 7){
       return "<a href=\"javascript:parent.clickAdSpot2('/whzon/".$mblp."mbr/mbrViewPhotos.php?wzID=".$sKey."&vPhotoID=".$itemID."&fwzUserID=".$owner."');\">";
     }	
     if ($acCode == 17){
       if ($mblp){
         return "<a href=\"javascript:parent.clickAdSpot2('/whzon/".$mblp."vidView/viewVideoPg.php?wzID=".$sKey."&videoID=".$itemID."');\">";
       }
       return "<a href=\"javascript:parent.clickAdSpot2('/whzon/mbr/vidView/viewVideoPg.php?wzID=".$sKey."&videoID=".$itemID."');\">";
     }	
     if ($acCode == 18){


       $SQL = "select urlBBX,urlLink from newsDirectory.tblUrlShares where urlShareID=".$itemID;
       $ures = mkyMyqry($SQL);
       $uRec = mkyMyFetch($ures);
       if ($uRec){
         if ($uRec['urlBBX']){
           return "<a href='javascript:parent.document.location.href=\"".$uRec['urlLink']."\";'>";
         }
       }

       return "<a href=\"javascript:parent.clickAdSpot2('/whzon/".$mblp."mbr/mbrViewWNewsShare.php?wzID=".$sKey."&newsID=".$itemID."');\">";
     }	
     if ($acCode == 19){
       return "<a href=\"javascript:parent.clickAdSpot2('/whzon/".$mblp."mbr/mbrViewSItemShare.php?wzID=".$sKey."&itemID=".$itemID."');\">";
     }	
     if ($acCode == 4){
       return "<a href=\"javascript:parent.clickAdSpot2('/whzon/".$mblp."mbr/mbrViewClassified.php?wzID=".$sKey."&itemID=".$itemID."');\">";
     }	
     if ($acCode == 23){
       return "<a href=\"javascript:parent.clickBoostLS('/whzon/live/chan/chanLiveStreams.php?wzID=".$sKey."&videoID=".$itemID."');\">";
     }
   }
   return null;
}
function mrkBForDel($id){
   $SQL = "update tblLikeBoosts set mrkForDel = now() ";
   $SQL .= "where boostID = ".$id;

   $result = mkyMsqry($SQL);
}
function getBoostImg($acID,$sKey,$boostID){
   $SQL = "Select acCode, acItemID,wzUserID from tblActivityFeed  "; 
   $SQL .= "where activityID = ".$acID;
	
   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);

   if ($tRec){
     $acCode = $tRec['acCode'];
     $itemID = $tRec['acItemID'];
     $owner  = $tRec['wzUserID'];
	  
     if ($acCode == 7){
       return "//image.bitmonky.com/getPhotoTmn.php?id=".$itemID;
     }	
     if ($acCode == 17){
       $SQL = "select vidURL from tblwzVideo  where wzVideoID=".$itemID;
       $result = mkyMsqry($SQL);
       $tRec = mkyMsFetch($result);
       if ($tRec){
         $img = $tRec['vidURL'];
         $img = preg_replace('/.*youtube.com/','youtube.com',$img);
         $img  = mkyStrReplace("youtube.com/v","//i2.ytimg.com/vi",$img);
         $img  = $img."/default.jpg";
         return $img;
       }
       else {
         mrkBForDel($boostID);
       }
     }	
	 
     if ($acCode == 4){
       $SQL = "select imgFlg from tblClassifieds  where adID = ".$itemID;
       $cresult = mkyMsqry($SQL);
       $cRec = mkyMsFetch($cresult);
       if ($cRec){
         if ($cRec['imgFlg'] == 1){
           return " //image.bitmonky.com/getClassTmn.php?id=".$itemID;
         }
         else {
           return "//image.bitmonky.com/img/classBoost.jpg";
         }
       }
       else {
         mrkBForDel($boostID);
       }
     }	
     if ($acCode == 18){
       $SQL = "select urlBBX,urlImgLink from newsDirectory.tblUrlShares where urlShareID=".$itemID;
       $ures = mkyMyqry($SQL);
       $uRec = mkyMyFetch($ures);
       if ($uRec){
         if ($uRec['urlBBX']){
           return smgrFixBBoxImg($uRec['urlImgLink']);
         }
       }
       return "//image.bitmonky.com/getNShareImg.php?id=".$itemID;
     }	
     if ($acCode == 19){
       $SQL = "select itemStoreID from tblClassifieds  where adID = ".$itemID;
       $result = mkyMsqry($SQL);
       $tRec = mkyMsFetch($result);
       if ($tRec){
         $storeID = $tRec['itemStoreID'];
         return "//image.bitmonky.com/getStoreItemImg.php?ad=".$itemID."&fs=".$storeID;
       }
       else {
         mrkBForDel($boostID);
       }
     }	
     if ($acCode == 23){
       $SQL = "select bname from tblChanLiveStreams   where cstrID = ".$itemID;
       $presult = mkyMsqry($SQL);
       $pRec = mkyMsFetch($presult);
       if ($pRec){
         $src = "//image.bitmonky.com/whzon/live/thumbs/".$pRec['bname'].".png";
         return $src;
       }
       else {
         mrkBForDel($boostID);
       }
     }
   }
   return "//image.bitmonky.com/img/monkyTalkfbCard.png";
}

?>
