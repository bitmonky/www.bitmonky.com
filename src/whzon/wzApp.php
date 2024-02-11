<?php
if (stripos($_SERVER['SERVER_NAME'],'bitmonky.com') === false){
  header('Location: https://www.gogominer.com/gogoMiner.php'); //closed.php'); //whzon/wzAppClosed.php');
  exit('');
}
//header("Access-Control-Allow-Origin: antsrv.bitmonky.com");
include_once("phpCacheMgr.php");
phpCachePg(60);

include_once("mkysess.php");
include_once("JSON.php");
$furl = safeGET('furl');
$fnrl = safeGET('fnrl');
$IP  = left(clean($_SERVER['REMOTE_ADDR']),15);
$jIP = jsonIPData($IP);
$IPC = jsonIPToCountryID($IP,$jIP);

allowIn($userID);

$PC_imgbck = "img/junglebckPC.png";
//if ($userID == 17621){
  $PC_imgbck = "img/bckTest.jpeg";
//}
if ($deactivDate){
  header('Location: /whzon/gold/frmRestoreACC.php?wzID='.$sKey);
  exit('');
}
if ($inSandBox){
  header('Location: /whzon/gold/getOutOfJail.php?wzID='.$sKey);
  exit('');
}
/*
if ($gotoVJail){ //|| $userID == 820231){
  if ($userID > 0 ){ //|| $userID == 820231){
    header('Location: /whzon/gold/mustVerifyEmail.php?wzID='.$sKey);
    exit('');
  }
}
*/
$vcontest = safeGET('fcontest');

if (isset($_GET['tmode'])){$tmode = safeGET('tmode');} else {$tmode = null;}
if (isset($_COOKIE["wzMblViewOn"])){
  $wzMobileView = clean($_COOKIE["wzMblViewOn"]);
}
else {
  $wzMobileView = null;
}
$showMobLink = null;
require_once 'mblDetect/Mobile_Detect.php';
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$gsoftON = mkyStripos($_SERVER['SERVER_NAME'],'gsoft.bitmonky.com');
if ($detect->isMobile() || $detect->isTablet() || $gsoftON !== false){
  if ( $wzMobileView === null || $wzMobileView == "Yes"){
	if (isset($_SERVER['QUERY_STRING'])){$qry = "?".$_SERVER['QUERY_STRING'];}else {$qry=null;}
        if ($qry == '?'){
          $qry = null;
        }
	if ($qry){
          if (mkyStrpos($qry,"?") === false){
	    $qry .= "?mblp=on";
   	  }
	  else {
	    $qry .= "&mblp=on";
	  }
        }
	$nmURL = "/whzon/mblp/wzMbl.php".$qry;
	header("Location: ".$nmURL);
	exit("");
  }
  else {
    $showMobLink = 'yes';
  }
}
include_once("wzAppALinc.php");
setHeaderTags($furl);
if (isset($_SERVER['QUERY_STRING'])){
  $canonQry = $_SERVER['QUERY_STRING'];
  if (trim($canonQry) != ""){
    $canonQry = '/whzon/wzApp.php?'.$canonQry;
  }
}
else {$canonQry=null;}
?>
<!doctype html>
<html lang="en">
<head>
<!-- Google tag (gtag.js) -->
<link rel="preload" fetchpriority="high" as="image" href="<?php echo $GLOBALS['MKYC_imgsrv']."/img/digihutBan.webp";?>" type="image/webp">
<script async src="https://www.googletagmanager.com/gtag/js?id=G-L9BBF7ES7Z"></script>
<script>
  var canRunAds = true;
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-L9BBF7ES7Z');
</script>

  <meta charset="utf-8"/>
  <title><?php echo $mPgTitle;?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>
  <link rel="canonical" href='https://www.bitmonky.com<?php echo $canonQry;?>'/>
  <!--script src="/whzon/ads.js"></script-->
  <meta name="description" content="<?php echo $mPgDesc;?>"/>
  <meta property="og:title" content="<?php echo $mPgTitle;?>"/>
  <meta property="og:url" content="<?php echo $mPgUrl;?>"/>
  <meta property="og:image" content="<?php echo $mPgImage;?>"/>
  <meta property="og:description" content="<?php echo $mPgDesc;?>" />
	  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <?php 
  include_once("schemaOrgInc.php");
  include_once("wzImpMineInc.php");
  $mobApp = null; 
  doALOutput();
  ?>
  <style>
  @keyframes mkyFlash {
    10% {
      background: #74a02a;
    }
    /* Adding a step in the middle */
    50% {
      background: blue;
    }
    100% {
      background: purple;
    }
  }
  html {
    background-color : #333333;
    <?php 
    //if ($userID == 17621){
      echo 'background-image : url("/img/mkyBackTexture3.webp");';
      echo 'background-repeat : no-repeat;';
    //}
    ?>
  } 
  </style>

