<link rel="import" href="/grid-elements/1.grid-auth">
<link rel="import" href="/grid-elements/1.grid-profile">
<link rel="import" href="/grid-elements/1.grid-jobs">
<link rel="import" href="/grid-elements/1.grid-bids">
<link rel="import" href="/grid-elements/1.grid-inbox">
<link rel="import" href="/grid-elements/1.grid-transactions">
<link rel="import" href="/bower_components/paper-material/paper-material.html">
<link rel="import" href="/bower_components/iron-pages/iron-pages.html">
<link rel="import" href="/elements/the-grid/grid-behaviors/grid-fold-behavior.html">
<link rel="import" href="/elements/the-grid/grid-behaviors/grid-tabs-behavior.html">

<dom-module id="grid-second-fold">
	<style include="iron-flex">
		:host {
			width: 400px;
			background-color: #ffffff;
			left: calc(var(--grid-drawer-collapse-width) - var(--grid-second-fold-width));
		    position: absolute;
		    top: 0px;
		    height: 100%;
		    z-index: 3;
		    transition: left 350ms cubic-bezier(0.4, 0, 0.2, 1);
		    display: block;
		}
		:host[opened] {
			left: 56px;
		}
		paper-header-panel {
			background-color: #fff;
		}
		iron-pages, iron-pages section {
			height: 100%;
		}
	</style>
	<template>
		<iron-pages id="tabs" selected="@{{selectedTab}}" attr-for-selected="data-tab">
			@if (Auth::guest())
			<section data-tab="auth">
				<grid-auth id="auth"></grid-auth>
			</section>
			@else
			<section data-tab="profile">
				<grid-profile id="profile"></grid-auth>
			</section>
			@endif
			<section data-tab="bids">
				<grid-bids id="bids"></grid-bids>
			</section>
			<section data-tab="jobs">
				<grid-jobs id="jobs"></grid-jobs>
			</section>
			<section data-tab="inbox">
				<grid-inbox id="inbox"></grid-inbox>
			</section>
			<section data-tab="transactions">
				<grid-transactions id="transactions"></grid-transactions>
			</section>
		</iron-pages>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-second-fold',
			behaviors: [GridBehaviors.FoldBehavior, GridBehaviors.TabsBehavior],
			_onClose: function() {
				this.thirdFold.close();
			},
			callParent: function() {
				this.thirdFold.opened = !this.thirdFold.opened;
			}
		});
	}());
</script>
