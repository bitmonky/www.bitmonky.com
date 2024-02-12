<script>
function openFanReq(fanID,wsID){
      URL="/wzUsers/mbrProfiles/fanRequestFrm.asp?fanID=" + fanID + "&wsID=" + wsID;
      winName = "wzReq" + fanID;
      var onfanReq = window.open(URL,winName,"target=new,width=450,height=130,resizable=no,scrollbars=no");
      onfanReq.focus();
}

function openGigBox(wsID){
  parent.OpenGigBox(wsID);
}

</SCRIPT>
<p>
<div class='infoCardClear'>
   <B><span style='color:darkKhaki;font-wheight:bold;'>Top MoshBOX's</span> - See Also</b>  
   <?php drawMyMenu($myMode,$modes);?>
</div>
   <p>

<table style='width:95%;'>
  <tr valign='top'>
    <td>
<?php 

$mbSearch = " Where mpitType = 1 ";

$SQL = "SELECT tblMoshPit.moshPitID, tblwzUser.wzUserID, firstname,venuName,description, health, tblMoshPit.nViews, gigID  FROM ";
$SQL .= "tblMoshPit  ";
$SQL .= "inner join tblwzUser  on tblMoshPit.wzUserId=tblwzUser.wzUserID "; 
$SQL .= "inner join tblCity  on tblCity.cityID = tblwzUser.cityID ";
$SQL .= "inner join tblMoshPerformance  on tblMoshPit.moshPitID=tblMoshPerformance.moshPitID ";
$SQL .= "where ".$userSearch." ";
$SQL .= "group by tblMoshPit.moshPitID, tblwzUser.wzuserID, firstname,venuName,description, health, tblMoshPit.nViews, gigID  ";
$SQL .= "Order by count(*) desc, tblMoshPit.nViews desc;";

$tRec = null;
$result = mkyMsqry($SQL) or die($SQL);
$tRec = mkyMsFetch($result);

if (isset($_GET['newPg'])){$pg = clean($_GET['newPg']);} else {$pg = 0;}
$nextPage = $pg;
$n = $pg + 1;

$p = 0;
while ($tRec && $p < $nextPage) {
  $tRec = mkyMsFetch($result);
  $p = $p + 1;
}


echo "<p><table class='docTable' style='border: 0px;margin-top:40px;' cellpadding=3 width=100% >";

echo "<tr valign=top>";
echo "<td><b style='font-size:larger;'>Mosh Pit Owner</b></font></td>";
echo "<td><b style='font-size:larger;'>Now Playing</b></font></td>";
echo "</tr>";

$i = 0;
$n = 0;
$nRows = 10;
$link = "?wzID=".$sKey."&fmyMode=mosh&fwzUserId=".$wzUserID;
$appName = "myTown.php";
if ($digID){
  showDigListing($digID,safeGetINT('songID'));
}
while ($tRec && $n < $nRows){

  $gigID = $tRec['gigID'];
  $boxID = $tRec['moshPitID'];

  $SQL = "select  artistID, uTubeCD, tblMoshArtist.name, title,tblMoshArtist.img from tblMoshPerformance  ";
  $SQL .= "inner join tblmoshSong  on tblmoshSong.songID=tblMoshPerformance.songID ";
  $SQL .= "inner join tblMoshArtist  on tblmoshSong.ArtistID=tblMoshArtist.moshArtistID where moshPerformanceID=".$gigID;
  $SQL = "select artistID, uTubeCD, tblMoshArtist.name, title,tblMoshArtist.img from tblMoshPerformance ";
  $SQL .= "inner join tblmoshSong on tblmoshSong.songID=tblMoshPerformance.songID "; 
  $SQL .= "inner join tblMoshArtist on tblmoshSong.artistID=tblMoshArtist.moshArtistID where moshPerformanceID=".$gigID;
  $gRec = null;
  $gresult = mkyMsqry($SQL) or die($SQL);
  $gRec = mkyMsFetch($gresult);

  $artist = "-";
  $title = "-";
  $img = 0;
  if ( $gRec) {
    $uTubeCD = $gRec['uTubeCD'];
    $artistID = $gRec['artistID'];
    $artist = $gRec['name'];
    $title  = $gRec['title'];
    if (!$gRec['img']){
      $img = 0;
    } 
    else {
      $img = 1;
    }
  }

  $wzImg = "<a href='javascript:wzLink(\"/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."\");'>";
  $wzImg .= "<img title='View Members Profile' alt='View Members Profile' style='margin:3px;margin-right:12px;border-radius:50%;width:60px;height:80px;float:left;'";
  $wzImg .= "src='https://image.bitmonky.com/getMbrImg.php?id=".$tRec['wzUserID']."'></a>";

  $SQL = "select count(*) as nRec from tblMoshPerformance  where moshPitID=".$boxID; 
  $gRec = null;
  $gresult = mkyMsqry($SQL);
  $gRec = mkyMsFetch($gresult);

  if ($gRec) {
    $nSongs = $gRec['nRec'];
  }
  $openAnk = "<a style='font-size:12px;' href='javascript:parent.OpenGigBox(".$tRec['moshPitID'].");'>";
  $name    = "<b> Mosh Title:</b> - ".$openAnk.$tRec['venuName']."</a>";
  $desc    = "<b>description:</b> - ".$tRec['description']."<br><b>Songs In Collection:</b> - ".$nSongs;
  $nViews  = $tRec['nViews'];

  if ($nSongs > 0) {
    echo "<tr  valign=top>";
    echo "<td align='left'>".$wzImg.$name."<br>".$desc. "</td>";
    echo "<td align='left'>" ?>

    <?php echo $openAnk;?><img title='Listen To This MoshBOX Now'
    style='float:left;margin-bottom:9px;margin-right:5px;border-radius:.25em;width:120px;height:90;'
    src='<?php echo getSongIMG($uTubeCD) ?>'></a>
    <B>Artist:</b> - <?php echo $artist ?><br>
    <B>Song:</b> - <?php echo $title ?><br>
    <B>Views:</b> - <?php echo $nViews ?> </td></tr>
<?php 
  }
  $i = $i + 1;
  $n = $n + 1;
  $tRec = mkyMsFetch($result);
}
echo "</table>";

