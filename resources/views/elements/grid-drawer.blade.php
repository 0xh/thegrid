<link rel="import" href="/bower_components/paper-material/paper-material.html">
<link rel="import" href="/bower_components/paper-ripple/paper-ripple.html">

<dom-module id="grid-drawer">
	<style>
		:host {

		}
		:host[opened] {
			
		}
		paper-material#menu {
			padding: var(--paper-material-padding);
			height: 100%;
		}
		paper-material#menu .menu-item {
			display: block;
			height: 40px;
			overflow: hidden;
			position: relative;
			line-height: 24px;
		}
		paper-material#menu .menu-item.account {
			height: 200px;
		}
		paper-material#menu .menu-item.account .profile-image {
			height: 75px;
		}
		paper-material#menu .menu-item.account .profile {
			padding: 8px;
			opacity: 0;
			/*white-space: nowrap;*/
			transition-property: opacity;
			transform-style: cubic-bezier(0.4, 0, 0.2, 1);
			transition-duration: 250ms;
			/*transition-delay: 0ms;*/
		}
		:host[opened] paper-material#menu .menu-item.account .profile {
			transition-delay: 250ms;
			/*transition-duration: 250ms;*/
			opacity: 1;
		}
		paper-material#menu .menu-item.account .profile p {
			margin: 0;
		}
		paper-material#menu .menu-item.account paper-icon-button {
			transition: height 350ms cubic-bezier(0.4, 0, 0.2, 1), width 350ms cubic-bezier(0.4, 0, 0.2, 1);
		}
		:host[opened] paper-material#menu .menu-item.account paper-icon-button {
			height: 75px;
			width: 75px;
		}
		paper-material#menu .menu-item.hidden {
			visibility: hidden;
			opacity: 0;
			transition: visibility 350ms, opacity 350ms cubic-bezier(0.4, 0, 0.2, 1);
		}
		:host[opened] paper-material#menu .menu-item.hidden {
			visibility: visible;
			opacity: 1;
		}
		paper-material#menu .menu-item .menu-item-icon {
			float: left;
			position: relative;
		}
		paper-material#menu .menu-item .menu-item-icon .icon-badge {
			position: absolute;
			top: 5px;
			right: 5px;
			width: 10px;
			height: 10px;
			border-radius: 50%;
			background-color: red;
			z-index: 3;
			opacity: 1;
			transition: opacity 350ms cubic-bezier(0.4, 0, 0.2, 1);
		}
		paper-material#menu .menu-item .menu-item-text .icon-badge {
			float: right;
		    color: #ffffff;
		    background: red;
		    border-radius: 50%;
		    width: 24px;
		    height: 24px;
		    text-align: center;
		    font-style: normal;
		    opacity: 0;
		    transition-property: opacity;
			transform-style: cubic-bezier(0.4, 0, 0.2, 1);
			transition-duration: 100ms;
			transition-delay: 0ms;
		}
		:host[opened] paper-material#menu .menu-item .menu-item-text .icon-badge {
			transition-duration: 250ms;
			transition-delay: 350ms;
			opacity: 1;
		}
		paper-material#menu .menu-item .menu-item-text {
			float: left;
			width: 194px;
			width: calc(100% - 40px);
			padding: 8px;
			line-height: 24px;
			white-space: nowrap;
			opacity: 0;
		    transition-property: opacity;
			transform-style: cubic-bezier(0.4, 0, 0.2, 1);
			transition-duration: 350ms;
			/*transition-delay: 200ms;*/
		}
		:host[opened] paper-material#menu .menu-item .menu-item-text {
			opacity: 1;
		}
		paper-material#menu .menu-item .menu-item-text span {

		}
		:host[opened] paper-material#menu .menu-item span {
			/*display: inline-block;*/
		}
		:host[opened] paper-material#menu .menu-item .menu-item-icon .icon-badge {
			opacity: 0;
		}
		.divider {
			height: 1px;
			background-color: #d6d6d6;
			margin: 10px -8px;
		}
		.bottom {
			position: absolute;
			bottom: 0;
		}
	</style>
	<template>
		<paper-material id="menu" elevate="2" class="flex layout vertical">
			<div class="menu-item">
				<paper-icon-button icon="menu" on-tap="toggle"></paper-icon-button>
				<span>THE GRID</span>
			</div>
			<div class="menu-item account">
				<div class="profile-image">
				@if (Auth::guest())
					<paper-icon-button icon="account-circle" on-tap="openAuth"></paper-icon-button>
				@else
					<paper-icon-button icon="account-circle" on-tap="openProfile"></paper-icon-button>
				@endif
				</div>
				<div class="profile">
					@if (Auth::guest())
                        <p on-tap="openAuth">
                        	Sign In/Sign up
                        	<paper-ripple></paper-ripple>
                        </p>
					@else
						<p>Hello,</p>
					    <strong>{{ Auth::user()->name }}</strong>
					@endif
				</div>
			</div>
			<div class="divider"></div>
			<div class="menu-item" on-tap="openJobs">
				<div class="menu-item-icon">
					<paper-icon-button icon="work"></paper-icon-button>
				</div>
				<div class="menu-item-text">
					<span>Posts</span>
					<paper-ripple></paper-ripple>
				</div>
			</div>
			<div class="menu-item" on-tap="openBids">
				<div class="menu-item-icon">
					<paper-icon-button src="/images/BID.svg"></paper-icon-button>
				</div>
				<div class="menu-item-text">
					<span>Bids</span>
					<paper-ripple></paper-ripple>
				</div>
			</div>
			<div class="menu-item" on-tap="openInbox" data-tab="inbox">
				<div class="menu-item-icon">
					<i class="icon-badge"></i>
					<paper-icon-button icon="mail"></paper-icon-button>
				</div>
				<div class="menu-item-text">
					<span>Inbox</span>
					<i class="icon-badge">2</i>
					<paper-ripple></paper-ripple>
				</div>
			</div>
			<div class="menu-item" on-tap="openTransactions">
				<div class="menu-item-icon">
					<paper-icon-button icon="credit-card"></paper-icon-button>
				</div>
				<div class="menu-item-text">
					<span>Transactions</span>
					<paper-ripple></paper-ripple>
				</div>
			</div>
			<div class="divider"></div>
			<div class="menu-item hidden">
				<div class="menu-item-icon">
					<paper-icon-button icon="settings"></paper-icon-button>
				</div>
				<div class="menu-item-text">
					<span>Settings</span>
					<paper-ripple></paper-ripple>
				</div>
			</div>
			<div class="menu-item hidden">
				<div class="menu-item-icon">
					<paper-icon-button icon="help"></paper-icon-button>
				</div>
				<div class="menu-item-text">
					<span>Help &amp; Feedback</span>
					<paper-ripple></paper-ripple>
				</div>
			</div>
