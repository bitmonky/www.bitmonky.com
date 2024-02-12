<?php
include_once("../mkysess.php");
$wzUserID = safeGET('fwzUserID');

//include_once("../mbrData.php");

  if (isset($_GET['mode'])){$fmode = clean($_GET['mode']);} else {$fmode = null;}

  include_once("../mblp/mblTemplate.php");
  $scrWidth = '100%';
  $pgMargin = '15px';
  ?>
  <script src='/whzon/wzToolboxJS.php'></script>
  <script>
  function pcInit() {
  }
  var qPg      = null;
  
function wzOnLoad(){
    qPg  = 0;
    getChannelMgr(0);
}
function wzPopADDCh(){
   parent.wzAPI_showFrame("/whzon/talk/frmAddChannel.php?wzID=<?php echo $sKey.'&fwzUserID='.$wzUserID;?>",400,300,50,100);
}
function getChannelMgr(pg){
  qPg = pg;
  var outp = document.getElementById('channelMgr');
  outp.innerHTML = "<br/><img style='width:35px;height;35px;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/imgLoading.gif'/> Fetching Channel List Please Wait...";
  var edUrl = '/whzon/mytown/myChannelMgr.php?wzID=<?php echo $sKey.'&fwzUserID='.$wzUserID;?>&newPg=' + pg;
  var edDiv = 'channelMgr';
  parent.updateDivHTML(edUrl,edDiv);
  parent.window.scrollTo(0,0);
}
</script>
<div style='padding:0px;padding-top:5px;'>

<div ID='wzAppsContainer' style='display:none;border:8px solid #777777;border-radius: 0.5em;position:absolute;'></div>
<div style='padding-bottom:200px'>
<?php 
echo "<div align='right'>";
echo "<a href=\"javascript:wzLink('/whzon/mblp/selFindMbrAutoT.php?wzID='+parent.sID);\">";
echo "Mbr Search</a>";
echo "</div>";
?>
<div style='' ID='channelMgr'></div>
<?php
  
  include_once("../mblp/mblFooter.php");
?>
</body>
</html>

