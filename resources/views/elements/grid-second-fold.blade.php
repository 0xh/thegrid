<!-- <link rel="import" href="/grid-elements/1.grid-auth">
<link rel="import" href="/grid-elements/1.grid-register">
<link rel="import" href="/grid-elements/1.grid-forgot-password">
<link rel="import" href="/grid-elements/1.grid-profile">
<link rel="import" href="/grid-elements/1.grid-jobs">
<link rel="import" href="/grid-elements/1.grid-bids">
<link rel="import" href="/grid-elements/1.grid-inbox">
<link rel="import" href="/grid-elements/1.grid-transactions">
<link rel="import" href="/grid-elements/1.grid-add-job"> -->
<link rel="import" href="/bower_components/iron-pages/iron-pages.html">

<dom-module id="grid-second-fold">
	<style include="iron-flex grid">
		paper-header-panel {
			background-color: #fff;
		}
		iron-pages, iron-pages section {
			height: 100%;
		}
	</style>
	<template>
		@if(Auth::check())
			{{-- <strong>{{Auth::user()->id}}, </strong> --}}
		@endif
		<iron-pages id="tabs" selected="@{{selectedTab}}" attr-for-selected="data-tab">
			@if (Auth::guest())
			<section data-tab="auth">
				<grid-auth id="auth"></grid-auth>
			</section>
			<section data-tab="register">
				<grid-register id="register"></grid-register>
			</section>
			<section data-tab="forgot-password">
				<grid-forgot-password id="forgotPassword"></grid-forgot-password>
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

			<section data-tab="add-job">
				<grid-add-job id="addJob"></grid-add-job>
			</section>

		</iron-pages>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-second-fold',
			behaviors: [
				GridBehaviors.FoldBehavior,
				GridBehaviors.TabsBehavior,
				GridBehaviors.PageBehavior
			],
			_onClose: function() {
				this.thirdFold.close();
			},
			_initialize: function() {
				var tab = this.$$('#'+this.selectedTab);
				if (tab) {
						if(Polymer.isInstance(tab)) {
							return;
						}
						Polymer.Base.importHref(
							'/grid-elements/1.grid-'+this.selectedTab,
							() => {
								if(!tab.isInit)
									tab.init();
							}
						);
				}
			},
			callParent: function() {
				this.thirdFold.opened = !this.thirdFold.opened;
			},
			ready: function() {
				// console.log('tab',this.getTab);
				if(this.getTab) {
					this.selectedTab = this.getTab;
					this.secondFold.open();
				}
			}
		});
	}());
</script>
