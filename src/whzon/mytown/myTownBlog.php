<?php
include_once('newsHLObjs.php');
$fsq = safeGET('fsq');
$rdot = null;
if ($fsq){
  $rdot = ',R.nRes';
}
$mkey = new mkySess($sKey,$userID);
$webItem = new mkyActivityCard($mkey,$cityID,$scope,'mBlog','Member Blogs',$fsq);
$fsqry = null;
$fsqry = $webItem->getQry();
$fsqry = mkyStrReplace('tblMBlogEntry.title','tblObjPreIndex.objpWord',fxnToStr($fsqry));
$spin = safeGET('spin');
?>
<p>
<div class='infoCardClear'>
<B><span class='wzBold' style='color:darkKhaki;'>Blogs Scope - <?php echo $scopeDisplay ?></span>  See Also </b>  
<?php drawMyMenu($myMode,$modes);?>
<div style='margin-top:.5em;'>
<form method='GET' action=''>
<input type='hidden' name='wzID'      value='<?php echo $sKey;?>'/>
<input ID='accQryStr' class='srchBox' type='text'  onkeydown="return (event.keyCode!=13);"
   name='fsq'   onfocus='this.value=""'  value='<?php echo $fsq;?>'
   placeholder='Member Blogs' style='font-size:larger;font-weight:bold;width:55%;'/>
<input type='hidden' name='catID'     value='<?php echo $catID;?>'/>
<input type='hidden' name='fscope'    value='<?php echo $scope;?>'/>
<input type='hidden' name='franCID'   value='<?php echo $cityID;?>'/>
<input type='hidden' name='fmyMode'   value='mBlog'/>
<input type='hidden' name='fwzUserID' value='<?php echo $userID;?>'/>
<input class='srchButton' type='button' onclick='doAccQry(0,true,"mBlog")' style='padding:.65em;vertical-align:top;' value=' Search '/>
<input class='srchButton' type='submit' style='padding:.65em;vertical-align:top;' name='spin' value=' Spin '/>
</form>
</div>
</div>
<p>
<div ID='accQrySpot'></div>

