<!-- <link rel="import" href="/grid-elements/2.grid-job">
<link rel="import" href="/grid-elements/2.grid-bid">
<link rel="import" href="/grid-elements/2.grid-confirmation">
<link rel="import" href="/grid-elements/2.grid-conversation"> -->

<dom-module id="grid-third-fold">
	<style include="iron-flex">
		:host {

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
					value: null,
					notify: true,
				}
			},
			observers: ['_lazyLoad(component)'],
			behaviors: [GridBehaviors.FoldBehavior],
			_lazyLoad: function(component) {
				if(component) {
					//var _component = document.createElement(component);
					//this.innerHTML = '';
					//this.appendChild(_component);
					var component = this.$$('#'+this.component);
					if(Polymer.isInstance(component)) {
						return;
					}
					Polymer.Base.importHref(
						'/grid-elements/2.grid-'+this.component,
						() => {
							
						}
					);
				}
			},
		});
	}());

</script>
