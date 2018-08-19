<?php

	$id = $_POST["id"];

	$output = fopen($id."/livello.txt", "r");

	$livello = fgets($output);

	fclose($output);

	$output = fopen($id."/potenza.txt", "r");

	$potenza = fgets($output);

	fclose($output);

	$output = fopen($id."/time.txt", "r");

	$time = fgets($output);

	fclose($output);

	echo $livello.",".$potenza.",".$time;

?>
