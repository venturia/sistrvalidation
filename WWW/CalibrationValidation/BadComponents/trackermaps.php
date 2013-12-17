<H1>Tracker Maps from Bad Component Calibration</H1>

The list of relevant directories can be found 
<a href="https://twiki.cern.ch/twiki/bin/view/CMS/BadChannelCalibrationResultsDirectories">here</a>

<?php

include '/afs/cern.ch/cms/tracker/sistrcalib/WWW/CondDBMonitoring/drawTrend.php';

exec ("find -name TrackerMap -type d",$dirlist);
$tagdirs=array();
foreach($dirlist as $rawdir) {
  list($tagdirs[])=str_replace("/TrackerMap","",sscanf($rawdir,"./%s/TrackerMap"));
} 
#$tagdirs[]="GR11/ExpressReco";
#$tagdirs[]="GR12/ExpressReco";
#$tagdirs[]="GR13/ExpressReco";

$thedir="";
if($_POST["go"]) {
  $thedir=$_POST["wanteddir"];
}

#echo "<H2>$tagdir</H2>";
?>

<form action="trackermaps.php" method="post" enctype="multipart/form-data">
<select name="wanteddir">


<?php

foreach($tagdirs as $tagdir) {
  if($tagdir!=$thedir) {
    echo "<option value=$tagdir>$tagdir</option>";
  } else {
    echo "<option value=$tagdir selected>$tagdir</option>";
  }
}
  
?>
</select>
<BR>

<?php

if($thedir!="") {

  echo "<select name='wantediovs[]' multiple size='20'>";
  $filelist=array();
  exec ("ls -F $thedir/TrackerMap/*.png",$filelist);
  
  foreach($filelist as $file) {
    list($run) = sscanf($file,"$thedir/TrackerMap/TkMapBadComponents_offline_Run%d.png");
    echo "<option value=$file>$thedir/$run</option>";
  }
  echo "</select><BR>";  
  echo "Do you want trend plots of $thedir? ";
  echo "<input type='radio' name='wantedtrend' value='no' checked> No ";
  echo "<input type='radio' name='wantedtrend' value='yes'> Yes ";
  echo "<br>";

}
?>
<p><input onClick="return true;" name="go" type="submit" value="Select"/></p>
</form>

<?php

if($_POST['go']) {

  foreach($_POST['wantediovs'] as $iov) {
    $actualtagdir=substr($iov,0,strpos($iov,"/Tracker"));
    list($run) = sscanf($iov,"$actualtagdir/TrackerMap/TkMapBadComponents_offline_Run%d.png");
    $logfiles=array();
    exec("ls $actualtagdir/QualityLog/BadComponents_*$run.txt",$logfiles);

    echo "<a href='$actualtagdir/PlotsPerRun/$run'>Analysis plots of run $run</a><br>";
    echo "<a href='$iov'><img src='$iov' hspace=5 vspace=5 border=0 height=250 width=500 ALT='$iov'></a> <br>"; 
    
    foreach($logfiles as $logfile) {
      echo "<a href='$logfile'> $actualtagdir/$run </a><br>";
    }
    echo "<br>";
  }
  if($_POST['wantedtrend']=="yes") {
    drawBadChannelTrend($thedir);
  }
}
?>
