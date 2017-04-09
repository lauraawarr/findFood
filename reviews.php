<?php

if (isset($_GET['id'])){
	$id = $_GET['id'];
} else {
	header('Location: index.html');
}

if (isset($_GET['startLat']) && isset($_GET['startLon'])){
	$startLon = $_GET['startLon'];
	$startLat = $_GET['startLat'];

	$url = "https://developers.zomato.com/api/v2.1/restaurant?apikey=792848e206636aad10f5708eeb906eb1&res_id=".$id;
	$result = json_decode(file_get_contents($url));

	include('header.php');
	$img = $result->featured_image;
	if ($img == ""){
		$img = "images/food2_min.jpg";
	};

	$endLat = $result->location->latitude;
	$endLon = $result->location->longitude;

	$address = str_replace(" ", "%20", $result->location->address);
	$address = str_replace(",", "%2C", $address);

	// Request price estimate
	$url2 = "https://api.uber.com/v1.2/estimates/price?start_latitude=".$startLat."&start_longitude=".$startLon."&end_latitude=".$endLat."&end_longitude=".$endLon;
	$token = "6qUr8SiRPITA9h0HECHxJKiyHdCZ6rBdyWAQrnhH";
	
	$ch = curl_init($url2);
	curl_setopt($ch, CURLOPT_HTTPGET, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Authorization: Token ".$token,
		"Accept-Language: en_US",
		"Content-Type: application/json"
		    ));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	$response = json_decode($response);
	curl_close($ch);

	$starColor = "#".$result->user_rating->rating_color;

	if (isset($response->prices[0]->estimate)){
		$estimate = "(".$response->prices[0]->estimate.")";
	} else {
		$estimate = "";
	}

}


?>
<style>

	.star{
		border-bottom: 7px  solid <?php echo $starColor; ?>;
	}

	.star:before {
  		border-bottom: 8px solid <?php echo $starColor; ?>;
  	}

  	.star:after {
		border-bottom: 7px solid <?php echo $starColor; ?>;
	}

</style>

<body>
	<div id='load-overlay'></div>
	<div class="headerImg">
		<img src='<?php echo $img ?>' />
	</div>
	<a class="back-button" href="index.html">
		<img src="images/back-button.svg" alt="back button"/>
	</a>
	<div class='restaurant content-wrapper'>
		<?php
	
		echo "<div class='rating'>";
		$rating = $result->user_rating->aggregate_rating;
		for ($i=0; $i < floor($rating); $i++){
			echo "<div class='star'></div>";
		};
		if ($rating > 0){
			echo "(".$rating.")";
		} else {
			echo "No ratings yet";
		}
		echo "</div>";

		echo "<h1 id='rest-name'>".$result->name."</h1>";
		echo "<p>".$result->location->address."</p>";
		
		//Opens Uber - if on mobile, pickup and dropoff will be set automatically
		$url = "https://m.uber.com/ul/?action=setPickup&client_id=WYvDwsHclzZNVkF-JMOGYcP8FUUP2t2B&pickup=my_loc‌​ation&dropoff[nickna‌​me]=".$result->name."&dropoff[formatted_address]=".$address."&dropoff[latitude]=".$result->location->latitude."&dropoff[longitude]=".$result->location->longitude;
		echo "<a id='uber' href='".$url."'><p>GET A RIDE <span id='estimate'>".$estimate."</span></p></a>";

		echo "<a id='directions' target='_blank' href='https://www.google.ca/maps/dir//".$address."/@".$endLat.",".$endLon.",17z'>";
		echo "<p>GET DIRECTIONS</p></a>";

		// Create array of cuisine string and print to divs
		$cuisines = explode(",", $result->cuisines);
		echo "<div id='cuisine-list'>";
		foreach ($cuisines as $c) {
			echo "<div class='cuisine'>".$c."</div>";
		}
		echo "</div>";


		// echo "<p id='cost-for-two'>Avg cost for two: $".$result->average_cost_for_two."</p>";

		// echo "<a id='menu' targte='_blank' href='".$result->menu_url."'>Menu</a>";

		// var_dump( $result );
		?>

	</div>
	
</body>
<script type="text/javascript">
	var overlay = document.querySelector('#load-overlay');
	overlay.style.opacity = '0';//classList.add('fadeOut');
	setTimeout(function(){
		document.querySelector('body').removeChild(overlay);
	},2000);
</script>
</html>