</head>
<body onload="wzStartApp();" style='margin-left:8%;margin-right:8%;' >
<div class='chatAlert' ID='videoChatWindow' style='position:fixed;border-radius:.5em;display:none;top:25px;left:25px;background:#222222;'><div id='videoChatFrame' style='border:0px;padding:.25em;'></div></div>
<div ID='mkyCookie' style='width:calc(98% - 2em);max-width:calc(1460px -2em);position:absolute;padding:1em;display:none;background:#333333;color:white;'>

<b>BitMonky Uses Tracking Cookies for session managment... To Agree And Continue Click </b>
<input type='button' value=' Accept ' onclick='mkyAcceptCKY();'> |
<input type='button' value=' Leave Site ' onclick='mkyLeaveSite();'>
</div>
<!--
//******************************
// Chat Notification Widget
//******************************
-->
<div class='chatAlert' ID='pvChatAlertSpotCon' style='position:fixed;display:none;top:250px;left:450px;'><div id='pvChatAlertSpot'></div></div>

<div ID='chatWidget' style='width:35%;display:none;border-radius: .5em;background:#222322;color:white;
padding:5px 15px 15px 15px;position:fixed;top:0px;left:0px;'>
<div ID='chatWidgetT' style='width:100%;'>
<?php
  echo "<span style='font-size:larger;font-weight:bold'>";
  getLogo();
  echo "</span>";
  $talkToID = $userID;
  if ($talkToID == 0){
    $talkToID=63555;
  }
  if (mkyStripos($whzdom,"bitmonky.com") !== false){
    echo "<img alt='BitMonky Mascot - Gord The Gorilla' ";
    echo "style='vertical-align:middle;border:0px;margin-right:12px;height:85px;width:85px;border-radius:50%;' ";
    echo "src='".$GLOBALS['MKYC_imgsrv']."/img/bitGoldCoin.webp'/>";
  }
?>
<img ID='chatWhoImg' style='float:right;border-radius:50%;margin:5px;width:45px;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/getMbrImg.php?id=<?php echo $talkToID;?>'/>

<div ID='chatWhoMsg' style='display='none;'></div>

<form >
<textarea ID='chatWidgetBox'  <?php if ($userID != 0){ echo "oninput='scanInput(null);'";}?>
placeholder="<?php sayTxt('Type Here To Chat');?>..." style='width:90%;height:7.5em;'></textarea><br/>
<input type='hidden' ID='postToChan' value=''/>
<input type='button'  value=' Send ' onclick='sendMsg(true);'/>
<input ID='goPrivate' type='button' value=' <?php sayTxt('Go Private');?> '  onclick=''/>
<input ID='goMoreWid' type='button' value=' <?php sayTxt('More');?> ' onclick=''/>
<input type='button' value=' Hide [^] '  style='background:orange' onclick='hideChatWidget();'/>
</form>
<p/>
<div align='right' style='font-weight:normal;color:white;'>
<span style='font-size:smaller;font-wieght:bold;padding:0px 15px 5px 0px;'><?php sayTxt('We Share The Wealth... When You Share The Word');?>.</span>
</div>
</div>
</div>
<!--
//********************
// Maind App
//********************
-->
<div ID='wzAppsContainer' style='display:none;border:0px solid #777777;position:absolute;'></div>
<div style='position:relative;z-index:1;background:#222322;'>
<!--
<div style='height:100%;width:100%;z-index:-1;position:absolute;top:0;left:0;'>
</div>
opacity:.59;background-image:url("<?php echo $GLOBALS['MKYC_imgsrv']."/".$PC_imgbck;?>");'>
</div>
-->

          <?php
          if ($userID == 0){
            echo "<div align='right' ID='optionsSpot' style='padding:8px 8px 0px 0px;'>";
            ?>
            <span style="padding:3px;background:#83b364;border-radius: .25em;"><a style='color:white;' href="javascript:wzGetPage('/whzon/adMgr/advertiseInfo.php')"><?php sayTxt('Advertiser Info');?></a></span>
             <span style="padding:3px;background:#83b364;border-radius: .25em;"><a style='color:white;' href="javascript:wzGetPage('/whzon/franMgr/franMgr.php')"><?php sayTxt('Claim A City');?></a></span>
             <span style="padding:3px;background:#83b364;border-radius: .25em;"><a style='color:white;' href="javascript:wzGetPage('/whzon/store/storeFrontsInfo.php?wzID=0')"><?php sayTxt('Store Fronts Info');?></a></span>
<!--
             <span style="padding:3px;background:#83b364;border-radius: .25em;"><a style='color:white;' href="javascript:wzGetPage('/whzon/live/chan/chanLiveStreams.php?wzID=0')">Video FAQ</a></span>
-->
            <?php
            echo "</div>";
          }
          ?>

