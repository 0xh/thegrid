<link rel="import" href="/bower_components/paper-header-panel/paper-header-panel.html">
<link rel="import" href="/bower_components/paper-toolbar/paper-toolbar.html">

<dom-module id="grid-bids">
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
		      <div class="flex">Bids</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		    <div role="listbox">
			 	<template is="dom-repeat" items="@{{bids}}" as="bid">
					<paper-icon-item on-tap="viewJob" data-id$=@{{bid.id}}>
						<iron-icon icon="account-circle" item-icon></iron-icon>
						<paper-item-body two-line>
							<div>@{{bid.job.name}}</div>
							<div secondary>@{{bid.job.location}}</div>
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
			is: 'grid-bids',
			properties: {
				bids: {
					type: Object,
					value: function() {
						return [];
					},
					notify: true,
				}
			},
			behaviors: [GridBehaviors.FoldBehavior],
			close: function() {
				this.secondFold.close();
			},
			insertBid: function(data) {
				this.unshift('bids', data);
				console.log('new bid', data);
			},
			refreshBids: function() {
				var self = this;
				axios.get('/'+Grid.user_id+'/bids')
					.then(function (response) {
						var data = response.data;
						self.bids = data;
						console.log('bids', data);
					});
			},
			ready: function() {
				this.refreshBids();
			}
		});
	}());
</script>
