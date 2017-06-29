<dom-module id="grid-conversation">
	<style include="iron-flex">
		:root {
			--paper-toolbar-background: #FFFFFF;
			--paper-toolbar-color: #636b6f;
		}
		:host {
			height: 100%;
		}
		.container {
			margin: 0 20px;
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

		.chat__bubble {
			margin-bottom: 3px;
			padding: 5px 10px;
			clear: both;
			border-radius: 10px 10px 2px 2px;
		}

		.chat__bubble--rcvd {
			background: #f2f2f2;
			color: black;
			float: left;
			border-bottom-right-radius: 10px;
		}

		.chat__bubble--sent {
			background: #0066ff;
			color: white;
			float: right;
			border-bottom-left-radius: 10px;
		}

		.chat__bubble--sent + .chat__bubble--sent {
			border-top-right-radius: 2px;
		}

		.chat__bubble--rcvd + .chat__bubble--rcvd {
			border-top-left-radius: 2px;
		}
		.chat__bubble--stop {
			border-bottom-right-radius: 10px;
			border-bottom-left-radius: 10px;
		}
	</style>
	<template>
		<paper-header-panel class="flex">
			<paper-toolbar slot="header">
				<div class="flex">@{{conversation.job.name}}</div>
				<paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
			</paper-toolbar>
			<div role="">
				<template is="dom-if" if="[[isLoading]]">
					<paper-progress indeterminate></paper-progress>
				</template>
				<div class="container">
					<div>
						<ul class="chat">
							<template is="dom-repeat" items="[[messages]]" sort="_sort">
								<li id="[[item.id]]" class$="chat__bubble @{{_getClass(item.author_id, item)}}">
									[[item.message]]
								</li>
							</template>
	{{-- 						<li class="chat__bubble chat__bubble--rcvd chat__bubble--stop">What are you up to?</li>
	 <li class="chat__bubble chat__bubble--sent">Not much.</li>
	 <li class="chat__bubble chat__bubble--sent">Just writing some CSS.</li>
	 <li class="chat__bubble chat__bubble--sent">I just LOVE writing CSS.</li>
	 <li class="chat__bubble chat__bubble--sent chat__bubble--stop">Do you?</li>
	 <li class="chat__bubble chat__bubble--rcvd">Yeah!</li>
	 <li class="chat__bubble chat__bubble--rcvd">It's super fun.</li>
	 <li class="chat__bubble chat__bubble--rcvd chat__bubble--stop">... SUPER fun.</li> --}}
						</ul>
					</div>
					<template is="dom-if" if="[[isTyping]]">
						<span>Typing...</span>	
					</template>
					<paper-input id="message" name="message" label="Write..." type="text" value="@{{message}}" required autofocus error-message="@{{errorMessage}}" on-keydown="typing"></paper-input>
					<paper-button raised on-tap="sendMessage">Send</paper-button>
				</div>
			</div>
		</paper-header-panel>
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
			},
			observers: ['_conversationChange(id)'],
			behaviors: [
			GridBehaviors.FoldBehavior
			],
			_conversationChange: function(id) {
				var self = this;
				if ( id ) {
					self.isLoading = true;
					axios.get('/users/'+Grid.user_id+'/conversations/'+id)
					.then(function(response) {
						var data = response.data;
						self.messages = data;
						var author_id, recipent_id;
						if ( Grid.user_id == self.conversation.user_id_1 ) {
							self.author = self.conversation.user1;
							self.recipient = self.conversation.user2;
						} else {
							self.author = self.conversation.user2;
							self.recipient = self.conversation.user1;
						}
						self.isLoading = false;
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
			_getClass: function(id, item) {
				console.log(item);
				if ( Grid.user_id == id ) {
					return 'chat__bubble--sent';
				} else {
					return 'chat__bubble--rcvd';
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
				})
				.catch(function(error) {

				});
			},
			_sort: function(a, b) {
				a = new Date(a.created_at);
				b = new Date(b.created_at);
				return b>a ? -1 : b<a ? 1 : 0;
			},
			typing: function(e) {
				var self = this;
				if (e.keyCode === 13) {
					self.sendMessage();
					return;
				}
                //var searchTimeout;
                if (self.timeout) clearTimeout(self.timeout);
                self.timeout = setTimeout(function() {
                	socket.emit('typing', {
                		recipient_id: self.recipient.id
                	});
                }, 500);
            },
            close: function() {
            	this.thirdFold.close();
            },
            attached: function() {
            	var self = this;
            	socket.on('receive-message', function(data) {
            		if ( ! data ) return;
            		if ( data.conversation_id == self.id ) {
            			self.push('messages', data);
            			self.isTyping = false;
            		} 
            	});
            	socket.on('sender-typing', function() {
            		self.isTyping = true;
            		if(self.senderTimeout) clearTimeout(self.senderTimeout);
            		self.senderTimeout = setTimeout(function() {
            			self.isTyping = false;
            		}, 1000);
            	});
            }
        });
    </script>
</dom-module>