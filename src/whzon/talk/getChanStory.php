<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
include_once("../mkysess.php");

$inChan=clean($_GET['fchan']);
$URL=getChanURL($inChan,$sKey);

echo mkyStrReplace("?","?wzID=".$sKey."&",$URL);


function getChanURL($chn,$sKey){
    $gotoURL = "homepg.php?wzID=".$sKey;
    if ($chn!=1){
      
      $SQL  = "Select videoID,spoken,firstname, privateChan,chanType,name,guide,ownerID, websiteID, URL,link, smNewsID,smNewsLinksID, ";
      $SQL .= "mNewsID, photoID,mBlogEID, tblChatChannel.img from tblChatChannel  ";
      $SQL .= "left join tblwzUser  on wzUserID=ownerID ";
      $SQL .= "left join tblWebsites  on websiteID=chanWSID  ";
      $SQL .= "where tblChatChannel.channelID=".$chn;

      $result = mkyMsqry($SQL);
      $tRec = mkyMsFetch($result);
      if ($tRec){
        $tklink=$tRec['link'];
        $chanType=$tRec['chanType'];
        $URL=$tRec['URL'];
        $chanWSID=$tRec['websiteID'];
        $smNewsID=$tRec['smNewsID'];
        $smNewsLinksID=$tRec['smNewsLinksID'];
        $photoID=$tRec['photoID'];
        $mBlogEID=$tRec['mBlogEID'];
        $ownerID = $tRec['ownerID'];
        $ownerName=$tRec['firstname'];
        $mNewsID = $tRec['mNewsID'];
        $privateChan=$tRec['privateChan'];
        $spoken=$tRec['spoken'];
		$videoID=$tRec['videoID'];

        $gotoURL= "/whzon/mbr/mbrProfile.php?wzID=".$sKey."&fwzUserID=".$ownerID; 

        if (!is_null($tklink)){
          if ($chanType=="mVideo"){ 
            $gotoURL = '/whzon/mbr/vidView/viewVideoPg.php?wzID='.$sKey.'&videoID='.$videoID; 
          }     
          if ($chanType=="mBlog"){ 
            $gotoURL = $tklink; 
          }     
          if ($chanType=="mNews"){ 
            $gotoURL = $tklink;
          }
        }

        if (!is_null($URL)){
          if (!is_null($smNewsID)){
            $gotoURL= "/whozon/wzViewWSNews.asp?fwebsiteID=".$chanWSID."&fnewsID=" .$smNewsID; 
            }
          else {
            $gotoURL= "/whozon/wzViewSite.asp?fwebsiteID=".$chanWSID;
          }
        }
        if (!is_null($photoID)){
          $SQL = "select wzUserID from tblwzPhoto  where PhotoID=".$photoID;
          $result = mkyMsqry($SQL);
          $tRec = mkyMsFetch($result);
          if ($tRec){
            $phUserID=$tRec['wzUserID'];
            }
          else {
            $phUserID=0;
          }
          $gotoURL= "/whozon/mbrViewPhotos.asp?fwzUserID=".$phUserID."&vPhotoID=".$photoID; 
        }
      }
    }
    return $gotoURL;
}
?>
