<?php

const DUPLICATE_ERROR = "Errore: ricarica la pagina.";

$servername = "techaviationflight.com.mysql:3306";
$username = "techaviationflight_com_whatwefly";
$password = "Marino01.";
$conn = new mysqli($servername, $username, $password);
if (!$conn)
	die("Connection failed: " . mysqli_connect_error());
mysqli_select_db($conn , "techaviationflight_com_whatwefly");

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
	$sql = 'SELECT icao FROM airport_list WHERE iso_country = "'.$state.'" ORDER BY RAND() LIMIT 1';
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
    $t=($start_long - $end_lat);
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
function showDistance(){
    $distance= getDistance($_GET['start_long'], $_GET['start_lat'], $_GET['end_long'], $_GET['end_lat']);
	echo 'distanza: '. $distance;
}

if (isset($_GET['abroad'])) {
//trigger del submit

	if ($_GET['abroad'] == "no" && $_GET['robin'] && $_GET['state']) {
        $a = getICAOfromSTATE($conn, $_GET['state']);
        $b = getICAOfromSTATE($conn, $_GET['state']);
        if (preventDuplicate($a, $b)) {
            showDepArr($a, $b);
            showDistance();
        } else redir(true);
    }




	else {
		if ($_GET['state']=="no") {
            if ($_GET['abroad'] == "no") {
                if ($_GET['robin'] == "no") {
                    //ab no, rob no
                    $a = getICAOfromIT($conn);
                    $b = getICAOfromIT($conn);
                    if (preventDuplicate($a, $b)) {
                        showDepArr($a, $b);
                        showDistance();
                    } else redir(false);
                } else {
                    //ab no, rob si
                    $icao = getICAOfromABROAD($conn);
                    echo 'TRATTA: ' . $icao . ' - ' . $icao . '';
                    showDistance();
                }
            }
        }
	else {
		if ($_GET['robin'] == "no") {
			//ab si, rob no
			$a = getICAOfromABROAD($conn); $b = getICAOfromABROAD($conn);
            if (preventDuplicate($a, $b)) {
                showDepArr($a, $b);
                showDistance();
            }
                else redir(false);
		}
		else {
			//ab si, rob si
			$icao = getICAOfromABROAD($conn);
			echo 'TRATTA: '.$icao.' - '.$icao.'';
			showDistance();
		}
	}
	}

	


}



?>
