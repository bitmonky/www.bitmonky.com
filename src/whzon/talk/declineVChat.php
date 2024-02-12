<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");




if (!$userID==0){
    $SQL = "update ICDchat.tblvCalls set pending=null where wzUserID=".$userID." or wzCallerID=".$userID;
    $myresult = mkyMyqry($SQL);

    
}

?>
