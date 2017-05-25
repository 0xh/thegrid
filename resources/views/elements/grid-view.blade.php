<link rel="import" href="/bower_components/google-map/google-map.html">
<link rel="import" href="/bower_components/google-map/google-map-marker.html">
<link rel="import" href="/bower_components/google-map/google-map-directions.html">
<link rel="import" href="/bower_components/google-map-markerclusterer/google-map-markerclusterer.html">
<link rel="import" href="/bower_components/paper-ripple/paper-ripple.html">
<link rel="import" href="/bower_components/paper-fab/paper-fab.html">
<link rel="import" href="/grid-elements/custom.grid-fab-toolbar">

<dom-module id="grid-view">
	<style include="iron-flex">
		:host {
			background-color: #fffff2;
			/*display: flex; width: 100%; height: 100%; align-items: center; justify-content: center; position: absolute; top: 0; left: 0;*/
			--paper-input-container-input-color: #FFF;
			--paper-input-container-color: #FFF;
			--paper-input-container-underline: #FFF;
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
	</style>
	<template>
		<google-map-markerclusterer id="markerCluster"></google-map-markerclusterer>
		<google-map id="map" api-key="AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc" clientID="AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc" latitude="25.276987" longitude="55.296249" additional-map-options='@{{mapOptions}}' draggable="true" drag-events="true" single-info-window>
		</google-map>
		<!-- <google-map latitude="37.779" longitude="-122.3892" api-key="AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc" zoom="13" disable-default-ui>
  		</google-map>
		<google-map-directions
		  start-address="San Francisco"
		  end-address="Mountain View"
		  api-key="AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc"></google-map-directions> -->
		<div class="view-controls layout vertical">
			<grid-fab-toolbar fab-position="bottom" direction="left">
				<paper-fab icon="search" id="add2" on-tap="searchOpen" mini></paper-fab>
				<paper-toolbar id="toolbar2">
					<div class="item flex">
						{{-- <paper-input label="What" slot="top" class="flex"></paper-input> --}}
						<input type="text" name="" class="tt" placeholder="Search...">
					</div>
				</paper-toolbar>
			</grid-fab-toolbar>

			<div class="flex"></div>
			<paper-fab icon="sort" mini id="sort"></paper-fab>
			<grid-fab-toolbar fab-position="top" direction="left">
				<paper-fab icon="add" id="add" on-tap="searchOpen"></paper-fab>
				<paper-toolbar id="toolbar">
					<div class="item flex">
						{{-- <paper-input label="What" slot="top" class="flex"></paper-input> --}}
						<input type="text" name="" class="tt">
					</div>
					<div class="item flex">
						{{-- <paper-input label="When" slot="top" class="flex"></paper-input> --}}
						<input type="text" name="" class="tt">
					</div>
					<div class="item flex">
						{{-- <paper-input label="Where" slot="top" class="flex"></paper-input> --}}
						<input type="text" name="" class="tt">
					</div>
				</paper-toolbar>
			</grid-fab-toolbar>

		</div>
		{{-- <div style="width: 2px; height: 2px; background-color: #FFFFFF; z-index: 5;"></div> --}}
	</template>
</dom-module>
<script>
	(function(){
		'user strict'

		Polymer({
			is: 'grid-view',
			properties: {
				lat: {
					type: Number,
					value: ''
				},
				lng: {
					type: Number,
					value: ''
				},
				open: {
					type: Boolean,
					value: true,
					reflectToAttribute: true,
				}
			},
			// observers: ['generateLocation(lat, lng)'],
			behaviors: [
				GridBehaviors.MapBehavior,
				GridBehaviors.FoldBehavior
			],
			searchOpen: function() {

			},
			getMarkers: function(lat, lng)  {
		       var markers = [];
		   
		       for (var i = 0; i < 1000; ++i) {
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

		         var latLng = new google.maps.LatLng(newY,
		             newX)
		         var marker = new google.maps.Marker({
		          position: latLng,
//		          icon: markerImage
		         });
		         markers.push(marker);
		       }

		       return markers;
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
				var gMap = this.$.map;
				var gMapCluster = this.$.markerCluster;
				 console.log(gMap.markers);// = gMap.markers;
						  console.log(gMapCluster.markers);// = gMap.markers;
				gMapCluster.map = gMap.map;
						  gMapCluster.markers = gMap.markers;
			},
			ready: function() {
				var self = this;
				var gMap = this.$.map;
				var gMapCluster = this.$.markerCluster;
				//document.querySelector('google-map');
				// gMap.additionalMapOptions = {
				// 	'mapTypeId': 'sattelite',
				// 	'ControlPosition': 'TOP_RIGHT'
				// }
								// console.log(gMap);
				// gMap.dragEvents = true;
				gMap.addEventListener('google-map-ready', function(e) {
					// alert('Map loaded!');
					// console.log(this.map);
					// console.log(gMap.markers);
					// gMapCluster.map = gMap.map;
					if(navigator) {
						navigator.geolocation.getCurrentPosition(function(location) {
						  console.log(location.coords.latitude);
						  console.log(location.coords.longitude);
						  console.log(location.coords.accuracy);
						  gMap.latitude = location.coords.latitude;
						  gMap.longitude = location.coords.longitude;
						  self.generateLocation(gMap.latitude, gMap.longitude);
						  // self.lat = gMap.latitude;
						  // self.lng = gMap.longitude;
						  
						 
						  // console.log(this.getMarkers(lat, lng));
							// this.$.map.appendChild(marker);
							//gMapCluster.markers = self.getMarkers(gMap.latitude, gMap.longitude);
						});
					}

					// console.log(gMapCluster.map);
					// console.log(document.querySelector('google-map-markerclusterer'));
				});
				gMap.addEventListener('google-map-dragend', function(e) {
					// alert('map dragened');
					var center = this.map.getCenter();
					console.log(center.lat(), center.lng());//.getCenter());
				});
				gMap.clickEvents = true;
				gMap.addEventListener('google-map-click', function(e) {
					// alert('map was clicked');
					self.secondFold.close();
					self.drawer.close();
				});
				// var gmap = document.querySelector('google-map');
				// gmap.addEventListener('google-map-ready', function(e) {
				//   document.querySelector('google-map-markerclusterer').map = this.map;
				// });
			}
		});



	}());
</script>