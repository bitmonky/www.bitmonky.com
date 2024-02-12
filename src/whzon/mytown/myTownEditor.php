<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
include_once("../mbr/mbrData.php");
include_once("../gold/goldInc.php");

//if ($userID == 17621){
//  $userID = 449233;
//  echo "swap".$userID;
//}
  

include_once("myTownInc.php");
if (isset($_GET['mode'])){$editMode = true;} else {$editMode = false;}

$SQL = "Select count(*) as nrec from ICDimages.tblStoreBanner where fieldname = fieldValue";
scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID);
$mRec = null;
$myresult = mkyMyqry($SQL);
if ($myresult){$mRec = mkyMyFetch($myresult);}
$nBanners = $mRec['nrec'];
$maxBanners = 1;
$nCredits = -1;
$bannerID = 0;
$task = "Load Banner For -  ".$scopeDisplay." ";
if ($scope == "myCity"){
  $task .= $myCityname.", ".$myState.", ".$myCountry;
}  
else if ($scope  == "myState"){
  $task .= $myState.", ".$myCountry;
}
else if ($scope == "myCountry"){
  $task .= $myCountry;
}

$title="";
$mKeywords="";
$mDesc="";

$mRec = null;
$bgImgID = 0;

$dbCon=openDSimgDB();    
$dbMyImg = selectImgDB("ICDimages",$dbCon);

$pWidth  = null;
$pHeight = null;
$pxOff   = 0;
$pyOff   = 0;
$pyOFF   = null;
$pxOFF   = null;
$facMode = null;
$ptMd5ID = "";

$mRec = null;
$bgMode = 'null';
$bgHeight = 0;
$bgMd5 = "";

$SQL = "select bannerID,bannerUID, height,cropMd5ID from ICDimages.tblStoreBanner ";
$SQL .= "where useSysBanID is null and uploadStatus is null and fieldname=fieldValue order by bDate desc";
scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID);
$myresult = mkyMyqry($SQL);
if ($myresult){$mRec = mkyMyFetch($myresult);}
$bannerUID = null;
if ($mRec){
  $bannerID = $mRec['bannerID'];
  $bannerUID = $mRec['bannerUID'];
  $bgImg = "/getBGImg.php?id=".$bannerID;
  $bgOffset = 0;
  $bgImgID = $mRec['bannerID'];
  $bgMode = "'edit'";
  $bgHeight = $mRec['height'];
  $bgMd5 = "#that".$mRec['cropMd5ID'];
  $bgImg .= $bgMd5;  
  if (!$bgHeight){$bgHeight = 0;} else {$bgHeight = $bgHeight - 220;}
  
}
else{
  $mRec = null;
  $SQL = "select bannerID,bannerUID, useSysBanID,yoffset,height,cropMd5ID from ICDimages.tblStoreBanner where udefault = 1 and fieldname=fieldValue";
  scopeSQL($SQL,$cityID,$stateID,$countryID,$metroID);
  $myresult = mkyMyqry($SQL);
  if ($myresult){$mRec = mkyMyFetch($myresult);}

  if($mRec){
    // Check copy right credits have been created.
    $bannerID  = $mRec['bannerID'];
    $bannerUID = $mRec['bannerUID'];
      
    $SQL = "select count(*) as nRec from tblCopyRightCredits where NOT cprTaskStatus is null and cprAcCode=20 and cprAcItemID=".$bannerID;
    $result = mkyMsqry($SQL) or die($SQL);
    $cRec = null;
    $resultCR = mkyMsqry($SQL);
    $cRec = mkyMsFetch($resultCR);
    $nCredits = $cRec['nRec'];
	
    if ($mRec['useSysBanID']){
      $SQL = "select bgName,yOffset from tblDefBackground where bgID = ".$mRec['useSysBanID'];
      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      $bgImg    = 'https://image.bitmonky.com/img/'.$tRec['bgName'];
      $bgOffset = $tRec['yOffset'];
    }
    else {
      $bgImg    = "/getBGImg.php?id=".$mRec['bannerID'];
      $bgMd5 = null; //"".$mRec['cropMd5ID'];
      $bgImg .= $bgMd5;  
      $bgMode = "'edit'";
      $bgOffset = -1 * $mRec['yoffset'];
      $bgHeight = $mRec['height'];
      if (!$bgHeight){$bgHeight = 0;} else {$bgHeight = $bgHeight - 220;}
      $bgImgID  = $mRec['bannerID'];
    }
  }
  else {
    $SQL = "select count(*) as nRec from tblDefBackground";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $nbg = $tRec['nRec'];
    $pick = rand(1,$nbg);

    $SQL = "select bgID,bgName,yOffset from tblDefBackground";
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    $n = 1;
    while ($n < $pick){
      $tRec = mkyMsFetch($result);
      $n = $n + 1;
    }
    $bgImg    = 'https://image.bitmonky.com/img/'.$tRec['bgName'];
    $bgOffset = $tRec['yOffset'];
  }
}  