<table width='95%' class='myTown'>
<tr valign='top'>
<td>
<?php 
if (isset($_GET['fcatID'])){$catID = clean($_GET['fcatID']);} else {$catID = "";}
  if ( $catID != "" ) {
    $catStr = " and oldcategoryID=".$catID." ";
  }
  if ( $myMetroID == 0 ) {
    $SQL = "SELECT firstname,date(tblmBlogTopic.rDate) frDate, tblmBlogTopic.mBlogTopicID,tblmBlogTopic.wzUserID,";
    $SQL .= "details,tblmBlogTopic.name From tblmBlogTopic  ";
    $SQL .= "inner join tblMBlogEntry  on tblmBlogTopic.mBlogTopicID = tblMBlogEntry.mBlogTopicID ";
    if ($fsq){
      $SQL .= "inner join ( select count(*)nRes, objpItemID ";
      $SQL .= "from tblObjPreIndex  ";
      $SQL .= "inner join tblpreIndexCWords  on prcwWord = objpWord ";
      $SQL .= "where tblObjPreIndex.objpName='mBlog' ".$fsqry;
      $SQL .= "group by  objpItemID ";
      $SQL .= ")R on R.objpItemID = tblMBlogEntry.mBlogEntryID ";
    }
    $SQL .= "INNER JOIN   tblwzUser  ON tblmBlogTopic.wzUserID = tblwzUser.wzUserID ";
    $SQL .= "where ".$userSearch." and mbStatus is null and sandBox is null and privacy is null and adultContent<>1 and spamFlg<>1 ";
    $SQL .= "group by tblmBlogTopic.mBlogTopicID,tblmBlogTopic.wzUserID".$rdot.",details,tblmBlogTopic.name,tblmBlogTopic.rDate,udate ";
    $SQL .= "order by ";
    if ($fsq){
      $SQL .= "R.nRes desc,uDate desc ";
    }
    else {
      $SQL  .= "uDate desc";
    }
  } 
  else {
    $SQL = "SELECT firstname,date(tblmBlogTopic.rDate) frDate, tblmBlogTopic.mBlogTopicID,tblmBlogTopic.wzUserID,";
    $SQL .= "details,tblmBlogTopic.name From tblmBlogTopic  ";
    $SQL .= "INNER JOIN  tblwzUser  ON tblmBlogTopic.wzUserID = tblwzUser.wzUserID ";
    $SQL .= "inner join tblMBlogEntry  on tblmBlogTopic.mBlogTopicID = tblMBlogEntry.mBlogTopicID ";
    if ($fsq){
      $SQL .= "inner join ( select count(*)nRes, objpItemID ";
      $SQL .= "from tblObjPreIndex  ";
      $SQL .= "inner join tblpreIndexCWords  on prcwWord = objpWord ";
      $SQL .= "where tblObjPreIndex.objpName='mBlog' ".$fsqry;
      $SQL .= "group by  objpItemID ";
      $SQL .= ")R on R.objpItemID = tblMBlogEntry.mBlogEntryID ";
    }
    $SQL .= "inner join tblCity  on tblCity.cityID=tblwzUser.cityID ";
    $SQL .= "where ".$userSearch." and mbStatus is null and sandBox is null and privacy is null and adultContent<>1 and spamFlg<>1  ";
    $SQL .= "group by tblmBlogTopic.mBlogTopicID,tblmBlogTopic.wzUserID".$rdot.",details,tblmBlogTopic.name,tblmBlogTopic.rDate,udate ";
    $SQL .= "order by ";
    if ($fsq){
      $SQL .= "R.nRes desc,uDate desc ";
    }
    else {
      $SQL  .= "uDate desc";
    }
  }
  if ($spin){
    $SQL = mkyStrIReplace('order by ','order by rand(), ',$SQL);
  }
  $tRec = null;
  $result = mkyMsqry($SQL) or die($SQL);
  $tRec = mkyMsFetch($result);

  if (isset($_GET['newPg'])){$pg = safeGET('newPg');} else {$pg = "";}
    if ($pg == ""){$pg = 0;}
    $nextPage = $pg ;
    $n = $pg + 1;
    $cpage = 0;
    while($tRec && $cpage < $nextPage) {
      $tRec = mkyMsFetch($result);
      $cpage = $cpage + 1;
    }
    ?>

    <p>
    <table class='myTown' width='100%' >
    <tr valign=top>
    <td style="background-image: url('');" ><font color='white'><b>Owner</b></font></td>
    <td style="background-image: url('');" ><font color='white'><b>Blog Topic</b></font></td>
    </tr>
    <?php 

    $i = 0;
    $nRows = 10;
    $link = $linkRoot."&fcatID=".$catID."&fsq=".mkyUrlEncode(fxnToStr($fsq));
    $appName = "myTown.php";
    if ($digID){
      showDigListing($digID);
    }
    While ($tRec && $i < $nRows) {

      $articleID = $tRec['mBlogTopicID'];
      $cUserID   = $tRec['wzUserID'];
      $story     = "";
      $img       = "";
      $story     = left($tRec['details'],80)."...";
 
      $readerApp = "/whzon/mbr/blog/mbrMBLOG.php?wzID=".$sKey."&fwzUserID=".$cUserID."&fTopicID=".$articleID;
      $newsLink  = "<a href='javascript:wzLink(\"".$readerApp."\");'>";

      ?>

      <tr  valign='top'>
      <td>
      <?php echo $newsLink ?><span>
      <img title="View <?php echo $tRec['firstname'] ?>'s profile" style='border-radius:0.5em;margin-bottom:25px;
      border-radius:50%;width:45px;height:55px;margin:1em;' src='https://image.bitmonky.com/getMbrImg.php?id=<?php echo $tRec['wzUserID'] ?>'>
      </span></a></td>
      <td><b style='font-size:larger;'><?php echo $tRec['name'] ?></b><p/> 
      <?php 
      $SQL = "Select title, entry, imgFlg, mBlogEntryID from tblMBlogEntry  ";
      $SQL .= "where mBlogTopicID=".$articleID." order by rDate desc limit 1";

      $nresult = mkyMsqry($SQL) or die($SQL);
      $nRec = mkyMsFetch($nresult);
      $postID = null;
      if ($nRec) {
        $eImg = "";
        $postID = $nRec['mBlogEntryID'];
        if ( $nRec['imgFlg'] == 1 ) {
          $eImg = "<img style='float:right;margin: 0px 0px 12px 18px;border-radius:0.5em;' 
          src='https://image.bitmonky.com/getmBlogTmn.php?id=".$nRec['mBlogEntryID']."'>";
        }
        $readerApp = "/whzon/mbr/blog/mbrMBLOG.php?wzID=".$sKey."&fwzUserID=".$cUserID."&fTopicID=".$articleID."&postID=".$postID."#mb".$postID;
        $newsLink  = "<a href='javascript:wzLink(\"".$readerApp."\");'>";
        ?>
      
        <?php echo $newsLink.$eImg ?></a><b><?php echo $nRec['title'] ?></b><br/>
        <?php echo splitLWords(left($nRec['entry'],200)); ?>... <?php echo $newsLink ?>Read mBlog</a>
      
        <?php 
      }
      ?>
      <div align='right' style='display:none;font-size:smaller;'><?php echo $tRec['frDate'] ?></div>
      </td>
      </tr>
      <?php   
      $i = $i +1;
      $n = $n +1;
      $tRec = mkyMsFetch($result);
    }
    
    echo "</table>";

    echo "<div style='margin-top:2em;'>";
    if ($i > 0){
      echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage + $nRows)."\");'>Next</a>";
    }
    if ($nextPage > 0) {
      echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=".($nextPage - $nRows)."\");'>Back</a>";
    }
    echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&newPg=0\");'>Top</a>";
    echo "<a class='scrollBut' href='javascript:wzLink(\"".$appName.$link."&spin=spin&newPg=0\");'>Spin Again</a>";
    echo "</div>";
    
  
  if (!$sessISMOBILE){
    echo "</td><td style='padding:15px;'>";
  }
  else {
    echo "<p/>";
  }
  getBigCubeAds('0px',2);
  ?>
  </td>
  </tr>
  </table> 
  <?php

