
<script>
if (document.location.href == parent.document.location.href){
  try {document.body.style.display = 'none';}
  catch(err){}
  var htRf = escape(document.referrer);
  var nurl = document.location.href;
  nurl = escape(nurl);
  wzLog(htRf,nurl);
  //trackRefer();
}
function wzLog(htRf,nurl) {
  var currentTime = new Date();
  var ranTime = '#' + currentTime.getMilliseconds();
  parent.document.location.href = '/whzon/mblp/wzMbl.php?furl=' + nurl;
}

</script>
<?php
include_once("../mkysess.php");
include_once('newsHLObjs.php');

ini_set('display_errors',1);
error_reporting(E_ALL);

$cityID = safeGetINT('cityID');
$scope  = safeGET('fscope');
$acOnly = safeGET('actOnly');
$fpOnly = safeGET('fpOnly');
$qry    = safeGET('fqry');
$mkey = new mkySess($sKey,$userID);

echo "<div ID='visualSearchDisplay' class='infoCardClear'>";
if (!$fpOnly){
  echo "<div align='right'><a href='javascript:hideVisualSearch();'>Hide[^]</a></div>";
  echo "<h3>Over View</h3>";
  $acSco  = new mkyScope($scope,'city',$cityID);
  $acSco->putScopeLinks($sKey);
  echo "<p/>";
  makeActivtyMenu($mkey,$cityID,$scope,$qry);
  echo "</div>";
}
if ($acOnly == 'on'){
  exit('');
}
?>
<script>
if (document.location.href == parent.document.location.href){
  try {document.body.style.display = 'none';}
  catch(err){}
  var htRf = escape(document.referrer);
  var nurl = document.location.href;
  nurl = escape(nurl);
  wzLog(htRf,nurl);
  //trackRefer();
}
</script>
<?php
echo "<div ID='newsHeadLines' style='margin-bottom:1.5em;' class='infoCardClear'>";
echo "<div align='right'><a href='javascript:hideHeadLines();'>Hide[^]</a></div>";
echo "<h2>Focused Posts</h2>";
$nscope = $scope;
$nCard1 = new mkyNewsHLCard($mkey,1,$cityID,$nscope);
$nCard3 = new mkyNewsHLCard($mkey,3,$cityID,$nscope);
$nCard4 = new mkyNewsHLCard($mkey,4,$cityID,$nscope);
$nCard2 = new mkyNewsHLCard($mkey,2,$cityID,$nscope);
$nCard6 = new mkyNewsHLCard($mkey,6,$cityID,$nscope);
$nCard1->draw();
$nCard3->draw();
$nCard4->draw();
$nCard2->draw();
$nCard6->draw();
echo "</div>";

function makeActivtyMenu($mkey,$cityID,$scope,$qry=null){
  global $userID;

  $chnItem = new mkyActivityCard($mkey,$cityID,$scope,'chan','Member Channels',$qry);
  $chnItem->draw();

  $vidItem = new mkyActivityCard($mkey,$cityID,$scope,'video','Member YouTube Videos',$qry);
  $webItem = new mkyActivityCard($mkey,$cityID,$scope,'web','Websites',$qry);
  $shaItem = new mkyActivityCard($mkey,$cityID,$scope,'mshare','Member Shares',$qry);
  $blgItem = new mkyActivityCard($mkey,$cityID,$scope,'mBlog','Blogs',$qry);
  $mosItem = new mkyActivityCard($mkey,$cityID,$scope,'mosh','MoshBox Music',$qry);
  $claItem = new mkyActivityCard($mkey,$cityID,$scope,'class','Classifieds',$qry);
  $phoItem = new mkyActivityCard($mkey,$cityID,$scope,'photo','Pictures',$qry);
  $mbrItem = new mkyActivityCard($mkey,$cityID,$scope,'mbrs','People',$qry);

  $vidItem->draw(); // = new mkyActivityCard($mkey,$cityID,$scope,'video','Member YouTube Videos',$qry);
  $webItem->draw(); // = new mkyActivityCard($mkey,$cityID,$scope,'web','Websites',$qry);
  $shaItem->draw(); // = new mkyActivityCard($mkey,$cityID,$scope,'mshare','Member Shares',$qry);
  $blgItem->draw(); // = new mkyActivityCard($mkey,$cityID,$scope,'mBlog','Blogs',$qry);
  $mosItem->draw(); // = new mkyActivityCard($mkey,$cityID,$scope,'mosh','MoshBox Music',$qry);
  $claItem->draw(); // = new mkyActivityCard($mkey,$cityID,$scope,'class','Classifieds',$qry);
  $phoItem->draw(); // = new mkyActivityCard($mkey,$cityID,$scope,'photo','Pictures',$qry);
  $mbrItem->draw();
}
?>

