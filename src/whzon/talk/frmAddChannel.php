<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

if (isset($_GET['ferror'])){$ferror=clean($_GET['ferror']);} else {$ferror=null;}
?>
<!doctype html>
<html class='pgHTM' lang="en">
<head>
  <meta charset="utf-8">
  <title>Whzon.com</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0">
  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>
<body class='pgBody' style='margin:15px;'>

<?php
   if ($ferror==1){
     echo "<span style='color:red;'>You Must Give Your Channel A Name!</span>";
  }
  if ($ferror=='hash'){
     echo "<span style='color:red;'>Hash Tag Already Taken... Choose Another Tag Name!</span>";
  }
  if ($ferror=='nohash'){
     echo "<span style='color:red;'>Hash Tag Required... Please Choose A Tag Name For Your Channel!</span>";
  }

?>

   <form method="GET" action="addChatChannel.php?">
     <input type='hidden' name='fcallbck' value='frmAddChannel.php'>
     <input type='hidden' name='wzID' value='<?php echo $sKey;?>'>
 
      <p><b>bitmonky.com | Add New Topic Channel</b> 
      <p>
      <table>
        <tr>
          <td>Channel Name</td>
          <td><input name="fname" size="40" maxlength="50"></td>
        </tr>
        <tr>
          <td>HashTag</td>
          <td><input name="fhash" size="40" placeholder='#YourHashTagName' maxlength="90"></td>
        </tr>
        <tr>
          <td>Channel Description</td>
          <td><input name="fdesc" size="40" maxlength="250"></td>
        </tr>
        <tr>
          <td>Channel Language</td>
          <td style='color:brown;'><input name="fspoken" size="20" maxlength="20"> !optional</td>
        </tr>
        <tr>
          <td>Make Channel Private</td>
          <td><input type='checkbox' name='fpriv'> <a href="javascript:parent.wzGetPageOS('/whozon/MiniNews.asp?fwebsiteID=5&fnewsID=400');">Info On Private Channels</a></td>
        </tr>
      </table>

      <p><input name="fsubmit" type="submit"
      value="Add Channel"> </p>
    </form>

</body>
</html>

