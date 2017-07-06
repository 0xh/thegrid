<link rel="import" href="/bower_components/paper-fab/paper-fab.html">
<link rel="import" href="/bower_components/paper-toolbar/paper-toolbar.html">
<link rel="import" href="/bower_components/paper-ripple/paper-ripple.html">
<dom-module id="grid-fab-toolbar">
	<style>
		:host {

		}
		:host paper-toolbar {

		}
		#toolbarContent {

		}
		paper-ripple {
			color: #f00;
		}
		.grid-fab-toolbar-wrapper {
			overflow: hidden;
			height: 64px;
			/*position: relative;*/
			transition-property: all;
			transform-style: cubic-bezier(0.4, 0, 0.2, 1);
		}
		.grid-fab-toolbar-wrapper ::slotted(paper-fab) {
			/*position: absolute;*/
			/*right: 0;*/
			/*bottom: 0;*/
		}
		.grid-fab-toolbar ::slotted(paper-toolbar) {
			/*position: absolute;*/
			background-color: transparent;
		}
		.grid-fab-toolbar-triger {
			/*width: 56px;*/
			/*height: 56px;*/
			/*position: absolute;*/
			/*right: 10px;*/
			/*top: 0px;*/
			/*z-index: 20;*/
			/*transform: translate(0px, 15px);*/
			transition-property: all;
			transform-style: cubic-bezier(0.4, 0, 0.2, 1);
		}
		.grid-fab-toolbar-background {
			width: 100%;
		    height: 100%;
		    position: absolute;
		    right: 0;
		    top: 0;
		    border-radius: 50%;
		    background-color: #e00008;
		    pointer-events: none;
		    transform: scale(0);
		    transform-origin: center;
		    /*transition-property: transform;
			transform-style: cubic-bezier(0.4, 0, 0.2, 1);
			transition-duration: 350ms;*/
			-webkit-transition: all .3s cubic-bezier(.55,0,.55,.2);
    		transition: all .3s cubic-bezier(.55,0,.55,.2);
		}
		:host[transformed] .grid-fab-toolbar-background {
			transform: scale(25);
		}
		.grid-fab-toolbar-container {
			height: 64px;
			width: 100%;
			background-color: transparent;
			pointer-events: none;
			position: relative;
			z-index: 23;
		}
		#trigger {
			float: right;
		}
	</style>
	<template>
		<div id="wrapper" class="grid-fab-toolbar-wrapper" on-blur="original">
			<div id="trigger" class="grid-fab-toolbar-triger" on-tap="transform">
				<slot id="fabContent" select="paper-fab"></slot>
				<div id="bg" class="grid-fab-toolbar-background" ></div>
			</div>
			<div id="toolbarContainer" class="grid-fab-toolbar-container">
				<slot id="toolbarContent" select="paper-toolbar"></slot>
			</div>
		</div>
	</template>
