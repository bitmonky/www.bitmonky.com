<?php 
  ini_set('display_errors',1);
  error_reporting(E_ALL);
include_once("whzon/mkyPHPInc.php");
include_once('whzon/mblDetect/Mobile_Detect.php');
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$gsoftON = mkyStripos($_SERVER['SERVER_NAME'],'gsoft.bitmonky.com');
if ($detect->isMobile() || $detect->isTablet() || $gsoftON !== false){
  if (isset($_SERVER['QUERY_STRING'])){$qry = "?".$_SERVER['QUERY_STRING'];}else {$qry=null;}
  if ($qry == '?'){
    $qry = null;
  }
  if ($qry){
    if (mkyStrpos($qry,"?") === false){
      $qry .= "?mblp=on";
    }
    else {
      $qry .= "&mblp=on";
    }
  }
  include_once('whzon/mblp/wzMblTPL.php');
  exit("");

}
include_once("whzon/wzAppTPL.php");
exit('');
?>
