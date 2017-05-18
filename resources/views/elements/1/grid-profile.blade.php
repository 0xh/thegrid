<link rel="import" href="/bower_components/paper-header-panel/paper-header-panel.html">
<link rel="import" href="/bower_components/paper-toolbar/paper-toolbar.html">
<link rel="import" href="/bower_components/paper-ripple/paper-ripple.html">
<link rel="import" href="/bower_components/paper-input/paper-input.html">
<link rel="import" href="/bower_components/paper-button/paper-button.html">

<dom-module id="grid-profile">
	<style include="iron-flex">
		:root {
			--paper-toolbar-background: #FFFFFF;
			--paper-toolbar-color: #636b6f;
		}
		:host {
			height: 100%;
		}
	</style>
	<template>
		<paper-header-panel class="flex">
		    <paper-toolbar slot="header">
		      <div class="flex">Profile</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
			    <div role="listbox">
				<paper-item on-tap="logout">
					<paper-item-body>
						<div>Logout</div>
					</paper-item-body>
					<paper-ripple></paper-ripple>
				</paper-item>
			</div>
	        <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
	                {{ csrf_field() }}
	        </form>
		</paper-header-panel>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-profile',
			behaviors: [GridBehaviors.FoldBehavior],
			close: function() {
				this.secondFold.close();
			},
			logout: function() {
				this.$.logoutForm.submit();
			}
		});
	}());
</script>