<link rel="import" href="/bower_components/paper-spinner/paper-spinner.html">
<link rel="import" href="/grid-elements/scripts.axios">

<dom-module id="grid-info-window">
	<style include="iron-flex">
		:host  .wrapper {
			height: 170px;
			width: 200px;
			display: flex; 
			align-items: center; 
			justify-content: center;
		}
		.btn-bid {
			background-color: #E00008;
			color: #FFF;
			width: 100%;
		}
		.bid-input {
			/*--paper-input-container-color: ;*/
		}
		.spinner {
			text-align: center;
		}
	</style>
	<template>
		<div class="wrapper">
			<div>
				<div><strong>Posted by</strong>: @{{data.user.name}}</div>
				<div><strong>Demand</strong>: @{{data.name}}</div>
				<div><strong>Price</strong>: AED @{{data.price}}</div>
				<template is="dom-if" if="[[isMyPost]]">
					<p>This is my post</p>
				</template>
				<template is="dom-if" if="[[!isMyPost]]">
					<template is="dom-if" if="[[!isBidded]]">
						<div>
							<paper-input label="Quick Bid" value="@{{price_bid}}" name="bid" class="flex bid-input"></paper-input>
							<paper-button raised class="flex btn-bid" on-tap="quickBid">Bid</paper-button>
						</div>
					</template>
					<template is="dom-if" if="[[isBidded]]">
						<p>You already bidded to this job</p>
					</template>
				</template>
			</div>
			{{-- <template is="dom-if" if="[[!isChecked]]">
				<div class="flex spinner">
					<paper-spinner id="spinner" active></paper-spinner>
				</div>
			</template> --}}
		</div>
	</template> 
</dom-module>
<script>
	(function(){

		Polymer({
			is: 'grid-info-window',
			properties : {
				data: {
					type: Object,
					value: function() {
						return {};
					}
				},
				price: {
					type: String,
					value: '',
					notify: true
				},
				isBidded: {
					type: Boolean,
					value: false,
					notify: true
				},
				isChecked: {
					type: Boolean,
					value: false,
					notify: true
				}
			},
			behaviors: [GridBehaviors.FoldBehavior],
			attached: function() {
				if(typeof this.data === 'string') {
					this.data = JSON.parse(this.data);
				}
				var self = this;
				// axios.get('/'+Grid.user_id+'/bid/check/'+this.data.id)
				// 	.then(function(response) {
				// 		self.isBidded = response.data.isBidded;
				// 		self.isChecked = true;
				// 	}).catch(function(response) {

				// 	});
			},
			quickBid: function() {
				var self = this;
				if(Grid.authenticated) {
					axios.post('/bid', {
						'user_id' : Grid.user_id,
						'job_id' : this.data.id,
						'price_bid' : parseFloat(this.price_bid)
					}).then(function(response) {
						self.isBidded = true;
						console.log(response);
						var data = response.data;
						self.secondFold.$.bids.insertBid(data);
						socket.emit('new-bid', data);
					}).catch(function(response) {

					});
				} else {
					alert('Please login first');
				}
			},
			ready: function() {
				
			}
		});
	}());
</script>