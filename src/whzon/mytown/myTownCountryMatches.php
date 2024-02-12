<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
?>
<p><b style='color:darkKhaki;'>Click On Country To Select:</b>
<?php 
  $str = clean($_GET['fstr']);
  $catID = safeGetINT('fcatID');
  if ($catID !== null){
    $catID = "&fcatID=".$catID;
  }
  $fsq = safeGET('fsq');
  $fsqPg = null;
  if ($fsq){
    $fsqPg = "&fsq=".mkyUrlEncode($fsq);
    $catID .= $fsqPg;
  }

  $SQL = "SELECT countryID,name as country from tblCountry  ";
  $SQL .= "where dopeFlg = 0 and name like '".$str."%' ";
  $SQL .= "limit 30";
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);

  $n = 0;
  While ($tRec){
    echo "<div ID='wzline:".$n."' onmouseover='highlight(".$n.");' onmouseout='undoHighlight(".$n.");'><a target='_parent' style='color:#777777;' ";
    echo "href='javascript:parent.wzGetPage(\"/whzon/mytown/myTownChangeULoc.php?wzID=".$sKey.$catID."&fcountryID=".$tRec['countryID']."\");'>".$tRec['country']."</a></div>";
    $n = $n + 1;
    $tRec = mkyMsFetch($result);
  }
?>
