<!-- #INCLUDE FILE="wzSortOrder.inc" -->

   <p>
   <B><span class=wzBold>World News -  See Also</span></b> 
   | <a href='<?php echo changeModeTo('class');?>'>Classifieds</a>
   | <a href='<?php echo changeModeTo('web');?>'>Websites</a>
   <!--
   | <a href='<?php echo changeModeTo('event');?>'>Events</a>
   -->
   | <a href='<?php echo changeModeTo('mBlog');?>'>miniBLOGs</a>
   | <a href='<?php echo changeModeTo('mbrs');?>'>People</a>
   | <a href='<?php echo changeModeTo('photo');?>'>Photos</a>

   <p>
   <?php 
   if ($userID == 17621){
     echo "<a href='newsTaskEditor.php?wzID=".$sKey."&fmyMode=photo&fwzUserId=".$wzUserID."'>news tasks</a> ";
   }
   ?>

   <table style='width:96%'>
  <tr valign='top'>
  
    <td style='padding-right:40px;'>
<?php 

$link = $linkRoot;
$i = 0;
$nRows = 14;
 ?>
<form style='padding-top:20px;padding-bottom:20px;' method='GET' action='https://bitmonky.com/whozon/qryWSNews.asp'>

<b>Search News For</b> <input name='fqry' value='<?php if (isset($_GET['fqry'])){echo clean($_GET['fqry']);} ?>'> <input type='submit' value='Search'>

<?php 
if (isset($_GET['newPg'])){$inPg = safeGET('newPg');} else {$inPg = 1;}

if ( $inPg == 0 ) {
  $inPg = 1;
}

$nextPage = $inPg + 1;
 	
$nRows = 20;

$TopPgs =  " limit ".($inPg * $nRows);

$SQL = "SELECT  newsDirectory.tblWSNewsMap.link, newsDirectory.tblWSNewsMap.websiteID, newsLinksID,imgFlg,title,description FROM newsDirectory.tblWSNewsLinks Inner join newsDirectory.tblWSNewsMap on ";
$SQL .= " newsDirectory.tblWSNewsMap.newsMapID=tblWSNewsLinks.newsMapID ";
$SQL .= " where not pass2 is null and failed is null order by pDate desc ".$TopPgs;  

$myresult = mkyMyqry($SQL);
if ($myresult){$tRec = mkyMyFetch($myresult);} else { $tRec=null;}

$skip = 0;
while($tRec && $skip < ($inPg * $nRows - $nRows)) {
  $tRec = mkyMyFetch($myresult);
  $skip = $skip + 1;
}

$appName = "myTown.php";
$link = "?wzID=".$sKey."&myMode=wNews&fwzUserID=".$wzUserID;
$i = 0;
$link = "?fwzUserID=".$wzUserID."&fcatID=".$catID;

echo "<span style='margin-left:8px;padding:3px;background:#eeeeee;border-radius: .5em;'><a href='".$appName.$link."&newPg=1'>Refresh News</a></span></form><p/>";

While ($tRec && $i < $nRows){

  $SQL = "select URL from tblWebsites where websiteID=".$tRec['websiteID'];
  $mRec = null;
  $mresult = mkyMsqry($SQL);
  $mRec = mkyMsFetch($mresult);

  $url = $mRec['URL'];
  $SQL = "SELECT width,height from ICDimages.tblwsNewsImg where newsLinkID = ".$tRec['newsLinksID'];
  $mmyresult = mkyMyqry($SQL) or die($SQL);
  if ($mmyresult){$mtRec = mkyMyFetch($mmyresult);} else { $mtRec=null;}
  $iwidth = null;
  if ($mtRec){
    $iwidth = 200; //$mtRec['width'];
	$iheight = 130; //$mtRec['height'];
	$margin  = "margin: 12px 20px 12px 0px;";
  }
  if (!$iwidth){
    $iwidth = 100;
	$iheight = 65;
	$margin  = "margin: 3px 8px 3px 0px;";
  }	


  $newsImgStr = "";
  if ($tRec['imgFlg']) {
    $wsAnkor = "<a href='/whzon/mbr/mbrReadWNews.php?wzID=".$sKey."&newsID=".$tRec['newsLinksID']."'>";
    $newsImgStr = $wsAnkor."<img style='width:".$iwidth."px;height:".$iheight."px;float:left;".$margin." vertical-align: top;border-radius: .5em;border: 0px solid #74a02a;' src='https://image.bitmonky.com/getWsNewsImg.php?id=".$tRec['newsLinksID']."'></a>";
  }
           ?>
	<table  style="width:65%;background:#fafafa;margin-bottom:20px;border:0px solid #eeeeee;">
	   <tr valign='top'>
		 <td style='padding:8px;'>
		   <div style='width:100%;'>   
             <a href='/whzon/mbr/mbrReadWNews.php?wzID=<?php echo $sKey."&newsID=".$tRec['newsLinksID'];?>'><?php echo $tRec['title'] ?></a><br/>
           </div>
		   <div style='width:100%;'>   
             <?php echo $newsImgStr ?><p style='<?php echo $margin;?>'><?php echo $tRec['description'] ?></p>
		     <p>
             <b>Source: </b><a href='/whzon/mbr/viewWebsite.php?fwebsiteID=<?php echo $tRec['websiteID'] ?>'><?php echo $url ?></a> 
           </div>
		   <?php if ($userID != 0){?>	 
		   <br clear='left'/>
		   <a href='/whzon/mbr/mbrViewWNewsShare.php?wzID=<?php echo $sKey;?>&newsID=<?php echo $tRec['newsLinksID'];?>'>Share</a>
		   <a href='/whzon/mbr/mbrViewWNewsShare.php?wzID=<?php echo $sKey;?>&newsID=<?php echo $tRec['newsLinksID'];?>'>Like</a>
		   <?php }?>
		 </td>
	 </tr>
   </table> 		
<?php 
  $i = $i + 1;
  $tRec = mkyMyFetch($myresult);
}

if ($nextPage != $inPg && $i > 0) {
  echo "<p><a href='".$appName.$link."&newPg=".($nextPage)."'>Next</a> | ";
}

if ($nextPage > 1) {
  echo "<a href='".$appName.$link."&newPg=".($nextPage - 2)."'>Back</a> | ";
}
echo "<a href='".$appName.$link."&newPg=1'>Top</a>";
?>
</td></tr></table>

