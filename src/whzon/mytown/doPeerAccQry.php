<?php
$mkySQLLocal = true;

include_once("../mkysess.php");
ini_set('display_errors',1);
error_reporting(E_ALL);
include_once("../public/doPeerHutQryObjInc.php");
include_once("../bitMiner/bcMUIDInc.php");
$time_pre = microtime(true);

$vmode = safeGET('mode');

$flex  = '100%';
$limit = 20;
$imgs  = null;
//$vmode = 'PC';

if($vmode == 'PC'){
  $flex = '25%';
  $imgs = "style='height:90px;' ";
  $rows = 4;
  $cols = 5;
}
else {
  $rows = 20;
  $cols = 1;
}

$qry=safeGET('search');
$qmode = safeGET('qmode');
if ($qmode == 'all'){
  $qmode = null;
}
?>
<style>
* {
  box-sizing: border-box;
}

.header {
  text-align: center;
  padding: 32px;
}

.row {
  display: -ms-flexbox; /* IE10 */
  display: flex;
  -ms-flex-wrap: wrap; /* IE10 */
  flex-wrap: wrap;
  padding: 0 4px;
}

/* Create four equal columns that sits next to each other */
.column {
  -ms-flex: <?php echo $flex;?>; /* IE10 */
  flex: <?php echo $flex;?>;
  max-width: <?php echo $flex;?>;
  padding: 0 4px;
}

.column img {
  margin-top: 8px;
  background-color: black;
  border-radius:1.2em 1.2em 0em 0em;
  vertical-align: middle;
  width: 100%;
}
/* Responsive layout - makes a two column-layout instead of four columns */
@media screen and (max-width: 300px) {
  .column {
    -ms-flex: 50%;
    flex: 50%;
    max-width: 50%;
  }
}
</style>
<?php
echo "<div align='right'>";
echo "</div>";
/*
echo "<div class='infoCardClear' style='background:#151515;'>";
?>
<form onsubmit="getHutDirectory(0,true);return false;"><input style="width:75%;font-size:larger;"
onkeypress="return noenter();" id="peerMemQry" placeholder=" Search Agent SiteMonkey`s Big Brain " type="text" name="search"/>
<input id="peerMemBut" style='' type="button" value=" Search Again " onclick="getHutDirectory(0,true);"/>
<input type="button" value=" Return To Big List " onclick="getHutDirectory();"/>
</form></div>
<?php 
*/

$SQL  = "SELECT count(*)nRec  from (";
$SQL .= "select acmeACID FROM ICDirectSQL.tblActivityMemories group by acmeACID)R";
$res = mkyMyqry($SQL);
$rec = mkyMyFetch($res);
echo "<h2>Total Memories Stored: ".mkyNumFormat($rec['nRec'])."</h2>";