echo "<div style='margin-top:2em;'>";
echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage + $nRows)."\");'>Next</a>";
if ($nextPage > 0) {
  echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage - $nRows)."\");'>Back</a>";
}
echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=0\");'>Top</a>";
//echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&spin=spin&newPg=0\");'>Spin Again</a>";
echo "</div>";

echo "</td></tr></table>";

function showDigListing($digID,$songID){
    global $sKey;
    global $userID;
    global $n,$i;

    $SQL = "SELECT tblMoshPit.moshPitID, tblwzUser.wzUserID, firstname,venuName,description, health, tblMoshPit.nViews, gigID  FROM ";
    $SQL .= "tblMoshPit  ";
    $SQL .= "inner join tblwzUser  on tblMoshPit.wzUserId=tblwzUser.wzUserID ";
    $SQL .= "inner join tblCity  on tblCity.cityID = tblwzUser.cityID ";
    $SQL .= "inner join tblMoshPerformance  on tblMoshPit.moshPitID=tblMoshPerformance.moshPitID ";
    $SQL .= "where tblMoshPit.moshPitID = ".$digID." and tblMoshPerformance.songID = ".$songID;

    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $gigID = $tRec['gigID'];
    $boxID = $tRec['moshPitID'];

    $SQL = "select  artistID, uTubeCD, tblMoshArtist.name, title,tblMoshArtist.img from tblMoshPerformance  ";
    $SQL .= "inner join tblmoshSong  on tblmoshSong.songID=tblMoshPerformance.songID ";
    $SQL .= "inner join tblMoshArtist  on tblmoshSong.ArtistID=tblMoshArtist.moshArtistID where moshPerformanceID=".$gigID;

    $gRec = null;
    $gresult = mkyMsqry($SQL) or die($SQL);
    $gRec = mkyMsFetch($gresult);

    $artist = "-";
    $title = "-";
    $img = 0;
    if ( $gRec) {
      $uTubeCD = $gRec['uTubeCD'];
      $artistID = $gRec['artistID'];
      $artist = $gRec['name'];
      $title  = $gRec['title'];
      if (!$gRec['img']){
        $img = 0;
      }
      else {
        $img = 1;
      }
    }

    $wzImg = "<a href='javascript:wzLink(\"/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."\");'>";
    $wzImg .= "<img title='View Members Profile' alt='View Members Profile' style='margin:3px;margin-right:12px;border-radius:50%;width:60px;height:80px;float:left;'";
    $wzImg .= "src='https://image.bitmonky.com/getMbrImg.php?id=".$tRec['wzUserID']."'></a>";

    $SQL = "select count(*) as nRec from tblMoshPerformance  where moshPitID=".$boxID;
    $gRec = null;
    $gresult = mkyMsqry($SQL);
    $gRec = mkyMsFetch($gresult);

    if ($gRec) {
      $nSongs = $gRec['nRec'];
    }
    $openAnk = "<a style='font-size:12px;' href='javascript:parent.OpenGigBox(".$tRec['moshPitID'].");'>";
    $name    = "<b> Mosh Title:</b> - ".$openAnk.$tRec['venuName']."</a>";
    $desc    = "<b>description:</b> - ".$tRec['description']."<br><b>Songs In Collection:</b> - ".$nSongs;
    $nViews  = $tRec['nViews'];

    if ($nSongs > 0) {
      echo "<tr  valign=top>";
      echo "<td align='left'>".$wzImg.$name."<br>".$desc. "</td>";
      echo "<td align='left'>" ?>

      <?php echo $openAnk;?><img title='Listen To This MoshBOX Now'
      style='float:left;margin-bottom:9px;margin-right:5px;border-radius:.25em;width:120px;height:90;'
      src='<?php echo getSongIMG($uTubeCD) ?>'></a>
      <B>Artist:</b> - <?php echo $artist ?><br>
      <B>Song:</b> - <?php echo $title ?><br>
      <B>Views:</b> - <?php echo $nViews ?> </td></tr>
      <?php
    }
    $i = $i + 1;
    $n = $n + 1;
} 
