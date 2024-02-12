<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
$MyBrow=$_SERVER['HTTP_USER_AGENT'];
$onload = null;
if (isset($_GET['mode'])){
   $onload = " onload='parent.updateBlockList();' ";
}

?>
<!doctype html>
<html class='pgHTM' lang="en">
<head>
  <meta charset="utf-8">
  <title>Whzon.com</title>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0">
  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

</head>
<body class='pgBody' style='margin:1em' <?php echo $onload;?>>
<div class='infoCardNBG'>
<form ID='getLocation' name='wzLocationFrm'  method='get' >

<h1 style='font-size:14px;'>Blocking Mail and Private Chats From Countries:</h1>

You can now block mail and private chats from countries that you do not wish to recieve messages from.  To block a country enter the first
few letters of the country name and then click on the country to block from the list that appears below... 


<?php

   $SQL = "select blkCountryID from tblwzUserBlockList  where blkCountryID=0 and wzUserID=".$userID; 
   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);

   if (!$tRec){
     $unknownBlk=False;
     }
   else{
     $unknownBlk=True;
   }

?>
   <p><b>Block IP's Where Country Is Not Known:
   <?php if ($unknownBlk){?>
     <a href='unBlockCountry.php?wzID=<?php echo $sKey;?>&fcountryID=0'> No</a> | <font style='color:red;'>Yes</font>
   <?php }else {?>
     <font style='color:red;'>No</font> | <a href='blockCountry.php?wzID=<?php echo $sKey;?>&fcountryID=0'> Yes</a>
   <?php }?>
   </p>

<h2 style='font-size:12px;'>Countries You Have Blocked</h2>

   
<?php

   $SQL = "select blkCountryID, name from tblwzUserBlockList  inner join tblCountry  on tblCountry.countryID=blkCountryID ";
   $SQL .= "where blockUserID=0 and tblwzUserBlockList.wzUserID=".$userID." order by name";

   $result = mkyMsqry($SQL);
   $tRec = mkyMsFetch($result);

   if (!$tRec){
     echo " - you are not blocking any Countries.";
   }
   echo "<table>";

   while($tRec){
     $name=$tRec['name'];
     if (is_null($name)){ 
       $name="IP's where country is unknown";
     }

?>
   <tr valign='top'><td>
   <b><?php echo $name;?></b></td><td>
   - <a href='unBlockCountry.php?wzID=<?php echo $sKey;?>&fcountryID=<?php echo $tRec['blkCountryID'];?>'> Unblock</a><br clear='left'></td></tr>
<?php
      $tRec = mkyMsFetch($result);
    }

?>
    </table>

<p>

Type Country Name To Block:
<?php if (!mkyStrpos($MyBrow,"MSIE")===false){?>
  <input type='text'  name='flocation' onpropertychange='doClick(event);'> 
<?php }else{?>
  <input type='text'  name='flocation' oninput='doClick(event);'> 
<?php }?>
<div ID='putLocations'></div> 
</form>




<script>
function highlight(row){
    var wzoutput = document.getElementById("wzline:" + row);
    wzoutput.style.background="#f0f0f0";
}
function undoHighlight(row){
    var wzoutput = document.getElementById("wzline:" + row);
    wzoutput.style.background="none";
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
        prepUrl('blockCountryMatches.php?wzID=<?php echo $sKey;?>&fstr=' + msg,myxml);
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
</div>
</body>
</html>

