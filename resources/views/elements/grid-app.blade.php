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
			width: calc(100% - var(--grid-second-fold-width));
			left: -100%
		}

		[mobile] grid-second-fold[opened] {
			width: calc(100% - var(--grid-drawer-collapse-width));
			left: var(--grid-drawer-collapse-width);
		}

		[mobile] grid-second-fold[opened] ~ grid-third-fold {
			width: calc(100% - var(--grid-drawer-collapse-width));
			left: calc(var(--grid-drawer-collapse-width) - 100%);
			z-index: 3;
		}

		[mobile] grid-second-fold[opened] ~ grid-third-fold[opened] {
			/*width: calc(100% - var(--grid-drawer-collapse-width));*/
			left: var(--grid-drawer-collapse-width);
		}

		[mobile] grid-third-fold {
			width: calc(100% - var(--grid-drawer-collapse-width));
			left: -100%
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

			}
		});
	}());
</script>
{{-- 
@media (min-width:320px) { /* smartphones, portrait iPhone, portrait 480x320 phones (Android) */ }
@media (min-width:480px) { /* smartphones, Android phones, landscape iPhone */ }
@media (min-width:600px) { /* portrait tablets, portrait iPad, e-readers (Nook/Kindle), landscape 800x480 phones (Android) */ }
@media (min-width:801px) { /* tablet, landscape iPad, lo-res laptops ands desktops */ }
@media (min-width:1025px) { /* big landscape tablets, laptops, and desktops */ }
@media (min-width:1281px) { /* hi-res laptops and desktops */ }
 --}}