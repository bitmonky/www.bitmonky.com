<?php
include_once('../mkysess.php');
//ini_set('display_errors',1);
//error_reporting(E_ALL);


if (isset($_GET['qcache'])){
  $qcache = $_GET['qcache'];
  $fcache = '/var/www/mkyCache/askMonkeyLastQry.txt';
  $myfile = fopen($fcache, "w");
  if ($myfile){
     if (flock($myfile, LOCK_EX)) {
       fwrite($myfile, $qcache);
       flock($myfile,LOCK_UN);
     }
     fclose($myfile);
  }
  echo 'OK';
}

?>
