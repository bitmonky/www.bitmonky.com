<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

if ($userID!=0){
  $title = safeGET('fname');
  $guide = left(safeGET('fdesc'),250);
  $hcoID = safeGET('hcoID');
  $story = safeGET('hcoStory');

  $callBck=clean($_GET['fcallbck']);
  if (isset($_GET['fpriv'])) {$isPrivate=clean($_GET['fpriv']);} else {$isPrivate=null;}
  $spoken=clean($_GET['fspoken']);

  if ($title=="" || $title==null) {
    header("Location: ".$callBck."?wzID=".$sKey."&ferror=1");
    exit("");
  }

  $chanID=clean($_GET['fchanID']);

  if ($isPrivate=="on"){
      $isPrivate = 1;
    }
  else{
    $isPrivate='null';
  }

  $SQL  = "UPDATE tblChatChannel  set name='".$title."',guide='".$guide."',spoken='".$spoken."',privateChan=".$isPrivate;
  $SQL .= " where ownerID=".$userID." and channelID=".$chanID;
  $result = mkyMsqry($SQL);

  if ($hcoID){
    $SQL = "update tblHashChanOwner set hcoStory = '".$story."' where hcoID = ".$hcoID;
    mkyMyqry($SQL);
  }
}

?>
<!doctype html>
<html class="pgHTM" lang="en">
<head>
  <meta charset="utf-8">
<script>
  function cleanup(){
    parent.wzChangeChannel(<?php echo $chanID;?>);
    parent.wzAPI_closeWin();
  }
</script>
</head>
<body class='pgBody' onload='cleanup();'>
<A HREF=# onclick='javascript:parent.wzAPI_closeWin();'>[Done]</a>
</body>
</html>