function showDigListing($digID){
    global $sKey;
    global $userID;
    global $whzdom;
    global $i,$n;

    $SQL = "SELECT firstname,date(tblmBlogTopic.rDate) frDate, tblmBlogTopic.mBlogTopicID,tblmBlogTopic.wzUserID,";
    $SQL .= "details,tblmBlogTopic.name From tblmBlogTopic  ";
    $SQL .= "INNER JOIN   tblwzUserJoins  ON tblmBlogTopic.wzUserID = tblwzUserJoins.wzUserID ";
    $SQL .= "inner join tblMBlogEntry  on tblmBlogTopic.mBlogTopicID = tblMBlogEntry.mBlogTopicID ";
    $SQL .= "where mBlogEntryID =".$digID." and mbStatus is null and sandBox is null and privacy is null and adultContent<>1 and spamFlg<>1 "; 

    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);
    if (!$tRec){
      return;
    }
    $articleID = $tRec['mBlogTopicID'];
    $cUserID   = $tRec['wzUserID'];
    $story     = "";
    $img       = "";
    $story     = left($tRec['details'],80)."...";

    $readerApp = "/whzon/mbr/blog/mbrMBLOG.php?wzID=".$sKey."&fwzUserID=".$cUserID."&fTopicID=".$articleID;
    $newsLink  = "<a href='javascript:wzLink(\"".$readerApp."\");'>";

    ?>

    <tr  valign='top'>
    <td>
    <?php echo $newsLink ?><span>
    <img title="View <?php echo $tRec['firstname'] ?>'s profile" style='border-radius:0.5em;margin-bottom:25px;
    border-radius:50%;width:45px;height:55px;margin:1em;' src='https://image.bitmonky.com/getMbrImg.php?id=<?php echo $tRec['wzUserID'] ?>'>
    </span></a></td>
    <td><b style='font-size:larger;'><?php echo $tRec['name'] ?></b><p/>
    <?php
    $SQL = "Select title, entry, imgFlg, mBlogEntryID from tblMBlogEntry  ";
    $SQL .= " where mBlogEntryID=".$digID." limit 1";

    $nresult = mkyMsqry($SQL) or die($SQL);
    $nRec = mkyMsFetch($nresult);
    $postID = null;
    if ($nRec) {
      $eImg = "";
      $postID = $nRec['mBlogEntryID']."sdfd";
      if ( $nRec['imgFlg'] == 1 ) {
        $eImg = "<img style='float:right;margin: 0px 0px 12px 18px;border-radius:0.5em;'
        src='https://image.bitmonky.com/getmBlogTmn.php?id=".$nRec['mBlogEntryID']."'>";
      }
      $readerApp = "/whzon/mbr/blog/mbrMBLOG.php?wzID=".$sKey."&fwzUserID=".$cUserID."&fTopicID=".$articleID."&postID=".$postID."#mb".$postID;
      $newsLink  = "<a href='javascript:wzLink(\"".$readerApp."\");'>";

      ?>

      <?php echo $newsLink.$eImg ?></a><b><?php echo $nRec['title'] ?></b><br/>
      <?php echo mkyStrReplace('/',' ',left($nRec['entry'],200)); ?>... <?php echo $newsLink ?>Read mBlog</a>

      <?php
    }
    ?>
    <div align='right' style='display:none;font-size:smaller;'><?php echo $tRec['frDate'] ?></div>
    </td>
    </tr>
    <?php
    $i = $i +1;
    $n = $n +1;
}
?>
