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
$vmode   = safeGET('vmode');
if(!$vmode){
  $vmode = 'width:35%;';
}
else {
  $vmode = '';
}
$mod     = safeGET('mod');
if (!$mod){
  $mod = 'gpt-3.5-turbo';
}
$scopeStr = null;
$scopeLoc  = null;

$scope = safeGET('scope');

if ($scope){
  $scope = json_decode(base64_decode($scope));
}

if ($scope){
  if ($scope->scope != 'myWorld'){
    $scopeStr = $scope->scope;
    if ($scopeStr == 'myCity'){
      $scopeLoc  = $scope->city.', '.$scope->state.', '.$scope->country;
    }
    if ($scopeStr == 'myState'){
      $scopeLoc  = $scope->state.', '.$scope->country;
    }
    if ($scopeStr == 'myCountry'){
      $scopeLoc  = $scope->country;
    }
    if ($scopeStr == 'myWRegion' || $scopeStr == 'myWorldRegion'){
      $scopeLoc  = 'Word Wide ';
    }
    //echo "<h3>Search Scope Is: ".$scopeStr." ID:".$scopeID."</h3>";
  }
}
if ($scopeLoc){
  $scopeLoc =" The Geographic Locations For the search is: {".$scopeLoc."} 
  Please Do Not Make statments like 'sorry, but I couldn't find any direct information about ... etc. ";
}  
$qRes = null;
if (isset($_GET['qres'])){
  $qRes = "The Top Search result on bitmonky search is ".$_GET['qres']." use information from this result
    in your response if relevent. ";
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
$siteInfo .= "GJEX (Great Jungle Exchange) is the sites market for trading BMGP for dogecoin place. ";
$siteInfo .= "Free to join. ";
$siteInfo .= "DigiHut is what we call member profiles. ";
$siteInfo .= "Types of Huts are Regular Members, Business and YouTuber. ";
$siteInfo .= "BMGP can be used to buy advertising. ";
$siteInfo .= "BMGP can only be purchase from other members who have earned them. ";
$siteInfo .= "\" ".$ecod." The sites onwer is Peter,  Location Toronto, Ontario, Canada";

$websiteID = null;
$mkyUID    = null;

$qry = $msg;
$prompt  = 'Act as if you are human your name is Agent SiteMonkey AI, you are the host for www.bitmonky.com. you can 
search and respond in any language.
A user of the site has submitted the following search query/question {"'.$qry.'"} '.$scopeLoc.'. The system will output 
relevant results. Please either provide and answer or provide a short comment on what you know about 
the question or topic. If asked about BitMonky or BMGP use this current info about the site {"info":"'.$siteInfo.'"}
Keep your responses under 300 words. If the search is in a language other then english respond in the same language use in the query.
Keep in mind the word Bitmonky is english. '.$qRes;

gfbug('mkySearch::'.$prompt);

$msg = new stdClass;
$msg->action = "getTextNow";
$msg->role   = "system";
$msg->prompt = $prompt;
$msg->n      = 1;
$msg->maxTokens = 620;
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

//billAIUser($billUID,$res->usage->prompt_tokens,$res->usage->completion_tokens,$mod);

$final =  urldecode(htmlspecialchars($res->response.''));
$final .= "<div align='right'><div class='infoCardClear' style='margin:0.5em 0em 0em 0em;".$vmode."background:black;color:darkKhaki;font-size:small;'> 
   DISCLAIMER - Information provided by Site Monkey AI may not be true or factual. Verify 
   responses with multiple sources!</div></div>"; 
echo $final;

$jr = json_decode($qRes);
if ($jr){
}
$j = new stdClass;
$j->lastQry  = $qry;
$j->response = $final;
$j->muid     = $mbrMUID;
$j->qResult  = json_decode($_GET['qres']);

$fcache = '/var/www/mkyCache/askMonkeyLastQry.txt';
$myfile = fopen($fcache, "w");
if ($myfile){
   if (flock($myfile, LOCK_EX)) {
     fwrite($myfile, json_encode($j));
     flock($myfile,LOCK_UN);
   }
   fclose($myfile);
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
