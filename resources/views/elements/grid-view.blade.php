<link rel="import" href="/bower_components/google-map/google-map.html">
<link rel="import" href="/bower_components/paper-ripple/paper-ripple.html">
<link rel="import" href="/bower_components/paper-fab/paper-fab.html">
<link rel="import" href="/bower_components/paper-button/paper-button.html">
<link rel="import" href="/bower_components/paper-icon-button/paper-icon-button.html">
<link rel="import" href="/bower_components/iron-icons/maps-icons.html">
<link rel="import" href="/bower_components/vaadin-date-picker/vaadin-date-picker-light.html">
<link rel="import" href="/grid-elements/custom.grid-fab-toolbar">
<link rel="import" href="/grid-elements/custom.grid-info-window">
<link rel="import" href="/grid-elements/scripts.axios">
{{-- <link rel="import" href="/grid-elements/custom.grid-mapster"> --}}

<dom-module id="grid-view">
	<style include="iron-flex">
		:host {
			background-color: #fffff2;
			/*display: flex; width: 100%; height: 100%; align-items: center; justify-content: center; position: absolute; top: 0; left: 0;*/
			/*--paper-input-container-input-color: #FFF;*/
			/*--paper-input-container-color: #FFF;*/
			/*--paper-input-container-underline: #FFF;*/
		}
		.view-controls {
			/*position: fixed;
		    bottom: 10px;
		    right: 10px;
		    text-align: center;
		    z-index: 1;*/
		    height: 100%;
		    width: 100%;
		}
		#add {
			--paper-fab-background: #E00008;
		}
		#sort {
			--paper-fab-background: #FAFAFA;
			margin: 10px auto;
			color: #737373;
		}
		google-map {
			position: absolute;
			top: 0;
			left: 0;
			height: 100%;
			width: 100%;
		}
		/*#toolbar {
			position: fixed;
		    bottom: 0;
		    right: 0;
		    text-align: left;
		    width: calc(100% - 52px);
		    background-color: #f00;
		    opacity: 0;

		    -webkit-transition-duration: .5s;
		    transition-duration: .5s;
		    -webkit-transition-timing-function: cubic-bezier(.35,0,.25,1);
		    transition-timing-function: cubic-bezier(.35,0,.25,1);
		    -webkit-transition-property: background-color,fill,color;
		    transition-property: background-color,fill,color;
		}
		#toolbar paper-input {
			margin-right: 5px;
		}
		#toolbar paper-input:last-child {
			margin-right: 0px;
		}*/
		.tt {
			background-color: transparent;
			border: 1px solid #f35c62;
			line-height: 24px;
			color: #FFFFFF;
			padding: 2px 10px;
			width: 100%;
		}
		grid-fab-toolbar {
			/*position: fixed;
		    top: 0;
		    right: 0;
		    width: calc(100% - var(--grid-drawer-collapse-width));*/
		    width: 100%
		}
		#add2 {
			--paper-fab-background: #FAFAFA;
			color: #737373;
		}
		.item {
			padding: 2px 5px;
		}
		.done {
			background-color: #FFFFFF;
			width: 100%;
			color: #737373;
		}
		.center-container {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			display: flex; 
			width: 100%; 
			height: 100%;
			align-items: center; 
			justify-content: center;
		}
		#centerMarker {
			width: 40px; 
			height: 40px; 
			background-color: transparent; 
			z-index: 5;
			/*display: none;*/
			transform: scale(0);
			overflow: visible;

		}
		#centerMarker img {
			width: 100%;
			height: 100%;
			margin-top: -20px;
		}
		#centerMarker.bounce {
			animation: bounce 1s 0s;
			transform: scale(1);
		}
		#centerMarker.debounce {
			animation: debounce 1s 0s;
			transform: scale(0);
		}
		@keyframes bounce {
			0% { transform: scale(0); }
			10% { transform: scale(1.1); }
			50% { transform: scale(1.6); }
			60% { transform: scale(0.6); }
			80% { transform: scale(0.95); }
			90% { transform: scale(0.85); }
			100% { transform: scale(1); }
		}
		@keyframes debounce {
			0% { transform: scale(1); }
			10% { transform: scale(0.85); }
			50% { transform: scale(0.95); }
			60% { transform: scale(0.6); }
			80% { transform: scale(1.6); }
			90% { transform: scale(1.1); }
			100% { transform: scale(0); }			
		}
	</style>
	<dom-bind>
		<template>
			<google-map id="map" class="center-container" api-key="[[apiKey]]" clientID="[[apiKey]]" latitude="25.276987" longitude="55.296249" additional-map-options='@{{mapOptions}}' draggable="true" drag-events="true" single-info-window map="@{{map}}" fit-to-markers>
				<div id="centerMarker">
					<img src="http://icons.iconarchive.com/icons/paomedia/small-n-flat/1024/map-marker-icon.png" alt="icon" />
				</div>
			</google-map>
			<div class="view-controls layout vertical">
				<grid-fab-toolbar fab-position="bottom" direction="left">
					<paper-fab icon="search" id="add2" on-tap="searchOpen" mini></paper-fab>
					<paper-toolbar id="toolbar2">
						<div class="item flex">
							<input id="" type="text" name="" class="tt" placeholder="Search...">
						</div>
					</paper-toolbar>
				</grid-fab-toolbar>
				<div class="flex"></div>
				<paper-fab icon="sort" mini id="sort"></paper-fab>
				@if (Auth::check())
				<grid-fab-toolbar id="addFabToolbar" fab-position="top" direction="left">
					<paper-fab icon="add" id="add" on-tap="searchOpen"></paper-fab>
					<paper-toolbar id="toolbar">
						<div class="item flex">
							<label>What</label>
							<input type="text" name="" class="tt" value="@{{what::input}}">
						</div>
						<div class="item flex">
							<label>When</label>
							<input is="iron-input" type="date" name="" class="tt" value="@{{when::input}}">
						</div>
						<div class="item flex">
							<label>Where</label>
							<input id="gftwhere" type="text" name="" class="tt" value="@{{where::input}}">
						</div>
						<div class="item">
							<paper-icon-button icon="maps:my-location" on-tap="setCurrentLocation"></paper-icon-button>
						</div>
						<div class="item">
							<paper-icon-button icon="chevron-right" on-tap="showAddJobPane"></paper-icon-button>
						</div>
					</paper-toolbar>
				</grid-fab-toolbar>
				@endif
			</div>
		</template>
	</dom-bind>
