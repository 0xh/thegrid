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
					<paper-icon-item on-tap="viewConversation">
						<iron-icon icon="account-circle" item-icon></iron-icon>
						<paper-item-body two-line>
							<div>AED @{{item.job.price}} | @{{item.job.name}}</div>
							<div secondary>@{{item.job.created_at}}</div>
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
			viewConversation: function(e) {
				var conversation = e.model.item;
				this.thirdFold.component = 'conversation';
				this.thirdFold.$.conversation.id = conversation.id;
				this.thirdFold.$.conversation.conversation = conversation;
				this.thirdFold.open();
			},
			init: function() {
				var self = this;
				axios.get('/users/'+Grid.user_id+'/conversations')
					.then(function(response) {
						var data = response.data;
						self.inbox = data;
					})
					.catch(function(error) {

					});
				socket.on('receive-message', function(data) {
					console.log(data);
				});
			},

		});
	}());
</script>
