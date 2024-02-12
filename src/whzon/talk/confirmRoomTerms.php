<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
$SQL = "update tblwzUser set defaultRoom = 1 where defaultRoom is null and wzUserID=".$userID;
$result = mkyMsqry($SQL);

?>
OK
