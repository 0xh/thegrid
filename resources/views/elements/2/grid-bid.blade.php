<link rel="import" href="/bower_components/vaadin-grid/vaadin-grid.html">
<link rel="import" href="/bower_components/vaadin-grid/vaadin-grid-sorter.html">

<dom-module id="grid-bid">
	<style include="iron-flex grid">
		:root {
			--paper-toolbar-background: #FFFFFF;
			--paper-toolbar-color: #636b6f;
		}
		:host {
			height: 100%;
		}
		.main-price {
			font-weight: 400;
			font-size: 18px;
		}
		paper-progress {
			width: 100%;
			margin-bottom: -4px;
		}
	</style>
	<template>
		<paper-scroll-header-panel class="flex h100">
		    <paper-toolbar slot="header" class="border-bottom">
		      <div class="flex">Bid Details</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		    <div role="">
		    	<template is="dom-if" if="[[isLoading]]">
		    		<paper-progress indeterminate></paper-progress>
		    	</template>
		    	<div class="container">
		    		<p><span class="main-price">AED [[bid.job.price]]</span></p>
		    		<p>[[bid.job.name]]</p>
		    		<p>Posted by: [[bid.job.user.name]]</p>
		    		<p><strong>Time remaining:</strong> <span>[[timeLeft]]</span></p>
		    		<p><strong>Bidded:</strong> <span>[[bid.price_bid]]</span></p>
		    		<p><strong>Rank:</strong> <span>[[rank]]</span></p>
		    		<template is="dom-if" if="[[bid.is_approved]]">
		    			This Bid has been approved! at @{{bid.is_approved.created_at}}
		    		</template>
		    		<p><strong>Lowest Bid:</strong> <span>[[lowestBid]]</span></p>
		    		<p><strong>Highest Bid:</strong> <span>[[highestBid]]</span></p>
		    		<template is="dom-repeat" items="[[bid.job.only_bids]]" as="bid">
		    			<p><strong>[[bid.price_bid]]</strong> : [[bid.user_id]]</strong></p>
		    		</template>
		    	</div>
			</div>
		 </paper-scroll-header-panel>
	</template>
	<script>
		Polymer({
			is: 'grid-bid',
			properties: {//http://dev.thegrid.com/1/bid/1
				id: {
					type: Number,
					value: 0,
					notify: true
				},
				bid: {
					type: Object,
					value: function() {
						return null;
					},
					notify: true
				},
				timeLeft: {
					type: String,
					//computed: 'countDown(job.date)'
				},
				started: {
					type: Boolean,
					value: false
				},
				selectedItem: {
					type: Object,
					value: function() {
						return null
					},
					notify: true
				},
				isLoading: {
					type: Boolean,
					value: true
				}
			},
			observers: ['_getBidDetails(id)'],
			behaviors: [
				GridBehaviors.FoldBehavior
			],
			_bidChange: function(bid) {
				if(bid) {
					this.countDown(bid.job.date);
					this.getRank();
					this.bid = bid;
					// this.set('bid.is_approved', null);
					// this.set('bid.is_approved', bid.is_approved);
				}
			},
			getTimeRemaining: function(endtime){
				var t = Date.parse(endtime) - Date.parse(new Date());
				var seconds = Math.floor( (t/1000) % 60 );
				var minutes = Math.floor( (t/1000/60) % 60 );
				var hours = Math.floor( (t/(1000*60*60)) % 24 );
				var days = Math.floor( t/(1000*60*60*24) );
				return {
					'total': t,
					'days': days,
					'hours': hours,
					'minutes': minutes,
					'seconds': seconds
				};
			},
			countDown: function(date) {
				this.updateTimeLeft();
				if(!this.started) {
					this.startCountDown();
				}
			},
			updateTimeLeft: function() {
				var t = this.getTimeRemaining(this.bid.job.date);
				if(t.total <= 0) {
					this.timeLeft = "Expired";
				} else {
					this.timeLeft = t.days + ' days : ' + t.hours + ' hours : ' + t.minutes + ' minutes : ' + t.seconds + ' seconds' ; 
				}
			},
			startCountDown: function() {
				var self = this;
				setInterval(function() {
					self.updateTimeLeft();
				}, 1000);
			},
			refreshBid: function() {
				//if(bid.id) {
					this._getBidDetails(this.id);
				//}
			},
			getRank: function() {
				var self = this;
				var arr = this.bid.job.only_bids;
				var sorted = arr.slice().sort(function(a,b){return b.price_bid - a.price_bid})
				var ranks = arr.slice().map(function(v){ return sorted.indexOf(v)+1 });
				this.rank = sorted.findIndex(function(e){
								return e.user_id === self.bid.user_id;
							}) + 1;
				this.lowestBid = sorted[0].price_bid;
				this.highestBid = sorted[sorted.length-1].price_bid;
			},
			_getBidDetails: function(id) {
				var self = this;
				if(id) {
					self.isLoading = true;
					axios.get('/'+Grid.user_id+'/bid/'+id)
						 .then(function(response) {
						 	// console.log(response.data);
						 	self.isLoading = false;
						 	self._bidChange(response.data);
						 });
				}
			},
			close: function() {
				this.thirdFold.close();
			},
			ready: function() {

			}
		});
	</script>
</dom-module>