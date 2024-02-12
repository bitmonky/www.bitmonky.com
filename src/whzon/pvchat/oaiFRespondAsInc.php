<?php
function respondAsSiteMonkey($msg,$talkerID,$digiHut=null){
  $userID = 63555;
  $hutOwnID = 'null';
  $billUID  = $talkerID;
  if ($digiHut){
    $hutOwnID = getUserID($digiHut);
    if ($hutOwnID != 17621){
      $billUID  = $hutOwnID;
    }

  }
  $SQL = "update ICDchat.tblMbrChat set mread = 1
  where (msgUserID=".$talkerID." and msgMbrID = ".$userID.")";
  mkyMyqry($SQL);

  $ts =	checkAITokenSuply($billUID);
  if ($ts->bal <= 0){
    $msg = "Sorry But You Are Out Of BMGP Tokens... I might work for bananas but they are not free! 
    You will have to earn some more or buy some on the GJEX.";

    $SQL  = "Insert into ICDchat.tblMbrChat (msg,msgUserID,msgMbrID,sentBy,modFlg,mchaDigiHutOID) ";
    $SQL .= "values('".left($msg,5999)."',".$userID.",".$talkerID.",".$userID.",2,".$hutOwnID.")";
    gfbug('respondAS:'.$SQL);
    $myresult = mkyMyqry($SQL);
    return;
  }	  

  if ($digiHut){
    $digiHut = '&digiHut='.$digiHut;
  }
  $SQL = "select firstname,nicNoEmo from tblwzUser where wzUserID = ".$talkerID;
  $ures = mkyMyqry($SQL);
  $urec = mkyMyFetch($ures);
  $atname = null;
  $uname  = 'nouser';
  gfbug('pvchatSQL'.$SQL.$msg);
  if ($urec){
    $uname  = str_replace(' ','.',$urec['firstname']);
    $atname = '@'.$urec['nicNoEmo'].' - ';
  }
  
  $data = '?UID='.$talkerID.'&msg='.urlencode($msg).'&uname='.urlencode($uname).$digiHut;
  $ores = tryFetchURL('https://www.bitmonky.com/whzon/pvchat/oaiPVSiteMonky.php'.$data);
  $j = json_decode($ores,false,512,JSON_INVALID_UTF8_SUBSTITUTE);
  $mkyresp = null;
  gfbug('pvores:'.$ores.$data);
  if ($j){
    if ($j->result){
      $mkyresp = $j->data;
    }
  }
  if ($mkyresp){
    $msg = addslashes($mkyresp);

    $SQL  = "Insert into ICDchat.tblMbrChat (msg,msgUserID,msgMbrID,sentBy,modFlg,mchaDigiHutOID) ";
    $SQL .= "values('".left($msg,5999)."',".$userID.",".$talkerID.",".$userID.",2,".$hutOwnID.")";
    $myresult = mkyMyqry($SQL);
  }
}
?>
