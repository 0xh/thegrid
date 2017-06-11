<link rel="import" href="/bower_components/paper-dropdown-menu/paper-dropdown-menu.html">
<link rel="import" href="/bower_components/paper-listbox/paper-listbox.html">
<link rel="import" href="/bower_components/paper-item/paper-item.html">
<link rel="import" href="/grid-elements/scripts.axios">
<dom-module id="grid-add-job">
	<style include="iron-flex">
		:root {
				--paper-toolbar-background: #FFFFFF;
				--paper-toolbar-color: #636b6f;
			}
			:host {
				height: 100%;
			}
			.container {
				padding: 8px 16px;
			}
			.sign-up {
				width: 100%;
			    margin: 8px 0;
			    margin-top: 20px;
			    background-color: #e00008;
			    color: #FFF;
			    text-transform: none;
			    line-height: 28px;
			}
	</style>
	<template>
		<paper-header-panel class="flex">
		    <paper-toolbar slot="header">
		      <div class="flex">Add Job</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		    <div>
			    <div class="container">
				 	<form id="form" role="form" method="POST" action="#">
				 		{{ csrf_field() }}
				 		<paper-input id="what" name="what" label="What" type="text" value="@{{what}}" required autofocus error-message="@{{errorWhat}}"></paper-input>
				 		<paper-input id="when" name="when" label="When" type="date" value="@{{when}}" required error-message="@{{errorWhat}}"></paper-input>
				 		<paper-input id="where" name="where" label="Where" type="text" value="@{{where}}" required error-message="@{{errorWhat}}"></paper-input>
				 		<paper-input id="price" name="price" label="Budget" value="@{{price}}" required error-message="@{{errorWhat}}">
				 			<div suffix>AED </div>
				 		</paper-input>
				 		<paper-dropdown-menu label="Job Type">
							<paper-listbox class="dropdown-content" selected="3" attr-for-selected="value">
								<paper-item value="1">Product</paper-item>
								<paper-item value="3">Service</paper-item>
							</paper-listbox>
						</paper-dropdown-menu>
				 		{{-- <div>@{{lat}},@{{lng}}</div> --}}
				 		<paper-button id="submit" raised class="sign-up" type="submit" on-tap="postJob">
				 			<template is="dom-if" if="[[isLoading]]">
				 				<paper-spinner id="spinner" active></paper-spinner>
							</template>
							<template is="dom-if" if="[[!isLoading]]">
				 				<span>Submit</span>
				 			</template>
				 		</paper-button>
				 	</form>
				 	<paper-toast id="toast" class="fit-bottom"></paper-toast>
				 </div>
			 </div>
		 </paper-header-panel>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-add-job',
			properties: {
				isLoading: {
					type: Boolean,
					value: false
				},
				what: String,
				when: String,
				where: String,
				lat: String,
				lng: String,
				price: String,
			},
			behaviors: [
				GridBehaviors.FoldBehavior,
				GridBehaviors.TabsBehavior
			],
			attached: function() {
				
			},
			_updateCredentials: function() {
				//this.email = this.secondFold.$.auth.email;
				//this.password = this.secondFold.$.auth.password;
			},
			postJob: function() {
				var self = this;
				self.isLoading = true;
				axios.post('/job', {
					user_id: Grid.user_id,
					name: this.what,
					price: this.price,
					lat: this.lat,
					lng: this.lng,
					location: this.where,
					date: this.when
				})
				.then(function (response) {
					var data = response.data;
					console.log('new job', data);
					self.secondFold.selectedTab = 'jobs';
					// self.secondFold.$.jobs.refreshJobs();
					self.secondFold.$.jobs.insertJob(data);
					self.secondFold.open();
					var customPin = {
						path: 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z',
						fillColor: '#00872d',
						fillOpacity: 1,
						scale: 1,
						strokeColor: '#00872d',
						strokeWeight: 0.5
					};

					var d = JSON.stringify(data),
						content = document.createElement('grid-info-window');
					content.data = d;
					content.isMyPost = true;
					content.isBidded = false;

					self.view.addMarker({
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

					socket.emit('new-job', data);
				})
				.catch(function (error) {
					if (error.response) {
						var data = error.response.data;

				    	var err = [];
				    	for( var key in data) {
				    		// err += "<br/>" + data[key];
				    		err.push(data[key]);
				    	}
				    	
				    	// self.error = err.join('<br/>');
				    	self.$.toast.fitInto = self;
				    	self.$.toast.innerHTML = err.join('<br/>');
				    	self.$.toast.open();
				    } else {
				      // console.log('Error', error.message);
				    }
				    self.isLoading = false;
				});
			},
			openAuth: function() {
				this.drawer.openAuth();
			},
			close: function() {
				this.secondFold.close();
			}
		});
	}());
</script>