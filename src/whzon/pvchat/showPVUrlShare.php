<?php
include_once("../mkysess.php");
ini_set('display_errors',1); 
error_reporting(E_ALL);

$inMsgID = safeGET('msgID');
$mode  = safeGET('mode');
$url   = safeGET('inURL');
$com   = safeGET('com');
$empID = safeGET('empID');
$jobID = safeGET('jobID');

echo "<div class='infoCardClear' style='background:#222222;'>";
$spos = mkyStripos($url,'bitmonky.com');
if ($spos !== false){
  //exitEr("Sorry You Can't Share That Link...");
}
$spos = mkyStripos($url,'image.');
if ($spos !== false){
   exit("EMOT");
}
$isYTubeChan = null;
$isYouTube = mkyStripos($url,"youtube.com") | mkyStripos($url,"youtu.be");
if ($isYouTube){
  $isYTubeChan = mkyStripos($url,"/channel");
}

$doc = tryFetchURL($url);
$doc = mkyStrReplace("'","\"",$doc);
$doc = mkyStrReplace("/>",">",$doc);
$doc = mkyStrReplace(" >",">",$doc);
$doc = mkyStrReplace("< ","<",$doc);


//$doc = preg_replace('/\s+/', '', $doc);
$doc = mkyTrim(preg_replace('/\s\s+/', ' ', mkyStrReplace("\n", " ", $doc)));
$title  = getTag('<title>','</title>',$doc);
$otitle = getTag('og:title" content="','">',$doc);
$img    = getTag('og:image" content="','">',$doc);
$desc   = getTag('og:description" content="','">',$doc);
$dom    = parse_url($url);
$dom    = $dom['host']; 
$author = 'null';
$channel = 'null';
if ($isYouTube){
  if (!$isYTubeChan){
    $title = getTag('"title":"','",',$doc);
    $desc  = getTag('"shortDescription":"','",',$doc);
    $desc  = mkyStrReplace('\\n','<br/>',$desc);
    //$desc  = "A YouTube Video";
    $author  = "'".left(getTag('"author":"','",',$doc),120)."'"; 
    $channel = "'".left(getTag('"ownerChannelName":"','",',$doc),180)."'";
    $img   = 'https://i.ytimg.com/vi/';

    $img  .= getUTubeTag('{\"videoId\":\"','\",',$doc);
    $img  .= '/hqdefault.jpg';
    $img = fixUTubeImg($url,$img);
    //$img  = preg_replace('#/+#','',$img);
    //$img  = mkyStrReplace('\\','/',$img);
  }
}
if ($mode != 'share'){
  if ($doc == ''){
    exitEr("Link URL not found...");
  }
  else {
    /*
    echo "<div align='right'><input type='button' onclick='doShareLinkNow(\"".$url."\")' value=' Share Link '/> ";
    echo "<input type='button' onclick='hideShare();' value=' Cancel '/> ";
    echo "</div>";
    */
    if ($img){
      echo "<a target='_new' href='".$url."'>";
      echo "<img style='float:left;margin:0px 18px 18px 0px;border-radius:0.5em;width:110px;height:75px;' src='".$img."'>";
      echo "</a>";
    }
    echo "<div>";
    if ($otitle) {
      echo "<b>".$otitle."</b>";
    }
    else if ($title){
      echo "<b>".$title."</b>";
    }
    if ($desc){
      echo "<br/>".$desc;
    }
    else {
      echo "<br/>No description found... ";
    }
    echo "<br/><a target='pvcURLs' href='".$url."'>".$dom."</a>";
    echo "</div>";
  }
}
echo "<br clear='left'></div>";
if ($mode == 'share'){
  $newSID = null;
  $oldSID = null;
  $SQL = "select urlShareID from newsDirectory.tblUrlShares where urlShareUID = ".$userID." and urlLink = '".$url."'";
  $mResult = mkyMyqry($SQL);
  
  $mRec = mkyMyFetch($mResult);
  if (!$mRec){
    $SQL = "insert into newsDirectory.tblUrlShares (urlShareUID,urlLink,urlImgLink,urlTitle,urlDesc,urlComment,urlUTauthor,urlUTChan) ";
    $SQL .= "values (".$userID.",'".$url."','".$img."','".$title."','".$desc."','".$com."',".$author.",".$channel.")";
    $mResult = mkyMyqry($SQL);

    $SQL = "select urlShareID from newsDirectory.tblUrlShares where urlShareUID = ".$userID." and urlLink = '".$url."'";
    $mResult = mkyMyqry($SQL);
    $mRec = mkyMyFetch($mResult);
    $newSID = $mRec['urlShareID'];
  }
  else {
    $oldSID = $mRec['urlShareID'];
  }
  if ($newSID){
    $newACID = acNewUrlShare($userID,$newSID,$empID,$jobID);
  }
  if ($newSID){
    $urlSID = $newSID;
  } 
  else {
    $urlSID = $oldSID;
  }
  shareToRoom($urlSID,$img,$url,$userID,left($title,250));
  echo "Link Shared...";
}

