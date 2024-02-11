<?php 
  ini_set('display_errors',1);
  error_reporting(E_ALL);
include_once("whzon/mkyPHPInc.php");
include_once('whzon/mblDetect/Mobile_Detect.php');
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$gsoftON = mkyStripos($_SERVER['SERVER_NAME'],'gsoft.bitmonky.com');
if ($detect->isMobile() || $detect->isTablet() || $gsoftON !== false){
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
  include_once('whzon/mblp/wzMblTPL.php');
  exit("");

}
include_once("whzon/wzAppTPL.php");
exit('');
?>
<!doctype html>
<html class='pgHTM' lang="en">
<head>

  <link rel="stylesheet" href="/whzon/pc.css?v=1.0"/>

  <meta charset="utf-8"/>
  <meta http-equiv="cache-control" content="max-age=0" />
  <meta http-equiv="cache-control" content="no-cache" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
  <meta http-equiv="pragma" content="no-cache" />
  <title>GoGo Noob Miner Co</title>
  <meta name="description" content="So Easy So Fun!">

  <meta property="og:title" content="So Easy So Fun!" />
  <meta property="og:url" content="https://guerrillasoft.org" />
  <meta property="og:image" content="https://image.gogominer.com/img/bitGoldCoin.webp"/>
  <meta property="og:description" content="A Digital Gold Mine And Trading Post!" />
<?php include_once("whzon/schemaOrgInc.php")?>

  <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
	  <script>
	      function gotoApp() {
	          trackRefer();
	          var store = location.hash.substr(1);
	          if (store) {
	              window.location.href = '/whzon/store/fetchStore.php?ftag=' + store;
	          }
	          else {
                    var turl = window.location.href.toLowerCase(); 
                    
                    if (turl.indexOf('monkytalk.com') > -1){
                      window.document.location = 'https://guerrillasoft.org';
                    }
                    if (turl.indexOf('gogominer.com') > -1){
                      window.document.location = 'https://guerrillasoft.org/gogoMiner.php';
                    }
                    if (turl.indexOf('gopow.me') > -1){
                      window.document.location = 'https://guerrillasoft.org/gogoMiner.php';
                    }
                    if (turl.indexOf('guerrillasoft.org') > -1){
                      //alert('Welcome To Bitmonky');
	              if (window.mobilecheck() == true) {
                        //alert('mobile');
	                window.location.href = 'https://guerrillasoft.org/whzon/mbl/wzMbl.php?fnrl=/whzon/mblp/gold/aboutBitMonky.php';
	              }
	              else {
                         //alert('Go To Bitmonky');
	                 window.location.href = 'https://guerrillasoft.org/whzon/wzApp.php';
	              }
                    }
                    else {
                      window.document.location = 'https://guerrillasoft.org/gogoMiner.php'; 
                    }
	          }
	      }
	      window.mobilecheck = function() {
	          var check = false;
	          (function(a) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|playbook|ipad|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true })(navigator.userAgent || navigator.vendor || window.opera);
//	          (function(a, b) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|playbook|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) window.location = b })(navigator.userAgent || navigator.vendor || window.opera, 'http://detectmobilebrowser.com/mobile'); return check;
	      }
	  </script>
</head>
<body class='pgBody' onload="gotoApp();">
<script>
  function trackRefer(){
    var scrW = "0";
    var scrC = "0";
    var jScrp = "0";

    var htRf = escape(top.document.referrer);
    if (htRf == "" || htRf == "Bookmark") htRf = "Bookmark+or+URL+Typed+Direct"
    var dhRDM = Math.floor(Math.random() * 1000);
    scrW = screen.width;

    if (navigator.appName != "Netscape") {
        scrC = screen.colorDepth;
    }
    else {
        scrC = screen.pixelDepth;
    }

    var BID = "&BID=" + wzhash(navigator.appName + navigator.appVersion + navigator.cpuClass + navigator.platform + navigator.userAgent + screen.width);
    jScrp = navigator.javaEnabled();
    jData = "&scrW=" + scrW + "&scrC=" + scrC + BID;
    jData = jData + "&htRefer=" + htRf + "&jScrp=" + jScrp + "";
    if (!(navigator.appName == "Netscape" && navigator.appVersion.charAt(0) == "2")) {
        var rURL = "/whzon/track/trackLT.php";
        rURL = rURL + "?ID=17621&wsID=5&pgID=584190" + jData + "&" + dhRDM + "'>";
        sendTrackReq(rURL);
    }
  }
  function sendTrackReq(url) {
      var mlikesxml = getHttpConnection();
      var currentTime = new Date();
      var ranTime = currentTime.getMilliseconds();
      mlikesxml.open("GET", url, true);
      mlikesxml.onreadystatechange = donothing;
      mlikesxml.send(null);

  }
  function donothing(){}
  
function getHttpConnection() {
  var xmlhttp = null;
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
if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
	try {
		xmlhttp = new XMLHttpRequest();
	} catch (e) {
		xmlhttp = false;
  }
}
if (!xmlhttp && window.createRequest) {
	try {
		xmlhttp = window.createRequest();
	} catch (e) {
		xmlhttp = false;
	}
  }
return xmlhttp;
}
function wzhash(str) {
        var num = 0;
        n = 1;
        hstr = "";

        for (var i = 0; i < str.length; i++) {
            num = num + str.charCodeAt(i);
            if (n > str.length / 4) {
                hstr = hstr + num;
                n = 1;
            }
            else
                n = n + 1;
        }
        return (num + "H" + hstr);
}
    
</script>     
</body>
</html>
