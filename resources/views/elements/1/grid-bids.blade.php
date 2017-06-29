<dom-module id="grid-bids">
	<style include="iron-flex">
		:root {
			--paper-toolbar-background: #FFFFFF;
			--paper-toolbar-color: #636b6f;
		}
		:host, paper-scroll-header-panel {
			height: 100%;
		}
		.icon-badge {
			float: right;
		    color: #ffffff;
		    background: red;
		    border-radius: 50%;
		    width: 24px;
		    height: 24px;
		    text-align: center;
		    font-style: normal;
		}
		paper-progress {
			width: 100%;
		}
	</style>
	<template>
		<paper-scroll-header-panel class="flex">
		    <paper-toolbar slot="header">
		      <div class="flex">Bids</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		    <div role="listbox">
		    <dom-bind>
		    	<template is="dom-if" if="[[isLoading]]">
		    		<paper-progress indeterminate></paper-progress>
		    	</template>
			 	<template is="dom-repeat" items="@{{bids}}" as="bid">
					<paper-icon-item on-tap="viewBid" data-id$="@{{bid.id}}">
						@{{_createRoom(bid.job.id)}}
						<iron-icon icon="account-circle" item-icon></iron-icon>
						<paper-item-body two-line>
							<div>@{{bid.job.name}}</div>
							<div secondary>@{{bid.job.location}}</div>
						</paper-item-body>
						<template is="dom-if" if="[[bid.is_approved]]">
							<i class="icon-badge"></i>
						</template>
						<paper-ripple></paper-ripple>
					</paper-icon-item>
				</template>
				<template is="dom-if" if="[[pagination.next_page_url]]">
					<paper-item on-tap="loadBids">
						<paper-item-body>
							<div >Load more</div>
						</paper-item-body>
					</paper-item>
				</template>
			</dom-bind>
			</div>
		 </paper-scroll-header-panel>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-bids',
			properties: {
				bids: {
					type: Array,
					value: function() {
						return [];
					},
					notify: true,
				},
				indexOpened: {
					type: Number,
					value: -1,
					notify: true
				},
				pagination: {
					type: Object,
					value: function() {
						return [];
					},
					notify: true
				}
			},
			listeners: {
			"content-scroll": "scroll"
			},
			scroll: function(e, d){
				if (d.target.scrollHeight - (d.target.clientHeight * 1.5) < d.target.scrollTop) {
        			//console.log(d.target.scrollHeight, d.target.clientHeight, d.target.scrollTop);
        			if(!this.isLoading) {
        				this.loadBids();
        			}
   				}
   			},
			behaviors: [GridBehaviors.FoldBehavior],
			close: function() {
				this.secondFold.close();
			},
			_createRoom: function(room) {
				var suf = 'JOB_';
				room = suf + room;
				console.log(room)
				socket.emit('join-room', room);
			},
			viewBid: function(e) {
				var bid = e.model.bid;
				this.thirdFold.component = 'bid'
				this.thirdFold.$.bid.bid = bid;
				this.thirdFold.$.bid.id = bid.id;
				this.thirdFold.open();
				var bids = this.bids;
				this.indexOpened = bids.findIndex(x => x.id == bid.id);
			},
			insertBid: function(data) {
				this.unshift('bids', data);
			},
			loadBids: function() {
				var self = this,
					pagination = self.pagination;
				if(pagination.next_page_url) {
					self.isLoading = true;
					axios.get(pagination.next_page_url)
						.then(function (response) {
							var data = response.data,
								c_b = self.bids;
							self.bids = c_b.concat(data.data);
							self.pagination = {
								total: data.total,
								per_page: data.per_page,
								current_page: data.current_page,
								last_page: data.last_page,
								next_page_url: data.next_page_url,
								prev_page_url: data.prev_page_url,
								from: data.from,
								to: data.to
							};
							self.isLoading = false;
						});
				}
			},
			refreshBids: function() {
				var self = this;
				axios.get('/'+Grid.user_id+'/bids')
					.then(function (response) {
						var data = response.data;
						self.bids = data.data;
						self.pagination = {
							total: data.total,
							per_page: data.per_page,
							current_page: data.current_page,
							last_page: data.last_page,
							next_page_url: data.next_page_url,
							prev_page_url: data.prev_page_url,
							from: data.from,
							to: data.to
						};
						self.isLoading = false;
					});
			},
			init: function() {
				this.refreshBids();
				var self = this;
				socket.on('approve-bid', function(data) {
					var bids = self.bids;
					var index = bids.findIndex(x => x.id == data.id);
					// console.log('data', data);
					// console.log('index', index);
					if(self.bids[index]) {
						self.set('bids.'+ index, data);
						self.notifyPath('bids.' + index);
						self.notifyPath('bids.' + index + '.is_approved');
						// console.log('bids', self.bids);
					}
					if(self.indexOpened > -1) {
						if(index == self.indexOpened) {
							self.thirdFold.$.bid.refreshBid();
							// console.log('bid.'+index+' open');
						}
					}
				});
				socket.on('new-bidder', function(data) {
					// console.log('new bidder', data); // working
					self.thirdFold.$.bid.refreshBid();
				});
				this.isInit = true;
			},
		});
	}());
</script>
