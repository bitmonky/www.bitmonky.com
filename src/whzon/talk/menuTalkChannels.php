<!doctype html>
<html class='pgHTM' lang="en">
<head>
  <meta charset="utf-8">
  <title>Whzon.com</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>
    <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
<?php
//ini_set('display_errors',1); 
//error_reporting(E_ALL);
include_once("../mkysess.php");

$qry = safeGET('fqry');
    if (isset($_GET['history'])) {
       $history = clean($_GET['history']);
       $histLink = "history=on&";
       $histCheck = " checked='checked' ";
       $histValue = " 1 == 1 ";
    } else {
      $history = null;
      $histLink = null;
      $histCheck = "";
      $histValue = " 1 == 2 ";
    }

?>
<script language="JavaScript">
  var sURL = window.location.href;
  function doLoad() {
    //setTimeout( "refresh()",25*1000 );
  }
  function refresh(){
    window.location.href = sURL;
  }
  doLoad();
function togleHistory(){
  if (<?php echo $histValue;?>){
    parent.chanFilter = null;
    sURL = sURL.replace("&history=on","");
  }
  else {
  parent.chanFilter = 'history';
  sURL = sURL + '&history=on';
  }
  refresh();
}

function highlight(row){
    var wzoutput = document.getElementById("trow" + row);
    wzoutput.style.background="#8ec634";
}
function undoHighlight(row){
    var wzoutput = document.getElementById("trow" + row);
    wzoutput.style.background="none";
}

function wzPopDELCh(){
  winName = "wzPopAddCh";
  var paddCh= window.open("popChatterDelCh.asp",winName,"target=new,width=400,height=300,resizable=yes,scrollbars=no, scrolling=none");
  paddCh.focus();
}
function wzPopADDCh(){
  winName = "wzPopAddCh";
  var paddCh= window.open("popChatterAddCh.asp",winName,"target=new,width=400,height=300,resizable=yes,scrollbars=no, scrolling=none");
  paddCh.focus();
}
function wzOpenSiteLOG(URL){
  winName = "wzCom";
  var win2 = window.open(URL,winName,"target=new,width=1025,height=600,resizable=no,scrollbars=yes");
  win2.focus();
}
</script>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
</head>

<body class='pgBody' style='margin:15px;'  onmouseover='parent.mcancelclosetime();' onmouseout='parent.mclosetime();'>