<table style='width:100%;'>
  <tr>
    <td>
	  <div ID='offHeader'>
      <table style='width:100%;'>
      <tr style='vertical-align:top;'>
      <td style='padding:5px;padding-left:15px;white-space:nowrap;width:65px;'>
      <a alt='link To Mission Statement'  href="javascript:wzGetPage('/whzon/gold/mission.php?wzID=<?php echo $sKey;?>&fwzUserID=17621');">
      <?php getLogo();?>
      </a><span style='font-size:larger;font-weight:bold;color:darkKhaki;'><a target='bitMintPro' alt='Link To BitMonky token page on MINTME.com'
       href='https://www.bitmonky.com:#@Guerrilla+Coders+Inc'>The POWAPAY Economy</a></span>	
      <br/><a alt='Dogecoin Site Link' href='https://www.dogecoin.com' style='color:darkKhaki;' >We Accept Dogecoin</a>
      <!--span style='color:darkKhaki;font-weight:bold;'><?php sayTxt('A Guerrilla Coders Initiative');?></span-->
      </td>
      
      <td style='width:78px;'>	
      <a alt='How To Earn The Gold Page' href="javascript:wzGetPage('/whzon/franMgr/postForGold.php?wzID=<?php echo $sKey;?>')" 
      style="color:#cb0051;font-size:14px;" Title="click here for more info">
      <?php 
      if (mkyStripos($whzdom,"bitmonky.com") !== false){
	echo "<img alt='BitMonky Mascot - Gord The Gorilla' ";
	echo "style='vertical-align:middle;border:0px;margin-right:12px;height:74px;width:74px;border-radius:50%;' ";
	echo "src='".$GLOBALS['MKYC_imgsrv']."/img/bitGoldCoin.webp'/>";
      }
      else {
        echo "<img alt='BitMonky Mascot - Gord The Gorilla' ";
        echo "style='vertical-align:middle;border:0px;margin-right:5px;' src='".$GLOBALS['MKYC_imgsrv']."/img/potrainLogo.png'/>";
      }
      ?></a>
      <td><td style='padding-top:0px;'>
      
      <div style='margin:0em 1em 0em 3em;padding:1em;max-width:65%;background:#111111;border-radius:.75em;'>
      <?php
      $h1 = "H1";
      $brk = "";
      $ftext = "Learn To Do Business In the Digital Jungle ";
      $faction = "Set Up Your Hut and Start Earning Some";
      $ftext = "Learn To Do Business In the Digital Jungle ";
      $faction = "Join For Free. Learn Fast By Doing";
      if ($furl || $fnrl){
        $h1 = "SPAN";
	$brk = "<br/>";
	$ftext = "Learn To Do Business In the Digital Jungle ";
        $faction = "Join For Free. Learn Fast By Doing";
      }	  
      echo "<".$h1." style='margin:0em;font-size:2.3em;font-weight:bold;color:#888888;'>";
      sayTxt($ftext); 
      echo "</".$h1.">".$brk;
      ?>
      <input style='font-size:larger;' type='button' Value=' <?php echo $faction;?> ' onclick='wzQuickReg();'/> 
      <input style='font-size:larger;' type='button' Value=' Why Join ' onclick='wzGetPage("/whzon/gold/howToEarnGold.php");'/>
      <br/>
      <span style='color:white;font-weight:bold'></span>
      </div>
      <br/>
 		 	</span>
			<form ID='headerSearch' style='margin:0px;padding:0px;margin-top:0px;' method='GET' onsubmit='return runSearchQry();'>
			<input type='hidden' name='fmyMode' value='qry'/>
  			<input type='hidden' name='fwzUserID' value='17621'/>
            <!--span style='font-size:14px;color:#ffffff;white-space:nowrap;padding-left:18px;'>
            Search<input style='margin-left:4px;vertical-align:middle;' size='25' name='fqry' value=''/> 
            <input style='vertical-align:middle;' type='submit' value='Go'/>
			<a style='color:#8ec634;' href="javascript:wzGetPage('franMgr/mbrBonusRpt.php?wzID='+sID);">Help whzon Grow And Earn Some Cash For Your Efforts!</a> 
            </span--> 
            </form>
          </td>
        </tr> 
      </table>
      </div>
      <?php
      if ($userID != 0){
        echo "<div align='right' ID='optionsSpot' style='padding:8px 8px 0px 0px;'></div>";
      }
      ?>
      </div>

      <table  style='width:100%;'>
      <tr style='vertical-align:bottom;'>
      <td style='padding-left:15px;padding-bottom:12px;height:60px;'>
      <?php
      if ($showMobLink) {
        echo  "<span style='padding:3px;background:grey;border-radius: .25em;'><a style='color:#8ec634;' ";
        echo  " href='/whzon/mblp/setMobileView.php?fview=Yes'>Use Mobile</a></span>";
      }
      if ($userID !=0) {
        ?>
        <a alt='BitMonky Mascot - Gord The Gorilla' href="javascript:wzGetPage('/whzon/gold/howToEarnGold.php?wzID=<?php echo $sKey;?>');">
        <img style="width:66px;height:66px;border:0px;vertical-align:bottom;margin-bottom:0px;margin-right:3px;border-radius:50%;" 
        src="<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/bitGoldCoin.webp"></img></a>
        <?php 
      }
      ?>
      <span ID='wzMainControls'>
      <span style="padding:3px;background:#81b861;border-radius: .25em;"><a style='color:white;' 
      alt='Explore BitMonky Link'   href="javascript:popFindMbr();">Explore</a></span>  
       <span style="padding:3px;background:orange;border-radius: .25em;">
      <a alt='Join Bitmonky Page' style='color:white;' href="javascript:wzQuickReg();"><?php sayTxt(' Join Here ');?></a></span>
      </span>
      <?php
      if ($userID !=0) {
        ?>
        <span style="margin-right:1em;padding:3px;background:#81b861;border-radius: .25em;"><a style='color:white;'  
        href="javascript:popFindMbr();">Search</a>
        </span>
        <?php
      }
      ?>
      <span ID="wzWRTCctrl"></span><span ID='wzHeadNotify'></span><span ID='wzNotifyAlerts'></span>
  <?php
  if ($wikiMode){
    echo "<div style='height:50px;margin-left:35px;display:inline;border:2px solid white;border-radius:.25em;padding:3px;color:white;'>";
    echo "<a title='Turn Wiki Mode Off' href='javascript:setWikiMode(\"off\");'><img src='".$GLOBALS['MKYC_imgsrv']."/img/wikiMode.png' ";
    echo "style='background:gold;vertical-align:bottom;border-radius:50%;border:0px solid white;height:44px;width:44px;'/></a>";
    echo " Wiki Mode Is On";
    echo "</div>";
  }
  ?>
  </td>
  <td style='width:38%;text-align:right;padding-bottom:5px;padding-right:8px;'>
  <span id='wzSessionDisplay'></span>
  </td>
  </tr>
  </table>
