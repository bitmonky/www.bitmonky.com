  </td></tr>
</table> 
<p/>
<div class='infoCardClear' style='margin:.5em;'>
  BitMonky - Powered By People
</div>
<div ID='wzAppsContainer' style='display:none;border:8px solid #777777;border-radius: 0.5em;position:absolute;background:#ffffff;'></div>
<script src='/whzon/wzToolboxJS.php'></script>

<?php
function ipToCountryCD($ipStr){

  $ends = mkyStrpos($ipStr,".");
  $mults = 256*256*256;
  $IPc = 0;

  if ($ends !== False){
    while ($ends > 0){ 
      $word  = left($ipStr,$ends-1);
      $lens  = strlen($ipStr);
      $ipStr = right($ipStr,$lens - $ends); 
      $IPc   = $IPc + $word * $mults;
      $mults = $mults/256;
      $ends  = mkyStrpos($ipStr,".");
    }

    $IPc = $IPc + $ipStr;
  
    $SQL = "select name,countryCD2 from IpToCountry  where LOWERip < ".$IPc." and upperIP > ".$IPc;

    $tRec = null;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    if ($tRec){
      return $tRec['Name'];
    }
    else {
      return "Not Found";
    }
  }
  else {
    return "Fail";
  }
}
?>

