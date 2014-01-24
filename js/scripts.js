//Log in to facebook
function login() {
			FB.login(function(response) {
				if (response.authResponse) {
					console.log('Welcome!  Fetching your information.... ');
					FB.api('/me', function(response) {
						console.log('Good to see you, ' + response.name + '.');
						console.log('Your location is ' + response.location.name  + '.');
					});
					getMyLocationPoints();
					getFriendsLocationsPoints();
					$('#loginModal').modal('hide');
				} else {
					console.log('User cancelled login or did not fully authorize.');
				}
			}, {scope: 'user_location, friends_location'});
		}

var map;
var center;
var markers = [];
var markerCluster;
var infoBubble_prev = new InfoBubble();
var unlocated = [];

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
 			disableDefaultUI: false,
 			mapTypeControl: false,
 			streetViewControl: false,
 			mapTypeId: google.maps.MapTypeId.ROADMAP
 		}
 		map = new google.maps.Map(map_canvas, map_options);
		    //Associate the styled map with the MapTypeId and set it to display.
		    map.mapTypes.set('map_style', styledMap);
		    map.setMapTypeId('map_style');

		markerCluster = new MarkerClusterer(map, markers, {
          zoomOnClick: false,
          minimumClusterSize: 1,
          averageCenter: true,
          gridSize: 40
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


		google.maps.event.addListener(markerCluster, 'click', function(cluster) {
				var markers = cluster.getMarkers();

				//sort markers by name
				markers.sort(function(a, b){
    				if(a.name < b.name) return -1;
   					if(a.name > b.name) return 1;
   					return 0;
					});

				var people = "<div class=markerCluster list-group>";
    			//Get all the titles
    			for(var i = 0; i < markers.length; i++) {
        			people += markers[i].bubbleRow;
    				}
    			people += '</div>'

    			var infoBubble = new InfoBubble({
          			minWidth: 40,
          			maxWidth: 300,
          			padding: 0,
          			borderWidth: 2,
          			disableAutoPan: false,
          			hideCloseButton: true,
          			content: people
        			});

    			var center = new google.maps.MVCObject;
				center.set('position', cluster.center_);

    			infoBubble_prev.close();
    			infoBubble.open(map, center);
    			infoBubble_prev = infoBubble;
			});
	}

//Add a marker to the map for each person
function addMarker(lat, lng, location, person){
		//windowContent = '<div id="windowContent"> <p class="text-center">' + '  ' + person + '</p></div>'
		Latlng = new google.maps.LatLng(lat, lng);

		windowContent = '<a class=list-group-item href=' + person.link + ' target=_blank>' + person.name + '</a>';

		var markerImage = { 
				url :"http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/images/m1.png",
                size : new google.maps.Size(50, 50),
                origin : new google.maps.Point(0, 0),
                anchor : new google.maps.Point(27,27)
            };

		//Add Location to map
		var marker = new google.maps.Marker({
				position: Latlng,
				//	animation: google.maps.Animation.DROP,
				map: map,
				title: person.name,
				anchorPoint: new google.maps.Point(0,0),
				icon: markerImage
			});
		marker['bubbleRow'] = windowContent;
		marker['name'] = person.name;

		markers.push(marker);
		markerCluster.addMarker(marker);
}


//add unlocated friend to "Unable to Locate" list
function addUnlocated(friend){
	unlocated.push(friend);
}


//Get the location of the user and mark it on the map
function getMyLocationPoints() {
			FB.api('/me', {fields: 'name, location, link'}, function(response) {
				console.log('Hello, ' + response.name + '.'); 
				var location = response.location.name;    
				console.log('Your location is ' +  location + '.');
				findLocation(response);
			});
}

//Get the locations of all of the user's friends and mark them on the map
function getFriendsLocationsPoints() {
	var friendLatlng;
	var locationTitle;
	FB.api('me/friends', {fields: 'name, location, link'}, function(response) {
		$.each(response.data,function(index,friend) {
				if (typeof friend.location === "undefined") {
					addUnlocated(friend);
				}
				else {
					findLocation(friend);
					}
			});
	});
}

//Find the location coordinates using the FB graph api
function findLocation(friend) {
	FB.api('/'+friend.location.id, function(GEOresponse) {
					if (!GEOresponse || GEOresponse.error) {
						console.log("API Response Error: " + GEOresponse.error.message);
						queryGeoNames(friend.location.name, friend);
					}
					else {
						addMarker(GEOresponse.location.latitude, GEOresponse.location.longitude, GEOresponse.name, friend);
							}
						});
}

//RESTful query to geonames.org if FB query fails
function queryGeoNames(location, friend) {
	var queryURL = "http://api.geonames.org/searchJSON?q=" + location + "&maxRows=1&username=triestpa";
					$.getJSON(queryURL)
					.done(function( data ){
						var firstmatch = data.geonames[0];
						console.log("Location: " + firstmatch.name + ", " + firstmatch.lat + ", " + firstmatch.lng);
						addMarker(firstmatch.lat, firstmatch.lng, location, friend);
  					})
					.fail(function( jqxhr, textStatus, error ) {
						var err = textStatus + ", " + error;
						console.log( "Request Failed: " + err );
					});
}
