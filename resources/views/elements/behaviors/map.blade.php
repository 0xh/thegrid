<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
<script>
    window.GridBehaviors = window.GridBehaviors || {};

    GridBehaviors.MapBehavior = {
        properties: {
            mapOptions: {
                type: Object,
                value: function() {
                    return {
                        'dragEvents': true,
                        'clickEvents': true,
                        'mapTypeControl': false,
                        'streetViewControl': false,
                        'mapTypeId': 'roadmap',
                        'disableDefaultUI': true,
                        'panControl': false,
                        'zoomControl': true,
                        'zoom': 16,
                        'styles': 
                        [
                            {
                                "featureType": "all",
                                "elementType": "labels",
                                "stylers": [
                                    {
                                        "visibility": "on"
                                    }
                                ]
                            },
                            {
                                "featureType": "all",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "saturation": 36
                                    },
                                    {
                                        "color": "#000000"
                                    },
                                    {
                                        "lightness": 40
                                    }
                                ]
                            },
                            {
                                "featureType": "all",
                                "elementType": "labels.text.stroke",
                                "stylers": [
                                    {
                                        "visibility": "on"
                                    },
                                    {
                                        "color": "#000000"
                                    },
                                    {
                                        "lightness": 16
                                    }
                                ]
                            },
                            {
                                "featureType": "all",
                                "elementType": "labels.icon",
                                "stylers": [
                                    {
                                        "visibility": "off"
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative",
                                "elementType": "geometry.fill",
                                "stylers": [
                                    {
                                        "color": "#000000"
                                    },
                                    {
                                        "lightness": 20
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative",
                                "elementType": "geometry.stroke",
                                "stylers": [
                                    {
                                        "color": "#000000"
                                    },
                                    {
                                        "lightness": 17
                                    },
                                    {
                                        "weight": 1.2
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative.country",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#e5c163"
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative.locality",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#c4c4c4"
                                    }
                                ]
                            },
                            {
                                "featureType": "administrative.neighborhood",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    }
                                ]
                            },
                            {
                                "featureType": "landscape",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#000000"
                                    },
                                    {
                                        "lightness": 20
                                    }
                                ]
                            },
                            {
                                "featureType": "poi",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#000000"
                                    },
                                    {
                                        "lightness": 21
                                    },
                                    {
                                        "visibility": "on"
                                    }
                                ]
                            },
                            {
                                "featureType": "poi.business",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "visibility": "on"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway",
                                "elementType": "geometry.fill",
                                "stylers": [
                                    {
                                        "color": "#575757"
                                    },
                                    {
                                        "lightness": "0"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway",
                                "elementType": "geometry.stroke",
                                "stylers": [
                                    {
                                        "visibility": "off"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.highway",
                                "elementType": "labels.text.stroke",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.arterial",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#000000"
                                    },
                                    {
                                        "lightness": 18
                                    }
                                ]
                            },
                            {
                                "featureType": "road.arterial",
                                "elementType": "geometry.fill",
                                "stylers": [
                                    {
                                        "color": "#575757"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.arterial",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#ffffff"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.arterial",
                                "elementType": "labels.text.stroke",
                                "stylers": [
                                    {
                                        "color": "#2c2c2c"
                                    }
                                ]
                            },
                            {
                                "featureType": "road.local",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#000000"
                                    },
                                    {
                                        "lightness": 16
                                    }
                                ]
                            },
                            {
                                "featureType": "road.local",
                                "elementType": "labels.text.fill",
                                "stylers": [
                                    {
                                        "color": "#999999"
                                    }
                                ]
                            },
                            {
                                "featureType": "transit",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#000000"
                                    },
                                    {
                                        "lightness": 19
                                    }
                                ]
                            },
                            {
                                "featureType": "water",
                                "elementType": "geometry",
                                "stylers": [
                                    {
                                        "color": "#000000"
                                    },
                                    {
                                        "lightness": 17
                                    }
                                ]
                            }
                        ]
                    }
                }
            },
            gMap: {
                type: Object,
                value: function() {
                    return [];
                },
                notify: true,
                observer: '_setMap'
            },
            google: Object,
            markers: {
                type: Object,
                value: function() {
                    return [];
                },
                notify: true
            },
            apiKey: {
                type: String,
                value: 'AIzaSyAJyuWvZ03O18yHTvC1t3Mlj22VY73hJWc'
            },
            cluster: {
                type: Boolean,
                value: true
            },
            markerClusterer: Object,
            clusterOptions: {
                type: Object,
                value: function() {
                    return {
                        imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
                    }
                }
            },
            markerType: {
                type: Object,
                value: function() {
                    return {
                        newJob: {
                            color: '#808080'
                        },
                        myJob: {
                            color: '#00872d'
                        },
                        biddedJob: {
                            color: '#2c29e8'
                        }
                    }
                }
            }
        },
        _setMap: function() {
            if(this.google && this.markerClusterer) {
                //console.log('set Map', this.gMap);
                //console.log('set clusterer', this.markerClusterer);
            }
        },
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
        get infoWindow() {
            return new google.maps.InfoWindow()
        },
        addMarker: function(opts) {
            var marker
                self = this;
            opts.position = {
                lat: opts.lat,
                lng: opts.lng
            };
            marker = this._createMarker(opts);
            if(this.cluster) {
               this.markerClusterer.addMarker(marker);
            }
            this._addMarker(marker);
            if (opts.events) {
                this._attachEvents(marker, opts.events);
            }
            if (opts.content) {
                var infoWindow = new google.maps.InfoWindow({
                    content: opts.content
                });
                this._on({
                    obj: marker,
                    event: 'click',
                    callback: function() {
                        infoWindow.open(this.gMap, marker);
                    }
                });
            }
            return marker;
        },
        _attachEvents: function(obj, events) {
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
        remove: function(item) {
            var indexOf = this.markers.indexOf(marker);
            if (indexOf !== -1) {
                this.markers.splice(indexOf, 1);
            }
        },
        find: function(callback, action) {
            var callbackReturn,
            markers = this.markers,
            length = markers.length,
            matches = [],
            i = 0;
            console.log('markers', markers);
            for(; i < length; i++) {
                callbackReturn = callback(markers[i], i);
                if(callbackReturn) {
                    matches.push(markers[i]);
                }
            }

            if (action) {
                action.call(this, matches);
            }

            return matches;
        },
        findBy: function(callback) {
            return this.find(callback);
        },
        removeBy: function(callback) {
            var self = this;
            self.find(callback, function(markers) {
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
    }
</script>