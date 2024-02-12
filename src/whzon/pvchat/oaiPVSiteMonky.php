<?php
include_once('../mkysess.php');
ini_set('display_errors',1);
error_reporting(E_ALL);
include_once('../talk/oaiEconInc.php');
include_once('../gold/goldInc.php');
$rcode = null;
$j = new stdClass;
$tran = new stdClass;
$msg = left(safeGET('msg'),800);
$uname = left(safeGET('uname'),100);
$mbrUID = safeGET('UID');
$digiHut = safeGET('digiHut');

$mtemp   = safeGET('mtemp');
$mod     = safeGET('mod');
$billUID = $mbrUID;
$prompt  = null;
if (!$mtemp){
  $mtemp = 0.85;
}
if (!$mod){
  $mod = 'gpt-3.5-turbo';
}

$mkyOwner    = "Peter";
$mkySite     = "bitmonky.com";
$mkyTitle    = "BitMonky DigiHuts";
$mkyLocation = "Toronto, Ontario, CANADA";

$siteInfo  = "bitmonky.com basic info if you are asked. \"";
$siteInfo .= "BitMonky Token is ERC20 minted on the MINTME blockchain. ";
$siteInfo .= "BMGP short for BitMonky Gold Piece. ";
$siteInfo .= "BMGP are a derivative backed by the BitMonky Token. ";
$siteInfo .= "BMGP can only be earned on bitmonky or purchased from other members. ";
$siteInfo .= "Members can withdraw BMGP to a self custody BitMonky wallet or sell them for Dogecoin on the GJEX. ";
$siteInfo .= "One BitMonky equals 500 BMGP. ";
$siteInfo .= "Members can buy and sell their BMGP on the GJEX ";
$siteInfo .= "GJEX is the sites market place. ";
$siteInfo .= "Free to join. ";
$siteInfo .= "DigiHut is what we call member profiles. ";
$siteInfo .= "Types of Huts are Regular Members, Business and YouTuber. ";
$siteInfo .= "BMGP can be used to buy advertising. ";
$siteInfo .= "BMGP can only be purchase from other members who have earned them. ";
$siteInfo .= "\" ".$ecod." ";

$websiteID = null;
$pText     = null;
if($digiHut && $digiHut != 'dc14fd698646636277d080a6a8854e9a'){
  $SQL = "select M.wmkyUseModel,U.wzUserID,U.firstname,W.Title,W.websiteID,W.udomain,pText,concat(U.city,', ',U.prov,', ',U.country)loc from tblwzUser U   
  inner join tblWebsites W on W.wzUserID = U.wzUserID  
  left join tblWebSiteMonkey M on wmkyWSID = W.websiteID  
  where U.mbrMUID = '".$digiHut."' limit 1";
  $res = mkyMyqry($SQL);
  $rec = mkyMyFetch($res);
  if ($rec){
    $mkyOwner    = $rec['firstname'];
    if ($rec['wzUserID'] != 17621){
      $billUID     = $rec['wzUserID'];
    }
    $mkySite     = $rec['udomain'];
    $mkyTitle    = $rec['Title'];
    $websiteID   = $rec['websiteID'];
    $mkyLocation = $rec['loc'];
    $pText       = $rec['pText'];
    $mod         = $rec['wmkyUseModel'];
    if (!$mod){
      $mod = 'gpt-4';
    }
    $siteInfo  = $mkySite." basic info if you are asked. {".$rec['pText']." ";
    $siteInfo .= " Business Location : ".$mkyLocation;
    $siteInfo .= " Business Name : ".$mkyOwner;
    $siteInfo .= " }";
    $ecod = null;
  } 
}  
$options = checkForPageInfo($websiteID,$msg,$mtemp,$mod);
if ($options !== null && $options != 'No Options'){
  $SQL = "select oaiBrief,description from ICDirectSQL.tblWebsitePgs where webPgID = ".urlencode($options);
  gfbug('checkForPageInfo:'.$SQL);
  $res = mkyMyqry($SQL);
  $infoReq = $pText;
  if ($res){
    $rec = mkyMyFetch($res);
    if ($rec){
      $infoReq = urldecode($rec['oaiBrief']);
      $infoReq .= " ".urldecode($rec['description']);
    }
  }  
}
else {
  $referals = checkForReferals($websiteID,$msg,$mtemp,$mod);     
  $SQL = "select woaisSummary from tblWebsiteOAISummary where woaisWSID = ".$websiteID;
  if ($res = mkyMyqry($SQL)){
    $rec = mkyMyFetch($res);
    if ($rec){
      $pText = $rec['woaisSummary'];
    }
  }     
  $infoReq = $pText;
}
if ($infoReq){
  $siteInfo .= " Additional Site Info : {".$infoReq."} ";
}