if($qry){
  $qry = left(prepWords($qry),500);

  //*Log serch to the top qry file.
  $SQL = "select pqryID from ICDirectSQL.tblPeerMemTopQrys where pqryQry = '".$qry."'";
  $res = mkyMyqry($SQL);
  $rec = mkyMyFetch($res);
  if ($rec){
    $SQL = "update ICDirectSQL.tblPeerMemTopQrys set pqryNQrys = pqryNQrys + 1 where pqryID = ".$rec['pqryID'];
  }
  else {
    $SQL = "insert into ICDirectSQL.tblPeerMemTopQrys (pqryQry,pqryNQrys) values ('".$qry."',1)";
  }
  mkyMyqry($SQL);

  $SQL = "select pmacPMemOwner from ICDirectSQL.tblPeerMemoryAcc";
  $res = mkyMyqry($SQL);
  $rec = mkyMyFetch($res);
  $mbrMUID = $rec['pmacPMemOwner'];

  //echo "Start Search:".(microtime(true) - $time_pre);
  $j = ptreeSearchMem($mbrMUID,$qry,$qmode,null,null);
  //echo "<br/>End Search:".(microtime(true) - $time_pre);
  //$j = stripslashes($j);
  $j = mkyStrReplace('"{','{',$j);
  $j = mkyStrReplace('}"','}',$j);
  $j = mkyStrReplace('\\"','"',$j);
  $j = mkyStrReplace('NULL','',$j);
  $r = json_decode($j); //,JSON_INVALID_UTF8_SUBSTITUTE);
  if ($r->result){
    echo "<h2>Search Result</h2>";
    $nrec = sizeof($r->data) -1;
    if ($nrec > 0){
      echo "Records found: <span class='mkyNumber'>".sizeof($r->data)."</span><p/>";
    }
    //echo "Start Render:".(microtime(true) - $time_pre);
    $n=1;
    $ACIDs = [];

    $fstr = '';
    forEach($r->data as $rec){
      $fstr .= $rec->pmcMemObjID."\t";
      $fstr .= $rec->pmcMemObjNWords."\t";
      $fstr .= $rec->nMatches."\t";
      $fstr .= $rec->score."'\r\n";
      $n=$n+1;
      if ($n > 20){
        break;
      }

    }	      
    $tmpTable = makeBC_MUID(hash('sha256', $qry.time()));
    $tmpFile  = '/var/www/www.bitmonky.com/wzAdmin/tmpQry'.$tmpTable.'.txt';

    if (!file_put_contents($tmpFile,$fstr)){
      if ($qmode == 'web'){
        exit('BrainFreeze:Try All');
      }	
      exit('fail to write query result file');
    }      
    if (!mkyStartTransaction()){
      exit('fail to start database transaction');
    }

    $SQL  = "CREATE TABLE ICDirectSQL._tmp".$tmpTable." (";
    $SQL .= "  `pmcMemObjID` varchar(64) NOT NULL,";
    $SQL .= "  `pmcMemObjNWords` int(10) unsigned DEFAULT NULL,";
    $SQL .= "  `nMatches` int(10) unsigned DEFAULT NULL,";
    $SQL .= "  `score` decimal(12,9) DEFAULT NULL,";
    $SQL .= "  PRIMARY KEY (`pmcMemObjID`),";
    $SQL .= "  KEY `ndxPmcMemObjID` (`pmcMemObjID`),";
    $SQL .= "  KEY `score` (`score`)";
    $SQL .= ")ENGINE=InnoDB;";

    //$SQL .= "TRUNCATE TABLE ICDirectSQL.tmpPeerQry; ";
    $qres = mkyMyqry($SQL);

    $SQL  = "LOAD DATA LOCAL INFILE '".$tmpFile."' INTO TABLE ICDirectSQL._tmp".$tmpTable.";";
    $qres = mkyMyqry($SQL);
    
    unlink($tmpFile); // Delete temp text file. 

    $SQL = "select firstname,mbrMUID,tags,contentOwnerID,activityID,AF.websiteID, acCode, acLink,acItemID, 
    U.wzUserID,firstname,age,sex from ICDirectSQL.tblActivityFeed AF 
    left join ICDirectSQL.tblActivityMemories on acmeACID = activityID 
    inner join ICDirectSQL._tmp".$tmpTable." on acmeMemHash = pmcMemObjID 
    left join tblwzUserJoins U on U.wzUserID = AF.wzUserID 
    where suppress is null 
    group by activityID,tags,contentOwnerID,AF.websiteID, acCode, acLink,acItemID,U.wzUserID,
    firstname,age,sex order by score desc";
    $qres = mkyMyqry($SQL);
    $SQL = "drop table ICDirectSQL._tmp".$tmpTable.";";
    mkyMyqry($SQL);
    mkyCommit();

    $lrec = mkyMyFetch($qres);
    $n = 1;
    if (!$lrec){
      echo "<br/> - No Results Found";
    }
    $n = 0;
    echo "<div class='row'>";
    $n = 1;
    $maxn = 2;
    $n = 1;
    $maxn = 6;
    $list = rcSort($qres,$lrec,$rows,$cols);
    
    forEach($list as $qrec){
      if ($n == 1 ){
        echo "<div class='column'>";
      }
      if ($qrec){
        echo "<div class='infoCardClear' style='background:black;'>";
        display($qrec);
        formatHashTagsNUPS($qrec['tags'],0);
        echo "<br/>ActivityID: ".$qrec['activityID']." - ".$qrec['tags'];
        echo "</div>";
      }
      $n = $n + 1;
      if ($n == $maxn){
        $n=1;
        echo "</div>";
      }
    }
    if ($n != 1){
      echo "</div>";
    }
  }
}
echo "</div><p/>Job Complete:\n";
$time_post = microtime(true);
$exec_time = $time_post - $time_pre;
echo "Job Run Time: ".$exec_time."\n";
echo "</div>";

