<?php

const DUPLICATE_ERROR = "Errore: ricarica la pagina.";

$servername = "localhost";
$username = "root";
$password = "";
$conn = new mysqli($servername, $username, $password);
if (!$conn)
	die("Connection failed: " . mysqli_connect_error());
mysqli_select_db($conn , "whatwefly");

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

function showDepArr($dep, $arr){
	echo 'TRATTA: '.$dep.' - '.$arr;
}

function redir($sts){
	if (!$sts) header("Location: index.php?robin=".$_GET['robin']."&abroad=".$_GET['abroad']."&fmdup=1");
		else header("Location: index.php?robin=".$_GET['robin']."&abroad=".$_GET['abroad']."&state=".$_GET['state']."&fmdup=1");
}

function get_distance($start_long, $start_lat, $end_long, $end_lat)
{
	$t = ($start_long - $end_long);
	$distance = sin(degree_to_radius($start_lat)) * sin(degree_to_radius($end_lat)) + cos(degree_to_radius($start_lat)) * cos(degree_to_radius($end_lat)) * cos(degree_to_radius($t));
	$distance = acos($distance);
	$distance = radius_to_degree($distance);
	$distance = $distance * 60 * 1.1515;
	$distance = $distance * 1.1507;

	return number_format($distance, 1);
}

function degree_to_radius($deg) {
	return ($deg * pi() / 180.0);
}

function radius_to_degree($rad) {
	return ($rad / pi() * 180.0);
}

		if (isset($_GET['abroad'])) {
//trigger del submit

			if ($_GET['abroad'] == "no" && $_GET['robin'] && $_GET['state']) {
				$a = getICAOfromSTATE($conn, $_GET['state']);
				$b = getICAOfromSTATE($conn, $_GET['state']);
				if (preventDuplicate($a, $b)) showDepArr($a, $b); else redir(true);
			}

			else {
				if ($_GET['abroad'] == "no") {
					if ($_GET['robin'] == "no") {
			//ab no, rob no
						$a = getICAOfromIT($conn); $b = getICAOfromIT($conn);
						if (preventDuplicate($a, $b)) showDepArr($a, $b); else redir(false);
					}
					else {
			//ab no, rob si
						$icao = getICAOfromABROAD($conn);
						echo 'TRATTA: '.$icao.' - '.$icao;
					}
				} 

				else {
					if ($_GET['robin'] == "no") {
			//ab si, rob no
						$a = getICAOfromABROAD($conn); $b = getICAOfromABROAD($conn);
						if (preventDuplicate($a, $b)) showDepArr($a, $b); else redir(false);
					}
					else {
			//ab si, rob si
						$icao = getICAOfromABROAD($conn);
						echo 'TRATTA: '.$icao.' - '.$icao;
					}
				}
			}




		}



		?>