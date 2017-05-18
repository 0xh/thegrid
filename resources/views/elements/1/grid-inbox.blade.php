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
		    <paper-toolbar slot="header">
		      <div class="flex">Inbox</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		    <div role="listbox">
			 	<template is="dom-repeat" items="@{{inbox}}">
					<paper-icon-item onclick="thirdFold.open()">
						<iron-icon icon="account-circle" item-icon></iron-icon>
						<paper-item-body two-line>
							<div>@{{item.title}}</div>
							<div secondary>@{{item.text}}</div>
						</paper-item-body>
						<paper-ripple></paper-ripple>
					</paper-icon-item>
				</template>
			</div>
		 </paper-header-panel>
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
			},
			ready: function() {
				this.inbox = [
					{title: 'Macbook for sale (AED 1000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Iphone for sale (AED 900)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Web Developer needed budget (AED 9000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Macbook for sale (AED 1000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Iphone for sale (AED 900)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Web Developer needed budget (AED 9000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Macbook for sale (AED 1000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Iphone for sale (AED 900)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Web Developer needed budget (AED 9000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Macbook for sale (AED 1000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Iphone for sale (AED 900)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Web Developer needed budget (AED 9000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Macbook for sale (AED 1000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Iphone for sale (AED 900)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Web Developer needed budget (AED 9000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Macbook for sale (AED 1000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Iphone for sale (AED 900)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Web Developer needed budget (AED 9000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Macbook for sale (AED 1000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Iphone for sale (AED 900)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Web Developer needed budget (AED 9000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Macbook for sale (AED 1000)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Iphone for sale (AED 900)', text: 'Brand new, no dents, 10% complete'},
					{title: 'Web Developer needed budget (AED 9000)', text: 'Brand new, no dents, 10% complete'},
				];
			}
		});
	}());
</script>
