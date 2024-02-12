<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../pvchatInc.php");
include_once("../gold/goldInc.php");
include_once('oaiFRespondAsInc.php');
$mbrID=clean($_GET['fmbrID']);



if (!$userID==0){
    $modFlg = 'null';
    if ($isMod) {$modFlg = 1;}
	if ($isAdmin) {$modFlg = 2;}
	
    $msg = clean($_GET['fmsg']);

    $SQL  = "Insert into ICDchat.tblMbrChat (msg,msgUserID,msgMbrID,sentBy,modFlg) ";
    $SQL .= "values('".left($msg,5999)."',".$userID.",".$mbrID.",".$userID.",".$modFlg.")"; 
    $myresult = mkyMyqry($SQL);
    if ($mbrID != 63555){
      if (isPvChatAvailable($userID,$mbrID)){
        //$lastMsg = getMTokenTime($mbrID);
        $lastAction = getLastAction($mbrID);
        if ($lastAction > 250 || $lastAction === null){
          sendEAlert($userID,$mbrID,left($msg,5999));
        }
      }
    }  
    else {
      gfbug('mkypvchat'.$msg);    
      respondAsSiteMonkey($msg,$userID);
    }
    logReferralActivity(3,$userID);

    echo "OK";
}
function getLastAction($toUID){
    $SQL = "select TIMESTAMPDIFF(second,lastAction,now()) as lastAction ";
    $SQL .= "from tblwzUser  ";
    $SQL .= "left join tblwzOnline on tblwzOnline.wzUserID = tblwzUser.wzUserID ";
    $SQL .= "where tblwzUser.wzUserID = ".$toUID;

    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if (!$tRec){
      return null;
    }
    else {
      return $tRec['lastAction'];
    }
}
function sendEAlert($fromUID,$toUID,$msg){

    $SQL  = "SELECT wzUserID,firstname,email,verifyword,sex,cityID,city ";
    $SQL .= "from tblwzUser  ";
    $SQL .= "where wzUserID = ".$toUID;

    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if ($tRec){
      $m = "";
      $letters = randomKeyFill(8);

      $email        = $tRec['email'];
      $token        = getMToken($email);

      $UserID       = $tRec['wzUserID'];
      $firstname    = $tRec['firstname'];
      $verifyword   = $tRec['verifyword'];
      $cityID       = $tRec['cityID'];
      $cityName     = $tRec['city'];
      $uSex         = $tRec['sex'];

      if ( $verifyword == null || $verifyword == "" ) {
        $SQL = "update tblwzUser set verifyword='".$letters."' where wzUserID=".$UserID;
        $presult = mkyMsqry($SQL);
      }
      else {
        $letters = $verifyword;
      }

      //$email = "nofoolingz@gmail.com";
      //$email = "peter@whzon.com";

      $vword   = $letters;

      $whzdom  = "bitmonky.com";

      $a  = getALoginLink($UserID,$token);
      $ahref = getALoginHref($UserID,$token);

      $m .= "<div style='margin:auto;border-radius: .5em;background:green;color:white;padding:5px 15px 15px 15px;'>";
      $m .= "<div align='right'> ";
      $m .= "<span style='color:white;font-wieght:bold;'>".getTRxt('Networking For The People, By The People That Pays The People').".</span><p/> ";
      $m .= getLogoMonkyTalk();
      $m .= "</div>";
      $m .= "<span style='color:white;'><h2>Instant Message Alert</h2>";
      $m .= "You have an instant message waiting for you on monkytalk!  ";
      $m .= "Click on their picture and go straight to their profile.<p/></span>";

      $SQL  = "select wzUserID,firstname,pText from tblwzUser  ";
      $SQL .= "where wzUserID =".$fromUID;

      $presult = mkyMsqry($SQL);
      $pRec = mkyMsFetch($presult);
      $nDisp =0;
      if ($pRec){
        $mbrName   = $pRec['firstname'];
        $mbrUID = $pRec['wzUserID'];
        $pText  = "<p/>".left($pRec['pText'],250);
        $profile = 'https://bitmonky.com/whzon/mbr/emlinks/vfmnu.php?tku='.$token.'&mt='.$UserID.'&fi='.$vword.'&mu='.$mbrUID;

        $wsAnkor   = "<a  style='color:#222322;font-size:larger;' title='View ".$mbrName."`s Profile' ";
        $wsAnkor  .= "href='".$profile."'>";
        $wsImgStr  = $wsAnkor."<img style='float:left;margin: 0px;margin-right:.5em;border-radius:50%;width:60px;height:80px;' ";
        $wsImgStr .= "src='".$GLOBALS['MKYC_imgsrv']."/getMbrImg.php?id=".$mbrUID."'>Message From: ".$mbrName."</a> ";
        $wsImgStr .= "<p style='color:black;'>".$msg."</p>";

        $m .= "<div style='background:white;color:black;border-radius:.5em;padding:.5em;'>".$wsImgStr."<br clear='left'/></div><p/>";
      }
      $m .= "<span style='color:white;'>";

      $m .= "<p/><div align='right'><a style='color:orange;' href='".$ahref."'>Login Now And Continue The Conversation!</a></div>";
      $m .= "</span></div>";
      $m .= "<p/>";

      $m = getEHeader($m,'Name Change',$UserID);

      wzSendMail("BitMonky Alerts<support@bitmonky.com>", $email, "You Have A Private Message From ".$mbrName." On MonkyTalk!", $m,$token);
    }
}
?>
