<?php
const DUPLICATE_ERROR = "Errore: ricarica la pagina.";


$db_giorgio = array('servername' => "techaviationflight.com.mysql:3306", 'username' => 'UID', 'password' => "yuorpassword");
$db_gio = array('servername' => 'localhost', 'username' => 'root', 'password' => '');

foreach ($db_giorgio as $key => $value) {
	$sn = $db_giorgio['servername'];
	$un = $db_giorgio['username'];
	$pwd =  $db_giorgio['password'];
}

$conn = new mysqli($sn, $un, $pwd);
#conn = new mysqli("localhost", "root", "");
if (!$conn)
die("Connection failed: " . mysqli_connect_error());

#mysqli_select_db($conn , "techaviationflight_com_whatwefly");
mysqli_select_db($conn, "techaviationflight_com_whatwefly");

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

function preventDuplicate ($icao1, $icao2) {
	return (($icao1 == $icao2)?false:true);
}

function getDistance($start_long, $start_lat, $end_long, $end_lat){
    $d1 = sin((deg2rad($end_lat)- deg2rad($start_lat))/2);
    $d1 = pow($d1, 2);
    $d2 = cos(deg2rad($end_lat)) * cos(deg2rad($start_lat));
		$d3 = sin((deg2rad($end_long)- deg2rad($start_long))/2);
		$d3 = pow($d3, 2);
		$r1 = $d1 + $d2 * $d3;
    $distance = 2 * 6371 * asin(sqrt($r1));
    $distance = $distance / 1.85200;
    return $distance;
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
	echo ' distanza: '. $distance;
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
