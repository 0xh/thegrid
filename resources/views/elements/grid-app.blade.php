<!-- <link rel="import" href="/elements/elements.html" /> -->
<link rel="import" href="/grid-elements" />

<dom-module id="grid-app">
	<style include="iron-flex">
		:host {
			--grid-view-left: 56px;
			--grid-view-left-drawer-open: 150px;
			--grid-view-second-fold-left: 250px;
			--grid-view-third-fold-left: 500px;
		}
		div[main] {
			height: 100%;
		}
		grid-view {
			left: var(--grid-view-left);
		    position: absolute;
		    top: 0px;
		    width: calc(100% - 56px);
		    height: 100%;
		    z-index: 0;
		    transition: left 350ms cubic-bezier(0.4, 0, 0.2, 1);/*, right 350ms cubic-bezier(0.4, 0, 0.2, 1);*/
		}
		grid-drawer[opened] ~ grid-view {
			left: var(--grid-view-left-drawer-open);
		}
		grid-second-fold[opened] ~ grid-view {
			left: var(--grid-view-second-fold-left);
		}
		grid-second-fold[opened] ~ grid-third-fold {
			left: calc((var(--grid-second-fold-width) + var(--grid-drawer-collapse-width)) - var(--grid-third-fold-width));
		}
		grid-third-fold[opened] ~ grid-view {
			left: var(--grid-view-third-fold-left);
		}
		grid-second-fold[opened] ~ grid-third-fold[opened] {
			left: calc(var(--grid-drawer-collapse-width) + var(--grid-second-fold-width)) !important;
		}

	</style>
	<template>
		<div main class="layout horizontal">
			<grid-drawer id="drawer"></grid-drawer>
			<grid-second-fold id="secondFold"></grid-second-fold>
			<grid-third-fold id="thirdFold" component=""></grid-third-fold>
			<grid-view id="view" class="flex"></grid-view>
		</div>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'
		Polymer({
			is: 'grid-app',
			get drawer() {
				return this.$.drawer;
			},
			get inbox() {
				return this.$.inbox;
			},
			_click: function() {
				console.log('click from child');
				console.log(this.customStyle);
			},
			_openInbox: function() {
				this.drawer.opened = false;
				this.inbox.opened = true;
			},
			_toggleInbox: function() {
				this.inbox._toggleInbox();
			},
			ready: function() {
				
			}
		});
	}());
</script>
