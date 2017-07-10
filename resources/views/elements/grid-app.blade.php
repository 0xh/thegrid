<!-- <link rel="import" href="/elements/elements.html" /> -->
<link rel="import" href="/grid-elements" />

<dom-module id="grid-app">
	<style include="iron-flex">
		:host {
			--grid-view-left: 56px;
			--grid-view-left-drawer-open: 150px;
			--grid-view-second-fold-left: 250px;
			--grid-view-third-fold-left: 500px;
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			overflow: hidden;
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
		grid-drawer {
			position: relative;
			height: 100%;
			z-index: 4;
			transition: width 350ms cubic-bezier(0.4, 0, 0.2, 1), right 350ms cubic-bezier(0.4, 0, 0.2, 1);
			background-color: #ffffff;
			width: var(--grid-drawer-collapse-width);
		}
		grid-drawer[opened] {
			width: var(--grid-drawer-expanded-width);
		}
		grid-drawer[opened] ~ grid-view {
			left: var(--grid-view-left-drawer-open);
		}
		grid-second-fold {
			width: var(--grid-second-fold-width);
			background-color: #ffffff;
			left: calc(var(--grid-drawer-collapse-width) - var(--grid-second-fold-width));
		    position: absolute;
		    top: 0px;
		    height: 100%;
		    z-index: 3;
		    transition: left 350ms cubic-bezier(0.4, 0, 0.2, 1);
		    display: block;
		    border-right: 1px solid #d6d6d6;
		}
		grid-second-fold[opened] {
			left: 56px;
		}
		grid-second-fold[opened] ~ grid-view {
			left: var(--grid-view-second-fold-left);
		}
		grid-third-fold {
			background-color: #FFFFFF;
			width: var(--grid-third-fold-width);
			position: absolute;
			left: calc(var(--grid-drawer-collapse-width) - var(--grid-third-fold-width));
			height: 100%;
			z-index: 2;
			@apply(--grid-transition-effect);
		}
		grid-second-fold[opened] ~ grid-third-fold {
			left: calc((var(--grid-second-fold-width) + var(--grid-drawer-collapse-width)) - var(--grid-third-fold-width));
		}
		grid-third-fold[opened] ~ grid-view {
			left: var(--grid-view-third-fold-left);
		}
		grid-second-fold[opened] ~ grid-third-fold[opened] {
			left: calc(var(--grid-drawer-collapse-width) + var(--grid-second-fold-width));
		}

		[tablet] grid-second-fold {
			width: calc(50% - var(--grid-drawer-collapse-width));
		}
		[tablet] grid-third-fold {
			width: 50%;
			left: -100%;
		}
		[tablet] grid-second-fold[opened] ~ grid-third-fold {
			left: -50%;
		}
		[tablet] grid-second-fold[opened] ~ grid-third-fold[opened] {
			left: 50%;
		}

		[mobile] grid-second-fold {
			width: 100%;
			left: 100%
		}

		[mobile] grid-second-fold[opened] {
			left: 0;
		}

		[mobile] grid-second-fold[opened] ~ grid-third-fold {
			width: 100%;
			left: 100%;
			z-index: 3;
		}

		[mobile] grid-second-fold[opened] ~ grid-third-fold[opened] {
			/*width: calc(100% - var(--grid-drawer-collapse-width));*/
			left: 0;
		}

		[mobile] grid-third-fold {
			width: calc(100% - var(--grid-drawer-collapse-width));
			left: 100%
		}

		[mobile] grid-drawer {
			left: calc(0px - var(--grid-drawer-expanded-width));
			width: var(--grid-drawer-expanded-width);
			max-width: 100%;
			transition: left 350ms cubic-bezier(0.4, 0, 0.2, 1);
		}

		[mobile] grid-drawer[opened] {
			left: 0;
		}

		[mobile] grid-view {
			left: 0 !important;
			width: 100%;
		}

		grid-mobile-header {
			display: none;
		}

		[mobile] grid-mobile-header {
      display: block;
    }

	</style>
	<dom-bind>
		<template>
			<iron-media-query query="(max-width: 600px)" query-matches="@{{mobile}}"></iron-media-query>
			<iron-media-query query="(min-width: 601px) and (max-width: 800px)" query-matches="@{{tablet_p}}"></iron-media-query>
			<iron-media-query query="(max-width: 1024px)" query-matches="@{{tablet_l}}"></iron-media-query>
			<iron-media-query query="(max-width: 1280px)" query-matches="@{{desktop}}"></iron-media-query>
			<iron-media-query query="(min-width: 1281px)" query-matches="@{{desktop_h}}"></iron-media-query>
			<div main class="main-layout layout horizontal" mobile$="@{{mobile}}"" tablet$="@{{tablet_p}}"">
				<grid-mobile-header></grid-mobile-header>
				<grid-drawer id="drawer"></grid-drawer>
				<grid-second-fold id="secondFold"></grid-second-fold>
				<grid-third-fold id="thirdFold" component=""></grid-third-fold>
				<grid-view id="view" class="flex"></grid-view>
			</div>
		</template>
	</dom-bind>
</dom-module>
{{--
--grid-drawer-collapse-width: 56px; /* 72px */
--grid-drawer-expanded-width: 253px; /* 333px */
--grid-second-fold-width: 400px; /* 519px */
--grid-third-fold-width-comp: calc(100% - (var(--grid-drawer-collapse-width) + var(--grid-second-fold-width)));
--grid-third-fold-width: calc(100% - 456px); /*var(--grid-third-fold-width-comp);*/
--grid-third-fold-min-width: 400px;

--grid-view-left: 56px;
--grid-view-left-drawer-open: 150px;
--grid-view-second-fold-left: 250px;
--grid-view-third-fold-left: 500px;
--}}
<script>
	(function(){
		'use strict'
		Polymer({
			is: 'grid-app',
			properties: {},
			get drawer() {
				return this.$.drawer;
			},
			get inbox() {
				return this.$.inbox;
			},
			_click: function() {
			},
			_openInbox: function() {
				this.drawer.opened = false;
				this.inbox.opened = true;
			},
			_toggleInbox: function() {
				this.inbox._toggleInbox();
			},
			ready: function() {
				if(!Polymer.isInstance(this.$.view)) {
					Polymer.Base.importHref(
						'/grid-elements/grid-view',
						() => {
							console.log('view has been loaded');
						}
					);
				}
				if(!Polymer.isInstance(this.$.thirdFold)) {
					Polymer.Base.importHref(
						'/grid-elements/grid-third-fold',
						() => {
							console.log('thirdFold has been loaded');
						}
					);
				}
				if(!Polymer.isInstance(this.$.secondFold)) {
					Polymer.Base.importHref(
						'/grid-elements/grid-second-fold',
						() => {
							console.log('secondFold has been loaded');
						}
					);
				}

			}
		});
	}());
</script>
