<link rel="import" href="/bower_components/paper-header-panel/paper-header-panel.html">
<link rel="import" href="/bower_components/paper-toolbar/paper-toolbar.html">
<link rel="import" href="/bower_components/paper-ripple/paper-ripple.html">
<link rel="import" href="/bower_components/paper-input/paper-input.html">
<link rel="import" href="/bower_components/paper-button/paper-button.html">

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
	</style>
	<template>
		<paper-header-panel class="flex">
		    <paper-toolbar>
		      <div class="flex">Login/Register</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		 </paper-header-panel>
		 <div class="container">
		 	<form id="form" role="form" method="POST" action="{{ route('login') }}">
		 		{{ csrf_field() }}
		 		<paper-input name="email" label="Email" type="email" value="{{ old('email') }}"></paper-input>
		 		<paper-input name="password" label="Password" type="password"></paper-input>
		 		<paper-button raised class="sign-in" type="submit" on-tap="signIn">Sign In</paper-button>
		 	</form>
		 	<div class="or">
		 		<span>OR</span>
		 	</div>
		 	<a href="/auth/facebook">
		 		<paper-button raised class="sign-in facebook">Sign In with Facebook</paper-button>
		 	</a>
		 	<a href="/auth/google">
		 		<paper-button raised class="sign-in google">Sign In with Google</paper-button>
		 	</a>
		 </div>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-auth',
			behaviors: [GridBehaviors.FoldBehavior],
			close: function() {
				this.secondFold.close();
			},
			signIn: function() {
				this.$.form.submit();
			}
		});
	}());
</script>