$SQL = "update tblwzOnline set lastAction = now() where wzUserID=63555";
  mkyMyqry($SQL);

  $ftopic = "The conversation is taking place in ".$uname."'s Private Chat Room";

  $SQL  = "update ICDchat.tblMbrChat ";
  $SQL .= "set mread = 1 ";
  $SQL .= "where (msgUserID=".$mbrUID." and msgMbrID = 63555) ";
  $SQL .= "and mread is null";
  mkyMyqry($SQL);

  $SQL  = "SELECT msgUserID,msgMbrID,msg FROM ICDchat.tblMbrChat ";
  $SQL .= "where (msgUserID=".$mbrUID." and msgMbrID = 63555) or (msgUserID=63555 and msgMbrID=".$mbrUID.") "; 
  $SQL .= "order by msgID desc limit 10";
  gfbug('consql:'.$SQL);
  $res = mkyMyqry($SQL);
  $rec = mkyMyFetch($res);
  $conv = "";
  if ($rec){
    $conv  = "";
    while ($rec){
      $atname = "@".$uname." ";    
      if ($rec['msgUserID'] != 63555){
        $atname = '@Agent.SiteMonkey.AI ';
      }      
      $conv = $atname.$rec['msg']." ".$conv;	  
      $rec = mkyMyFetch($res); 
    }
    gfbug("chatlog:".$conv);
  }    
  
$prompt  = 'Act as if you are human your name is Agent SiteMonkey AI, you are the host for '.$mkySite.' '.$ftopic.' ';
$prompt .= 'Give the response only with no explanations.  be creative. ';
$prompt .= 'Keep your reponses short. The users name is '.$uname.'.  ';
$prompt .= $siteInfo;
$prompt .= 'The conversation so far is as follows. '.$conv;
$prompt .= ' Please DO NOT start your responses with any salutation to the user. Do not mention \'@'.$uname.'\' at the start of your response. ';

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
  fail ('hcoStoryGen JSON Response Error:');
}
$usage = ' : cost - '.$res->usage->total_tokens;
gfbug('usage:'.json_encode($res->usage));
billAIUser($billUID,$res->usage->prompt_tokens,$res->usage->completion_tokens,$mod);

respond(oaiCleanUp($res->response));

