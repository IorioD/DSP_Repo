function checkfile(param) {

	if (window.ActiveXObject) {

		var http = new ActiveXObject("Microsoft.XMLHTTP");

	} else {

		var http = new XMLHttpRequest();

	}

	var url = "CheckFile.php";

	var params = "lastModification=" + param;

	setInterval(function() {

		http.open("POST", url, true);

		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		http.onreadystatechange = function() {

		    if (http.readyState == 4 && http.status == 200) {

		        document.getElementById("status").innerHTML = http.responseText;

		    }

		}

		http.send(params);

	}, 2000);

}
