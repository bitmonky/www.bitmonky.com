<?php 
if (isset($_GET['newPg'])){$pg = clean($_GET['newPg']);} else {$pg = 0;}
$nextPage = $pg;
?>

<script>
var feedConn = null;
var likesConn = null;

function wzFeedLoad(){
  //** Initialize Here
   feedConn    = parent.getHttpConnection();
   likesConn   = parent.getHttpConnection();
   readFeed();
}
function readFeed(){
     var currentTime = new Date();
     var ranTime = currentTime.getMilliseconds();
     var url = '/whzon/public/mbrActivityFeed.php?pg=<?php echo $pg;?>&wzID=<?php echo $sKey; echo $feedData;?>&xm=' + ranTime ;
     feedConn.open("GET", url,true);
     feedConn.onreadystatechange = doWriteFeed;
     feedConn.send(null);
}
function doWriteFeed(){
 
    if (feedConn.readyState == 4){
      if(feedConn.status  == 200){ 
        var html = mkyTrim(feedConn.responseText);
        var wzout = document.getElementById('myTownActivities');
		if (wzout){
		  wzout.innerHTML = html;
		  fhReDrawFrame();
		}
      }
    }

}
  function activityVoteTxt(vote,acID){
     var liketxt = document.getElementById('frmlike' + acID).fliketxt.value;
	 if (liketxt == '') {
	   alert('comment on why you ' + vote + ' this!');
	 }
	 else {
       var waiting = document.getElementById('newLikeSpot' + acID);
	   if (waiting) {
	     waiting.innerHTML = '<img style="border-radius:50%;width:35px;height;35px;" src="<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/imgLoading.gif"/> Please Wait...';
		 newlikes = waiting;
	   }
       var currentTime = new Date();
       var ranTime = currentTime.getMilliseconds();
       var url = '/whzon/public/activityLikeUpdate.php?wzID=<?php echo $sKey;?>&fv=' + vote + '&facID=' + acID + '&fliketxt=' + liketxt + '&xm=' + ranTime ;
       likesConn.open("GET", url,true);
       likesConn.onreadystatechange = doActivityVoteTxt;
       likesConn.send(null);
	 }
  }
  function doActivityVoteTxt(){
    if (likesConn.readyState == 4){
      if (likesConn.status  == 200){ 
        parent.checkLikeStatus(likesConn.responseText);
	    if (newlikes) {
	      newlikes.innerHTML = '';
	    }
//	    clearTimeout(feedTimer);
	    readFeed();
      }
    }
  }
function wzPopScrollJoin(mbrID){
        parent.wzQuickReg();
}
function startPShare(){
  <?php if ($userID != 0){
    echo "document.getElementById('typeBox').placeholder = '...Please Select from one of the options above';";
	//echo 'parent.startPShare();';
  }
  else {
    echo 'alert("You Must Be A Member To Post... Join Now Or Login.");';
    echo 'parent.wzQuickReg();';
  }?>
}
</script>
   <p>
<div class='infoCardClear'>
   <B><span style='color:darkKhaki;font-wheight:bold;'>People In My <?php echo $scopeDisplay ?></span> - See Also </b>
   <?php drawMyMenu($myMode,$modes);?>
</div>

