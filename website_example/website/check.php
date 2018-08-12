<?php

	$id = $_POST["id"];

	$output = fopen($id."/livello.txt", "r");

	// echo "Livello dell'acqua: ".fgets($output);

	echo fgets($output);

	fclose($output);

?>
