<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
include_once("../mkysess.php");
if (isset($_GET['fchanID'])) { $chanID = clean($_GET['fchanID']);} else {$chanID = null;}

if ($sessISMOBILE){
  ?>
  <!doctype html>
  <html class='pgHTM' lang="en">
  <head>
  <meta charset="utf-8">
  <title>Whzon.com</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>
  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  </head>
  <body class='pgBody' style='margin-top:20px;'  >
  <?php
}
else {
  include_once("../mblp/mblTemplate.php");
}
?>
<script>
function reSizeViewer(){
  pgFooter = document.getElementById('bottomOffBox');
  if (pgFooter){
    pgViewFrame = parent.document.getElementById("popUpFrame");
    fheight = getOffset( pgFooter ).top;
    parent.window.scrollTo(1, 1);
    parent.popViewer.style.height = fheight + 'px';
  }
}
function getYOffset(id){
  var el = document.getElementById(id);
  return getOffset(el).top;
}
function getYoffsetBottom(id){
  var el = document.getElementById(id);
  return getOffset(el).bottom;
}
function getOffset( el ) {
  var _x = 0;
  var _y = 0;
  var _b = 0;
  while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) && !isNaN(el.offsetHeight) ) {
    _x += el.offsetLeft - el.scrollLeft;
    _y += el.offsetTop;
    _b += el.offsetHeight;
    el = el.offsetParent;
  }
  return { top: _y, left: _x, bottom: _b };
}
</script>
<?php $mbrID=clean($_GET['fmbrID']);?>
<div style='padding:1em;'>
<!-- img style='float:right;margin-left:15px;width:90px;' src='//image.bitmonky.com/img/Chat.png'/>
-->
<h3>Channel History Viewer</h3>
<iframe FRAMEBORDER="NO" BORDER="NO" scrolling="NO" height="1700" width="100%"  src="zoomConversationData.php?wzID=<?php echo $sKey?>&fmbrID=<?php echo $mbrID;?>&fchanID=<?php echo $chanID;?>"></iframe>
<div ID='bottomOffBox'></div>
</div>
<?php 
if ($sessISMOBILE){
  include_once("../mblp/mblFooter.php");
}
else {
  echo "</body></html>";
}
?>