<p>
<table style='margin-top:50px;width:100%'>
<tr valign='top'>
<?php
if (!$sessISMOBILE){
  ?>
  <td ID='feedContainer' style='width:50%;padding-right:15px;'>
  <?php 

     $postPhoto = 'javascript:parent.startPShare();';
     $postVideo = '/whzon/mbr/vidView/frmQuickVideos.php?wzID='.$sKey.'&fmode=1';
     $postBlog  = '/whzon/mbr/blog/mbrMBLOG.php?fTopicID=0&wzID='.$sKey.'&fwzUserID='.$userID;
     $postSale  = '/whzon/mbr/mbrPostClassified.php?itemID=post&wzID='.$sKey;
     if ($userID == 0){   
       $postVideo = 'javascript:startPShare();';
       $postBlog  = 'javascript:startPShare();';
       $postSale  = 'javascript:startPShare();';
     }
     ?>

     <div style='border-radius:.5em .5em 0em 0em;margin:0px;background:darkKhaki;border:0px solid darkKahaki;border-bottom:0px;width:100%;'>
     <a href='<?php echo $postPhoto;?>'><img style='border:0px;' 
     src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/_photoIcon.png' height='24px'/></a>Photo

     <a href='javascript:wzLink("<?php echo $postVideo;?>");'><img style='border:0px;' 
     src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/_videoIcon.png' height='24px'/></a>Video

     <a href='javascript:wzLink("<?php echo $postBlog;?>");'><img style='border:0px;' 
     src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/_newsIcon.png'  height='24px'/></a>Blog

     <a href='javascript:wzLink("<?php echo $postSale;?>");'><img style='border:0px;' 
     src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/_saleIcon.png'  height='24px'/></a> Sell
     </div>
     <form id="friends" style="margin:0px;padding:0px;width:100%;background:#eeeeee;" method="get" action="">
     <input name="wzID" value="<?php echo $sKey;?>" type="hidden"/>
     <input name="fmbrID" value="<?php echo $wzUserID;?>" type="hidden"/>
     <textarea id="typeBox" style="border:0px;background:#eeeeee;FONT-FAMILY: tahoma,sans-serif;font-size:13px;font-weight:bold;padding:2px;width:98%;height:43px;" 
     onclick="startPShare();" placeholder=" ...Click Here To Share Photo's Or Videos." name="fshout" wrap="VIRTUAL" scrollbars="no"></textarea>
     <div><span ID='falertwait'> <input  style="margin-top:3px;border-radius: .25em;margin-right:15px;border:0px 
     solid #fefefe;margin:3px;" type='button' value='Share It Now' onclick='startPShare();'></span></div>
     </form>

   <div style='width:100%;margin-top:25px;' ID='myTownActivities'>
   <img style='border-radius:50%;width:35px;height;35px;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/imgLoading.gif'> Loading Please Wait...	
   </div>
   </td>
   <td style='width:50%;padding-left:25px;'>
  <?php
}
else {
  echo "<td>";
}
?>
<div align='right' class='infoCardClear' style=''>
<a href="javascript:wzLink('/whzon/mblp/selFindMbrAutoT.php?wzID='+parent.sID);">
Search By Nickname
</a>
</div>
<table style='width:100%'>
<tr valign='top'>
<td>
<?php 

$SQL = "SELECT tblwzUser.sex, tblwzUser.age, tblwzOnline.wzUserID as online, ";
$SQL .= " tblwzUser.verified, tblwzUser.imgFlg,tblwzUser.wzUserID,tblwzUser.firstname, tblwzUser.profileText, ";
$SQL .= " tblwzUser.country, tblwzUser.city, tblwzUser.lastOnline  ";
$SQL .= " from tblwzUser  inner join tblCity  on tblCity.cityID=tblwzUser.cityID ";
$SQL .= " left join tblwzOnline  on tblwzUser.wzUserID = tblwzOnline.wzUserID ";
$SQL .= " where ".$userSearch." and  tblwzUser.sandBox is null  and tblwzUser.imgFlg=1 ";
$SQL .= " order by  online desc,lastOnline desc;";

$winX = 200;
$Xinc = 120;


echo "<p><table>";
$i = 0;
$nRows = 30;

$link = $linkRoot;
$appName = "myTown.php";

$nTop = $pg + $nRows;

$selTop = "desc limit ".$nTop." ";

$SQL = mkyStrReplace("desc;",$selTop,$SQL);

$tRec = null;
$result = mkyMsqry($SQL);
$tRec = mkyMsFetch($result);

$cpage = 0;
while($tRec && $cpage < $nextPage) {
  $tRec = mkyMsFetch($result);
  $cpage = $cpage + 1;
}

$frIconStyle = "";
$onIconStyle = "";

