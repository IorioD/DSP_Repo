<?php

	session_start();

	if (!array_key_exists("id", $_SESSION)) {
        
		header("Location: index.php");

		exit;
		
	}

	$id = $_SESSION["id"]; 

	if (!isset($_SESSION["count"])) { // se l'user accede per la prima volta al sistema

		$_SESSION["count"] = 0;

		// cancello precedenti processi in esecuzione
		$shell = shell_exec('ps -x');
		$array = explode("\n", $shell);
		for ($i = 1; $i < count($array); $i++) {
			
			$command = substr($array[$i], 0, strpos($array[$i], '/dep'));
		    $idps = substr($command, 0, strpos($command, ' ?'));
		    shell_exec('kill '.$idps);
		}

		// setto gli input di default
		$input = fopen($id."/setpoint.txt", "w");
		fwrite($input, 4.5);
		fclose($input);

		$input = fopen($id."/gain.txt", "w");
		fwrite($input, 28000);
		fclose($input);

		// svuoto gli output
		$output = fopen($id."/livello.txt", "w");
		fclose($output);

		$output = fopen($id."/logLivello.txt", "w");
		fclose($output);

		$output = fopen($id."/potenza.txt", "w");
		fclose($output);

		$output = fopen($id."/logPotenza.txt", "w");
		fclose($output);

		$output = fopen($id."/time.txt", "w");
		fclose($output);

		// faccio partire il nuovo processo
		chdir("/var/www/example.com/public_html/".$id);
		shell_exec("./depuratore > /dev/null 2>&1 &");
		chdir("/var/www/example.com/public_html");

	} else {

		$_SESSION["count"]++;

	}

	// leggo gli input più recenti
	$setpointFile = fopen($id."/setpoint.txt", "r");
	$setpoint = fgets($setpointFile);
	fclose($setpointFile);

	$gainFile = fopen($id."/gain.txt", "r");
	$gain = fgets($gainFile);
	fclose($gainFile);

	// leggo gli output più recenti
	$livelloFile = fopen($id."/livello.txt", "r");
	$livello = fgets($livelloFile);
	fclose($livelloFile);

	$potenzaFile = fopen($id."/potenza.txt", "r");
	$potenza = fgets($potenzaFile);
	fclose($potenzaFile);

	$timeFile = fopen($id."/time.txt", "r");
	$time = fgets($timeFile);
	fclose($timeFile);

	if (array_key_exists("submit", $_POST)) { // se l'utente ha cliccato su Invia

		if (isset($_POST["setpoint"])) { // aggiorno setpoint.txt

			$setpoint = $_POST["setpoint"];

			$input = fopen($id."/setpoint.txt", "w");
			fwrite($input, $setpoint);
			fclose($input);
			
		}

		if (isset($_POST["gain"])) { // aggiorno gain.txt

			$gain = $_POST["gain"];

			$input = fopen($id."/gain.txt", "w");
			fwrite($input, $gain);
			fclose($input);
			
		}

	}

?>

