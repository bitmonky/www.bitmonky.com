<?php
$title="";
$mKeywords="";
$mDesc="";

$profileText = mkyStrReplace("[","<b>",$profileText);
$profileText = mkyStrReplace("]","</b>",$profileText);
$profileText = mkyStrReplace("\n","<p>",$profileText);

if ($thisIsME){$facMode = "friends";}
if (isset($_GET['facMode'])) {$facMode = clean($_GET['facMode']);}  

?>
    <div style='margin-top:12px;'>
    <a href = '/whzon/mbr/mbrProfile.php?wzID=<?php echo $sKey.'&fwzUserID='.$wzUserID;?>'>
    <img alt='<?php echo $primContact;?>' style='float:left;width:80px;height:100px;margin:3px;margin-right:15px;border-radius:50%;'
     src='//image.bitmonky.com/getMbrImg.php?id=<?php echo $wzUserID;?>'/></a>

    <b style='font-size:larger;'>
    <?php 
    $isFranOwner = getFranchiseID($ccityID);
    if ($isFranOwner){
      echo "<a style='font-size:larger;' title='Read how to become a bitmonky.com Media Tycoon' href='/whzon/franMgr/franMgr.php?mode=reRead&wzID=".$sKey."'>
      Media Tycoon</a>
      <img src='//image.bitmonky.com/img/tycoonIcon.webp' height='28' width='23' style='border-radius:50%;vertical-align:middle; margin:0px;border:0px;' ><br>";
    } 
    echo $primContact."</b>";
    $tok = getfranTokenInfo($isFranOwner);
    if ($tok){
      echo "<br clear='left'>";
      echo "<div class='infoCardClear' style='margin-top:1.5em;background:#222222;'>";
      echo "<a target='MINTME' href='https://www.mintme.com/token/".$tok->symbol."/invite'>";
      echo "<img style='float:right;height:3.5em;border-radius:50%;' src='".$GLOBALS['MKYC_imgsrv'].$tok->img."'/></a>";
      echo "<h2>City Token: <span style='font-size:bigger;color:darkKhaki;font-weight:bold;'>".$tok->symbol."</span></h2>";
   //   echo "<p/>";
      echo "<br/>Treasury Account:  <span style='color:darkKhaki;font-size:smaller'>".$tok->addr."</span>";
      echo "<br/>Gas Available: <span style='color:darkKhaki;'>".mkyNumFormat($tok->gas,6)." </span>";
      echo "<br/>Supply:  <span style='color:darkKhaki;'>".mkyNumFormat($tok->balance,6)." ".$tok->symbol."</span>";
      echo "<br/>Price:   <span style='color:darkKhaki;'>".mkyNumFormat($tok->spotPrice,5)." MINTME</span>";
      echo "<br/>Average: <span style='color:darkKhaki;'>".mkyNumFormat($tok->avgPrice,5)." MINTME</span>";
      echo "<div align='right' style='font-size:smaller'>Earn <a target='MINTME' href='https://www.mintme.com/token/".$tok->symbol."/invite'>".$tok->symbol."'s</a> for doing work in this City.</div>";
      echo "<br/><input type='button'  value=' ".$tok->symbol." Market Place ' onclick='wzLink(\"https://www.bitmonky.com/whzon/gold/mrkViewGJEX.php?wzID=".$sKey."&mrkCut=doge&mrkTok=".$tok->symbol."\");'/>";
      echo "</div><p/>";
    }
    ?>
    
    <p/>
    I am <?php if ($sex != "" ){?>, <?php echo $sex;?> <?php echo $age;?><?php }?> 
    From <a href="/whzon/mytown/myTownProfile.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $wzUserID;?>&fscope=myCity&fmyMode=mbrs"><?php echo $hCity;?></a>,
    <a href="/whzon/mytown/myTownProfile.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $wzUserID;?>&fscope=myCountry&fmyMode=mbrs"><?php echo $hCountry;?></a>
    <?php 
	if ($profileText != "" ){
	   echo "<p/>".$primContact." Says,<p/>".$profileText;
	}else{
      echo "<p>This member has not yet loaded their welcome message.";
    }
	?>
    </span> 
   <br clear='left'/>

   <div style='text-align:right;'>
   <?php if ( $userID != 0 ){?>
     <?php if (!$thisIsME){?>
       <?php if ( $online == 1 ){?>
         <b>online</b> <a href=javascript:parent.wzPopChat(<?php echo $wzUserID;?>); title='Click To Start Live Chat' alt='Click To Start Live Chat'>
         <img title="Start Private Chat" style="border: 0px none; margin-left: 3px;vertical-align:middle;" src="//image.bitmonky.com/img/chatBubIcon.png">Start Private Chat</a>
       <?php }else{?>
         
         <a style='' href='javascript:gotoMiniBLOG();'>Read My miniBlog</a>
        <?php }?>
     <?php }?>
   <?php } else {?>
     <a style='' href="javascript:parent.wzQuickReg();" >Email Me</a> | 
     <a style='' href='javascript:gotoMiniBLOG();'>Read My miniBlog</a>
   <?php }?>
   <span ID='lowerCtrls'>
	<?php if ($userID != 0){?>
      <?php if ($iAmBLOCKED != true ) {?>
        | <a href="javascript:parent.wzAPI_showFrame('/whzon/mbr/blockUserFrm.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $wzUserID;?>',350,250,500,200);">Block</a>
      <?php }else{?>
        <a href="javascript:parent.wzAPI_showFrame('/whzon/mbr/blockUndoFrm.php?wzID=<?php echo $sKey;?>&fwzUserID=<?php echo $wzUserID;?>',300,180,500,200);">Unblock</a>
      <?php }?>
 
      <?php if ( $userID != $wzUserID ){
        if(!$weAreFriends){?>
          <a href="javascript:parent.wzAPI_showFrame('/whzon/mblp/mbr/popFriendInvite.php?wzID=<?php echo $sKey;?>&fmbrID=<?php echo $wzUserID;?>',parent.pgViewXW,280,parent.margn,120);">
		  <img title="Send Friends Request" style="vertical-align:middle;border: 0px none;" src="//image.bitmonky.com/img/friendsIcon.png">Friends Request</a>
		<?php } else {?>
		  <img title="We Are Friends" style="vertical-align:middle;border: 0px none;" src="//image.bitmonky.com/img/friendsIcon.png"> <b>We Are Friends</b>
        <?php }?>
      <?php }?>
    <?php }?>
    </span>
    </div>   

