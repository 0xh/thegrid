<link rel="import" href="/bower_components/google-map/google-map.html">
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
		<google-map id="map" api-key="AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc" clientID="AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc" latitude="25.276987" longitude="55.296249" fit-to-markers additional-map-options='@{{mapOptions}}' draggable="true" drag-events="true">
<!-- 		  <google-map-marker latitude="25.276987" longitude="55.296249"
		      draggable="true" title="Go Giants!">
		      	
		      	<div style="position: relative; padding: 8px; height: 100px;">
		      		I am a info window!<br>
		      		<input type="text" name=""><br>
		      		<button type="button">Quick Bid</button>
		      		<paper-ripple></paper-ripple>	
		      	</div>
		      </google-map-marker> -->
		  
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
			behaviors: [GridBehaviors.MapBehavior],
			ready: function() {
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