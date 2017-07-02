<dom-module id="grid-transactions">
	<style include="iron-flex grid">
		:root {
			--paper-toolbar-background: #FFFFFF;
			--paper-toolbar-color: #636b6f;
		}
		:host {
			height: 100%;	
		}
	</style>
	<template>
		<paper-scroll-header-panel class="flex h100">
		    <paper-toolbar slot="header" class="border-bottom">
		      <div class="flex">Transactions</div>
		      <paper-icon-button icon="chevron-left" on-tap="close"></paper-icon-button>
		    </paper-toolbar>
		    <div>
		    	content
		    	<paper-icon-button icon="help" on-click="callParent"></paper-icon-button>
		    </div>
		 </paper-scroll-header-panel>
	</template>
</dom-module>
<script>
	(function(){
		'use strict'

		Polymer({
			is: 'grid-transactions',
			behaviors: [GridBehaviors.FoldBehavior],
			close: function() {
				this.secondFold.close();
			}
		});
	}());
</script>
