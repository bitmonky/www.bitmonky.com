<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
?>

<p><b style='color:darkKhaki;'>Click On City Name To Select It:</b>

<?php 
  $str =clean($_GET['fstr']);
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

  $SQL = "SELECT cityID,tblCity.name,tblCountry.name as country, tblState.name as state from tblCity ";
  $SQL .= "inner join tblCountry on tblCity.countryID=tblCountry.countryID ";
  $SQL .= "inner join tblState on tblCity.StateID=tblState.stateID ";
  $SQL .= "where tblCity.dopeFlg = 0 and concat(tblCity.name, ' ', tblState.name) like '".$str."%' ";
  $SQL .= "limit 30";
   
  $tRec = null;
  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  $n = 0;
  While ($tRec){
    echo "<div ID='wzline:".$n."' onmouseover='highlight(".$n.");' onmouseout='undoHighlight(".$n.");' style='padding:.35em;'><a ";
    echo "style='font-size:larger;color:darkKhaki;' ";
    echo "href='javascript:updateCitySelector(".$tRec['cityID'].",\"".$tRec['name'].", ".$tRec['state']." - ".$tRec['country']."\");'>";
    echo $tRec['name'].", ".$tRec['state']." - ".$tRec['country']."</a></div>";
    $tRec = mkyMsFetch($result);
    $n = $n + 1;
  }
 ?>