</div>

<table ID='wzMainTableID' style='width:100%;min-height:2000px;border-collapse:collapse;margin:0px;'>
<tr valign="top">
<td ID='wzChatterCol' style="width:430px;min-height:vh;background:#444444;overflow:hidden;">
    <div ID='wzChatLoader' class='infoCardClear' style='min-height:300px;margin:2em 1em 1em 1em;'></div>
    <div ID='wzPopContainer' style='display:none;border:0px solid #c0c0c0;position:absolute;'></div>
    <div ID='wzPopNotifications' style='display:none;border:0px solid #e0e0e0;border-radius:0.5em;position:absolute;'></div>
    <div ID='userAccNotice'></div>
    <div style="padding:20px;" ID="wzStreamDisplay"></div>
</td>
<td ID="wzBrowser" style="min-height:vh;background:#444444;#111112;overflow:hidden;">

<!-- *** Main Page Tabs ****
-->
<div ID="wzTopControls" style="padding:0px;margin:0px 0px 12px 0px;" >
<span class='mpgTab' style='padding:2px;padding-top:0px;padding-left:3px;padding-right:3px;background:#333333;color:#888888;border-radius: .25em .25em 0em 0em;'>
<span ID='mboxpulldwn'>
<!--img style='height:1.0em;width:1.4em;' src='<?php echo $GLOBALS['MKYC_imgsrv'];?>/img/_videoIcon.png'/-->
<a onmouseout="mclosetime();" onmouseover="pullDwnMoshChannels();" href='javascript:pullDwnMoshChannels();' style='font-weight:bold;color:#dddddd;'/>MoshBox TV</a>
</span>
<a href="javascript:OpenGigBox(0);">[+]</a>
<a href='javascript:wzAPI_hideMosh()'>[-]</a>
<a href='javascript:wzAPI_closeMosh()'>[x]</a></span>
<span style='display:none;padding:1px;padding-top:0px;padding-left:3px;padding-right:3px;background:#eeeeee;border-radius: .5em;'>
<b>Browser:</b><a href="javascript:wzSurfTo(netViewURL);">[+]</a>
<a href='javascript:wzAPI_hideURL()'>[-]</a>
<a href='javascript:wzAPI_closeURL()'>[x]</a></span>
<span class='mpgTab' ID='vchatControl'></span>
<span class='mpgTab' ID='pvchatControl'></span>
<span class='mpgTab' ID='wzVidViewControl'></span>
<span class='mpgTab' ID='wzBitMineControl'></span>
<span ID='wzAlertControl'></span>
</div>
<div ID='wzPageLoader' class='infoCardClear' style='min-height:2000px;margin:1em 1em 1em 1em;'>
<div style='display:inline;height:28px;color:#777777;' ><div class='mkyloader'></div><span style=font-size:large;'>DigiHut Virtual Jungle Loading...</span></div>
<?php
if ($furl || $fnrl){
  echo "<h2 style='color:#777777;'>Locating Member Hut</h2>";
}
?>
<div ID='wzMainLoader'></div>
</div>