if ($mobile){
  $frIconStyle = "height:30px;width:36px;vertical-align:middle;";
  $onIconStyle = "height:26px;width:160px;margin-top:15px;";
}
if ($digID){
  showDigListing($digID);
}
While ($tRec && $i < $nRows){

  $age = $tRec['age'];
  $sex = $tRec['sex'];

  if ($sex !== null) {
    if ( $sex == 1) {
      $sex = "f - ";
    } 
    else if ( $sex == 3) {
      $sex = "A YouTuber ";
      $age = '';
    }
    else if ( $sex == 4) {
      $sex = "A Business ";
      $age = "";
    }
    else {
      $sex = "m - ";
    }
  }

  $profile_A = "<a style='font-size:14px;' href='javascript:wzLink(\"/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."\");'>";
  $SQL = "select mWebFlg, websiteID, Title, rating, wsImgFlg, tblTopSites.wzUserID From tblTopSites  ";
  $SQL .= " left outer join tblwsRatings  on tblTopSites.wsRatingID=tblwsRatings.wsRatingID ";
  $SQL .= " where wzUserID=".$tRec['wzUserID']." ORDER BY wsImgFlg desc;";

  $wRec = null;
  $wresult = mkyMsqry($SQL) or die($SQL);
  $wRec = mkyMsFetch($wresult);

  if ( $wRec ) {
    $linkTrade_A = "<a href=javascript:openLinkTrade('/wzUsers/trackerLT/confirmTradeLink.asp?fwebsiteID=".$wRec['websiteID']."',".$wRec['websiteID'].")>";
  }

  if ($userID != 0 ) {
    $folJS = "href=javascript:parent.wzAPI_showFrame('/whzon/mbr/fanRequestFrm.php?wzID=".$sKey."&fanID=".$tRec['wzUserID']."',380,230,500,".$winX.");";
  } 
  else {
    $folJS = "href='javascript:parent.xyAquickLogin(500,".($winX-100).");'";
  }

  $winX = $winX + $Xinc;
  $cityLink    = "/whzon/mytown/myTown.php?franCID=&wzID=".$sKey."&fscope=myCity&fcatID=&fwzUserID=".$tRec['wzUserID'];
  $countryLink = "/whzon/mytown/myTown.php?franCID=&wzID=".$sKey."&fscope=myCountry&fcatID=&fwzUserID=".$tRec['wzUserID'];

 ?>
 <tr valign=top>
 <td style='width:88px;'> 
 <?php echo $profile_A ?> 
 <img title="View <?php echo $tRec['firstname'] ?>'s profile" style='border-radius:.5em;width:72px;height:90px;' 
 src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrImg.php?id=<?php echo $tRec['wzUserID'] ?>'></center>
 </a>
 </td>
 <td ID='tag<?php echo $tRec['wzUserID'] ?>'>
 <div class='infoCardClear'>
 <a <?php echo $folJS ?>><img title='Send Friends Request' style='border-radius:.3em;vertical-align:middle;<?php echo $frIconStyle ?>' 
 src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/friendsIcon.png'></a>
 <?php echo $profile_A ?><?php echo $tRec['firstname'] ?></a><span style='font-size:13px;'> <b><?php echo $sex ?> <?php echo $age ?></b>
 <b>From</b> <a href='javascript:wzLink("<?php echo $cityLink."\");'>".$tRec['city'];?></a>,
 <a href='javascript:wzLink("<?php echo $countryLink."\");'>".$tRec['country'];?></a> </span><br style='clear:right;'>

 <?php if ($tRec['online']){ ?>
   <?php if ( $userID != 0) { ?>
     <a href='javascript:parent.wzPopChat(<?php echo $tRec['wzUserID'] ?>);'>
   <?php } else { ?>
     <a href='javascript:parent.wzQuickReg(<?php echo $tRec['wzUserID'] ?>);'>
   <?php } ?>
   <img title='Start Chat With <?php echo $tRec['firstname'] ?>' style='float:right;margin:0.5em 0em 1em 1.5em;border-radius:50%;<?php echo $onIconStyle ?>' 
   src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/onlineIcon.png'></a>
 <?php } else { ?>
   <b>Last Online:</b> <?php echo $tRec['lastOnline'] ?>
 <?php } ?>
 <P> 
 <?php  
 if ( $tRec['profileText' ] != "" ) { 
   ?>
   <b>Greeting:</b> <?php echo left(splitLWords($tRec['profileText']),180);?>  <?php echo $profile_A ?>..more</a><br>
   <?php 
 } 
 ?>
  <p/>
  <?php 
  $wc = 0;
  while ($wRec && $wc < 5){
    if ( $wRec['mWebFlg'] == 1 ) {
      $webURL = "https://bitmonky.com/whozon/mbrMWeb.asp";
    }  
    else {
      $webURL = "/whzon/mbr/viewWebsite.php";
    }

    if ( $wRec['wsImgFlg']== 1 ) { 
      ?>
      <a href='javascript:wzLink("<?php echo $webURL ?>?fwebsiteID=<?php echo $wRec['websiteID'] ?>&fwzUserID=<?php echo $tRec['wzUserID'] ?>");'>
      <img title='<?php echo left($wRec['Title'],25) ?>..' style='float: left;border: 0px solid #777777;margin:0.25em 1em 1em 0em;' 
      src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getWsMiniImg.php?id=<?php echo $wRec['websiteID'] ?>'>
      <?php echo left($wRec['Title'],25) ?>..
      </A>  <?php echo $wRec['rating'] ?>
      <br style='clear:left;'> 
      <?php 
    } 
    else {
      ?>
      | <a href=Javascript:parent.wzOpenNewLOG('<?php echo $webURL ?>?fwebsiteID=<?php echo $wRec['websiteID'] ?>&fwzUserID=<?php echo $tRec['wzUserID'] ?>',<?php echo $tRec['wzUserID'] ?>)>
      <?php echo left($wRec['Title'],25) ?>..</A> <?php echo $wRec['rating'] ?><br>
      <?php
    }
    $wRec = mkyMsFetch($wresult);
    $wc = $wc + 1;
  }
  ?>
  <br><br>
  </div>
  </td>
  </tr>
  <?php 
  $i = $i + 1;
  if ($i == 4){
    echo "<tr><td colspan='3'>";
    getBigCubeAd('5px');
    echo "</td></tr>";
  }
  $tRec = mkyMsFetch($result);
}
echo "</table>";
if ($i > 0 ) {
  echo "<p><a href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage + $nRows)."\");'>Next</a>";
}  
if ($nextPage > 0 ) {
  echo " | <a href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage - $nRows)."\");'>Back</a>";
}
echo " | <a href='javascript:wzLink(\"".$appName.$link."&newPg=0\");'>Top</a>";

