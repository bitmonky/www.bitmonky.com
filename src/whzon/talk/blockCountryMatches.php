<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
?>
<script>
function highlight(row){
    var wzoutput = document.getElementById("wzline:" + row);
    wzoutput.style.background="#fefefe";
}
function undoHighlight(row){
    var wzoutput = document.getElementById("wzline:" + row);
    wzoutput.style.background="none";
}
</script>
<?php
  $str=clean($_GET['fstr']);

  $SQL = "SELECT top 30 countryID, name from tblCountry where dopeFlg = 0 and name like '".$str."%'";
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);

  $n=0;
  While ($tRec){
    echo "<div ID='wzline:".$n."' onmouseover='highlight(".$n.");' onmouseout='undoHighlight(".$n.");'><a style='color:#777777;' href='blockCountry.php?wzID=".$sKey."&fcountryID=".$tRec['countryID']."'>".$tRec['name']."</a></div>";
    $tRec = mkyMsFetch($result);
    $n=$n+1;
  }

?>
