<link rel="import" href="/bower_components/paper-material/paper-material.html">

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
		<div on-tap="showStyles">Third Fold</div>
	</template>
</dom-module>
<script>
	
	(function(){
		'use strict'

		Polymer({
			is: 'grid-third-fold',
			behaviors: [GridBehaviors.FoldBehavior]
		});
	}());

</script>