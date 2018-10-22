<?php

const DUPLICATE_ERROR = "Errore: ricarica la pagina.";

$db_giorgio = array('servername' => "techaviationflight.com.mysql:3306", 'username' => 'techaviationflight_com_whatwefly', 'password' => "Marino01.");
$db_gio = array('servername' => 'localhost', 'username' => 'root', 'password' => '');

foreach ($db_gio as $key => $value) {
	$sn = $db_gio['servername'];
	$un = $db_gio['username'];
	$pwd =  $db_gio['password'];
}

$conn = new mysqli($sn, $un, $pwd);
#conn = new mysqli("localhost", "root", "");
if (!$conn)
die("Connection failed: " . mysqli_connect_error());

#mysqli_select_db($conn , "techaviationflight_com_whatwefly");
mysqli_select_db($conn, "whatwefly");

function redir($sts){
	if (!$sts) header("Location: index.php?robin=".$_GET['robin']."&abroad=".$_GET['abroad']."&fmdup=1");
	else header("Location: index.php?robin=".$_GET['robin']."&abroad=".$_GET['abroad']."&state=".$_GET['state']."&fmdup=1");
}

function getICAOfromIT($conn) {
	$sql = "SELECT icao FROM airport_list WHERE icao LIKE 'LI%' ORDER BY RAND() LIMIT 1";
	$r = mysqli_query($conn, $sql);
	while ($a = mysqli_fetch_assoc($r)) {
		return $a['icao'];
	}
}

function getICAOfromABROAD($conn) {
	$sql = "SELECT icao FROM airport_list WHERE icao NOT LIKE 'LI%' ORDER BY RAND() LIMIT 1";
	$r = mysqli_query($conn, $sql);
	while ($a = mysqli_fetch_assoc($r)) {
		return $a['icao'];
	}
}

function getICAOfromSTATE($conn, $state){
	$sql = 'SELECT icao FROM airport_list WHERE state = "'.$state.'" ORDER BY RAND() LIMIT 1';
	$r = mysqli_query($conn, $sql);
	while ($a = mysqli_fetch_assoc($r)){
		return $a['icao'];
	}
}
function getDeplatCoordinates($conn, $dep)
{
	$sql = 'SELECT latitude_deg FROM airport_list WHERE ident = "' . $dep . '" ';
	$r = mysqli_query($conn, $sql);
	while ($a = mysqli_fetch_assoc($r)) {
		return $a['start_lat'];

	}
}
function getDeplongCoordinates($conn, $dep)
{
	$sql = 'SELECT longitude_deg FROM airport_list WHERE ident = "' . $dep . '" ';
	$r = mysqli_query($conn, $sql);
	while ($a = mysqli_fetch_assoc($r)) {
		return $a['start_long'];
	}
}
function getArrlatCoordinates($conn, $arr)
{
	$sql = 'SELECT latitude_deg FROM airport_list WHERE ident = "' . $arr . '" ';
	$r = mysqli_query($conn, $sql);
	while ($a = mysqli_fetch_assoc($r)) {
		return $a['end_lat'];

	}
}
function getArrlongCoordinates($conn, $arr)
{
	$sql = 'SELECT longitude_deg FROM airport_list WHERE ident = "' . $arr . '" ';
	$r = mysqli_query($conn, $sql);
	while ($a = mysqli_fetch_assoc($r)) {
		return $a['end_long'];
	}
}
function preventDuplicate ($icao1, $icao2) {
	return (($icao1 == $icao2)?false:true);
}
function getDistance($start_long, $start_lat, $end_long, $end_lat) {
	$t = $start_long - $end_lat;
	$distance= sin(degree_to_radious($start_lat)) * sin(degree_to_radious($end_lat)) * cos(degree_to_radious($start_long)) * cos(degree_to_radious($end_long)) * cos(degree_to_radious($t));
	$distance = acos($distance);
	$distance = radious_to_degree($distance);
	$distance = $distance * 60 * 1.1515;
	$distance = $distance * 1.1507;
	$distance = number_format($distance, 1);
	return $distance;
}

