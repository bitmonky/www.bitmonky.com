<!-- SiteLOGz Code For: "gov - country listings" only!  -->
<script src='/whzon/snapLTp.php?ID=17621&wsID=5&pgID=583668'></script>
<!-- End of SiteLOGz Code -->
<script>
function presidentialPowers(){
   wzAPI_showFrame("/whozon/gov/joinInfo.asp",680,550,50,100);
}

function wzPopScrollJoin(mbrID){
   el=document.getElementById('tag' + mbrID);
   ptop=wzAPI_getOffset( el ).top;
   pleft=wzAPI_getOffset( el ).left;   
   wzAPI_setRefreshPg(1);
   wzAPI_showFrame("http://whzon.com/whozDev/frmQuickLogin.asp",400,450,pleft,ptop);
}
function viewPoliticalParties(countryID){
   var gtag='govtag' + countryID;
   var el=document.getElementById(gtag);
   var yoff=gov_getOffset( el ).top;
   var xoff=gov_getOffset( el ).left; 
   wzAPI_setRefreshPg(1);
   wzAPI_showFrame('/whozon/gov/menuViewParties.asp?fcountryID=' + countryID,430,350,xoff-80,yoff-100);
}

function formPoliticalParty(countryID){
   var gtag='govtag' + countryID;
   var el=document.getElementById(gtag);
   var yoff=gov_getOffset( el ).top;
   var xoff=gov_getOffset( el ).left; 
   wzAPI_setRefreshPg(1);
   wzAPI_showFrame('/whozon/gov/frmFormParty.asp?fcountryID=' + countryID,430,250,xoff,yoff);
}
function gov_getOffset( el ) {
    var _x = 0;
    var _y = 0;
    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop;
        el = el.offsetParent;
    }
    return { top: _y, left: _x };
}
</script>
   <p>
   <B><span class=wzBold>People In My <%=scopeDisplay%></span></b>
   | <a href='myTown.asp?fmyMode=class&fwzUserId=<%=wzUserID%>'>Classifieds</a> 
   | <a href='myTown.asp?fmyMode=web&fwzUserId=<%=wzUserID%>'>Websites</a> 
   | <a href='myTown.asp?fmyMode=event&fwzUserId=<%=wzUserID%>'>Events</a> 
   | <a href='myTown.asp?fmyMode=mBlog&fwzUserId=<%=wzUserID%>'>miniBLOGs</a> 
   | <a href='myTown.asp?fmyMode=news&fwzUserId=<%=wzUserID%>'>miniNEWs</a> 
   | <a href='myTown.asp?fmyMode=photo&fwzUserId=<%=wzUserID%>'>Photos</a> 
   | <a href='myTown.asp?fmyMode=wNews&fwzUserId=<%=wzUserID%>'>World News</a> 

<%
  
   search=cint(session("wzONSearch"))

%>

<p>

<table style='margin-top:50px;width:100%'>
  <tr valign='top'>
    <td>

      <h1 style="FONT-FAMILY:impact;font-weight:normal;font-size:17px;">Elections Have Started... Form Your whzon.com Virtual Democracies!
      <a style='color:red;' href='javascript:presidentialPowers();'>[Read More Here...]</a></h1>
      
      <p style='font-size:larger;margin-right:80px;'>Get your economy growing!  Keep your constituents safe and happy.  These are all the roles of a whzon.com goverening body.
      Elected parties  will get their chance to grow a real economy on whzon.com.</p>
      <p style='font-size:larger;margin-right:80px;'>
      Not happy with your leaders you can vote them out and bring in a new party!  Form and opposition party your self and oust 
      the current regime!  Keep the Dictators from taking over and run the scoundrels out of town.</p>
      
      <h1 style="FONT-FAMILY:impact;font-weight:normal;font-size:17px;">Vote And View Results Here</h1>
      <b style='color:red;'>Notice:</b> - Votes have been adjusted so that members who have more then one account are not included.
<%

