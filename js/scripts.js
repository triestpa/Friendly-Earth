//Log in to facebook
function login() {
			FB.login(function(response) {
				if (response.authResponse) {
					console.log('Welcome!  Fetching your information.... ');
					FB.api('/me', function(response) {
						console.log('Good to see you, ' + response.name + '.');
						console.log('Your location is ' + response.location.name  + '.');
					});
				} else {
					console.log('User cancelled login or did not fully authorize.');
				}
			}, {scope: 'user_location, friends_location'});
		}


//Load the facebook friend list and insert it into the html
function loadFriendList() {
			var friendList_HTML = "";

			FB.api('me/friends', function(response) {
				$.each(response.data,function(index,friend) {
					FB.api(friend.id, function(response) {
						friendList_HTML += "<a href=\"#\" class=\"list-group-item\">" + friend.name + '  lives in  ' + response.location.name + '.' + "</a>";
						console.log(friend.name + '  lives in  ' + response.location.name + '.');
						$("#friendList").html(friendList_HTML);
					});
				});
			});
		}


var map;
var center;
var markers = [];
var markerCluster;
var infoBubble_prev = new InfoBubble();

//Initialize the google map
function initialize() {

		  // Create an array of styles.
		  var styles = [
		  {
		  	stylers: [
		  	{ hue: "#007fff" },
		  	{ saturation: -25 }
		  	]
		  },{
		  	featureType: "road",
		  	elementType: "geometry",
		  	stylers: [
		  	{ lightness: 100 },
		  	{ visibility: "simplified" }
		  	]
		  },{
		  	featureType: "road",
		  	elementType: "labels",
		  	stylers: [
		  	{ visibility: "off" }
		  	]
		  }
		  ];

		// Create a new StyledMapType object, passing it the array of styles,
 		// as well as the name to be displayed on the map type control.
 		var styledMap = new google.maps.StyledMapType(styles,
 			{name: "Styled Map"});

 		var map_canvas = document.getElementById('map_canvas');
 		var map_options = {
 			center: new google.maps.LatLng(20, 0),
 			zoom: 2,
 			maxZoom: 15,
 			minZoom: 2,
 			disableDefaultUI: true,
 			mapTypeId: google.maps.MapTypeId.ROADMAP
 		}
 		map = new google.maps.Map(map_canvas, map_options);
		    //Associate the styled map with the MapTypeId and set it to display.
		    map.mapTypes.set('map_style', styledMap);
		    map.setMapTypeId('map_style');

		markerCluster = new MarkerClusterer(map, markers, {
          zoomOnClick: false
        });

		google.maps.event.addListener(map, 'zoom_changed', function() {
				markerCluster.repaint();
 			});
		google.maps.event.addListener(map, 'dragend', function() {
				markerCluster.repaint();
 			});
		google.maps.event.addListener(map, 'click', function() {
				infoBubble_prev.close();
 			});


		google.maps.event.addListener(markerCluster, 'click', function(c) {
				var markers = c.getMarkers();
				var people = "<ul class=list-group; id=markerCluster>";
    			//Get all the titles
    			for(var i = 0; i < markers.length; i++) {
        			people += "<li class=list-group-item>" + markers[i].getTitle() + "</li>";
    				}
    			people += '</ul>'

    			var infoBubble = new InfoBubble({
          			minWidth: 40,
          			maxWidth: 200,
          			maxHeight: 150,
          			padding: 0,
          			disableAutoPan: true,
          			hideCloseButton: true,
          			content: people
        			});
    			infoBubble_prev.close();
    			infoBubble.open(map, markers[0]);
    			infoBubble_prev = infoBubble;
			});
	}

//Add a marker to the map for each person
function addMarker(lat, lng, location, person){
		windowContent = '<div id="windowContent"> <p class="text-center">' + '  ' + person + '</p></div>'

		var infoBubble = new InfoBubble({
          	minWidth: 40,
          	maxWidth: 200,
          	minHeight: 10,
          	maxHeight: 100,
          	padding: 0,
          	disableAutoPan: true,
          	hideCloseButton: true,
          	content: windowContent
        	});

		Latlng = new google.maps.LatLng(lat, lng);

		//Add Location to map
		var marker = new google.maps.Marker({
				position: Latlng,
				//	animation: google.maps.Animation.DROP,
				map: map,
				title: person
			});

		markers.push(marker);

		google.maps.event.addListener(marker, 'click', function() {
				infoBubble_prev.close();
   				infoBubble.open(map,marker);
   				infoBubble_prev = infoBubble;
  			});

		markerCluster.addMarker(marker);
}


//Get the location of the user and mark it on the map
function getMyLocationPoints() {
			FB.api('/me', function(response) {
				console.log('Hello, ' + response.name + '.'); 
				var location = response.location.name;    
				console.log('Your location is ' +  location + '.');
				console.log(response.location.id);
				FB.api('/'+response.location.id, function(GEOresponse) {
					addMarker(GEOresponse.location.latitude, GEOresponse.location.longitude, GEOresponse.name, response.name);
				});
			});
}

//Get the locations of all of the user's friends and mark them on the map
function getFriendsLocationsPoints() {
	var friendLatlng;
	var locationTitle;
	FB.api('me/friends', function(response) {
		$.each(response.data,function(index,friend) {
			FB.api(friend.id, function(response) {
				if (typeof response.location === "undefined") {
					console.log("Cannot access the location of " + friend.name);
				}
				else {
					FB.api('/'+response.location.id, function(GEOresponse) {
					addMarker(GEOresponse.location.latitude, GEOresponse.location.longitude, GEOresponse.name, response.name);
					});
				}
			});
		});
	});
}

//RESTful query to geoname.org
function queryGeoNames(location) {
	var queryURL = "http://api.geonames.org/searchJSON?q=" + location + "&maxRows=1&username=triestpa";
					$.getJSON(queryURL)
					.done(function( data ){
						console.log(data);
						var firstmatch = data.geonames[0];
						console.log("Location: " + firstmatch.name);
						console.log("Lat: " + firstmatch.lat);
						console.log("Lng: " + firstmatch.lng);
  					})
					.fail(function( jqxhr, textStatus, error ) {
						var err = textStatus + ", " + error;
						console.log( "Request Failed: " + err );
					});
}