<?php
    $isAdmin=False;

    if ($userID!=0){
      if ($userID==17621){
        $isAdmin=True;
      }
    }


    if ($qry==""){
      $searchStr = " ";
      $wzTIMESTAMPDIFF  = " TIMESTAMPDIFF(HOUR,cDate,now()) < 24  and ";
      }
    else{
      $wzTIMESTAMPDIFF="";
      $searchStr=" and name like '%".$qry."%' ";
    }

    if ($qry!=""){
       $SQL = "SELECT  videoID,photoID,spoken,MoshBoxID, channelID,name, chanWSID,ownerID, count(tblChatterBox.channel) as nPosts, nInRoom as nChats from tblChatChannel ";
       $SQL .= "left join tblChatterBox on channel=channelID where isMod is null and ".$wzTIMESTAMPDIFF." not name='' ".searchStr." ";
       $SQL .= "group By channelID,chanWSID,rdate,Name,ownerID, guide,stickyChan, nInRoom,moshBoxID,spoken order by stickyChan desc, count(tblChatterBox.channel) desc,tblchat.channel.rdate desc, name";
      }
    else{
      $SQL = "SELECT Top 300 videoID,photoID,spoken, moshBoxID, channelID , name,  chanWSID, ownerID, nPosts, nInRoom as nChats from tblChatChannel ";
      if ($history){
        $SQL .= "inner join tblChanHistory  on chChanID = channelID ";
        $history = " chUID = ".$userID. " and ";
      }
      $SQL .= "where ".$history." isMod is null and  (not nInRoom is null or not nPosts is null) order by stickyChan desc, nInRoom desc, nPosts desc";
    }
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    echo "<div class='infoCardNBG'>";
    if ($tRec) {
      $pg=0;
      if ( safeGET('newPg')!="" ){
        $pg = safeGET('newPg');
      }

      $nextPage = $pg; 
      $n        = $pg + 1;
      $pgcount =0;
      while ($tRec && $pgcount<$nextPage) {
        $pgcount = $pgcount + 1;
        $tRec = mkyMsFetch($result);
      }

      $i=0;
      $nRows=9;
      $appName="menuTalkChannels.php?".$histLink;

      if ( !$tRec ) {
        echo "! No Channels To List...";
      }


      $tagID=119423;
      $rowStyle="onmouseover=highlight('".$tagID."'); onmouseout=undoHighlight('".$tagID."');";

      if ($inChatChanID== $tagID) {
        $rowStyle=" Style='background:#222322;' ";
      }

      $goToURL="href='changeTalkChannel.asp?fchan=".$tagID."'";
     
?>
      <div align='right'>
        <input type='checkbox' name='tmode' onclick='togleHistory();' <?php echo $histCheck;?>> <?php sayTxt('My History Only.');?>
        <img  src='//image.bitmonky.com/img/settingsIcon.png' style='margin-left:3px;height:17px;width:17px;vertical-align:middle;border:0px;'/>
      </div>
      <h3><?php sayTxt('MonkyTalk Channels');?></h3>
      <table class='docTableSmall' style='width:100%;'>
      <tr><td><b><?php sayTxt('Channel Name');?></b></td><td style='text-align:right;'><b><?php sayTxt('Total Posts : Online');?></b></td></tr>
<?php
      while ($tRec && $i<$nRows) { 
        $tagID = $tRec['channelID']; 
        $websiteID=$tRec['chanWSID'];
        $mosh=$tRec['moshBoxID'];
        $spoken=$tRec['spoken'];
        $photoID = $tRec['photoID'];
        $ownID   = $tRec['ownerID'];
        $videoID = $tRec['videoID'];

        if (!is_null($spoken)) {
          $spoken= "<b style='color:#888888;'> - ".$spoken."</b>";
        }

        $nInRoom=$tRec['nChats'];
        $inRoom="";
        if (!is_null($nInRoom)){
          if ($nInRoom > 0 ) {
            $inRoom=" : ".$nInRoom;
          }
        }

        if (is_null($websiteID)) {
          $websiteID=0;
        }

        $adminLink="";
        if ($isAdmin){
          $adminLink="<a href=javascript:parent.wzAPI_showFrame('/whozAdmin/frmRemoveChan.asp?fchanID=".$tagID."',300,180,200,200);>X</a>";
        }

        if ($websiteID==0){
          $fanlink="<a href=#".$tagID." onclick=wzOpenSiteLOG('/whozon/mbrFanRoom.asp?fwzUserID=".$tRec['ownerID']."');>";
        }
        else{
          $fanlink="<a href=#".$tagID." onclick=wzOpenSiteLOG('/whozon/fanRoom.asp?fwebsiteID=".$websiteID."');>";
        }

        $rowStyle="onmouseover=highlight('".$tagID."'); onmouseout=undoHighlight('".$tagID."');";

        if ($inChatChanID==$tagID) {
          $rowStyle=" Style='background:#222322;' ";
        }

        $moshImg="";
        if (is_null($mosh)){
          $goToURL="href='javascript:parent.wzChangeChannel(".$tRec['channelID'].");'";
        }
        else{
          $goToURL="style='color:darkKhaki;' title='Listen To Music While You Chat!' alt='Listen To Music While You Chat!' href='javascript:parent.OpenGigBox(".$mosh.")'";
          $moshImg="<img style='border: 0px none; vertical-align: top; margin-top: 0px; margin-left: 5px; height: 12px; width: 10px;' src='//image.bitmonky.com/img/musicIcon.png'>";
        }
		    if ($photoID) {
          $goToURL="style='' href='javascript:parent.wzGetPage(\"https://whzon.com/whozon/clone/mbrViewPhotos.asp?fwzUserID=".$ownID."&vPhotoID=".$photoID."\");'";
        }		
		    if ($videoID) {
          $goToURL="style='' href='javascript:parent.wzGetPage(\"/whzon/mbr/vidView/viewVideoPg.php?wzID=".$sKey."&videoID=".$videoID."\");'";
        }		

?>
        <tr  ID='trow<?php echo $tagID;?>' valign='top' <?php echo $rowStyle;?>><td>
        <img style='width:18px;height:24px;float:left;margin-right:4px;margin-bottom:2px;border-radius:50%;
        border: 0px solid #eeeeee;' src='//image.bitmonky.com/getMbrTnyImg.php?id=<?php echo $ownID;?>'>
        <?php echo $adminLink;?> <a ID='<?php echo $tagID;?>' target='_top' <?php echo $goToURL;?>>
        <?php echo left(mkyTrim(realTimeNic($tRec['name'])),33); echo $moshImg;?></a><?php echo $spoken;?> </td><td style='text-align:right;'><?php echo $tRec['nPosts']; echo $inRoom;?></td></tr>
      
<?php
        $i=$i+1;
        $n=$n+1;
        $tRec = mkyMsFetch($result);
      }//wend


      echo "</table><p><a href='".$appName."wzID=".$sKey."&newPg=".($nextPage + $nRows)."&fqry=".mkyUrlEncode($qry)."'>".getTRxt('Next')."</a>";
      if($nextPage > 0) {
        echo " | <a href='".$appName."wzID=".$sKey."&newPg=".($nextPage - $nRows)."&fqry=".mkyUrlEncode($qry)."'>".getTRxt('Back')."</a>";
      }
      echo " | <a href='".$appName."wzID=".$sKey."&newPg=0&fqry=".mkyUrlEncode($qry)."'>".getTRxt('Top')."</a>";
    }
echo "</div>";

?>
</body>
</html>
