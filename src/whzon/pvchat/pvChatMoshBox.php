<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");

$mbrID=clean($_GET['fmbrID']);

    $SQL = "SELECT tblMoshUsers.moshPitID, Title, tblMoshArtist.name  From tblMoshUsers ";
    $SQL .=        "inner join tblMoshPit on  tblMoshUsers.moshPitID=tblMoshPit.moshPitID ";
    $SQL .= "inner join tblMoshPerformance on gigID=moshPerformanceID ";
    $SQL .= "inner join tblmoshSong on tblmoshSong.songID=tblMoshPerformance.songID ";
    $SQL .= "inner join tblMoshArtist on tblmoshSong.artistID=tblMoshArtist.moshArtistID ";
    $SQL .= "where tblMoshUsers.wzUserID=".$mbrID; 

    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    if ($tRec){
      $moshPit = $tRec['moshPitID'];
      $title   = $tRec['title'];
      $artist  = $tRec['name'];
?>
      <b>Listening To</b><img style="border: 0px none; vertical-align: middle; margin-top: 0px; margin-left: 2px; height: 14px; width: 11px;" src="//image.bitmonky.com/img/musicIcon.png">
       - '<?php echo $title;?>' By <?php echo $artist;?>
      <a style='font-size:12px;' href='javascript:parent.OpenGigBox(<?php echo $moshPit;?>);'>Join Them</a>
<?php
    }

?>
