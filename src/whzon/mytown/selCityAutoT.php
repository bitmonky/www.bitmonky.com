<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");
$MyBrow = $_SERVER['HTTP_USER_AGENT'];

$catID = safeGetINT('fcatID');
if ($catID !== null){
  $catID = "&fcatID=".$catID;
}
$fsq = safeGET('fsq');
$fsqPg = null;
if ($fsq){
  $fsqPg = "&fsq=".mkyUrlEncode($fsq);
  $catID .= $fsqPg;
}
?>
<!doctype html>
<html class='pgHTM' lang="en">
<head>
  <meta charset="utf-8"/>
  <title></title>
  <meta name="keywords" content=""/>
  <meta name="description" content=""/>
  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>
  <!--[if lt IE 9]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>
<body class='pgBody' style='margin:.8em;'>

<h1 style='Font-size:14px;'>Change Search City</h1>

<form ID='getLocation' name='wzLocationFrm'  method='get' ">
<b>Type New City Name:</b>

<?php if (mkyStrpos($MyBrow,"MSIE") !== False){?>
  <input autocomplete='off' type='text'  name='flocation' onpropertychange='doClick(event);'> 
<?php }else{?>
  <input autocomplete='off' type='text'  name='flocation' oninput='doClick(event);'> 
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
        prepUrl('myTownMatches.php?wzID=<?php echo $sKey.$catID;?>&fstr=' + msg,myxml);
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