<!DOCTYPE html>
<html>

	<head>

		<meta charset="utf-8">

		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

		<title>Controllo</title>

		<style>

			body {
				font-family: arial, sans-serif;
				font-weight: bold;
			}

			table {
				font-weight: bold;
				table-layout: fixed;
				font-family: arial, sans-serif;
				border-collapse: collapse;
				width: 50%;
				float: left;
			}

			td {
				border: 1px solid #dddddd;
				text-align: left;
				padding: 8px;
			}

			th {
				background-color: #dddddd;
				text-align: center;
				padding: 8px;
			}

			td:nth-child(1) {  
			  	text-align: right;
			}

			.divisor {
				border-bottom: 5px solid #dddddd;
			}

			#container {

				width: 40%;
				float: left;
				text-align: center;

			}

			#container > * {

				margin-top: 25px;

			}

		</style>

	</head>

	<body>

		<!--<p><a href="vasca.png" target="_blank">Guarda il modello</a></p>-->

		<div id="container">

			<form method="post">

				<div>
					<label for="setpoint">Valore di riferimento dell'acqua: </label>
					<select name="setpoint">

						<option value="4.3">4.3</option>
						<option value="4.4">4.4</option>
						<option value="4.5" selected="selected">4.5</option>
						<option value="4.6">4.6</option>
						<option value="4.7">4.7</option>

					</select>
				</div>

				<div>
					<label for="setpoint">Valore del guadagno del PID: </label>
					<select name="gain">

						<option value="26000">26000</option>
						<option value="27000">27000</option>
						<option value="28000" selected="selected">28000</option>
						<option value="29000">29000</option>
						<option value="30000">30000</option>

					</select>
				</div>

				<input type="submit" name="submit" value="Invia">

			</form>

			<p><a href='<?php echo $id; ?>/logLivello.txt' download='logLivello.txt'>Scarica logLivello.txt</a></p>
			<p><a href='<?php echo $id; ?>/logPotenza.txt' download='logPotenza.txt'>Scarica logPotenza.txt</a></p>

		</div>

		<table>
		  	<tr>
				<th colspan="2">Dati della simulazione</th>
		  	</tr>
		  	<tr>
				<td>Riferimento altezza dell'acqua</td>
				<td><?php echo $setpoint; ?></td>
		 	</tr>
		  	<tr class="divisor">
				<td>Coefficiente del PID</td>
				<td><?php echo $gain; ?></td>
		  	</tr>
		  	<tr>
				<td>Tempo di simulazione</td>
				<td id='tempoSpan'><?php echo $time; ?></td>
		  	</tr>
		  	<tr>
				<td>Livello dell'acqua</td>
				<td id='livelloSpan'><?php echo $livello; ?></td>
		  	</tr>
		  	<tr>
				<td>Potenza della pompa</td>
				<td id='potenzaSpan'><?php echo $potenza; ?></td>
		  	</tr>
		</table>

		<canvas id="myChart"></canvas>

		<script>

			function checkfile(id, graph) {

				if (window.ActiveXObject) {

					var http = new ActiveXObject("Microsoft.XMLHTTP");

				} else {

					var http = new XMLHttpRequest();

				}

				var url = "check.php";

				var params = "id=" + id;

				setInterval(function() {

					http.open("POST", url, true);

					http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

					http.onreadystatechange = function() {

					    if (http.readyState == 4 && http.status == 200) {

							var values = http.responseText.split(",");

							document.getElementById("livelloSpan").innerHTML = values[0];

							document.getElementById("potenzaSpan").innerHTML = values[1];

							document.getElementById("tempoSpan").innerHTML = values[2];

							graph.data.labels.push(values[2]);
						    graph.data.datasets[0].data.push(values[0]);
						    
						    graph.update();

					    }

					}

					http.send(params);

				}, 1000);

			}

			var myChart = document.getElementById("myChart").getContext("2d");

			var graph = new Chart(myChart, {

				type: "line",
				data: {

					labels: [<?php

								$i = 1;

								$log = fopen($id."/logLivello.txt", "r");

								while(!feof($log)) {

									$dato = trim(fgets($log));

									if ($dato != "") {

										if ($i != 1) {

											echo ", ";

										}

										echo $i;

									}

									$i = $i + 1;
								}

								fclose($log);

							?>],

					datasets: [{

						label: "Livello dell'acqua nella vasca",
						data: [<?php

								$i = 1;

								$log = fopen($id."/logLivello.txt", "r");

								while(!feof($log)) {

									$dato = trim(fgets($log));

									if ($dato != "") {

										if ($i != 1) {

											echo ", ";

										}

										echo $dato;

									}

									$i = $i + 1;
								}

								fclose($log);

							?>]

					}]

				},

				options: {
    				elements: {
	                    point:{
	                        radius: 0
	                    }
	                }
				}

			});

			checkfile(<?php echo $id; ?>, graph);

		</script>

	</body>

</html>