<!-- 			<div class="bottom">
					<span>Download The Grid for iOS and Android</span>
					<div class="divider"></div>
					<span>Version 0.0.0.1</span><br/>
					<span>Privacy - Terms of Use - License</span>
			</div> -->
		</paper-material>
	</template>
</dom-module>

<script>
	(function(){
		'user strict'

		Polymer({
			is: 'grid-drawer',

			behaviors: [
				GridBehaviors.FoldBehavior,
				GridBehaviors.PageBehavior
			],

			_closeOtherFolds: function() {
				this.secondFold.close();
				this.thirdFold.close();
			},

			_onOpen: function() {
				// console.log('drawer has been opened');
				this._closeOtherFolds();
			},

			_onClose: function() {
				// console.log('drawer has been closed');
			},

			_selectTab: function(tab) {
				if(this.opened)
					this.close();
				if(this.thirdFold.opened)
					this.thirdFold.close();
				this.secondFold.open();
				this.secondFold.selectedTab = tab;
			},

			selectTabInbox: function() {
				this._selectTab('jobs');
			},

			openAuth: function() {
				this._selectTab('auth');
			},

			openProfile: function() {
				this._selectTab('profile');
			},

			// inbox events
			openInbox: function() {
				this._selectTab('inbox');
			},

			// jobs events
			openJobs: function() {
				this._selectTab('jobs');
			},

			openBids: function() {
				this._selectTab('bids');
			},

			// transactions events
			openTransactions: function() {
				this._selectTab('transactions');
			},
		});
	}());
</script>
