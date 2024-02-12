<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");

    $SQL = "Delete from ICDchat.tblMbrChat where groupID  is null and msgMbrID=".$userID." or msgUserID = ".$userID;
    $myresult = mkyMyqry($SQL);

    

?>
  <HTML>
  <script>
    parent.wzAPI_closePVC();
  </script>
  </HTML>
