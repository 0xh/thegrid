<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc">
</script>
<script type="text/javascript">
	// map-options

(function(window, google, mapster) {
	mapster.MAP_OPTIONS = {
		center: {
			lat: 37.2342,
			lng: -122.34123
		},
		zoom: 10,
		disableDefaultUI: true,
		scrollwheel: true,
		draggable: true,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		zoomControlOptions: {
			position: google.maps.ControlPosition.BOTTOM_LEFT,
			style: google.maps.ZoomControlStyle.DEFAULT
		},
		panControlOptions: {
			position: google.maps.ControlPosition.LEFT_BOTTOM
		},
		cluster: {
			options: {
				style: [{
					url: '',
					height: 56,
					width: 56,
					textColor: '#FFF',
					textSize: 18
				}]
			}
		},
		geocoder: true
	}
}(window, google, window.Mapster || (window.Mapster = {})));

(function(window) {
	var List = (function() {
		function List(params) {
			this.items = [];
		}
		List.prototype = {
			add: function(item) {
				this.items.push(item);
			},
			remove: function(item) {
				var indexOf = this.items.indexOf(marker);
				if (indexOf !== -1) {
					this.items.splice(indexOf, 1);
				}
			},
			find: function(callback, action) {
				var callbackReturn,
					items = this.items,
					length = items.length,
					matches = [],
					i = 0;
				for(; i < length; i++) {
					callbackReturn = callback(items[i], i);
					if(callbackReturn) {
						matches.push(items[i]);
					}
				}

				if (action) {
					action.call(this, matches);
				}

				return matches;
			}
		};
		return List;
	}());

	List.create = function(params) {
		return new List(params);
	}

	window.List = List;
}(window));

(function(window, google, List) {
	var Mapster = (function() {
		function Mapster(element, opts) {
			console.log('element map', element);
			this.gMap = new google.maps.Map(element, opts);

			this.markers = List.create();
			if (opts.cluster) {
				this.markerClusterer = new markerClusterer(this.gMap, [], opts.cluster.options);
			}
			if (opts.geocoder) {
				this.geocoder = new google.maps.Geocoder();
			}
		}
		Mapster.prototype = {
			zoom: function(level) {
				if(level) {
					this.gMap.setZoom(level);
				} else {
					return this.gMap.getZoom();
				}
			},
			_on: function(opts) {
				var self = this;
				google.maps.event.addListener(opts.obj, opts.event, function(e) {
					opts.callback.call(self, e, opts.obj);
				});
			},
			geocode: function(opts) {
				geocoder.geocode({
					address: opts.address
				}, function(results, status) {
					if (status === google.maps.GeocoderStatus.OK) {
						opts.success.call(this, results, status);
					} else {
						opts.error.call(this.status);
					}
				});
			},
			getCurrentPosition: function(callback) {
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(function(position) {
						callback.call(this, position);
					})
				}
			},
			addMarker: function(opts) {
				var marker
					self = this;
				opts.position = {
					lat: opts.lat,
					lng: opts.lng
				};
				marker = this._createMarker(opts);
				if(this.markerClusterer) {
					this.markerClusterer.addMarker(marker);
				}
				this._addMarker(marker);
				if (opts.events) {
					this._attachEvents(marker, opts.events);
				}
				if (opts.content) {
					this._on({
						obj: marker,
						event: 'click',
						callback: function() {
							var infoWindow = new google.maps.InfoWindow({
								content: opts.content
							});
							infoWindow.open(this.gMap, marker);
						}
					});
				}
				return marker;
			},
			_attachEvents: function(obj, event) {
				var self = this;
				events.forEach(function(event) {
					self._on({
						obj: obj,
						event: event.name,
						callback: event.callback
					});
				});
			},
			_addMarker: function(marker) {
				this.markers.push(marker);
			},
			findBy: function(callback) {
				return this.markers.find(callback);
			},
			removeBy: function(callback) {
				var self = this;
				self.marker.find(callback, function(markers) {
					markers.forEach(function(marker) {
						if (self.markerClusterer) {
							self.markerClusterer.removeMarker(marker);
						} else {
							marker.setMap(null);
						}
					});
				});
			},
			_createMarker: function(opts) {
				opts.map = this.gMap;
				return new google.maps.Marker(opts);
			}
		};

		return Mapster;
	}());

	Mapster.create = function() {
		return new Mapster();
	}
	window.Mapster = Mapster;
}(window, google, List));

/*(function(window, mapster) {

	// map options
	var options = mapster.MAP_OPTIONS,
	element = document.getElementById('map-canvas'),
	// map
	map = mapster.create(element, options);
	// map.zoom(1);

	// map._on('click', function(e) {
	// 	alert('click');
	// });
	var marker = map.addMarker({
		lat: 13123,
		lng: asdf
	});

	var infoWindow = new google.maps.


}(window, window.Mapster)); */
</script>