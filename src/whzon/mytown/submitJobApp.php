<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../gold/goldInc.php");

if ($userID !=0){
  $jobID    = safeGetINT('jobID');
  $cityID   = safeGetINT('cityID');
  $tycID    = safeGetINT('tycID');

  $SQL = "select cityID from tblwzUser where wzUserID=".$userID;
  $result = mkyMsqry($SQL);
  $resultCR = mkyMsFetch($result);
  if ($resultCR['cityID'] != $cityID){
    exit("Please Only Apply To Jobs In Your Own City");
  }
  $SQL = "select count(*) as nRec from tblTycJobApplications where tjaUID = ".$userID." and tjaJobID = ".$jobID;
  $result = mkyMsqry($SQL);
  $cRec = null;
  $resultCR = mkyMsqry($SQL);
  $cRec = mkyMsFetch($resultCR);
  $nRec = $cRec['nRec'];
  
  if ($nRec == 0){
    $SQL  = "insert into tblTycJobApplications (tjaUID,tjaCityID,tjaJobID,tjaTycoonID,tjaDate) ";
    $SQL .= "values (".$userID.",".$cityID.",".$jobID.",".$tycID.",now())";
    $result = mkyMsqry($SQL) or die($SQL);
  }
  else {
    exit('You Have Already Applied For This Job');
  }
  sendJobAppNotice($tycID,$cityID,$userID);
  exit('Application Sent');
}
exit('Login To Apply');


function sendJobAppNotice($tycID,$cityID,$appUID){

      $SQL = "select wzUserID,firstname,verifyWord,countryID,sex,usrAllowEAds from tblwzUser  ";
      $SQL .= "where wzUserID = ".$tycID;
      $main = mkyMsqry($SQL);
      $tRec = mkyMsFetch($main);
    

      $letters = randomKeyFill(8);

      $email        = $tRec['email'];
      $token        = getMToken($email);

      $UserID       = $tRec['wzUserID'];
      $firstname    = $tRec['firstname'];
      $verifyword   = mkyTrim($tRec['verifyWord']);
      $countryID    = $tRec['countryID'];
      $sex          = $tRec['sex'];
      $allowEAds    = $tRec['usrAllowEAds'];

      if ( $verifyword == '' ) {
        $SQL = "update tblwzUser set verifyword='".$letters."' where wzUserID=".$UserID;
        $presult = mkyMsqry($SQL);
      }
      else {
        $letters = $verifyword;
      }

      $goURL  = 'https://bitmonky.com/whzon/mbr/emlinks/vjapp.php?tku='.$token.'&mt='.$UserID.'&fi='.$letters;
      $abutt = "<a style='padding:3px;background:orange;color:white;border-radius:.15em;' href='".$goURL."'>";

      $Title = "You Have A New Job Application In Your City";

      $m  = "<div style='background:#222322;color:white;border:0px;border-radius: .5em;padding:8px;'>";
      $m .= "<h2>Job Application Notice</h2> ";

      $m .= "Someone has applied to a freelance position in your city or town ";   
      $m .= "please take a moment to login and review their application. ";
      $m .= "<p/>Please to not accept applications from people that do NOT actually live in ";
      $m .= "your city or town. ";

      $m .= "<p/>Login now View Their Application.";
      $m .= "<p/>".$abutt."Login Now</a>";
      $m .= "<br clear='right'/></div><p/>";

      $m     = emTranStr($m,$UserID,1);
      $Title = emTranStr($Title,$UserID,1);

      $m .= "<p/>";

      $m = getEHeader($m,'Name Change',$UserID);

      wzSendMail("BitMonky Tycoon Jobs Manager<support@bitmonky.com>", $email, $Title, $m,$token,$mtype);
      wzSendMail("BitMonky Tycoon Jobs Manager<support@bitmonky.com>", 'peter@icdirect.com', $Title, $m,$token,$mtype);
}
?>
