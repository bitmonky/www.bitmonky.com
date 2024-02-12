<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../gold/goldInc.php");
$taskAmt = getTaskPrice('geoBan');

if ($userID !=0){
  if (isset($_GET['bannerID'])){$newBannerID = clean($_GET['bannerID']);} else {$newBannerID=null;}
  $wzUserID = clean($_GET['fwzUserID']);
  $scope    = clean($_GET['fscope']);
  $cprText  = clean($_GET['cprText']);
  $cprURL   = clean($_GET['cprURL']);

  if ( $cprText == ''){
    header('Location:  getImgCredits.php?ferror=noText&wzID='.$sKey.'&fwzUserID='.$wzUserID.'&fscope='.$scope.'&bannerID='.$newBannerID);
    exit('');
  }

  $testit = tryFetchURL($cprURL);
  if ( $testit === False){
    header('Location:  getImgCredits.php?ferror=badURL&wzID='.$sKey.'&fwzUserID='.$wzUserID.'&fscope='.$scope.'&bannerID='.$newBannerID);
    exit('');
  }

  $SQL = "select count(*) as nRec from tblCopyRightCredits where cprAcCode=20 and cprAcItemID=".$newBannerID;
  $result = mkyMsqry($SQL) or die($SQL);
  $cRec = null;
  $resultCR = mkyMsqry($SQL);
  $cRec = mkyMsFetch($resultCR);
  $nCredits = $cRec['nRec'];
  
  if ($nCredits == 0){
    $SQL = "insert into tblCopyRightCredits (cprAcCode,cprAcItemID,cprText,cprURL,cprTaskStatus) ";
    $SQL .= "values (20,".$newBannerID.",'".$cprText."','".$cprURL."',1)";
  }
  else {
    $SQL = "update tblCopyRightCredits set cprTaskStatus=1, cprText='".$cprText."',cprURL='".$cprURL."' where cprAcCode=20 and cprAcItemID=".$newBannerID;
  } 
  
  $result = mkyMsqry($SQL) or die($SQL);
  
  if (makeGoldTransaction($userID,$taskAmt,'eGold','Task updateCredits',null)){
  
    $SQL  = "insert into tblGoldEarnLog (earnUID,amount,earnType) ";
    $SQL .= "Values(".$userID.",".$taskAmt.",'taskReward')";
    $result = mkyMsqry($SQL) or die($SQL);
  }
  if ($scope == "myCity"){
    $SQL = "update tblTaskCities set TaskDone = now() where taskBannerID = ".$newBannerID;
    $result = mkyMsqry($SQL);
  }
}

header('Location:  getImgCredits.php?fmoder=done='.$sKey.'&fwzUserID='.$wzUserID.'&fscope='.$scope.'&bannerID='.$newBannerID);

?>
