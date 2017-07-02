<dom-module id="grid-register">
	<style include="iron-flex grid">
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
		<paper-scroll-header-panel class="flex h100">
			<paper-toolbar slot="header" class="border-bottom">
				<div class="flex">Register</div>
				<paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
			</paper-toolbar>
			<div>
				<div class="container">
					<form id="form" role="form" method="POST" action="{{ route('register') }}">
						{{ csrf_field() }}
						<paper-input id="name" name="name" label="Name" type="text" value="@{{name}}" required autofocus error-message="@{{errorName}}"></paper-input>
						<paper-input id="email" name="email" label="Email" type="email" value="@{{email}}" required autofocus error-message="@{{errorEmail}}"></paper-input>
						<input type="hidden" id="country_code" name="country_code" value="@{{country.phonecode}}" />
						<gold-phone-input id="phone_number" name="phone_number" value="@{{phone_number}}" label="Mobile Number" country-code="@{{country.phonecode}}" maxlength="[[country.nsn]]" minlength="[[country.nsn]]" required error-message="@{{errorPhone}}"></gold-phone-input>
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
		</paper-scroll-header-panel>
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
				country: {
					type: Object,
					value: function() {
						return {};
					}
				},
				password: String,
				password_confirmation: String
			},
			behaviors: [
			GridBehaviors.FoldBehavior
			],
			observers: ['_countryChange(country)'],
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
					country_code: this.country.phonecode,
					phone_number: this.phone_number,
					password_confirmation: this.password_confirmation
				})
				.then(function (response) {
					var data = response.data;
					console.log(data);
					self.$.toast.fitInto = self;
					self.$.toast.innerHTML = data.message;
					self.$.toast.open();
					self.thirdFold.component = 'confirmation';
					self.thirdFold.open();
			    	// self.secondFold.$.auth.isMessage = true;
			    	// self.secondFold.$.auth.message = data.message;
			    	//self.close();
			    	self.isLoading = false;
			    	self.$.submit.disabled = true;
			    })
				.catch(function (error) {
					if (error.response) {
						var data = error.response.data;
						console.log(error.response.data);
				    	// console.log(error.response.data);
				    	self.errorName = data.name;
				    	self.errorPhone = data.phone_number;
				    	self.errorPasswordConfirmation = data.password;
				    	self.$.name.validate();
				    	self.$.email.validate();
				    	self.$.phone_number.validate();
				    	self.$.password.validate();
				    	self.$.password_confirmation.validate();
				    	if(data.password) {
				    		self.$.password_confirmation.invalid = true;
				    	} else {
				    		self.$.password_confirmation.invalid = false;
				    	}
				    	if(data.phone_number) {
				    		self.$.phone_number.invalid = true;
				    	} else {
				    		self.$.phone_number.invalid = false;
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
			_countryChange: function(country) {
				if( country ) {
					var ptr = '';
					for( var i = 1; i <= country.nsn; i++ ) {
						ptr += 'X';
					}
					// this.$.phone_number.maxlength = country.nsn;
					this.$.phone_number.phoneNumberPattern = ptr;
					this.country.phonecode = '+' + country.phonecode;
				}
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