function checkForReferals($websiteID,$prompt,$mtemp=0.85,$mod='gpt-3.5-turbo'){
  $SQL = "SELECT concat(Title,C.name)pageName,websiteID,wzUserID,wrefRateBMGP FROM ICDirectSQL.tblWebsiteReferral
  inner join tblWebsites on websiteID = wrefReferWSID
  inner join Category2 C on oldCategoryID = C.categoryID
  where wrefOwnWSID = ".$websiteID;
  $res = mkyMyqry($SQL);
  $rec = mkyMyFetch($res);
  if ($rec){
    $options = "{";
    $opts    = [];
    $coma = '';
    while ($rec){
      $rate = $rec['wrefRateBMGP'];
      if (!$rate){
	$rate = $GLOBALS['MKYC_hutDRate'];
      }
      $ts = checkAITokenSuply($rec['wzUserID']);
      if ($ts->bal >=  $rate){
        $pageName = urldecode($rec['pageName']);
        array_push($opts,$rec['websiteID']);
        $options .= $coma.'{"option":'.$rec['websiteID'].',"pageTitle":"'.$pageName.'"}';
	$coma = ',';
      }	
      $rec = mkyMyFetch($res);
    }
    if (count($opts) == 0){
      return false;
    }
    $prompt = 'Given the following webpage titles : '.$options.',"No Options"} what page would you need to read  to best
    respond to this prompt: "'.$prompt.'". Response  instructions:
    [respond with no explanations. Give the option number only. ]';
    $j = new stdClass;
    $j->option = sendPrompt($prompt,$mod,$mtemp);
    if (isPINT($j->option)){
      $SQL = "SELECT URL, Title,C.name,Description,websiteID FROM ICDirectSQL.tblWebsites
      inner join Category2 C on oldCategoryID = C.categoryID
      where websiteID = ".$j->option;
      $res = mkyMyqry($SQL);
      $rec = mkyMyFetch($res);
      if ($rec){
        doMakeReferal($rec,$j->option);
      }
    }  
  }
  return false;
}
function doMakeReferal($rec,$wsID){
  $prompt = "Sorry we do not offer that service but we can recommend you visit our associates website so 
  that they may help you. 
  <div class='infoCardClear' style='color:gray'>
  <a href='javascript:hutRefer(".$wsID.",\"".$GLOBALS['digiHut']."\")'>
  <img src='https://image.bitmonky.com/getWsImg.php?id=".$wsID."' style='width:100%'/></a>
  <h3 style='lightGray'>".$rec['Title']."</h3>
  ".$rec['Description']."
  <div style='clear:right'></div>
  </div>";
  respond($prompt);    	
}
function isPINT($str) {
  $intValue = intval($str);
  return is_numeric($str) && $intValue > 0 && $str == $intValue;
}
function checkForPageInfo($websiteID,$prompt,$mtemp=0.85,$mod='gpt-3.5-turbo'){
  if (!$websiteID){
    return null;
  }
  $SQL = "SELECT pageName,webPgID FROM ICDirectSQL.tblWebsitePgs where websiteID=".$websiteID."
  union 
  SELECT wpgiPageID,wpgiTopic FROM ICDirectSQL.tblWebsitePgItems
  where wpgiWSID = 1".$websiteID;
  $res = mkyMyqry($SQL);
  $rec = mkyMyFetch($res);
  $options = "{";
  $opts    = [];
  $coma = '';
  while ($rec){
    $pageName = urldecode($rec['pageName']);
    array_push($opts,$rec['webPgID']);
    $options .= $coma.'{"option":'.$rec['webPgID'].',"pageTitle":"'.$pageName.'"}';
    $rec = mkyMyFetch($res);
    $coma = ',';
  }
  $prompt = 'Given the following webpage titles : '.$options.',"No Options"} what page would you need to read  to best
  respond to this prompt: "'.$prompt.'". Response  instructions:
  [respond with no explanations. Give the option number only. ]';
  $j = new stdClass;
  $j->option = sendPrompt($prompt,$mod,$mtemp);
  return $j->option;
}
function sendPrompt($prompt,$mod='gpt-4',$temp=0.85){
  $msg = new stdClass;
  $msg->action = "getText";
  $msg->role   = "system";
  $msg->prompt = $prompt;
  $msg->n      = 1;
  $msg->maxTokens = 620;
  $msg->temperature = 0 + $temp;
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
    fail ('hcoStoryGen JSON Response Error:');
  }
  $usage = ' : cost - '.$res->usage->total_tokens;
  gfbug('usage:'.$usage);
  billAIUser($GLOBALS['billUID'],$res->usage->prompt_tokens,$res->usage->completion_tokens,$mod);
  return oaiCleanUp($res->response);
}
function oaiCleanUp($str){
  $str = str_replace('%24','$',$str);
  return $str;
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
