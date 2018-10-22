<!DOCTYPE html>
<html>
<head>
	<title>WhatWeFly</title>
	<link rel="stylesheet" href="style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="script.js"></script>
</head>
<body>
	<h3>Generatore casuale di tratta</h3>
	<form name="form1" method="GET" action="" onsubmit="return validity()">
		'<table border="1">
			<tr>
				<td>Round Robin?</td>
				<td>NO <input type="radio" id="robin" name="robin" value="no">
					SI <input type="radio" id="robin" name="robin" value="si">
				</td>
			</tr>
			<tr>
				<td>All'estero?</td>
				<td>
					NO <input type="radio" name="abroad" id="abroad" value="no">
					SI <input type="radio" name="abroad" id="abroad" value="si">
				</td>
			</tr>
			<!--
			<tr>
				<td>Da stato a stato</td>
				<td>
					NO <input type="radio" name="states" value="no" checked="checked" onchange="showStates()">
					SI <input type="radio" name="states" value="si" onchange="showStates()">
					<?php #require 'state_list.html'; ?>
				</td>
			</tr>
		-->

			<tr>
				<td colspan="2">
					<input type="submit" value="Genera">
				</td>
			</tr>

		</table>

	</form>

<?php
	require 'api.php';
?>

</body>
</html>