function rcSort($result,$rec,$rows,$cols){
  $nres = mysqli_num_rows($result);	
  $maxn = $rows*$cols;
  $c = 1;
  $list = array_fill(0, $maxn, null);
  $n = 0;
  $p = $c;
  while ($rec){
    $list[$p-1] = $rec;
    $p = $p + $cols;
    $n = $n +1;
    $rec = mkyMyFetch($result);
    if ($n >= $rows){
      $n = 0;
      $c = $c + 1;
      $p = $c;
    }
  }
  return $list;
}

function inList($ac){
  forEach($GLOBALS['ACIDs'] as $acid){
    if ($acid == $ac){
      return true;
    }
  }
  return false;
}
function drawRecentSearches(){
  echo "<div ID='drawRSearches'></div>";
}
function display($tRec){
  global $sKey; 
  $acCode   = $tRec['acCode'];
  $wzUserID = $tRec['wzUserID'];
  echo "<div onmouseover=\"parent.showDiv('addBut".$tRec['websiteID']."');\"
  onmouseout=\"parent.hideDiv('addBut".$tRec['websiteID']."');\" > ";
  echo "<img src='https://image.bitmonky.com/getMbrImg.php?mid=".$tRec['mbrMUID']."' ";
  echo   "style='float:right;width:3.5em;height:3.5em;border-radius:50%;margin:0.5em;'/>";
  echo $tRec['firstname'];

  if ($acCode == 2 || $acCode == 12 || $acCode == 14 || $acCode == 24){
    $wsID = $tRec['acItemID'];
    if (!$wsID){
      $wsID = $tRec['websiteID'];
    }
    sayNewWS($wzUserID, $tRec['firstname'], $wsID, $acCode,$addRef=true);
  }

  if ($acCode == 1){
    $SQL = "select profileText from tblwzUser where wzUserID = ".$wzUserID;
    $cRes = mkyMyqry($SQL);
    $cRec = mkyMyFetch($cRes);
    sayNewUser($wzUserID, $tRec['firstname'], $cRec['profileText']);
  }


  if ($acCode == 16) {
    $fresult=sayNewSong($wzUserID, $tRec['firstname'], $tRec['acLink'],$tRec['activityID']);
  }

  if ($acCode == 4) {
    $fresult = sayNewAD($wzUserID, $tRec['firstname'], $tRec['acLink'],$tRec['acItemID'],$sKey);
  }

  if ($acCode == 5) {
    $fresult = sayNewEvent($wzUserID, $tRec['firstname'], $tRec['acLink'],$tRec['acItemID'],$sKey);
  }

  if ($acCode == 6) {
    sayNews($wzUserID, $tRec['firstname'], $tRec['acLink']);
  }

  if ($acCode == 7) {
    sayNewPhoto($wzUserID, $tRec['firstname'], $tRec['acItemID'],$tRec['activityID']);
  }

  if ($acCode == 17) {
    sayNewVideoShare($wzUserID, $tRec['firstname'], $tRec['acItemID'],$tRec['activityID']);
  }
  if ($acCode == 18) {
    sayWNewsShare($wzUserID, $tRec['firstname'], $tRec['acItemID'],$sKey,$tRec['activityID']);
  }
  if ($acCode == 22) {
    sayNewChannel($wzUserID,$tRec['firstname'],$tRec['acItemID'],$tRec['activityID']);
  }

  if ($acCode == 23) {
    sayNewLiveStream($wzUserID,$tRec['firstname'],$tRec['acItemID'],$tRec['activityID']);
  }
  if ($acCode == 19) {
    saySItemShare($wzUserID, $tRec['firstname'], $tRec['acItemID'],$sKey);
  }

  if ($acCode == 8) {
    sayBLOG($wzUserID, $tRec['firstname'], $tRec['acItemID'],$sKey);
  }

  if ($acCode == 13) {
    sayInMoshBox($wzUserID, $tRec['firstname'], $tRec['acLink'] , $hisher);
  }

  echo "</div>";
} 
?>

