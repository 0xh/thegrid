<dom-module id="grid-inbox">
	<style include="iron-flex grid">
		:root {
			--paper-toolbar-background: #FFFFFF;
			--paper-toolbar-color: #636b6f;
		}
		:host {
			height: 100%;
		}
		#searchBar {
			background-color: #FFFFFF;
			/*pointer-events: none;*/
			/*transform: scale(0);*/
			transform-origin: right;
			-webkit-transition: all .3s cubic-bezier(.55,0,.55,.2);
			transition: all .3s cubic-bezier(.55,0,.55,.2);
			/*width: 40px;*/

		}
		#searchBar .h {
			opacity: 0;
		}
		#searchBar .search {
			width: 100%;
	    line-height: 40px;
	    border: none;
	    font-size: 16px;
	    font-weight: 100;
			outline: none;
		}
		#searchBar .search:active,
		#searchBar .search:focus {
			border: none;
			outline: none;
		}
		:host[searching] #searchBar .h {
			opacity: 1;
		}
		:host[searching] #searchBar {
			width: calc(100% - 32px);
			position: absolute;
			right: 16px;
			z-index: 10;
		}
	</style>
	<template>
		<paper-scroll-header-panel class="flex h100">
		    <paper-toolbar slot="header" class="border-bottom">
					<paper-icon-button icon="first-page" on-tap="close"></paper-icon-button>
		      <div class="flex">Inbox</div>
					<div id="searchBar" class="layout horizontal">
						<paper-icon-button icon="chevron-left" class="h fab-button" on-tap="closeSearch"></paper-icon-button>
			      <div class="h flex">
							<input type="search" id="search" class="search" placeholder="Search..." />
						</div>
			      <paper-icon-button icon="search" class="fab-button" on-tap="openSearch"></paper-icon-button>
			  	</div>
		      <paper-icon-button icon="more-vert"></paper-icon-button>
		    </paper-toolbar>
		    <div role="listbox">
		    	<template is="dom-if" if="[[isLoading]]">
		    		<paper-progress indeterminate class="w100"></paper-progress>
		    	</template>
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
		 </paper-scroll-header-panel>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-inbox',
			properties: {
				searching: {
					type: Boolean,
					value: false,
					reflectToAttribute: true
				}
			},
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
			openSearch: function() {
				this.searching = true;
				this.$.search.focus();
			},
			closeSearch: function() {
				this.searching = false;
			},
			init: function() {
				var self = this;
				axios.get('/users/'+Grid.user_id+'/conversations')
					.then(function(response) {
						var data = response.data;
						self.inbox = data;
						self.isLoading = false;
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
