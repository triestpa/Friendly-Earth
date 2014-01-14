<!DOCTYPE html>
<html>
<head>
	<title>FriendMap</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<script src="js/jquery.js"></script>

	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
	<link href="css/my-styles.css" rel="stylesheet" media="screen">

	<script src="js/bootstrap.min.js"></script>

	<!-- Load the google map api -->
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5jiD2pw8yK6dyh1brz_0jEBcjb7AzcUg&amp;sensor=false"></script>
	<script>

	var map;
	var center;
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
 			disableDefaultUI: true,
 			mapTypeId: google.maps.MapTypeId.ROADMAP
 		}
 		map = new google.maps.Map(map_canvas, map_options);
		    //Associate the styled map with the MapTypeId and set it to display.
		    map.mapTypes.set('map_style', styledMap);
		    map.setMapTypeId('map_style');
		}


		google.maps.event.addDomListener(window, 'load', function(){

			initialize();

			//The map will re-center when the page size changes
			google.maps.event.addDomListener(map, 'idle', function(){
				center = map.getCenter();
			});

		});

		$(window).resize(function(){
			map.setCenter(center);
		});
		</script>

		</head>
		<body>

		<!-- Setup Facebook Integraton -->
		<div id="fb-root"></div>
		
		<script>
			window.fbAsyncInit = function() {
		    // init the FB JS SDK
		   	FB.init({
		      appId      : '681219568576604',                    // App ID from the app dashboard
		      status     : true,                                 // Check Facebook Login status
		      xfbml      : true                                  // Look for social plugins on the page
		  	});

	    	// Additional initialization code such as adding Event Listeners goes here
		       
			};

		  // Load the SDK asynchronously
		  (function(){
		     // If we've already installed the SDK, we're done
		     if (document.getElementById('facebook-jssdk')) {return;}

		     // Get the first script element, which we'll use to find the parent node
		     var firstScriptElement = document.getElementsByTagName('script')[0];

		     // Create a new script element and set its id
		     var facebookJS = document.createElement('script'); 
		     facebookJS.id = 'facebook-jssdk';

		     // Set the new script's source to the source of the Facebook JS SDK
		     facebookJS.src = "//connect.facebook.net/en_US/all.js";

		     // Insert the Facebook JS SDK into the DOM
		     firstScriptElement.parentNode.insertBefore(facebookJS, firstScriptElement);
		 }());

	  </script>

	  <!-- Navbar -->
	  <div class="navbar navbar-default navbar-fixed-top">
	  	<div class="container">

	  		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	  			<span class="icon-bar"></span>
	  			<span class="icon-bar"></span>
	  			<span class="icon-bar"></span>
	  		</button>

	  		<a class="navbar-brand text-muted" href="#">CouchIt</a>
	  		<div class="collapse navbar-collapse">
	  			<ul class="nav navbar-nav navbar-right">
	  				<li class="active"><a href="#">Map</a></li>
	  				<li><a href="#">Overview</a></li>
	  				<li><a href="#">About Us</a></li>
	  				<li><a href="#">Future Plans</a></li>
	  			</ul>

	  		</div>
	  	</div>
	  </div>
	  <!-- End navbar -->


	  <div class="jumbotron">  
	  	<div class="container">  
	  		<h1>The CouchIt Map</h1>
	  		<p class="lead">Find a friend to crash with!</p>
	  	</div>
	  </div>

	  <div class="row">
	  	<div class="col-sm-9">
	  		<div id="map_canvas" class=""></div>
	  	</div>

	  	<div class="col-sm-3" id="friend-list">

	 	<button id="Login" class="btn btn-default testButtons"> Log In </button> 
	 	<button id="Logout" class="btn btn-default testButtons"> Log Out </button> 
	 	<button id="LoadList" class="btn btn-default testButtons"> Load List</button> 
	 	<button id="LoadLocation" class="btn btn-default testButtons"> Load Location</button> 



	  	<script>
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
			//-Set Log In button:
			document.getElementById("Login").onclick = login;
			
			document.getElementById("Logout").onclick = function(){
				FB.logout(function(response) {
					console.log("User is logged out");
					});
				};

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

	  		document.getElementById("LoadList").onclick = loadFriendList;

	  		function getLocationPoints() {
	  			FB.api('/me', function(response) {
	       			console.log('Hello, ' + response.name + '.'); 
	       			var location = response.location.name;       			
	       			console.log('Your location is ' +  location + '.');

	       			var queryURL = "http://api.geonames.org/searchJSON?q=Grinnell,%20IA&maxRows=10&username=triestpa";
	       			
	       			$.getJSON(queryURL)
	       			.done(function( data ){
	       				console.log(data);
	       				console.log(data.geonames[0]);
	       			})
	       			.fail(function( jqxhr, textStatus, error ) {
    					var err = textStatus + ", " + error;
    					console.log( "Request Failed: " + err );
					});

	       			/*
	       			var xmlHttp = null;
    				xmlHttp = new XMLHttpRequest();
    				xmlHttp.open( "GET", queryURL, false );
    				xmlHttp.send( null );

    				var respJSON = xmlHttp.;
    				console.log(respJSON.geonames.geoname[0]);
   					//console.log(xmlHttp.responseText);
   					*/
	     			});
	  		}

	     	document.getElementById("LoadLocation").onclick = getLocationPoints;

		</script>

	  	<div class="list-group" id ="friendList"></div>

	  	</div>
	  </div>
	</body>
</html>