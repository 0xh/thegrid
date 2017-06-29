<link rel="import" href="/grid-elements/2.grid-job">
<link rel="import" href="/grid-elements/2.grid-bid">
<link rel="import" href="/grid-elements/2.grid-confirmation">
<link rel="import" href="/grid-elements/2.grid-conversation">

<dom-module id="grid-third-fold">
	<style include="iron-flex">
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
		iron-pages, iron-pages section {
			height: 100%;
		}
	</style>
	<template>
		<iron-pages selected="@{{component}}" attr-for-selected="data-component">
			<section data-component="job">
				<grid-job id="job"></grid-job>
			</section>
			<section data-component="bid">
				<grid-bid id="bid"></grid-bid>
			</section>
			<section data-component="conversation">
				<grid-conversation id="conversation"></grid-conversation>
			</section>
			<section data-component="confirmation">
				<grid-confirmation id="confirmation"></grid-confirmation>
			</section>
		</iron-pages>
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
					value: 'job',
					notify: true,
				}
			},
			// observers: ['_insertComponent(component)'],
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