function shoutPick(){

   echo "<select name='fgroup'><option value='0'>All My Friends";

   $SQL = "SELECT * FROM tblDefFriendGroups ";
   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);
   while ($tRec){
     echo "<option value='".$tRec['fgTypeID']."'>".$tRec['fgname'];
     $tRec = mkyMsFetch($result);
   }
   echo "</select>";
}

include_once("myTownTemplate.php");
?>
<style>
BODY {
font-family:MS Sans Serif, Geneva, sans-serif;
}
</style>
<div
onmouseover="showBannerControls();" onmouseout="bannerTimer = setTimeout('hideBannerControls()',500);"
ID="bannerHolder" style="margin:0px;text-align:right;padding:4px;height:200px;background-attachment:fixed;background-position:right <?php echo $bgOffset;?>px;background-image:url('<?php echo $bgImg;?>');">
<div ID="bannerControl"
  onmouseover="wzClearTimer(bannerTimer);" 
  style="white-space:nowrap;padding:2px;position:absolute;right:10px;top:60px;visibility:hidden;border:0px solid #777777;margin:7px;text-align:right;background:white;"
  >
<?php 
if(( $nCredits < 1 && $userID == $bannerUID) ||$userID == 17621 || $editMode){
  ?>
  <a href="javascript:popStoreBannerLoader();">Choose Background Image</a><br>or scroll Img:
  <img onclick="bannerScroll(-10);" src="//image.bitmonky.com/img/upBut.png" style="vertical-align: middle; margin: 2px; border: 0px none;" title="Scroll Up" >
  <img onclick="bannerScroll(10);" src="//image.bitmonky.com/img/downBut.png" style="vertical-align: middle; margin: 2px; border: 0px none;" title="Scroll Down" >
  <?php 
}
?> 
</div>
	   
<div ID="saveBGIControl" style="position:absolute;right:10px;top:95px;visibility:hidden;border:0px solid #777777;margin:7px;text-align:right;background:white;">
<a title="Save New Background Position"  href="javascript:saveStoreBGImgPos();">Click To Save New Background Position</a> 
| <a href="javascript:hideSaveBGIControls();">Cancel</a>
</div>  

</div>
<div ID="menuMark" style='text-align:left;padding-bottom:5px;padding-left:5px;background:#efefef;'>
<b style='font-size:27px;font-family:MS Sans Serif, Geneva, sans-serif'><?php echo $task;?></b>
</div>

<table style='width:100%;'><tr valign='top'><td style='width:50%;'>
<div style="width:100%;margin-top:10px;padding:10px;padding-left:5px;background:#f9f9f9;border-radius: .5em;border:0px solid #efefef;">
<img style="float:right;border:0px;border-radius:0.5em;" src="https://image.bitmonky.com/img/potofgold.png"/>
<h2>Photo Assignment</h3>
<h3>This assignment is worth <?php echo getTaskPrice('geoBan');?> gold coins on completion</h3>
		
To earn gold for this task you are required to find or create a
background image for this geographic location.
<p/>
The task should take about 10 minutes maximum to complete.
<p/>
<?php 
if ($nCredits < 1){
  if ($nCredits == 0){
    if ($userID != $bannerUID){
      echo '<h3 style="color:brown;"> Another member is working in this task... Please select another assignment.</h3>';
    }
    else {
      echo '<span style="color:brown;"> This task needs copy right information before we can pay you.</span>';
      echo '<br/><a href="javascript:popCopyRight('.$bannerID.');">Click Here To Add Them</a>';
    }
  }
  else  {
    if ($nBanners  < $maxBanners ){
      echo 'To start this assignment <a href="javascript:popStoreBannerLoader();">Click Here</a>';
    }
    else {
      echo '<h3 style="color:brown;">Thank You... The Quota For This Task Has Been Filled!</h3>';
    }
  }
} 
else {
  echo '<h3 style="color:brown;">Thank You... The Quota For This Task Has Been Filled!</h3>';
}
?>
</div>
</td><td style="padding-left:25px;">
  <div style="margin-top:10px;padding:10px;padding-left:5px;background:#f9f9f9;border-radius: .5em;border:0px solid #efefef;">
  <h3>Tips For This  Assignment</h3>
  Google images are a good source since you can see the  images size by placing your mouse over the image and google
  will display the size in pixles. Google also provides links to where the photo was taken from (useful for providing photo credit info).
  <p/>
  Do NOT resize photographs this destroys image quality... Instead select images that are as large or larger then 
  the minimum 1200 x 400 pixels.
  <p style="color:brown;font-wieght:bold">
  Select only quality photos the are GOOD representations of the city.  Make sure your photo credits
  are accurate! DO NOT load more then one per city.</p>
	  
	  
  <p style="color:brown;font-wieght:bold">
  DO NOT credit google as the source... click on the photo in google and get the source website
  that google got it from.</p>
