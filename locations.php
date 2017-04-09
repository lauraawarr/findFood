<?php

	$apikey = "792848e206636aad10f5708eeb906eb1";

	if (isset($_GET['radius'])) {
		$radius = $_GET['radius'];
	} else {
		$radius = 1000;
	}

	if (isset($_GET['lat']) && isset($_GET['lon'])){
		$lat = $_GET['lat'];
		$lon = $_GET['lon'];

		$url = "https://developers.zomato.com/api/v2.1/search?lat=".$lat."&lon=".$lon."&radius=".$radius."&apikey=".$apikey;
		
		$out = file_get_contents($url);
	} else if (isset($_GET['city'])) {
		$city = $_GET['city'];

		$url = "https://developers.zomato.com/api/v2.1/locations?query=".$city."&apikey=".$apikey;

		$out = file_get_contents($url);
		// $out = $result->location_suggestions[0];
	} else {
		$out = "No code specified";
	};

	echo $out;

?>