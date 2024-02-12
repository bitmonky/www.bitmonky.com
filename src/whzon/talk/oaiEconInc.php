<?php
include_once('../gold/goldInc.php');

$SQL = "select * from tblCurrencyPriceLog order by cplgID desc limit 1";
$res = mkyMyqry($SQL);
$rec = mkyMyFetch($res);
$mcap = 10000000.0;
$bitMP = 1;

if ($rec){
  $mcap = $mcap * $rec['cplgPriceCAD'];
  $bitMP = $bitMP * $rec['cplgPriceCAD'];

  //$ecod .= "Current BitMonky Market Cap:<b> \$".mkyNumFormat($mcap,4)." CAD</b>";
  $ecod  = "Here is the BitMonky Economic Report (";
  $ecod .= "Earning Difficulty: ".mkyNumFormat($eGoldDiff*100,9)."%, ";

  $ecod .= "BitMonky Holdings: ".mkyNumFormat($rec['cplgBMHolding'],4).", "; 
  $ecod .= " BitMonky Price:  \$".mkyNumFormat($bitMP,7)." CAD, ";
  $ecod .= " BMGP Price:  \$".mkyNumFormat($bitMP/500.0,7)." CAD, ";
  $ecod .= " Total Holdings Value:  \$".mkyNumFormat($rec['cplgBMHolding'] * $bitMP,4)." CAD, ";
  $ecod .= "BitMonky Not Released: ".mkyNumFormat(bitMonkyUnReleased(),4).", ";
  $bitMonkyTAvailable = $rec['cplgAvailable'];
  $ecod .= "BitMonky Master Wallet : ".mkyNumFormat($rec['cplgMasterWallet'],4).", ";

  $SQL = "select sum(goldcoins + purchasedGold +tradeGold + taxGold)/".$GLOBALS['MKYC_gogoFactor']." bitMonky from tblwzUser";
  $res = mkyMsqry($SQL);
  $rec = mkyMsFetch($res);
  $bitMonkyInCirc = $rec['bitMonky'];
  
  $ecod .= "BitMonky In Circulation As BMGP: ".mkyNumFormat($bitMonkyInCirc,4).", ";

  $SQL = "SELECT sum(impwMintMe)bitMonky FROM ICDirectSQL.tblImpWithdrawLog ";
  $SQL .= "where Not impwPending is null and (impwPayStatus is null or impwPayStatus = 'inProgress') ";
  $result = mkyMsqry($SQL);
  $tRec   = mkyMsFetch($result);
  $minerBonus = 0;
  if($tRec){
    $minerBonus = $tRec['bitMonky'];
  }
  $ecod .= "Miner Bonus Pending Payments: ".mkyNumFormat($minerBonus,4).", ";

  $SQL = "select (sum(msorAmtGP) - sum(msorAlocated))/500 bitMTotal from tblmrkSellOrder ";
  $SQL .= "where  msorToken = 'BMGP' and msorTradeCanceled is null and NOT msorGoldSecured is null and msorFilled is null ";
  $result = mkyMsqry($SQL);
  $tRec   = mkyMsFetch($result);
  $gjexTotal = 0;
  if($tRec){
    $gjexTotal = $tRec['bitMTotal'];
  }
  $ecod .= "GJEX bitMonky Held For Sale: ".mkyNumFormat($gjexTotal,4).", ";
  $bitMonkBal = $bitMonkyTAvailable;

  $SQL = "select sum(tywaBalance - tywaMkyBalance)bmDep from mkyBank01.tblTycWAccess";
  
  $result = mkyMsqry($SQL);
  $tRec   = mkyMsFetch($result);
  $bmDeposits =  $tRec['bmDep'];
  $ecod .= "Bitmonky Deposit Wallets: ".mkyNumFormat($bmDeposits,4).", ";
  
  $bitMonkBal = $bitMonkBal + $bmDeposits;

  $ecod .= "Bitmonky Final Balance: ".mkyNumFormat($bitMonkBal,4).", ";
  $ecod .= "Source: CoinMarketCap, MINTME )";

}
?>