<!-- *** Page Frame Object Containers ***
 -->
<div ID='wzNetView' style='display:none;border:0px solid #777777;position:absolute;'></div>
<div ID='wzPageContainer' style='display:none;border:0px solid #777777;position:absolute;'></div>
<div ID='wzMoshContainer' style='display:none;border:0px solid #777777;position:absolute;background:none;'></div>
<div ID='wzPVCContainer' style='display:none;border:0px solid #777777;position:absolute;'></div>
<div ID='wzBitMineContainer' style='display:none;border:0px solid #777777;position:absolute;'></div>
<div ID='wzVidContainer' style='display:none;border:0px solid #777777;position:absolute;'></div>
<div ID='wzVidViewContainer' style='display:none;border:0px solid #777777;position:absolute;'></div>
		  </td>
		</tr>
	  </table>
	  <table style='width:100%;border-collapse:collapse;margin:0px;background:black;'>
        <tr>
          <td>
            <div ID='wzfooter' style='padding:8px;background:black;color:white;'>
            <b><span style='color:white'>
            <A  alt='Home' href="//<?php echo $whzdom;?>"><B style='color:lightGreen;'>Home</B></A> |
            <A  alt='FAQ'  href="javascript:wzGetPage('/whzon/mbr/blog/mbrMBLOG.php?wzID=0&fwzUserID=17621&fTopicID=1707&ftopicName=')">
            <B style='color:white;'>FAQ</B></A> |
            <A  alt='Bookmark Page' href="javascript:window.external.AddFavorite('//<?php echo $whzdom;?>','BitMonky.com')"><B style='color:white;'>
            <?php sayTxt('Add To Bookmarks');?></B></A>  
            </span></b>
            <p/>
            <span style="color:white">

            <STRONG>BitMonky <?php sayTxt('Online Services');?></STRONG> 
            <B style='color:white;'> - A Guerrilla Soft Creation</B> <BR/>
            <?php sayTxt('Please Read');?> | 
            <A alt='Link to site map' style='color:orange;' href="/sitemap.html"><?php sayTxt('Site Map');?></A> |
            <A alt='Link to xml site map' style='color:orange;' href="/sitemap.xml"><?php sayTxt('XML Map');?></A> |
	    <A alt='Terms of Service' style='color:orange;' 
            href="javascript:wzGetPage('/whzon/wzonTOS.php?wzID=<?php echo $sKey;?>');"><?php sayTxt('Terms Of Service');?></A> |
	    <A alt='Privacy Statement' style='color:orange;' 
            href="javascript:wzGetPage('/whzon/wzPrivacy.php?wzID=<?php echo $sKey;?>');"><?php sayTxt('Privacy Statement');?></A>
            <?php
            if ($userID == 17621){
              echo "<a href=\"javascript:wzGetPage('/wzAdmin/tempStateFix.php?wzID=".$sKey."&fwzUserID=17621');\">[test]</a>";
              echo "<a href='javascript:sayHelloPage();'>Boo</a>";
              echo "<a href=\"javascript:wzGetPage('/whzon/franMgr/recomendChannel.php?powChan=https://www.youtube.com/c/FREENVESTING');\">[test]</a>";
            }
            ?>
            </span>
	    </div>
          </td><td style='padding:1em;'>
            <?php  getSmallBannerAd('3px','isBadMonky');?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>     
