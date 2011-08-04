<?php
define('MY_ROOT', realpath(dirname(__FILE__) . '/../'));

include MY_ROOT.'/inc/init.php';
include MY_ROOT.'/inc/flourishDB.php';


//$stations = fRecordSet::build('Station', NULL, NULL, 100, 100);
$stations = fRecordSet::buildFromSQL(
    'Station',
    "SELECT * FROM stations WHERE `source` IN ('fukushima_fleep','fukushima_daiichi')"
);

$output = '{"results":[';
$datastreams = "";		
$jsonData = "";
foreach ($stations as $station) {
	//$stationdatas = $station->buildStationdatas();
	$fleepdatas = fRecordSet::buildFromSQL('Fleepdata', array("SELECT * FROM `fukushimadatas` WHERE `station_id`=%i AND reading_date > ADDDATE(NOW(), INTERVAL -9 DAY) ORDER BY `reading_date` DESC LIMIT 0 , 6",$station->getStationId()));
	//$fleepdatas = fRecordSet::buildFromSQL('Fleepdata', array("SELECT * FROM `fukushimadatas` WHERE `station_id`=%i ORDER BY `reading_date` DESC LIMIT 0 , 6",$station->getStationId()));
	//$stationdatas->sort('getDatetimeString','desc');
	$datastreams.= '{"title": "'.$station->getStationNameEn().'",'.
			'"title_jp": "'.$station->getStationNameJp().'",'.
			'"description": "",'.
			'"creator": "fukushima_fleep",'.
			'"feed": "http://www.rdtn.org/feeds/station/'.$station->getStationId().'.json",'.
			'"location": {"lon":'.$station->getLongitude().', "lat":'.$station->getLatitude().', "name": "'.$station->getPrefectureNameEn().'"},'.
			'"id":'.$station->getStationId().','.
			'"datastreams": [';
	$success = false;
	foreach($fleepdatas as $fleepdata){	
		$sa = $fleepdata->getReadingValue();	
		if($sa!=-888 && $sa!=-999){
			$unit = $fleepdata->createUnit();
			$datastreams.= '{"at": "'.$fleepdata->getReadingDate().'",'.
					'"max_value": "0",'.
					'"min_value": "0",'.
					'"current_value": "'.$fleepdata->getReadingValue().'",'.
					'"id": "'.$fleepdata->getReadingId().'",'.
					'"unit": {"type": "basicSI","label": "'.$unit->getUnitLabel().'","symbol": "'.$unit->getUnitSymbol().'"}}';
			$success=true;
			break;
		}
				
	}
	if($success){
		//close and append
		$datastreams.= ']},';
		$jsonData.=$datastreams;
	}
	$datastreams="";
}

$output.= rtrim($jsonData, ',');
$output.= '], "itemsPerPage": '.$stations->count().', "startIndex": 0, "totalResults": '.$stations->count(TRUE).'}';

$file = new fFile(DOC_ROOT . '/feeds/fukushima_full_static.json');
$file->write($output);
echo $output;
?>


