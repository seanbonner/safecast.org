<?php

try{
	$lat = $_REQUEST["latitude"];
	$lon = $_REQUEST["longitude"];
	$zoom = $_REQUEST["zoom"];
	$description = $_REQUEST["route_description"]; 
	$drivers = $_REQUEST["drivers"]; 
}catch(PDOException $e){
	echo "failed to get POST vars";
}
	    

include './inc/init.php';
include './inc/flourishDB.php';
include './inc/pdoDB.php';

$tmpl->set('siteName', $translations->siteName);
$tmpl->set('siteTagline', $translations->siteTagline);
$tmpl->set('formallyKnownAs', $translations->formallyKnownAs);
$tmpl->set('maps', $translations->maps);
$tmpl->set('blog', $translations->blog);
$tmpl->set('forums', $translations->forums);
$tmpl->set('submitAReading', $translations->submitAReading);
$tmpl->set('languageSelect', $translations->languageSelect);
$tmpl->set('contact', $translations->contact);
$tmpl->set('follow', $translations->follow);
$tmpl->set('termsPolicy', $translations->termsPolicy);
$tmpl->set('conceivedPart1', $translations->conceivedPart1);
$tmpl->set('conceivedPart2', $translations->conceivedPart2);
$tmpl->set('pageName', 'submit');
$action = fRequest::get('action',"string?" );
$driveId = fRequest::get('driveId',"integer" );


$existingLatitude = "";
$existingLongitude = "";
$existingDescription = "";
$existingDrivers = "";
$existingZoom = "13";