</dom-module>
<script>
	(function(){
		'user strict'

		Polymer({
			is: 'grid-view',
			properties: {
				lat: {
					type: String,
					value: ''
				},
				lng: {
					type: String,
					value: ''
				},
				open: {
					type: Boolean,
					value: true,
					reflectToAttribute: true,
				},
				query: {
					type: String,
					notify: true
				},
				what: {
					type: String,
					notify: true
				},
				when: {
					type: String,
					notify: true
				},
				where: {
					type: String,
					value: '',
					notify: true
				},
				isAddingJob: {
					type: Boolean,
					value: false,
					notify: true
				},
			},
			observers: [
			'updateAddJobPane(what, when, where, lat, lng)'
			],
			behaviors: [	
			GridBehaviors.FoldBehavior,
			GridBehaviors.TabsBehavior,
			GridBehaviors.MapBehavior
			],
			searchOpen: function() {

			},
		   	generateLocation: function(lat, lng) {
		   		for( var i = 0; i <= 50; i++) {
					var r = 1000/111300 // = 500 meters
					, y0 = lat
					, x0 = lng
					, u = Math.random()
					, v = Math.random()
					, w = r * Math.sqrt(u)
					, t = 2 * Math.PI * v
					, x = w * Math.cos(t)
					, y1 = w * Math.sin(t)
					, x1 = x / Math.cos(y0);

					var newY = y0 + y1,
					newX = x0 + x1;
					// console.log('aaa', newY, newX);

					var marker = document.createElement('google-map-marker');
					marker.setAttribute('latitude', newY);
					marker.setAttribute('longitude', newX);
					marker.setAttribute('icon', 'data:image/svg+xml,<svg%20xmlns%3D"http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg"%20width%3D"38"%20height%3D"38"%20viewBox%3D"0%200%2038%2038"><path%20fill%3D"%23808080"%20stroke%3D"%23ccc"%20stroke-width%3D".5"%20d%3D"M34.305%2016.234c0%208.83-15.148%2019.158-15.148%2019.158S3.507%2025.065%203.507%2016.1c0-8.505%206.894-14.304%2015.4-14.304%208.504%200%2015.398%205.933%2015.398%2014.438z"%2F><text%20transform%3D"translate%2819%2018.5%29"%20fill%3D"%23fff"%20style%3D"font-family%3A%20Arial%2C%20sans-serif%3Bfont-weight%3Abold%3Btext-align%3Acenter%3B"%20font-size%3D"12"%20text-anchor%3D"middle">'+i+'<%2Ftext><%2Fsvg>');
					marker.innerHTML = 'I am a marker';
					Polymer.dom(this.$.map).appendChild(marker);
					console.log('adad');
				}
			},
			updateAddJobPane: function(what, when, where, lat, lng) {
				var addJobPane = this.secondFold.$.addJob;
				addJobPane.what = what;
				addJobPane.when = when;
				addJobPane.where = where;
				addJobPane.lat = lat;
				addJobPane.lng = lng;
			},
			showAddJobPane: function() {
				this.updateAddJobPane(this.what, this.when, this.where, this.lat, this.lng);
				this.secondFold.selectedTab = 'add-job';
				this.secondFold.open();
				this.$.addFabToolbar.close();
			},
			geocodeLatLng: function() {
				var self = this;
				var latlng = {
					lat: parseFloat(this.lat),
					lng: parseFloat(this.lng)
				};
				var geocoder = new google.maps.Geocoder;
				return new Promise(function(resolve,reject) {
					geocoder.geocode({'location': latlng}, function(results, status) {
						if (status === 'OK') {
							if (results[1]) {
								resolve(results);
							} else {
								reject(status);
							}
						} else {
							reject(status);
						}
					});
				});
			},
			setCurrentLocation: function() {
				var gMap = this.$.map;
				var self = this;
				this.getCurrentPosition(function(location) {
					gMap.latitude = location.coords.latitude;
					gMap.longitude = location.coords.longitude;
					self.lat = gMap.latitude;
					self.lng = gMap.longitude;
					self.geocodeLatLng()
					.then(function(results) {
						self.where = results[0].formatted_address;
					})
					.catch(function(status) {
						console.log(status);
					});
				});
			},

			_newMarker: function(data) {
				var customPin = {
					// path: 'M34.305 16.234c0 8.83-15.148 19.158-15.148 19.158S3.507 25.065 3.507 16.1c0-8.505 6.894-14.304 15.4-14.304 8.504 0 15.398 5.933 15.398 14.438z',
					path: 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z',
					fillColor: data.fillColor,
					fillOpacity: 1,
					scale: 1,
					strokeColor: data.fillColor,
					strokeWeight: 0.5
				};
				var d = JSON.stringify(data),
					content = document.createElement('grid-info-window');
				content.data = d;
				content.isMyPost = data.isMyPost;
				content.isBidded = data.isBidded;

				this.addMarker({
					lat: parseFloat(data.lat),
					lng: parseFloat(data.lng),
					icon: customPin,
					content: content,
					label: {
						text: "AED " + data.price,
						color: '#FFF',
						fontSize: '10px',
						fontWeight: 'normal',
						textAlign: 'center',
						width: '100px'
					}
				});
			},

			generateMarkers: function() {
				console.log('generating markers');
				var markers = this.markers;
				var self = this;
				axios.get('/job/all')
				.then(function (response) {
					var data = response.data;
					if (markers) {
						markers.forEach(function(marker) {
							marker.setMap(null);
						});
					}
					
					for(var i = 0; i < data.length; i++) {
							data[i].fillColor = '#FFF';
							data[i].isMyPost = false;
							data[i].isBidded = false;
						var bids = data[i].bids;
						if(Grid.user_id) {
							if(Grid.user_id == data[i].user_id) {
								data[i].fillColor = '#00872d';
								data[i].isMyPost = true;
							}
							for(var j = 0; j < bids.length; j++) {
								// Check if user is already bidded
								if(Grid.user_id == bids[j].user_id) {
									data[i].fillColor = '#f1f422';
									data[i].isBidded = true;
								}
							}
						}
						
						self._newMarker(data[i]);
					}
				});
			},
			ready: function() {
				var self = this;
				var gMap = this.$.map;
				
				gMap.addEventListener('google-map-ready', function(e) {
					self.google = google;
					self.gMap = this.map;
					socket.on('set-map', function(data) {
						console.log(data);
						self.lat = data.lat;
						self.lng = data.lng;
						self.gMap.setCenter(new google.maps.LatLng(self.lat, self.lng));
					});

					socket.on('add-marker', function(data) {
						data.fillColor = '#FFF';
						data.isMyPost = false;
						data.isBidded = false;
						self._newMarker(data);
					});
					
					self.markerClusterer = new MarkerClusterer(this.map, [], { imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m' });
					this.map.setOptions({ zoomControlOptions: { position: google.maps.ControlPosition.RIGHT_CENTER } });
					self.getCurrentPosition(function(location) {
						gMap.latitude = location.coords.latitude;
						gMap.longitude = location.coords.longitude;
						self.generateMarkers();
					});

					var markers = [];
					var input = document.getElementById('gftwhere');

					if(input) {
						var searchBox = new google.maps.places.SearchBox(input);

						google.maps.event.addListener(searchBox, 'places_changed', function() {
							self.where = input.value;
							var places = searchBox.getPlaces();
							if (places.length == 0) {
								return;
							}
							var bounds = new google.maps.LatLngBounds();
							for (var i = 0, place; place = places[i]; i++) {
								bounds.extend(place.geometry.location);
							}
							self.gMap.fitBounds(bounds);
						});

						google.maps.event.addListener(self.gMap, 'bounds_changed', function() {
							var bounds = self.gMap.getBounds();
							searchBox.setBounds(bounds);
						});
					}

				});
				gMap.addEventListener('google-map-dragend', function(e) {
					var center = this.map.getCenter();
					self.lat = center.lat();
					self.lng = center.lng();
					
					if(self.isAddingJob) {
						var geocoder = new google.maps.Geocoder;
						self.geocodeLatLng(geocoder, this.map)
						.then(function(results) {
							self.where = results[0].formatted_address;
						})
						.catch(function(status) {
						});
						document.getElementById('gftwhere').value = self.where;
					}

					socket.emit('google-map-dragend', {
						lat: self.lat,
						lng: self.lng
					});
				});
				gMap.clickEvents = true;

				gMap.addEventListener('google-map-click', function(e) {
					self.secondFold.close();
					self.drawer.close();
				});

				if(Grid.authenticated) {
					this.$.addFabToolbar.addEventListener('grid-fab-toolbar-opened', function(e) {
						self.$.centerMarker.classList.add("bounce");
						self.$.centerMarker.classList.remove("debounce");
						self.isAddingJob = true;
					});

					this.$.addFabToolbar.addEventListener('grid-fab-toolbar-closed', function(e) {
						self.$.centerMarker.classList.add("debounce");
						self.$.centerMarker.classList.remove("bounce");
						self.isAddingJob = false;
					});
				}
			}
		});
	}());
</script>