if ($i != 4){
  getBigCubeAd('12px');
}
if (isset($_GET['ferror'])){
  if (clean($_GET['ferror']) == 1 ) {
    echo "<p><span class='errorMsg'></span>";
  }
}
function showDigListing($digID){
  global $whzdom,$i;
  global $sKey,$winX,$Xinc;
  global $userID;
  global $frIconStyle;

  $SQL = "SELECT tblwzUser.sex, tblwzUser.age, tblwzOnline.wzUserID as online, ";
  $SQL .= " tblwzUser.verified, tblwzUser.imgFlg,tblwzUser.wzUserID,tblwzUser.firstname, tblwzUser.profileText, ";
  $SQL .= " tblwzUser.country, tblwzUser.city, tblwzUser.lastOnline  ";
  $SQL .= " from tblwzUser  left join tblCity  on tblCity.cityID=tblwzUser.cityID ";
  $SQL .= " left join tblwzOnline  on tblwzUser.wzUserID = tblwzOnline.wzUserID ";
  $SQL .= "where tblwzUser.wzUserID = ".$digID;

  $result = mkyMsqry($SQL);
  $tRec = mkyMsFetch($result);
  if (!$tRec){
    return;
  }
  $age = $tRec['age'];
  $sex = $tRec['sex'];

  if ($sex !== null) {
    if ( $sex == 1) {
      $sex = "f - ";
    }
    else {
      $sex = "m - ";
    }
  }

  $profile_A = "<a style='font-size:14px;' href='javascript:wzLink(\"/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."\");'>";

  $SQL = "select mWebFlg, websiteID, Title, rating, wsImgFlg, tblTopSites.wzUserID From tblTopSites  ";
  $SQL .= " left outer join tblwsRatings  on tblTopSites.wsRatingID=tblwsRatings.wsRatingID ";
  $SQL .= " where wzUserID=".$tRec['wzUserID']." ORDER BY wsImgFlg desc;";

  $wRec = null;
  $wresult = mkyMsqry($SQL) or die($SQL);
  $wRec = mkyMsFetch($wresult);

  if ( $wRec ) {
    $linkTrade_A = "<a href=javascript:openLinkTrade('/wzUsers/trackerLT/confirmTradeLink.asp?fwebsiteID=".$wRec['websiteID']."',".$wRec['websiteID'].")>";
  }

  if ($userID != 0 ) {
    $folJS = "href=javascript:parent.wzAPI_showFrame('/whzon/mbr/fanRequestFrm.php?wzID=".$sKey."&fanID=".$tRec['wzUserID']."',380,230,500,".$winX.");";
  }
  else {
    $folJS = "href='javascript:parent.xyAquickLogin(500,".($winX-100).");'";
  }

  $winX = $winX + $Xinc;
  $cityLink    = "/whzon/mytown/myTown.php?franCID=&wzID=".$sKey."&fscope=myCity&fcatID=&fwzUserID=".$tRec['wzUserID'];
  $countryLink = "/whzon/mytown/myTown.php?franCID=&wzID=".$sKey."&fscope=myCountry&fcatID=&fwzUserID=".$tRec['wzUserID'];

 ?>
 <tr valign=top>
 <td  ID='tag<?php echo $tRec['wzUserID'] ?>' colspan='2' style='width:100%;'>
 <div class='infoCardClear' style='padding:0em;background:black;'>
 <?php echo $profile_A ?>
 <center><img title="View <?php echo $tRec['firstname'] ?>'s profile" style='border-radius:0em;max-width:100%;'
 src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrPImg.php?id=<?php echo $tRec['wzUserID'] ?>'></center>
 </center></a>
 </div>
 <div class='infoCardClear'>
 <a <?php echo $folJS ?>><img title='Send Friends Request' style='border-radius:.3em;vertical-align:middle;<?php echo $frIconStyle ?>'
 src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/friendsIcon.png'></a>
 <?php echo $profile_A ?><?php echo $tRec['firstname'] ?></a><span style='font-size:13px;'> <b><?php echo $sex ?> <?php echo $age ?></b>
 <b>From</b> <a href='javascript:wzLink("<?php echo $cityLink."\");'>".$tRec['city'];?></a>,
 <a href='javascript:wzLink("<?php echo $countryLink."\"'>".$tRec['country'];?></a> </span><br style='clear:right;'>

 <?php if ($tRec['online']){ ?>
   <?php if ( $userID != 0) { ?>
     <a href='javascript:parent.wzPopChat(<?php echo $tRec['wzUserID'] ?>);'>
   <?php } else { ?>
     <a href='javascript:parent.wzQuickReg(<?php echo $tRec['wzUserID'] ?>);'>
   <?php } ?>
   <img title='Start Chat With <?php echo $tRec['firstname'] ?>' style='float:right;margin:0.5em 0em 1em 1.5em;border-radius:50%;<?php echo $onIconStyle ?>'
   src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/onlineIcon.png'></a>
 <?php } else { ?>
   <b>Last Online:</b> <?php echo $tRec['lastOnline'] ?>
 <?php } ?>
 <P>
 <?php
 if ( $tRec['profileText' ] != "" ) {
   ?>
   <b>Greeting:</b> <?php echo left(splitLWords($tRec['profileText']),180);?>  <?php echo $profile_A ?>..more</a><br>
   <?php
 }
 ?>
  <p/>
  <?php
  $wc = 0;
  while ($wRec && $wc < 5){
    if ( $wRec['mWebFlg'] == 1 ) {
      $webURL = "https://bitmonky.com/whozon/mbrMWeb.asp";
    }
    else {
      $webURL = "/whzon/mbr/viewWebsite.php";
    }

    if ( $wRec['wsImgFlg']== 1 ) {
      ?>
      <a href='javascript:wzLink("<?php echo $webURL ?>?fwebsiteID=<?php echo $wRec['websiteID'] ?>&fwzUserID=<?php echo $tRec['wzUserID'] ?>|);'>
      <img title='<?php echo left($wRec['Title'],25) ?>..' style='float: left;border: 0px solid #777777;margin: 2px;'
      src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getWsMiniImg.php?id=<?php echo $wRec['websiteID'] ?>'>
      <?php echo left($wRec['Title'],25) ?>..
      </A>  <?php echo $wRec['rating'] ?>
      <br style='clear:left;'>
      <?php
    }
    else {
      ?>
      | <a href=Javascript:parent.wzOpenNewLOG('<?php echo $webURL ?>?fwebsiteID=<?php echo $wRec['websiteID'] ?>&fwzUserID=<?php echo $tRec['wzUserID'] ?>',<?php echo $tRec['wzUserID'] ?>)>
      <?php echo left($wRec['Title'],25) ?>..</A> <?php echo $wRec['rating'] ?><br>
      <?php
    }
    $wRec = mkyMsFetch($wresult);
    $wc = $wc + 1;
  }
  ?>
  <br><br>

  </div>
  </td>
  </tr>
  <?php
  $i = $i + 1;
}
?>
<br><br>
</td>
<td style='padding:0px;text-align:right;'>
</td>
</tr>
</table>
</td>
</tr>
</table>
<div ID='frameFooter'></div>