function getTag($s,$e,$doc){
  $spos = mkyStripos($doc,$s);
  if ($spos === false){
    return null;
  }
  $spos = $spos + strlen($s);
  $tag = right($doc,strlen($doc) - $spos);

  $epos = mkyStripos($tag,$e);
  if ($epos === false){
    return null;
  }
  $tag = left($tag,$epos);
  if ($tag == '' || $tag ==  ' '){
   return null;
  }
  return $tag;
}

function exitEr($msg){
  $pframe = 'parent.';
  if ($GLOBALS['sessISMOBILE']){
    $pframe = null;
  }

  echo "<div align='right'>";
  echo "<input type='button' onclick='".$pframe."hideShare(".$GLOBALS['inMsgID'].");' value=' Hide [x] '/> ";
  echo "</div>";
  echo $msg;
  exit('');
}
//------------------------------------------------------------------------------
function acNewUrlShare($inUserID,$urlSID,$empID,$jobID){
//------------------------------------------------------------------------------
    $SQL = "select count(*) as nRec from tblActivityFeed where acCode=18 and acItemID=".$urlSID." and wzUserID=".$inUserID;
    $result = mkyMsqry($SQL);
    $tRec   = mkyMsFetch($result);
    if ($tRec['nRec'] > 0){
      return 0;
    }

    $SQL    = "select cityID from tblwzUser where wzUserID=".$inUserID;
    $result = mkyMsqry($SQL);
    $tRec   = mkyMsFetch($result);
    $cityID = $tRec['cityID'];
    $dstamp = mkySQLDstamp();

    $SQL = "Insert into tblActivityFeed  (websiteID,wzUserID,acCode,acLink,acItemID,acCityID,acDate) ";
    $SQL .= "Values (0,".$inUserID.",18,'',".$urlSID.",".$cityID.",'".$dstamp."')";
    $result = mkyMsqry($SQL);

    $SQL = "SELECT activityID FROM tblActivityFeed  Where wzUserID=".$inUserID." and acdate='".$dstamp."'";
    $result = mkyMsqry($SQL);
    $tRec   = mkyMsFetch($result);

    $acNewACID = $tRec['activityID'];

    $SQL = "update newsDirectory.tblUrlShares set urlACID=".$acNewACID." WHERE urlShareID=".$urlSID;
    $result = mkyMyqry($SQL);
    return $acNewACID;
}
function shareToRoom($urlSID,$img,$link,$shareID,$title){

  $SQL = "select inChatChanID, firstname ,TIMESTAMPDIFF(second,lastShare,now()) as tShare from tblwzOnline  ";
  $SQL .= "where wzUserID=".$shareID;
  $result = mkyMsqry($SQL) or die($SQL);
  $tRec = mkyMsFetch($result);

  if ($tRec) {
    if ($tRec['tShare'] > 120 || $tRec['tShare'] === null || $shareID == 63555){
      $channel   = $tRec['inChatChanID'];
      $shareName = $tRec['firstname'];
      $SQL = "update tblwzOnline set lastShare = now() where wzUserID = ".$shareID;
      $cresult = mkyMsqry($SQL) or die($SQL);

      $timestamp = mkySQLDstamp();

      $SQL = "insert into tblChatterBox (wzUserID,msg,channel,isShare,isVshare,urlShareImg,cDate,videoID,urlShareLink) ";
      $SQL .= "values(".$shareID.",N'".$title."',".$channel.",1,4,'".$img."','".$timestamp."',".$urlSID.",'".$link."')";
      $result = mkyMsqry($SQL) or die($SQL);

      $SQL = "select msgID from tblChatterBox where wzUserID=".$shareID." and channel=".$channel." and cDate='".$timestamp."'";
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);

      if ($tRec) {
        $msgID = $tRec['msgID'];
      }
      else {
        $msgID = 0;
      }
      $SQL = "insert into tblChatterBuf (wzUserID,msg,channel,realID,isShare,isVshare,urlShareImg,videoID,urlShareLink) ";
      $SQL .= "values(".$shareID.",N'".$title."',".$channel.",".$msgID.",1,4,'".$img."',".$urlSID.",'".$link."')";
      $result = mkyMsqry($SQL);
    }
  }
}
?>
