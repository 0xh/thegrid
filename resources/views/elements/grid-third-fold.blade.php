<link rel="import" href="/bower_components/paper-material/paper-material.html">
<link rel="import" href="/grid-elements/2.grid-register">

<dom-module id="grid-third-fold">
	<style is="custom-style">
		:host {
			background-color: #FFFFFF;
			/*min-width: var(--grid-third-fold-min-width);*/
			width: var(--grid-third-fold-width);
			position: absolute;
			left: calc(var(--grid-drawer-collapse-width) - var(--grid-third-fold-width));
			height: 100%;
			z-index: 2;
			@apply(--grid-transition-effect);
		}
	</style>
	<template>
		
	</template>
</dom-module>
<script>
	
	(function(){
		'use strict'

		Polymer({
			is: 'grid-third-fold',
			properties: {
				component: {
					type: String,
					notify: true,
				}
			},
			observers: ['_insertComponent(component)'],
			behaviors: [GridBehaviors.FoldBehavior],
			_insertComponent: function(component) {
				if(component) {
					var _component = document.createElement(component);
					this.innerHTML = '';
					this.appendChild(_component);
				}
			},
		});
	}());

</script>