SQL = "SELECT tblCountry.countryID,name, mbrPopulation, presID, firstname as president, partyName, nparty from tblCountry " + _
      "left join tblwzUser on wzUserID=presID " + _
      "left join govParty on rullingPartyID=partyID " + _
      "where not dopeFlg=1 order by nparty desc, mbrPopulation desc "

'response.write SQL

Set jlistRec = Server.CreateObject("ADODB.Recordset")
Set ws = Server.CreateObject("ADODB.Recordset")
Set wo = Server.CreateObject("ADODB.Recordset")

winX=200
Xinc=120

pg=clean(request("newPg"))
nextPage = pg
%>
   <p><table>
  <tr valign=top>
    <td style='padding:5px;'><b>Country</b></td>
    <td style='padding:5px;'><b>Whzon Presidential Leader</b></td>
    <td style='padding:5px;'><b>Parties And Voting Results</b></td>
    <td style='padding:5px;text-align:right;'><b>Mbr Population</b></td>
  </tr>

<%
i=0
nRows=30

link="?fmyMode=gov&fwzUserId=" & wzUserID
appName="myTown.asp"

nTop=pg+nRows

selTop="Select Top " & nTop & " "

SQL=replace(SQL,"SELECT",selTOP)

jlistRec.Open SQL, "DSN=ICDirectSQL"

if (NOT(jlistRec.EOF or jlistRec.BOF)) then
  jlistRec.move nextPage
end if

frIconStyle=""
onIconStyle=""

if session("mobile")=True Then
  frIconStyle="height:30px;width:36px;vertical-align:middle;"
  onIconStyle="height:26px;width:160px;margin-top:15px;"
end if

rowColor = "#ddddff"  
While (Not (jlistRec.EOF or jlistRec.BOF) and i < nRows)
  country  = jlistRec("name")
  countryID= jlistRec("countryID")
  pop      = jlistRec("mbrPopulation")
  pres     = jlistRec("president")
  party    = jlistRec("partyName")
  presID   = jlistRec("presID")
  nparty   = jlistRec("nparty")

  if isnull(nparty) then
    nparty="none - <a style='font-size:smaller' href=""javascript:formPoliticalParty(" & countryID & ");"">Form Party</a>"
  else
    nparty=nparty & " - <a style='font-size:smaller' href=""javascript:viewPoliticalParties(" & countryID & ");"">View/vote here</a> | <a  style='font-size:smaller' href=""javascript:formPoliticalParty(" & countryID & ");"">Form Opposition Party</a>"
  end if

  if isnull(presID) then
    presID=0
    pres="none elected"
  end if
  if isnull(pop) then
    pop=0
  end if

  presDisp="<img style='float:left;margin-right:8px;' src='https://image.bitmonky.com/getMbrTmn.php?id=" & presID & "'>" & pres

%>
  <tr valign=top>
    <td style='padding:5px;'><%=country%></td>
    <td style='padding:5px;'><%=presdisp%></td>
    <td style='padding:5px;'><span ID="govtag<%=countryID%>"><%=nparty%></span></td>
    <td style='padding:5px;text-align:right;'><%=formatnumber(pop,0)%></td>
  </tr>
<%
  i=i+1
  jlistRec.moveNext
wend
response.write "</table>"

response.write "<p><a href=" & appName & link & "&newPg=" & nextPage + nRows & ">Next</a>"
if(nextPage > 0) then
  response.write " | <a href=" & appName & link & "&newPg=" & nextPage - nRows & ">Back</a>"
end if
response.write " | <a href='" & appName & link & "&newPg=0'>Top</a>"


jlistRec.Close

   If clean(request("ferror"))=1 then
     response.write "<p><span class='errorMsg'></span>"
   end if
%>

   <br><br>



<%if session("mobile")=True Then%>
 </td>
   <!--mobile ad space-->
 <td style='padding:0px;text-align:right;'>
   <!--regular ad space -->
<%else%>
 </td>
 
 <td style='padding:0px;text-align:right;'>
<%end if%>
</td></tr></table>