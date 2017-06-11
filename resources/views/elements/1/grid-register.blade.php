<link rel="import" href="/grid-elements/scripts.axios">
<dom-module id="grid-register">
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
		      <div class="flex">Register</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		    <div>
			    <div class="container">
				 	<form id="form" role="form" method="POST" action="{{ route('register') }}">
				 		{{ csrf_field() }}
				 		<paper-input id="email" name="email" label="Email" type="email" value="@{{email}}" required autofocus error-message="@{{errorEmail}}"></paper-input>
				 		<paper-input id="name" name="name" label="Name" type="text" value="@{{name}}" required autofocus error-message="@{{errorName}}"></paper-input>
				 		<paper-input id="password" name="password" label="Password" type="password" value="@{{password}}" required error-message="@{{errorPassword}}"></paper-input>
				 		<paper-input id="password_confirmation" name="password_confirmation" type="password" label="Password Confirmation" type="text" value="@{{password_confirmation}}" required error-message="@{{errorPasswordConfirmation}}"></paper-input>
				 		<paper-button id="submit" raised class="sign-up" type="submit" on-tap="register">
				 			<template is="dom-if" if="[[isLoading]]">
				 				<paper-spinner id="spinner" active></paper-spinner>
							</template>
							<template is="dom-if" if="[[!isLoading]]">
				 				<span>Register</span>
				 			</template>
				 		</paper-button>
				 	</form>
				 	<div><a href="#" on-click="openAuth">Sign In</a></div>
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
			is: 'grid-register',
			properties: {
				isLoading: {
					type: Boolean,
					value: false
				},
				email: String,
				name: String,
				password: String,
				password_confirmation: String
			},
			behaviors: [
				GridBehaviors.FoldBehavior
			],
			attached: function() {
				this.$.email.focus();
			},
			_updateCredentials: function() {
				//this.email = this.secondFold.$.auth.email;
				//this.password = this.secondFold.$.auth.password;
			},
			register: function() {
				//this._updateCredentials();
				var self = this;
				self.isLoading = true;
				axios.post('/register', {
					email: this.email,
					name: this.name,
					password: this.password,
					password_confirmation: this.password_confirmation
				})
				.then(function (response) {
					var data = response.data;
					console.log(data);
					self.$.toast.fitInto = self;
			    	self.$.toast.innerHTML = data.message;
			    	self.$.toast.open();
			    	self.secondFold.$.auth.isMessage = true;
			    	self.secondFold.$.auth.message = data.message;
			    	self.close();
			    	console.log('success');
				})
				.catch(function (error) {
					if (error.response) {
						var data = error.response.data;
				    	console.log(error.response.data);
				    	// console.log(error.response.data);
				    	self.errorName = data.name;
				    	self.errorPasswordConfirmation = data.password;
				    	self.$.email.validate();
				    	self.$.name.validate();
				    	self.$.password.validate();
				    	self.$.password_confirmation.validate();
				    	if(data.password) {
				    		self.$.password_confirmation.invalid = true;
				    	} else {
				    		self.$.password_confirmation.invalid = false;
				    	}
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
				    // self.$.spinner.active = false;
				    self.isLoading = false;
				});
			},
			openAuth: function() {
				this.drawer.openAuth();
			},
			close: function() {
				this.secondFold.close();
			},
			ready: function() {
				//this._updateCredentials();			
			}
		});
	}());
</script>