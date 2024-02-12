<?php
if (!isset($inpID)){$inpID = null;}
?>
// **************************************
// url auto share code
// **************************************
function atsUrlRemoveIt(url){
  return;
  var inp = document.getElementById('<?php echo $inpID;?>');
  var txt = inp.value.toString();
  txt = txt.replace(url,'');
  inp.value = txt;
}
function scanInput(msg,msgID){
  var txt = msg + ' ';
  var lnkpos = txt.search(/https:\/\//i);
  var isHttp = false;
  if (lnkpos == -1){
    lnkpos = txt.search(/http:\/\//i);
    if (lnkpos > -1){
      isHttp = true;
    }
  }
  var lnkend = txt.length;
  console.log('pvcURL_look-' + lnkpos,isHttp);
  if (lnkpos > -1 && (txt.substring(lnkend-1,lnkend) == ' ' || txt.substring(lnkend-1,lnkend) == '\n')){
    url = txt.substring(lnkpos,lnkend);
    if (url.indexOf('image.bitmonky') > -1)
      return false;
    atsUrlRemoveIt(url);
    if (isHttp){
      url = 'https' + url.substring(4,url.length);
    }
    doGetShareData(url,msgID);
    return true;
  }
  lnkpos = txt.search(/www\./i);

  if (lnkpos > -1 && (txt.substring(lnkend-1,lnkend) == ' ' || txt.substring(lnkend-1,lnkend) == '\n')){
    url = txt.substring(lnkpos,lnkend);
    if (url.indexOf('image.bitmonky') > -1)
      return false;
    atsUrlRemoveIt(url);
    doGetShareData('https://' + url);
    return true;
  }
  return false;
}
function showShare(msgID){
   var spot = ifrm.document.getElementById('linkSpot' + msgID);
   if (spot){
     spot.style.display = 'inline';
   }
}
function hideShare(msgID){
   var spot = ifrm.document.getElementById('linkSpot' + msgID);
   if (spot){
     spot.style.display = 'none';
   }
}
function doShareLinkNow(url,msgID){
  var inp = ifrm.document.getElementById('<?php echo $inpID;?>');
  var txt = inp.value.toString();
  var edUrl = '/whzon/pvchat/showPVUrlShare.php?mode=share&wzID=' + parent.sID + '&inURL=' + url + '&com=' + txt;
  var edDiv = 'linkSpot' + msgID;
  parent.updateDivHTML2(edUrl,edDiv);
  setTimeout(hideShare,3*1000);
  return;
}
function fetchPVURL(url,msgID) {
   var onexml = parent.getHttpConnection();
   var currentTime = new Date();
   var ranTime = currentTime.getMilliseconds();
   var url = '/whzon/pvchat/showPVUrlShare.php?wzID=' + parent.sID + '&msgID=' + msgID +  '&inURL=' + url + '&xr=' + ranTime;
   onexml.timeout   = 20*1000;
   onexml.ontimeout = function() {};
   onexml.onerror   = function() {};
   onexml.open("GET", url, true);
   onexml.onreadystatechange = function(){
     if (onexml.readyState == 4){
       if (onexml.status  == 200){
         var jdata = onexml.responseText;
         spot = ifrm.document.getElementById('linkSpot' + msgID);
         console.log('pvcURL_spot linkSpot' + msgID,jdata);
         if (spot && jdata != 'EMOT') {
           spot.innerHTML = jdata;
         }
         else {
           hideShare(msgID);
         }
       }
     }
   };
   onexml.send(null);
}
function doGetShareData(url,msgID){
  console.log('pvcURL_found:' + url,msgID);
  var check = url.toUpperCase();
  if (check.indexOf('IMAGE0.') > -1){
    return;
  }
  showShare(msgID);
  fetchPVURL(url,msgID);
  return;
}
// ********* End Auto Share Code ***********