<?php
    // *********************
    // List Member Stores
    // *********************
    
    $SQL = "SELECT storeID,storeDesc,storeTitle From tblStore where not approved is null and storeUID=".$wzUserID." limit 3";  
    $tRec = null;
    $result = mkyMsqry($SQL) or die($SQL);
    $tRec = mkyMsFetch($result);

    if ($tRec){
      echo "<h2>Shop At ".$primContact."'s Online Store</h2>"; 
      echo "<p><table>";

      $n = 1;

      while ($tRec){

        $storeAnkor = "<a href='/whzon/store/storeProfile.php?wzID=".$sKey."&fstoreID=".$tRec['storeID']."'>"; 

        $storeImgStr = $storeAnkor."<img style='margin-bottom:3px;margin-top:3px;margin-right:8px;border-radius: .5em;width:100px;height:65px;' ";
        $storeImgStr .= "src='//image.bitmonky.com/getMbrStoreImg.php?id=".$tRec['storeID']."'>";
        ?>
        <tr valign='top'>
        <td style='padding-bottom:12px; padding-right:12px;'><?php echo $storeAnkor?><?php echo $storeImgStr?></a></td>
        <td colspan='2'style='padding-bottom:12px; padding-right:12px;'>
        <span class='wzInstruction'><?php echo $storeAnkor?><?php echo $tRec['storeTitle'];?></a></span><br>
        <font color='#aaaaaa'><?php echo $tRec['storeDesc'];?>...</font><br clear='left'>
        </td>
        </tr>
        <?php
        $n = $n + 1;
        $tRec = mkyMsFetch($result);
      }
      echo "</table>";
    }
    
    // *********************
    // List Member classifieds
    // *********************
    
    $SQL  = "SELECT adID, shortDesc, item  From tblClassifieds ";
    $SQL .= "where (itemStoreID is null or itemStoreID=0) and imgFlg=1 and wzUserID=".$wzUserID." and postStatus is null ";  
    $SQL .= "order by adID desc limit 3";
    
    $tRec = null;
    $result = mkyMsqry($SQL) or die($SQL);
    $tRec = mkyMsFetch($result);

    if ($tRec){
      
      echo "<p ID='forSale'>";
      echo "<h2>".$primContact."'s Stuff For Sale</h2>"; 
      echo "<p><table>";

      $n = 1;

      while ($tRec){
        $adID = $tRec['adID'];
        $sAnkor   = "<a href='/whzon/mbr/mbrViewClassified.php?wzID=".$sKey."&franCID=&fwzUserID=".$wzUserID."&itemID=".$adID."'>";
        $wsImgStr = "<img style='margin-bottom:3px;margin-top:3px;margin-right:8px;border-radius: .5em;width:100px;height:65px;' src='//image.bitmonky.com/getClassTmn.php?id=".$adID."'/>";
?>
        <tr valign='top'>
        <td style='padding-bottom:12px; padding-right:12px;'><?php echo $sAnkor?><?php echo $wsImgStr?></a></td>
        <td colspan='2'style='padding-bottom:12px; padding-right:12px;'>
        <b><?php echo $tRec['item'];?></b><br/>
        <?php echo $tRec['shortDesc'];?>... 
        <a href='/whzon/mbr/mbrViewClassified.php?wzID=<?php echo $sKey;?>&franCID=&fwzUserID=<?php echo $wzUserID;?>&itemID=<?php echo $adID;?>'>read more</a>
        </td>
        </tr>
<?php
        $n = $n + 1;
        $tRec = mkyMsFetch($result);
      }
      echo "</table>";
    }
    
    // *********************
    // List Member websites
    // *********************
    
    $SQL = "SELECT mWebFlg as miniWeb,websiteID,wsImgFlg,Description,URL, Title  From tblWebsites where (oldcategoryID <> 2192 and reviewed > 1 or ditched=0) and doNotGroupWs=0 AND wzUserID=".$wzUserID;  
    $SQL .= " limit 3";
    $tRec = null;
    $result = mkyMsqry($SQL) or die($SQL);
    $tRec = mkyMsFetch($result);

    if ($tRec){
      echo "<p ID='faveSites'>";
      if ($wzUserID != 63555){
        echo "<h2>Websites Hosted By ".$primContact."</h2>";
      }  
      else {
        echo "<h2>A Few Of ".$primContact."'s Favorite Websites</h2>"; 
      }
      echo "<p><table>";

      $n = 1;

      while ($tRec){

        if ($tRec['miniWeb'] == 0){
          $sAnkor = "<a href='/whzon/mbr/viewWebsite.php?fwebsiteID=".$tRec['websiteID']."'>"; 

          $wsImgStr = "";
          if ($tRec['wsImgFlg'] == 1){
            $wsAnkor = "";
            $wsImgStr = $wsAnkor."<img style='margin-bottom:3px;margin-top:3px;margin-right:8px;border-radius: .5em;width:100px;height:65px;' ";
            $wsImgStr .= "src='//image.bitmonky.com/getWsImg.php?id=".$tRec['websiteID']."'>";
          }
          ?>
          <tr valign='top'>
          <td style='padding-bottom:12px; padding-right:12px;'><?php echo $sAnkor?><?php echo $wsImgStr?></a></td>
          <td colspan='2'style='padding-bottom:12px; padding-right:12px;'>
          <span class='wzInstruction'><?php echo $tRec['Title'];?></span><br>
          <font color='#aaaaaa'><?php echo $tRec['Description'];?>...</font><br clear='left'>
          <a href='/whzon/mbr/viewWebsite.php?wzID=<?php echo $sKey;?>&fwebsiteID=<?php echo $tRec['websiteID'];?>'>Website</a>
          </td>
          </tr>
          <?php
          $n = $n + 1;
        }
        $tRec = mkyMsFetch($result);
      }
      echo "</table>";
    }
    
    showEmployees($ccityID);

    // *******************
    // List Member Fans
    // *******************

    $SQL = "SELECT count(*) as nRec from tblwzUser where sandBox is null and cityID=".$cityID." and not qualityProvider is null";
    $tRec = null;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    $nFans = $tRec['nRec'];

    $SQL = "SELECT wzUserID from tblwzUser where sandBox is null and cityID=".$cityID." and not qualityProvider is null limit 40";
    $tRec = null;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    if ($tRec){
      echo "<h3>Quality Providers Working For ".$primContact." - ".$nFans."</h3>";

      while ($tRec){
        $wsImgStr = "";
        $wsAnkor  = " <a  href='/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$tRec['wzUserID']."'>";
        $wsImgStr = $wsAnkor."<img style='margin: 0px;' src='//image.bitmonky.com/getMbrTmn.php?id=".$tRec['wzUserID']."'></a>";
        echo $wsImgStr;
        $tRec = mkyMsFetch($result);
      }
      if ($thisIsME) {
        //echo "<br/><a href='/whzon/mbr/mail/mailSetting.php?mode=mfan&wzID=".$sKey."'>Remove or Block A Fan</a>";
      }
    }
?>
  </div>