function degree_to_radious($deg){
	return($deg * pi()/180.0);
}

function radious_to_degree($rad){
	return($rad/pi() * 180.0);
}

function showDepArr($dep, $arr){
	echo 'TRATTA: '.$dep.' - '.$arr.'';
}

function showDistance($conn, $dep, $arr){
	$sqlDep = 'SELECT * FROM airport_list WHERE icao = "'.$dep.'"';
	$sqlArr = 'SELECT * FROM airport_list WHERE icao = "'.$arr.'"';
	$res = mysqli_query($conn, $sqlDep);
	$res2 = mysqli_query($conn, $sqlArr);

	#$lon_dep=0; $lon_arr=0; $lat_dep=0; $lat_arr=0;
	while ($row = mysqli_fetch_assoc($res)) {
		#var_dump($row);
		$lat_dep = $row['lat'];
		$lon_dep = $row['lon'];
	}

	while ($row = mysqli_fetch_assoc($res2)) {
		#var_dump($row);
		$lat_arr = $row['lat'];
		$lon_arr = $row['lon'];
	}

	$distance = getDistance(floatval($lon_dep), floatval($lat_dep), floatval($lon_arr), floatval($lat_arr));
	echo 'distanza: '. $distance;
}

if (isset($_GET['abroad'])) {


	switch ($_GET['abroad']) {
		case 'si':
			if ($_GET['robin'] == "si"){
				//volo estero rr
				$icao = getICAOfromABROAD($conn);
				echo 'TRATTA: '.$icao.' - '.$icao.'';
				echo 'Distanza: /';
			}
			else {
				//volo all'estero normale
				$a = getICAOfromABROAD($conn);
				$b = getICAOfromABROAD($conn);
				if (preventDuplicate($a, $b)) {
					showDepArr($a, $b);
					showDistance($conn, $a, $b);
				}
				else redir(false);
			}
			break;
		case 'no':
			if ($_GET['robin'] == "si"){
				//volo italia rr
				$icao = getICAOfromIT($conn);
				echo 'TRATTA: ' . $icao . ' - ' . $icao . '';
				echo 'Distanza: /';
			}
			else {
				//volo italia normale
				$a = getICAOfromIT($conn);
				$b = getICAOfromIT($conn);
				if (preventDuplicate($a, $b)) {
					showDepArr($a, $b);
					showDistance($conn, $a, $b);
				}
				else redir(false);
			}
		default:
			// code...
			break;
	}

/* CONDIZIONI ERRATE!!!

	if ($_GET['abroad'] == "no" && $_GET['robin'] == "si"){
		//vola in italia round robin
		$icao = getICAOfromIT($conn);
		echo 'TRATTA: ' . $icao . ' - ' . $icao . '';
		echo 'Distanza: /';
	}
	elseif ($_GET['abroad'] == "no" && $_GET['robin'] == "no"){
		//vola in italia normale
		$a = getICAOfromIT($conn);
		$b = getICAOfromIT($conn);
		if (preventDuplicate($a, $b)) {
			showDepArr($a, $b);
			showDistance($conn, $a, $b);
		}
		else redir(false);
	}
	else {
		//volo estero round robin
		$icao = getICAOfromABROAD($conn);
		echo 'TRATTA: '.$icao.' - '.$icao.'';
		echo 'Distanza: /';
	}
}

if ($_GET['abroad']=="si" && $_GET['robin'=="no"]){
	//volo esterno normale
	$a = getICAOfromABROAD($conn);
	$b = getICAOfromABROAD($conn);
	if (preventDuplicate($a, $b)) {
		showDepArr($a, $b);
		showDistance($conn, $a, $b);
	}
	else redir(false);
	*/
}



?>
