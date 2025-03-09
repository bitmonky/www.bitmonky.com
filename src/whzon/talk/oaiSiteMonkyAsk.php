<?php
include_once('../mkysess.php');
include_once('oaiEconInc.php');

//ini_set('display_errors',1);
//error_reporting(E_ALL);

$rcode = null;
$j = new stdClass;
$tran = new stdClass;
$msg     = left(safeGET('msg'),800);
$uname   = left(safeGET('uname'),100);
$chanID  = safeGET('chanID');
$mbrUID  = safeGET('UID');
$digiHut = safeGET('digiHut');
$billUID = $mbrUID;
$mod     = safeGET('mod');
if (!$mod){
  $mod = 'gpt-3.5-turbo';
}
$SQL = "update tblwzOnline set lastAction = now() where wzUserID=63555";
mkyMyqry($SQL);

$SQL = "SELECT ownerID,name,chanType FROM ICDirectSQL.tblChatChannel ";
$SQL .= "where channelID = ".$chanID;
gfbug('chanID:'.$SQL);
$res = mkyMyqry($SQL);
$rec = mkyMyFetch($res);
if ($rec){
  $topic     = $rec['name'];
  $chanOUID  = $rec['ownerID'];

  if ($rec['chanType'] == 'mbrProf'){
    $ftopic = realTimeNic($topic)."'s Personal Channel";
  }
  else {
    $ftopic = $topic;
  }
  $ftopic = "The Chat Channel You Are Talking In Topic is ".$ftopic;

  $SQL = "SELECT msgID,wzUserID,callToUID,msg FROM ICDirectSQL.tblChatterBox ";
  $SQL .= "where channel = ".$chanID." and ((wzUserID=".$mbrUID." and callToUID = 63555) or (wzUserID=63555 and callToUID=".$mbrUID.")) ";
  $SQL .= "order by msgID desc limit 10";
  $res = mkyMyqry($SQL);
  $rec = mkyMyFetch($res);
  $conv = null;
  if ($rec){
    $conv  = "";
    while ($rec){
      $conv = $rec['msg']." ".$conv;	  
      $rec = mkyMyFetch($res); 
    }
    gfbug("chatlog:".$conv);
  }    
  
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
$mkyUID    = null;

if($digiHut && $digiHut != 'dc14fd698646636277d080a6a8854e9a'){
  $SQL = "select  M.wmkyUseModel,U.wzUserID,W.websiteID,U.firstname,W.Title,W.udomain,pText,concat(U.city,', ',U.prov,', ',U.country)loc from tblwzUser U 
  inner join tblWebsites W on W.wzUserID = U.wzUserID 
  left join tblWebSiteMonkey M on wmkyWSID = W.websiteID
  where U.mbrMUID = '".$digiHut."' limit 1";
  
  $res = mkyMyqry($SQL);
  $rec = mkyMyFetch($res);
  if ($rec){
    $mkyOwner  = $rec['firstname'];
    $mkyUID    = $rec['wzUserID'];
    $mkySite   = $rec['udomain'];
    $mkyTitle  = $rec['Title'];
    $websiteID = $rec['websiteID'];
    $mod       = $rec['wmkyUseModel'];
    if (!$mod){
      $mod = 'gpt-4';
    }

    $mkyLocation = $rec['loc'];

    if ($mkyUID == $chanOUID){
      $billUID = $mkyUID;
    }

    $digiInfo = $mkySite." basic info if you are asked. {".$rec['pText']." ";
    $digiInfo .= " Business Location : ".$mkyLocation;
    $digiInfo .= " Business Name : ".$mkyOwner;
    $digiinto .= " } ";
    if (stripos($msg,'bitmonky') !== false || stripos($msg,'digiHut') !== false || stripos($msg,'BGMP') !== false){ 
      $siteInfo .= $digiInfo;
    }
    else {
      $siteInfo = $digiInfo;      
    }  
  }
}
if  ($mkyUID == $chanOUID && $digiHut && $digiHut != 'dc14fd698646636277d080a6a8854e9a'){
  $mtemp = 0.85;    
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
}

$prompt  = 'Act as if you are human your name is Agent SiteMonkey AI, you are the host for '.$mkySite.' '.$ftopic.' ';
$prompt .= 'Ask the current user question base on the current conversation so far that is likely to further engage the user. be creative. ';
$prompt .= 'Keep your reponses short. The users name is '.$uname.'.  ';
$prompt .= $siteInfo;
$prompt .= 'The conversation so far is as follows. '.$conv;
$prompt .= ' Please DO NOT start your question with any salutation to the user. Do not mention \'@'.$uname.'\' at the start of your question. ';
$prompt .= "Do not repeat things that the other person says.";
gfbug('chanChat::'.$conv);

$msg = new stdClass;
$msg->action = "getTextNow";
$msg->role   = "system";
$msg->prompt = $prompt;
$msg->n      = 1;
$msg->maxTokens = 620;
$msg->useModel = $mod;

// Send the prompt to chatGPT via the NodeJS server.
// note - I will post the code soon for the intermediary nodeJS service. 

$pUrl = "https://antsrv.bitmonky.com:".$GLOBALS['MKYC_portOPAI']."/netREQ/msg=".mkyUrlEncode(json_encode($msg));

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
//billAIUser($billUID,$res->usage->prompt_tokens,$res->usage->completion_tokens,$mod);

respond(oaiCleanUp($res->response));

function checkForPageInfo($websiteID,$prompt,$mtemp=0.85,$mod='gpt-3.5-turbo'){
  if (!$websiteID){
    return null;
  }
  $SQL = "SELECT pageName,webPgID FROM ICDirectSQL.tblWebsitePgs where websiteID=".$websiteID;
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

  $pUrl = "https://antsrv.bitmonky.com:".$GLOBALS['MKYC_portOPAI']."/netREQ/msg=".mkyUrlEncode(json_encode($msg));

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
  //billAIUser($GLOBALS['billUID'],$res->usage->prompt_tokens,$res->usage->completion_tokens,$mod);
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
