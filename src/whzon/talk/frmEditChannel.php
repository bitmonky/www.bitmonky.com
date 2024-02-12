<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
if (isset($_GET['ferror'])){$ferror=clean($_GET['ferror']);} else {$ferror=null;}
?>
<!doctype html>
<html class="pgHTM" lang="en">
<head>
  <meta charset="utf-8">
  <title>Whzon.com</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0">
  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
<title>SiteLogz - Add Chat Channel</title>
</head>
<body class='pgBody' style='margin:15px;'>

<?php

if ($userID!=0){

   $chanID=clean($_GET['fchanID']);

   if ($ferror==1){
     echo "<span stlye='color:red'>You Must Give Your Channel A Name!</span>";
   }

   $SQL = "select privateChan,name,guide,spoken,hcoID,hcoStory from tblChatChannel ";
   $SQL .= "left join tblHashChanOwner on hcoID = chanHcoID ";
   $SQL .= "where ownerID=".$userID." and channelID=".$chanID;
   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);

   $name      = $tRec['name'];
   $guide     = $tRec['guide'];
   $spoken    = $tRec['spoken'];
   $ispriv    = $tRec['privateChan'];
   $story     = $tRec['hcoStory'];
   $hcoID     = $tRec['hcoID'];

   $ischecked="";
   if ($ispriv==1){
     $isChecked=" Checked ";
   }

?>

   <form method="get" action="editChatChannel.php">
     <input type='hidden' name='fchanID' value='<?php echo $chanID;?>'>
     <?php
     if ($hcoID){
       echo "<input type='hidden' name='hcoID' value='".$hcoID."'>";
     }
     ?>
     <input type='hidden' name='wzID' value='<?php echo $sKey;?>'>
     <input type='hidden' name='fcallbck' value='frmEditChannel.php'>

      <p><b>Whzon.com | Add New Chat Channel</b> 
      <p>
      <table>
        <tr>
          <td>Channel Name</td>
          <td><input name="fname" value='<?php echo $name;?>' size="40" maxlength="50"></td>
        </tr>
        <tr>
          <td>Channel Description</td>
          <td><input name="fdesc" value='<?php echo $guide;?>' size="40" maxlength="250"></td>
        </tr>
        <tr>
          <td>Channel Language</td>
          <td style='color:brown;'><input name="fspoken" value='<?php echo $spoken;?>' size="20" maxlength="20"> !optional</td>
        </tr>
        <tr>
          <td>Make Channel Private</td>
          <td><input type='checkbox' name='fpriv' <?php echo $isChecked;?>> <a target='_top' href="javascript:parent.wzGetPageOS('/whozon/MiniNews.asp?fwebsiteID=5&fnewsID=400');">Info On Private Channels</a></td>
        </tr>
        <?php
        if ($hcoID){
          ?>
          <tr><td colspan='2'>
	  About This Channel<br/>
          <textarea name='hcoStory' style='width:calc(100% - 2em);height:4em;'><?php echo $story;?></textarea>
          </td></tr>
          <?php
	}
        ?>
        </table>

      <p><input name="fsubmit" type="submit"
      value="Update Channel"> </p>
    </form>

<?php
  }
  else{
    echo "<h1>Please Login To Do This</h1>";
  }
  
?>
</body>
</html>

