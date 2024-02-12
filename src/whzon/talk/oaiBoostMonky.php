<?php
include_once('../mkysess.php');
include_once('../gold/goldInc.php');
//ini_set('display_errors',1);
//error_reporting(E_ALL);
$rcode = null;
$j = new stdClass;
$tran = new stdClass;
$postID    = safeGET('boostACID');
$postReply = safeGET('liketxt');

$mod     = safeGET('mod');
if (!$mod){
  $mod = 'gpt-3.5-turbo';
}

$SQL = "update tblwzOnline set lastAction = now() where wzUserID=63555";
mkyMyqry($SQL);

$p = buildPostText($postID);
if (!$p->text || !$p->ownerID){
  fail('No text or OwnerID not found:'.$pownerID);
}
$post    = $p->text;
$billUID = $p->ownerID;

$prompt = "Given this post:\"".$post."\" rank the following  reply  from 1 to 10 for 
   how relevant it is to the post.   Reply: \"".$postReply. "\" Also rate it from 1 to 10 for effort put into the reply. 
   Please respond in JSON format  like this {\"relevance\" : value, \"effort\" :value }";

gfbug('chanChat::'.$prompt);

$msg = new stdClass;
$msg->action = "getTextNow";
$msg->role   = "system";
$msg->prompt = $prompt;
$msg->n      = 1;
$msg->maxTokens = 620;
$msg->useModel = $mod;

$pUrl = "https://antsrv.bitmonky.com:13381/netREQ/msg=".mkyUrlEncode(json_encode($msg));

$res = getAccToken($pUrl);
$res = json_decode($res);
if ($res){
  if (!$res->result){
    fail($res->message);
  }
}
else {
  fail ('likeBoostOAI JSON Response Error:');
}
$usage = ' : cost - '.$res->usage->total_tokens;
gfbug('likeBoostOAI usage:'.$usage);
$res->spent = billAIUser($billUID,$res->usage->prompt_tokens,$res->usage->completion_tokens,$mod);
respond($res);

function buildPostText($postACID){
  $j = new stdClass;
  $j->ownerID = null;
  $j->text  = null;
  $SQL = "select activityID,tags, acItemID, actvName,wzUserID,acCityID,acDate 
    from tblActivityFeed 
    inner join tblActivity  on activityCD = acCode 
    where activityID = ".$postACID;
  $res = mkyMyqry($SQL);
  $rec = mkyMyFetch($res);  
  if (!$rec){
    return $j;
  }
  $j->text = getWords($rec);  
  $j->ownerID = $rec['wzUserID'];
  return $j;
}
function getWords($acRec){
  $objType  = $acRec['actvName'];
  $acItemID = $acRec['acItemID'];
  $acID     = $acRec['activityID'];
  $acUID    = $acRec['wzUserID'];
  if (!$acItemID && $objType == 'mbrs'){
    $acItemID = $acUID;
  }
  else {
    if (!$acItemID){
      $acItemID = 0;
    }
  }
  $rec      = null;
  if ($objType == 'mshare'){
    $SQL  = "SELECT  urlTitle title,urlDesc acDesc FROM newsDirectory.tblUrlShares ";
    $SQL .= "where urlShareID = ".$acItemID;

    $myresult = mkyMyqry($SQL);
    if ($myresult){$tRec = mkyMyFetch($myresult);} else { $tRec=null;}
  }
  else {
    $SQL = null;
    $tRec = null;
    if ($objType == 'class'){
      $SQL  = "SELECT  item title,left(AdBody,500) acDesc FROM tblClassifieds ";
      $SQL .= "where adID = ".$acItemID;
    }
    if ($objType == 'photo'){
      $SQL  = "SELECT  title,left(photoTxt,500) acDesc FROM tblwzPhoto  ";
      $SQL .= "inner join tblwzPhotoAlbum  on tblwzPhotoAlbum.wzPhotoAlbumID = tblwzPhoto.wzPhotoAlbumID ";
      $SQL .= "where privacy < 2 and photoID = ".$acItemID;
    }
    if ($objType == 'web'){
      $SQL  = "SELECT  Title title,Category2.name+' '+Description acDesc FROM tblWebsites  ";
      $SQL .= "left join Category2 on categoryID = oldCategoryID ";
      $SQL .= "where minWebFlg=0 and  websiteID = ".$acItemID;
    }
    if ($objType == 'mBlog'){
      $SQL  = "SELECT  title, left(entry,500) acDesc FROM tblMBlogEntry  ";
      $SQL .= "where mBlogEntryID = ".$acItemID;
    }
    if ($objType == 'mbrs'){
      $SQL  = "SELECT  firstname title, left(pText,500) acDesc FROM tblwzUser  ";
      $SQL .= "where wzUserID = ".$acItemID;
    }
    if ($objType == 'video'){
      $SQL  = "SELECT vTitle title,vDesc acDesc FROM tblwzVideo  ";
      $SQL .= "where wzVideoID = ".$acItemID;
    }
    if ($objType == 'chan'){
      $SQL = "select name title,guide+' '+hcoHash acDesc from tblHashChanOwner  ";
      $SQL .= "inner join tblChatChannel on channelID = hcoChatChanID ";
      $SQL .= "where tblChatChannel.channelID = ".$acItemID;
    }
    if ($SQL){
      $result = mkyMsqry($SQL);
      $tRec   = mkyMsFetch($result);
    }
  }
  if ($tRec){
    $data = $tRec['title']." ".$tRec['acDesc'];
    return $data;
  }
  else {
    return "";
  }
}
function respond($data){
  $result = true;
  fail('Monkey Response Created',$data,$result);
}
function tranFail($msg='Database Transaction Failed... Try Later'){
  mkyRollback();
  fail($msg);
}
function fail($msg,$data='',$result=false){
  $j = new stdClass;
  $j->result = $result;
  $j->message = $msg;
  $j->data = $data;
  $j->prompt = $GLOBALS['prompt'];
  //$j->cost  = $GLOBALS['bmgpPrice'];
  exit(json_encode($j));
}
function getAccToken($url,$method='GET'){
    global $rcode;
    $crl = curl_init();
    $timeout = 5;
    curl_setopt ($crl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt ($crl, CURLOPT_URL,$url);
    curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt ($crl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt ($crl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
    curl_setopt ($crl, CURLOPT_MAXREDIRS,5);
    curl_setopt ($crl, CURLOPT_REFERER, 'https://monkytalk/');
    curl_setopt ($crl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt ($crl, CURLOPT_HTTPHEADER , array(
      'accept: application/json',
      'content-type: application/json')
    );
    $ret = curl_exec($crl);
    $furl = curl_getinfo($crl, CURLINFO_EFFECTIVE_URL);
    if (!curl_errno($crl)) {
      $info = curl_getinfo($crl);
      $rcode = $info['http_code'];
    }

    curl_close($crl);
    return $ret;
}
?>