if($action=="submit"){
	$tmpl->set('title', 'Drive Submitted');
	$tmpl->place('header_headless');

	echo '<div class="content">
				<div class="relativeWrap">
					<div class="submitFormWrap">
						<div class="sectionHeadForm">Add New Drive</div><div id="submitFormBody" class="form">';
	
		
	try {
	    //$drive = new Drive();
	    /*
	    $lat = fRequest::get('latitude',"float" );
	    $lon = fRequest::get('longitude',"float" );
	    $zoom = fRequest::get('zoom',"integer" );
	    $description = fRequest::get('route_description',"string"); 
	    $drivers = fRequest::get('drivers',"string"); 
	    */
	    
	    


	   
		
		//prepare the db insert statements for re-use:
		$sql = "INSERT INTO drives (route_description, drivers, latitude, longitude, zoom) values (?, ?, ?, ?, ?)"; 
		$submitInsert = $db->prepare($sql);
		$drive = array($description, $drivers, $lat, $lon, $zoom);
		$submitInsert->execute($drive);
		$driveId = $db->lastInsertId();
		

		
	    /*
	    $drive->setLatitude($lat);
	    $drive->setLongitude($lon);
	    $drive->setZoom($zoom);
	    $drive->setRouteDescription($description);
	    $drive->setDrivers($drivers);	
	    $drive->store();
	    $driveId = $drive->getDriveId();
	    */
	    //echo 'description: '.$description.' drivers: '.$drivers;
	    echo 'Drive Added.   Would you like to <a href="/drive/'.$driveId.'/addData">add data</a> to your drive?';
	 
	} catch (fExpectedException $e) {
	    echo $e->printMessage();
	}


	
	
	echo '</div>
		</div>
	</div>
</div>';
	$db=null;
	die;
}else if($action=="update"){
	include './feeds/driveCacheTool.php';
	$tmpl->set('title', 'Drive Submitted');
	$tmpl->place('header_headless');

	echo '<div class="content">
				<div class="relativeWrap">
					<div class="submitFormWrap">
						<div class="sectionHeadForm">Drive Update</div><div id="submitFormBody" class="form">';
	
		
	try {
	
		$driveId = fRequest::get('drive_id',"integer" );
		
				
		//prepare the db insert statements for re-use:
		$sql = "UPDATE  `drives` SET  `route_description` =  ?, `drivers` = ?, `latitude` = ?, `longitude` = ?, `zoom` = ? WHERE `drive_id` = ?";
		$updateInsert = $db->prepare($sql);
		$drive = array($description, $drivers, $lat, $lon, $zoom, $driveId);
		$updateInsert->execute($drive);

		
		/*
		$drive = new Drive($driveId);
	    $drive->setRouteDescription(fRequest::get('route_description',"string" ));
   	    $drive->setDrivers(fRequest::get('drivers',"string" ));
   	    $drive->setLatitude(fRequest::get('latitude',"string" ));
   	    $drive->setLongitude(fRequest::get('longitude',"string" ));
   	    $drive->setZoom(fRequest::get('zoom',"string" ));
	    //$drive->populate();		
	    $drive->store();
	    */
	    
	    echo "Drive updated. Would you like to <a href='/drive/".$driveId."/addData'>add data</a> to your drive?";
	    $wroteFiles = generateStaticFiles($driveId);
		if($wroteFiles){
			echo "<p>Created/Updated static cache files successfully</p>";
		}else{
			echo "<p>failed to write static cache files</p>";
		}
	 
	} catch (fExpectedException $e) {
	    echo $e->printMessage();
	}


	
	
	echo '</div>
		</div>
	</div>
</div>';
	$db=null;
	die;
}else if($action=="flush"){
	die;
	$tmpl->set('title', 'Drive data flushed');
	$tmpl->place('header_headless');

	echo '<div class="content">
				<div class="relativeWrap">
					<div class="submitFormWrap">
						<div class="sectionHeadForm">Drive data flushed</div><div id="submitFormBody" class="form">';
	
		
	try {
		$result = $db->query("DELETE FROM `drivingdatas` WHERE `drive_id` = ".$driveId);
	    echo 'All data associated with drive'.$driveId.' has ben removed.   Would you like to <a href="/drive/'.$driveId.'/addData">add data</a> to this drive?';
	 
	} catch (fExpectedException $e) {
	    echo $e->printMessage();
	}


	
	
	echo '</div>
		</div>
	</div>
</div>';
	$db=null;
	die;
}elseif($action=="delete"){
	die;
	$tmpl->set('title', 'Drive Deleted');
	$tmpl->place('header_headless');

	echo '<div class="content">
				<div class="relativeWrap">
					<div class="submitFormWrap">
						<div class="sectionHeadForm">Drive Deleted</div><div id="submitFormBody" class="form">';
	
		
	try {
		$result = $db->query("DELETE FROM `drivingdatas` WHERE `drive_id` = ".$driveId);
		$result = $db->query("DELETE FROM `drives` WHERE `drive_id` = ".$driveId);
	    echo 'Drive deleted.   Would you like to <a href="/drive/add">create a new drive</a>?';
	 
	} catch (fExpectedException $e) {
	    echo $e->printMessage();
	}


	
	
	echo '</div>
		</div>
	</div>
</div>';
	$db=null;
	die;
}else if($action=="addData"){
	$tmpl->add('css',  'script/fileuploader/fileuploader.css');
	$tmpl->add('js',  'script/fileuploader/fileuploader.js');
	$tmpl->set('title', 'Add Drive Data');
	$tmpl->place('header_headless');
	

	echo '<script type="text/javascript">var currentDrive='.$driveId.';</script><div class="content">
				<div class="relativeWrap">
					<div class="submitFormWrap">
						<div class="sectionHeadForm">Add Data from a Drive</div><div id="submitFormBody" class="form">
	
		
				<div id="file-uploader">             
				</div>
				<div id="uploadResult"><pre id="response"></pre></div>';

	
	
	echo '</div>
		</div>
	</div>
</div>';

	die;
}else if($action=="cache"){

	$tmpl->set('title', 'Generating Drive Cache');
	$tmpl->place('header_headless');

	echo '<div class="content">
				<div class="relativeWrap">
					<div class="submitFormWrap">
						<div class="sectionHeadForm">Generating Drive Cache <img id="spinner" src="/script/spinner/spinner.gif" alt ="" /></div><div id="submitFormBody" class="form">
							<div id="uploadResult"><pre id="response"></pre></div>';
	
	//include './feeds/driveCacheTool.php';
	$sql = "SELECT `drive_id` FROM drives";
	$result = $db->query($sql);
	$result->setFetchMode(PDO::FETCH_OBJ);	
	echo '<script type="text/javascript">';
	echo'
		var drives = [';
	$driveArrayString = "";
	while ($data = $result->fetch()) {
		$driveArrayString.=$data->drive_id.',';
	}
	$driveArrayString = rtrim($driveArrayString, ',');
	echo $driveArrayString.'];';
	
	
	echo '
	function performDriveCache(driveId) {
		var myData = "id="+driveId;
		$.ajax(
		{"url": "/feeds/driveCache.php",
		"data": myData,
		"type": "POST",
		beforeSend: function(){
	              $("#response").prepend("generating cache for drive: "+driveId+" \n");
	        },
		"success": function(data){
	             var t=setTimeout("populateAllCache()",50);
            },
		"error": function(jqXHR, textStatus, errorThrown){
	               $("#response").prepend("failed drive: "+driveId+" \n");
	        }
		});  
	   
	    return false;
	            
	}
	
	function populateAllCache(){
		if(drives.length>0){
			var which = drives.pop();
			performDriveCache(which);
		}else{
	       $("#response").prepend("DONE ! \n");
	       $("#spinner").hide();
		}
	}
	
	window.addEventListener?window.addEventListener("load",populateAllCache,false):window.attachEvent("onload",populateAllCache);
	';
	echo '
</script>';
	
	


	
	
	echo '</div>
		</div>
	</div>
</div>';
	$db=null;
	
	die;
}else if($action=="list"){
	//$tmpl->add('css',  'script/fileuploader/fileuploader.css');
	//$tmpl->add('js',  'script/fileuploader/fileuploader.js');
	$tmpl->set('title', 'List of Drives');
	$tmpl->add('js',  '/script/spinner/jquery.spinner.js');
	$tmpl->place('header_headless');
	$drives = fRecordSet::build('Drive');
	
	echo '<script type="text/javascript">
function performDriveCache(driveId) {
	var myData = "id="+driveId;
	$this = $("#generateLink" + driveId);
	$.ajax(
	{"url": "/feeds/driveCache.php",
	"data": myData,
	"type": "POST",
	beforeSend: function(){
              $("#generateLink" + driveId).spinner({ position: "center", hide: true, img: "/script/spinner/spinner.gif"});
        },
	"success": function(data){
              $("#generateLink" + driveId).spinner("remove");
        },
	"error": function(jqXHR, textStatus, errorThrown){
                alert("error: "+textStatus);
                $("#generateLink" + driveId).spinner("remove");
        }
	});  
   
    return false;
    

        
}</script>
<script language="Javascript">var currentDrive='.$driveId.';</script><div class="content">
				<div class="relativeWrap">
					<div class="crudWrap">
						<div class="crudHeaderWrap">
							<div class="crudHeader">Manage Drives (<a href="/drive/add">Add a new drive</a>)</div><div style=" position: absolute; top: 3px; right: 10px;">(<a href="/drive/manage/cache">generate static caches for all</a>)</div> 
						</div>
						<div id="submitFormBody" class="form">
	
		
				<table>
					<tr>
						<th>Drive Description</th>
						<th>Participants</th>
						<th></th>
						<th></th>
						<th></th>
						';
	$odd = true;				
	foreach ($drives as $drive) {
		if($odd){
			echo '<tr class="crudOddRow">';
		}else{
			echo '<tr class="crudEvenRow">';
		}
		$odd = !$odd;
    	//echo '<td>'.$drive->getRouteDescription().'</td><td>'.$drive->getDrivers().'</td><td><a href="/drive/'.$drive->getDriveId().'/edit">Edit Drive</a></td><td><a href="/drive/'.$drive->getDriveId().'/addData">Add Data</a></td><td><a href="/drive/'.$drive->getDriveId().'/flush">Flush Data</a></td><td><a href="/drive/'.$drive->getDriveId().'/delete">Delete Drive</a></td><td><a href="/drive/'.$drive->getDriveId().'" target="_top">View</a></td><td><a id="generateLink'.$drive->getDriveId().'"  onclick="performDriveCache('.$drive->getDriveId().'); return false;" href="/feeds/driveCache.php?id='.$drive->getDriveId().'">Generate Static Files</a></td></tr>';
		//echo '<td>'.$drive->getRouteDescription().'</td><td>'.$drive->getDrivers().'</td><td><a href="/drive/'.$drive->getDriveId().'/edit">Edit Drive</a></td><td><a href="/drive/'.$drive->getDriveId().'/addData">Add Data</a></td><td><a href="/drive/'.$drive->getDriveId().'/flush">Flush Data</a></td><td><a href="/drive/'.$drive->getDriveId().'/delete">Delete Drive</a></td><td><a href="/drive/'.$drive->getDriveId().'" target="_top">View</a></td><td><a id="generateLink'.$drive->getDriveId().'"  onclick="performDriveCache('.$drive->getDriveId().'); return false;" href="/feeds/driveCache.php?id='.$drive->getDriveId().'">Generate Static Files</a></td></tr>';
		echo '<td>'.$drive->getRouteDescription().'</td><td>'.$drive->getDrivers().'</td><td><a href="/drive/'.$drive->getDriveId().'/edit">Edit Drive</a></td><td><a href="/drive/'.$drive->getDriveId().'/addData">Add Data</a></td><td><a href="/drive/'.$drive->getDriveId().'" target="_top">View</a></td></tr>';
		
	}
	
		

		echo '</table>';

	
	
	echo '</div>
		</div>
	</div>
</div>';

	die;
}else if($action=="edit"){
	//$tmpl->add('css',  'script/fileuploader/fileuploader.css');
	//$tmpl->add('js',  'script/fileuploader/fileuploader.js');
	$tmpl->set('title', 'List of Drives');
	$drive = new Drive($driveId);
	$existingLatitude = $drive->getLatitude();
	$existingLongitude = $drive->getLongitude();
	$existingDescription = $drive->getRouteDescription();
	$existingDrivers = $drive->getDrivers();
	$existingZoom = $drive->getZoom();
	
}else{
	$tmpl->set('title', 'Add New Drive');
	$tmpl->add('js',  'script/jquery.geolocation.js');
	$tmpl->add('js',  'script/formValidation.js');

}



if($mobile){
	$tmpl->add('css',  'style/formalizeMobile.css');
}else{
	$tmpl->add('css',  'style/formalize.css');
}
$tmpl->add('css',  'style/anytime.css');
$tmpl->add('js',  'http://maps.google.com/maps/api/js?sensor=false');
$tmpl->add('js',  'script/jquery.formalize.min.js');
$tmpl->add('js',  'script/jquery.checkForm.1.0.js');
$tmpl->add('js',  'script/jquery.locationpicker.js');
$tmpl->add('js',  'script/anytime.js');
$tmpl->add('js',  'script/anytimetz.js');


$tmpl->place('header_headless');



/*
    "enterLocation": "enter location",
    "submitReading": "Submit a reading:",

    */
?>

			<div class="content">
				<div class="relativeWrap">
					<div class="submitFormWrap">
						<?php if($action=="edit"){
							echo '<div class="sectionHeadForm">Update a Drive</div>';
						}else{
							echo '<div class="sectionHeadForm">Add New Drive</div>';
						}
						?>
						<div id="submitFormBody" class="form">
								
								<fieldset>
    								<legend>Where should the map be initially centered?</legend>
    									<div id="locationWrapper">
											<div class="formRow">
												<label for="lnglat"><?php echo $translations->location; ?></label><br />
												<input type="text" name="lnglat" value="" id="lnglat" class="input_xxlarge" />
											</div>
											<div id="locationOrSpacer"> - or - </div>
											<?php if($action=="edit"){
											
												echo '<form id="submitDrive" name="submitDrive" method="post" action="drive/update" class="">
														<input type="hidden" name="drive_id" value="'.$driveId.'" />';
											}else{
												echo '<form id="submitDrive" name="submitDrive" method="post" action="drive/submit" class="">';
																										
											}	
											?>

											<div class="latlngWrap">
												<div class="latlngbox">
													<label for="readings-lat"><?php echo $translations->latitude; ?></label><br />
													<input type="text" name="latitude" value="<?php echo $existingLatitude ?>" id="readings-lat" class="input_medium" />
												</div>
												<div class="latlngbox">
													<label for="readings-lng"><?php echo $translations->longitude; ?></label><br />
													<input type="text" name="longitude" value="<?php echo $existingLongitude ?>" id="readings-lng" class="input_medium" />
												</div>
											</div>
											<div id="locationMap"></div>
											<div id="mapNote"><?php echo $translations->youMayDragPin; ?></div>
										</div>
										
										
								</fieldset>
								<fieldset>
    								<legend>Drive Info</legend>
									<div class="formRow">
										<label for="drives-route_description">Route Description</label><br />
										<input type="text" name="route_description" value="<?php echo $existingDescription ?>" id="drives-route_description" class="input_xxlarge require" />
									</div>
									<div class="formRow">
										<label for="drives-drivers">Drivers</label><br />
										<input type="text" name="drivers" value="<?php echo $existingDrivers ?>" id="drives-drivers" class="input_xxlarge require" />
									</div>
									<div class="formRow">
										<label for="drives-zoom">Map Zoom level (default is 13)</label><br />
										<input type="text" name="zoom" value="<?php echo $existingZoom ?>" id="drives-zoom" class="input_xxlarge require" />
									</div>
								</fieldset>
								<div class="formRowSubmit"><br />
									<input type="image" src="images/submitNow.png" alt="" style="width:168px; height:33px:" />
								</div>			
  							</form>
						</div>
					</div>
					
					
				</div>
			</div>	
				
<?php   
if($action=="edit"){
	echo '<script type="text/javascript">
			$("#lnglat").locationPicker();
			$("#lnglat").trigger("forceUpdate");    
	</script>';
}
	
?>				
