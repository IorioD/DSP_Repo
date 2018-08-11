<?php

	if (isset($_POST["lastModification"])) {

		$post = $_POST["lastModification"];

		$data = explode("/", $post);

		$lastModification = $data[0];

		$count = $data[1];

		$output = filemtime("e.txt");

		if ($output > $lastModification) {

			shell_exec("python script.py");

			$output = fopen("e.txt", "r") or die("Non riesco ad aprire il file di input!");

			fclose($output);

			echo "This is the corresponding response: ".$risposta.",".$count;

		} else {

			echo "Running the model ...,".$count;

		}

	}

?>
