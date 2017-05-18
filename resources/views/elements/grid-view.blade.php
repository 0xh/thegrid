<link rel="import" href="/bower_components/google-map/google-map.html">
<link rel="import" href="/bower_components/google-map/google-map-marker.html">
<link rel="import" href="/bower_components/google-map/google-map-directions.html">
<link rel="import" href="/bower_components/paper-ripple/paper-ripple.html">
<link rel="import" href="/bower_components/paper-fab/paper-fab.html">

<dom-module id="grid-view">
	<style>
		:host {
			background-color: #fffff2;
			display: flex; width: 100%; height: 100%; align-items: center; justify-content: center; position: absolute; top: 0; left: 0;
		}
		.view-controls {
			position: fixed;
		    bottom: 10px;
		    right: 10px;
		    text-align: center;
		    z-index: 1;
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
	</style>
	<template>
		<google-map id="map" api-key="AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc" clientID="AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc" latitude="25.276987" longitude="55.296249" {{-- fit-to-markers --}} additional-map-options='@{{mapOptions}}' draggable="true" drag-events="true" single-info-window>
<!-- 		  <google-map-marker latitude="25.276987" longitude="55.296249"
		      draggable="true" title="Go Giants!">
		      	
		      	<div style="position: relative; padding: 8px; height: 100px;">
		      		I am a info window!<br>
		      		<input type="text" name=""><br>
		      		<button type="button">Quick Bid</button>
		      		<paper-ripple></paper-ripple>	
		      	</div>
		      </google-map-marker> -->
		  	{{-- <google-map-marker id="marker" latitude="25.276987" longitude="55.296249"></google-map-marker> --}}
		</google-map>
		<!-- <google-map latitude="37.779" longitude="-122.3892" api-key="AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc" zoom="13" disable-default-ui>
  		</google-map>
		<google-map-directions
		  start-address="San Francisco"
		  end-address="Mountain View"
		  api-key="AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc"></google-map-directions> -->
		<div class="view-controls">
			<paper-fab icon="sort" mini id="sort"></paper-fab>
			<paper-fab icon="search" id="add"></paper-fab>
		</div>
			<div style="width: 2px; height: 2px; background-color: #FFFFFF; z-index: 5;"></div>
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
				}
			},
			// observers: ['generateLocation(lat, lng)'],
			behaviors: [GridBehaviors.MapBehavior],
			generateLocation: function(lat, lng) {
				for( var i = 0; i <= 9; i++) {
					var r = 500/111300 // = 500 meters
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
					console.log('aaa', newY, newX);

					var marker = document.createElement('google-map-marker');
					marker.setAttribute('latitude', newY);
					marker.setAttribute('longitude', newX);
					marker.setAttribute('icon', 'data:image/svg+xml,<svg%20xmlns%3D"http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg"%20width%3D"38"%20height%3D"38"%20viewBox%3D"0%200%2038%2038"><path%20fill%3D"%23808080"%20stroke%3D"%23ccc"%20stroke-width%3D".5"%20d%3D"M34.305%2016.234c0%208.83-15.148%2019.158-15.148%2019.158S3.507%2025.065%203.507%2016.1c0-8.505%206.894-14.304%2015.4-14.304%208.504%200%2015.398%205.933%2015.398%2014.438z"%2F><text%20transform%3D"translate%2819%2018.5%29"%20fill%3D"%23fff"%20style%3D"font-family%3A%20Arial%2C%20sans-serif%3Bfont-weight%3Abold%3Btext-align%3Acenter%3B"%20font-size%3D"12"%20text-anchor%3D"middle">'+i+'<%2Ftext><%2Fsvg>');
					marker.innerHTML = 'I am a marker';
					Polymer.dom(this.$.map).appendChild(marker);
				}
				// this.$.map.appendChild(marker);

			},
			ready: function() {
				var self = this;
				var gMap = this.$.map;//document.querySelector('google-map');
				// gMap.additionalMapOptions = {
				// 	'mapTypeId': 'sattelite',
				// 	'ControlPosition': 'TOP_RIGHT'
				// }
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
					});
				}
				console.log(gMap);
				// gMap.dragEvents = true;
				gMap.addEventListener('google-map-ready', function(e) {
					// alert('Map loaded!');
				});
				gMap.addEventListener('google-map-dragend', function(e) {
					// alert('map dragened');
					var center = this.map.getCenter();
					console.log(center.lat(), center.lng());//.getCenter());
				});

			}
		});

	}());
</script>