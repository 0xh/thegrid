<dom-module id="grid-conversation">
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
		.chat {
			list-style-type: none;
			width: 100%;
			padding: 0;
		}
		.chat:after {
			content: ' ';
			display: block;
			height: 0;
			clear: both;
		}

		.chat-bubble {
			margin-bottom: 3px;
			padding: 5px 10px;
			clear: both;
			border-radius: 10px 10px 2px 2px;
		}

		.chat-bubble-rcvd {
			background: #f2f2f2;
			color: black;
			float: left;
			border-bottom-right-radius: 10px;
		}
		.chat-bubble-sent {
			background: #0066ff;
			color: white;
			float: right;
			border-bottom-left-radius: 10px;
		}

		.chat-bubble-sent + .chat-bubble-sent {
			border-top-right-radius: 2px;
		}

		.chat-bubble-rcvd + .chat-bubble-rcvd {
			border-top-left-radius: 2px;
		}
		.chat-bubble-stop, li.chat-bubble:nth-last-child(2) {
			border-bottom-right-radius: 10px !important;
			border-bottom-left-radius: 10px !important;
		}
		.typing {
			font-style: italic;
		}
		#msgs  {
			min-height: 100%;
			height: 100%;
		}
	</style>
	<template>
		<div class="layout vertical flex h100">
			<paper-scroll-header-panel id="scroller" class="flex" fixed>
				<paper-toolbar slot="header" class="border-bottom">
					<div class="flex">
						<paper-icon-button icon="first-page" on-tap="closeParent"></paper-icon-button>
						<paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
						<template is="dom-if" if="[[!isTyping]]">
							<span>@{{conversation.job.name}}</span>
						</template>
						<template is="dom-if" if="[[isTyping]]">
							<span class="typing">Typing...</span>
						</template>
					</div>
					<paper-icon-button icon="more-vert"></paper-icon-button>
				</paper-toolbar>
				<div id="msgs" role="list-box">
					<template is="dom-if" if="[[isLoading]]">
						<paper-progress indeterminate></paper-progress>
					</template>
					<div class="container h100">
						<div class="layout vertical flex h100">
							<div id="filler" class="flex"></div>
							<template is="dom-if" if="[[pagination.next_page_url]]">
								<div on-tap="loadConversation">
									Load more...
								</div>
							</template>
							<div>
								<ul class="chat">
									<template is="dom-repeat" items="[[messages]]" sort="_sort">
										<li id="[[item.id]]" data-index$="[[index]]" class$="chat-bubble @{{_getClass(item.author_id, index)}}" title="[[item.created_at]]">
											[[item.message]]
										</li>
									</template>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</paper-scroll-header-panel>
			<paper-toolbar class="border-top">
				<paper-input id="message" name="message" label="Write..." type="text" class="flex" value="@{{message}}" required autofocus error-message="@{{errorMessage}}" on-keydown="typing"></paper-input>
				<paper-icon-button icon="send" on-tap="sendMessage"></paper-icon-button>
			</paper-toolbar>
		</div>
	</template>
	<script>
		Polymer({
			is: 'grid-conversation',
			properties: {
				id: {
					type: Number,
					value: 0,
					notify: true
				},
				conversation: {
					type: Object
				},
				isLoading: {
					type: Boolean,
					value: true
				},
				isTyping: {
					type: Boolean,
					value: false
				},
				message: {
					type: String,
					notify: true
				},
				messages: Object,
				author: Object,
				recipient: Object,
				myid: {
					type: Number,
					value: Grid.user_id
				},
				i: Number,
				pagination: {
					type: Object,
					value: function() {
						return [];
					},
					notify: true
				}
			},
			observers: ['_conversationChange(id)'],
			behaviors: [
			GridBehaviors.FoldBehavior,
			GridBehaviors.StorageBehavior
			],
			listeners: {
			"content-scroll": "scroll"
			},
			scroll: function(e, d){
				if (d.target.scrollTop <= 0) {
					this.loadConversation();
				}
   			},
   			restore: function() {
   				this.$.scroller.scroll(this.$.msgs.scrollHeight - this.previousScrollHeightMinusTop);
   				console.log('scroll', this.$.msgs.scrollHeight - this.previousScrollHeightMinusTop);
   			},
   			prepareFor: function(d) {
   				this.readyFor = 'up';
   				this.previousScrollHeightMinusTop = this.$.msgs.scrollHeight - this.$.msgs.scrollTop;
   				console.log('previousScrollHeightMinusTop', this.previousScrollHeightMinusTop);
   			},
   			loadConversation: function() {
   				var self = this,
   					pagination = self.pagination;
   				if(pagination.next_page_url) {
					self.isLoading = true;
					self.prepareFor('up');
					axios.get(pagination.next_page_url)
						.then(function (response) {
							var data = response.data,
								c_m = self.messages;
							// data.data.sort(function(a,b){
							// 	return new Date(a.created_at) - new Date(b.created_at);
							// });
							self.messages = data.data.concat(c_m);
							setTimeout(function() {
								self.restore();
							}, 50);
							self.setSessionStorage('cnv-'+self.id, JSON.stringify(self.messages));
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
			_conversationChange: function(id) {
				var self = this;
				if ( id ) {
					self.isLoading = true;
					if(self.getSessionStorage('cnv-'+self.id)) {
						// self.set('messages', JSON.parse(self.getSessionStorage('cnv-'+self.id)));
						// self.isLoading = false;
						// self.scrollToBottom();
					} else {
						self.set('messages', null);
					}
					axios.get('/users/'+Grid.user_id+'/conversations/'+id)
					.then(function(response) {
						var data = response.data;
						// data.data.sort(function(a,b){
						// 	return new Date(a.created_at) - new Date(b.created_at);
						// });
						self.messages = data.data;

						var author_id, recipent_id;
						if ( Grid.user_id == self.conversation.user_id_1 ) {
							self.author = self.conversation.user1;
							self.recipient = self.conversation.user2;
						} else {
							self.author = self.conversation.user2;
							self.recipient = self.conversation.user1;
						}
						self.isLoading = false;
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
						self.scrollToBottom();
						self.setSessionStorage('cnv-'+self.id, JSON.stringify(self.messages));
					})
					.catch(function(error) {

					});
				}
			},
			setAuthor: function(id) {
				var self = this;
				axios.get('/users/'+id)
				.then(function(response) {
					self.author = response.data;
				})
				.catch(function(error) {

				});
			},
			setRecipient: function(id) {
				var self = this;
				axios.get('/users/'+id)
				.then(function(response) {
					self.recipient = response.data;
				})
				.catch(function(error) {

				});
			},
			_isMine: function(id) {
				return Grid.user_id == id;
			},
			_getClass: function(id, index) {
				if ( this.i != id ) {
					this.i = id;
					var li = document.querySelector('li[data-index="'+(index-1)+'"]');
					if(li) {
						li.className += " chat-bubble-stop";
					}
				}
				if ( Grid.user_id == id ) {
					return 'chat-bubble-sent';
				} else {
					return 'chat-bubble-rcvd';
				}
			},
			sendMessage: function() {
				var self = this;
				axios.post('/users/'+Grid.user_id+'/conversations/'+self.id, {
					message: self.message,
					recipient_id: self.recipient.id
				})
				.then(function(response) {
					var data = response.data;
					self.message = '';
					self.push('messages', data);
					socket.emit('new-message', data);
					self.scrollToBottom(true);
					self.setSessionStorage('cnv-'+self.id, JSON.stringify(self.messages));
				})
				.catch(function(error) {

				});
			},
			_sort: function(a, b) {
				a = new Date(a.created_at);
				b = new Date(b.created_at);
				return b > a ? -1 : b < a ? 1 : 0;
			},
			typing: function(e) {
				var self = this;
				if (e.keyCode === 13) {
					self.sendMessage();
					return;
				}
				if (self.timeout) clearTimeout(self.timeout);
				self.timeout = setTimeout(function() {
					socket.emit('typing', {
						recipient_id: self.recipient.id,
						conversation_id: self.id
					});
				}, 500);
			},
			scrollToBottom: function(smooth) {
				var self = this;
				setTimeout(function(){
					var height = self.$.msgs.scrollHeight;
					self.$.scroller.scroll(height, smooth);
				}, 50);
			},
			close: function() {
				this.thirdFold.close();
			},
			closeParent: function() {
				this.secondFold.close();
			},
			attached: function() {
				var self = this;
				socket.on('receive-message', function(data) {
					if ( ! data ) return;
					if ( data.conversation_id == self.id ) {
						self.push('messages', data);
						self.setSessionStorage('cnv-'+self.id, JSON.stringify(self.messages));
						self.isTyping = false;
						self.scrollToBottom(true);
					}
				});
				socket.on('sender-typing', function(data) {
					if(data.conversation_id == self.id) {
						self.isTyping = true;
						if(self.senderTimeout) clearTimeout(self.senderTimeout);
						self.senderTimeout = setTimeout(function() {
							self.isTyping = false;
						}, 1000);
					}
				});
				this.previousScrollHeightMinusTop = 0;
			}
		});
	</script>
</dom-module>
