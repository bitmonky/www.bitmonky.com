<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
?>
<script>
    function highlight(row) {
        var wzoutput = document.getElementById("wzline:" + row);
        wzoutput.style.background = "#fefefe";
    }
    function undoHighlight(row) {
        var wzoutput = document.getElementById("wzline:" + row);
        wzoutput.style.background = "#ffffff";
    }
</script>
<p><b style='color:brown;'>Click On Member To Add To Your Group:</b>
<?php 
  $str     = clean($_GET['fstr']);
  $groupID = clean($_GET['fgroupID']);    

  $SQL = "SELECT wzUserID,firstname from tblwzUser ";
  $SQL .= "where sandBox is null and firstname like '".$str."%' order by lastOnline desc limit 30";
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  if(!$tRec){
    echo "<p/>No Members Found With That Name...";
  }
  $n = 0;
  While ($tRec){
    echo "<div style='width:100%;' ID='wzline:".$n."' onmouseover='highlight(".$n.");' onmouseout='undoHighlight(".$n.");'><a style='color:#777777;' ";
	echo "href='groupAddMbr.php?wzID=".$sKey."&fmbrID=".$tRec['wzUserID']."&fgroupID=".$groupID."'>";
	echo "<img style='float:left;width:18px;height:24px;border-radius: .25em;border: 0px solid #74a02a;margin-right:2px;' ";
	echo "src='".$GLOBALS['MKYC_imgsrv']."/getMbrTnyImg.php?id=".$tRec['wzUserID']."'/>".$tRec['firstname']."</a></div><br>";
    $n = $n + 1;
    $tRec = mkyMsFetch($result);
  }
?>
