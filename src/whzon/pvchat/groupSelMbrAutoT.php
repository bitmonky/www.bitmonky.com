<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

    


$MyBrow = $_SERVER['HTTP_USER_AGENT'];

$groupID = clean($_GET['fgroupID']);    

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title></title>
  <meta name="keywords" content=""/>
  <meta name="description" content=""/>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>
  <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <script src='../wzToolboxJS.php'></script>
</head>
<body style='background:#ffffff;margin:0px;'>
<table style='width:100%;'>
  <tr valign='bottom'>
    <td colspan='2' style='background:#222222;padding-left:15px;padding-bottom:5px;'>
      <div style='white-space:nowrap; margin:0px;padding:0px;'>
        <font style='FONT-FAMILY: Impact;font-size:20px;font-weight:normal;color:#8ec634;'> Bit<font style='color:#ffffff;'>Monky</font><font style='color:#eb9b52;'></font></font>
        </font><font style='font-size:14px;font-weight:normal;color:#cb0051;'>Private Chat
        </font>
		  <div ID='myAlerts'></div>
	  </div
  </tr>
</table>
<div style='padding:20px;padding-bottom:0px;'>
	<a href='groupChat.php?wzID=<?php echo $sKey?>&fgroupID=<?php echo $groupID?>'>[Return To Chat]</a>
</div>	
<table style='margin:20px;'><tr valign='top'><td style='padding:8px;'>	
  <h2><span style="padding:6px;background:#eeeeee;border-radius: .5em;">Add Members Here</span></h2>
  <form ID='getLocation' name='wzLocationFrm'  method='get' ">
  <b>Type Member Name:</b>
  <?php if (mkyStrpos($MyBrow,"MSIE") !== False){?>
    <input type='text'  name='flocation' onpropertychange='doClick(event);'> 
  <?php }else{?>
    <input type='text'  name='flocation' oninput='doClick(event);'> 
  <?php }?>
  <div ID='putLocations'></div> 
  </form>
</td>
<td style='padding:8px;width:50%'>
  <h2><span style="padding:6px;background:#eeeeee;border-radius: .5em;">Members In This Group</span></h2>
	<b>Click to remove a member from your group</b>
	<p/>
<?php
  $SQL = "SELECT groupMbrID from ICDchat.tblChatGroupMbrs ";
  $SQL .= "where groupID = ".$groupID;
  $mRec = null;
  $myresult = mkyMyqry($SQL);
  $mRec = mkyMyFetch($myresult);
  if(!$mRec){
    echo "<p/>No Members Yet...";
  }
  $n = 0;
  While ($mRec){
    $SQL = "SELECT wzUserID,firstname from tblwzUser where wzUserID = ".$mRec['groupMbrID'];
    $tRec = null;
    $result = mkyMsqry($SQL);
    $tRec = mkyMsFetch($result);

    echo "<div style='width:100%;' ID='wzlineIn:".$n."' onmouseover='highlight(".$n.");' onmouseout='undoHighlight(".$n.");'><a style='color:#777777;' ";
	echo "title='Click To Remove' href='groupDeleteMbr.php?wzID=".$sKey."&fmbrID=".$tRec['wzUserID']."&fgroupID=".$groupID."'>";
	echo "<img style='float:left;width:18px;height:24px;border-radius: .25em;border: 0px solid #74a02a;margin-right:2px;' ";
	echo "src='//image.bitmonky.com/getMbrTnyImg.php?id=".$tRec['wzUserID']."'/>".$tRec['firstname']."</a></div><br>";
    $n = $n + 1;
    $mRec = mkyMyFetch($myresult);
  }

?>
	
</td></tr></table>



<script>
function highlight(row){
    var wzoutput = document.getElementById("wzline:" + row);
    wzoutput.style.background="#f0f0f0";
}
function undoHighlight(row){
    var wzoutput = document.getElementById("wzline:" + row);
    wzoutput.style.background="#ffffff";
}

var myxml   = getUrlCom();

function getMatchingList(){
      var msg  = document.getElementById("getLocation").elements["flocation"].value;
      msg = mkyTrim(msg);
      while (msg.indexOf(',')!=-1)
        msg=msg.replace(',','');
      while (msg.indexOf('-')!=-1)
        msg=msg.replace('-','');
      while (msg.indexOf('  ')!=-1)
        msg=msg.replace('  ',' ');
      if (msg!='' ){
        prepUrl('groupMbrMatches.php?wzID=<?php echo $sKey;?>&fgroupID=<?php echo $groupID;?>&fstr=' + msg,myxml);
        myxml.onreadystatechange = putMatchingList;
        myxml.send(null);
        }
      else{
        var wzoutput = document.getElementById("putLocations");
        wzoutput.innerHTML='';
      }
}

function putMatchingList(){
  if (myxml.readyState == 4){
    var result=myxml.responseText;
    var wzoutput = document.getElementById("putLocations");
    wzoutput.innerHTML=result.toString();
  }
}

function doClick(e)
    {
       var key;
         if(window.event)
           key = window.event.keyCode;     //IE
         else
           key = e.which;     //firefox
    
       if (1==1){
          getMatchingList();
      }
    }


function isAlpha(xChr){
    var xStr=xChr.toString();  
    alert(xStr);
    var regEx = /^[a-zA-Z0-9\-]+$/;  
    return xChr.match(regEx);  
}  


function mkyTrim(stringToTrim) {
  return stringToTrim.replace(/^\s+|\s+$/g,"");
}
 


function prepUrl(url,inxml){
 var xm = new Date();
 if (url.indexOf("?")!=-1)
   url=url+ "&xv="  + xm.getMilliseconds();
 else
   url=url+ "?xv="  + xm.getMilliseconds();
 
 inxml.open("GET", url,true);
}

function getUrl(url){
 myxml.open("GET", url,false);
 myxml.send(null);
 return myxml.responseText;
}

function getUrlCom() {
var xmlhttp=null;
/*@cc_on @*/
/*@if (@_jscript_version >= 5)
// JScript gives us Conditional compilation, we can cope with old IE versions.
// and security blocked creation of the objects.
 try {
  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
 } catch (e) {
  try {
   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  } catch (E) {
   xmlhttp = false;
  }
 }
@end @*/
if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
	try {
		xmlhttp = new XMLHttpRequest();
	} catch (e) {
		xmlhttp=false;
  }
}
if (!xmlhttp && window.createRequest) {
	try {
		xmlhttp = window.createRequest();
	} catch (e) {
		xmlhttp=false;
	}
  }
return xmlhttp;
}

</script>

</body>
</html>