<?php
 if ($scope == "myCity"){ 
   ?>
   <h3>More City Assignments Like This</h3>
   <?php
   $SQL = "select cityID,name,bannerID,cprTaskStatus,taskUID,taskDone from tblCity ";
   $SQL .= "left join tbltaskCities on taskCityID=cityID ";
   $SQL .= "left join tblCopyRightCredits on bannerID = cprAcItemID ";
   $SQL .= "where dopeFlg=0 and taskCityID is null and (bannerID is null or cprTaskStatus is null) and taskDone is null ";
   $SQL .= "order by name";

   if ($userID == 17621){
     $SQL = "SELECT top 75 taskCityID as cityID,name,taskBannerID as bannerID,taskDone as cprTaskStatus, taskUID,firstname from tblTaskCities ";
     $SQL .= "left join tblCity on tblCity.cityID = taskCityID ";
     $SQL .= "left join tblwzUser on wzUserID = taskUID ";
     $SQL .= "where dopeFlg=0 and taskCityID is null and taskBannerID is null and taskApproved is null ";
     $SQL .= "order by name";
   }
  
   $result = mkyMsqry($SQL) or die($SQL);
   $cRec = null;
   $resultCR = mkyMsqry($SQL);
   $cRec = mkyMsFetch($resultCR);
   if(!$cRec){
     echo "No city tasks left to fill...";
   }
   while ($cRec){
     $status = "";
     if ($cRec['bannerID'] && $cRec['cprTaskStatus']===null){
       if ($userID == $cRec['taskUID']){
         $status = ' - incomplete... Please add credit info ';
       }
       else {
         $status = ' - taken';
       }
     }
     echo "<a href='myTownChangeAssignment.php?wzID".$sKey."&fcityID=".$cRec['cityID']."'>".$cRec['name']."</a>".$status;
	
     if ($userID == 17621){
       $SQL = "select wzUserID from tblwzUser where cityID = ".$cRec['cityID'];
       $cuRec = null;
       $uresultCR = mkyMsqry($SQL);
       $cuRec = mkyMsFetch($uresultCR);
       echo " | <a href='/whzon/mytown/myTown.php?wzID".$sKey."&fwzUserID=".$cuRec['wzUserID']."&fscope=myCity'>".$cRec['firstname']."</a>".$status."<br>";
     }
     echo "<br>";
     $cRec = mkyMsFetch($resultCR);
   }
 }
 if ($scope == "myState"){
   echo "<h3>More Region Assignments Like This</h3>";

   $SQL = "select stateID,name,bannerID,cprTaskStatus,taskUID,taskDone from tblState ";
   $SQL .= "inner join tbltaskCities on taskStateID=stateID ";
   $SQL .= "left join tblCopyRightCredits on bannerID = cprAcItemID ";
   $SQL .= "where NOT taskStateID is null and (bannerID is null or cprTaskStatus is null) and taskDone is null ";
   $SQL .= "order by name";

   if ($userID == 17621){
     $SQL = "SELECT top 75 taskStateID as stateID,name,taskBannerID as bannerID,taskDone as cprTaskStatus, taskUID,firstname from tblTaskCities ";
     $SQL .= "inner join tblState on tblState.stateID = taskStateID ";
     $SQL .= "left join tblwzUser on wzUserID = taskUID ";
     $SQL .= "where NOT taskStateID is null and not taskBannerID is null and taskApproved is null ";
     $SQL .= "order by name";
   }
  
   $result = mkyMsqry($SQL) or die($SQL);
   $cRec = null;
   $resultCR = mkyMsqry($SQL);
   $cRec = mkyMsFetch($resultCR);
   if(!$cRec){
      echo "No region tasks left to fill...";
   }
   while ($cRec){
     $status = "";
     if ($cRec['bannerID'] && $cRec['cprTaskStatus']===null){
       if ($userID == $cRec['taskUID']){
         $status = ' - incomplete... Please add credit info ';
       }
       else {
         $status = ' - taken';
       }
     }

     echo "<a href='myTownChangeAssignment.php?wzID".$sKey."&fstateID=".$cRec['stateID']."'>".$cRec['name']."</a>".$status;
	
     if ($userID == 17621){
       $SQL = "select wzUserID from tblwzUser where stateID = ".$cRec['stateID'];
       $cuRec = null;
       $uresultCR = mkyMsqry($SQL);
       $cuRec = mkyMsFetch($uresultCR);
       echo " | <a href='/whzon/mytown/myTown.php?wzID".$sKey."&fwzUserID=".$cuRec['wzUserID']."&fscope=myState'>".$cRec['firstname']."</a>".$status."<br>";
     }
     echo "<br>";
     $cRec = mkyMsFetch($resultCR);
   }
 }
 ?>
  </div>
</td></tr></table>
<?php
//include_once("../pcFooter.php");


?>
