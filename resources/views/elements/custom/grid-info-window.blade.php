
<dom-module id="grid-info-window">
	<style include="iron-flex grid">
		:host  .wrapper {
			/*height: 170px;
			width: 200px;
			display: flex;
			align-items: center;
			justify-content: center;*/
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
		<div>
			<h3><span>@{{data.user.name}}</span></h3>
			<div><strong>Budget</strong>: <span>AED @{{data.price}}</span></div>
			<div><strong>Distance</strong>: <span>@{{data.distance}} Miles</span></div>
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
					if(this.data.distance)
						this.set('data.distance' ,this.data.distance.toFixed(2));
					this.startCountDown(this.data.date);
				}
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
				var t = this.getTimeRemaining(this.data.date);
				if(t.total <= 0) {
					this.timeLeft = "Expired";
				} else {
					this.timeLeft = t.days + ' days : ' + t.hours + ' hrs. : ' + t.minutes + ' min. : ' + t.seconds + ' sec.' ;
				}
			},
			startCountDown: function() {
				var self = this;
				setInterval(function() {
					self.updateTimeLeft();
				}, 1000);
			},
			ready: function() {

			}
		});
	}());
</script>
