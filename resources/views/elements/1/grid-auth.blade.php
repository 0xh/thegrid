<link rel="import" href="/bower_components/paper-header-panel/paper-header-panel.html">
<link rel="import" href="/bower_components/paper-toolbar/paper-toolbar.html">
<link rel="import" href="/bower_components/paper-ripple/paper-ripple.html">
<link rel="import" href="/bower_components/paper-input/paper-input.html">
<link rel="import" href="/bower_components/paper-toast/paper-toast.html">
<link rel="import" href="/bower_components/paper-button/paper-button.html">
<link rel="import" href="/bower_components/paper-spinner/paper-spinner.html">
<link rel="import" href="/bower_components/iron-ajax/iron-ajax.html">
<link rel="import" href="/grid-elements/scripts.axios">

<dom-module id="grid-auth">
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
		.sign-in {
			width: 100%;
		    margin: 8px 0;
		    background-color: #e00008;
		    color: #FFF;
		    text-transform: none;
		    line-height: 28px;
		}
		.sign-in.facebook {
			background-color: #335795;
		}
		.sign-in.google {
			background-color: #D34836;
		}
		.or {
			text-align: center;
    		margin: 20px 0;
		}
		a {
			text-decoration: none;
		}
		#submit {
			margin-top: 20px;
		}
		#submit span {
			/*line-height: 28px;*/
		}
		#spinner {
			/*margin-left: -33px;*/
			/*margin-right: 5px;*/
		}
	</style>
	<template>
		{{-- <iron-ajax 
			id="ajaxToken" 
			url="/oauth/token"
			method="POST"
			handle-as="json"
			content-type="application/json"
			on-response="_getToken"
			on-error="_getTokenError"
			last-response="_getTokenError"
		></iron-ajax>
		<iron-ajax 
			id="ajaxGetUser" 
			url="/users"
			method="GET"
			handle-as="json"
			content-type="application/json"
			on-response="_getUsers"
		></iron-ajax>
		<iron-request id="xhr" ></iron-request> --}}
		<paper-header-panel class="flex">
		    <paper-toolbar slot="header">
		      <div class="flex">Sign In</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		    <div>
			    <div class="container">
			    	<p hidden="@{{!isMessage}}">@{{message}}</p>
				 	<form id="form" role="form" method="POST" action="{{ route('login') }}">
				 		{{ csrf_field() }}
				 		<paper-input id="email" name="email" label="Email" type="email" value="@{{email}}" required autofocus error-message="@{{errorEmail}}"></paper-input>
				 		<paper-input id="password" name="password" label="Password" type="password" value="@{{password}}" required error-message="@{{errorPassword}}"></paper-input>
				 		<paper-button id="submit" raised class="sign-in" type="submit" on-tap="signIn">
				 			<template is="dom-if" if="[[isLoading]]">
				 				<paper-spinner id="spinner" active></paper-spinner>
							</template>
							<template is="dom-if" if="[[!isLoading]]">
				 				<span>Sign In</span>
				 			</template>
				 		</paper-button>
				 	</form>
				 	<div >
				 		<a href="#" on-click="openRegister">Sign Up</a>
					 	<template is="dom-if" if="[[attempt]]">
					 		<span> | </span>
					 		<a href="#" on-click="openForgotPassword">Forgot Password</a>
					 	</template>
					 </div>
				 	<div class="or">
				 		<span>OR</span>
				 	</div>
				 	<a href="/auth/facebook">
				 		<paper-button raised class="sign-in facebook">Sign In with Facebook</paper-button>
				 	</a>
				 	<a href="/auth/google">
				 		<paper-button raised class="sign-in google">Sign In with Google</paper-button>
				 	</a>
				 	<paper-toast id="errorToast" class="fit-bottom"></paper-toast>
				 </div>
			 </div>
		 </paper-header-panel>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-auth',
			properties: {
				email: String,
				password: String,
				errorEmail: String,
				errorPassword: String,
				isLoading: {
					type: Boolean,
					value: false
				},
				isMessage: {
					type: Boolean,
					value: false
				},
				message: String,
				attempt: {
					type: Boolean,
					value: false,
					// observer: '_attemptChange'
				}
			},
			behaviors: [
				GridBehaviors.FoldBehavior
			],
			close: function() {
				this.secondFold.close();
			},
			// _set
			_getToken: function(e, request) {
				console.log(e);
				// console.log(request.response);
				// this.$.ajaxGetUser.headers = {
				// 	'Accept' : 'application/json',
				// 	'Authorization' : 'Bearer ' + request.response.access_token
				// };
				// this.$.ajaxGetUser.generateRequest();
				// console.log('get user done');
			},
			_getTokenError: function(e, request) {
				console.log(e);
			},
			_getUsers: function(e, request) {
				console.log(request.response);
			},
			signIn: function() {
				var self = this;
				// self.$.spinner.active = true;
				self.isLoading = true;
				axios.post('/login', {
					email: this.email,
					password: this.password
				})
				.then(function (response) {
					console.log(response);
					var data = response.data;
					if(data.status == 1) {
						window.location.href = "/";
					} else if(data.status == 0) {
						// self.thirdFold.component = 'grid-register';
						// self.thirdFold.open();
					}
					self.isLoading = false;
				})
				.catch(function (error) {
					if (error.response) {
						var data = error.response.data;
				    	// console.log(error.response.data);
				    	self.errorEmail = data.email;
				    	self.errorPassword = data.password;
				    	self.$.email.validate();
				    	self.$.password.validate();

				    	var err = [];
				    	for( var key in data) {
				    		// err += "<br/>" + data[key];
				    		err.push(data[key]);
				    	}
				    	
				    	// self.error = err.join('<br/>');
				    	self.$.errorToast.fitInto = self;
				    	self.$.errorToast.innerHTML = err.join('<br/>');
				    	self.$.errorToast.open();
				    	self.attempt = true;
				    } else {
				      // Something happened in setting up the request that triggered an Error
				      console.log('Error', error.message);
				    }
				    // self.$.spinner.active = false;
				    self.isLoading = false;
				});
				// this.$.ajaxToken.body = {
				// 	username: this.email,
				// 	password: this.password,
				// 	client_id: {{env('CLIENT_ID')}},
				// 	client_secret: '{{env('CLIENT_SECRET')}}',
				// 	grant_type: 'password'
				// };
				// this.$.ajaxToken.generateRequest();
			},
			openRegister: function() {
				this.drawer._selectTab('register');
			},
			openForgotPassword: function() {
				this.drawer._selectTab('forgot-password');
			},
			_attemptChange: function() {
				if (this.$.attempt > 0) {

				}
			}
		});
	}());
</script>