<dom-module id="grid-profile">
	<style include="iron-flex grid">
		:root {
			--paper-toolbar-background: #FFFFFF;
			--paper-toolbar-color: #636b6f;
		}
		:host {
			height: 100%;
		}
	</style>
	<template>
		<paper-scroll-header-panel class="flex h100">
		    <paper-toolbar slot="header" class="border-bottom">
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
		</paper-scroll-header-panel>
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