<div class='wzAlert' id='wzAlertContainer'>	
  <div class='wzAlertMsg' id='wzAlertMsg'></div>
</div>	
<?php 
$SQL = "select mbrMUID from ICDirectSQL.tblwzOnline ";
$SQL .= "left join tblwzUserJoins on tblwzUserJoins.wzUserID = tblwzOnline.wzUserID "; 
$SQL .= "where NOT tblwzOnline.wzUserID = ".$userID." order by rand() limit 1 ";
$res = mkyMyqry($SQL);
$rec = mkyMyFetch($res);


$hutMUID = 'efc6d1ba446e229536dd1f7f51923d87';
if ($rec){
  $repMUID = '&rep='.$rec['mbrMUID'];
}
else {
  $repMUID = null;
}
$tmode = null;
if ($userID == 0){
  $tmode = '&tmode=virtualG';
}
if ($fnrl){
  if (strpos($fnrl,'mbrProfile.php') !== false){
    $hUID = strpos($fnrl,'fwzUserID');
    $hUID = right($fnrl,strlen($fnrl) - ($hUID + 10));
    $SQL = "select mbrMUID from tblwzUser where wzUserID = ".$hUID;
    $res = mkyMyqry($SQL);
    $rec = mkyMyFetch($res);
    if ($rec){
      $hutMUID = $rec['mbrMUID'];
      $repMUID = '&rep='.$rec['mbrMUID'];
    }  	  
  }
}
else {
  $repMUID = '&rep=dc14fd698646636277d080a6a8854e9a';
}
?>
<script async src="https://gsoft.bitmonky.com/whzon/adMgr/srvWebAssistantJS.php?rad=<?php echo $hutMUID.$repMUID.$tmode;?>&bg=dimGray&color=white&float=botRight&theme=dark"></script>
  <div ID="gsoftWebCtrl_<?php echo $hutMUID;?>"></div>
  <div ID="gsoftWebAssist_<?php echo $hutMUID;?>"></div>
</body>
</html>