</dom-module>
<script>
	(function(){

		Polymer({
			is: 'grid-fab-toolbar',
			properties : {
				opened: {
					type: Boolean,
					value: false,
					notify: true,
					reflectToAttribute: true,
					observer: '_gridFabToolbarStatus'
				},
				fabPosition : {
					type: String,
					value: 'top'
				},
				direction: {
					type: String,
					value: 'left'
				}
			},
			// listeners: {
			// 	// 'tap': 'regularTap',
			// 	'add.tap': 'specialTap'
			// },
			attached: function() {
				var fabStyles = window.getComputedStyle(this.fab);
				this.$.bg.style.backgroundColor = fabStyles.backgroundColor;
				this.$.trigger.style.width = fabStyles.width;
				this.$.trigger.style.height = fabStyles.height;
				if(this.fabPosition == 'top') {
					this.$.trigger.style.marginTop = '0';
					this.$.trigger.style.marginBottom = 'auto';
				} else {
					this.$.trigger.style.marginTop = 'auto';
					this.$.trigger.style.marginBottom = '0';
				}

				if(this.direction == 'left') {
					this.$.trigger.style.marginRight = '10px';
					this.$.trigger.style.float = 'right';
				} else if(this.direction == 'right') {
					this.$.trigger.style.float = 'lefy';
					this.$.trigger.style.marginLeft = '10px';
				}
			},
			_gridFabToolbarStatus: function() {
				if(this.opened) {
					this.fire('grid-fab-toolbar-opened');
				} else {
					this.fire('grid-fab-toolbar-closed');
				}
			},
			get fab() {
				return Polymer.dom(this.$.fabContent).getDistributedNodes()[0];
			},
			get toolbar() {
				return Polymer.dom(this.$.toolbarContent).getDistributedNodes()[0];
			},
			transform: function(e) {
				e.stopPropagation();

				var scale = 2 * (this.$.wrapper.offsetWidth / this.fab.offsetWidth);
				this.$.bg.style.transitionDelay = '50ms';
				this.$.bg.style.transform = 'scale('+scale+')';
				this.$.trigger.style.transitionDelay = '0ms';
				this.$.trigger.style.transitionDuration = '200ms';
				var tx = this.$.wrapper.offsetWidth / 2;
				var ty = '-';
				if(this.direction == 'right') {
					ty = '';
				}
				tx = 30;
				if(this.fabPosition == 'top') {
					this.$.trigger.style.transform = 'translate('+ty+tx+'px, 8px)';
				} else {
					this.$.trigger.style.transform = 'translate('+ty+tx+'px, -8px)';
				}
				//this.transformed = true;
				this.$.toolbarContainer.style.pointerEvents = 'inherit';
				var items = this.toolbar.querySelectorAll('.item');
				items.forEach(function(item, index) {
					item.style.transform = 'scale(1)';
					// item.style.transitionDelay = '.3s';
					if(self.direction == 'right') {
						item.style.transitionDelay = 350 + (index * 25) + 'ms';
					} else {
						item.style.transitionDelay = ((items.length * 25) +  350) - (index * 25) + 'ms';
					}
				});

				this.$.wrapper.style.transitionDelay = '350ms';
				this.$.wrapper.style.backgroundColor = window.getComputedStyle(this.fab).backgroundColor;

				this.opened = true;
			},
			close: function() {
				//this.transformed = false;
				this.$.bg.style.transitionDelay = '0ms';
				this.$.bg.style.transform = 'scale(0)';
				this.$.toolbarContainer.style.pointerEvents = 'none';
				this.$.trigger.style.transitionDelay = '.2s';
				//if(this.fabPosition == 'top') {
					//this.$.trigger.style.transform = 'translate(0px, -8px)';
				//} else {
					this.$.trigger.style.transform = 'translate(0px, 0px)';
				//}

				this.$.wrapper.style.transitionDelay = '.3s';
				// setTimeout(function() {
				// 	self.$.wrapper.style.overflow = 'visible';
				// }, 300)
				var items = this.toolbar.querySelectorAll('.item');
				items.forEach(function(item, index) {
					item.style.transform = 'scale(0)';
					// item.style.transitionDelay = '.3s';
					item.style.transitionDelay = (items.length + index) * 25 + 'ms';
				});
				this.$.wrapper.style.transitionDelay = '0ms';
				this.$.wrapper.style.backgroundColor = 'transparent';

				this.opened = false;
			},
			ready: function() {
				// console.log(this.fab, this.toolbar);
				var self = this;
				var items = this.toolbar.querySelectorAll('.item');
				items.forEach(function(item, index) {
					item.style.transform = 'scale(0)';
					item.style.transitionDelay = '.3s';
					// item.style.transitionDelay = (items.length - index) * 25 + 'ms';
				});
				this.fab.addEventListener('click', function(e) {
					//this.fab.style.transform = 'scale(20)';

				});
				this.$.wrapper.addEventListener('click', function(e) {
					//this.fab.style.transform = 'scale(20)';
					// e.stopPropagation();
				});
				document.body.addEventListener('click', function(e) {
					// alert('body was clicked');
					// console.log(e);
					// self.close();
				});
			}
		});
	}());
</script>
