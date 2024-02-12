<?php 
include_once("../mkysess.php");

$cityID = clean($_GET['cityID']);
$SQL  = "Select name from tblCity  where cityID = ".$cityID;
$cresult = mkyMsqry($SQL) or die($SQL);
$cRec = mkyMsFetch($cresult);
$myCityname = $cRec['name'];

$SQL = "select count(*)nJobs from tblTycEmployee where tycEmpUID=".$userID;
$cRec = null;
$cresult = mkyMsqry($SQL) or die($SQL);
$cRec = mkyMsFetch($cresult);
if ($cRec['nJobs'] > 0){
  exit('');
}

$SQL  = "SELECT tycJobID,tycJobTitle,tycJobDesc,tjobMaxDayPosts,tjobRate,tjobMaxEmp from tblTycJobDesc  ";
$SQL .= "left join tblTycEmployee  on tycEmpType = tycJobID and TycCityID=".$cityID." ";
$SQL .= "where tycEmpID is null";


 $cRec = null;
 $cresult = mkyMsqry($SQL) or die($SQL);
 $cRec = mkyMsFetch($cresult);

 if ($cRec){
   echo "<div style='' class='infoCardClear'>";
   echo "<img style='float:right;width:90px;height:90;' src='//image.bitmonky.com/img/jobOpen.png'/>";
   echo "<h3>".getTRxt('There Are Online Job Openings In')." ".$myCityname."</h3>";
   echo getTRxt("This city has openings for the following online positions").":";
   echo "<br clear='right'><div align='right'><a style='font-size:smaller;' href='javascript:hideJobs();'>".getTRxt('Hide Jobs Listings')."</a></div>";
   echo "</div>";
   while ($cRec){
     $jobID = $cRec['tycJobID'];
     $SQL = "Select count(*) as nRec from tblTycJobApplications  where tjaUID=".$userID." and tjaCityID=".$cityID." and tjaJobID = ".$cRec['tycJobID'];
     $jresult = mkyMsqry($SQL);
     $jRec = mkyMsFetch($jresult);
     $appSent = $jRec['nRec'];
	 
     echo "<div ID='jobApp".$cRec['tycJobID']."' class='infoCardClear' style='background:#222222;margin:0px 0px 25px; 0px;'>";
     if ($appSent < 1){
       echo "<div align='right' style='margin-bottom:2px;'><a style='font-size:smaller;' href='javascript:applyToJob(".$cRec['tycJobID'].");'>";
       echo "Apply For This Job</a></div>";
     }
     else {
       echo "<div align='right' style='font-size:smaller;margin-bottom:2px;'><b>".getTRxt('Your Application For This Job is:')."</b> ".getTRxt('Pending')."</div>";
     }
     echo "<b>".getTRxt('Position:')."</b> ".getTRxt($cRec['tycJobTitle']);
     if ($jobID == 5){
       echo "<p/><b>".getTRxt('Pay:')."</b> ".getTRxt('Monthly Percentage Of Tycoon Tax (to be decided)')." ";
     }
     else {
       echo "<p/><b>".getTRxt('Pay:')."</b>  ".mkyNumFormat($cRec['tjobRate'],0)." gp Per Post | <b>Post Per Day:</b> ".$cRec['tjobMaxDayPosts'];
     }
     echo "<br/><b>".getTRxt('Positions Available:')."</b>  ".$cRec['tjobMaxEmp'];
     echo "<p><b>".getTRxt('Job Description:')."</b><br/><br/>";
     echo getTRxt($cRec['tycJobDesc']);
     echo "<p/></div>";
     $cRec = mkyMsFetch($cresult);
   }
   echo "</table>";
 }
 else {
   echo "<div class='infoCardClear'>";
   echo "<b>".getTRxt('Tycoon Job Openings:')."</b> - ".getTRxt('Sorry No Job Openings At This Time');
   echo "</div>";
 }
?>
