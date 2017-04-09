$(window).on('load', function(){

 	// Return user location
 	var userLat, userLon;
 	function getLocation() {
	    if (navigator.geolocation) {
	        navigator.geolocation.getCurrentPosition(showPosition,
	        	function(error){ // case if user chooses not to use geolocation
	        		$('.locationIn').css('display','block');
	        		$('#overlay > .output').html("Please enter your city.");

	        		$('.locationIn > button').on('click', getCoords);
	        
	        	});
	    } else { // case if geolocation is not available
	    	$('.locationIn').css('display','block');
    		$('#overlay > .output').html("Geolocation is not available <br/>Please enter your city");

    		$('.locationIn > button').on('click', getCoords);
	        console.log("Geolocation is not supported by this browser.");
	    }
	}
	function showPosition(position) {
		userLat = position.coords.latitude;
		userLon = position.coords.longitude;

		console.log(userLat, userLon);
	    searchLocations(userLat, userLon, 5000);
	}
	getLocation();

 	function getCoords(){
 		var cityIn = $('.locationIn > input').val().replace(",", "%2C").replace(" ", "");
		var url = "locations.php?city=" + cityIn;
 		var ajaxObject = $.ajax({
 			type: "GET",
 			url: url
 		});

 		ajaxObject.done(function(){
 			var city = JSON.parse(ajaxObject.responseText).location_suggestions[0]
 			console.log(city);
 			searchLocations(city.latitude, city.longitude, 5000);
 		});
 	}

 	function searchLocations(lat, lon, radius){
 		if (radius == null) radius = 1000;

 		var url = "locations.php?lat=" + lat + "&lon=" + lon + "&radius=" + radius;
 		var ajaxObject = $.ajax({
 			type: "GET",
 			url: url
 		});
 		ajaxObject.always(function(){
 			console.log("Loading..");
 		});
 		ajaxObject.done(function(){
 
 			try {
 				var suggest = JSON.parse(ajaxObject.responseText);
 				var count = suggest.results_found;
 				var restaurants = suggest.restaurants;

 				// Removes overlay
 				$('#overlay').addClass("slideUp");

 				setTimeout(function(){
 					$('#overlay').css("display","none");
 				}, 1000);

 				// Prints results to divs
 				$('#main .output').html("<div id='resultCount'></div><div id='resultList'></div>");

 				if (count == 0){
 					$('#main .output').html("No restaurants found...");
 				} else if (count == 1){
 					$('#resultCount').html(count + " restaurant found");
 				} else {
 					$('#resultCount').html(count + " restaurants found");
 				}

 				for (var i = 0; i < Math.min(count, 20); i++){
 					var res = restaurants[i].restaurant;

 					var link = "<a href='reviews.php?id=" + res.id + "&startLat=" + userLat + "&startLon=" + userLon + "'>";
 					var name = "<div class='restaurant'><h3>" + res.name + "</h3>";
 					var address = "<p>" + res.location.address + "</p>";
 					var cuisineListOpen = "<div class='cuisine-list'>";
 					var cuisineListClose = "</div>";
 					var closeLink = "</div></a>";

					// Create array of cuisine string and print to divs
					var cuisines = res.cuisines.split(", ");
					var cuisineList = "";
					for (var j=0; j < cuisines.length; j++){
						cuisineList += "<p class='cuisine'>" + cuisines[j] + "</p>"
					};
 	

					$('#resultList').append( link + name + address +  cuisineList +  closeLink );

 				};
 				
 			} catch(e) {
 				$('#main .output').html("Invalid Code")
 			}
 		});
 		ajaxObject.fail(function(){
 			console.log("ERROR: " + ajaxObject.responseText);
 		});
 	}

 });