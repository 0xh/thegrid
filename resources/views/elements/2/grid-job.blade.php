<link rel="import" href="/bower_components/paper-header-panel/paper-header-panel.html">
<link rel="import" href="/bower_components/paper-toolbar/paper-toolbar.html">
<link rel="import" href="/grid-elements/scripts.axios">

<dom-module id="grid-job">
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
		      <div class="flex">Job Details</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		    <div role="">
			 	<h1>@{{job.name}}</h1>
			 	<p>@{{job.date}}</p>
			 	<p>@{{job.price}}</p>
			 	<template is="dom-if" if="[[hasBids]]">
				 	<div id="status">
				 		<strong>@{{lowestBid}} - @{{highestBid}}</strong>
				 	</div>
				</template>
			 	<div id="bidders">
			 		<h2>Bidders</h2>
			 		<template is="dom-if" if="[[hasBids]]">
				 		<template is="dom-repeat" items="@{{job.bids}}" as="bid">
				 			<p><strong>@{{bid.price_bid}}</strong> : @{{bid.user.name}}</strong></p>
				 		</template>
				 	</template>
				 	<template is="dom-if" if="[[!hasBids]]">
				 		<p>This job has no bidder</p>
				 	</template>
			 	</div>
			</div>
		 </paper-header-panel>
	</template>
	<script>
		Polymer({
			is: 'grid-job',
			properties: {
				job: {
					type: Object,
					value: function() {
						return null;
					},
					notify: true
				},
				hasBids: {
					type: Boolean,
					value: false
				}
			},
			observers: ['_jobChange(job)'],
			behaviors: [
				GridBehaviors.FoldBehavior
			],
			_jobChange: function(job) {
				var self = this;
				if(job) {
					console.log('job has changed');
					console.log(job);
					var bids = job.bids;
					if(bids.length) {
						this.bids = bids;
						this.hasBids = true;
						this.highestBid = Math.max.apply(Math,bids.map(function(bid) { 
							return bid.price_bid;
						}));
						this.lowestBid = Math.min.apply(Math, bids.map(function(bid) {
							return bid.price_bid;
						}));
					} else {
						this.hasBids = false;
						this.highestBid = 0;
						this.lowestBid = 0;
					}
				}
			},
			refreshJob: function(job) {
				this._jobChange(job);
			},
			insertBid: function(data) {
				var job = this.job;
				this.unshift('job.bids', data);
				this._jobChange(this.job);
				// this.hasBids = true;
			},
			close: function() {
				this.thirdFold.close();
			},
			ready: function() {
				var self = this;
				socket.on('add-bid', function(data) {
					// self._jobChange(self.job);
					// self.notifyPath('job.bids');
					// self.hasBids = true;
				});
			}
		});
	</script>
</dom-module>