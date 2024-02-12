<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../dredge/preIndexToolsInc.php");

if ($userID == 17621){
  $wsID = safeGET('wsID');

  $SQL = "update tblWebsites set mrkDelete = 17621,responseCD='999' where websiteID = ".$wsID;
  if ( mkyMsqry($SQL)){
     scrubIndex('web',$wsID);
     $SQL = "update ndxWeb.ndxWebsites set ndxwDeleted = now() where ndxwWebsiteID = ".$wsID;
     mkyMsqry($SQL);
     exit('{"result":"Removed"}');
  }
  exit('{"result":"Fail","msg":"database qry failed."}');
}
echo '{"result":"Fail"}';
?>
