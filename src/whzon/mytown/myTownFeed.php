<!-- SiteLOGz Code For: "myTownFeed" only!  -->
<script src='/whzon/snapLTp.php?ID=17621&wsID=5&pgID=584090'></script>
<!-- End of SiteLOGz Code -->
<script>
var feedConn = null;
var likesConn = null;

function wzFeedLoad(){
  //** Initialize Here
   feedConn    = parent.getHttpConnection();
   likesConn   = parent.getHttpConnection();
   readFeed();
}
function readFeed(){
     var currentTime = new Date();
     var ranTime = currentTime.getMilliseconds();
     var url = '/whzon/public/mbrActivityFeed.php?wzID=<?php echo $sKey; echo $feedData;?>&xm=' + ranTime ;
     feedConn.open("GET", url,true);
     feedConn.onreadystatechange = doWriteFeed;
     feedConn.send(null);
}

function doWriteFeed(){
 
    if (feedConn.readyState == 4){
      if(feedConn.status  == 200){ 
        var html = mkyTrim(feedConn.responseText);
        var wzout = document.getElementById('myTownActivities');
		if (wzout){
		  wzout.innerHTML = html;
		}
        fhReDrawFrame();
      }
    }

}
  function activityVoteTxt(vote,acID){
     var liketxt = document.getElementById('frmlike' + acID).fliketxt.value;
	 if (liketxt == '') {
	   alert('comment on why you ' + vote + ' this!');
	 }
	 else {
       var waiting = document.getElementById('newLikeSpot' + acID);
	   if (waiting) {
	     waiting.innerHTML = '<img style="width:35px;height;35px;" src="https://image.bitmonky.com/img/imgLoading.gif"/> Please Wait...';
		 newlikes = waiting;
	   }
       var currentTime = new Date();
       var ranTime = currentTime.getMilliseconds();
       var url = '/whzon/public/activityLikeUpdate.php?wzID=<?php echo $sKey;?>&fv=' + vote + '&facID=' + acID + '&fliketxt=' + liketxt + '&xm=' + ranTime ;
       likesConn.open("GET", url,true);
       likesConn.onreadystatechange = doActivityVoteTxt;
       likesConn.send(null);
	 }
  }
  function doActivityVoteTxt(){
    if (likesConn.readyState == 4){
      if (likesConn.status  == 200){ 
        parent.checkLikeStatus(likesConn.responseText);
	    if (newlikes) {
	      newlikes.innerHTML = '';
	    }
//	    clearTimeout(feedTimer);
	    readFeed();
      }
    }
  }


function wzPopScrollJoin(mbrID){
        parent.wzQuickReg();
}
</script>
   <p>
   <B><span class=wzBold>Posts In My <?php echo $scopeDisplay ?> - See Also:</span></b>
   | <a href="myTown.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fmyMode=mbrs&fwzUserId=<?php echo $wzUserID ?>">People</a>
   | <a href='myTown.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fmyMode=class&fwzUserId=<?php echo $wzUserID ?>'>Classifieds</a> 
   | <a href='myTown.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fmyMode=web&fwzUserId=<?php echo $wzUserID ?>'>Websites</a> 
   | <a href='myTown.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fmyMode=event&fwzUserId=<?php echo $wzUserID ?>'>Events</a> 
   | <a href='myTown.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fmyMode=mBlog&fwzUserId=<?php echo $wzUserID ?>'>miniBLOGs</a> 
   | <a href='myTown.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fmyMode=photo&fwzUserId=<?php echo $wzUserID ?>'>Photos</a> 
   | <a href='myTown.php?franCID=<?php echo $mtFranCID;?>&wzID=<?php echo $sKey;?>&fmyMode=wNews&fwzUserId=<?php echo $wzUserID ?>'>World News</a> 

<p/>
<div style='width:60%;margin-top:35px;' ID='myTownActivities'>
  <img style='width:35px;height;35px;' src='https://image.bitmonky.com/img/imgLoading.gif'> Loading Please Wait...	
</div>
