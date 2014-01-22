<!DOCTYPE html>
<html>
<head>
	<title>Friendly Earth</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<script src="js/jquery.js"></script>

	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
	<link href="css/my-styles.css" rel="stylesheet" media="screen">

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5jiD2pw8yK6dyh1brz_0jEBcjb7AzcUg&amp;sensor=false"></script>
	<script src="js/markerclusterer_packed.js"></script>
	<script src="js/infobubble-compiled.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/scripts.js"></script>

	<script>

	google.maps.event.addDomListener(window, 'load', function(){

		initialize();
		$("#map_canvas").height($(window).height() - 50);

			//The map will re-center when the page size changes
			google.maps.event.addDomListener(map, 'idle', function(){
				center = map.getCenter();
			});


			//if user is not yet logged in, prompt a loggin
			FB.getLoginStatus(function(response) {
  				if (response.status === 'connected') {
  					console.log("User is already logged in")
  					getFriendsLocationsPoints();
  				} else {
  		 			$('#loginModal').modal('show')
 		 		}
	 		});
	});

	$(window).resize(function(){
		map.setCenter(center);
		$("#map_canvas").height($(window).height() - 50);
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

		  	//if user is not yet logged in, prompt a loggin
			FB.getLoginStatus(function(response) {
  				if (response.status === 'connected') {
  					console.log("User is already logged in")
  					getFriendsLocationsPoints();
  				} else {
  		 			$('#loginModal').modal('show')
 		 		}
	 		});

		  </script>

		  <!-- Navbar -->
		  <div class="navbar navbar-default navbar-fixed-top">
		  	<div class="container">

		  		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		  			<span class="icon-bar"></span>
		  			<span class="icon-bar"></span>
		  			<span class="icon-bar"></span>
		  		</button>

		  		<a class="navbar-brand text-muted" href="#">Friendly Earth</a>
		  		<div class="collapse navbar-collapse">
		  			<ul class="nav navbar-nav navbar-right">
		  				<li id='loginPane'><a href="#">Login</a></li>
		  				<li class="active"><a href="#">Map</a></li>
		  				<li id='infoPane'><a href="#">About</a></li>
		  			</ul>
		  		</div>
		  	</div>
		  </div>
		  <!-- End navbar -->

		  <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  	<div class="modal-dialog">
		  		<div class="modal-content">
		  			<div class="modal-header">
		  				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		  				<h4 class="modal-title" id="myModalLabel">Login</h4>
		  			</div>
		  			<div class="modal-body">
		  				<button id="Login" class="btn btn-default testButtons"> Log In </button> 
		  				<button id="Logout" class="btn btn-default testButtons"> Log Out </button> 		  			</div>
		  			<div class="modal-footer">
		  				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  			</div>
		  		</div>
		  	</div>
		  </div>

		  <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  	<div class="modal-dialog">
		  		<div class="modal-content">
		  			<div class="modal-header">
		  				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		  				<h4 class="modal-title" id="myModalLabel">About</h4>
		  			</div>
		  			<div class="modal-body">
		  				Info Text Here
		  				</div>
		  			<div class="modal-footer">
		  				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  			</div>
		  		</div>
		  	</div>
		  </div>

		  <div id="map_canvas"></div>

		  		<script>
	  				//Set the buttons
	  				document.getElementById("Login").onclick = login;

	  				document.getElementById("Logout").onclick = function(){
	  					FB.logout(function(response) {
	  						console.log("User is logged out");
	  					});
	  				};

	  				document.getElementById("loginPane").onclick = function(){
	  					$('#loginModal').modal('show');
	  				};

	  				document.getElementById("infoPane").onclick = function(){
	  					$('#infoModal').modal('show');
	  				};

	  			</script>
	  		</div>
	  	</body>
	  	</html>