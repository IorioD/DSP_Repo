<?php

	session_start();

	$message = "";

	$values = array();

	if (!array_key_exists("id", $_SESSION)) {
        
		header("Location: index.php");

		exit;
		
	}

	$id = $_SESSION["id"];

	if (!isset($_SESSION['count'])) {

		$_SESSION['count'] = 0;

	} else {

		$_SESSION['count']++;

	}

	if (array_key_exists("submit", $_POST)) { // se l'utente ha inviato un nuovo setpoint aggiorno input.txt

		// $lastModification = filemtime($id."/e_test1.mat");

		// $link = mysqli_connect('db', 'user', 'test', "myDb");

		$link = mysqli_connect('localhost', 'root', 'Admin2015', 'myDb');

		if (mysqli_connect_error()) {

			die ("Database Connection Error");

		}

		if (isset($_POST["setpoint"])) {

			$setpoint = mysqli_real_escape_string($link, $_POST["setpoint"]);

			$message = "<p class='bold'>Hai inserito il seguente livello di riferimento: ".$setpoint."</p>";

			$input = fopen($id."/input.txt", "w");

			fwrite($input, $setpoint);

			fclose($input);

			// $p = new Process('./a.out');

        		// exec('nohup ./depuratore > /dev/null 2>&1 & echo $!' ,$op);




			// shell_exec("./depuratore > /dev/null 2>&1 &");

			// shell_exec("python py/script.py ".$id." e_test1.mat 2>&1");

			//$output = shell_exec("sudo apt-get install python-scipy");


			// shell_exec("sudo su");

			//$a = shell_exec("./depuratore");
			//echo $a;

			// shell_exec("exit");

			// shell_exec("./a.out");

			// shell_exec("gcc --version");

			// il processo è aggiornato ad ogni submit
			// processo terminato --> output.txt modificato
			// ad ogni submit --> 2 timeOfModification

			// $output = fopen("risposta.txt", "r") or die("Non riesco ad aprire il file di input!");

			// $risposta = "<p>This is the corresponding response: ".fgets($output).$ciao."</p>";

			// fclose($output);
			
		}

	} else {

		// cancello precedenti processi in esecuzione

		$shell = shell_exec('ps -x');

		$array = explode("\n", $shell);

		for ($i = 1; $i < count($array); $i++) {
			
			$command = substr($array[$i], 0, strpos($array[$i], '/dep'));
		    	$idps = substr($command, 0, strpos($command, ' ?'));
		    	shell_exec('kill '.$idps);
		}

		chdir("/var/www/example.com/public_html/".$id);

		// shell_exec("./depuratore 2>&1");

		// faccio partire il nuovo processo
		shell_exec("./depuratore > /dev/null 2>&1 &");

		chdir("/var/www/example.com/public_html");

		// il riferimento di default del livello della vasca è di 4.5 metri

		$input = fopen($id."/input.txt", "w");

		fwrite($input, 4.5);

		fclose($input);

		// la simulazione parte da uno stato di regime, con un livello di 4.588 metri

		$livello = fopen($id."/livello.txt", "w");

		fwrite($livello, 4.588);

		fclose($livello);

		// svuoto log.txt

		$log = fopen($id."/log.txt", "w");

		fclose($log);

	}

?>

<!DOCTYPE html>
<html>

	<head>

		<meta charset="utf-8">

		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

		<title>Controllo</title>

		<style>

			.image {
			    width: 75%;
			}

			p.bold {
				font-weight: bold;
			}

			p.res {
				color: blue;
			}

		</style>

	</head>

	<body>

		<p class="bold">Scegli il livello di riferimento dell'acqua per la vasca del depuratore</p>

		<form method="post">

		    	<select name="setpoint">

				<option value="4.0">4.0</option>
				<option value="4.1">4.1</option>
				<option value="4.2">4.2</option>
				<option value="4.3">4.3</option>
				<option value="4.4">4.4</option>
				<option value="4.5">4.5</option>
				<option value="4.6">4.6</option>

			</select>

			<input type="submit" name="submit" value="Send">

			<?php 

				if ($message != "") {

					echo "<div>Riferimento: ".$setpoint."</span></div>
					      <div>Livello dell'acqua: <span id='status'></span></div>
					      <a href='".$id."/log.txt' download='logging.txt'>Scarica log.txt</a>";

				}

			?>

		</form>

		<canvas id="myChart"></canvas>

		<script>

			function addData(chart, label, data) {
			    chart.data.labels.push(label);
			    chart.data.datasets.forEach((dataset) => {
				dataset.data.push(data);
			    });
			    chart.update();
			}

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

						//var data = http.responseText.split(",");

						//if (data[1] == <?php echo $_SESSION['count']; ?>) {

							document.getElementById("status").innerHTML = http.responseText;

							addData(graph, graph.data.labels.length + 1, http.responseText);

						//}

					    }

					}

					http.send(params);

				}, 1000);

			}

			var myChart = document.getElementById("myChart").getContext("2d");

			var graph = new Chart(myChart, {

				type: "line",
				data: {

					labels: [],
					datasets: [{

						label: "Prova",
						data: []

					}]

				},
				options: {}

			});

			checkfile(<?php echo $id; ?>, graph);

		</script>

	</body>

</html>
