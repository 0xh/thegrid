<link rel="import" href="/bower_components/paper-header-panel/paper-header-panel.html">
<link rel="import" href="/bower_components/paper-toolbar/paper-toolbar.html">
<link rel="import" href="/bower_components/paper-item/paper-item.html">
<link rel="import" href="/bower_components/paper-item/paper-item-body.html">
<link rel="import" href="/bower_components/paper-item/paper-icon-item.html">
<link rel="import" href="/bower_components/paper-ripple/paper-ripple.html">

<dom-module id="grid-inbox">
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
		    <paper-toolbar>
		      <div class="flex">Inbox</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		 </paper-header-panel>
		 <div role="listbox">
			<paper-icon-item onclick="thirdFold.open()">
				<iron-icon icon="account-circle" item-icon></iron-icon>
				<paper-item-body two-line>
					<div>(650) 555-1234</div>
					<div secondary>Mobile</div>
				</paper-item-body>
				<paper-ripple></paper-ripple>
			</paper-icon-item>
			<paper-icon-item>
				<iron-icon icon="account-circle" item-icon></iron-icon>
				<paper-item-body two-line>
					<div>(650) 555-1234</div>
					<div secondary>Mobile</div>
				</paper-item-body>
				<paper-ripple></paper-ripple>
			</paper-icon-item>
			<paper-icon-item>
				<iron-icon icon="account-circle" item-icon></iron-icon>
				<paper-item-body two-line>
					<div>(650) 555-1234</div>
					<div secondary>Mobile</div>
				</paper-item-body>
				<paper-ripple></paper-ripple>
			</paper-icon-item>
		</div>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-inbox',
			behaviors: [GridBehaviors.FoldBehavior],
			close: function() {
				this.secondFold.close();
			}
		});
	}());
</script>
